<?php


class Teacher extends BaseModel 
{

	protected $table = 'teachers';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Teacher';


	public $createRule = [ 'teacher_id'	=> array('required', 'min:1'),
						   'session_id'	=> array('required', 'min:1'),
						   'is_class_teacher' => array('required', 'in:yes,no'),
						   'class_id' => array('required', 'min:1'),
						   'section_code' => array('required', 'not_in:0'),
						   'is_active' => array('required', 'in:yes,no'),
						  ];

	public $updateRule = [ 'teacher_id'	=> array('required', 'min:1'),
						   'session_id'	=> array('required', 'min:1'),
						   'is_class_teacher' => array('required', 'in:yes,no'),
						   'class_id' => array('required', 'min:1'),
						   'section_code' => array('required', 'not_in:0'),
						   'is_active' => array('required', 'in:yes,no'),
						  ];

	//protected $defaultOrder = array('orderBy' => 'sort_order', 'orderOrder' => 'ASC');

	public function getListViewData($queryString)
	{
		///// Teacher-show-username-v1-changes-here //
		$admin_table = Admin::getTableName();
		///// Teacher-show-username-v1-changes-here //
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Employee::getTableName(), Employee::getTableName().'.id', '=', $model::getTableName().'.teacher_id')
					///// Teacher-show-username-v1-changes-here //
					->join($admin_table, $admin_table.'.admin_details_id', '=', Employee::getTableName().'.id')
					///// Teacher-show-username-v1-changes-here //
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', $model::getTableName().'.class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', $model::getTableName().'.session_id')
					//
					//->where($model::getTableName().'.is_active', 'yes')
					->where(AcademicSession::getTableName().'.is_active', 'yes')
					->where(Classes::getTableName().'.is_active', 'yes')
					->where(Employee::getTableName().'.is_active', 'yes');

		///// Teacher-show-username-v1-changes-here //
		$result = $result->select($model::getTableName().'.*', Employee::getTableName().'.employee_name', Classes::getTableName().'.class_name', AcademicSession::getTableName().'.session_name', $admin_table.'.username');
		///// Teacher-show-username-v1-changes-here //
		

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
			
				if($col == 'session_name')
				$result = $result->where(AcademicSession::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col == 'employee_name')
					$result = $result->where(Employee::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col == 'class_name')
					$result = $result->where(Classes::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				//// Teacher-show-username-v1-changes-here ////
				elseif($col == 'username')
					$result = $result->where(Admin::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				//// Teacher-show-username-v1-changes-here ////
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			
			}
		}
		

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
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Employee::getTableName(), Employee::getTableName().'.id', '=', $model::getTableName().'.teacher_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', $model::getTableName().'.class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', $model::getTableName().'.session_id')
					//
					//->where($model::getTableName().'.is_active', 'yes')
					->where(AcademicSession::getTableName().'.is_active', 'yes')
					->where(Classes::getTableName().'.is_active', 'yes')
					->where(Employee::getTableName().'.is_active', 'yes')
					->select($model::getTableName().'.*', Employee::getTableName().'.employee_name', Classes::getTableName().'.class_name', AcademicSession::getTableName().'.session_name')
					->where($model::getTableName().'.id', $id)
					->first();
	
		return $result;
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getActiveTeachers()
	{
		$data = array();
		
		$data = DB::table(EmployeePosition::getTableName())
				  ->join(Employee::getTableName(), Employee::getTableName().'.id', '=', EmployeePosition::getTableName().'.employee_id')
				  ->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
				  ->where(Group::getTableName().'.group_name', 'Teacher')
				  ->where(Employee::getTableName().'.is_active', 'yes')
				  ->select(Employee::getTableName().'.id', Employee::getTableName().'.employee_name')
				  ->lists('employee_name', 'id');
				  //->get();

		return $data;
	}
}