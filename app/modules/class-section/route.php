<?php

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'class-section'), function(){

		Route::get('/list',
				['as'	=>	'class-section-list',
				 'uses'	=>	'ClassSectionController@getListView']);

		Route::get('/create',
				['as'	=>	'class-section-create-get',
				 'uses'	=>	'ClassSectionController@getCreateView']);

		Route::get('/view/{class_id}',
				['as'	=>	'class-section-view',
				 'uses'	=>	'ClassSectionController@getViewview']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'class-section-create-post',
					 'uses'	=>	'ClassSectionController@postCreateView']);

			Route::post('/delete',
					['as'	=>	'class-section-delete-post',
					 'uses'	=>	'ClassSectionController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'class-section-purge-post',
					 'uses'	=>	'ClassSectionController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'class-section-purge-record-post',
					 'uses'	=>	'ClassSectionController@postDelete']);

		});
	});

});

	