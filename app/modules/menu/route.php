<?php
Route::group(array('prefix'=>'menu'),function(){
	Route::group(['before' => 'reg-superadmin-admin'], function()
{
	Route::get('/view/{id}',array(
			'as' => 'menu-view',
			'uses' => 'MenuController@getViewview'
	));

	Route::group(array('before'=>'reg-content-manager'),function(){

		Route::get('/create',array(
			'as' => 'menu-create-get',
			'uses' => 'MenuController@getCreate'
		));

		Route::get('/edit/{id}/{alias}',array(
			'as' => 'menu-edit-get',
			'uses' => 'MenuController@getEdit'
		));

		Route::get('/delete/{id}/{alias}',array(
			'as' => 'menu-delete-get',
			'uses' => 'MenuController@getDelete'
		));

		

		Route::group(array('before'=>'csrf'),function(){
			Route::post('/create',array(
				'as' => 'menu-create-post',
				'uses' => "MenuController@postCreate"
			));

			Route::post('/edit',array(
				'as' => 'menu-edit-post',
				'uses' => "MenuController@postEdit"
			));
		});

	});
	
});
});