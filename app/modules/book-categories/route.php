<?php
Route::group(array('prefix'=>'library'),function(){
	// routes requiring superadmin login
	Route::group(array('before'=>'reg-librarian'),function(){
		Route::get('/create-category',
			[
				'as'	=>	'book-categories-create-get',
				'uses'	=>	'BookCategoriesController@getCreateView'
			]);

		Route::get('/list-category',
				['as'	=>	'book-categories-list',
				 'uses'	=>	'BookCategoriesController@getListView'
				]);

		Route::get('/view-category/{id}',
				['as'	=>	'book-categories-view',
				 'uses'	=>	'BookCategoriesController@getViewview']);

		Route::get('/edit-category/{id}',
				['as'	=>	'book-categories-edit-get',
				 'uses'	=>	'BookCategoriesController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create-category',
					['as'	=>	'book-categories-create-post',
					 'uses'	=>	'BookCategoriesController@postCreateView']);

			Route::post('/edit-category/{id}',
					['as'	=>	'book-categories-edit-post',
					 'uses'	=>	'BookCategoriesController@postEditView']);

			Route::post('/delete-category',
					['as'	=>	'book-categories-delete-post',
					 'uses'	=>	'BookCategoriesController@deleteRows']);

			Route::post('/purge-category',
					['as'	=>	'book-categories-purge-post',
					 'uses'	=>	'BookCategoriesController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'book-categories-purge-record-post',
					 'uses'	=>	'BookCategoriesController@postDelete']);

		});
	});
});