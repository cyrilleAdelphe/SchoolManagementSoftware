<?php

class SavePushNotificationsController extends BaseController
{
	protected $view = 'save-push-notifications.views.';

	protected $model_name = 'SavePushNotifications';

	protected $module_name = 'save-push-notifications';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'user_group',
										'alias'			=> 'Group'
									),
									array
									(
										'column_name' 	=> 'user_id',
										'alias'			=> 'User'
									),
									array
									(
										'column_name' 	=> 'message',
										'alias'			=> 'Message'
									)
								 );

}
