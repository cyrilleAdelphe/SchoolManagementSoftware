<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'templates'), function(){

		Route::get('/list',
				['as'	=>	'templates-list',
				 'uses'	=>	'TemplateController@getListView']);

		Route::get('/create',
				['as'	=>	'templates-create-get',
				 'uses'	=>	'TemplateController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'templates-view',
				 'uses'	=>	'TemplateController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'templates-edit-get',
				 'uses'	=>	'TemplateController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'templates-create-post',
					 'uses'	=>	'TemplateController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'templates-edit-post',
					 'uses'	=>	'TemplateController@postEditView']);

			Route::post('/delete',
					['as'	=>	'templates-delete-post',
					 'uses'	=>	'TemplateController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'templates-purge-post',
					 'uses'	=>	'TemplateController@purgeRows']);

		});
	});

//});

	