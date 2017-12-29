<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'teacher-subject'), function(){

		Route::get('/list',
				['as'	=>	'teacher-subject-list',
				 'uses'	=>	'TeacherSubjectController@getListView']);

		Route::get('/create',
				['as'	=>	'teacher-subject-create-get',
				 'uses'	=>	'TeacherSubjectController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'teacher-subject-view',
				 'uses'	=>	'TeacherSubjectController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'teacher-subject-edit-get',
				 'uses'	=>	'TeacherSubjectController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'teacher-subject-create-post',
					 'uses'	=>	'TeacherSubjectController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'teacher-subject-edit-post',
					 'uses'	=>	'TeacherSubjectController@postEditView']);

			Route::post('/delete',
					['as'	=>	'teacher-subject-delete-post',
					 'uses'	=>	'TeacherSubjectController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'teacher-subject-purge-post',
					 'uses'	=>	'TeacherSubjectController@purgeRows']);

		});
	});

//});

	