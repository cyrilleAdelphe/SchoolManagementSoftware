<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'section'), function(){

		Route::get('/list',
				['as'	=>	'section-list',
				 'uses'	=>	'SectionController@getListView']);

		Route::get('/create',
				['as'	=>	'section-create-get',
				 'uses'	=>	'SectionController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'section-view',
				 'uses'	=>	'SectionController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'section-edit-get',
				 'uses'	=>	'SectionController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'section-create-post',
					 'uses'	=>	'SectionController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'section-edit-post',
					 'uses'	=>	'SectionController@postEditView']);

			Route::post('/delete',
					['as'	=>	'section-delete-post',
					 'uses'	=>	'SectionController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'section-purge-post',
					 'uses'	=>	'SectionController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'section-purge-record-post',
					 'uses'	=>	'SectionController@postDelete']);

		});
	});

//});

	