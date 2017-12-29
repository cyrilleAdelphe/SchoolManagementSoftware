<?php

	Route::group(array('prefix' => 'subject', 'before' => 'reg-superadmin-admin'), function(){

		Route::get('/list',
				['as'	=>	'subject-list',
				 'uses'	=>	'SubjectController@getListView']);

		Route::get('/create',
				['as'	=>	'subject-create-get',
				 'uses'	=>	'SubjectController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'subject-view',
				 'uses'	=>	'SubjectController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'subject-edit-get',
				 'uses'	=>	'SubjectController@getEditView']);

		Route::get('/ajax-get-teachers',
			['as'	=>	'ajax-subject-get-teachers',
			'uses'	=>	'SubjectController@ajaxGetTeachers']);

		Route::get('/ajax-get-subjects',
			['as'	=>	'ajax-subject-get-subjects',
			'uses'	=>	'SubjectController@ajaxGetSubjects']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'subject-create-post',
					 'uses'	=>	'SubjectController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'subject-edit-post',
					 'uses'	=>	'SubjectController@postEditView']);

			Route::post('/delete-subject/{id}',
					['as'	=>	'subject-delete-single-post',
					 'uses'	=>	'SubjectController@deleteSubject']);

			Route::post('/delete',
					['as'	=>	'subject-delete-post',
					 'uses'	=>	'SubjectController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'subject-purge-post',
					 'uses'	=>	'SubjectController@purgeRows']);

		});
	});

//});

	