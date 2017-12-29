<?php

	Route::group(array('prefix' => 'subject-teacher', 'before' => 'reg-superadmin-admin'), function(){

		Route::get('/list',
				['as'	=>	'subject-teacher-list',
				 'uses'	=>	'SubjectTeacherController@getListView']);

		Route::get('/create',
				['as'	=>	'subject-teacher-create-get',
				 'uses'	=>	'SubjectTeacherController@getCreateView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'subject-teacher-create-post',
					 'uses'	=>	'SubjectTeacherController@postCreateView']);

		});
	});

//});

	