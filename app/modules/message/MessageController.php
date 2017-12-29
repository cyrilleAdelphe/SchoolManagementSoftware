<?php

use Carbon\Carbon;

class MessageController extends BaseController
{
	protected $view = 'message.views.';

	protected $model_name = 'Message';

	protected $module_name = 'message';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'message_name',
										'alias'			=> 'Message Name'
									),
									array
									(
										'column_name' 	=> 'email',
										'alias'			=> 'Email'
									),
									array
									(
										'column_name' 	=> 'current_address',
										'alias'			=> 'Current Address'
									),
									array
									(
										'column_name' 	=> 'contact_number',
										'alias'			=> 'Contact number'
									)
								 );

	public function apiValidateInput($input, $update = false)
	{

		$validate = Validator::make($input, (new Message)->createRule);

		if($validate->fails())
		{
			return array('status' => 'error', 'data' => $validate->messages(), 'message' => 'Please fill all the fields');
		}
		else
		{
			return array('status' => 'success');
		}

	}

	public function apiValidateInputs($inputs, $update = false)
	{
		$status = 'success';
		$message = '';

		$model = $this->model_name;
		foreach($inputs as $input)
		{
			$validate = Validator::make($input, (new $model)->createRule);

			if($validate->fails())
			{
				$status = 'error';
				$message .= 'Invalid username: ' . $input['message_to_username'] . "\n";
			}
		}

		return array('status' => $status, 'message' => $message);
	}

	public function apiSendMessage($input = array())
	{	
		try
		{
			switch($input['message_to_group'])
			{
				case 'student':

					/*$user = StudentRegistration::where('id', $input['message_to_id'])->first();
					*/
					//$gcm_id = PushNotifications
					$gcm_ids = DB::table(Users::getTableName())
						->leftJoin(PushNotifications::getTableName(), PushNotifications::getTableName().'.user_id', '=', 'user_details_id')
										->where('role', $input['message_to_group'])
										->where('user_details_id', $input['message_to_id'])
										->lists('gcm_id', 'user_details_id');
					break;

				case 'guardian':
					$gcm_ids = DB::table(Users::getTableName())
						->leftJoin(PushNotifications::getTableName(), PushNotifications::getTableName().'.user_id', '=', 'user_details_id')
										->where('role', $input['message_to_group'])
										->where('user_details_id', $input['message_to_id'])
										->lists('gcm_id', 'user_details_id');
					break;

				case 'admin':
					$gcm_ids = DB::table(Admin::getTableName())
						->leftJoin(PushNotifications::getTableName(), PushNotifications::getTableName().'.user_id', '=', 'admin_details_id')
										->where(PushNotifications::getTableName().'.user_group', $input['message_to_group'])
										->where('admin_details_id', $input['message_to_id'])
										->lists('gcm_id', 'admin_details_id');
					break;

				case 'superadmin':
					$superadmin = SuperAdmin::find($input['message_to_id']);
					$gcm = PushNotifications::where('user_id', $input['message_to_id'])
						->where('user_group', 'superadmin')
						->first();

					$gcm_ids = array(
						$input['message_to_id'] => $gcm ? $gcm->gcm_id : null
					);
					
					break;

				default:
					$gcm_ids = array('' => '');



			}
			
			$msg = 'message # ' .
							'subject: ' . $input['message_subject'] . "\n" .
							'message: ' . $input['message'];
			 
			if(count($gcm_ids))
			{
				(new GcmController)->send($gcm_ids, $msg, array_keys($gcm_ids), $input['message_to_group']);
			}

			$input['is_viewed_by_superadmin'] = 'no';
			$id = $this->storeInDatabase($input, $this->model_name);
			
			$status = 'success';
			$message = '';
			$data = $id;
		}
		catch(Exception $e)
		{
			$status = 'error';
			$message = $e->getMessage();
			$data = 0;
		}

		return array('status' => $status, 'message' => $message, 'data' => $data);
	}

	public function apiSendMessages($inputs = array())
	{
		if (!count($inputs))
		{
			return array(
				'status'	=> 'error',
				'message' => 'no user',
				'data'		=> null
			);
		}
		$user_ids = array_map(function($input) {
			return $input['message_to_id'];
		}, $inputs);

		try
		{
			
			$gcm_ids = PushNotifications::whereIn('user_id', $user_ids)
												->where('user_group', $inputs[0]['message_to_group'])
												->lists('gcm_id', 'user_id');

			$msg = 'message # ' .
							'subject: ' . $inputs[0]['message_subject'] . "\n" .
							'message: ' . $inputs[0]['message'];
			 
			if(count($user_ids))
			{
				(new GcmController)->send($gcm_ids, $msg, $user_ids, $inputs[0]['message_to_group']);
			}

			foreach ($inputs as $input)
			{
				$input['is_viewed_by_superadmin'] = 'no';
				$id = $this->storeInDatabase($input, $this->model_name);
			}

			$status = 'success';
			$message = '';
			$data = $id;
		}
		catch(PDOException $e)
		{
			$status = 'error';
			$message = $e->getMessage();
			$data = 0;
		}

		return array('status' => $status, 'message' => $message, 'data' => $data);
	
	}

	public function apiPostSendMessage()
	{

		$input = Input::all();
		$model = $this->model_name;

		switch ($input['message_to_group']) 
		{
			case 'superadmin':
				$model = 'SuperAdmin';
				$id_name = 'id';
				break;

			case 'admin':
				$model = 'Admin';
				$id_name = 'admin_details_id';
				break;

			case 'student':

			case 'guardian':
				$model = 'Users';
				$id_name = 'user_details_id';
				break;

			default:
				return json_encode(array(
					'status'	=>	'error',
					'message'	=>	'invalid user group'			
				));
				break;
		}

		if(isset($input['message_to_username'])) // this is for differentiating with the dashboard module
		{
			$usernames = explode(',', $input['message_to_username']);
			$inputs = array();
			
			foreach($usernames as $username) 
			{
				$single_input = $input;
				$user = $model::where('username', trim($username))
											->first();
				$single_input['message_to_id'] = $user ? $user->$id_name : null;
				$single_input['message_to_username'] = $username;
				$inputs[] = $single_input;
			}

			
			
			// $result = $this->apiValidateInput($input);
			$result = $this->apiValidateInputs($inputs);

			if($result['status'] == 'error')
				return json_encode($result);

			//return json_encode($this->apiSendMessage($input));
			return json_encode($this->apiSendMessages($inputs));	

		}
		else
		{
			$result = $this->apiValidateInput($input);
			if($result['status'] == 'error')
				return json_encode($result);

			return json_encode($this->apiSendMessage($input));
			
		}
		
	}

	public function apiPostMarkViewed()
	{
		$message_id = Input::get('message_id', 0);
		$status = 'error';;
		$data = $model::where('id', $message_id)->first();

		if($data)
		{
			if($data->is_active == 'no')
				//$return = array('status' => $status, 'message' => 'Message not active');	
				$message = 'Message not active';
			elseif($data->is_viewed == 'yes')
				$message = 'Message already marked viewed';
			else
			{
				try
				{
					$data->is_viewed = 'yes';
					$data->save();
					$status = 'success';
					$message = "Message Viewed";
				}
				catch(Exception $e)
				{
					$message = $e->getMessage();
				}
			}	
		}
		else
		{
			$message = 'Message not found';
		}
		

		return json_encode(array('status' => $status, 'message' => $message));

	}

	public function apiPostNotice()
	{
		$title = Input::get('notice_title', '');
		$body = Input::get('notice_body', '');

		if(strlen($title) && strlen($body))
		{
			$notice = json_encode(array(
				'title' => $title, 
				'body' => $body,
				'created_at' => date('Y-m-d H:i:s')
			));
			file_put_contents(app_path().'/modules/notice/notice.json', $notice);
			$return = array('status' => 'success', 'message' => 'Notice Posted');

			$gcm_ids = PushNotifications::lists('gcm_id');
			$msg = 'notice # ' .
							'title: ' . $title . "\n" .
							'notice: ' . $body;
			if(count($gcm_ids))
			{
				(new GcmController)->send($gcm_ids, $msg);
			}
		}
		else
		{
			$return = array('status' => 'error', 'message' => 'Notice title or Notice body not given');
		}

		return json_encode($return);
	}

	public function apiGetNotices($user_id, $user_group)
	{
		$user_model = null;
		$user_details_column = '';
		switch ($user_group)
		{
			case 'student':
			case 'guardian':
			$user_model = 'User';
			$user_details_column = 'user_details_id';
			break;

			case 'admin':
			$user_model = 'Admin';
			$user_details_column = 'admin_details_id';
			break;

			case 'superadmin':
			$user_model = 'SuperAdmin';
			$user_details_column = 'id';
			break;

			default:
			return json_encode(array(
				'status'	=> 'error',
				'message'	=> 'Invalid User Group'
			));
		}

		
		$user = $user_model::where($user_details_column, $user_id)->first();
		$model = $this->model_name;

		if(!$user)
		{
			return json_encode(array(
					'status'	=> 'error',
					'message'	=> 'Invalid User ID'
				)); 
		}

		// $notices = $model::where('message_to_id', $user->user_details_id)
		// 										->where('message_to_group', $user->role)
		// 										->select('id', 'message_subject', 'message', 'is_viewed')
		// 										->get();

		$notices = SavePushNotifications::where('user_id', $user->$user_details_column)
												->where('user_group', $user_group)
												->select('id', 'message', 'created_at', 'is_active')
												->orderBy('created_at', 'DESC')
												->get();
		foreach ($notices as $notice)
		{
			$notice->message = strip_tags($notice->message);
			$message_array = explode(' # ', $notice->message);
			if (count($message_array) != 2) 
			{
				continue;
			}
			$notice->message_subject = ucwords($message_array[0]);
			$notice->message = $message_array[1];
			$notice->date = substr($notice->created_at, 0, 10);
			if (CALENDAR == 'BS')
			{
				$notice->date = (new DateConverter)->ad2bs($notice->date);
				$notice->date = HelperController::formatNepaliDate($notice->date);
			}
			else
			{
				$notice->date = DateTime::createFromFormat('Y-m-d', $notice->date)->format('d F Y');
			}

			$notice->time = substr($notice->created_at, 11);
			$notice->time = DateTime::createFromFormat('H:i:s', $notice->time)->format('g:i A');
		}

		SavePushNotifications::whereIn('id', array_map(
			function($notice) {
				return $notice['id'];
			}, 
			json_decode(json_encode($notices), true)
		))
		->update(['is_active' => 'no']);

		return json_encode(
			array(
				'status'	=> 'success', 
				'data'		=> $notices
			), 
			JSON_PRETTY_PRINT
		);
	}

	public function apiGetMessagesFromGroup($role, $details_id)
	{
		$return = array();

		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = Users::getTableName();
		
		$return['student'] = DB::table($model::getTableName())
			
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'student')
			->where('is_viewed','no')
			->count();

		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = Users::getTableName();
		//$role = $this->role == 'user' ? $role = $this->current_user->role : $this->role;
		
		$return['guardian'] = DB::table($model::getTableName())
			
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'guardian')
			->where('is_viewed','no')
			->count();

		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = Admin::getTableName();
		//$role = $this->role == 'user' ? $role = $this->current_user->role : $this->role;

		$return['admin'] = DB::table($model::getTableName())
			
			->where('message_to_group', $role)
			->where('message_from_group', 'admin')
			->where('message_to_id', $details_id)
			->where('is_viewed','no')
			->count();

		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = SuperAdmin::getTableName();

		
		
		$return['superadmin'] = DB::table($model::getTableName())
			
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'superadmin')
			->where('is_viewed','no')
			->count();

		return $return;
	}

	public function getMessageFromStudents( $role, $details_id)
	{
		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = Users::getTableName();
		//$role = $this->role == 'user' ? $role = $this->current_user->role : $this->role;
		
		$data = DB::table($model::getTableName())
			->join($table_3, $table_3.'.user_details_id', '=', $table_2.'.message_from_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then 1 else null end) as new_message_count,user_details_id as sender_id, name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			->where('role', 'student')
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'student')
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data_1 = DB::table($model::getTableName())
			->join($table_3, $table_3.'.user_details_id', '=', $table_2.'.message_to_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then null else null end) as new_message_count,user_details_id as sender_id, name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			->where('role', 'student')
			->where('message_to_group', 'student')
			->where('message_from_group', $role)
			->where('message_from_id', $details_id)
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data = $data->union($data_1)
					 ->orderBy('last_activity', 'DESC');
		$data = $data->paginate(Input::get('paginate', 10));

		return $data;
	}

	public function getMessageFromGuardians( $role, $details_id)
	{
		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = Guardian::getTableName();
		$table_4 = Users::getTableName();
		
		$data = DB::table($model::getTableName())
			->join($table_3, $table_3.'.id', '=', $table_2.'.message_from_id')
			->join($table_4, $table_4.'.user_details_id', '=', $table_3.'.id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then 1 else null end) as new_message_count,'.Config::get('database.connections.mysql.prefix').$table_3.'.id as sender_id, guardian_name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			->where('role', 'guardian')
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'guardian')
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data_1 = DB::table($model::getTableName())
			->join($table_3, $table_3.'.id', '=', $table_2.'.message_to_id')
			->join($table_4, $table_4.'.user_details_id', '=', $table_3.'.id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then null else null end) as new_message_count,'.Config::get('database.connections.mysql.prefix').$table_3.'.id as sender_id, guardian_name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity, username as sender_username, message_from_group'))
			->where('role', 'guardian')
			->where('message_to_group', 'guardian')
			->where('message_from_group', $role)
			->where('message_from_id', $details_id)
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC')
			;

			$data = $data_1->union($data)->orderBy('last_activity', 'DESC')
						->paginate(Input::get('paginate', 10));

		return $data;


	}

	public function getMessageFromEmployees( $role, $details_id)
	{
		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = Admin::getTableName();

		$data_1 = DB::table($model::getTableName())
			->join($table_3, $table_3.'.admin_details_id', '=', $table_2.'.message_to_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then null else null end) as new_message_count,admin_details_id as sender_id, name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			->where('message_to_group', 'admin')
			->where('message_from_group', $role)
			->where('message_from_id', $details_id)
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data = DB::table($model::getTableName())
			->join($table_3, $table_3.'.admin_details_id', '=', $table_2.'.message_from_id')
			->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then 1 else null end) as new_message_count,admin_details_id as sender_id, name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'admin')
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data = $data_1->union($data)->orderBy('last_activity', 'DESC')
									->paginate(Input::get('paginate', 10));
		
		return $data;
	}

	public function getMessageFromSuperadmin( $role, $details_id)
	{
		$model = $this->model_name;
		$table_2 = $model::getTableName();
		$table_3 = SuperAdmin::getTableName();

		$data = DB::table($model::getTableName())
			->join($table_3, $table_3.'.id', '=', $table_2.'.message_from_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then 1 else null end) as new_message_count,'.Config::get('database.connections.mysql.prefix').$table_3.'.id as sender_id, name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			
			->where('message_to_id', $details_id)
			->where('message_to_group', $role)
			->where('message_from_group', 'superadmin')
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data_1 = DB::table($model::getTableName())
			->join($table_3, $table_3.'.id', '=', $table_2.'.message_to_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then null else null end) as new_message_count,'.Config::get('database.connections.mysql.prefix').$table_3.'.id as sender_id, name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity,  username as sender_username, message_from_group'))
			
			->where('message_to_group', 'superadmin')
			->where('message_from_group', $role)
			->where('message_from_id', $details_id)
			->groupBy('sender_id')
			->orderBy('last_activity', 'DESC');

		$data = $data->union($data_1)->orderBy('last_activity', 'DESC');
		$data = $data->paginate(Input::get('paginate', 10));

		/*echo '<pre>';
		print_r($data);
		die();*/
		return $data;
	}

	public function apiGetMessageList()
	{
		$message_from = Input::get('message_from', 'student');
		$date_range = Input::get('calendar_range', '');
		$role = Input::get('role', '');
		$details_id = Input::get('details_id', '');
		
		if(strlen($role))
		{
			$role = $role;
		}
		else
		{
			$role = $this->role == 'user' ? $role = $this->current_user->role : $this->role;
		}

		if(strlen($details_id))
		{
			$details_id = $details_id;
		}
		else
		{
			$details_id = $this->details_id;
		}



		switch($message_from)
		{
			case 'student':
				$data = $this->getMessageFromStudents( $role, $details_id);
				break;

			case 'guardian':
				$data = $this->getMessageFromGuardians( $role, $details_id);
				break;

			case 'admin':
				$data = $this->getMessageFromEmployees( $role, $details_id);
				break;

			case 'superadmin':
				$data = $this->getMessageFromSuperadmin( $role, $details_id);
				break;

			default:
				$data = array();
				break;
		}

		$i = 0;
		
			$return = array();
			foreach($data as $d)
			{
				if(isset($return[$d->sender_id]))
				{
					if($d->message_from_group == $message_from)
					{
						$return[$d->sender_id]->new_message_count = $d->new_message_count;
					}

					if(strtotime($d->last_activity) > strtotime($return[$d->sender_id]->last_activity))
					{
						$return[$d->sender_id]->last_activity = $d->last_activity;	
					}
					
				}
				else
				{
					$return[$d->sender_id] = $d;	
				}
				//
				$i = 1;
			}	
		//die();
		
			//echo count($return);
			//die();
		$paginate = $i ? $data->appends(Input::query())->links() : ''; 

		return array($return, $paginate, $this->apiGetMessagesFromGroup($role, $details_id));
	}

	public function ajaxGetMessageList()
	{
		$message_from = Input::get('message_from', 'student');
		$data = $this->apiGetMessageList();

		//$data->setBaseUrl(URL::route('message-list'));
		return View::make($this->view.'ajax.list')
					->with('data', $data[0])
					->with('paginate', $data[1])
					->with('message_count', $data[2])
					->with('module_name', $this->module_name)
					->with('message_from', $message_from)
					->with('view_staff', false);
	}

	public function getMessageList()
	{
		
		$message_from = Input::get('message_from', 'student');
		$data = $this->apiGetMessageList();

		
			
		return View::make($this->view.'message')
					->with('data', $data[0])
					->with('paginate', $data[1])
					->with('message_count', $data[2])
					->with('module_name', $this->module_name)
					->with('message_from', $message_from)
					->with('view_staff', false);
		

	}
	
	public function getCreate()
	{
		return View::make($this->view.'create-message')
					->with('module_name', $this->module_name)
					->with('details_id', $this->details_id)
					->with('details_role', $this->details_role);

	}

	public function apiGetViewOtherData($group, $id)
	{
		$details_role = strlen(Input::get('details_role', '')) ? Input::get('details_role') : $this->details_role;
		$details_id = strlen(Input::get('details_id', '')) ? Input::get('details_id') : $this->details_id;


		if($group == 'superadmin')
		{
			$you_image = Config::get('app.url').'app/modules/superadmin/assets/images/no-img.png';
		}
		elseif($group == 'admin')
		{
			$you_image = HelperController::pluckFieldFromId('Employee', 'photo', $id);
			$you_image = strlen($you_image) ? Config::get('app.url').'app/modules/employee/assets/images/'.$you_image : Config::get('app.url').'app/modules/employee/assets/images/no-img.png';
		}
		else
		{
			$you_image = HelperController::pluckFieldFromId('StudentRegistration', 'photo', $id);
			$you_image = strlen($you_image) ? Config::get('app.url').'app/modules/student/assets/images/'.$you_image : Config::get('app.url').'app/modules/student/assets/images/no-img.png';
		}


		if($this->role == 'superadmin')
		{
			$me_image = Config::get('app.url').'app/modules/superadmin/assets/images/no-img.png';
		}
		elseif($this->role == 'admin')
		{
			$me_image = HelperController::pluckFieldFromId('Employee', 'photo', $details_id);
			$me_image = strlen($me_image) ? Config::get('app.url').'app/modules/employee/assets/images/'.$me_image : Config::get('app.url').'app/modules/employee/assets/images/no-img.png';
		}
		else
		{
			$me_image = HelperController::pluckFieldFromId('StudentRegistration', 'photo', $details_id);
			$me_image = strlen($me_image) ? Config::get('app.url').'app/modules/student/assets/images/'.$me_image : Config::get('app.url').'app/modules/student/assets/images/no-img.png';
		}

		return array('me_id' => $details_id, 'me_image' => $me_image, 'you_image' => $you_image, 'group' => $group, 'id' => $id, 'my_group' => $details_role, 'my_id' => $details_id);


	}

	public function apiGetViewData($group, $id)
	{
		$details_role = strlen(Input::get('details_role', '')) ? Input::get('details_role') : $this->details_role;
		$details_id = strlen(Input::get('details_id', '')) ? Input::get('details_id') : $this->details_id;

		$model = $this->model_name;
		$data = $model::where(function($query) use ($group, $id, $details_role, $details_id)
						{
							return $query->where('message_from_group', $group)
									->where('message_from_id', $id)
									->where('message_to_group', $details_role)
									->where('message_to_id', $details_id);
						})
						->orWhere(function($query) use ($group, $id, $details_role, $details_id)
						{
							return $query->where('message_to_group', $group)
										->where('message_to_id', $id)
										->where('message_from_group', $details_role)
										->where('message_from_id', $details_id);
						})
						->orderBy('id', 'DESC')
						->paginate(Input::get('paginate', 10));

		try
		{
			foreach($data as $d)
			{
				if($details_id == $d->message_to_id && $details_role == $d->message_to_group)
				{
					$d->is_viewed = 'yes';
					$d->save();	
				}
				
			}
		}
		catch(PDOException $e)
		{
			//do nothing
			
		}

		return $data;
		
	}

	public function getView($group, $id)
	{
		
		$data = $this->apiGetViewData($group, $id);
		$data_1 = $this->apiGetViewOtherData($group, $id);

		
		return View::make($this->view.'message-detail')
					->with('data', $data)
					->with('sender_name', urldecode(Input::get('sender_name', '')))
					->with('sender_username', urldecode(Input::get('sender_username', '')))
					->with('me_id', $data_1['me_id'])
					->with('me_image', $data_1['me_image'])
					->with('you_image', $data_1['you_image'])
					->with('group', $data_1['group'])
					->with('id', $id)
					->with('my_group', $data_1['my_group'])
					->with('my_id', $data_1['my_id']);
	}

	public function getNotifications()
	{
		$data = SavePushNotifications::where('user_group', $this->details_role)
									  ->where('user_id', $this->details_id)
									  ->paginate(Input::get('paginate', 10));

		
		return View::make($this->view.'notification')
					->with('module_name', $this->module_name)
					->with('data', $data)
					->with('role', $this->role);
	}

	public function postDeleteNotification()
	{

		//AccessController::allowedOrNot('student', 'can_delete');
		$model = new SavePushNotifications;
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			try
			{
				Users::where('id', $id)
							->delete();
				$record->delete();
				Session::flash('success-msg', 'Delete Successful');	
			}
			catch(Exception $e)
			{
				Session::flash('error-msg', ConfigurationController::errorMsg($e->getMessage()));
			}
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	
	}

