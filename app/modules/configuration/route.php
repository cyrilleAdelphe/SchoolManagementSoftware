<?php 

Route::group(array('prefix' => 'configuration', 'before' => 'reg-superadmin'), function(){

		Route::get('/{configuration_filename}',
				['as'	=>	'show-configuration',
				 'uses'	=>	'JsonConfigurationController@showConfigFile']);

		Route::get('/create',
				['as'	=>	'configuration-create-get',
				 'uses'	=>	'JsonConfigurationController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'configuration-view',
				 'uses'	=>	'JsonConfigurationController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'configuration-edit-get',
				 'uses'	=>	'JsonConfigurationController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/{configuration_filename}',
					['as'	=>	'show-configuration-post',
					 'uses'	=>	'JsonConfigurationController@updateConfigurationFile']);

			Route::post('/edit/{id}',
					['as'	=>	'configuration-edit-post',
					 'uses'	=>	'JsonConfigurationController@postEditView']);

			Route::post('/delete',
					['as'	=>	'configuration-delete-post',
					 'uses'	=>	'JsonConfigurationController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'configuration-purge-post',
					 'uses'	=>	'JsonConfigurationController@purgeRows']);

		});
	});