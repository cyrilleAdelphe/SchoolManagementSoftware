<?php

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'pdr'), function(){

		Route::get('/list',
				['as'	=>	'pdr-list',
				 'uses'	=>	'PdrController@getListView']);

		Route::get('/create',
				['as'	=>	'pdr-create-get',
				 'uses'	=>	'PdrController@getCreateView']);

		Route::get('/partials/create',
				['as'	=>	'pdr-partials-create-get',
				 'uses'	=>	'PdrController@partialsGetCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'pdr-view',
				 'uses'	=>	'PdrController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'pdr-edit-get',
				 'uses'	=>	'PdrController@getEditView']);

		Route::get('/list-feedback/{pdr_id}',
				['as'	=>	'pdr-feedback-list',
				 'uses'	=>	'PdrController@getPdrFeedBackListView']);

		Route::get('/view-feedback/{id}',
				['as'	=>	'pdr-view-feedback',
				 'uses'	=>	'PdrController@getFeedBackViewview']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'pdr-create-post',
					 'uses'	=>	'PdrController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'pdr-edit-post',
					 'uses'	=>	'PdrController@postEditView']);

			Route::post('/delete',
					['as'	=>	'pdr-delete-post',
					 'uses'	=>	'PdrController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'pdr-purge-post',
					 'uses'	=>	'PdrController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'pdr-purge-record-post',
					 'uses'	=>	'PdrController@postDelete']);

		});
	});

});

