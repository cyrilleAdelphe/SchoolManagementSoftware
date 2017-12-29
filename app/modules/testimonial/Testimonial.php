<?php

class Testimonial extends BaseModel
{
	protected $table = 'testimonials';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Testimonial';


	public $createRule = [ 'content' => array('required')
						  ];

	public $updateRule = [ 'content' => array('required')
						  ];

	protected $defaultOrder = array('orderBy' => 'sort_order', 'orderOrder' => 'ASC');

	/*public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Employee::getTableName(), Employee::getTableName().'.id', '=', $model::getTableName().'.testimonial_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', $model::getTableName().'.class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', $model::getTableName().'.session_id')
					//
					//->where($model::getTableName().'.is_active', 'yes')
					->where(AcademicSession::getTableName().'.is_active', 'yes')
					->where(Classes::getTableName().'.is_active', 'yes')
					->where(Employee::getTableName().'.is_active', 'yes');

		$result = $result->select($model::getTableName().'.*', Employee::getTableName().'.employee_name', Classes::getTableName().'.class_name', AcademicSession::getTableName().'.session_name');
		$result = $result->where($model::getTableName().'.is_active', $queryString['status']);

		if(isset($queryString['status']))
		{

		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if($queryString['filter']['field'] == 'session_name')
				$result = $result->where(AcademicSession::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			elseif($queryString['filter']['field'] == 'employee_name')
				$result = $result->where(Employee::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			elseif($queryString['filter']['field'] == 'class_name')
				$result = $result->where(Classes::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
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
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Employee::getTableName(), Employee::getTableName().'.id', '=', $model::getTableName().'.testimonial_id')
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
	public function getActiveTestimonials()
	{
		$data = array();
		
		$data = DB::table(EmployeePosition::getTableName())
				  ->join(Employee::getTableName(), Employee::getTableName().'.id', '=', EmployeePosition::getTableName().'.employee_id')
				  ->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
				  ->where(Group::getTableName().'.group_name', 'Testimonial')
				  ->where(Employee::getTableName().'.is_active', 'yes')
				  ->select(Employee::getTableName().'.id', Employee::getTableName().'.employee_name')
				  ->lists('employee_name', 'id');
				  //->get();

		return $data;
	}*/
}