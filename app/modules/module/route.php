<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'module'), function(){

		Route::get('/list',
				['as'	=>	'module-list',
				 'uses'	=>	'ModuleController@getListView']);

		Route::get('/create',
				['as'	=>	'module-create-get',
				 'uses'	=>	'ModuleController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'module-view',
				 'uses'	=>	'ModuleController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'module-edit-get',
				 'uses'	=>	'ModuleController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'module-create-post',
					 'uses'	=>	'ModuleController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'module-edit-post',
					 'uses'	=>	'ModuleController@postEditView']);

			Route::post('/delete',
					['as'	=>	'module-delete-post',
					 'uses'	=>	'ModuleController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'module-purge-post',
					 'uses'	=>	'ModuleController@purgeRows']);

		});
	});

//});

	