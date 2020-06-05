<?php
Route::group(array('prefix'=>'library'),function(){
	/*
	 * Routes requiring adminstrative privilege
	 */

	Route::get('download-book-barcodes/{id}', [
				'as' => 'download-book-barcodes', 
				'uses' => 'BooksController@getDownloadBooksBarcode'

		]);

	Route::get('generete-bar-code-get', [
				'as' => 'generete-bar-code-get', 
				'uses' => 'BooksController@getGenerateBarCodeView'
		]);

	Route::get('generate-bar-codes-from-books/{book_id}', [

				'as' => 'generate-bar-codes-from-books', 
				'uses' => 'BooksController@getGenerateBarCodesfromBooks'
		]);




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

	Route::post('upload-books-from-excel', [
					'as' => 'upload-books-from-excel', 
					'uses' => 'BooksController@postUploadBooksFromLibrary'

				]);
	Route::post('generate-bar-code-post', [
					'as' => 'generate-bar-code-post', 
					'uses' => 'BooksController@generateBarCodePost'

				]);
});