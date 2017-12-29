<?php

	Route::group(array('prefix' => 'cas'), function()
	{

		Route::get('/remark-setting-list', 
			['as'	=>	'remark-setting-list',
			 'uses'	=>	'RemarkSettingController@getListView']);

		

		Route::get('/remark-setting-edit/{id}',
				['as'	=>	'remark-setting-edit-get',
				 'uses'	=>	'RemarkSettingController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){
					Route::post('/remark-setting-create', 
					['as'	=>	'remark-setting-add',
					 'uses'	=>	'RemarkSettingController@postCreateView']);

					Route::post('/remark-setting-edit/{id}',
					['as'	=>	'remark-setting-edit-post',
					 'uses'	=>	'RemarkSettingController@postEditView']);

					Route::post('/remark-setting-delete',
					['as'	=>	'remark-setting-delete-post',
					 'uses'	=>	'RemarkSettingController@deleteRows']);

					Route::post('/remark-setting-purge',
					['as'	=>	'remark-setting-purge-post',
					 'uses'	=>	'RemarkSettingController@purgeRows']);

					Route::post('/remark-setting-purge-record',
					['as'	=>	'remark-setting-purge-record-post',
					 'uses'	=>	'RemarkSettingController@postDelete']);


		});


	});