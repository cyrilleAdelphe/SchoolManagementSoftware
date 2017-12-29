<?php

/*
SELECT   id, count(oID) 
FROM     MyTable 
GROUP BY oID 
HAVING   count(oID) = 1
*/
	// this api is for android app (moved to api module)
	// Route::post('api/send-message', [
	// 	'as'	=>	'api-message-send',
	// 	'uses' => 	'MessageController@apiPostSendMessage'
	// ]);
	
	Route::group(array('prefix' => 'message'), function()
	{
		Route::group(array('before' => 'csrf'), function()
		{
			// this api is for website
			Route::post('api-send',
				['as'	=>	'message-api-send',
				 'uses' => 	'MessageController@apiPostSendMessage']);

			Route::post('api-mark-viewed',
				['as'	=>	'message-api-mark-viewed',
				 'uses' => 	'MessageController@apiPostMarkViewed']);

			Route::post('api-post-notice',
				['as'	=>	'api-post-notice',
				 'uses' => 	'MessageController@apiPostNotice']);

			Route::post('delete-notification',
				[
					'as'	=> 'delete-notification',
					'uses'=> 'MessageController@postDeleteNotification'
				]
			);
		});

		Route::get('api-get-notices/{user_id}/{user_group}',
				[
					'as'	=> 'api-get-notices',
					'uses'=> 'MessageController@apiGetNotices'
				]);
			
			Route::group(array('before' => 'reg-superadmin-admin-user'), function()
			{

				Route::get('list',
					['as'	=>	'message-list',
					 'uses'	=>	'MessageController@getMessageList']);

///////////////////////////////////////////////////////////////////////////////////
				Route::get('staffs-history-list',
					['as'	=> 'message-staffs-history-list',
					 'uses'	=> 'MessageController@getStaffsHistoryList']
					);

				Route::get('staff-contact-list/{staff_group}/{staff_id}',
					['as'	=> 'message-staff-contact-list',
					 'uses'	=> 'MessageController@getStaffContactList']
					);

				Route::get('staff-conversation/{staff_group}/{staff_id}/{customer_group}/{customer_id}',
					['as'	=>	'message-staff-conversation',
					'uses'	=>	'MessageController@getStaffConversation']);
////////////////////////////////////////////////////////////////////////////////////////////

				Route::get('notifications',
					['as'	=>	'notification-list',
					 'uses'	=>	'MessageController@getNotifications']);

				Route::get('view/{group}/{id}/{is_staff_history?}', //dont forget to check if allowed to view or not
					['as'	=>	'message-view',
					 'uses'	=>	'MessageController@getView']);

				Route::get('create', //dont forget to check if allowed to view or not
					['as'	=>	'message-create-get',
					 'uses'	=>	'MessageController@getCreate']);

				Route::group(array('before' => 'csrf'), function()
				{

					Route::post('create', //dont forget to check if allowed to view or not
					['as'	=>	'message-create-post',
					 'uses'	=>	'MessageController@postCreate']);					

				});

			});

			Route::group(array('prefix' => 'ajax'), function()
			{
				Route::get('list',
					['as'	=>	'message-ajax-list',
					 'uses'	=>	'MessageController@ajaxGetMessageList']);

				Route::get('staffs-history-list',
					['as'	=> 'message-ajax-staffs-history-list',
					 'uses'	=> 'MessageController@ajaxGetStaffsHistoryList']
					);

				Route::get('staff-contact-list/{staff_group}/{staff_id}',
					['as'	=> 'message-ajax-staff-contact-list',
					 'uses'	=> 'MessageController@ajaxGetStaffContactList']
					);

			});

	});