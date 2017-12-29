<?php

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'guardian'), function(){

		Route::get('/list',
				['as'	=>	'guardian-list',
				 'uses'	=>	'GuardianController@getListView']);

		Route::get('/create',
				['as'	=>	'guardian-create-get',
				 'uses'	=>	'GuardianController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'guardian-view',
				 'uses'	=>	'GuardianController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'guardian-edit-get',
				 'uses'	=>	'GuardianController@getEditView']);

		Route::get('/import-excel',
			[
				'as'	=> 'guardian-import-excel-get',
				'uses'=> 'GuardianController@getImportExcel'
			]
		);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'guardian-create-post',
					 'uses'	=>	'GuardianController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'guardian-edit-post',
					 'uses'	=>	'GuardianController@postEditView',
					 ]);
			
			Route::post('/delete',
					['as'	=>	'guardian-delete-post',
					 'uses'	=>	'GuardianController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'guardian-purge-post',
					 'uses'	=>	'GuardianController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'guardian-purge-record-post',
					 'uses'	=>	'GuardianController@postDelete']);

			Route::post('/import-excel',
				[
					'as'	=> 'guardian-import-excel-post',
					'uses'=> 'GuardianController@postImportExcel'
				]
			);

		});

		Route::group(array('prefix' => 'ajax'), function()
		{
			Route::get('search-students',
			[
				'as'	=>	'ajax-search-students',
				'uses'	=>	'GuardianController@ajaxSearchStudents'
			]);
		});
	});
});

Route::post('/change-password/{id}',
				[
					'as'	=> 'guardian-change-password-post',
					'uses'=> 'GuardianController@postChangePassword',
					'before' => array('reg-superadmin-admin-user', 'csrf'),
					'prefix' => 'guardian'
				]
			);

	
	Route::post('/modal-edit/{id}',
					['as'	=>	'guardian-modal-edit-post',
					 'uses'	=>	'GuardianController@modalEditGuardianDetails',
					 'before' => array('reg-superadmin-admin-user', 'csrf'),
					 'prefix' => 'guardian']);

	Route::get('/view/{id}',
				['as'	=>	'guardian-view',
				 'uses'	=>	'GuardianController@getViewview',
				 'before' => array('reg-superadmin-admin-user'),
				 'prefix' => 'guardian']);

	