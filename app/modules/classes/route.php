<?php

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'classes'), function(){

		Route::get('/list',
				['as'	=>	'classes-list',
				 'uses'	=>	'ClassesController@getListView']);

		Route::get('/create',
				['as'	=>	'classes-create-get',
				 'uses'	=>	'ClassesController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'classes-view',
				 'uses'	=>	'ClassesController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'classes-edit-get',
				 'uses'	=>	'ClassesController@getEditView']);

		Route::get('/ajax-get-classes',
				['as'	=>	'ajax-classes-get-classes',
				 'uses'	=>	'ClassesController@ajaxGetClasses']
			);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'classes-create-post',
					 'uses'	=>	'ClassesController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'classes-edit-post',
					 'uses'	=>	'ClassesController@postEditView']);

			Route::post('/delete',
					['as'	=>	'classes-delete-post',
					 'uses'	=>	'ClassesController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'classes-purge-post',
					 'uses'	=>	'ClassesController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'classes-purge-record-post',
					 'uses'	=>	'ClassesController@postDelete']);

		});
	});

});	