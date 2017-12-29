<?php

class SMSController extends BaseController
{
	protected $view = 'sms.views.';

	protected $model_name = 'SMS';

	protected $module_name = 'sms';

	protected $role;

	public $columnsToShow = array(
		array
		(
			'column_name'	=> 'user_group',
			'alias'				=> 'User group'
		),
		array
		(
			'column_name' 	=> 'unseen_users',
			'alias'			=> 'Unseen Users'
		),
		array
		(
			'column_name' 	=> 'seen_users',
			'alias'			=> 'Seen Users'
		),
		array
		(
			'column_name' 	=> 'subject',
			'alias'			=> 'Subject'
		),
		array
		(
			'column_name' 	=> 'message',
			'alias'			=> 'Message'
		),
		array
		(
			'column_name'	=> 'created_at',
			'alias'			=> 'Sent at'
		)
	);
	
	public function getListView()
	{
		
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		$queries = $this->getQueries();

		$available_credits = SmsMainController::makeCurlRequest(MAIN_SERVER_URL.'sms-master/api/get-credits/'.SCHOOL_UNIQUE_ID);

		return View::make($this->view.'list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					->with('paginateBar', $this->getPaginateBar())
					->with('role', $this->role)
					->with('available_credits', $available_credits);

	}	

	public function getViewView($message_group_id)
	{

		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$model = new $this->model_name;

		$data = $model->getViewViewData($message_group_id);
		
		return View::make($this->view.'view')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('message_group_id', $message_group_id);
					//->with('actionButtons', $this->getActionButtons());

	}

	public function postSendSMS()
	{
		AccessController::allowedOrNot($this->module_name, 'can_send_sms');
		$model = $this->model_name;
		$message_group_id = Input::get('message_group_id', 0);
		$user_group = Input::get('user_group', '');

		$unsent_messages = $model::where('is_active', 'yes')->get();

		switch ($user_group)
		{
			case 'student':
			$data = StudentRegistration::join(
				$model::getTableName(),
				$model::getTableName() . '.user_id', '=',
				StudentRegistration::getTableName() . '.id'
			)->select('guardian_contact as contact', 'message', $model::getTableName() . '.id'
			)->where(
				$model::getTableName() . '.message_group_id', $message_group_id
			)->where(
				$model::getTableName() . '.is_active', 'yes'
			)->where(
				$model::getTableName() . '.user_group', 'student'
			);

			break;

			case 'guardian':
			$data = Guardian::join(
				$model::getTableName(),
				$model::getTableName() . '.user_id', '=',
				Guardian::getTableName() . '.id'
			)->join(
				StudentGuardianRelation::getTableName(),
				StudentGuardianRelation::getTableName() . '.guardian_id', '=',
				Guardian::getTableName() . '.id'
			)->join(
				StudentRegistration::getTableName(),
				StudentRegistration::getTableName() . '.id', '=',
				StudentGuardianRelation::getTableName() . '.student_id'
			)->select('guardian_contact as contact', 'message', $model::getTableName() . '.id'
			)->where(
				$model::getTableName() . '.user_group', 'guardian'
			)->where(
				$model::getTableName() . '.is_active', 'yes'
			)->where(
				$model::getTableName() . '.message_group_id', $message_group_id
			)->groupBy(
				StudentGuardianRelation::getTableName() . '.guardian_id'
			);
			break;

			case 'admin':
			$data = Employee::join(
				$model::getTableName(),
				$model::getTableName() . '.user_id', '=',
				Employee::getTableName() . '.id'
			)->select('primary_contact as contact', 'message', $model::getTableName() . '.id'
			)->where(
				$model::getTableName() . '.message_group_id', $message_group_id
			)->where(
				$model::getTableName() . '.is_active', 'yes'
			)->where(
				$model::getTableName() . '.user_group', 'admin'
			);
			break;

			case 'superadmin':
			$data = Superadmin::join(
				$model::getTableName(),
				$model::getTableName() . '.user_id', '=',
				Superadmin::getTableName() . '.id'
			)->select('contact', 'message', $model::getTableName() . '.id'
			)->where(
				$model::getTableName() . '.message_group_id', $message_group_id
			)->where(
				$model::getTableName() . '.is_active', 'yes'
			)->where(
				$model::getTableName() . '.user_group', 'superadmin'
			);
			break;
		}

		//formating the message here
		$data =$data->get();
		
		$phone = array();
		foreach($data as $d)
		{
			$phone[] = $d->contact;
		}

		$phone = array_unique($phone);
		

		//print_r($phone);
		//die();
		if(count($data))
		{
			$message = SMSHelperController::getOnlyMessage($data[0]->message);
			//die($message);
			$response = SmsMainController::makeCurlRequest(
				MAIN_SERVER_URL.'sms-master/api/send-sms/'.SCHOOL_UNIQUE_ID, 
				array(
					'phone' => $phone, 
					'message' => $message, 
					'message_group_id' => $message_group_id, 
					'sms_from' => SMS_FROM, 
					'sms_token' => SMS_TOKEN
				), 
				'post'
			);

			$response = json_decode($response, true);
			if($response['status'] == 'success')
			{
				foreach ($data as $d)
				{
					$model::where('id', $d->id)->update([
						'phone_no' => $d->contact, 
						'sms_status' => 'queued'
					]);
				}	
			}

			$message = $response['message'];

			Session::flash($response['status'].'-msg', ConfigurationController::translate($message));			
		}

		return Redirect::back();
	}

	

}
