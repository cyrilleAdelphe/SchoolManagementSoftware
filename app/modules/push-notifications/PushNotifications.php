<?php

class PushNotifications extends BaseModel
{
	protected $table = 'push_notifications';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'PushNotifications';


	public $createRule = [ 'user_group'	=> array('required', 'in:student,guardian,admin,superadmin'),
						   'user_id' => array('required', 'not_in:0'),
						   'gcm_id' => array('required')
						   ];

	public $updateRule = [ 'user_group'	=> array('required', 'in:student,guardian,admin,superadmin'),
						   'user_id' => array('required', 'not_in:0'),
						   'gcm_id' => array('required')
						   ];

	protected $defaultOrder = array('orderBy' => 'id', 'orderOrder' => 'ASC');

	public function getListViewData($queryString)
	{
		$user_group = Input::get('user_group', 'student');
		$model = $this->model_name;
		$result = DB::table($model::getTableName());

		if($user_group == 'student' || $user_group == 'guardian')
			$result = $result->join(Users::getTableName(), 'user_details_id', '=', PushNotifications::getTableName().'.user_id');
		elseif($user_group == 'admin')
			$result = $result->join(Users::getTableName(), '.admin_details_id', '=', PushNotifications::getTableName().'.user_id');
		else
		{
			//no need to join
		}

		$result = $result->select(array($model::getTableName().'.*', 'name'));
		if($user_group == 'student')
		{
			$result = $result->where('role', 'student');
		}
		elseif($user_group == 'guardian')
		{
			$result = $result->where('role', 'guardian');
		}
		else
		{
			//do nothing
		}

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}

		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
			
				if($col == 'name')
				{
					if($user_group == 'student' || $user_group == 'guardian')
					{
						$result = $result->where(Users::getTableName().'.name', 'LIKE', '%'.$query_vals[$index].'%');		
					}
					elseif($user_group == 'admin')
					{
						$result = $result->where(Admin::getTableName().'.name', 'LIKE', '%'.$query_vals[$index].'%');
					}
					else
					{
						$result = $result->where(SuperAdmin::getTableName().'.name', 'LIKE', '%'.$query_vals[$index].'%');
					}
				}
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			
			}
		}

		//$result = $result->orderBy(AcademicSession::getTableName().'.id', 'ASC');
		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}
}