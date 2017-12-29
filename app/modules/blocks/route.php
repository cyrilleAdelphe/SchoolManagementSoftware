<?php

Route::group(array('prefix'=>'/blocks','before'=>'reg-superadmin'),function(){
	Route::get('/create',array(
		'as'=>'blocks-create-get',
		'uses'=>'BlocksController@getCreate'
	));

	Route::get('/edit/{id}/{title}',array(
		'as'=>'blocks-edit-get',
		'uses'=>'BlocksController@getEdit'
	));

	Route::get('/delete/{id}/{title}',array(
		'as' => 'blocks-delete-get',
		'uses' => 'BlocksController@getDelete'
	));
	
	Route::group(array('before'=>'csrf'),function(){
		Route::post('/create',array(
			'as'=>'blocks-create-post',
			'uses'=>'BlocksController@postCreate'
		));

		Route::post('/edit',array(
			'as'=>'blocks-edit-post',
			'uses'=>'BlocksController@postEdit'
		));
	});
});
