<?php

class UsersController extends BaseController
{
	protected $view = 'users.views.';

	protected $model_name = 'Users';

	protected $module_name = 'users';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'username',
										'alias'			=> 'Username'
									),
									array
									(
										'column_name' 	=> 'name',
										'alias'			=> 'Name'
									),
									array
									(
										'column_name' 	=> 'role',
										'alias'			=> 'Role'
									),
									array
									(
										'column_name' 	=> 'is_active',
										'alias'			=> 'Status'
									)
								 );
	//protected $view = 'users.views.';
	//public $current_user;

	public function home()
	{
		if(Auth::user()->user()->role == 'student')
		{
			$model = 'StudentRegistration';
			$module_name = 'student';
			/*echo $this->role;
			die();*/
		}
		elseif(Auth::user()->user()->role == 'guardian')
		{
			$model = 'Guardian';
			$module_name = 'guardian';	
			
		}
		
		$model = new $model;
		$data = $model->getViewViewData(Auth::user()->user()->user_details_id);

		return View::make($this->view.'home')
					->with('data', $data)
					->with('user_role', Auth::user()->user()->role)
					->with('module_name', $module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user);
	}

	public function getRegister()
	{
		return View::make($this->view.'register');
	}

	public function postRegister()
	{
		$input_data = Input::all();

		$modelName = 'Users';

		//dd($input_data);

		$result = $this->validateInput($modelName, $input_data);

	   // dd($result);
	    if($result['status'] == 'error')
	    {
	    	return Redirect::route('uesrs-register')
	    					->with('errors', $result['message']);
	    }
	    else
	    {
	    	unset($input_data['confirm_password']);


	    	//generate-confirmation
	    	$input_data['confirmation'] = str_random(60);
	    	$input_data['password'] = Hash::make($input_data['password']);
	    	//store in database
	    	$result = $this->storeInDatabase($modelName, $input_data);

	    	if($result['status'] == 'success')
	    	{
	    		//send email.
	    		$view = $this->view.'emails.confirmation';
	    		//link //id //code
	    		$confirmationLink = URL::route('users-confirmation', array($result['data'], $input_data['confirmation']));
	    		$parameters = array('link' => $confirmationLink, 'name' => $input_data['fname']);
	    		$mailDetails = array('email' => $input_data['email'], 'firstname' => $input_data['fname'] );
	    		$subject = 'confirmation';
	    		$result = $this->sendMailFunction($view, $parameters, $mailDetails, $subject);

	    		return Redirect::route('users-login')
	    					->with('global', 'successfully created account. Please check your email for confirmation. click here to resend confirmation');
	    	}
	    }

	}

	public function getConfirmation($id, $code)
	{
		$unConfirmedUser = Users::where('id', $id)
								->where('confirmation', '!=', '')
								->get();

		if(count($unConfirmedUser) == 1)
		{
			$modelName = 'Users';
			
			$data['confirmation'] = '1';
			$data['id'] = $id;

			$result = $this->updateInDatabase($modelName, $data);

			Session::put('global', $result['message']);

			if($result['status'] == 'success')
			{
				return Redirect::route('users-login');
				//log in user directly
			}
			else
			{
				return Redirect::route('users-login');
			}
		}
	}

	public function getLogin()
	{
		Auth::superadmin()->logout();
		Auth::admin()->logout();
		return View::make($this->view.'login');
	}

	public function postLogin()
	{
		$input_data = Input::all();

		$remember = false;
		/*if(isset($data['remember']))
		{
			$remember = true;
		}
		else
		{
			$remember = false;
		}*/

		$username = $input_data['username'];


		if($username[0] == EMPLOYEE_PREFIX_IN_USERNAME) {
			// for school staff
			$result = Admin::where('username', $username)
										->where('is_active', 'yes')
										->first();
			$group = 'admin';
			$role = 'admin';
		} else {
			// for students and parents
			$result = User::where('username', $username)
							->where('is_active', 'yes')
							->first();
			$group = 'users';
			$role = $result ? $result->role : '';
		}
/*
		$result = Users::where('username', $input_data['username'])
					   ->where('is_active', 'yes')
					   ->first();
*/
					   //print_r($result);
		if($result && $result->is_blocked == 1)
		{
			return View::make('frontend.lock');
		}
		else if($result && Hash::check($input_data['password'], $result->password))
		{
			ConfigurationController::recordTheNoOfAttempts($input_data['username'], $group, true);

			//get required data


			$group == 'admin' ? Auth::admin()->login($result) : Auth::user()->login($result); 
			return Redirect::route($group.'-home');
		}
		else
		{
			$return = ConfigurationController::recordTheNoOfAttempts($input_data['username'], $group);
			if($return == 'blocked')
			{
				return View::make('backend.'.$group.'.lock');
			}
			else
			{
				return Redirect::route('users-login')
								->withInput()
								->with('error-msg', ConfigurationController::translate('Invalid username-password combination or user not active'));	
			}
		}
	}

	public function logout()
	{
		Auth::superadmin()->logout();
		
		if(Auth::admin()->check())
		{
			if(Auth::admin()->logout())
			{
				Session::put('success-msg', 'Successfully logged out!');
				return Redirect::route('users-login');	
			}
			else
			{
				Session::put('error-msg', 'Something went wrong');
				return Redirect::route('admin-home');
			}
		}
		elseif(Auth::user()->check())
		{
			$group = 'users';
			if(Auth::user()->logout())
			{
				Session::put('success-msg', 'Successfully logged out!');
				return Redirect::route('users-login');	
			}
			else
			{
				Session::put('error-msg', 'Something went wrong');
				return Redirect::route('users-home');
			}
		}

		//Auth::admin()->logout();
	}
}