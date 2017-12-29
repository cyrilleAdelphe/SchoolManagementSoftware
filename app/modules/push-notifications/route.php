<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'push-notifications'), function(){

		Route::get('/list',
				['as'	=>	'push-notifications-list',
				 'uses'	=>	'PushNotificationsController@getListView']);

		Route::get('/create',
				['as'	=>	'push-notifications-create-get',
				 'uses'	=>	'PushNotificationsController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'push-notifications-view',
				 'uses'	=>	'PushNotificationsController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'push-notifications-edit-get',
				 'uses'	=>	'PushNotificationsController@getEditView']);

		Route::get('/ajax-get-push-notifications',
				['as'	=>	'ajax-push-notifications-get-push-notifications',
				 'uses'	=>	'PushNotificationsController@ajaxGetPushNotifications']
			);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'push-notifications-create-post',
					 'uses'	=>	'PushNotificationsController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'push-notifications-edit-post',
					 'uses'	=>	'PushNotificationsController@postEditView']);

			Route::post('/delete',
					['as'	=>	'push-notifications-delete-post',
					 'uses'	=>	'PushNotificationsController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'push-notifications-purge-post',
					 'uses'	=>	'PushNotificationsController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'push-notifications-purge-record-post',
					 'uses'	=>	'PushNotificationsController@postDelete']);

		});
	});

//});

	