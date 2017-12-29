<?php

namespace Djereg\AsyncQueue;

use Djereg\AsyncQueue\AsyncQueueConnector;
use Djereg\AsyncQueue\AsyncQueueCommand;
use Illuminate\Support\ServiceProvider;

class AsyncQueueServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->app['queue']->addConnector('async', function () {
			return new AsyncQueueConnector();
		});
		$this->commands('command.queue.async');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->app['command.queue.async'] = $this->app->share(function () {
			return new AsyncQueueCommand();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('command.queue.async');
	}

}
