<?php

class GuardianController extends BaseController
{
	protected $view = 'guardian.views.';

	protected $model_name = 'Guardian';

	protected $module_name = 'guardian';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'guardian_name',
										'alias'			=> 'Guardian Name'
									),
									array
									(
										'column_name' 	=> 'student_name',
										'alias'			=> 'Student Name'
									),
									array
									(
										'column_name' 	=> 'class_section',
										'alias'			=> 'Class Section'
									),
									array
									(
										'column_name' 	=> 'contact_number',
										'alias'			=> 'Contact number'
									)
								 );
								 
								 
		public function getListView()
	
		{
		
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'list')
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

	public function modalEditGuardianDetails($id)
	{
		AccessController::allowedOrNot('guardian', 'can_edit');
		
		///// TODO///
		// 1. store in Guardian table
		// 2. store in Guardian student relation table
		// 3. store in Users table
		////////////////
		
		$data = Input::all();
		if (isset($data['dob_in_ad']) && $data['dob_in_ad']) {
			$data['dob_in_bs'] = $data['dob_in_ad'] ? (new DateConverter)->ad2bs($data['dob_in_ad']) : '';
		} else {
			$data['dob_in_bs'] = '';
		}
		
		$dataToUpdateInGuardianTable = array();

		try
		{
			$this->updateInDatabase($data);
			Session::flash('success-msg', 'Details successfully updated');
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', 'Something went wrong. Please try again');
			echo $e->getMessage();
		}
		
		return Redirect::back();
	}

	public function validateStudentUsernames($data)
	{
			////////// A student must be selected /////////////
		//////////////////////
		// for Guardian Student Relation table /////
		////////////////////////
		if(!isset($data['student_username']))
		{
			return array(	'status'	=> 'error', 
										'msg'			=> 'No student selected');
		}

		$invalid_student_usernames = array();
		foreach($data['student_username'] as $key => $student_username)
		{
			if ( !Users::where('username', $student_username)->where('role', 'student')->first() )
			{
				$invalid_student_usernames[] = $student_username;
			}
			else
			{
				$data['student_username'][$key] = $student_username;
			}
		}

		if(count($invalid_student_usernames))
		{
			return array(	'status'	=> 'error', 
										'msg'			=> 'Invalid student username(s): '. str_replace(array('"','[',']'),'',json_encode($invalid_student_usernames)));
		}

		$result['status'] = 'success';
		$result['data'] = [];
		foreach($data['student_username'] as $student_username)
		{
			$result['data'][] = 
											Users::where('username', $student_username)
														->where('role', 'student')
														->first()
														->user_details_id;
													
	
		}
		$result['data'] = array_unique(
														$result['data']
													);
		return $result;
	}

	public function postCreateView()
	{
		///// TODO///
		// 1. store in Guardian table
		// 2. store in Guardian student relation table
		// 3. store in Users table
		////////////////
		AccessController::allowedOrNot('guardian', 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();
		if (isset($data['dob_in_ad']) && $data['dob_in_ad']) {
			$data['dob_in_bs'] = $data['dob_in_ad'] ? (new DateConverter)->ad2bs($data['dob_in_ad']) : '';
		} else {
			$data['dob_in_bs'] = '';
		}

		$dataToStoreInGuardianTable = array();
		$dataToStoreInGuardianStudentRelationTable = array();
		$dataToStoreInUsersTable = array();

		// validate username(s) of student given
		$result = $this->validateStudentUsernames($data);
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate($result['msg']));
			return Redirect::route($this->module_name.'-create-get')
						->withInput();
		}
		else
		{
			$data['student_id'] = $result['data'];
		}
		
				
		//////////////////////////////////////////////////

		///////////////////////////
		// for guardian table //////
		$dataToStoreInGuardianTable = $this->assignValues($this->model_name, $data);
		$result = $this->validateInput($dataToStoreInGuardianTable);
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}

		////////////////////

		if(isset($data['eton_allow_backend_access_checkbox']))
		{
			$dataToStoreInUsersTable = $this->assignDataToStoreInGuardianTable($data);
			$result = $this->validateInput($dataToStoreInUsersTable, false, 'Users');
		
			if($result['status'] == 'error')
			{
				Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
				return Redirect::route($this->module_name.'-create-get')
							->withInput()
							->with('errors', $result['data']);
			}

		}
		else
		{
			$result = $this->validateEmailInUsersTable($data['email'], $id = 0);
			if($result['status'] == 'error')
			{
				Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
				return Redirect::route($this->module_name.'-create-get')
							->withInput()
							->with('errors', $result['data']);
			}	
		}

		////////////////////////////////////////////
		///////// storing begins //////////////////

		try
		{
			$photo = Input::hasFile('photo') ? Input::file('photo') : '';
			
			if($photo !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/guardian/assets/images', array('jpg', 'jpeg', 'png'), 10485760000);
				$result = $upload->uploadFile($photo);
				$result = json_decode($result, 1);
				if($result['status'] == 'error')
				{
					
					throw new Exception($result['message']);
				}
				else
				{
					$dataToStoreInGuardianTable['photo'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToStoreInGuardianTable['photo'] = '';
			}

			$id = $this->storeInDatabase($dataToStoreInGuardianTable);	//this is guardian id

			/////////
			//storing in guardian student relation table
			////////////
			StudentGuardianRelation::where('guardian_id', $id)->delete();
			
			foreach($data['student_id'] as $key => $student_id)
			{
				$dataToStoreInGuardianStudentRelationTable['is_active'] = 'yes';
				$dataToStoreInGuardianStudentRelationTable['guardian_id'] = $id;
				$dataToStoreInGuardianStudentRelationTable['student_id'] = $student_id;
				$dataToStoreInGuardianStudentRelationTable['relationship'] = $data['relationship'][$key];
				$this->storeInDatabase($dataToStoreInGuardianStudentRelationTable, 'StudentGuardianRelation');
			}

			/////////////////
			//////storing in users table
			///////////
			if(isset($data['eton_allow_backend_access_checkbox']))
			{
				$dataToStoreInUsersTable['user_details_id'] = $id;
				$dataToStoreInUsersTable['password'] = Hash::make($data['password']);
				$this->storeInDatabase($dataToStoreInUsersTable, 'Users');	
			}
			
			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot('guardian', 'can_edit');
		
		///// TODO///
		// 1. store in Guardian table
		// 2. store in Guardian student relation table
		// 3. store in Users table
		////////////////
		$success = false;
		$msg = '';
		$param = array('id' => $id);

		$data = Input::all();
		if (isset($data['dob_in_ad']) && $data['dob_in_ad']) {
			$data['dob_in_bs'] = $data['dob_in_ad'] ? (new DateConverter)->ad2bs($data['dob_in_ad']) : '';
		} else {
			$data['dob_in_bs'] = '';
		}
		
		$dataToUpdateInGuardianTable = array();
		$dataToUpdateInUsersTable = array();
		$dataToStoreInGuardianStudentRelationTable = array();

		// validate student username(s)
		$result = $this->validateStudentUsernames($data);
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate($result['msg']));
			return Redirect::route($this->module_name.'-create-get')
						->withInput();
		}
		else
		{
			$data['student_id'] = $result['data'];
		}

		///////////////////////////
		// for guardian table //////
		$dataToUpdateInGuardianTable = $this->assignValues($this->model_name, $data);
		$result = $this->validateInput($dataToUpdateInGuardianTable, true);
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-edit-get', array($id))
						->withInput()
						->with('errors', $result['data']);
		}

		$dataToUpdateInUsersTable = $this->assignDataToStoreInGuardianTable($data, true);
		$result = $this->validateEmailInUsersTable($data['email'], $id);
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-edit-get', array($id))
						->withInput()
						->with('errors', $result['data']);
		}

		////////////////////////////////////////////
		///////// storing begins //////////////////

		try
		{
			$photo = Input::hasFile('photo') ? Input::file('photo') : '';
			
			if($photo !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/guardian/assets/images', array('jpg', 'jpeg', 'png'), 10485760000, $data['original_photo']);

				$result = $upload->uploadFile($photo);
				$result = json_decode($result, 1);
				
				if($result['status'] == 'error')
				{
					
					throw new Exception($result['message']);
				}
				else
				{
					$dataToUpdateInGuardianTable['photo'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToUpdateInGuardianTable['photo'] = $data['original_photo'];
			}


			$id = $this->updateInDatabase($dataToUpdateInGuardianTable);	//this is guardian id

			/////////
			//storing in guardian student relation table
			////////////
			
			/////////////////
			//////storing in users table
			///////////
			
			$this->updateInDatabase($dataToUpdateInUsersTable, array(array('field' => 'user_details_id', 'operator' => '=', 'value' => $id), array('field' => 'role', 'operator' => '=', 'value' => 'guardian')), 'Users');	

			/////////
			//storing in guardian student relation table
			////////////
			StudentGuardianRelation::where('guardian_id', $id)->delete();
			foreach($data['student_id'] as $key => $student_id)
			{
				$dataToStoreInGuardianStudentRelationTable['is_active'] = 'yes';
				$dataToStoreInGuardianStudentRelationTable['guardian_id'] = $id;
				$dataToStoreInGuardianStudentRelationTable['student_id'] = $student_id;
				$dataToStoreInGuardianStudentRelationTable['relationship'] = $data['relationship'][$key];
				$this->storeInDatabase($dataToStoreInGuardianStudentRelationTable, 'StudentGuardianRelation');
			}
			
			
			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id;
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}


		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}

	public function postChangePassword($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_change_password');
		$input_data = Input::all();
		/*echo $this->current_user->id;
		die();*/
		if ($this->current_user->role == 'superadmin' || $this->details_id == $id)
		{
			//die('here')
			$result = Users::where('user_details_id', $id)
				->where('role', 'guardian')
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

	///////////////////////////////////////////////////////////////////////////////////////////////
	/////////// these are private functions /////////////////////////
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

	private function validateEmailInUsersTable($email, $id = 0)
	{
		$result = array('status' => 'error', 'data' => array());
		$rule = array('email' => array('unique:users,email'));

		if($id)
		{
			$user_details_id = User::where('user_details_id', $id)
									->where('role', 'guardian')
									->pluck('id');
			$rule['email'][0] = $rule['email'][0].",".$user_details_id;
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

	private function assignDataToStoreInGuardianTable($data, $update = false)
	{

			$dataToStoreInUserTable = array();
			$dataToStoreInUserTable['name'] = $data['guardian_name'];
			$dataToStoreInUserTable['contact'] = $data['primary_contact'];
			$dataToStoreInUserTable['address'] = $data['current_address'];
			$dataToStoreInUserTable['email'] = $data['email'];

			if($update)
			{
				$dataToStoreInUserTable['id'] = User::where('user_details_id', $data['id'])
													->where('role', 'guardian')
													->pluck('id');
			}
			else
			{
				$dataToStoreInUserTable['role'] = $data['role'];
				// $dataToStoreInUserTable['username'] = $data['username'];
				do {
					$dataToStoreInUserTable['username'] = GUARDIAN_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
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

	public function postDelete()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$model = new $this->model_name;
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			try
			{
				DB::connection()->getPdo()->beginTransaction();
					$image = $record->image;
					Users::where('user_details_id', $id)
								->where('role', 'guardian')
								->delete();
					@unlink(app_path().'/modules/guardian/assets/images/'.$image);
					$record->delete();
					Session::flash('success-msg', 'Delete Successful');
				DB::connection()->getPdo()->commit();

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


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                          Ajax Functions
	//
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function ajaxSearchStudents()
	{
		$helper = new GuardianHelperController;

		$input = Input::all();

		$input['unique_school_roll_number'] = isset($input['unique_school_roll_number']) ? $input['unique_school_roll_number'] : '';
		$input['student_name'] = isset($input['student_name']) ? $input['student_name'] : '';
		$input['class_id'] = isset($input['class_id']) ? $input['class_id'] : 0;

		$model = $this->model_name;
		$model = new $model;

		$data = $model->ajaxSearchStudents($input);

		$data = $helper->htmlAjaxSearchStudents($data);

		return $data;
	}

	public function getImportExcel()
	{
		AccessController::allowedOrNot('guardian', 'can_import');
		return View::make($this->view . 'import-excel')
									->with('role', $this->role)
									->with('current_user', $this->current_user)
									->with('module_name', $this->module_name);
	}

	/*
	 * Refer: http://www.maatwebsite.nl/laravel-excel/docs
	 * Refer: http://www.maatwebsite.nl/laravel-excel/docs/import
	 * Note: make sure that the excel file contains no more than one sheets
	 *			In case there is only one sheet, the library treats each row as a sheet!!! 
	 */
	public function postImportExcel()
	{
		AccessController::allowedOrNot('guardian', 'can_import');
		ini_set('max_execution_time', 3000);
		// $file = new Symfony\Component\HttpFoundation\File\File(Input::file('excel_file'));
		// dd($file->getMimeType());
		Config::set('excel::import.startRow', 2);
		$validator = Validator::make(
			array(
				'excel_file' => Input::hasFile('excel_file') ? Input::file('excel_file')->getClientOriginalExtension() : '',
			),
			array(
				'excel_file' => array('required', 'in:xls,xlsx'),
				//'excel_file' => array('required', 'mimes:xls,xlsx'),
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

		try
		{
			DB::connection()->getPdo()->beginTransaction();	

			// For file with single sheet
			foreach($reader as $row)
			{
				// convert object to array
				$data = json_decode(json_encode($row), true);

				foreach($data as $key => $value)
				{
					if ($value == null)
					{
						$data[$key] = '';
					}
				}

				if (!isset($data['guardian_name']) || !$data['guardian_name'])
				{
					continue;
				}
				
				$data['is_active'] = 'yes';
				$data['role'] = 'guardian';
				$data['password'] = DEFAULT_PASSWORD;
				$data['confirm_password'] = DEFAULT_PASSWORD;
				$data['student_username'] = explode(',', $data['student_usernames']);
				$data['student_relationship'] = explode(',', $data['student_relationships']);
				
				// remove any whitespaces
				$data['student_username'] = array_map('trim', $data['student_username']);
				$data['student_relationship'] = array_map('trim', $data['student_relationship']);
				
				$result = $this->createGuardian($data);
				if (!$result['success'])
				{
					Session::flash('error-msg', $result['msg']);
					return Redirect::back()
													->withInput();
				}
				
			}
			
			$success = true;
			$msg = 'Guardian created';
			$param = $result['param'];

			DB::connection()->getPdo()->commit();
		}
		catch (Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
			// die($msg);
		}
		
		if (!$success)
		{
			Session::flash('error-msg', $msg);
			return Redirect::back();
		}
		else
		{
			return $this->redirectAction($success, 'create', $param, $msg);
		}
	
	}

	// private functions
	private function createGuardian($data)
	{
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		if (isset($data['dob_in_ad']) && $data['dob_in_ad']) {
			$data['dob_in_bs'] = $data['dob_in_ad'] ? (new DateConverter)->ad2bs($data['dob_in_ad']) : '';
		} else {
			$data['dob_in_bs'] = '';
		}

		$dataToStoreInGuardianTable = array();
		$dataToStoreInGuardianStudentRelationTable = array();
		$dataToStoreInUsersTable = array();

		// validate username(s) of student given
		$result = $this->validateStudentUsernames($data);
		
		if($result['status'] == 'error')
		{
			return array(
				'success'	=> false,
				'msg'	=> (string)$result['msg']
			);
		}
		else
		{
			$data['student_id'] = $result['data'];
		}
		
				
		//////////////////////////////////////////////////

		///////////////////////////
		// for guardian table //////
		$dataToStoreInGuardianTable = $this->assignValues($this->model_name, $data);
		$result = $this->validateInput($dataToStoreInGuardianTable);
		
		if($result['status'] == 'error')
		{
			return array(
				'success'	=> false,
				'msg'	=> (string)$result['data']
			);
		}

		////////////////////

		$dataToStoreInUsersTable = $this->assignDataToStoreInGuardianTable($data);
		$result = $this->validateInput($dataToStoreInUsersTable, false, 'Users');
	
		if($result['status'] == 'error')
		{
			return array(
				'success'	=> false,
				'msg'	=> (string)$result['data']
			);
		}

		

		////////////////////////////////////////////
		///////// storing begins //////////////////

		try
		{
			
			$dataToStoreInGuardianTable['photo'] = '';
			

			$id = $this->storeInDatabase($dataToStoreInGuardianTable);	//this is guardian id

			/////////
			//storing in guardian student relation table
			////////////
			StudentGuardianRelation::where('guardian_id', $id)->delete();
			for ($i = 0; $i < count($data['student_id']); $i++)
			{
				$dataToStoreInGuardianStudentRelationTable['is_active'] = 'yes';
				$dataToStoreInGuardianStudentRelationTable['guardian_id'] = $id;
				$dataToStoreInGuardianStudentRelationTable['student_id'] = $data['student_id'][$i];
				$dataToStoreInGuardianStudentRelationTable['relationship'] = isset($data['student_relationship'][$i]) ? $data['student_relationship'][$i] : '' ;
				$this->storeInDatabase($dataToStoreInGuardianStudentRelationTable, 'StudentGuardianRelation');
			}

			
			$dataToStoreInUsersTable['user_details_id'] = $id;
			$dataToStoreInUsersTable['password'] = Hash::make($data['password']);
			$this->storeInDatabase($dataToStoreInUsersTable, 'Users');	
			
			
			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		return array(
			'success' => $success,
			'msg'			=> $msg,
			'param'		=> $param
		);
	}
	

}