/////////////////////////////////////////////////////////////////////
///////////// this is for staff history
	public function getStaffsHistoryList()
	{
		//get personnels that have sent messages according to group
		$message_from = Input::get('message_from', 'admin'); //by default search for admins
		$date_range = Input::get('calendar_range', '');
		$data = array($this->apigetStaffsHistoryList($message_from, $date_range), $this->apiGetMessageSendingGroupNumbers($message_from));

		return View::make($this->view.'staffs-history-list')
					->with('message_from', $message_from)
					->with('data', $data[0])
					->with('message_count', $data[1])
					->with('module_name', $this->module_name);

		
	}

	public function apiGetMessageSendingGroupNumbers($message_from)
	{

		$table = Message::getTableName();
		
		$return = array();
		$return['superadmin'] = DB::table($table)
					 ->where('message_from_group', 'superadmin')
					 ->where('is_viewed', 'no')
					 ->count();

		$return['admin'] = DB::table($table)
					 ->where('message_from_group', 'admin')
					 ->where('is_viewed', 'no')
					 ->count();

		$return['student'] = DB::table($table)
					 ->where('message_from_group', 'student')
					 ->where('is_viewed', 'no')
					 ->count();

		$return['guardian'] = DB::table($table)
					 ->where('message_from_group', 'guardian')
					 ->where('is_viewed', 'no')
					 ->count();
		return $return;
	}

	public function ajaxGetStaffsHistoryList()
	{
		$message_from = Input::get('message_from', 'admin'); //by default search for admins
		$date_range = Input::get('calendar_range', '');
		$data = array($this->apigetStaffsHistoryList($message_from, $date_range), $this->apiGetMessageSendingGroupNumbers($message_from));

		return View::make($this->view.'ajax.staffs-history-list')
					->with('message_from', $message_from)
					->with('data', $data[0])
					->with('message_count', $data[1])
					->with('module_name', $this->module_name);
	}

	public function apigetStaffsHistoryList($message_from, $date_range)
	{
		$model = $this->model_name;
		if(strlen($date_range) == 0)
		{
			//die('here');
			$start_date = Carbon::today()->subDays(7)->toDateTimeString();
			$end_date = Carbon::today()->addDay()->toDateTimeString();
		}
		else
		{
			$date_range = explode(' - ', $date_range);
			$start_date = Carbon::createFromFormat('F j, Y', trim($date_range[0]))->format('Y-m-d');
			
			$end_date = Carbon::createFromFormat('F j, Y', trim($date_range[1]))->format('Y-m-d H:i:s');
			
		}

		if($message_from == 'student') 
			$table_1 = Users::getTableName();
		elseif($message_from == 'guardian')
			$table_1 = Users::getTableName();
		elseif($message_from == 'admin')
			$table_1 = Admin::getTableName();
		else
			$table_1 = SuperAdmin::getTableName();

		$table_2 = $model::getTableName();

		if($message_from == 'superadmin')
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.id', '=', $table_2.'.message_from_id');
		elseif($message_from == 'admin')
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.admin_details_id', '=', $table_2.'.message_from_id');
		elseif($message_from == 'guardian')
		{
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.user_details_id', '=', $table_2.'.message_from_id')
						->where('role', 'guardian');
		}
		else
		{
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.user_details_id', '=', $table_2.'.message_from_id')
						->where('role', 'student');	
		}

		$data = $data//->where($table_2.'.created_at', '>=', $start_date)
					 //->where($table_2.'.created_at', '<=', $end_date)
					 ->where($table_2.'.message_from_group', $message_from)
					 ->select(DB::raw('max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity, name, username, message_from_id, message_from_group'))
					 ->groupBy('message_from_id')
					 ->orderBy($table_2.'.created_at', 'DESC')
					 ->paginate(Input::get('paginate', 10));
		
		return $data;
	}



	public function getStaffContactList($staff_group, $staff_id)
	{
		$message_to = Input::get('message_to', 'guardian'); //by default search for admins
		$date_range = Input::get('calendar_range', '');
		$sender_name = Input::get('sender_name');
		$sender_username = Input::get('sender_username');
		$data = array($this->apiGetStaffContactList($staff_group, $staff_id, $date_range, $message_to), $this->apiGetStaffContactListCount($staff_group, $staff_id, $message_to));


		return View::make($this->view.'staff-contact-list')
					->with('message_to', $message_to)
					->with('data', $data[0])
					->with('message_count', $data[1])
					->with('module_name', $this->module_name)
					->with('staff_group', $staff_group)
					->with('staff_id', $staff_id)
					->with('sender_name', $sender_name)
					->with('sender_username', $sender_username);
	}

	public function ajaxGetStaffContactList($staff_group, $staff_id)
	{
		//supply name and username
		$message_to = Input::get('message_to', 'guardian'); //by default search for admins
		$date_range = Input::get('calendar_range', '');
		$data = array($this->apiGetStaffContactList($staff_group, $staff_id, $date_range, $message_to), $this->apiGetStaffContactListCount($staff_group, $staff_id, $message_to));
		$sender_name = Input::get('sender_name');
		$sender_username = Input::get('sender_username');
		

		return View::make($this->view.'ajax.staff-contact-list')
					->with('message_to', $message_to)
					->with('data', $data[0])
					->with('message_count', $data[1])
					->with('module_name', $this->module_name)
					->with('staff_group', $staff_group)
					->with('staff_id', $staff_id)
					->with('sender_name', $sender_name)
					->with('sender_username', $sender_username);
	}

	public function apiGetStaffContactListCount($staff_group, $staff_id, $message_to)
	{
		$table = Message::getTableName();
		$return = array();

		$return['superadmin'] = DB::table($table)
								->where('message_from_group', $staff_group)
								 ->where('message_from_id', $staff_id)
								 ->where('message_to_group', 'superadmin')
								 ->where('is_viewed_by_superadmin', 'no')
								 ->count();

		$return['admin'] = DB::table($table)
								->where('message_from_group', $staff_group)
								 ->where('message_from_id', $staff_id)
								 ->where('message_to_group', 'admin')
								 ->where('is_viewed_by_superadmin', 'no')
								 ->count();

		$return['student'] = DB::table($table)
								->where('message_from_group', $staff_group)
								 ->where('message_from_id', $staff_id)
								 ->where('message_to_group', 'student')
								 ->where('is_viewed_by_superadmin', 'no')
								 ->count();

		$return['guardian'] = DB::table($table)
								->where('message_from_group', $staff_group)
								 ->where('message_from_id', $staff_id)
								 ->where('message_to_group', 'guardian')
								 ->where('is_viewed_by_superadmin', 'no')
								 ->count();

		return $return;
	}

	public function apiGetStaffContactList($staff_group, $staff_id, $date_range, $message_to)
	{
		$model = $this->model_name;
		if(strlen($date_range) == 0)
		{
			$start_date = Carbon::today()->subDays(7)->toDateTimeString();
			$end_date = Carbon::today()->addDay()->toDateTimeString();
		}
		else
		{
			$date_range = explode(' - ', $date_range);
			$start_date = Carbon::createFromFormat('F j, Y', trim($date_range[0]))->format('Y-m-d');
			
			$end_date = Carbon::createFromFormat('F j, Y', trim($date_range[1]))->format('Y-m-d H:i:s');
			
		}

		if($message_to == 'student') 
			$table_1 = Users::getTableName();
		elseif($message_to == 'guardian')
			$table_1 = Users::getTableName();
		elseif($message_to == 'admin')
			$table_1 = Admin::getTableName();
		else
			$table_1 = SuperAdmin::getTableName();

		$table_2 = Message::getTableName();

		if($message_to == 'superadmin')
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.id', '=', $table_2.'.message_to_id');
		elseif($message_to == 'admin')
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.admin_details_id', '=', $table_2.'.message_to_id');
		elseif($message_to == 'guardian')
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.user_details_id', '=', $table_2.'.message_to_id')
						->where('role', 'guardian');
		else
			$data = DB::table($table_2)
						->join($table_1, $table_1.'.user_details_id', '=', $table_2.'.message_to_id')
						->where('role', 'student');
		
		$data = $data//->where($table_2.'.created_at', '>=', $start_date)
					 //->where($table_2.'.created_at', '<=', $end_date)
					 ->where($table_2.'.message_from_group', $staff_group)
					 ->where($table_2.'.message_from_id', $staff_id)
					 ->where('message_to_group', $message_to)
					 ->select(DB::raw('max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity, name, username, message_from_id, message_from_group, message_to_id, message_to_group'))
					 ->groupBy('message_to_id')
					 ->orderBy($table_2.'.created_at', 'DESC')
					 //->get();
					 ->paginate(Input::get('paginate', 10));

		
		return $data;
	}

	public function getStaffConversation($staff_group, $staff_id, $customer_group, $customer_id)
	{
		$model = $this->model_name;
		$data = $model::where(function($query) use ($staff_group, $staff_id, $customer_group, $customer_id)
						{
							return $query->where('message_from_group', $staff_group)
									->where('message_from_id', $staff_id)
									->where('message_to_group', $customer_group)
									->where('message_to_id', $customer_id);
						})
						->orWhere(function($query) use ($staff_group, $staff_id, $customer_group, $customer_id)
						{
							return $query->where('message_to_group', $staff_group)
										->where('message_to_id', $staff_id)
										->where('message_from_group', $customer_group)
										->where('message_from_id', $customer_id);
						})
						->orderBy('id', 'DESC')
						->paginate(Input::get('paginate', 10));

		try
		{
			foreach($data as $d)
			{
				$d->is_viewed_by_superadmin = 'yes';
				$d->save();
			}

		}catch(PDOException $e)
		{
			//do nothing
		}

		if($staff_group == 'superadmin')
		{
			$you_image = Config::get('app.url').'app/modules/superadmin/assets/images/no-img.png';
		}
		elseif($staff_group == 'admin')
		{
			$you_image = HelperController::pluckFieldFromId('Employee', 'photo', $staff_id);
			$you_image = strlen($you_image) ? Config::get('app.url').'app/modules/employee/assets/images/'.$you_image : Config::get('app.url').'app/modules/employee/assets/images/no-img.png';
		}
		else
		{
			$you_image = HelperController::pluckFieldFromId('StudentRegistration', 'photo', $staff_id);
			$you_image = strlen($you_image) ? Config::get('app.url').'app/modules/student/assets/images/'.$you_image : Config::get('app.url').'app/modules/student/assets/images/no-img.png';
		}


		if($customer_group == 'superadmin')
		{
			$me_image = Config::get('app.url').'app/modules/superadmin/assets/images/no-img.png';
		}
		elseif($customer_group == 'admin')
		{
			$me_image = HelperController::pluckFieldFromId('Employee', 'photo', $customer_id);
			$me_image = strlen($me_image) ? Config::get('app.url').'app/modules/employee/assets/images/'.$me_image : Config::get('app.url').'app/modules/employee/assets/images/no-img.png';
		}
		else
		{
			$me_image = HelperController::pluckFieldFromId('StudentRegistration', 'photo', $customer_id);
			$me_image = strlen($me_image) ? Config::get('app.url').'app/modules/student/assets/images/'.$me_image : Config::get('app.url').'app/modules/student/assets/images/no-img.png';
		}

		
		return View::make($this->view.'staff-conversation')
					->with('data', $data)
					->with('sender_name', urldecode(Input::get('sender_name', '')))
					->with('sender_username', urldecode(Input::get('sender_username', '')))
					->with('me_id', $customer_id)
					->with('me_image', $me_image)
					->with('you_image', $you_image)
					->with('group', $staff_group)
					->with('id', $staff_id)
					->with('my_group', $customer_id)
					->with('my_id', $customer_id);
	}
}