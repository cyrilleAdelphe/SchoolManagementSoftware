<?php

namespace Djereg\AsyncQueue;

use Djereg\AsyncQueue\AsyncQueueJob;
use Djereg\AsyncQueue\AsyncQueueJobStorage;
use Illuminate\Queue\SyncQueue;

class AsyncQueueHandler extends SyncQueue {

	protected $config;

	public function __construct(array $config) {
		$this->config = $config;
	}

	public function push($job, $data = '', $queue = null) {
		$id = $this->saveJob($job, $data);
		$this->startProcess($id, 0);

		return 0;
	}

	public function saveJob($job, $data, $delay = 0) {
		$payload = $this->createPayload($job, $data);

		$storage = new AsyncQueueJobStorage($this->config['storage']);
		$storage->status = AsyncQueueJob::STATUS_OPEN;
		$storage->delay = $delay;
		$storage->payload = $payload;

		$storage->save();

		return $storage->id;
	}

	public function startProcess($jobId) {
		chdir($this->container['path.base']);
		$command = $this->getCommand($jobId);
		exec($command, $output, $return);
	}

	protected function getCommand($jobId) {
		$cmd = 'php artisan queue:async %s %s --env=%s';
		$cmd = $this->getBackgroundCommand($cmd);

		$storage = $this->config['storage'];
		$environment = $this->container->environment();

		return sprintf($cmd, $storage, $jobId, $environment);
	}

	protected function getBackgroundCommand($cmd) {
		if (defined('PHP_WINDOWS_VERSION_BUILD')) {
			return sprintf('start /B %s', $cmd);
		} else {
			return sprintf('%s > /dev/null 2>&1 &', $cmd);
		}
	}

	public function later($delay, $job, $data = '', $queue = null) {
		$delay = $this->getSeconds($delay);
		$id = $this->saveJob($job, $data, $delay);
		$this->startProcess($id);

		return 0;
	}

}
