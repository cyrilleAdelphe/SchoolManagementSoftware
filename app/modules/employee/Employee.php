<?php

class Employee extends BaseModel
{
	protected $table = 'employees';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Employee';


	public $createRule = [ 'employee_name'		=> array('required'),
						   'employee_dob_in_ad' => array('date_format:Y-m-d'),
						   'employee_dob_in_bs' => array(),
						   'sex' 				=> array('required', 'in:male,female,other'),
						   'current_address' 	=> array(),
						   'permanent_address' 	=> array(),
						   'primary_contact' 	=> array(),
						   'email'				=> array('unique:employees,email'),
						   'joining_date_in_ad' => array('date_format:Y-m-d'),
						   'is_working' 		=> array('required', 'in:yes,no'),
						   'leave_date' 		=> array('date_format:Y-m-d'),
						   'photo'				=> array('mimes:jpeg,jpg,png', 'max:1024'),
						   'cv'					=> array('mimes:pdf,doc,docx', 'max:4500')];

	public $updateRule = [ 'employee_name'		=> array('required'),
						   'employee_dob_in_ad' => array('date_format:Y-m-d'),
						   'employee_dob_in_bs' => array(),
						   'sex' 				=> array('required', 'in:male,female,other'),
						   'current_address' 	=> array(),
						   'permanent_address' 	=> array(),
						   'primary_contact' 	=> array(),
						   'email'				=> array('unique:employees,email'),
						   'joining_date_in_ad' => array('date_format:Y-m-d'),
						   'is_working' 		=> array('required', 'in:yes,no'),
						   'leave_date' 		=> array('date_format:Y-m-d'),
						   'photo'				=> array('mimes:jpeg,jpg,png', 'max:1024'),
						   'cv'					=> array('mimes:pdf,doc,docx', 'max:4500')];

	protected $defaultOrder = array('orderBy' => 'employee_name', 'orderOrder' => 'ASC');

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->leftJoin(Admin::getTableName(), Admin::getTableName().'.admin_details_id', '=', $model::getTableName().'.id');
		$result = $result->select(array($model::getTableName().'.*', Admin::getTableName().'.username'));
		

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if($queryString['filter']['field'] == 'username')
				$result = $result->where(Admin::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			else
				$result = $result->where($model::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
		}
		//}

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

	public function getViewViewData($id)
	{
		$return = array();
		$model = $this->model_name;
		$result = $model::find($id);

		$result->username = Admin::where('admin_details_id', $id)->first()->username;

		//getting employee positions

		$positions = DB::table(EmployeePosition::getTableName())
						->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
						->where(EmployeePosition::getTableName().'.employee_id', $id)
						->where(Group::getTableName().'.is_active', 'yes')
						->lists('group_name', 'group_id');


		$return['data'] = $result;
		$return['groups'] = $positions;
	
		return $return;
	}

	
}