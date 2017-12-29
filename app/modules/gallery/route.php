<?php

define('GALLERY_ORIGINAL_FOLDER', app_path().'/modules/gallery/assets/images/original');
define('GALLERY_THUMBNAILS_FOLDER', app_path().'/modules/gallery/assets/images/thumbnails');

Route::group(array('prefix' => 'frontend-gallery/facebook'), function()
{
	Route::get('show-albums', [
		'as' => 'frontend-gallery-facebook-show-albums',
		'uses' => 'GalleryController@frontendShowAlbums'
	]);

	Route::get('show-images/{album_id}', [
		'as' => 'frontend-gallery-facebook-show-albums',
		'uses' => 'GalleryController@frontendShowPhotos'
	]);
});

Route::group(array('prefix'=>'gallery', 'before'=>'reg-content-manager'), function()
{
	Route::get('show-category/{category_id}', [
		'as' => 'gallery-show-category',
		'uses' => 'GalleryController@showCategory'
	]);

	Route::get('get-access-token',
		['as'	=>	'gallery-get-access-token',
		 'uses'	=>	'GalleryController@getAccessToken']);

	Route::get('get-facebook-images',
		['as'	=>	'gallery-get-facebook-images',
		 'uses'	=>	'GalleryController@getFacebookImages']);

	Route::post('get-facebook-images',
		['as'	=>	'gallery-get-facebook-images-post',
		 'uses'	=>	'GalleryController@postFacebookImages']);

	Route::get('get-facebook-albums',
		['as'	=>	'gallery-get-facebook-albums',
		 'uses'	=>	'GalleryController@getFacebookAlbums']);
});

Route::group(array('prefix'=>'gallery', 'before'=>'reg-content-manager'), function()
{
	Route::get('add-image', 
		[
			'as'	=> 'gallery-create-get',
			'uses'	=> 'GalleryController@getCreateView'
		]);

	Route::get('edit-image/{id}', 
		[
			'as'	=> 'gallery-edit-get',
			'uses'	=> 'GalleryController@getEditView'
		]);

	Route::get('list-images',
		[
			'as'	=>	'gallery-list',
			'uses'	=>	'GalleryController@getListView'
		]);

	Route::group(array('before'=>'csrf'),function()
	{
		Route::post('add-image', 
			[
				'as'	=> 'gallery-create-post',
				'uses'	=> 'GalleryController@postCreateView'
			]);

		Route::post('edit-image/{id}', 
			[
				'as'	=> 'gallery-edit-post',
				'uses'	=> 'GalleryController@postEditView'
			]);

		Route::post('delete-image', 
			[
				'as'	=> 'gallery-delete-post',
				'uses'	=> 'GalleryController@postDelete'
			]);

	});

	Route::get('/test',function()
	{
		return View::make('gallery.views.test');
	});

	Route::post('/test', 
	[
		'as' => 'gallery-test-post',
		'uses' =>
			function()
			{
				
				if(Input::hasFile('fileToUpload'))
				{
					$img_original = Image::make(Input::file('fileToUpload'));
					$img_original->save(GALLERY_ORIGINAL_FOLDER.'/abc');

					$img_original->crop(100, 100)->save(GALLERY_THUMBNAILS_FOLDER . '/abc');
				}
			}
	

	]);
});