<?php

Route::group(array('prefix' => 'house','before'=>'reg=content-manager'), function(){

	Route::get('view-house', [
		'as'	=> 'list-house',
		'uses'	=> 'HouseController@getHouseView'
		]);

	Route::get('update-house/{id}', [
		'as'	=> 'house-edit',
		'uses'	=> 'HouseController@getEditHouse'
		]);

	Route::get('delete-house/{id}', [
		'as'	=> 'delete-house',
		'uses'	=> 'HouseController@getDeleteHouse'
		]);

Route::group(array('before'=>'csrf'), function() {

	Route::post('create-house',[
		'as'	=> 'create-house',
		'uses'	=> 'HouseController@postCreatehouse'
		]);

	Route::post('update-house/{id}', [
		'as'	=> 'update-house',
		'uses'	=> 'HouseController@postUpdateHouse'

		]);

});

	

	
});