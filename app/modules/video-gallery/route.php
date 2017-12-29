<?php

Route::group(array('prefix' => 'video-gallery'), function() {
	Route::get('youtube-gallery', [
		'as'	=>	'video-gallery-youtube-gallery',
		'uses'=> 	'VideoGalleryController@youtubeGallery'
	]);

	Route::get('youtube-video/{id?}', [
		'as'	=>	'video-gallery-youtube-video',
		'uses'=>	'VideoGalleryController@youtubeVideo'
	]);

	Route::get('/vimeo-gallery', [
		'as'		=> 'video-gallery-show',
		'uses'	=> 'VideoGalleryController@show'
	]);

	Route::get('/video/{id}', [
		'as'		=> 'video-gallery-show-video',
		'uses'	=> 'VideoGalleryController@showVideo'
	]);

	Route::group(array('before' => 'reg-superadmin-admin'), function() {
		Route::get('/test', [
			'as'		=> 'video-gallery-test',
			'uses'	=> 'VideoGalleryController@test'
		]);

		Route::get('/config', [
			'as'		=> 'video-gallery-config-get',
			'uses'	=> 'VideoGalleryController@getConfig'
		]);

		Route::group(array('before' => 'csrf'), function() {
			Route::post('/config', [
				'as'		=> 'video-gallery-config-post',
				'uses'	=> 'VideoGalleryController@postConfig'
			]);
		});
	});

});
	