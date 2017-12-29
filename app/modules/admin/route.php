<?php

Route::group(array('prefix' => 'admin'), function()
{
	Route::group(array('before' => 'guest-admin'), function()
	{
		/*Route::get('/register',
			['as'	=>	'admin-register',
			 'uses'	=>	'AdminController@getRegister']);*/
/*
		Route::get('/login',
			['as'	=>	'admin-login',
			 'uses'	=>	'AdminController@getLogin']);*/
	});

	Route::group(array('before' => 'reg-admin'), function()
	{
		Route::get('/home', 
			['as'	=>	'admin-home',
			 'uses'	=>	'AdminController@home']);

		Route::get('/logout',
			['as'	=>	'admin-logout',
			 'uses'	=>	'AdminController@logout']); 
	});
	
///////////////////////////////////////////////////////////////////////////////////////////
	Route::group(array('before' => 'guest-admin'), function()
	{
		Route::group(array('before' => 'csrf'), function(){

		});
	});
	////////////////////////////////////////////////////////
	
	/////// AdminDashboard-v1-changed-made-here ///////// 
	Route::post('/change-details',
		[
			'as'	=> 'admin-change-details-post',
			'uses'=> 'AdminController@postChangeDetails',
			'before' => array('reg-admin', 'csrf')
		]
	);
	/////// AdminDashboard-v1-changed-made-here /////////
});