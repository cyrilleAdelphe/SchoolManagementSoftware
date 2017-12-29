<?php

Route::get('recent-assignments',
	[
		'as'	=>	'assignments-recent',
		'uses'	=>	'AssignmentsController@getRecent'
	]);

Route::group(array('before'=>'reg-superadmin', 'prefix'=>'assignments'), function() {
	Route::get('/files', 
			[
				'as'	=>	'assignments-files',
				'uses'	=>	'AssignmentsController@getFiles'
			]);

	Route::get('/upload',
			[
				'as'	=>	'assignments-upload-get',
				'uses'	=>	'AssignmentsController@getUpload'
			]);

	Route::get('/config',
			[
				'as'	=>	'assignments-config-get',
				'uses'	=>	'AssignmentsController@getConfig'
			]);

	Route::get('/push-notification/{assignment_id}/{class_id}/{section_id}', 
			[
				'as'	=>	'assignments-push-notification',
				'uses'	=>	'AssignmentsController@pushNotification'
			]);

	Route::group(array('before'=>'csrf'), function() {
		Route::post('/upload-file', [
				'as'	=>	'assignments-upload-file-post',
				'uses'	=>	'AssignmentsController@postUploadFile'
			]);

		Route::post('/upload-files', [
				'as'	=>	'assignments-upload-files-post',
				'uses'	=>	'AssignmentsController@postUploadFiles'
			]);

		Route::post('/config',
			[
				'as'	=>	'assignments-config-post',
				'uses'	=>	'AssignmentsController@postConfig'
			]);

		Route::post('/remove-file', [
				'as'	=> 'assignments-remove-file-post',
				'uses'	=>	'AssignmentsController@postRemoveFile'
			]);
	});
});