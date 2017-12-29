<?php

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'class-subject'), function(){

		Route::get('/list',
				['as'	=>	'class-subject-list',
				 'uses'	=>	'ClassSubjectController@getListView']);

		Route::get('/create',
				['as'	=>	'class-subject-create-get',
				 'uses'	=>	'ClassSubjectController@getCreateView']);

		Route::get('/view/{class_id}',
				['as'	=>	'class-subject-view',
				 'uses'	=>	'ClassSubjectController@getViewview']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'class-subject-create-post',
					 'uses'	=>	'ClassSubjectController@postCreateView']);

			Route::post('/delete',
					['as'	=>	'class-subject-delete-post',
					 'uses'	=>	'ClassSubjectController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'class-subject-purge-post',
					 'uses'	=>	'ClassSubjectController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'class-subject-purge-record-post',
					 'uses'	=>	'ClassSubjectController@postDelete']);

		});
	});

});

	