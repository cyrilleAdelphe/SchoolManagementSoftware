<?php

Route::group(array('prefix'=>'slides','before'=>'reg-content-manager'),function(){
	Route::get('/list',
			['as'	=>	'slides-list',
			 'uses'	=>	'SlidesController@getListView']);

	Route::get('/create',
			['as'	=>	'slides-create-get',
			 'uses'	=>	'SlidesController@getCreateView']);

	Route::get('/view/{id}',
			['as'	=>	'slides-view',
			 'uses'	=>	'SlidesController@getViewview']);

	Route::get('/edit/{id}',
			['as'	=>	'slides-edit-get',
			 'uses'	=>	'SlidesController@getEditView']);

	
	Route::group(array('before' => 'csrf'), function(){

		Route::post('/create',
				['as'	=>	'slides-create-post',
				 'uses'	=>	'SlidesController@postCreateView']);

		Route::post('/edit/{id}',
				['as'	=>	'slides-edit-post',
				 'uses'	=>	'SlidesController@postEditView']);

		Route::post('/delete',
				['as'	=>	'slides-delete-post',
				 'uses'	=>	'SlidesController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'slides-purge-post',
				 'uses'	=>	'SlidesController@purgeRows']);

		Route::post('/purge-record',
					['as'	=>	'slides-purge-record-post',
					 'uses'	=>	'SlidesController@postDelete']);

	});
	
});