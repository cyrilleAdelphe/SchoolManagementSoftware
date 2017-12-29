<?php

Route::group(array('prefix' => 'employee-document', 'before' => 'reg-superadmin-admin'), function() {
	Route::get('/', [
		'as'	=>	'employee-document-main',
		'uses'=>	'EmployeeDocumentController@getMain'	
	]);

	Route::get('/info', [
		'as'	=>	'employee-document-info',
		'uses'=>	'EmployeeDocumentController@info'
	]);

	Route::group(array('before' => 'csrf'), function() {
		Route::post('/file', [
			'as'	=>	'employee-document-file-post',
			'uses'=>	'EmployeeDocumentController@postFile'
		]);

		Route::post('/files', [
			'as'	=>	'employee-document-files-post',
			'uses'=>	'EmployeeDocumentController@postFiles'
		]);

		Route::post('/delete', [
			'as'	=>	'employee-document-purge-record-post',
			'uses'=>	'EmployeeDocumentController@postDelete'
		]);
	});

});