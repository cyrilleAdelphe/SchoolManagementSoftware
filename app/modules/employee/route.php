<?php

Route::group(array('prefix' => 'employee-ajax'), function() {
	Route::get('document-list', [
		'as'	=> 'employee-ajax-document-list',
		'uses'=> 'EmployeeController@ajaxDocumentList'
	]);
});

Route::post('employee/change-password-by-superadmin-post/{id}', [
	'as'	=> 'employee-change-password-by-superadmin-post',
	'uses'=> 'EmployeeController@changePasswordBySuperadmin'
]);

Route::group(array('before' => 'reg-superadmin-admin'), function()
{
	Route::group(array('prefix' => 'employee'), function(){

		Route::get('/list',
				['as'	=>	'employee-list',
				 'uses'	=>	'EmployeeController@getListView']);

		Route::get('/create',
				['as'	=>	'employee-create-get',
				 'uses'	=>	'EmployeeController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'employee-view',
				 'uses'	=>	'EmployeeController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'employee-edit-get',
				 'uses'	=>	'EmployeeController@getEditView']);
		
		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'employee-create-post',
					 'uses'	=>	'EmployeeController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'employee-edit-post',
					 'uses'	=>	'EmployeeController@postEditView']);

			Route::post('/delete',
					['as'	=>	'employee-delete-post',
					 'uses'	=>	'EmployeeController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'employee-purge-post',
					 'uses'	=>	'EmployeeController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'employee-purge-record-post',
					 'uses'	=>	'EmployeeController@postDelete']);
		});
	});

});

	