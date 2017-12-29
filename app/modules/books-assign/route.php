<?php
Route::group(array('prefix'=>'library'),function(){
	/**
	 * Routes requiring superadminstrative privilege
	 */
	Route::group(array('before'=>'reg-librarian'),function(){
		Route::get('/books-assign',
			[	'as'	=>	'books-assign-create-get',
				'uses'	=>	'BooksAssignController@getCreateView'
			]);
		Route::get('/books-return',
			[
				'as'	=>	'books-assign-return-get',
				'uses'	=>	'BooksAssignController@getReturnView'
			]);

		Route::get('/assigned-history',
				['as'	=>	'books-assign-list',
				 'uses'	=>	'BooksAssignController@getListView'
				]);

		Route::get('/assigned-view/{id}',
				['as'	=>	'books-assign-view',
				 'uses'	=>	'BooksAssignController@getViewview']);

		Route::get('/assigned-edit/{id}',
				['as'	=>	'books-assign-edit-get',
				 'uses'	=>	'BooksAssignController@getEditView']);

		Route::get('/send-notification/{id}', [
				'as'	=>	'books-assign-send-notification',
				'uses'=>	'BooksAssignController@sendNotification'
			]);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/books-assign',
					['as'	=>	'books-assign-create-post',
					 'uses'	=>	'BooksAssignController@postCreateView']);

			Route::post('/books-return/{id}',
				[
					'as'	=>	'books-assign-return-post',
					'uses'	=>	'BooksAssignController@postReturnView'
				]);

			Route::post('/assigned-edit/{id}',
					['as'	=>	'books-assign-edit-post',
					 'uses'	=>	'BooksAssignController@postEditView']);

			Route::post('/assigned-delete',
					['as'	=>	'books-assign-delete-post',
					 'uses'	=>	'BooksAssignController@deleteRows']);

			Route::post('/assigned-purge',
					['as'	=>	'books-assign-purge-post',
					 'uses'	=>	'BooksAssignController@purgeRows']);

			Route::post('/assigned-purge-record',
					['as'	=>	'books-assign-purge-record-post',
					 'uses'	=>	'BooksAssignController@postDelete']);
		});
	});
});