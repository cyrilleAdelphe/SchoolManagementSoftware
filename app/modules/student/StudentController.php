<?php
///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////
define('REPORT_CONFIG_FILEPATH', base_path().'/app/modules/student/report-config.json');
///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////

class StudentController extends BaseController
{
	protected $module_name = 'student';
 	protected $model_name = 'StudentRegistration';//this controller has multiple models
 	protected $view = 'student.views.';

 	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'student_name',
										'alias'			=> 'Student Name'
									),
									array
									(
										'column_name' 	=> 'last_name',
										'alias'			=> 'Last Name'
									),
									array
									(
										'column_name' 	=> 'username',
										'alias'			=> 'Username'
									),
									array
									(
										'column_name' 	=> 'guardian_contact',
										'alias'			=> 'Guardian Contact'
									),
									array
									(
										'column_name' 	=> 'class_name',
										'alias'			=> 'Class'
									),
									array
									(
										'column_name' 	=> 'registered_section_code',
										'alias'			=> 'Section'
									));
	
	public function getDeactiveStudentList()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getDeactiveListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'deactive-list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					//->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					->with('role', $this->role);
	}

	public function postRestoreDeactivateStudents()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');

		$data = Input::all();

		Student::whereNull('current_session_id')
				->whereNull('current_class_id')
				->whereNull('current_section_code')
				->where('student_id', $data['user_id'])
				->update([
						'current_session_id' => $data['deactivate_session_id'],
						'current_class_id' => $data['deactivate_class_id'],
						'current_section_code' => $data['deactivate_section_code'],
						'deactivate_session_id' => NULL,
						'deactivate_class_id' => NULL,
						'deactivate_section_code' => NULL
					]);

		Session::flash('success-msg', 'Succesfully Restored');

		return Redirect::back();
	}

	public function postDeactivateStudents()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');

		$data = Input::all();

		Student::where('current_session_id', $data['current_session_id'])
				->where('current_class_id', $data['current_class_id'])
				->where('current_section_code', $data['current_section_code'])
				->where('student_id', $data['user_id'])
				->update([
						'current_session_id' => NULL,
						'current_class_id' => NULL,
						'current_section_code' => NULL,
						'deactivate_session_id' => $data['current_session_id'],
						'deactivate_class_id' => $data['current_class_id'],
						'deactivate_section_code' => $data['current_section_code']
					]);

		Session::flash('success-msg', 'Succesfully Deactivated');

		return Redirect::back();
	}

	public function getMergeParents()
	{
		return View::make($this->view.'merge-parents')
					->with('module_name', $this->module_name);
	}

	public function postMergeParents()
	{
		AccessController::allowedOrNot('student', 'can_edit');

		//get file

		$reader = Excel::load(Input::file('excel_file'))
						->get();

		foreach($reader as $row)
		{
			foreach($row as $index => $value)
			{
				$row[$index] = trim($value);	
			}
		}

		$users_table = Users::getTableName();
		$student_guardian_relation_table = StudentGuardianRelation::getTableName();

		try
		{
			DB::connection()->getPdo()->beginTransaction();
				foreach($reader as $row)
				{
					foreach($row as $r)
					{
						$student_id = $father_id = $mother_id = 0;
						if(strlen($row['student_username']) && $row['father_username'] != '-' && $row['mother_username'] != '-')
						{
							$student_id = (int) DB::table($users_table)
											->where('username', $row['student_username'])
											->pluck('user_details_id');

							$father_id = (int) DB::table($users_table)
											->where('username', $row['father_username'])
											->pluck('user_details_id');

							$mother_id = (int) DB::table($users_table)
											->where('username', $row['mother_username'])
											->pluck('user_details_id');

							if($father_id)
							{
								$record = StudentGuardianRelation::firstOrNew(['student_id' => $student_id, 
																	          'guardian_id' => $father_id,
																	          'relationship'	=>	'Father']);

								$record->guardian_id = $father_id;
								$record->save();	
							}
										

							if($mother_id)
							{
								$record = StudentGuardianRelation::firstOrNew(['student_id' => $student_id, 
																	          'guardian_id' => $mother_id,
																	          'relationship'	=>	'Mother']);

								$record->guardian_id = $mother_id;
								$record->save();					
							}
							
						}
					}	
				}

			Session::flash('success-msg', 'Parents successfully merged');
				
			DB::connection()->getPdo()->commit();	
		}
		catch(Exception $e)
		{
			/*echo '<pre>';
			print_r($row);
			echo $student_id.'<br>';
			echo $father_id.'<Br>';
			echo $mother_id.'<br>';
			die();
			Session::flash('error-msg', $e->getMessage());*/
			//do nohting
		}
		
		return Redirect::back();
	}

	////// StudentReport-dynamic-header-titles-v1-changes-made-here ///////
	public function getShowReportConfig()
	{
		AccessController::allowedOrNot('student', 'can_show_report');
		if(File::exists(REPORT_CONFIG_FILEPATH))
		{
			$data = json_decode(File::get(REPORT_CONFIG_FILEPATH), true);

			return View::make($this->view.'show-report-config')
						->with('data', $data);
		}
		else
		{
			die('Configuration file not found. Contact Administrator');
		}
	}

	public function postShowReportConfig()
	{
		AccessController::allowedOrNot('student', 'can_show_report');
		$data = Input::all();
		$records = [];
		foreach($data['id'] as $id => $d)
		{
			$temp = [];
			$temp['id'] = $data['id'][$id];
			$temp['alias'] = $data['alias'][$id];
			$temp['column_name'] = $data['column_name'][$id];
			$temp['table'] = $data['table'][$id];
			$temp['hidden'] = $data['hidden'][$id];
			$temp['show'] = isset($data['show'][$id]) ? 'yes' : 'no';
			$records[] = $temp;
		}

		try
		{
			File::put(REPORT_CONFIG_FILEPATH, json_encode($records, JSON_PRETTY_PRINT));	
			Session::flash('success-msg', 'Columns Successfully set');
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}
		
		return Redirect::back();
	}
	////// StudentReport-dynamic-header-titles-v1-changes-made-here ///////
	
	public function getMassRollAssignment()
	{
		AccessController::allowedOrNot('student', 'can_edit');

		return View::make($this->view.'mass-roll-assignment')
					->with('module_name', $this->module_name);
	}

	public function postMassRollAssignment()
	{
		AccessController::allowedOrNot('student', 'can_edit');	
		
		$input = Input::all();

		

		$validator = Validator::make(
									array(
										'excel_file' => Input::hasFile('excel_file') ? Input::file('excel_file')->getClientOriginalExtension() : '',
										'registered_session_id' => Input::get('registered_session_id'),
										'registered_class_id' => Input::get('registered_class_id'),
										'registered_section_code' => Input::get('registered_section_code')
									),
									array(
										'excel_file' => array('required', 'in:xls,xlsx,csv'),
										//'excel_file' => array('required', 'mimes:xls,xlsx'),
										'registered_session_id' => ['required', 'not_in:0'],
										'registered_class_id' => ['required', 'not_in:0'],
										'registered_section_code' => ['required', 'not_in:0'],
									)
								);
		if ($validator->fails())
		{
			return Redirect::back()
											->withInput()
											->withErrors($validator);
		}

		$success = false;
		$msg = '';
		$param = array('id' => 0);


		$reader = Excel::load(Input::file('excel_file'))
										->get();

		$student_table = Student::getTableName();
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			foreach($reader as $row)
			{
				DB::table($student_table)
					->where('student_id', $row['student_id'])
					->where('current_session_id', $input['registered_session_id'])
					->where('current_class_id', $input['registered_class_id'])
					->where('current_section_code', $input['registered_section_code'])
					->update(['current_roll_number'=> $row['current_roll_number']]);
				
			}
				Session::flash('success-msg', 'Roll successfully assigned');

			DB::connection()->getPdo()->commit();
		}
		catch (Exception $e)
		{
			//die('here');
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back();	
	}
	
	public function getMigrateStudent()
	{
		AccessController::allowedOrNot('student', 'can_migrate');
		return View::make($this->view.'migrate-students')
					->with('module_name', $this->module_name);
	}

	public function postMigrateStudent()
	{
		AccessController::allowedOrNot('student', 'can_migrate');
		$input = Input::all();

		$validator = Validator::make(
									array(
										'excel_file' => Input::hasFile('excel_file') ? Input::file('excel_file')->getClientOriginalExtension() : '',
										'registered_session_id' => Input::get('registered_session_id'),
										'registered_class_id' => Input::get('registered_class_id'),
										'registered_section_code' => Input::get('registered_section_code')
									),
									array(
										'excel_file' => array('required', 'in:xls,xlsx,csv'),
										//'excel_file' => array('required', 'mimes:xls,xlsx'),
										'registered_session_id' => ['required', 'not_in:0'],
										'registered_class_id' => ['required', 'not_in:0'],
										'registered_section_code' => ['required', 'not_in:0'],
									)
								);
		if ($validator->fails())
		{
			return Redirect::back()
											->withInput()
											->withErrors($validator);
		}

		$success = false;
		$msg = '';
		$param = array('id' => 0);


		$reader = Excel::load(Input::file('excel_file'))
										->get();

		$usernames = [];
		
		try
		{
			foreach($reader as $row)
			{
				
				$usernames[] = $row['student_username'];
				
			}

			$user_table = Users::getTableName();
			$student_ids = DB::table($user_table)
							->whereIn('username', $usernames)
							->where('role', 'student')
							->lists('user_details_id');

			DB::connection()->getPdo()->beginTransaction();
				
				foreach($student_ids as $student_id)
				{
					Student::firstOrCreate(['student_id' => $student_id, 'current_session_id' => $input['registered_session_id'], 'current_class_id' => $input['registered_class_id'], 'current_section_code' => $input['registered_section_code']]);
				}

				Session::flash('success-msg', 'Students Successfully migrated');

			DB::connection()->getPdo()->commit();
		}
		catch (Exception $e)
		{
			//die('here');
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back();

	}
						
	public function getStudentReportExcel()
	{	

		AccessController::allowedOrNot($this->module_name, 'can_export');
		$input_data = Input::all();
		
		Excel::create('Student Report', function($excel) use ($input_data)
		{

			$excel->sheet('Student Report', function($sheet) use ($input_data)
			{

				$row = [];
				$row[] = 'Student Report of Class:' .$input_data['class_1'];
				$row[] = '| Section: '.$input_data['section_1'];
				$row[] = 'To Class: '. $input_data['class_2'];
				$row[] = ' Section: '. $input_data['section_2'] ;
				$row[] = 'Age: '. $input_data['age_1'] .'-' . $input_data['age_2'];
				$row[] = 'Gender: '. $input_data['gender'] ;
				$row[] = 'Ethnicity: '. $input_data['ethnicity'] ;
				$row[] = 'Discount Organization: ' . $input_data['discount_org_id'] ;
				$row[] = 'House: ' . $input_data['house'] ;
				$row[] = 'Facility: '. $input_data['facility'];
				$sheet->row(1, $row);
				$i =1;

				$input_data['row'] = json_decode($input_data['json']);

				foreach($input_data['row'] as $r)
				{
					$row = [];
					foreach($r as $d)
					{
						$row[] =  $d;
					}
					$sheet->row(++$i, $row);		
					
				}
					
			});
		})->download('xls');
	} 
								 
		 public function getViewView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$model = new $this->model_name;
		
		$data = $model->getViewViewData($id);
		$house_name = DB::table('houses')
						->join('student_registration','student_registration.house_id','=','houses.id')
						->where('student_registration.id',$id)
						->pluck('houses.house_name');



		return View::make($this->view.'view')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('actionButtons', $this->getActionButtons())
					->with('house_name', $house_name);

	}

	public function getCreateView()		 
						{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$ethnicity = DB::table('ethnicity')->select('ethnicity_name','id')->where('is_active', 'yes')->lists('ethnicity_name', 'id');
		
		$house = DB::table('houses')->select('house_name','id')->where('is_active', 'yes')->lists('house_name' , 'id');

		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('actionButtons', $this->getActionButtons())
					->with('ethnicity', $ethnicity)
					->with('house',$house);
	
						}
	public function postCreateView()
	{
		AccessController::allowedOrNot('student', 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$dataToStoreInStudentTable = array();
		$dataToStoreInUserTable = array();
		

		if (isset($data['dob_in_ad']) && $data['dob_in_ad'] != "YYYY-MM-DD" && (bool)strtotime($data['dob_in_ad']) != false )
		{

			$data['dob_in_ad'] = DateTime::createFromFormat('Y-m-d', $data['dob_in_ad'])												->format('Y-m-d');
			$data['dob_in_bs'] = (new DateConverter)->ad2bs($data['dob_in_ad']);	
		}
		else
		{
			$data['dob_in_bs'] = '';	
		}
		/*
		/ Task of Validating
		/
		/
		*/
		
		$dataToStoreInStudentTable = $this->assignValues('StudentRegistration', $data);
		$dataToStoreInStudentTable['current_roll_number'] = $data['current_roll_number'];

		
		$dataToStoreInStudentTable['current_session_id'] = $data['registered_session_id'];
		$dataToStoreInStudentTable['current_class_id'] = $data['registered_class_id'];
		$dataToStoreInStudentTable['current_section_code'] = $data['registered_section_code'];
		$dataToStoreInStudentTable['house_id']			= $data['house_id'];
		$dataToStoreInStudentTable['ethnicity_id']			= $data['ethnicity_id'];
		$dataToStoreInStudentTable['last_name']			= $data['last_name'];
		
		
		
		$result = $this->validateInput($dataToStoreInStudentTable, false, 'StudentRegistration');

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::errorMsg('some validation error has occured'));
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		

		$dataToStoreInUserTable = $this->assignDataToStoreInUserTable($data);
		$result = $this->validateInput($dataToStoreInUserTable, false, 'Users');

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::errorMsg('some validation error has occured'));
			dd($result['data']);
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}	

		/*
		/ 
		/ Storing begins
		/
		*/
		try
		{
			DB::connection()->getPdo()->beginTransaction();
			//upload file here

			$photo = Input::hasFile('photo') ? Input::file('photo') : '';
			
			if($photo !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/student/assets/images', array('jpg', 'jpeg', 'png'), 10485760000);
				$result = $upload->uploadFile($photo);
				$result = json_decode($result, 1);
				if($result['status'] == 'error')
				{
					
					throw new Exception($result['message']);
				}
				else
				{
					$dataToStoreInStudentTable['photo'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToStoreInStudentTable['photo'] = '';
			}

			$id = $this->storeInDatabase($dataToStoreInStudentTable, 'StudentRegistration'); //student_id

			$dataToStoreInUserTable['user_details_id'] = $id;
			$dataToStoreInUserTable['password'] = Hash::make($data['password']);
			$dataToStoreInUserTable['is_active'] = 'yes';
			$this->storeInDatabase($dataToStoreInUserTable, 'Users');

			$this->storeOrUpdateInStudentTable($data, $id);

						
			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		return $this->redirectAction($success, 'create', $param, $msg);
	}

	
	
	public function getEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$model = new $this->model_name;
		$data = $model->getEditViewData($id);	
		$house = DB::table('houses')->select('house_name','id')->where('is_active', 'yes')->lists('house_name' , 'id');
		
		$ethnicity = DB::table('ethnicity')->select('ethnicity_name','id')->where('is_active', 'yes')->lists('ethnicity_name' , 'id');
	
		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('ethnicity', $ethnicity)
					->with('data', $data)
					->with('house', $house);
					
					
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot('student', 'can_edit');
		$success = false;
		$msg = '';
		$param = array('id' => $id);

		$data = Input::all();
if (isset($data['dob_in_ad']) && $data['dob_in_ad'] != "YYYY-MM-DD" && (bool)strtotime($data['dob_in_ad']) != false )
		{

			$data['dob_in_ad'] = DateTime::createFromFormat('Y-m-d', $data['dob_in_ad'])									

			->format('Y-m-d');
			$data['dob_in_bs'] = (new DateConverter)->ad2bs($data['dob_in_ad']);	
		}
		else
		{
			$data['dob_in_bs'] = '';	
		}

		$dataToUpdateInStudentRegistrationTable = array();

		/*
		/ Task of Validating
		/
		/
		*/

		$dataToUpdateInStudentRegistrationTable = $this->assignValues('StudentRegistration', $data, true);
		
		$result = $this->validateInput($dataToUpdateInStudentRegistrationTable, true, 'StudentRegistration');

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::errorMsg('some validation error has occured'));
			//return Redirect::route($this->module_name.'-edit-get', $id)
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}

		$dataToUpdateInUserTable = $this->assignDataToStoreInUserTable($data, true);
		
		if(isset($data['email']) && $data['email'])
		{
			$result = $this->validateEmailInUsersTable($data['email'], $id);
		}
		/*
		/ 
		/ Storing begins
		/
		*/
		try
		{
			DB::connection()->getPdo()->beginTransaction();
			//upload file here

			$photo = Input::hasFile('photo') ? Input::file('photo') : '';
			
			if($photo !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/student/assets/images', array('jpg', 'jpeg', 'png'), 10485760000, $data['original_photo']);

				$result = $upload->uploadFile($photo);
				$result = json_decode($result, 1);
				
				if($result['status'] == 'error')
				{
					throw new Exception($result['message']);
				}
				else
				{
					$dataToUpdateInStudentRegistrationTable['photo'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToUpdateInStudentRegistrationTable['photo'] = $data['original_photo'];
			}

			$id = $this->updateInDatabase($dataToUpdateInStudentRegistrationTable, array(), 'StudentRegistration'); //student_id
			
			$this->updateInDatabase($dataToUpdateInUserTable, array(array('field' => 'user_details_id', 'operator' => '=', 'value' => $id), array('field' => 'role', 'operator' => '=', 'value' => 'student')), 'Users');

			$this->storeOrUpdateInStudentTable($data, $id, true);
			
			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id;
			DB::connection()->getPdo()->commit();

		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		if (Input::get('redirect_back', 'no') == 'yes')
		{
			Session::flash($success ? 'success-msg' : 'error-msg', $msg);
			return Redirect::back();
		}
		else 
		{
			return $this->redirectAction($success, 'edit', $param, $msg);
		}
	}

	public function postDelete()
	{

		AccessController::allowedOrNot('student', 'can_delete');
		$model = new $this->model_name;
		$id = Input::get('id');
		

		$record = $model->find($id);


		if($record)
		{
			try
			{
				
				DB::connection()->getPdo()->beginTransaction();
					$image = $record->image;
					$user = Users::where('user_details_id', $id)
								->where('role', 'student')
								->first();

					$user->delete();
					
					$record->delete();
					@unlink(app_path().'/modules/student/assets/images/'.$image);
				
				DB::connection()->getPdo()->commit();

				Session::flash('success-msg', 'Delete Successful');	
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', ConfigurationController::errorMsg($e->getMessage()));
			}
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	public function getRegister()
	{
		AccessController::allowedOrNot('student', 'can_create');
		return View::make($this->view . 'studentsregistration');
	}

	public function postRegister()
	{
		AccessController::allowedOrNot('student', 'can_create');
		$model_name = 'StudentRegistration';
 		$input_data = Input::all();

 		//TODO: send the password to the user via email
 		$password = uniqid();
 		$input_data['password'] = $password;

 		$validator = $this->validateInput($input_data,false,$model_name);
 		
 		if($validator['status'] == 'error')
 		{
 			return Redirect::route('students-register-get')
 						->withErrors($validator['data'])
 						->withInput();
 		}

 		$input_data['password'] = Hash::make($password);
 		
 		$database_result = BaseModel::cleanDatabaseOperation([
											[$this,'storeInDatabase',[$input_data,'StudentRegistration']]
										]);
 		//TODO:create a record in the user table as well

 		



 		if($database_result['success'])
		{
			Session::flash('success-msg', 'Students Registered');

	 		//TODO: send email to student (send password)
	 		echo 'Your password is '.$password;
	 		die();

			return Redirect::route($this->module_name.'-register-get');
		}
		else
		{
			Session::flash('error-msg', 'Error! Please try again later');
			echo $database_result['msg'];
			die();

			return Redirect::route($this->module_name.'-register-get');
					
		}
	}
	
	public function getStudentReport() 
	{	
		AccessController::allowedOrNot('student', 'can_show_report');
		$academic_session = AcademicSession::where('is_current', 'yes')
										  ->where('is_active', 'yes')
										  ->pluck('id');

		$class_list = Classes::select('class_name','id')
		                          ->where('academic_session_id', $academic_session)
		                          ->where('is_active','yes')
		                          ->lists('class_name', 'id');

		$house_list = House::select('house_name', 'id')
		                       	 ->where('is_active', 'yes')
		                         ->lists('house_name', 'id');

		$ethnicity_list = Ethnicity::select('ethnicity_name', 'id')
		                         ->where('is_active', 'yes')
		                         ->lists('ethnicity_name', 'id');

		$discount_host = DB::table('billing_discount_organization')
								->select('organization_name', 'id')
								->where('is_active', 'yes')
								->lists('organization_name','id');

		return View::make($this->view.'.student-report')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('class_list', $class_list)
					->with('house_list', $house_list)
					->with('ethnicity_list', $ethnicity_list)
					->with('discount_host', $discount_host);
	}

///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////
	public function getShowReport()
	{	
		AccessController::allowedOrNot('student', 'can_show_report');


		$input_data = Input::all();
		// echo '<pre>';
		// print_r($input_data);
		// die();
		
		$student_details = '';

		$columns_for_report = StudentHelperController::getColumnsForReport();
		$columns_for_report['array_for_select_statment'][] = StudentRegistration::getTableName().'.'.'id';

			
			$student_details = DB::table('students')
								->join('student_registration', 'student_registration.id', '=','students.student_id')
								->join('users', 'users.user_details_id', '=' , 'students.student_id')
								->leftjoin('student_guardian_relation', 'student_guardian_relation.student_id','=','students.student_id')
								->leftjoin('guardians', 'guardians.id' , '=' , 'student_guardian_relation.guardian_id')
								
								->join('classess', 'classess.id','=','students.current_class_id')
								->join('sections', 'sections.section_code','=','students.current_section_code')
								->leftJoin('houses','houses.id','=','student_registration.house_id')
								->leftJoin('ethnicity','ethnicity.id','=','student_registration.ethnicity_id')
								//->select($columns_for_report['array_for_select_statment'])
								->where('students.current_session_id', $input_data['academic_session_id'])
								->where('users.role', 'student');
								

			if($input_data['house'] == 'all')
			{

			}
			else
			{
				$student_details = $student_details->where('houses.id', $input_data['house']);
				//die(1);
			}

			if($input_data['ethnicity'] != 'all')
			{
				//die(2);
				$student_details = $student_details->where('ethnicity.id', $input_data['ethnicity']);
			}	

			if($input_data['gender'] != 'all')
			{
				//die(3);
				$student_details = $student_details->where('sex', $input_data['gender']);
			}


			if($input_data['discount_org_id'] != 0)
			{
				if(!is_null($input_data['discount_type']) || $input_data['discount_type'] != 0)
				{
					//die($input_data['discount_type']);
					$student_details = $student_details->join('billing_discount_details','billing_discount_details.student_id','=','student_registration.id')
														
														->where('billing_discount_details.discount_id', $input_data['discount_type']);
														
				}
			}
			if($input_data['facility'] == "transportation")
			{
				$student_details = $student_details->join('transportation_students', 'transportation_students.student_id','=','students.student_id')
												->join('transportation', 'transportation.id', '=', 'transportation_students.transportation_id');

				$columns_for_report['array_for_select_statment'][] = 'number_plate';
				$columns_for_report['array_for_formating_data'][] = [
                
                    "column_name" => 'number_plate',
                    'alias' => 'Number Plate'
                ];
					
			}


			if(strlen(trim($input_data['age_1'])) && strlen(trim($input_data['age_2'])))
			{
				$start_date = Carbon::now()->subYears($input_data['age_1'])->format('Y-m-d');
				$end_date = Carbon::now()->subYears($input_data['age_2'])->format('Y-m-d');	

				$student_details = $student_details->whereBetween('student_registration.dob_in_ad', [$end_date, $start_date]);
			}

			if($input_data['class_1'] != "all" && $input_data['class_2'] != "all")
			{

				$sort_order_1 = DB::table('classess')->where('id',$input_data['class_1'])->pluck('sort_order');
				$sort_order_2 = DB::table('classess')->where('id',$input_data['class_2'])->pluck('sort_order');

				if($sort_order_1 > $sort_order_2)
				{
					$a = $sort_order_1;
					$b = $sort_order_2;

					$sort_order_2 = $a;
					$sort_order_1 = $b;
				}
				
				
				$class_ids = DB::table('classess')
								->whereBetween('sort_order', [$sort_order_1, $sort_order_2])
								->lists('id');

				if($input_data['section_1'] != 'all')
				{
					$section_code_1 = DB::table('sections')->where('id',$input_data['section_1'])->pluck('section_code');
				}
				else
				{
					$section_code_1 = 'all';
				}

				if($input_data['section_2'] != 'all')
				{
					$section_code_2 = DB::table('sections')->where('id',$input_data['section_2'])->pluck('section_code');
				}
				else
				{
					$section_code_2 = 'all';
				}

				$student_details = $student_details->where(function($query) use ($class_ids, $section_code_1, $section_code_2, $input_data) {

					foreach($class_ids as $c)
					{
						if($c == $input_data['class_1'] && $section_code_1 != 'all')
						{
							$query = $query->orWhere(function($q) use ($section_code_1, $input_data)
								{
									$q->where('current_section_code', $section_code_1)
									  ->where('current_class_id', $input_data['class_1']);
								});

						}
						elseif($c == $input_data['class_2'] && $section_code_2 != 'all')
						{

							$query = $query->orWhere(function($q) use ($section_code_2, $input_data)
								{
									$q->where('current_section_code', $section_code_2)
									  ->where('current_class_id', $input_data['class_2']);
								});

						}
						else
						{
							$query = $query->orWhere('current_class_id', $c);

						}
					}

				});
				
			}
			
			$student_details = $student_details->orderBy('classess.sort_order', 'ASC')
											   ->orderBy('sections.section_code', 'ASC')
											   ->orderBy('student_name', 'ASC');


			
			$student_details = $student_details->select($columns_for_report['array_for_select_statment']);
			$student_details = $student_details->get();
			

			
			$listed_student_ids = [];
			foreach($student_details as $index => $d)
			{
				if(!isset($listed_student_ids[$d->id]))
				{
					$listed_student_ids[$d->id]['id'] = $d->id;
					foreach($columns_for_report['array_for_formating_data'] as $column_name)
					{
						$listed_student_ids[$d->id][$column_name['column_name']] = $d->$column_name['column_name'];
					}
				}
				else
				{
					if(isset($listed_student_ids[$d->id]['guardian_name']))
					{
						$listed_student_ids[$d->id]['guardian_name'] .= ','.$d->guardian_name;
					}

					if(isset($listed_student_ids[$d->id]['relationship']))
					{
						$listed_student_ids[$d->id]['relationship'] .= ','.$d->relationship;
					}
				}

				unset($student_details[$index]);
			}


			
		return View::make($this->view.'.show-report')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('student_details',$listed_student_ids)
					->with('input_data', $input_data)
					->with('columns', $columns_for_report['array_for_formating_data']);
	}		
	
	public function getAjaxClassSectionReport()
	{
		$class_id = Input::get('class_id');
		
		$data = DB::table('classess_sections')
						->join('sections', 'sections.section_code','=','classess_sections.section_code')
						->select('sections.section_name','sections.id')
						->where('classess_sections.class_id',$class_id)
						->lists('sections.section_name', 'id');

													

		return StudentHelperController::generateStaticSelectList($data, 'class_id');

	}
	///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////

	public function getAjaxDiscountTypeReport()
	{

		$discount_org_id = Input::get('discount_org_id');

		$data = DB::table('billing_discount')
					->join('billing_discount_organization', 'billing_discount_organization.id','=','billing_discount.organization_id')
					->select('billing_discount.discount_name', 'billing_discount.organization_id','billing_discount.id')
					->where('billing_discount.organization_id',$discount_org_id)
					->lists('discount_name','id');

		return HelperController::generateStaticSelectList($data, 'discount_type');

		
	}
	
public function getExportExcel()
	{
	

		AccessController::allowedOrNot('student', 'can_export');
		$current_session = Input::get('session_id', 0) ? AcademicSession::find(Input::get('session_id')) :  AcademicSession::find

(HelperController::getCurrentSession());

		if ($current_session)
		{
			Excel::create('Students - ' . $current_session->session_name, function($excel) use ($current_session) {
				$classes = Classes::where('academic_session_id', $current_session->id)->get();
				foreach($classes as $class)
				{
					$sections = ClassSection::where('class_id', $class->id)->get();
					foreach ($sections as $section)
					{
						$excel->sheet(
							$class->class_code . ' - ' . $section->section_code, 
							function($sheet) use ($current_session, $class, $section) {
								$students = StudentRegistration::join(
									Student::getTableName(), 
									Student::getTableName().'.student_id', '=',
									StudentRegistration::getTableName() . '.id'
								)->join(
									Users::getTableName(),
									Users::getTableName().'.user_details_id', '=',
									StudentRegistration::getTableName() . '.id'
								)->join(
									ClassSection::getTableName(),
									ClassSection::getTableName() . '.class_id', '=',
									Student::getTableName() . '.current_class_id'
								)->leftjoin(House::getTableName(), House::getTableName().'.id', '=', 

StudentRegistration::getTableName().'.house_id')
								->leftjoin(Ethnicity::getTableName(),Ethnicity::getTableName().'.id', '=' 

,StudentRegistration::getTableName().'.ethnicity_id')
								->where(Users::getTableName() . '.role', 'student')
								->where(Student::getTableName() . '.current_session_id', $current_session->id)
								->where(Student::getTableName() . '.current_section_code', $section->section_code)
								->where(ClassSection::getTableName() . '.class_id', $class->id)
								->where(ClassSection::getTableName() . '.section_code', $section->section_code)
								->orderBy('student_name', 'ASC')
								->select(
									'student_name',
									'last_name',
									'username as student_username',
									'dob_in_bs',
									'dob_in_ad',
									'current_address',
									'permanent_address',
									'sex',
									'house_name',
									'ethnicity_name',
									StudentRegistration::getTableName(). '.email',
									'guardian_contact',
									'secondary_contact',
									'unique_school_roll_number',
									'student_id',
									'current_roll_number'
								)->get();

								foreach ($students as $student)
								{
									$relationships = ['father', 'mother'];

									foreach ($relationships as $relationship)
									{
										$parent = StudentGuardianRelation::join(
											StudentRegistration::getTableName(),
											StudentRegistration::getTableName() . '.id', '=',
											StudentGuardianRelation::getTableName() . '.student_id'
										)->join(
											Guardian::getTableName(),
											Guardian::getTableName() . '.id', '=',
											StudentGuardianRelation::getTableName() . '.guardian_id'
										)->join(
											Users::getTableName(),
											Users::getTableName() . '.user_details_id', '=',
											Guardian::getTableName() . '.id'
										)->where(Users::getTableName() . '.role', 'guardian')
										->where('student_id', $student->student_id)
										->where('relationship', $relationship)
										->first();

										$name_header = $relationship . 's_name';
										$username_header = $relationship . 's_username';

										$student->$name_header = $parent ? $parent->guardian_name : '';
										$student->$username_header = $parent ? $parent->username : '';
									}
								}

								$sheet->fromArray($students);
							}
						);
					}
				}
			})->export('xls');
		}
		else
		{
			Session::flash('error-msg', 'current session not selected!!!');
			return Redirect::back();
		}
	}


	public function getImportExcel()
	{
		AccessController::allowedOrNot('student', 'can_import');
		return View::make($this->view . 'import-excel')
									->with('role', $this->role)
									->with('current_user', $this->current_user)
									->with('module_name', $this->module_name);
	}

	/*
	 * Refer: http://www.maatwebsite.nl/laravel-excel/docs
	 * Refer: http://www.maatwebsite.nl/laravel-excel/docs/import
	 * Note: make sure that the excel file contains more than one sheets
	 *			In case there is only one sheet, the library treats each row as a sheet!!! 
	 */
	public function postImportExcel()
	{
		ini_set('max_execution_time', 300); 
		AccessController::allowedOrNot('student', 'can_import');//300 seconds = 5 minutes
		
		$excel_header_map = ['first_name' => 'First Name', 'last_name' =>	'Last Name', 	'roll_no' => 'Roll No',	'house' => 'House',	'ethnicity' => 'Ethnicity',	'current_address' => 'Current Address',	'permanent_address' => 'Permanent Address', 	'sex_malefemale' => 'Sex (Male/Female)', 	'dob_yy_mm_dd' => 'DOB (YY-MM-DD)', 'primary_contact'=>	'Primary Contact', 	'secondary_contact'=>'Secondary Contact', 'email' => 'Email', 'father_name' => 'Father Name', 	'mother_name' =>'Mother Name',	'multiple_children_number' => 'multiple_children_number',	'class' => 'Class Name', 'section_name' =>	'Section Name'];
		
		$reader = Excel::load(Input::file('excel_file'))
										->get();


		/* Do some validation here */
		
		$current_session_id = (int) HelperController::getCurrentSession();

		if($current_session_id == 0)
		{
			die('No session set as current');
		}

		try
		{
			$has_multiple_children = []; //['multiple_number' => 'student_id']
			DB::connection()->getPdo()->beginTransaction();

			foreach($reader as $row)
			{
				foreach($row as $index => $r)
				{
					$row[$index] = trim($r);
					$row[$index] = $row[$index] == '-' ? '' : $row[$index];
				}

				// getting house id
				$house_id = strlen($row['house']) ? House::firstOrCreate(['house_name' => $row['house'], 'house_code' => $row['house'], 'is_active' => 'yes'])->id : NULL;

				// getting ethnicity id
				$ethnicity_id = strlen($row['ethnicity']) ? Ethnicity::firstOrCreate(['ethnicity_name' => $row['ethnicity'], 'ethnicity_code' => $row['ethnicity'], 'is_active' => 'yes'])->id : NULL;

				if(strlen($row['class_name']) == 0 || strlen($row['section_name']) == 0 || strlen($row['first_name']) == 0)
				{
					continue;
				}

				//getting class id
				$class_id = Classes::firstOrNew(['academic_session_id'=> $current_session_id, 'class_name' => $row['class_name'], 'class_code' => $row['class_name'], 'is_active' => 'yes']);

				if((int) $class_id->sort_order == 0)
				{
					$highest_class_sort_order = (int) DB::table(Classes::getTableName())
												->where('academic_session_id', $current_session_id)
												->max('sort_order') + 1;

					$class_id->sort_order =  $highest_class_sort_order;
					$class_id->save();
				}

				$class_id = $class_id->id;

				//gettting section id
				$section_id = Section::firstOrCreate(['section_name' => $row['section_name'], 'section_code' => $row['section_name'], 'is_active' => 'yes']);
				$section_code = $section_id->section_code;
				$section_id = $section_id->id;

				//map class and section
				ClassSection::firstOrCreate(['class_id' => $class_id, 'section_code' => $section_code]);

				$data_to_store_in_student_registration_table = [];
				
				$data_to_store_in_student_registration_table['student_name'] = $row['first_name'];
				
				$data_to_store_in_student_registration_table['last_name'] = $row['last_name'];
				
				$data_to_store_in_student_registration_table['dob_in_bs'] = $row['dob_yy_mm_dd'];

				$data_to_store_in_student_registration_table['dob_in_ad'] = strlen($row['dob_yy_mm_dd']) ? (new DateConverter)->bs2ad($row['dob_yy_mm_dd']) : NULL;

				$data_to_store_in_student_registration_table['current_address'] = $row['current_address'];

				$data_to_store_in_student_registration_table['permanent_address'] = $row['permanent_address'];

				$data_to_store_in_student_registration_table['sex'] = strtolower($row['sex_malefemale']);

				$data_to_store_in_student_registration_table['email'] = $row['email'];

				$data_to_store_in_student_registration_table['guardian_contact'] = $row['primary_contact'];

				$data_to_store_in_student_registration_table['secondary_contact'] = $row['secondary_contact'];

				$data_to_store_in_student_registration_table['registered_session_id'] = $current_session_id;

				$data_to_store_in_student_registration_table['registered_class_id'] = $class_id;

				$data_to_store_in_student_registration_table['registered_section_code'] = $section_code;

				$data_to_store_in_student_registration_table['photo'] = '';

				$data_to_store_in_student_registration_table['house_id'] = $house_id;

				$data_to_store_in_student_registration_table['ethnicity_id'] = $ethnicity_id;

				$data_to_store_in_student_registration_table['unique_school_roll_number'] = '';

				$data_to_store_in_student_registration_table['is_active'] = 'yes';

				$student_id = StudentRegistration::create($data_to_store_in_student_registration_table)->id;

				//unset($data_to_store_in_student_registration_table) ;

				$data_to_store_in_users_table = [];

				do {
					$data_to_store_in_users_table['username'] = STUDENT_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
				} while(Users::where('username', $data_to_store_in_users_table['username'])->first());

				$data_to_store_in_users_table['email'] = $row['email'];

				$data_to_store_in_users_table['password'] = Hash::make(DEFAULT_PASSWORD);

				$data_to_store_in_users_table['name'] = $row['first_name'].' '.$row['last_name'];

				$data_to_store_in_users_table['contact'] = $row['primary_contact'];

				$data_to_store_in_users_table['role'] = 'student';

				$data_to_store_in_users_table['user_details_id'] = $student_id;

				$data_to_store_in_users_table['confirmation'] = '';

				$data_to_store_in_users_table['confirmation_count'] = 0;

				$data_to_store_in_users_table['is_blocked'] = 'no';

				$data_to_store_in_users_table['is_active'] = 'yes';

				Users::create($data_to_store_in_users_table);

				unset($data_to_store_in_users_table);

				$data_to_store_in_student_table = [];
				$data_to_store_in_student_table['current_session_id'] = $current_session_id;
				$data_to_store_in_student_table['current_class_id'] = $class_id;
				$data_to_store_in_student_table['current_section_code'] = $section_code;
				$data_to_store_in_student_table['current_roll_number'] = $row['roll_no'];
				$data_to_store_in_student_table['is_active'] = 'yes';
				$data_to_store_in_student_table['student_id'] = $student_id;
				Student::firstOrCreate($data_to_store_in_student_table);
				unset($data_to_store_in_student_table);


				//if multiple children parent already created so no need to create
				if((int) $row['multiple_children_number'])
				{
					if(in_array($row['multiple_children_number'], array_keys($has_multiple_children)))
					{
						/*$has_multiple_children[$row['multiple_children_number']][] = $student_id;*/
						if($has_multiple_children[$row['multiple_children_number']]['father'])
						{
							StudentGuardianRelation::firstOrCreate(['guardian_id' => $has_multiple_children[$row['multiple_children_number']]['father'], 'student_id' => $student_id, 'relationship' => 'father']);	
						}

						if($has_multiple_children[$row['multiple_children_number']]['mother'])
						{
							StudentGuardianRelation::firstOrCreate(['guardian_id' => $has_multiple_children[$row['multiple_children_number']]['mother'], 'student_id' => $student_id, 'relationship' => 'mother']);	
						}
						
					}
					else
					{
						$return = $this->createGuardianFromImport($row, $student_id);
						$has_multiple_children[$return['index']] = $return['value'];
					}	
				}
				else
				{
					$this->createGuardianFromImport($row, $student_id);
				}
			}
			Session::flash('success-msg', 'Students successfully created');
			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
			
		}
		
		return Redirect::back();
	}

	public function postChangePassword($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_change_password');
		$input_data = Input::all();
		if ($this->current_user->role == 'superadmin' || $this->details_id == $id)
		{
			$result = Users::where('user_details_id', $id)
				->where('role', 'student')
			  ->where('is_active', 'yes')
			  ->first();
			if ($result && Hash::check($input_data['old_password'], $result->password))
			{
				if ($input_data['new_password'] == $input_data['new_password_confirm'])
				{
					$result->password = Hash::make($input_data['new_password']);
					$result->save();
					Session::flash('success-msg', 'Password Changed');
					//die('here');
					return Redirect::back();
				}
				else
				{
					Session::flash('error-msg', 'Confirm Password Mismatch');
					//die('not here');
					return Redirect::back();
				}
			}
			else
			{
				Session::flash('error-msg', 'Incorrect password');
				//die('agin here here');
				return Redirect::back();
			}
		}
		else
		{
			return Response('Unauthorized', 500);
		}
	}

	

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////// Private Functions ///////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	private function validateEmailInUsersTable($email, $id = 0)
	{
		$result = array('status' => 'error', 'data' => array());
		$rule = array('email' => array('required', 'unique:users,email'));

		if($id)
		{
			$user_details_id = User::where('user_details_id', $id)
									->where('role', 'student')
									->pluck('id');
			$rule['email'][1] = $rule['email'][1].",".$user_details_id;
		}

		$validate = Validator::make(array('email' => $email), $rule);

		if($validate->fails())
		{
			$result['status'] = 'error';
			$result['data'] = $validate->messages();
		}
		else
		{
			$result['status'] = 'success';
		}

		return $result;
	}

	private function assignValues($modelname, $data, $update = false)
	{
		$tablename = $modelname::getTableName();
		$columns = Schema::getColumnListing($tablename);
		foreach($columns as $column)
		{
			$dataToStore[$column] = isset($data[$column]) ? $data[$column] : null;
		}

		if(!$update)
			$dataToStore['unique_school_roll_number'] = str_random(10);

		return $dataToStore;
	}

	private function assignDataToStoreInUserTable($data, $update = false)
	{

			$dataToStoreInUserTable = array();
			$dataToStoreInUserTable['name'] = $data['student_name'];
			$dataToStoreInUserTable['contact'] = $data['guardian_contact'];
			$dataToStoreInUserTable['address'] = $data['current_address'];
			$dataToStoreInUserTable['email'] = $data['email'];
			$dataToStoreInUserTable['role'] = 'student';
				
			if($update)
			{
				$dataToStoreInUserTable['id'] = User::where('user_details_id', $data['id'])
													->where('role', 'student')
													->pluck('id');
			}
			else
			{
				//$dataToStoreInUserTable['username'] = $data['username'];

				do {
					$dataToStoreInUserTable['username'] = STUDENT_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
				} while(Users::where('username', $dataToStoreInUserTable['username'])->first());
				
				$dataToStoreInUserTable['password'] = $data['password'];
				$dataToStoreInUserTable['confirm_password'] = $data['confirm_password'];
				$dataToStoreInUserTable['user_details_id'] = 0; //this will be changed later
				$dataToStoreInUserTable['is_blocked'] = 'no';
				$dataToStoreInUserTable['confirmation'] = '';
				$dataToStoreInUserTable['confirmation_count'] = 0;
				$dataToStoreInUserTable['is_active'] = 'yes';	
			}
			
			return $dataToStoreInUserTable;
	}

	private function storeOrUpdateInStudentTable($data, $student_id, $update = false)
	{
		$dataToStore = array();

		$dataToStore['current_session_id'] = $data['registered_session_id'];
		$dataToStore['current_class_id'] = $data['registered_class_id'];
		$dataToStore['current_section_code'] = $data['registered_section_code'];
		if (isset($data['current_roll_number']) && $data['current_roll_number'])
		{
			$dataToStore['current_roll_number'] = $data['current_roll_number'];
		}
		else
		{
			$dataToStore['current_roll_number'] = '';
		}
		//die($dataToStore['current_roll_number']);
		$dataToStore['student_id'] = $student_id;

		try
		{
			if($update)
			{
				//need session id
				$dataToStore['current_session_id'] = $data['current_session_id'];
				$dataToStore['current_class_id'] = $data['current_class_id'];
				$dataToStore['current_section_code'] = $data['current_section_code'];

				$this->updateInDatabase($dataToStore, array(
						array('field' => 'student_id', 'operator' => '=', 'value' => $student_id), 
						array('field' => 'current_session_id', 'operator' => '=', 'value' => $data['old_current_session_id']), 
						array('field' => 'current_class_id', 'operator' => '=', 'value' => $data['old_current_class_id']), 
						array('field' => 'current_section_code', 'operator' => '=', 'value' => $data['old_current_section_code'])
						), 'Student');
			}
			else
			{
				//dd($dataToStore);
				$dataToStore['is_active'] = 'yes';
				//$dataToStore['current_roll_number'] = 0;
				$this->storeInDatabase($dataToStore, 'Student');
			}
			
			$success = true;
			$msg = 'Store/Update successful';
		}
		catch(Exception $e)
		{
			// DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		return array(
			'success' => $success,
			'msg'			=> $msg,
			'param'		=> $student_id
		);
		
	}

	private function createStudent($data)
	{
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$dataToStoreInStudentTable = array();
		$dataToStoreInUserTable = array();

		if (isset($data['dob_in_ad']) && $data['dob_in_ad'])
		{
			$data['dob_in_bs'] = (new DateConverter)->ad2bs($data['dob_in_ad']);	
		}
		else if (isset($data['dob_in_bs']) && $data['dob_in_bs'])
		{
			$data['dob_in_ad'] = (new DateConverter)->bs2ad($data['dob_in_bs']);	
			if (!$data['dob_in_ad']) {
				$data['dob_in_ad'] = '';
			}
		}
		else
		{
			$data['dob_in_ad'] = '';	
			$data['dob_in_bs'] = '';	
		}

		/*
		/ Task of Validating
		/
		/
		*/
		$dataToStoreInStudentTable = $this->assignValues('StudentRegistration', $data);
		$dataToStoreInStudentTable['current_roll_number'] = $data['current_roll_number'];

		
		$dataToStoreInStudentTable['current_session_id'] = $data['registered_session_id'];
		$dataToStoreInStudentTable['current_class_id'] = $data['registered_class_id'];
		$dataToStoreInStudentTable['current_section_code'] = $data['registered_section_code'];
		
		$result = $this->validateInput($dataToStoreInStudentTable, false, 'StudentRegistration');

		if($result['status'] == 'error')
		{
			return array(
							'success'	=> false,
							'msg'	=> (string)$result['data']
						);
			// Session::flash('error-msg', ConfigurationController::errorMsg('some validation error has occured'));
			// return Redirect::route($this->module_name.'-create-get')
			// 			->withInput()
			// 			->with('errors', $result['data']);
		}
		

		$dataToStoreInUserTable = $this->assignDataToStoreInUserTable($data);
		$result = $this->validateInput($dataToStoreInUserTable, false, 'Users');

		if($result['status'] == 'error')
		{
			return array(
							'success'	=> false,
							'msg'	=> (string)$result['data']
						);
			// Session::flash('error-msg', ConfigurationController::errorMsg('some validation error has occured'));
			// return Redirect::route($this->module_name.'-create-get')
			// 			->withInput()
			// 			->with('errors', $result['data']);
		}	

		/*
		/ 
		/ Storing begins
		/
		*/
		try
		{
			//DB::connection()->getPdo()->beginTransaction();
			//upload file here

			// $photo = Input::hasFile('photo') ? Input::file('photo') : '';
				
			$dataToStoreInStudentTable['photo'] = '';
			$id = $this->storeInDatabase($dataToStoreInStudentTable, 'StudentRegistration'); //student_id

			$dataToStoreInUserTable['user_details_id'] = $id;
			$dataToStoreInUserTable['password'] = Hash::make($data['password']);
			$dataToStoreInUserTable['is_active'] = 'yes';
			$this->storeInDatabase($dataToStoreInUserTable, 'Users');

			$this->storeOrUpdateInStudentTable($data, $id);

						
			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
			// DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			// DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		return array(
							'success' => $success,
							'msg'			=> $msg,
							'param'		=> $param
						);
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////// These are ajax functions /////////////////////////////////////////////////////////////////////////////////////////////////
	public function ajaxGetACtiveClasses()
	{

		$session_id = Input::get('session_id');
		$condition = array();
		$condition['field'] = 'academic_session_id';
		$condition['operator'] = '=';
		$condition['value'] = $session_id;

		return HelperController::generateSelectList('Classes', 'class_name', 'id', 'class_id', '', array($condition));
	}

	public function ajaxGetACtiveSections()
	{
		$class_id = Input::get('class_id');
		$condition = array();
		$condition['field'] = 'class_id';
		$condition['operator'] = '=';
		$condition['value'] = $class_id;

		return HelperController::generateSelectList('ClassSection', 'section_code', 'section_code', 'section_code', '', array($condition));
	}

	public function ajaxDocumentList()
	{
		if(!Input::has('student_id'))
		{
			return 'invalid request';
		}
		
		$id = Input::get('student_id');

		$documents = DB::table(StudentDocument::getTableName())
								->join(DownloadManager::getTableName(), DownloadManager::getTableName().'.id', '=', StudentDocument::getTableName().'.download_id')
								->select(array(StudentDocument::getTableName().'.*', 'filename', 'google_file_id'))
								->where(StudentDocument::getTableName().'.is_active', 'yes')
								->where('student_id', $id)
								->get();
		foreach($documents as $key => $child) {
			$documents[$key]->download_link = URL::route('download-manager-backend-file-download', [$child->download_id, $child->google_file_id]);
		}

		$guardians = DB::table(StudentGuardianRelation::getTableName())
										->join(Guardian::getTableName(), Guardian::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.guardian_id')
										->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', Guardian::getTableName().'.id')
										->where(StudentGuardianRelation::getTableName() . '.student_id', $id)
										->where(Users::getTableName() . '.role', 'guardian')
										->select('guardian_name', 'username')
										->get();

		return View::make($this->view . 'document-list')
								->with('role', $this->role)
								->with('current_user', $this->current_user)
								->with('documents', $documents)
								->with('guardians', $guardians);

	}

	public function ajaxGetFee()
	{
		if(!Input::has('student_id'))
		{
			return 'invalid request';
		}

		$current_session_id = Input::get('academic_session_id', HelperController::getCurrentSession());

		$student = DB::table(Student::getTableName())
					->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'current_class_id')
					->where(Student::getTableName().'.student_id', Input::get('student_id'))
					->where(Student::getTableName().'.current_session_id', $current_session_id)
					->first();
		
		if(!StudentRegistration::find(Input::get('student_id')))
		{
			return View::make($this->view.'payment')
					->with('status', 'error')
					->with('msg', 'Student Not Registered');
		}
		elseif(!$student)
		{
			return View::make($this->view.'payment')
					->with('status', 'error')
					->with('msg', 'Student Not Registered For the current session');
		}
		else
		{
			// we show all records
			$payments = BillingInvoice::where('related_user_id', Input::get('student_id'))
							->where('related_user_group', 'student')
							->where('is_final', 'yes')
							->where('is_cleared', '!=', 'yes')
							->orderBy('year_in_bs', 'desc')
							->orderBy('month_in_bs', 'desc')
							->get();

			return View::make($this->view . 'payment')
							->with('status', 'success')
							->with('student', $student)
							->with('academic_session_id', $current_session_id)
							->with('payments', $payments)
							->with('current_session_id', $current_session_id);	
		}
	}

	public function ajaxExamReport()
	{
		$student_id = Input::get('student_id', 0);
		$session_id = Input::get('session_id', HelperController::getCurrentSession());

		$unpaid_fees = BillingInvoice::where('related_user_id', Input::get('student_id'))
							->where('related_user_group', 'student')
							->orderBy('year_in_bs', 'DESC')
							->orderBy('month_in_bs', 'DESC')
							->where('is_final', 'yes')
							->where('is_cleared', '!=', 'yes')
							->get();

		$exams = Report::where('student_id', $student_id)
						->where('session_id', $session_id)
						->get();

		$final_exams = FinalReport::where('student_id', $student_id)
									->where('session_id', $session_id)
									->get();
		/*
		join(Report::getTableName(), Report::getTableName().'.student_id', '=', ExamMarks::getTableName().'.student_id')
						 ->join(ExamDetails::getTableName(), ExamDetails::getTableName().'.subject_id', '=', ExamMarks::getTableName().'.subject_id')
						 ->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', ExamMarks::getTableName().'.student_id')
						 ->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', Report::getTableName().'.exam_id')
						 ->where(ExamMarks::getTableName().'.student_id', Input::get('student_id', 0))
						 ->where(ExamDetails::getTableName().'.is_active', 'yes')
						 ->groupBy(ExamMarks::getTableName().'.exam_id')
						 ->select(ExamMarks::getTableName().'.marks', ExamDetails::getTableName().'.pass_marks', ExamDetails::getTableName().'.full_marks', Report::getTableName().'.*', StudentRegistration::getTableName().'.student_name', ExamConfiguration::getTableName().'.exam_name')
						 ->get();*/

		$exam_config = json_decode(File::get(REPORT_CONFIG_FILE));

		return View::make($this->view . 'exams')
								->with('exams', $exams)
								->with('final_exams', $final_exams)
								->with('exam_config', $exam_config)
								->with('unpaid_fees', $unpaid_fees)
								->with('role', $this->role);
	
	}

	public function ajaxExtraActivities()
	{
		$student_id = Input::get('student_id', 0);

		$extra_activities = DB::table(ExtraActivity::getTableName())
								->join(Events::getTableName(), Events::getTableName().'.id', '=', ExtraActivity::getTableName().'.event_id')
								->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', ExtraActivity::getTableName().'.student_id')
								->select(
												array(
													ExtraActivity::getTableName().'.*',
													Events::getTableName().'.title',
													Events::getTableName().'.from_ad',
													Events::getTableName().'.from_bs',
													Events::getTableName().'.to_ad',
													Events::getTableName().'.to_bs',
												)
								)
								->where(ExtraActivity::getTableName().'.is_active', 'yes')
								->where(ExtraActivity::getTableName().'.student_id', $student_id)
								->get();
		
		return View::make($this->view . 'extra-activities')
								->with('extra_activities', $extra_activities);
	}

	public function ajaxLibrary()
	{
		$student_id = Input::get('student_id', 0);

		$books = DB::table(BooksAssigned::getTableName())
								->join(StudentRegistration::getTableName(),  BooksAssigned::getTableName().'.student_id','=',StudentRegistration::getTableName().'.id')
								->join(Books::getTableName(),BooksAssigned::getTableName().'.books_id', '=', Books::getTableName().'.id')
								->select(
											BooksAssigned::getTableName().'.*',
											StudentRegistration::getTableName().'.student_name',
											Books::getTableName().'.title as book_title'
								)
								->where(BooksAssigned::getTableName().'.is_active', 'yes')
								->where(StudentRegistration::getTableName().'.id', $student_id)
								->get();

		return View::make($this->view . 'library')
								->with('books', $books);
	}

	public function ajaxAttendanceSelectMonth()
	{
		return View::make($this->view . 'ajax.attendance-select-month')
								->with('student_id', Input::get('student_id', 0));
	}

	public function ajaxAttendanceGetMonth() 
	{
		$student_id = Input::get('student_id', 0);
		$month = Input::get('month', 0);

		$academic_session_id = HelperController::getCurrentSession();
		$student = Student::where('current_session_id', $academic_session_id)
			->where('student_id', $student_id)
			->first();

		if (!$student) 
		{
			return 'Student not registered for current session';
		}

		$class = Classes::find($student->current_class_id);
		$academic_session = AcademicSession::find($academic_session_id);
		
		$session_start_date = (CALENDAR == 'BS') ? $academic_session->session_start_date_in_bs : $academic_session->session_start_date_in_ad;
		$session_end_date = (CALENDAR == 'BS') ? $academic_session->session_end_date_in_bs : $academic_session->session_end_date_in_ad;
		
		$start_date_array = explode('-', $session_start_date);
		$end_date_array = explode('-', $session_end_date);

		if (count($start_date_array) != 3 || count($end_date_array) != 3) {
			return 'ERROR!!!';
		}

		$date_converter = new DateConverter;
		$attendance_records = array(); 
		for ($day = 1; ; $day++) {
			if($month > $start_date_array[1]) {
				$year = $start_date_array[0];
			} elseif ($month == $start_date_array[1]) {
				$year = ($day >= $start_date_array[2]) ?
														$start_date_array[0] :
														$end_date_array[0];
				
			}	else {
				$year = $end_date_array[0];
			}

			$date = $year . '-' . $month . '-' . $day;
			$date_ad = (CALENDAR == 'BS') ? $date_converter->bs2ad($date) : $date;

			if (!HelperController::validateDate($date_ad, $format = 'Y-m-d'))
			{
				break;
			}

			$filename = app_path().'/modules/attendance/assets/attendance-records/'.$date_ad .':'.$student->current_class_id.':'.$student->current_section_code.'.csv';

			if(File::exists($filename) && File::isFile($filename)) {
				$attendance_contents = File::get($filename);
				$temp = HelperController::csvToArray($filename, $search_for_key = 0, $search_for_value = $student_id);
				if(CALENDAR == 'BS') {
					$date_string = HelperController::formatNepaliDate($date);
				} else {
					$date_string = DateTime::createFromFormat('Y-m-d', $date)->format('d F Y');
				}
				if (count($temp))
				{
					$temp[] =  $date_string;
					$attendance_records[] = $temp;
				}
			}
		}

		return View::make($this->view . 'ajax.attendance-show-month')
								->with('attendance_records', $attendance_records);
	}
	
	public function createGuardianFromImport($row, $student_id)
	{
		$father_id = 0;
		$mother_id = 0;

		$data_to_store_in_guardian_table = [];
		if(strlen($row['father_name']))
		{
			$data_to_store_in_guardian_table['guardian_name'] = $row['father_name']	;
			$data_to_store_in_student_registration_table['is_active'] = 'yes';
			$father_id = Guardian::create($data_to_store_in_guardian_table)->id;

			StudentGuardianRelation::firstOrCreate(['student_id' => $student_id, 'guardian_id' => $father_id, 'relationship' => 'Father', 'is_active' => 'yes']);

			$data_to_store_in_users_table = [];

			do {
				$data_to_store_in_users_table['username'] = GUARDIAN_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
			} while(Users::where('username', $data_to_store_in_users_table['username'])->first());

			$data_to_store_in_users_table['email'] = '';

			$data_to_store_in_users_table['password'] = Hash::make(DEFAULT_PASSWORD);

			$data_to_store_in_users_table['name'] = $row['father_name'];

			$data_to_store_in_users_table['contact'] = $row['primary_contact'];

			$data_to_store_in_users_table['role'] = 'guardian';

			$data_to_store_in_users_table['user_details_id'] = $father_id;

			$data_to_store_in_users_table['confirmation'] = '';

			$data_to_store_in_users_table['confirmation_count'] = 0;

			$data_to_store_in_users_table['is_blocked'] = 'no';

			$data_to_store_in_users_table['is_active'] = 'yes';

			Users::create($data_to_store_in_users_table);

			unset($data_to_store_in_users_table);					
		}

		if(strlen($row['mother_name']))
		{
			$data_to_store_in_guardian_table['guardian_name'] = $row['mother_name']	;
			$data_to_store_in_student_registration_table['is_active'] = 'yes';
			$mother_id = Guardian::create($data_to_store_in_guardian_table)->id;

			StudentGuardianRelation::firstOrCreate(['student_id' => $student_id, 'guardian_id' => $mother_id, 'relationship' => 'Mother', 'is_active' => 'yes']);

			$data_to_store_in_users_table = [];

			do {
				$data_to_store_in_users_table['username'] = GUARDIAN_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
			} while(Users::where('username', $data_to_store_in_users_table['username'])->first());

			$data_to_store_in_users_table['email'] = '';

			$data_to_store_in_users_table['password'] = Hash::make(DEFAULT_PASSWORD);

			$data_to_store_in_users_table['name'] = $row['mother_name'];

			$data_to_store_in_users_table['contact'] = $row['primary_contact'];

			$data_to_store_in_users_table['role'] = 'guardian';

			$data_to_store_in_users_table['user_details_id'] = $mother_id;

			$data_to_store_in_users_table['confirmation'] = '';

			$data_to_store_in_users_table['confirmation_count'] = 0;

			$data_to_store_in_users_table['is_blocked'] = 'no';

			$data_to_store_in_users_table['is_active'] = 'yes';

			Users::create($data_to_store_in_users_table);

			unset($data_to_store_in_users_table);
		}

		//$has_multiple_children = [];
		if($row['multiple_children_number'])
		{
			return ['index' => $row['multiple_children_number'],  'value' => ['father' => $father_id, 'mother' => $mother_id]];
		}
	}
}