<?php

class SavePushNotifications extends BaseModel
{
	protected $table = 'save_push_notifications';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'SavePushNotifications';


	public $createRule = [ 
						   ];

	public $updateRule = [ 
						   ];

	protected $defaultOrder = array('orderBy' => 'id', 'orderOrder' => 'ASC');

/*
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

		$result = $result->where($model::getTableName().'.is_active', $queryString['status']);

		if(isset($queryString['status']))
		{

		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if($queryString['filter']['field'] == 'name')
			{
				if($user_group == 'student' || $user_group == 'guardian')
				{
					$result = $result->where(Users::getTableName().'.name', 'LIKE', '%'.$queryString['filter']['value'].'%');		
				}
				elseif($user_group == 'admin')
				{
					$result = $result->where(Admin::getTableName().'.name', 'LIKE', '%'.$queryString['filter']['value'].'%');
				}
				else
				{
					$result = $result->where(SuperAdmin::getTableName().'.name', 'LIKE', '%'.$queryString['filter']['value'].'%');
				}
			}
			else
				$result = $result->where($model::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
		}
		//}

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
*/
}