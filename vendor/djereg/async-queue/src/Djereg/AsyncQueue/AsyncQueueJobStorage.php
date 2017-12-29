<?php

namespace Djereg\AsyncQueue;

use Illuminate\Support\Facades\Cache;

class AsyncQueueJobStorage {

	private $fields = [
		'id' => null,
		'status' => 0,
		'delay' => 0,
		'payload' => ''
	];
	private $type = 'file';

	public function __construct($type) {
		if (!in_array($type, ['file', 'cache', 'database'])) {
			throw new \Exception("Invalid storage type: {$type}");
		}
		$this->type = $type;
	}

	public function __get($name) {
		if (!in_array($name, array_keys($this->fields))) {
			throw new \Exception("Invalid field (read): {$name}");
		}
		return $this->fields[$name];
	}

	public function __set($name, $value) {
		if (!in_array($name, array_keys($this->fields))) {
			throw new \Exception("Invalid field (write): {$name}");
		}
		$this->fields[$name] = $value;
	}

	public function find($id) {
		try {
			return $this->findOrFail($id);
		} catch (\Exception $ex) {
			return null;
		}
	}

	public function findOrFail($id) {
		switch ($this->type) {
			case 'database':
				break;
			case 'cache':
				$key = $this->getCacheKey($this->fields['id']);
				if (!Cache::has($key)) {
					throw new \Exception('Data not found in cache');
				}
				$this->fields = Cache::get($key);
				break;
			case 'file':
				$path = $this->getFilePath($id);
				if (!is_file($path)) {
					throw new \Exception('Storage file not found');
				}
				$this->fields = include $path;
				break;
			default:
				throw new \Exception("Invalid storage type: {$this->type}");
		}
		return $this;
	}

	public function save() {
		switch ($this->type) {
			case 'database':
				break;
			case 'cache':
				$this->setIdIfNotSet(str_random());
				Cache::put($this->getCacheKey($this->fields['id']), $this->fields, 30);
				break;
			case 'file':
				$this->setIdIfNotSet(str_random());
				$path = $this->getFilePath($this->fields['id']);
				$dir = pathinfo($path, PATHINFO_DIRNAME);
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
					file_put_contents("{$dir}/.gitignore", "*\r\n!.gitignore\r\n");
				}
				file_put_contents($path, $this->makeFileContent());
				break;
			default:
				throw new \Exception("Invalid storage type: {$this->type}");
		}
		return $this;
	}

	public function delete() {
		switch ($this->type) {
			case 'database':
				break;
			case 'cache':
				if ($this->fields['id']) {
					Cache::forget($this->getCacheKey($this->fields['id']));
				}
				break;
			case 'file':
				$path = $this->getFilePath($this->fields['id']);
				if (file_exists($path)) {
					unlink($path);
				}
				break;
			default:
				throw new \Exception("Invalid storage type: {$this->type}");
		}
	}

	private function getFilePath($id) {
		return storage_path('queue') . DIRECTORY_SEPARATOR . $id . '.php';
	}

	private function getCacheKey($id) {
		return "async-queue.key.{$id}";
	}

	private function setIdIfNotSet($id) {
		if (!$this->fields['id']) {
			$this->fields['id'] = $id;
		}
		return $id;
	}

	private function makeFileContent() {
		return '<?php return ' . var_export($this->fields, true) . ';';
	}

}
