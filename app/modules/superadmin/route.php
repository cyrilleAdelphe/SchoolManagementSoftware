<?php

Route::group(array('prefix' => 'superadmin'), function()
{
	Route::get('/register',
		['before' => 'reg-superadmin',
		 'as'	=>	'superadmin-register',
		 'uses'	=>	'SuperAdminController@getRegister']);

/////////////////////////////////////////////////////////////////////////////////////
	Route::get('/login',
		['before' => 'guest-superadmin',
		 'as'	=>	'superadmin-login',
		 'uses'	=>	'SuperAdminController@getLogin']);

////////////////////////////////////////////////////////////////////////////////////////
	Route::get('/create-group',
		['before' => 'reg-superadmin',
		 'as'	=>	'create-group',
		 'uses'	=>	'SuperAdminController@getCreateGroup']);

	Route::get('/list-groups', 
		['before' => 'reg-superadmin', 
		 'as'	=>	'list-groups',
		 'uses'	=>	'SuperAdminController@listGroups']);

	Route::get('/edit-group/{id}',
		['before' => 'reg-superadmin',
		 'as'	=>	'edit-group',
		 'uses'	=>	'SuperAdminController@getEditGroup']);
//////////////////////////////////////////////////////////////////////////////////////

	Route::get('/create-module-function/{controller_id}',
		['before' => 'reg-superadmin',
		 'as'	=>	'create-module-function',
		 'uses'	=>	'SuperAdminController@getCreateModuleFunction']);

	Route::get('/list-module-functions/{controller_name}',
		['before' => 'reg-superadmin',
		 'as'	=>	'list-module-funcitons',
		 'uses'	=>	'SuperAdminController@listModuleFunctions']);
/////////////////////////////////////////////////////////////////////////////////////////
	Route::get('/permissions/create',
		['before' => 'reg-superadmin',
		 'as'	=>	'permissions-create-get',
		 'uses'	=>	'SuperAdminController@getCreatePermissions']);
///////////////////////////////////////////////////////////////////////////////////////////////
	Route::get('/permissions-by-routename/create',
				['before'	=> 'reg-superadmin',
				'as'		=> 'permissions-by-routename-create-get',
				'uses'		=> 'SuperAdminController@getCreatePermissionsByRouteName']);
/////////////////////////////////////////////////////////////////////////////////////////////

	Route::get('/permissions-by-routename/edit/{id}',
				['before'	=> 'reg-superadmin',
				'as'		=> 'permissions-by-routename-edit-get',
				'uses'		=> 'SuperAdminController@getEditPermissionsByRouteName']);
/////////////////////////////////////////////////////////////////////////////////////////////
	Route::get('/permissions-by-routename/list',
				['before'	=> 'reg-superadmin',
				'as'		=> 'permissions-by-routename-list',
				'uses'		=> 'SuperAdminController@listPermissionsByRouteName']);
/////////////////////////////////////////////////////////////////////////////////////////////

	Route::get('/home', 
		['before' => 'reg-superadmin',
		 'as'	=>	'superadmin-home',
		 'uses'	=>	'SuperAdminController@home']);
/////////////////////////////////////////////////////////////////////////////////////////////
	
	Route::get('/logout',
		['before' => 'reg-superadmin',
		 'as'	=>	'superadmin-logout',
		 'uses'	=>	'SuperAdminController@logout']);
////////////////////////////////////////////////////////////////////////////////////////////////	
	
	Route::get('generate-route', 
	['before' => 'reg-superadmin',
	 'as'	=> 'generate-route-superadmin',
	 'uses'	=> 'SuperAdminController@getGenerateRoute']);
////////////////////////////////////////////////////////////////////////////////////////////////
	
	Route::get('list-admins', 
	['before' => 'reg-superadmin',
	 'as'	=> 'list-admins',
	 'uses'	=> 'SuperAdminController@getAdminList']);

////////////////////////////////////////////////////////////////////////////////////////////////

	Route::get('set-temporary-permissions', 
	['before' => 'reg-superadmin',
	 'as'	=> 'set-temporary-permissions',
	 'uses'	=> 'SuperAdminController@getSetTemporaryPermissions']);

	Route::get('edit-temporary-permissions/{id}', 
				['before' => 'reg-superadmin',
				 'as'	=> 'edit-temporary-permissions',
				 'uses'	=> 'SuperAdminController@getEditTemporaryPermissions']);
	Route::get('/delete-notice', 
		['as' => 'delete-notice',
		'uses' => 'SuperAdminController@dashboardDeleteNotice']);
			

////////////////////////////////////////////////////////////////////////////////////////////////

	Route::group(array('before' => 'csrf'), function(){

		Route::post('/register',	
		   ['before' => 'reg-superadmin',
		    'as' 	=> 'superadmin-register-post',
			'uses' 	=> 'SuperAdminController@postRegister']);
////////////////////////////////////////////////////////////////////////////////////////////////////
		Route::post('/login', 
			['before' => 'guest-superadmin',
			 'as'	=>	'superadmin-login-post',
			 'uses'	=>	'SuperAdminController@postLogin']);
////////////////////////////////////////////////////////////////////////////////////////////////////
		Route::post('/create-group', 
			['before' => 'reg-superadmin',
			 'as' 	=> 'create-group-post',
			 'uses'	=> 'SuperAdminController@postCreateGroup']);

		Route::post('/edit-group/{id}', 
			['before' => 'reg-superadmin',
			 'as' 	=> 'edit-group-post',
			 'uses'	=> 'SuperAdminController@postEditGroup']);

////////////////////////////////////////////////////////////////////////////////////////////////////////
		Route::post('/permissions/create',
			['before' => 'reg-superadmin',
			 'as'	=>	'permissions-create-post',
			 'uses'	=>	'SuperAdminController@postCreatePermissions']);
////////////////////////////////////////////////////////////////////////////////////////////
		Route::post('/permissions-by-routename/create',
				['before'	=> 'reg-superadmin',
				'as'		=> 'permissions-by-routename-create-post',
				'uses'		=> 'SuperAdminController@postCreatePermissionsByRouteName']);
/////////////////////////////////////////////////////////////////////////////////////////////

	Route::post('/permissions-by-routename/edit/{id}',
				['before'	=> 'reg-superadmin',
				'as'		=> 'permissions-by-routename-edit-post',
				'uses'		=> 'SuperAdminController@postEditPermissionsByRouteName']);
/////////////////////////////////////////////////////////////////////////////////////////////

		Route::post('/permissions/delete',
			['before' => 'reg-superadmin',
			 'as'	=>	'permissions-delete-post',
			 'uses'	=>	'SuperAdminController@deletePermissions']);

		Route::post('/permissions/purge',
			['before' => 'reg-superadmin',
			 'as'	=>	'permissions-purge-post',
			 'uses'	=>	'SuperAdminController@purgePermissions']);		
//////////////////////////////////////////////////////////////////////////////////////////////
		Route::post('/generate-route', 
			['before' => 'reg-superadmin',
			 'as'	=> 'generate-route-superadmin-post',
			 'uses'	=> 'SuperAdminController@postGenerateRoute']);
////////////////////////////////////////////////////////////////////////////////////////////
		
		Route::post('delete-admins', 
				['before' => 'reg-superadmin',
				 'as'	=> 'delete-admins-post',
				 'uses'	=> 'SuperAdminController@postDeleteAdmins']);
///////////////////////////////////////////////////////////////////////////////////////////////

		Route::post('set-temporary-permissions', 
				['before' => 'reg-superadmin',
				 'as'	=> 'set-temporary-permissions-post',
				 'uses'	=> 'SuperAdminController@postSetTemporaryPermissions']);

		Route::post('edit-temporary-permissions/{id}', 
				['before' => 'reg-superadmin',
				 'as'	=> 'edit-temporary-permissions-post',
				 'uses'	=> 'SuperAdminController@postEditTemporaryPermissions']);
	});
///////////////////////////////////////////////////////////////////////////////////////////////
	Route::post('/add-header', 
		['as' => 'add-header',
		'uses' => 'SuperAdminController@postAddTableHeaders']);

	Route::post('/change-details/{id}',
		[
			'as'	=> 'superadmin-change-details-post',
			'uses'=> 'SuperAdminController@postChangeDetails',
			'before' => array('reg-superadmin', 'csrf'),
			'prefix' => 'superadmin'
		]
	);
});