<?php

	Route::group(array('prefix' => 'staff-request'), function()
	{
		Route::group(array('before' => 'csrf'), function()
		{

			Route::post('api-send',
				['as'	=>	'staff-request-api-send',
				 'uses' => 	'StaffRequestController@apiPostSendMessage']);

			Route::post('api-mark-viewed',
				['as'	=>	'staff-request-api-mark-viewed',
				 'uses' => 	'StaffRequestController@apiPostMarkViewed']);

			Route::group(array('before' => 'reg-superadmin'), function() {
				Route::post('approve-request', [
					'as' => 'staff-request-api-approve',
					'uses' => 'StaffRequestController@apiPostApprove'
				]);
			});

		});

		Route::group(array('before' => 'reg-superadmin'), function() {
			Route::get('create-others-reqest', [
				'as' => 'staff-request-create-others-request',
				'uses' => 'StaffRequestController@createOthersRequest'
			]);
		});

		Route::group(array('before' => 'reg-superadmin-admin'), function()
		{
			Route::get('list',
				['as'	=>	'staff-request-list',
				 'uses'	=>	'StaffRequestController@getMessageList']);

			///////////////////////////////////////////////////////////////////////////////////
			Route::get('staffs-history-list',
				['as'	=> 'staff-request-staffs-history-list',
				 'uses'	=> 'StaffRequestController@getStaffsHistoryList']
				);

			Route::get('staff-contact-list/{staff_group}/{staff_id}',
				['as'	=> 'staff-request-staff-contact-list',
				 'uses'	=> 'StaffRequestController@getStaffContactList']
				);

			Route::get('staff-conversation/{staff_group}/{staff_id}/{customer_group}/{customer_id}',
				['as'	=>	'staff-request-staff-conversation',
				'uses'	=>	'StaffRequestController@getStaffConversation']);
			////////////////////////////////////////////////////////////////////////////////////////////

			Route::get('view/{group}/{id}/{is_staff_history?}', //dont forget to check if allowed to view or not
				['as'	=>	'staff-request-view',
				 'uses'	=>	'StaffRequestController@getView']);

			Route::get('create', //dont forget to check if allowed to view or not
				['as'	=>	'staff-request-create-get',
				 'uses'	=>	'StaffRequestController@getCreate']);

			Route::group(array('before' => 'csrf'), function()
			{

				Route::post('create', //dont forget to check if allowed to view or not
				['as'	=>	'staff-request-create-post',
				 'uses'	=>	'StaffRequestController@postCreate']);					

			});

		});

		Route::group(array('prefix' => 'ajax'), function()
		{
			Route::get('list',
				['as'	=>	'staff-request-ajax-list',
				 'uses'	=>	'StaffRequestController@ajaxGetMessageList']);

			Route::get('staffs-history-list',
				['as'	=> 'staff-request-ajax-staffs-history-list',
				 'uses'	=> 'StaffRequestController@ajaxGetStaffsHistoryList']
				);

			Route::get('staff-contact-list/{staff_group}/{staff_id}',
				['as'	=> 'staff-request-ajax-staff-contact-list',
				 'uses'	=> 'StaffRequestController@ajaxGetStaffContactList']
				);

		});

	});