<?php

Route::get('list-students',
		[
			'as'	=>	'dormitory-student-list',
			'uses'	=>	'DormitoryStudentController@getListViewSimple',
			'prefix' => 'dormitory',
			'before'	=>	'auth'
		]);

Route::post('list-students',
			[
				'as'	=>	'dormitory-student-post-list',
				'uses'	=>	'DormitoryStudentController@postListViewSimple',
				'prefix' => 'dormitory',
				'before'	=>	'auth'
			]);

Route::group(array('prefix'=>'dormitory', 'before'=>'reg-superadmin'), function()
{
	Route::get('add-student', 
		[
			'as'	=> 'dormitory-student-create-get',
			'uses'	=> 'DormitoryStudentController@getCreateView'
		]);

	Route::get('edit-student/{id}', 
		[
			'as'	=> 'dormitory-student-edit-get',
			'uses'	=> 'DormitoryStudentController@getEditView'
		]);

	


	Route::group(array('before'=>'csrf'),function()
	{
		Route::post('add-student', 
			[
				'as'	=> 'dormitory-student-create-post',
				'uses'	=> 'DormitoryStudentController@postCreateView'
			]);


		Route::post('edit-student/{id}', 
			[
				'as'	=> 'dormitory-student-edit-post',
				'uses'	=> 'DormitoryStudentController@postEditView'
			]);

		Route::post('delete-student', 
			[
				'as'	=> 'dormitory-student-delete-post',
				'uses'	=> 'DormitoryStudentController@postDelete'
			]);

	});
});