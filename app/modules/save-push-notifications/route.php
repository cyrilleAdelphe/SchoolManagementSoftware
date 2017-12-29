<?php

Route::group(array('prefix'=>'save-push-notifications','before'=>'reg-superadmin-admin'),function(){
	Route::get('/list',
			['as'	=>	'save-push-notifications-list',
			 'uses'	=>	'SavePushNotificationsController@getListViewSimple']);

	/*Route::get('/create',
			['as'	=>	'save-push-notifications-create-get',
			 'uses'	=>	'SavePushNotificationsController@getCreateView']);*/

	/*Route::get('/view/{id}',
			['as'	=>	'save-push-notifications-view',
			 'uses'	=>	'SavePushNotificationsController@getViewview']);*/

	/*Route::get('/edit/{id}',
			['as'	=>	'save-push-notifications-edit-get',
			 'uses'	=>	'SavePushNotificationsController@getEditView']);*/

	
	Route::group(array('before' => 'csrf'), function(){

		/*Route::post('/create',
				['as'	=>	'save-push-notifications-create-post',
				 'uses'	=>	'SavePushNotificationsController@postCreateView']);

		Route::post('/edit/{id}',
				['as'	=>	'save-push-notifications-edit-post',
				 'uses'	=>	'SavePushNotificationsController@postEditView']);*/

		Route::post('/delete',
				['as'	=>	'save-push-notifications-delete-post',
				 'uses'	=>	'SavePushNotificationsController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'save-push-notifications-purge-post',
				 'uses'	=>	'SavePushNotificationsController@purgeRows']);

		Route::post('/purge-record',
					['as'	=>	'save-push-notifications-purge-record-post',
					 'uses'	=>	'SavePushNotificationsController@postDelete']);

	});
	
});