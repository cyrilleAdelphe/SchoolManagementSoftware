<?php

namespace Djereg\AsyncQueue;

use Djereg\AsyncQueue\AsyncQueueJobStorage;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\SyncJob;

class AsyncQueueJob extends SyncJob {

	const STATUS_OPEN = 0;
	const STATUS_WAITING = 1;
	const STATUS_STARTED = 2;
	const STATUS_FINISHED = 3;

	protected $job;

	public function __construct(Container $container, AsyncQueueJobStorage $job) {
		parent::__construct($container, $job);
	}

	public function fire() {

		$payload = $this->parsePayload($this->job->payload);

		if ($this->job->delay) {
			$this->job->status = self::STATUS_WAITING;
			$this->job->save();
			sleep($this->job->delay);
		}

		$this->job->status = self::STATUS_STARTED;
		$this->job->save();

		$this->resolveAndFire($payload);

		if (!$this->deleted) {
			$this->job->status = self::STATUS_FINISHED;
			$this->job->save();
		}
	}

	public function delete() {
		parent::delete();
		$this->job->delete();
	}

	protected function parsePayload($payload) {
		return json_decode($payload, true);
	}

}
