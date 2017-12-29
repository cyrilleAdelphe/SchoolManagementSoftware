<?php

	Route::group(array('prefix' => 'access-control'), function()
	{
		Route::group(array('before' => 'reg-superadmin-admin'), function()
		{
			Route::get('/list',
			['as'	=>	'access-list',
			 'uses'	=>	'AccessController@getAllModules'
			]);

			Route::get('/permissions/{module_name}',
			['as'	=>	'access-permissions',
			 'uses'	=>	'AccessController@getSetAccessControl'
			]);
			
			Route::group(array('before' => 'csrf'), function()
			{
				Route::post('/permissions/{module_name}',
			['as'	=>	'access-permissions-post',
			 'uses'	=>	'AccessController@postSetAccessControl'
			]);

			});

		});
	});
	

?>