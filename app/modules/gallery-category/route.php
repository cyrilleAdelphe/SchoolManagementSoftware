<?php
Route::group(array('prefix'=>'gallery', 'before'=>'reg-superadmin-admin'), function()
{
	Route::get('/', [
		'as'	=>	'gallery-category-show-all',
		'uses'=>	'GalleryCategoryController@showAll'
	]);
});

Route::group(array('prefix'=>'gallery', 'before'=>'reg-superadmin-admin'), function()
{
	Route::get('create-category', 
		[
			'as'	=> 'gallery-category-create-get',
			'uses'	=> 'GalleryCategoryController@getCreateView'
		]);

	Route::get('edit-category/{id}', 
		[
			'as'	=> 'gallery-category-edit-get',
			'uses'	=> 'GalleryCategoryController@getEditView'
		]);

	Route::get('list-category',
		[
			'as'	=>	'gallery-category-list',
			'uses'	=>	'GalleryCategoryController@getListView'
		]);

	Route::group(array('before'=>'csrf'),function()
	{
		Route::post('create-category', 
			[
				'as'	=> 'gallery-category-create-post',
				'uses'	=> 'GalleryCategoryController@postCreateView'
			]);

		Route::post('edit-category/{id}', 
			[
				'as'	=> 'gallery-category-edit-post',
				'uses'	=> 'GalleryCategoryController@postEditView'
			]);

		Route::post('delete-category', 
			[
				'as'	=> 'gallery-category-delete-post',
				'uses'	=> 'GalleryCategoryController@postDelete'
			]);

	});
});