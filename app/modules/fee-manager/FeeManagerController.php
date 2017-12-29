<?php

class FeeManagerController extends BaseController
{
	protected $view = 'fee-manager.views.';

	protected $model_name = 'MonthlyFee';

	protected $module_name = 'fee-manager';

	protected $role;

	// @override
	public function getSearchColumns($columnsToShow = array())
	{
		
		$columns = count($columnsToShow) ? $columnsToShow : $this->columnsToShow ;
		$i = 2;
		$html = '<tr><td></td>';
		foreach($columns as $cols)
		{
			$old_value = ''; // TODO: get old value from url parameters if it exists
			$html .= '<td><input type = "text" class = "input-sm search_column" value="'. $old_value .'" id = "'.$i++.'"><input type = "hidden" class = "field_name" value = "'.$cols['column_name'].'"></td>';
		}	
		$html .= '<td colspan = "2"></td>';
		$html .= "</tr>";
		return $html;
	}

	/*
	 * For monthly fee
	 */
	public function getMonthlyFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_view');
		$model = new MonthlyFee;
		$columnsToShow = 
					array(
						array(
							'column_name'	=> 'class',
							'alias'			=> 'Class'
						),
						array(
							'column_name'	=> 'amount',
							'alias'			=> 'Amount'
						),
					);

		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns($columnsToShow);
		$queries = $this->getQueries();

		return View::make($this->view.'monthly-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('role', $this->role);
	}

	public function postMonthlyFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();
		$result = $this->validateInput($data, false, 'MonthlyFee');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$old_record = MonthlyFee::where('class_id', $data['class_id'])->first();
			if($old_record)
			{
				$data['id'] = $old_record->id;
				$id = $this->updateInDatabase($data, [], 'MonthlyFee');	
			}
			else
			{
				$id = $this->storeInDatabase($data, 'MonthlyFee');	
			}
			

