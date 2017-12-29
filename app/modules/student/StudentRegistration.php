<?php
class StudentRegistration extends BaseModel
{
	protected $table = 'student_registration';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	public $model_name = 'StudentRegistration';
	
	public $createRule = [
							'student_name' => ['required'],
							'last_name'=> ['required'],
							'email' => ['email','unique:student_registration,email'],
							'is_active' => ['required', 'in:yes,no'],
							'current_address' => [],
							'permanent_address' => [],
							'sex' => ['required','in:male,female,other'],
							//'dob_in_bs' => ['required','date'],
//							//'dob_in_ad' => ['date'],
							'guardian_contact' => [],
							'secondary_contact' => [],
							'registered_session_id' => ['required', 'not_in:0'],
							'registered_class_id' => ['required', 'not_in:0'],
							'registered_section_code' => ['required', 'not_in:0'],
							'photo' => ['mimes:png,jpg,jpeg', 'max:256'],
							'unique_school_roll_number' => ['unique:student_registration,unique_school_roll_number']

						];

	public $updateRule = [
							'student_name' => ['required'],

							'email' => ['email','unique:student_registration,email'],
							'is_active' => ['required', 'in:yes,no'],
							'current_address' => [],
							'permanent_address' => [],
							'sex' => ['required','in:male,female,other'],
							//'dob_in_bs' => ['required','date'],
							//'dob_in_ad' => ['date'],
							'guardian_contact' => [],
							'secondary_contact' => [],
							'registered_session_id' => ['not_in:0'],
							'registered_class_id' => ['not_in:0'],
							'registered_section_code' => ['not_in:0'],
							'photo' => ['mimes:png,jpg,jpeg', 'max:256'],
							'unique_school_roll_number' => ['unique:student_registration,unique_school_roll_number']

						];

