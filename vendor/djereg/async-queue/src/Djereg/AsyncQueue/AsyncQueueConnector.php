<?php

namespace Djereg\AsyncQueue;

use Djereg\AsyncQueue\AsyncQueueHandler;
use Illuminate\Queue\Connectors\ConnectorInterface;

class AsyncQueueConnector implements ConnectorInterface {

	protected $defaults = array(
		'storage' => 'file',
		'file' => [
		],
		'database' => [
			'connection' => 'default',
			'table_name' => 'async_queue'
		],
		'cache' => [
		]
	);

	public function connect(array $config) {
		$config = array_merge($this->defaults, $config);
		return new AsyncQueueHandler($config);
	}

}