			$success = true;
			$msg = 'Record successfully created';

			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		if($success)
		{
			Session::flash('success-msg', $msg);
			return Redirect::back();
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back()
							->withInput();
		}
	}

	public function postMonthlyFeeDelete()
	{
		AccessController::allowedOrNot('fee-manager', 'can_delete');
		$model = new MonthlyFee;
		
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	/*
	 * For hostel fee
	 */
	public function getHostelFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_view');
		$model = new HostelFee;
		$columnsToShow = 
					array(
						array(
							'column_name'	=> 'class',
							'alias'			=> 'Class'
						),
						array(
							'column_name'	=> 'section_name',
							'alias'			=> 'Section'
						),
						array(
							'column_name'	=> 'type',
							'alias'			=> 'Type'
						),
						array(
							'column_name'	=> 'amount',
							'alias'			=> 'Amount'
						),
					);

		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns($columnsToShow);
		$queries = $this->getQueries();

		return View::make($this->view.'hostel-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('role', $this->role);
	}

	public function postHostelFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();

		if(!Input::get('class_id'))
		{
			Session::flash('error-msg', 'Form Incomplete');
			return Redirect::back()
							->withInput();
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			if($data['section_id']==0)
			{

				// writing the fee for all sections
				$class_section_table = ClassSection::getTableName();
				$section_table = Section::getTableName();

				$sections = DB::table($section_table)
								->join($class_section_table, $class_section_table.'.section_code', '=', $section_table.'.section_code')
								->where($section_table.'.is_active', 'yes')
								->where($class_section_table.'.is_active', 'yes')
								->where($class_section_table.'.class_id', Input::get('class_id'))
								->select($section_table.'.id')
								->get();

				foreach($sections as $section)
				{
					$data['section_id'] = $section->id;
					$id = $this->updateHostelFee($data);

					if(gettype($id)=='object')
					{
						// we got a view instead of ID
						return $id;
					}
					
				}
			}
			else
			{
				$id = $this->updateHostelFee($data);
				if(gettype($id)=='object')
				{
					// we got a view instead of ID
					return $id;
				}
			}

			$success = true;
			$msg = 'Record successfully created';

			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		if($success)
		{
			Session::flash('success-msg', $msg);
			return Redirect::back();
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back()
							->withInput();
		}

	}

	public function updateHostelFee($data)
	{
		AccessController::allowedOrNot('fee-manager', 'can_update');
		$result = $this->validateInput($data, false, 'HostelFee');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		
		$old_record = HostelFee::where('class_id', $data['class_id'])
							->where('section_id', $data['section_id'])
							->where('type', $data['type'])
							->first();
		if($old_record)
		{
			$data['id'] = $old_record->id;
			$id = $this->updateInDatabase($data, [], 'HostelFee');	
		}
		else
		{
			$id = $this->storeInDatabase($data, 'HostelFee');	
		}
		
		return $id;
	}

	public function postHostelFeeDelete()
	{
		AccessController::allowedOrNot('fee-manager', 'can_delete');
		$model = new HostelFee;
		
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	/*
	 * For examination fee
	 */
	public function getExaminationFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_view');
		$model = new ExaminationFee;
		$columnsToShow = 
					array(
						array(
							'column_name'	=> 'exam_name',
							'alias'			=> 'Exam_name'
						),
						array(
							'column_name'	=> 'amount',
							'alias'			=> 'Amount'
						),
						array(
							'column_name'	=> 'class_name',
							'alias'			=> 'Class'
						),
						array(
							'column_name'	=> 'month',
							'alias'			=> 'Month'
						),
					);

		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns($columnsToShow);
		$queries = $this->getQueries();

		return View::make($this->view.'examination-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('role', $this->role);
	}

	public function postExaminationFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();
		$result = $this->validateInput($data, false, 'ExaminationFee');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$old_record = ExaminationFee::where('class_id', $data['class_id'])
										->where('exam_id', $data['exam_id'])
										->first();
			if($old_record)
			{
				$data['id'] = $old_record->id;
				$id = $this->updateInDatabase($data, [], 'ExaminationFee');	
			}
			else
			{
				$id = $this->storeInDatabase($data, 'ExaminationFee');	
			}
			

			$success = true;
			$msg = 'Record successfully created';

			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		if($success)
		{
			Session::flash('success-msg', $msg);
			return Redirect::back();
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back()
							->withInput();
		}

		return $id;
	}

	public function postExaminationFeeDelete()
	{
		AccessController::allowedOrNot('fee-manager', 'can_delete');
		$model = new ExaminationFee;
		
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	/*
	 * For misc fee
	 */
	public function getMiscClassFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_view');
		$model_class = new MiscClassFee;

		$columnsToShowClass = 
					array(
						array(
							'column_name'	=> 'class',
							'alias'			=> 'Class'
						),
						array(
							'column_name'	=> 'section_name',
							'alias'			=> 'Section'
						),
						array(
							'column_name'	=> 'title',
							'alias'			=> 'Title'
						),
						array(
							'column_name'	=> 'amount',
							'alias'			=> 'Amount'
						),
						array(
							'column_name'	=> 'month',
							'alias'			=> 'Month'
						),
						array(
							'column_name'	=> 'session_name',
							'alias'			=> 'Session Name'
						),
					);
		$queryString = $this->getQueryString();
		$data_class = $model_class->getListViewData($queryString);
		$data = array('class'=> $data_class['data']);

		$searchColumns = $this->getSearchColumns($columnsToShowClass);
		$queries = $this->getQueries();

		return View::make($this->view.'misc-class-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('role', $this->role);
	}
	public function getMiscStudentFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_view');
		$model_student = new MiscStudentFee;
		
		$columnsToShowStudent = 
					array(
						array(
							'column_name'	=> 'title',
							'alias'			=> 'Title'
						),
						array(
							'column_name'	=> 'amount',
							'alias'			=> 'Amount'
						),
						array(
							'column_name'	=> 'month',
							'alias'			=> 'Month'
						),
						array(
							'column_name'	=> 'student_id',
							'alias'			=> 'Student Id'
						),
						array(
							'column_name'	=> 'student_name',
							'alias'			=> 'Student Name'
						),
						array(
							'column_name'	=> 'session_name',
							'alias'			=> 'Session Name'
						),
					);
				
		$queryString = $this->getQueryString();
		
		
		$data_student = $model_student->getListViewData($queryString);

		$data = array('student' => $data_student['data']);

		$searchColumns = $this->getSearchColumns($columnsToShowStudent);
		$queries = $this->getQueries();

		return View::make($this->view.'misc-student-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('role', $this->role);

	}

	public function postMiscClassFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();

		if(!Input::get('class_id'))
		{
			Session::flash('error-msg', 'Form Incomplete');
			return Redirect::back()
							->withInput();
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			if($data['section_id']==0)
			{

				// writing the fee for all sections
				$class_section_table = ClassSection::getTableName();
				$section_table = Section::getTableName();

				$sections = DB::table($section_table)
								->join($class_section_table, $class_section_table.'.section_code', '=', $section_table.'.section_code')
								->where($section_table.'.is_active', 'yes')
								->where($class_section_table.'.is_active', 'yes')
								->where($class_section_table.'.class_id', Input::get('class_id'))
								->select($section_table.'.id')
								->get();

				foreach($sections as $section)
				{
					
					$data['section_id'] = $section->id;
					$id = $this->updateMiscClassFee($data);

					if(gettype($id)=='object')
					{
						// we got a view instead of ID
						return $id;
					}
					
				}
			}
			else
			{
				$id = $this->updateMiscClassFee($data);
				if(gettype($id)=='object')
				{
					// we got a view instead of ID
					return $id;
				}
			}

			$success = true;
			$msg = 'Record successfully created';

			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		if($success)
		{
			Session::flash('success-msg', $msg);
			return Redirect::back();
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back()
							->withInput();
		}

	}

	public function updateMiscClassFee($data)
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
				
		$result = $this->validateInput($data, false, 'MiscClassFee');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		return $this->storeInDatabase($data, 'MiscClassFee');	
		
	}

	public function getMiscClassFeeEdit($id)
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = MiscClassFee::find($id);
		if(!$data)
		{
			Session::flash('error-msg', 'Record does not exist in database');
			return Redirect::back();
		}
		return View::make($this->view . 'edit-misc-class-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('role', $this->role);

	}

	public function postMiscClassFeeEdit($id)
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();

		$result = $this->validateInput($data, true, 'MiscClassFee');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		$this->updateInDatabase($data, [], 'MiscClassFee');

		Session::flash('success-msg', 'Successfully edited');
		
		return Redirect::route('fee-manager-misc-class-fee-get');

	}

	public function postMiscClassFeeDelete()
	{
		AccessController::allowedOrNot('fee-manager', 'can_delete');
		$model = new MiscClassFee;
		
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	public function postMiscStudentFee()
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();

		// the visible student ID is username!!
		$user = Users::where('username', $data['student_id'])
									->where('role', 'student')
									->first();
		$data['student_id'] = $user ? $user->user_details_id : 0;

		$result = $this->validateInput($data, false, 'MiscStudentFee');

		if(Input::has('student_id'))
		{
			$student = Student::where('student_id', $data['student_id'])
								->where('current_session_id', HelperController::getCurrentSession())
								->first();
			if(!$student)
			{
				if($result['status']=='success')
				{
					$result['status'] = 'error';
					$result['data'] = new Illuminate\Support\MessageBag;
				}
				$result['data']->add('student_id', 'Student not registered for current session');
			}
		}

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		$id = $this->storeInDatabase($data, 'MiscStudentFee');

		Session::flash('success-msg', 'Record successfully created');

		return Redirect::back();
	}

	public function getMiscStudentFeeEdit($id)
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = MiscStudentFee::find($id);
		if(!$data)
		{
			Session::flash('error-msg', 'Record does not exist in database');
			return Redirect::back();
		}

		// the visible student ID is username!!
		$data->student_id = Users::where('user_details_id', $data->student_id)
															->where('role', 'student')
															->first()
															->username;	

		return View::make($this->view . 'edit-misc-student-fee')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('role', $this->role);
	}

	public function postMiscStudentFeeEdit($id)
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();

		// the visible student ID is username!!
		$data['student_id'] = HelperController::getStudentIdFromUsername($data['student_id']);
		
		$result = $this->validateInput($data, true, 'MiscStudentFee');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		$this->updateInDatabase($data, [], 'MiscStudentFee');

		Session::flash('success-msg', 'Successfully edited');
		
		return Redirect::back();
	}	

	public function postMiscStudentFeeDelete()
	{
		AccessController::allowedOrNot('fee-manager', 'can_delete');
		$model = new MiscStudentFee;
		
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	public function getScholarship() 
	{
		AccessController::allowedOrNot('fee-manager', 'can_view');
		$columnsToShowStudent = 
					array(
						array(
							'column_name'	=> 'type',
							'alias'			=> 'Type'
						),
						array(
							'column_name'	=> 'percent',
							'alias'			=> 'Percent'
						),
						array(
							'column_name'	=> 'student_id',
							'alias'			=> 'Student Id'
						),
						array(
							'column_name'	=> 'student_name',
							'alias'			=> 'Student Name'
						),
						array(
							'column_name'	=> 'session_name',
							'alias'			=> 'Session Name'
						),
					);
		
		$model = new Scholarship;
		$queryString = $this->getQueryString();
				
		$data_student = $model->getListViewData($queryString);

		$data = array('student'=>$data_student['data']);

		$searchColumns = $this->getSearchColumns($columnsToShowStudent);
		$queries = $this->getQueries();

		return View::make($this->view.'scholarship')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('role', $this->role);
		
	}

	public function postScholarship()
	{
		AccessController::allowedOrNot('fee-manager', 'can_create,can_edit');
		$data = Input::all();
		
		// the visible student ID is username!!
		$data['student_id'] = HelperController::getStudentIdFromUsername($data['student_id']);

		$result = $this->validateInput($data, false, 'Scholarship');

		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$old_record = Scholarship::where('student_id', $data['student_id'])
																->where('type', $data['type'])
																->first();
			if($old_record)
			{
				$data['id'] = $old_record->id;
				$id = $this->updateInDatabase($data, [], 'Scholarship');	
			}
			else
			{
				$id = $this->storeInDatabase($data, 'Scholarship');	
			}

			$success = true;
			$msg = 'Record successfully created';

			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		if($success)
		{
			Session::flash('success-msg', $msg);
			return Redirect::back();
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back()
							->withInput();
		}
	}

	public function postScholarshipDelete()
	{
		AccessController::allowedOrNot('fee-manager', 'can_delete');
		$model = new Scholarship;
		
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	public function getTaxConfig()
	{
		AccessController::allowedOrNot('fee-manager', 'can_edit');
		return View::make($this->view . '.config')
			->with('config', FeeManagerHelperController::getConfig());
	}

	public function postTaxConfig()
	{
		AccessController::allowedOrNot('fee-manager', 'can_edit');
		$validator = Validator::make(
										Input::all(),
										array(
												'tax_percent'	=> ['required', 'numeric', 'min:0', 'max:100']
										)
									);
		if($validator->fails())
		{
			Session::flash('error-msg', 'Validation Error!!');
			return Redirect::back()
					->withInput()
					->withErrors($validator->messages());
		}
		
		if (FeeManagerHelperController::setConfig(Input::all()))
		{
			Session::flash('success-msg', 'Configuration updated');
		}
		else
		{
			Session::flash('error-msg', 'Error updating Configuration');
		}

		return Redirect::back();
	}
}