	public function getDeactiveListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Student::getTableName(), Student::getTableName().'.student_id', '=', $model::getTableName().'.id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.deactivate_class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', Student::getTableName().'.deactivate_session_id')
					->join(Section::getTableName(), Section::getTableName().'.section_code', '=', Student::getTableName().'.deactivate_section_code')
					->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
					//
					//->where($model::getTableName().'.is_active', 'yes')
					->where(AcademicSession::getTableName().'.is_active', 'yes')
					->where(Classes::getTableName().'.is_active', 'yes')
					->where(Users::getTableName().'.role', 'student');
					/*TODO
					MAke changes here
					*/

		$result = $result->select($model::getTableName().'.*', 
						Classes::getTableName().'.class_name', 
						AcademicSession::getTableName().'.session_name',
						Users::getTableName().'.username', 'deactivate_session_id', 'deactivate_class_id', 'deactivate_section_code');
		

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
				elseif($col == 'class_name')
					$result = $result->where(Classes::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col == 'username')
					$result = $result->where(Users::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col == 'last_name')
					$result = $result->where(StudentRegistration::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			}
		}
		//}

		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy(Classes::getTableName() . '.sort_order')
					->orderBy('sort_order', 'ASC')
					->orderBy('current_section_code', 'ASC')
					->orderBy('student_name', 'ASC');
		}

		$result = $result->paginate($queryString['paginate']);
		
		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getListViewData($queryString)
	{

		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Student::getTableName(), Student::getTableName().'.student_id', '=', $model::getTableName().'.id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.current_class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', Student::getTableName().'.current_session_id')
					->join(Section::getTableName(), Section::getTableName().'.section_code', '=', Student::getTableName().'.current_section_code')
					->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
					//
					//->where($model::getTableName().'.is_active', 'yes')
					->where(AcademicSession::getTableName().'.is_active', 'yes')
					->where(Classes::getTableName().'.is_active', 'yes')
					->where(Users::getTableName().'.role', 'student')
					/*TODO
					MAke changes here
					*/
					->where('current_session_id', Input::get('session_id', HelperController::getCurrentSession()));

		$result = $result->select($model::getTableName().'.*', 
						Classes::getTableName().'.class_name', 
						AcademicSession::getTableName().'.session_name',
						Users::getTableName().'.username', 'current_session_id', 'current_class_id', 'current_section_code');
		

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
				elseif($col == 'class_name')
					$result = $result->where(Classes::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col == 'username')
					$result = $result->where(Users::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				elseif($col == 'last_name')
					$result = $result->where(StudentRegistration::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			}
		}
		//}

		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy(Classes::getTableName() . '.sort_order')
					->orderBy('sort_order', 'ASC')
					->orderBy('current_section_code', 'ASC')
					->orderBy('student_name', 'ASC');
		}

		$result = $result->paginate($queryString['paginate']);
		
		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getViewViewData($id)
	{
		//check if superadmin, current user, and guardian
		if(Auth::user()->check())
		{
			if(Auth::user()->user()->role == 'student' && Auth::user()->user()->user_details_id != $id)
			{
				App::abort(403, 'Not allowed');
			}
			elseif(Auth::user()->user()->role == 'guardian')
			{
				$parent_ids = StudentGuardianRelation::where('student_id', $id)
														->lists('guardian_id');

				if(!in_array(Auth::user()->user()->user_details_id, $parent_ids))
				{
					App::abort(403, 'Not allowed');
				}
			}
		}

		$model = $this->model_name;
		
		$result = DB::table($model::getTableName())
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', $model::getTableName().'.registered_class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', $model::getTableName().'.registered_session_id')
					->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', $model::getTableName().'.id')
					//
					//->where($model::getTableName().'.is_active', 'yes')
					->where(AcademicSession::getTableName().'.is_active', 'yes')
					->where(Users::getTableName().'.role', 'student')
					->where(Classes::getTableName().'.is_active', 'yes')
					->select($model::getTableName().'.*', Classes::getTableName().'.class_name', AcademicSession::getTableName().'.session_name', Users::getTableName().'.username')
					->where($model::getTableName().'.id', $id)
					->first();

		$related_guardians = DB::table(StudentGuardianRelation::getTableName())
							  ->join(Guardian::getTableName(), Guardian::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.guardian_id')
							  ->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', Guardian::getTableName().'.id')
							  ->where(Users::getTableName().'.role', 'guardian')
							  ->where('student_id', $id)
							  ->select('guardian_name', 'username', Guardian::getTableName() . '.id', 'relationship')
							  ->get();
		$ethnicity_name = DB::table('ethnicity')
							->join('student_registration', 'student_registration.ethnicity_id','=','ethnicity.id')
							->where('student_registration.id', $id)
							->pluck('ethnicity.ethnicity_name');
		$house_name = DB::table('houses')
						->join('student_registration','student_registration.house_id','=','houses.id')
						->where('student_registration.id',$id)
						->pluck('houses.house_name');


		
		return array('data' => $result, 'related_guardians' => $related_guardians, 'ethnicity_name' => $ethnicity_name,'house_name' => $house_name);
	
	}

	public function getEditViewData($id)
	{
		$model = $this->model_name;
		$result = $model::join(
			Student::getTableName(),
			Student::getTableName() . '.student_id', '=',
			$model::getTableName() . '.id'
		)->where(
			$model::getTableName() . '.id',
			$id
		)->where(
			Student::getTableName() . '.current_session_id', 
			HelperController::getCurrentSession()
		)->select(
			$model::getTableName() . '.*',
			Student::getTableName() . '.current_roll_number',
			'current_session_id', 'current_class_id', 'current_section_code'
		)->first();
		
		return $result;
	}

		public function getCreateViewData()
	{
		$ethnicity = DB::table('ethnicity')->select('ethnicity_name','id')->where('is_active', 'yes')->lists('ethnicity_name' , 'id');
		return $ethnicity;
		
	}

}