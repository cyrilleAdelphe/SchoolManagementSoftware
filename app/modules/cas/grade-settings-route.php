<?php

Route::group(['prefix' => 'cas'], function()
{
	Route::get('grade-settings-list',
		['as'	=>	'cas-grade-settings-list',
		 'uses'	=> 	'CasGradeSettingsController@getGradeSettingsListView']);

	Route::get('grade-settings-edit',
		['as'	=>	'cas-grade-settings-edit-get',
		 'uses'	=> 	'CasGradeSettingsController@getEditGradeSettingsView']);

	Route::group(['before' => 'csrf'], function()
	{

		Route::post('grade-settings-create',
			['as'	=>	'cas-grade-settings-post',
			 'uses'	=>	'CasGradeSettingsController@postGradeSettingsCreatePost']);

		Route::post('grade-settings-edit-check',
		['as'	=>	'cas-grade-settings-edit-post',
		 'uses'	=> 	'CasGradeSettingsController@postEditGradeSettingsView']);

	});


	/// these are for apis
	Route::group(['prefix' => 'api'], function()
	{
		Route::get('grade-settings-list-from-class-to-class',
			['as'	=>	'cas-api-grade-settings-list-from-class-to-class',
			 'uses'	=>	'CasGradeSettingsController@apiGetGradeSettingsListFromClassToClass']);
	});

});


