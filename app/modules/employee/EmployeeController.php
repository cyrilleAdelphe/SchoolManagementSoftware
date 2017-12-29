<?php

class EmployeeController extends BaseController
{
	protected $view = 'employee.views.';

	protected $model_name = 'Employee';

	protected $module_name = 'employee';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'employee_name',
										'alias'			=> 'Employee Name'
									),

									array
									(
										'column_name' 	=> 'username',
										'alias'			=> 'Username'
									),
									
									array
									(
										'column_name' 	=> 'current_address',
										'alias'			=> 'Current Address'
									),
									array
									(
										'column_name' 	=> 'primary_contact',
										'alias'			=> 'Contact'
									),
									array
									(
										'column_name' 	=> 'joining_date_in_bs',
										'alias'			=> 'Joining Date'
									)
								 );

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$helper = new EmployeeHelperController;
		$posts = $helper->getAllGroups();
		//get all groups
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('role', $this->role)
					->with('posts', $posts)
					->with('actionButtons', $this->getActionButtons());

	}

	

	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$helper = new EmployeeHelperController;

		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		// echo '<pre>';
		// print_r($data);
		// die;

		$data['employee_dob_in_bs'] = (isset($data['employee_dob_in_ad']) && $data['employee_dob_in_ad'])
																	? (new DateConverter)->ad2bs($data['employee_dob_in_ad'])
																	: '';
		
		$data['joining_date_in_bs'] = (isset($data['joining_date_in_ad']) && $data['joining_date_in_ad'])
																	? (new DateConverter)->ad2bs($data['joining_date_in_ad'])
																	: '';
		
		$dataToStoreInEmployeeTable = array();
		$dataToStoreInEmployeePositionTable = array();
		$dataToStoreInAdminTable = array();


		/*
		/ Task of Validating
		/
		/
		*/
		$dataToStoreInEmployeeTable = $this->assignValues('Employee', $data);
		$result = $this->validateInput($dataToStoreInEmployeeTable);


		if($result['status'] == 'error')
		{
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		else
		{
			if(!isset($data['group_id']))
			{
				Session::flash('error-msg', ConfigurationController::translate('User must be assigned a group'));
				return Redirect::route($this->module_name.'-create-get')
						->withInput();
			}
		}

		if(isset($data['eton_allow_backend_access_checkbox']))
		{
			$dataToStoreInAdminTable = $this->assignDataToStoreInAdminTable($data);
			$result = $this->validateInput($dataToStoreInAdminTable, false, 'Admin');

			if($result['status'] == 'error')
			{
				Session::flash('error-msg', json_encode($result['data']));
				return Redirect::route($this->module_name.'-create-get')
							->withInput()
							->with('errors', $result['data']);
			}	
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
			$cv = Input::hasFile('cv') ? Input::file('cv') : '';

			if($cv !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/employee/assets/cv', array('pdf', 'doc'), 10485760000);
				$result = $upload->uploadFile($cv);
				$result = json_decode($result, true);

				if($result['status'] == 'error')
				{

					throw new Exception($result['message']);
				}
				else
				{
					$dataToStoreInEmployeeTable['cv'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToStoreInEmployeeTable['cv'] = '';
			}


			$photo = Input::hasFile('photo') ? Input::file('photo') : '';
			
			if($photo !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/employee/assets/images', array('jpg', 'jpeg', 'png'), 10485760000);
				$result = $upload->uploadFile($photo);
				$result = json_decode($result, 1);
				if($result['status'] == 'error')
				{
					
					throw new Exception($result['message']);
				}
				else
				{
					$dataToStoreInEmployeeTable['photo'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToStoreInEmployeeTable['photo'] = '';
			}

			$id = $this->storeInDatabase($dataToStoreInEmployeeTable); //employee_id


			EmployeePosition::where('employee_id', $id)->delete();
			foreach($data['group_id'] as $group_id)
			{
				$dataToStoreInEmployeePositionTable['employee_id'] = $id;
				$dataToStoreInEmployeePositionTable['group_id'] = $group_id;
				$dataToStoreInEmployeePositionTable['is_active'] = 'yes';
				$this->storeInDatabase($dataToStoreInEmployeePositionTable, 'EmployeePosition');
			}


			if(isset($data['eton_allow_backend_access_checkbox']))
			{
				$dataToStoreInAdminTable['admin_details_id'] = $id;
				$dataToStoreInAdminTable['password'] = Hash::make($data['password']);
				$dataToStoreInAdminTable['is_active'] = 'yes';
				$this->storeInDatabase($dataToStoreInAdminTable, 'Admin');
			}



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
		$helper = new EmployeeHelperController;
		$posts = $helper->getAllGroups();

		$model = new $this->model_name;
		
		$data = $model->getEditViewData($id);		
		
		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('posts', $posts)
					->with('data', $data)
					->with('id', $id)
					->with('actionButtons', $this->getActionButtons());
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$helper = new EmployeeHelperController;

		$success = false;
		$msg = '';
		$param = array('id' => $id);

		$data = Input::all();

		$data['employee_dob_in_bs'] = (isset($data['employee_dob_in_ad']) && $data['employee_dob_in_ad'])
																	? (new DateConverter)->ad2bs($data['employee_dob_in_ad'])
																	: '';
		
		$data['joining_date_in_bs'] = (isset($data['joining_date_in_ad']) && $data['joining_date_in_ad'])
																	? (new DateConverter)->ad2bs($data['joining_date_in_ad'])
																	: '';

		$data['leave_date_in_bs'] = (isset($data['leave_date_in_bs']) && $data['leave_date_in_bs'])
																? (new DateConverter)->ad2bs($data['leave_date_in_ad'])
																: '';

		$dataToUpdateInEmployeeTable = array();
		$dataToUpdateInEmployeePositionTable = array();

		/*
		/ Task of Validating
		/
		/
		*/
		$dataToUpdateInEmployeeTable = $this->assignValues('Employee', $data);
		$result = $this->validateInput($dataToUpdateInEmployeeTable, true);

		if($result['status'] == 'error')
		{
			return Redirect::route($this->module_name.'-edit-get', $id)
						->withInput()
						->with('errors', $result['data']);
		}
		else
		{
			if(!isset($data['group_id']))
			{
				Session::flash('error-msg', ConfigurationController::translate('User must be assigned a group'));
				return Redirect::route($this->module_name.'-edit-get', array($id))
						->withInput();
			}
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
			$cv = Input::hasFile('cv') ? Input::file('cv') : '';

			if($cv !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/employee/assets/cv', array('pdf', 'doc'), 10485760000, $data['original_cv']);
				$result = $upload->uploadFile($cv);
				$result = json_decode($result, true);

				if($result['status'] == 'error')
				{

					throw new Exception($result['message']);
				}
				else
				{
					$dataToUpdateInEmployeeTable['cv'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToUpdateInEmployeeTable['cv'] = $data['original_cv'];
			}


			$photo = Input::hasFile('photo') ? Input::file('photo') : '';
			
			if($photo !== '')
			{
				$upload = new FileUploadController(app_path().'/modules/employee/assets/images', array('jpg', 'jpeg', 'png'), 10485760000, $data['original_photo']);
				$result = $upload->uploadFile($photo);
				$result = json_decode($result, 1);
				if($result['status'] == 'error')
				{
					
					throw new Exception($result['message']);
				}
				else
				{
					$dataToUpdateInEmployeeTable['photo'] = $result['uploaded_name'];
				}
			}
			else
			{
				$dataToUpdateInEmployeeTable['photo'] = $data['original_photo'];
			}

			$id = $this->updateInDatabase($dataToUpdateInEmployeeTable); //employee_id


			EmployeePosition::where('employee_id', $id)->delete();
			foreach($data['group_id'] as $group_id)
			{
				$dataToUpdateInEmployeePositionTable['employee_id'] = $id;
				$dataToUpdateInEmployeePositionTable['group_id'] = $group_id;
				$dataToUpdateInEmployeePositionTable['is_active'] = 'yes';
				$this->storeInDatabase($dataToUpdateInEmployeePositionTable, 'EmployeePosition');
			}

			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id;
			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		return $this->redirectAction($success, 'edit', $param, $msg);
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
				$image = $record->photo;
				$cv = $record->cv;
				$record->delete();
				@unlink(app_path().'/modules/employee/assets/cv/'.$cv);
				@unlink(app_path().'/modules/employee/assets/images/'.$image);
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

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	private function assignValues($modelname, $data)
	{
		$tablename = $modelname::getTableName();
		$columns = Schema::getColumnListing($tablename);
		foreach($columns as $column)
		{
			$dataToStore[$column] = isset($data[$column]) ? $data[$column] : null;
		}

		return $dataToStore;
	}

	private function assignDataToStoreInAdminTable($data)
	{
			$dataToStoreInAdminTable = array();
			$dataToStoreInAdminTable['name'] = $data['employee_name'];
			$dataToStoreInAdminTable['email'] = $data['email'];
			
			//$dataToStoreInAdminTable['username'] = $data['username'];
			do {
					$dataToStoreInAdminTable['username'] = EMPLOYEE_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
				} while(Users::where('username', $dataToStoreInAdminTable['username'])->first());
			
			$dataToStoreInAdminTable['password'] = $data['password'];
			$dataToStoreInAdminTable['confirm_password'] = $data['confirm_password'];
			$dataToStoreInAdminTable['contact'] = $data['primary_contact'];
			$dataToStoreInAdminTable['address'] = $data['current_address'];
			$dataToStoreInAdminTable['admin_details_id'] = 0; //this will be changed later
			$dataToStoreInAdminTable['is_blocked'] = 'no';
			$dataToStoreInAdminTable['confirmation_count'] = 0;
			$dataToStoreInAdminTable['is_active'] = 'yes';

			return $dataToStoreInAdminTable;
	}

	// ajax functions
	public function ajaxDocumentList()
	{
	AccessController::allowedOrNot($this->module_name, 'can_view');
	
		if(!Input::has('employee_id'))
		{
			return 'invalid request';
		}
		
		$id = Input::get('employee_id');

		$documents = DB::table(EmployeeDocument::getTableName())
								->join(DownloadManager::getTableName(), DownloadManager::getTableName().'.id', '=', EmployeeDocument::getTableName().'.download_id')
								->select(array(EmployeeDocument::getTableName().'.*', 'filename', 'google_file_id'))
								->where(EmployeeDocument::getTableName().'.is_active', 'yes')
								->where('employee_id', $id)
								->get();
		foreach($documents as $key => $child) {
			$documents[$key]->download_link = URL::route('download-manager-backend-file-download', [$child->download_id, $child->google_file_id]);
		}

		return View::make($this->view . 'ajax.document-list')
								->with('role', $this->role)
								->with('current_user', $this->current_user)
								->with('documents', $documents);

	}

	public function changePasswordBySuperadmin($id)
	{
		$input_data = Input::all();
		/*echo $this->current_user->id;
		die();*/
		if ($this->current_user->role == 'superadmin')
		{
			$result = Admin::where('admin_details_id', $id)
				->where('is_active', 'yes')
			  ->first();

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
				Session::flash('error-msg', 'Password and confirm passwords must match');
				//die('agin here here');
				return Redirect::back();
			}
		}
		else
		{
			return Response('Unauthorized', 500);
		}
	
	}

}
