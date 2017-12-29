<?php

Route::group(['prefix' => 'general-downloads'], function() {
	Route::group(['before' => 'reg-superadmin-admin'], function() {
		
		Route::get('/', [
			'as'	=> 'general-downloads-main',
			'uses'=> 'GeneralDownloadsController@getListView'
		]);

		Route::group(['before' => 'reg-superadmin-admin'], function() {
			
			Route::post('create', [
				'as'	=> 'general-downloads-file-post',
				'uses'=> 'GeneralDownloadsController@postFile'
			]);

			Route::post('/delete', [
				'as'	=>	'general-downloads-purge-record-post',
				'uses'=>	'GeneralDownloadsController@postDelete'
			]);

		});
	});
});