<?php

Route::get('/register',
	['as'	=>	'users-register',
	 'uses'	=>	'UsersController@getRegister']);

Route::get('/confirmation/{id}/{code}',
	['as'	=>	'users-confirmation',
	 'uses'	=>	'UsersController@getConfirmation']);

Route::group(array('before' => 'guest-admin-user'),function()
{
	Route::get('/login',
		['as'	=>	'users-login',
		 'uses'	=>	'UsersController@getLogin']);

});

	Route::group(array('prefix' => 'users'), function(){

		Route::get('/list',
				['as'	=>	'users-list',
				 'uses'	=>	'UsersController@getListView']);

		Route::get('/create',
				['as'	=>	'users-create-get',
				 'uses'	=>	'UsersController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'users-view',
				 'uses'	=>	'UsersController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'users-edit-get',
				 'uses'	=>	'UsersController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'users-create-post',
					 'uses'	=>	'UsersController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'users-edit-post',
					 'uses'	=>	'UsersController@postEditView']);

		});
	});

Route::group(array('before' => 'reg-user'), function()
{
	Route::get('/home', 
		['as'	=>	'users-home',
		 'uses'	=>	'UsersController@home']);
});

Route::group(array('before' => 'reg-admin-user'), function()
{
	Route::get('/logout',
		['as'	=>	'users-logout',
		 'uses'	=>	'UsersController@logout']);
});

Route::group(array('before' => 'csrf'), function(){

	Route::post('/register',
		['as'	=>	'users-register-post',
		 'uses'	=>	'UsersController@postRegister']);
	Route::group(array('before' => 'guest-user'),function()
	{

		Route::post('/login',
			['as'	=>	'users-login-post',
			 'uses'	=>	'UsersController@postLogin']);

	});
});