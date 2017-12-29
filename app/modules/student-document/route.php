<?php

Route::group(array('prefix' => 'student-document', 'before' => 'reg-superadmin-admin'), function() {
	Route::get('/', [
		'as'	=>	'student-document-main',
		'uses'=>	'StudentDocumentController@getMain'	
	]);

	Route::get('/info', [
		'as'	=>	'student-document-info',
		'uses'=>	'StudentDocumentController@info'
	]);

	Route::group(array('before' => 'csrf'), function() {
		Route::post('/file', [
			'as'	=>	'student-document-file-post',
			'uses'=>	'StudentDocumentController@postFile'
		]);

		Route::post('/files', [
			'as'	=>	'student-document-files-post',
			'uses'=>	'StudentDocumentController@postFiles'
		]);

		Route::post('/delete', [
			'as'	=>	'student-document-purge-record-post',
			'uses'=>	'StudentDocumentController@postDelete'
		]);
	});
});