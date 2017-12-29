<?php

class Guardian extends BaseModel
{
	protected $table = 'guardians';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Guardian';


	public $createRule = [ 'guardian_name'		=> array('required'),
						   'dob_in_ad' => array('date_format:Y-m-d'),
						   'dob_in_bs' => array(),
						   
						   'current_address' 	=> array(),
						   'permanent_address' 	=> array(),
						   'primary_contact' 	=> array(),
						   'email'				=> array('unique:guardians,email'),
						   'photo'				=> array('mimes:jpeg,jpg,png', 'max:10485760000'),
						   'occupation'			=> array()];

	public $updateRule = [ 'guardian_name'		=> array('required'),
						   'dob_in_ad' => array('date_format:Y-m-d'),
						   'dob_in_bs' => array(),
						   
						   'current_address' 	=> array(),
						   'permanent_address' 	=> array(),
						   'primary_contact' 	=> array(),
						   'email'				=> array('unique:guardians,email'),
						   'photo'				=> array('mimes:jpeg,jpg,png', 'max:10485760000'),
						   'occupation'			=> array()];

	protected $defaultOrder = array('orderBy' => 'id', 'orderOrder' => 'DESC');

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', $model::getTableName().'.id');
		
		$result = $result->where(Users::getTableName().'.role', 'guardian');
		$result = $result->select(array($model::getTableName().'.*', Users::getTableName().'.username'));

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
			
				if($col == 'username')
					$result = $result->where(Users::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col=='student_name')
					{

					}
				elseif($col=='class_section')
				{

				}
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

		$guardian_id = $result->paginate($queryString['paginate'])->lists('id');

		$paginate = $result->paginate($queryString['paginate']);
		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		
		$related_students['data'] = DB::table(Guardian::getTableName())
							  ->leftJoin(StudentGuardianRelation::getTableName(), StudentGuardianRelation::getTableName().'.guardian_id', '=', Guardian::getTableName().'.id' )
							  ->leftJoin(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.student_id')
							  ->join(Users::getTableName(), function($query)
							  {
							  		$query->on(Users::getTableName().'.user_details_id', '=', StudentGuardianRelation::getTableName().'.guardian_id')
							  			->where('role', '=', 'guardian');
							  }) 
							  ->leftJoin(Classes::getTableName(), Classes::getTableName().'.id','=',StudentRegistration::getTableName().'.registered_class_id')
							  ->leftJoin(Section::getTableName(), Section::getTableName().'.section_code','=',StudentRegistration::getTableName().'.registered_section_code')
							  ->whereIn('guardian_id', $guardian_id)
							  ->select('guardian_id','student_name', 'username', 'relationship', Classes::getTableName().'.class_name', Section::getTableName().'.section_name', Guardian::getTableName().'.secondary_contact','primary_contact','guardian_name', 'guardian_id as id');
	
		

		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
			
				if(isset($col ) && $col=='student_name')
				{
					$related_students['data'] = $related_students['data']->where(StudentRegistration::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				}
				elseif(isset($col ) && $col=='class_section')
				{
					$related_students['data'] = $related_students['data']->where(function($query) use ($query_vals, $index)
						{
							$query->where(Classes::getTableName().'.class_name', 'LIKE', '%'.$query_vals[$index].'%')
								  ->orWhere(Section::getTableName().'.section_code', 'LIKE', '%'.$query_vals[$index]);
						}) ;
				}	
					
			}
		}
						  
		
		$related_students['data'] = $related_students['data']->get();

		//$guardian_id = $result;
		
		$result = [];
		foreach($related_students['data'] as $d)
		{
			if(isset($result[$d->guardian_id]))
			{
				$result[$d->guardian_id]->student_name .= ' , '. $d->student_name  ; 
				$result[$d->guardian_id]->class_name .= ' , '.$d->class_name . ' - '.$d->section_name ; 
				

			}
			else
			{
				$result[$d->guardian_id] = $d;
				$result[$d->guardian_id]->class_name = $d->class_name . ' - '.$d->section_name ;

			}
			
		}
		// echo '<pre>';
		// print_r($result);
		// die();

	


	
		return array('data' => $result, 'count' => $count, 'message' => $msg, 'guardian_id' => $paginate);
	}

	public function getViewViewData($id)
	{
		if(Auth::user()->check())
		{
			if(Auth::user()->user()->role == 'guardian' && Auth::user()->user()->user_details_id != $id)
			{
				App::abort(403, 'Not allowed');
			}
			elseif(Auth::user()->user()->role == 'student')
			{
				$student_ids = StudentGuardianRelation::where('guardian_id', $id)
														->lists('student_id');

				if(!in_array(Auth::user()->user()->user_details_id, $student_ids))
				{
					App::abort(403, 'Not allowed');
				}
			}
		}

		$model = $this->model_name;
		$result = $model::find($id);
		if ($result)
		{
			$result->username = Users::where('user_details_id', $id)	
				->where('role', 'guardian')
				->first()
				->username;
		}
		
		$related_students = DB::table(StudentGuardianRelation::getTableName())
							  ->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.student_id')
							  ->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
							  ->where(Users::getTableName().'.role', 'student')
							  ->where('guardian_id', $id)
							  ->select('student_name', 'username', StudentRegistration::getTableName() . '.id', 'relationship')
							  ->get();
	
		return array('data' => $result, 'related_students' => $related_students);
	}
	/*public function getViewViewData($id)
	{
		$return = array();
		$model = $this->model_name;
		$result = $model::find($id);

		//getting guardian positions

		$positions = DB::table(GuardianPosition::getTableName())
						->join(Group::getTableName(), Group::getTableName().'.id', '=', GuardianPosition::getTableName().'.group_id')
						->where(GuardianPosition::getTableName().'.guardian_id', $id)
						->where(Group::getTableName().'.is_active', 'yes')
						->lists('group_name', 'group_id');


		$return['data'] = $result;
		$return['groups'] = $positions;
	
		return $return;
	}*/


	///////////////////////////////////////////////////////////////////////////////////////////
	/////////
	/////////		Ajax funtions
	///////
	///////////////////////////////////////////////////////////////////////////////////////////////

	public function ajaxSearchStudents($query)
	{
		$return = array('status' => 'error', 'data' => array(), 'msg' => 'Please fill atleast one criteria');

		if(!($query['unique_school_roll_number'] == '' && $query['student_name'] == '' && $query['class_id'] == 0))
		{
			$data = DB::table(StudentRegistration::getTableName())
						  ->join(Student::getTableName(), Student::getTableName().'.student_id', '=', StudentRegistration::getTableName().'.id')
						  ->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.current_class_id')
						  ->select(StudentRegistration::getTableName().'.id', StudentRegistration::getTableName().'.student_name', Classes::getTableName().'.class_name', StudentRegistration::getTableName().'.photo');

			if(strlen(trim($query['unique_school_roll_number'])))
			{
				$data = $data->where('unique_school_roll_number', $query['unique_school_roll_number']);		 
			}
			else
			{
				if(strlen(trim($query['student_name'])))
					$data = $data->where('student_name', 'Like', '%'.$query['student_name'].'%');
				if($query['class_id'])
					$data = $data->where('class_id', $query['class_id']);
			}
			
			$data = $data->get();

			$return['data'] = $data;
			$return['status'] = 'success';
		}

		return $return;
		
	}


	
}