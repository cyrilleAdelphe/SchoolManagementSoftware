<?php
Route::group(array('prefix'=>'routine'),function(){
	/*
	 * Routes requiring adminstrative privilege
	 */
	Route::group(array('before'=>'reg-superadmin-admin'),function(){
		Route::get('/routine-list',
				[
					'as' 	=> 'routine-list',
					'uses'	=> 'RoutineController@getListView'
				]);

		Route::get('/routine-entry',
				[
					'as' 	=> 'routine-create-get',
					'uses'	=> 'RoutineController@getCreateView'
				]);

		Route::get('/routine-view/{id}',
				['as'	=>	'routine-view',
				 'uses'	=>	'RoutineController@getViewview']);

		Route::get('/routine-edit/{id}',
				['as'	=>	'routine-edit-get',
				 'uses'	=>	'RoutineController@getEditView']);

		Route::group(array('before'=>'csrf'),function(){

			Route::post('/routine-entry',
					[
						'as'	=> 'routine-create-post',
						'uses'	=> 'RoutineController@postCreateView'
					]);

			Route::post('/routine-edit/{id}',
					['as'	=>	'routine-edit-post',
					 'uses'	=>	'RoutineController@postEditView']);

			Route::post('/routine-delete',
					['as'	=>	'routine-delete-post',
					 'uses'	=>	'RoutineController@deleteRows']);

			Route::post('/purge-category',
					['as'	=>	'routine-purge-post',
					 'uses'	=>	'RoutineController@purgeRows']);
		});
	});
});