<?php
Route::group(array('before'=>'guest','prefix'=>'/downloads'),function(){
		
	Route::get('/',array(
		'as'	=> 'download-manager-frontend',
		'uses'	=> 'DownloadManagerController@frontend'
	));

	Route::get('/category/{folder_id}/{google_file_id}',array(
		'as'	=> 'download-manager-frontend-files',
		'uses'	=> 'DownloadManagerController@frontendFiles'
	));

	Route::get('/file-download/{file_id}/{google_file_id}',array(
		'as' => 'download-manager-backend-file-download',
		'uses' => 'DownloadManagerController@backendFileDownload'
	));

	Route::get('/search',array(
		'as'	=> 'download-manager-frontend-file-search',
		'uses'	=> 'DownloadManagerController@frontendFileSearch'
	));


});

Route::group(array('prefix'=>'/download-manager', 'before' => 'reg-superadmin'),function() {

	Route::get('/drive-config', array(
		'as'	=> 'download-manager-drive-config-get',
		'uses'=> 'DownloadManagerController@getDriveConfig'
	));

	Route::get('/config/{folder_id}/{google_file_id}',array(
		'as' 	=> 'download-manager-config-get',
		'uses'	=> 'DownloadManagerController@getConfig'
	));

	Route::get('/backend-access',array(
		'as' => 'download-manager-backend-light',
		'uses' => 'DownloadManagerController@backendAccessLight'
	));

	//the next four routes make up the tabs of backend-access
	Route::get('/backend-subcategories/{folder_id}/{google_file_id}',array(
		'as' => 'download-manager-backend-subcategories',
		'uses' => 'DownloadManagerController@backendSubCategories'
	));

	Route::get('/backend-files/{folder_id}/{google_file_id}',array(
		'as' => 'download-manager-backend-files',
		'uses' => 'DownloadManagerController@backendFiles'
	));

	Route::get('/backend-add-category/{folder_id}/{google_file_id}',array(
		'as' => 'download-manager-backend-add-category',
		'uses' => 'DownloadManagerController@backendAddCategory'
	));

	Route::get('/backend-upload-files/{folder_id}/{google_file_id}',array(
		'as' => 'download-manager-backend-upload-files',
		'uses' => 'DownloadManagerController@backendUploadFiles'
	));
	//the previous four routes make up the tabs of backend-access

	Route::get('/backend-edit/{id}/{google_file_id}',array(
		'as' => 'download-manager-backend-edit-get',
		'uses' => 'DownloadManagerController@getBackendEdit'
	));

	Route::get('/backend-file-remove/{file_id}/{google_file_id}',array(
		'as' => 'download-manager-backend-file-remove',
		'uses' => 'DownloadManagerController@backendFileRemove'
	));

	// though the route names it frontend, it is for backend only!
	Route::get('/file-upload',array(
		'as'	=> 'download-manager-frontend-file-upload-get',
		'uses'	=> 'DownloadManagerController@getFrontendFileUpload'
	));

	
	Route::group(array('before'=>'csrf'),function() {

		Route::post('/drive-config', array(
			'as'	=> 'download-manager-drive-config-post',
			'uses'=> 'DownloadManagerController@postDriveConfig'
		));
		
		Route::post('/config/{folder_id}/{google_file_id}',array(
			'as' => 'download-manager-config-post',
			'uses' => 'DownloadManagerController@postConfig'
		));

		Route::post('/backend-edit',array(
			'as' => 'download-manager-backend-edit-post',
			'uses' => 'DownloadManagerController@postBackendEdit'
		));

		Route::post('/backend-folder-create',array(
			'as' => 'download-manager-backend-folder-create-post',
			'uses' => 'DownloadManagerController@postBackendFolderCreate'
		));

		Route::post('/backend-files-upload/{parent_id}/{parent_google_file_id}',array(
			'as' => 'download-manager-backend-files-upload-post',
			'uses' => 'DownloadManagerController@postBackendFilesUpload'
		));

		Route::post('/backend-file-upload/{parent_id}/{parent_google_file_id}',array(
			'as' => 'download-manager-backend-file-upload-post',
			'uses' => 'DownloadManagerController@postBackendFileUpload'
		));

		
	});

});
?>