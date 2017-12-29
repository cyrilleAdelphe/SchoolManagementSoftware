<?php
use Carbon\Carbon;
class StaffRequestController extends BaseController {
	protected $view = 'staff-request.views.';

	protected $model_name = 'StaffRequest';

	protected $module_name = 'staff-request';

	public function apiValidateInput($input, $update = false)
	{

		$validate = Validator::make($input, (new StaffRequest)->createRule);

		if($validate->fails())
		{
			return array('status' => 'error', 'data' => $validate->messages(), 'message' => 'Please fill all the fields');
		}
		else
		{
			return array('status' => 'success');
		}

	}

	public function getCreate()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		return View::make($this->view.'create-message')
					->with('module_name', $this->module_name)
					->with('details_id', $this->details_id)
					->with('details_role', $this->details_role);
	}

	public function createOthersRequest()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		return View::make($this->view.'create-others-request')
			->with('module_name', $this->module_name);
	}

	public function getMessageList()
	{
		
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$data = $this->apiGetMessageList();

		return View::make($this->view.'message')
					->with('data', $data[0])
					->with('paginate', $data[1])
					->with('module_name', $this->module_name)
					->with('details_id', $this->details_id)
					->with('details_role', $this->details_role);
	}

	public function ajaxGetMessageList()
	{
		$data = $this->apiGetMessageList();

		//$data->setBaseUrl(URL::route('message-list'));
		return View::make($this->view.'ajax.list')
					->with('data', $data[0])
					->with('paginate', $data[1])
					->with('module_name', $this->module_name);
	}

	public function apiGetMessageList()
	{
		
		$date_range = Input::get('calendar_range', '');
		$details_id = Input::get('details_id', '');

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

		if(strlen($details_id))
		{
			$details_id = $details_id;
		}
		else
		{
			$details_id = $this->details_id;
		}

		
		$data = $this->getMessageFromEmployees($start_date, $end_date, Input::get('details_role', $this->details_role), $details_id);
				
		
		$paginate = count($data) ? $data->appends(Input::query())->links() : ''; 

		return array($data, $paginate);
	}

	public function getMessageFromEmployees($start_date, $end_date, $role, $details_id)
	{
		//$date = Carbon::now()->subDays(7)->toDateTimeString();
		$model = $this->model_name;
		$table_2 = $model::getTableName();
		if ($role == 'admin') 
		{
			$table_3 = Admin::getTableName();
			$details_column = 'admin_details_id';
		}
		elseif ($role == 'superadmin')
		{
			$table_3 = Superadmin::getTableName();	
			$details_column = 'id';
		}
		else
		{
			throw new Exception("Invalid group", 1);
		}
		
		$data = DB::table($model::getTableName())
			->join($table_3, $table_3.'.'.$details_column, '=', $table_2.'.message_from_id')
			->select('message', 'message_subject', 'request_type', 'is_approved')
			->where($table_2.'.created_at', '>=', $start_date)
			->where($table_2.'.created_at', '<=', $end_date)
			->where('message_from_id', $details_id)
			->where('message_from_group', $role);

		$data = $data
			->paginate(Input::get('paginate', 10));
		
		return $data;
	}



	public function apiSendMessage($input = array())
	{	
		try
		{
			
			$superadmins = DB::table(SuperAdmin::getTableName())
				// ->leftJoin(PushNotifications::getTableName(), PushNotifications::getTableName().'.user_id', '=', SuperAdmin::getTableName().'.id')
				// ->where(PushNotifications::getTableName().'.user_group', 'superadmin')
				// ->select(SuperAdmin::getTableName().'.id', PushNotifications::getTableName().'.gcm_id')
				->get();

			$gcm_ids = array();

			foreach ($superadmins as $superadmin)
			{
				$push_notification = PushNotifications::where('user_group', 'superadmin')
					->where('user_id', $superadmin->id)
					->first();
				$gcm_ids[$superadmin->id] = $push_notification ? $push_notification->gcm_id : NULL;
			}

					
			$msg = 'staff-request # ' .
							'subject: ' . $input['message_subject'] . "\n" .
							'message: ' . $input['message'];
			 
			if(count($gcm_ids))
			{
				(new GcmController)->send($gcm_ids, $msg, array_keys($gcm_ids), 'superadmin');
			}

			$input['academic_session_id'] = HelperController::getCurrentSession();
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

	public function apiPostSendMessage()
	{

		$input = Input::all();
		$input['is_approved'] = 'no';


		$result = $this->apiValidateInput($input);
			
		if($result['status'] == 'error') 
		{
			return json_encode($result);
		}
		else
		{
			return json_encode($this->apiSendMessage($input));
		}
			
	}

	// Methods for staff history

	public function getStaffsHistoryList()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		//get personnels that have sent messages according to group
		$message_from = Input::get('message_from', 'admin'); //by default search for admins
		$date_range = Input::get('calendar_range', '');
		$data = array($this->apigetStaffsHistoryList($message_from, $date_range));

		return View::make($this->view.'staffs-history-list')
					->with('message_from', $message_from)
					->with('data', $data[0])
					->with('module_name', $this->module_name);
	}

	public function ajaxGetStaffsHistoryList()
	{
		
		$message_from = Input::get('message_from', 'admin'); //by default search for admins
		$date_range = Input::get('calendar_range', '');
		$data = array($this->apigetStaffsHistoryList($message_from, $date_range));

		return View::make($this->view.'ajax.staffs-history-list')
					->with('message_from', $message_from)
					->with('data', $data[0])
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

		
		$table_1 = Admin::getTableName();
		$table_2 = $model::getTableName();

		
		$data = DB::table($table_2)
			->join($table_1, $table_1.'.admin_details_id', '=', $table_2.'.message_from_id');
		
		$data = $data
			->where($table_2.'.created_at', '>=', $start_date)
			->where($table_2.'.created_at', '<=', $end_date)
			->where($table_2.'.message_from_group', 'admin')
			->select($table_2.'.*', $table_1.'.username', $table_1.'.name')
			->orderBy($table_2.'.created_at', 'DESC')
			->paginate(Input::get('paginate', 10));
		
		return $data;
	}

	// method to approve
	public function apiPostApprove()
	{
		$response = array(
			'status' => 'error',
			'message' => ''
		);

		$model = $this->model_name;

		if (!Input::has('id'))
		{
			$response['message'] = 'request not specified';
		}
		else
		{
			$id = Input::get('id', 0);
			$request = $model::find($id);
			if (!$request) 
			{
				$response['message'] = 'invalid request given';
			}
			else
			{
				$request = $request->toArray();
				$request['approved_by'] = $this->current_user->id;
				$request['is_approved'] = $request['is_approved'] == 'no' ? 'yes' : 'no';
				try
				{
					$this->updateInDatabase($request);	
					$response['message'] = $request['is_approved'] == 'no' ? 'Disapproved!' : 'Approved!';
					$response['status'] = 'success';
				}
				catch (Exception $e)
				{
					$response['message'] = $e->getMessage();
				}
			}
		}

		return json_encode($response, JSON_PRETTY_PRINT);
	}
	
}