<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'position'), function(){

		Route::get('/list',
				['as'	=>	'position-list',
				 'uses'	=>	'PositionController@getListView']);

		Route::get('/create',
				['as'	=>	'position-create-get',
				 'uses'	=>	'PositionController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'position-view',
				 'uses'	=>	'PositionController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'position-edit-get',
				 'uses'	=>	'PositionController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'position-create-post',
					 'uses'	=>	'PositionController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'position-edit-post',
					 'uses'	=>	'PositionController@postEditView']);

			Route::post('/delete',
					['as'	=>	'position-delete-post',
					 'uses'	=>	'PositionController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'position-purge-post',
					 'uses'	=>	'PositionController@purgeRows']);

		});
	});

//});

	