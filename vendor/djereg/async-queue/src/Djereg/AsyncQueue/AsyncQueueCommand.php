<?php

namespace Djereg\AsyncQueue;

use Djereg\AsyncQueue\AsyncQueueJob;
use Djereg\AsyncQueue\AsyncQueueJobStorage;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class AsyncQueueCommand extends Command {

	protected $name = 'queue:async';
	protected $description = 'Run then async queue';

	public function fire() {
		$storage = new AsyncQueueJobStorage($this->argument('storage'));
		$item = $storage->findOrFail($this->argument('id'));

		$job = new AsyncQueueJob($this->laravel, $item);

		$job->fire();
	}

	protected function getArguments() {
		return array(
			array('storage', InputArgument::REQUIRED, 'Storage type'),
			array('id', InputArgument::REQUIRED, 'Job ID'),
		);
	}

}
