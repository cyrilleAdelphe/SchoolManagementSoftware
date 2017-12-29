## Async queue driver for Laravel 4.
***
Push the queued job to the background without waiting for its response.
Now you can use the "file" and "cache" storage type. The "database" is coming soon.

## Installation
Add the package to your composer.json:
```json
{
    "require": {
        "djereg/async-queue": "dev-master"
    }
}
```
Add the Service Provider to the providers array in config/app.php
```php
'providers' => array(
    // ...
    'Djereg\AsyncQueue\AsyncQueueServiceProvider',
    // ...
)
```

Now you have to use the async driver in config/queue.php
```php
'default' => 'async',

'connections' => array(
	// ...
	'async' => array(
		'driver' => 'async',
		'storage' => 'file', // "file" and "cache" is now available
	),
	// ...
}
```

That's all!

For more info see http://laravel.com/docs/queues