<?php

Route::group(array('prefix' => 'ethnicity','before'=>'reg=content-manager'), function(){

	Route::get('/list', [
		'as'	=> 'ethnicity-list',
		'uses'	=> 'EthnicityController@getEthnicityView'
		]);

	Route::get('/edit/{id}', [
		'as'	=> 'edit-ethnicity',
		'uses'	=> 'EthnicityController@getEthnicityEdit'

		]);

	Route::get('/delete/{id}', [
		'as'	=> 'delete-ethnicity',
		'uses'	=> 'EthnicityController@deleteEthnicity'
		]);

	Route::group(array('before' => 'csrf'), function(){

	Route::post('/create', [
		'as'	=> 'ethnicity-create',
		'uses'	=> 'EthnicityController@postCreateEthnicity'

		]);

	Route::post('/update/{id}', [
		'as'	=> 'ethnicity-update',
		'uses'	=> 'EthnicityController@postEthnicityUpdate'
		]);

	});
});