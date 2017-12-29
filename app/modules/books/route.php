<?php
Route::group(array('prefix'=>'library'),function(){
	/*
	 * Routes requiring adminstrative privilege
	 */
	Route::group(array('before'=>'reg-librarian'),function(){
		Route::get('/books-list',
				[
					'as' 	=> 'books-list',
					'uses'	=> 'BooksController@getListView'
				]);

		Route::get('/books-entry',
				[
					'as' 	=> 'books-create-get',
					'uses'	=> 'BooksController@getCreateView'
				]);

		Route::get('/books-view/{id}',
				['as'	=>	'books-view',
				 'uses'	=>	'BooksController@getViewview']);

		Route::get('/books-edit/{id}',
				['as'	=>	'books-edit-get',
				 'uses'	=>	'BooksController@getEditView']);

		Route::group(array('before'=>'csrf'),function(){

			Route::post('/books-entry',
					[
						'as'	=> 'books-create-post',
						'uses'	=> 'BooksController@postCreateView'
					]);

			Route::post('/books-edit/{id}',
					['as'	=>	'books-edit-post',
					 'uses'	=>	'BooksController@postEditView']);

			Route::post('/books-delete',
					['as'	=>	'books-delete-post',
					 'uses'	=>	'BooksController@deleteRows']);

			Route::post('/books-purge',
					['as'	=>	'books-purge-post',
					 'uses'	=>	'BooksController@purgeRows']);

			Route::post('/books-purge-record',
					['as'	=>	'books-purge-record-post',
					 'uses'	=>	'BooksController@postDelete']);

		});
	});
});