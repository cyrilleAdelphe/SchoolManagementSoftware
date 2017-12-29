<?php

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'academic-session'), function(){

		Route::get('/list',
				['as'	=>	'academic-session-list',
				 'uses'	=>	'AcademicSessionController@getListView']);

		Route::get('/create',
				['as'	=>	'academic-session-create-get',
				 'uses'	=>	'AcademicSessionController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'academic-session-view',
				 'uses'	=>	'AcademicSessionController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'academic-session-edit-get',
				 'uses'	=>	'AcademicSessionController@getEditView']);
				 
		Route::get('migrate-session',
			['as'	=>	'academic-session-migrate-session-get',
			 'uses'	=>	'SessionMigrateController@getMigrateSession']);


		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'academic-session-create-post',
					 'uses'	=>	'AcademicSessionController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'academic-session-edit-post',
					 'uses'	=>	'AcademicSessionController@postEditView']);

			Route::post('/delete',
					['as'	=>	'academic-session-delete-post',
					 'uses'	=>	'AcademicSessionController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'academic-session-purge-post',
					 'uses'	=>	'AcademicSessionController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'academic-session-purge-record-post',
					 'uses'	=>	'AcademicSessionController@postDelete']);
					 
			Route::post('migrate-session',
			['as'	=>	'academic-session-migrate-session-post',
			 'uses'	=>	'SessionMigrateController@postMigrateSession']);
	
		});
	});

});

	