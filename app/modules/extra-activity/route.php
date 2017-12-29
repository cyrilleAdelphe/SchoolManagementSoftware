<?php
Route::group(array('prefix' => 'extra-activity', 'before' => 'reg-superadmin-admin'), function() {
	
	Route::get('/create',
				['as'	=>	'extra-activity-create-get',
				 'uses'	=>	'ExtraActivityController@getCreateView']);

	Route::get('/list',
				['as'	=>	'extra-activity-list',
				 'uses'	=>	'ExtraActivityController@getListView']);

	Route::get('/view/{id}',
					['as'	=>	'extra-activity-view',
					 'uses'	=>	'ExtraActivityController@getViewview']);

	Route::get('/edit/{id}',
			['as'	=>	'extra-activity-edit-get',
			 'uses'	=>	'ExtraActivityController@getEditView']);

	Route::get('/push-notification/{id}',
		[
			'as'	=> 'extra-activity-push-notification',
			'uses'=> 'ExtraActivityController@sendPushNotification'
		]);

	Route::group(array('before' => 'csrf'), function(){

		Route::post('/create',
				['as'	=>	'extra-activity-create-post',
				 'uses'	=>	'ExtraActivityController@postCreateView']);

		Route::post('/edit/{id}',
				['as'	=>	'extra-activity-edit-post',
				 'uses'	=>	'ExtraActivityController@postEditView']);

		Route::post('/delete',
				['as'	=>	'extra-activity-delete-post',
				 'uses'	=>	'ExtraActivityController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'extra-activity-purge-post',
				 'uses'	=>	'ExtraActivityController@purgeRows']);

		Route::post('/purge-record',
					['as'	=>	'extra-activity-purge-record-post',
					 'uses'	=>	'ExtraActivityController@postDelete']);
	});
});