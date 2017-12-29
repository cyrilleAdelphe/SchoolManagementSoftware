<?php

class AdminController extends BaseController
{
	protected $view = 'admin.views.';
	protected $model_name = 'Admin';

	protected $module_name = 'admin';

	protected $role;


	/////// AdminDashboard-v1-changed-made-here /////////
	public function home()
	{
		$model = $this->model_name;
		$model = new $model;
		$data = array();
		$data['upcoming_events'] = DashboardController::dashboardGetUpcomingEvents('all');
		$data['no_of_upcoming_events'] = count($data['upcoming_events']);
		$data['no_of_teachers'] = DashboardController::dashboardGetTeachers();
		$data['no_of_total_students'] = DashboardController::dashboardGetTotalStudents();
		$data['notice'] = DashboardController::dashboardGetNotice();
		
		return View::make($this->view.'home')
					->with('data', $data);
	}
	/////// AdminDashboard-v1-changed-made-here /////////

	public function getRegister()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		return View::make($this->view.'register');
	}
	
	/////// AdminDashboard-v1-changed-made-here /////////
	public function postChangeDetails()
	{
		$input_data = Input::all();

		$id = Auth::admin()->user()->admin_details_id;
		$validator = Validator::make(
			$input_data,
			
			array(
					'photo' => ['image', 'max:1024'],
					'new_password' => ['min:6'],
					'new_password_confirm' => ['same:new_password']
			)
		);

		if ($validator->fails())
		{
			$error = '';
			foreach ($validator->messages()->all('<li>:message</li>') as $message)
			{
			    $error .= $message;
			}
			Session::flash('error-msg', $error);
			return Redirect::back();
		}

		if ($this->current_user->role == 'admin' && $this->details_id == $id)
		{
			$result = Admin::where('admin_details_id', $id)
				->where('is_active', 'yes')
			  ->first();

			if ($input_data['old_password'] && strlen($input_data['old_password']))  
			{	
				if ($result && Hash::check($input_data['old_password'], $result->password))
				{
					if ($input_data['new_password'] == $input_data['new_password_confirm'])
					{
						$result->password = Hash::make($input_data['new_password']);
					}
					else
					{
						Session::flash('error-msg', 'Confirm Password Mismatch');
						return Redirect::back();
					}
				}
				else
				{
					Session::flash('error-msg', 'Incorrect password');
					return Redirect::back();
				}
			}

			try
			{
				
					DB::connection()->getPdo()->beginTransaction();

					if (Input::hasFile('photo') && Input::file('photo')->isValid())
					{
						$photo = Input::file('photo');
						$upload = new FileUploadController(app_path().'/modules/employee/assets/images', array('jpg', 'jpeg', 'png'), 10485760000);
						$status = $upload->uploadFile($photo);
						$status = json_decode($status, 1);
						if($status['status'] == 'error')
						{
							Session::flash('error-msg', $status['message']);
							return Redirect::back();
							//throw new Exception($status['message']);
						}
						else
						{
							$employee_table = Employee::where('id', $id)->first();
							$employee_table->photo =$status['uploaded_name'];

							$employee_table->save();
						}
					
					
					}
					$result->save();

					DB::connection()->getPdo()->commit();
				
				Session::flash('success-msg', 'Details changed successfully');
			}
			catch(PDOException $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', $e->getMessage());
			}
			
			return Redirect::back();
		}
		else
		{
			return Response('Unauthorized', 500);
		}
	}
	/////// AdminDashboard-v1-changed-made-here /////////
	
	public function postRegister()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$input_data = Input::all();

		$modelName = $this->model_name;

		$result = $this->validateInput($modelName, $input_data);

	    if($result['status'] == 'error')
	    {
	    	return Redirect::route('admin-register')
	    					->with('errors', $result['message']);
	    }
	    else
	    {
	    	unset($input_data['confirm_password']);
	    	$input_data['password'] = Hash::make($input_data['password']);

	    	//store in database
	    	$result = $this->storeInDatabase($modelName, $input_data);

	    	return Redirect::route('admin-register')
	    					->with('global', 'successfully created account.');
	    }
	}

	public function getLogin()
	{
		
		return View::make($this->view.'login');
	}

	public function postLogin()
	{
		$input_data = Input::all();

		$remember = false;

		$result = Admin::where('username', $input_data['username'])
					   ->where('is_active', 'yes')
					   ->first();

		if($result && $result->is_blocked == 1)
		{
			return View::make('backend.admin.lock');
		}
		else if($result && Hash::check($input_data['password'], $result->password))
		{
			ConfigurationController::recordTheNoOfAttempts($input_data['username'], 'admin', true);

			Auth::admin()->login($result); 
			return Redirect::route('admin-home');
		}
		else
		{
			$return = ConfigurationController::recordTheNoOfAttempts($input_data['username'], 'admin');
			if($return == 'blocked')
			{
				return View::make('backend.admin.lock');
			}
			else
			{
				return Redirect::route('admin-login')
								->withInput()
								->with('error-msg', ConfigurationController::translate('Invalid username-password combination or user not active'));	
			}
		}
	}

	public function logout()
	{
	        if(Auth::admin()->logout())
		{
			
			Session::put('success-msg', 'Successfully logged out!');
			return Redirect::route('superadmin-login');	
		}
		else
		{
			Session::put('error-msg', 'Something went wrong. Please try again!');
			return Redirect::route('admin-home');
		}
		
	}
}

?>