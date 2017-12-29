<?php

Route::group(array('prefix'=>'/list','before'=>'reg-superadmin'),function(){
	Route::get('/create',array(
		'as'=>'list-create-get',
		'uses'=>'ListController@getList'
	));

	Route::get('/edit/{id}/{title}',array(
		'as'=>'list-edit-get',
		'uses'=>'ListController@getEdit'
	));

	Route::get('/delete/{id}/{title}',array(
		'as' => 'list-delete-get',
		'uses' => 'ListController@getDelete'
	));
	
	Route::group(array('before'=>'csrf'),function(){
		Route::post('/create',array(
			'as'=>'list-create-post',
			'uses'=>'ListController@postList'
		));

		Route::post('/edit',array(
			'as'=>'list-edit-post',
			'uses'=>'ListController@postEdit'
		));
	});
});
