<?php

class SMS extends BaseModel
{
	protected $table = 'save_push_notifications';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'SMS';


	public $createRule = [ 
	];

	public $updateRule = [ 
	];

	protected $defaultOrder = array('orderBy' => 'created_at', 'orderOrder' => 'DESC');

	public function getListViewData($queryString)
	{
		$final_status = explode(',', FINAL_SMS_STATUS);
		$model = $this->model_name;
		$result = $model::groupBy('message_group_id');

		//$affected_rows = array();

		$result = $result->select(
			DB::raw('COUNT(message_group_id) as total_users , count(case when is_active'.' = "no" then 1 else null end) as seen_users, count(case when is_active'.' = "yes" then 1 else null end) as unseen_users'),
			'message',
			'message_group_id',
			'user_group',
			'created_at'
		);
		
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
					if($col == 'subject')
					{
						$result = $result->where($model::getTableName().'.'.'message', 'LIKE', '%'.$query_vals[$index].'%');		
					}
					else
					{
						$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
					}
			}
		}

		$result = $result->whereNotIn(
			'id', 
			$model::select('id')->where('message', 'LIKE', 'message%')
				->where('sender_role', '!=', 'superadmin')
				->lists('id')
		);
		
		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}
	
		$result = $result->where(DB::raw('(SELECT count(case when is_active'.' = "yes" then 1 else null end))'), '>', 0);

		$result = $result->paginate($queryString['paginate']);
		foreach($result as $r)
		{
			$temp = array();
			if(!in_array(strtolower($r->sms_status), $final_status))
			{
				$affected_rows[$r->message_group_id][] = $r->phone_no;
			}

			$r->subject = substr($r->message, 0, strpos($r->message, '#')-1);
		}

		//make curl request here with parameters $affected_rows
		/*try
		{
			//need to fix this
			//$response = SmsMainController::makeCurlRequest(MAIN_SERVER_URL, array('parameters' => json_encode($affected_rows)), 'post');	
		}
		catch(Exception $e)
		{
			$response = json_encode(array('status' => 'error', 'message' => ''));
		}
		
		$response = json_decode($response, true);

		if($response['status'] == 'success')
		{
			$response = $response['data'];
			//update ids here
			foreach($response as $sms_status => $message_group_ids)
			{
				$update_array = array('sms_status' => $sms_status);

				if(in_array($sms_status, $final_status))
				{
					$update_status['is_active' => 'no']		;
				}

				foreach($message_group_ids as $message_group_id => $phone_no)
				{
					do
					{
						try
						{
							SMS::whereIn('phone_no', $phone_no)
								->whereIn('message_group_id', $message_group_id)
								->update($update_array);	
							$flag = true;
						}
						catch(Exception $e)
						{
							$flag = false;
						}	
					}while(!$flag);
				}
			}*/
			/*
			foreach($result as $index => $r)
			{
				$temp = array();
				if(!in_array($r->sms_status, $final_status))
				{
					foreach($response as $sms_status => $message_group_ids)
					{
						foreach($message_group_ids as $message_group_id => $phone_no)
						{
							if(isset($response[$sms_status][$r->message_group_id]) && in_array($r->phone_no, $response[$sms_status][$r->message_group_id]))
							{
								$result[$index]->sms_status = $sms_status;
							}
						}
					}
				}
			}*/
		//}

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';
		
		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getViewViewData($message_group_id)
	{
		//get phone_numbers with non-final id
		$data = SMS::where('message_group_id', $message_group_id)
					->whereNotIn('sms_status', explode(',', FINAL_SMS_STATUS))
					->lists('phone_no');

		$user_group = Input::get('user_group', '');

					//dd($data);


		if(count($data))
		{
			//make curl request to server of final data
			$data = json_encode($data);
			$url = MAIN_SERVER_URL.'sms-master/api/our-callback';
			$arguments = array('school_id' => SCHOOL_UNIQUE_ID, 'message_group_id' => $message_group_id, 'dlrs' => $data);
			
			try
			{
				$response = SmsMainController::makeCurlRequest($url, $arguments);
			}
			catch(Exception $e)
			{

				return array('status' => 'error', 'message' => $e->getMessage(), 'data' => array());
			}
				
			
			$response = json_decode($response, true);
			
			if($response['status'] == 'success')
			{

				//update in our database the current status
				// /{"status":"success","message":"","data":{"queued":{"1":["9860395442"]},"failed":{"1":["9849102298"]},"delivered":{"1":["9849102208"]}}}
				
				foreach($response['data'] as $sms_status => $phone_numbers)
				{
					do
					{
						try
						{
							SMSHelperController::updateSmsStatus($message_group_id, $sms_status, $phone_numbers);
							$flag = true;
						}
						catch(Exception $e)
						{
							//echo $e->getMessage();
							$flag = false;
						}	
					}while(!$flag);
				}
			}
			else
			{
				return $response;
			}
		}


		if($user_group == 'student')
		{
			
			$data = DB::table(SMS::getTableName())
					->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', 'user_id')
					->select('student_name as name', SMS::getTableName().'.*')
					->where('user_group', $user_group)
					->where('message_group_id', $message_group_id)
					->get();		
			//return $data;
		}
		elseif($user_group == 'guardian')
		{
			$data = DB::table(SMS::getTableName())
					->join(Guardian::getTableName(), Guardian::getTableName().'.id', '=', 'user_id')
					->select('guardian_name as name', SMS::getTableName().'.*')
					->where('user_group', $user_group)
					->where('message_group_id', $message_group_id)
					->get();
		}
		elseif($user_group == 'admin')
		{
			$data = DB::table(SMS::getTableName())
					->join(Employee::getTableName(), Employee::getTableName().'.id', '=', 'user_id')
					->select('employee_name as name', SMS::getTableName().'.*')
					->where('user_group', $user_group)
					->where('message_group_id', $message_group_id)
					->get();
		}
		elseif($user_group == 'superadmin')
		{
			$data = DB::table(SMS::getTableName())
					->join(Superadmin::getTableName(), Superadmin::getTableName().'.id', '=', 'user_id')
					->select('name', SMS::getTableName().'.*')
					->where('user_group', $user_group)
					->where('message_group_id', $message_group_id)
					->get();	
		}
		else
		{
			$data = array();
			return array('status' => 'error', 'message' =>'No data found', 'data' => array());
		}

		return array('status' => 'success', 'message' => '', 'data' => $data);

		//return $data;
	}

	public function getIndividualSmsStatus($message_group_id)
	{

	}
}