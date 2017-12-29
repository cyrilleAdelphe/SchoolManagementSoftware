<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

	public function getTest()
	{
		$tasks = array(array('name' => 'Prabal', 'progress' => '100', 'color' => 'blue'), array('name' => 'Prabal', 'progress' => '100', 'color' => 'blue'));

		return View::make('admin-lte.lock')
					->with('tasks', $tasks);
	}

	public function postResetPassword()
	{
		$group = Input::get('group', '');
		$id = Input::get('user_id'); //this id is id of users_table, admin_table and superadmin_table

		switch($group)
		{
			case 'student' :
				$model = 'Users';
				$password = DEFAULT_PASSWORD;
				$id = $model::where('user_details_id', $id)
							->where('role', 'student')
							->first();
						break;

			case 'guardian' :
				$model = 'Users';
				$password = DEFAULT_PASSWORD;
				$id = $model::where('user_details_id', $id)
							->where('role', 'guardian')
							->first();
							break;

			case 'admin' :
				$model = 'Admin';
				$password = DEFAULT_PASSWORD;
				$id = $model::where('admin_details_id', $id)
							->first();
							break;

			case 'superadmin':
				$model = 'Superadmin';
				$password = DEFAULT_PASSWORD;
				$id = $model::where('id', $id)
							->first();
							break;


			default:
				Session::flash('error-msg', 'Invalid group');
				return Redirect::back();
		}

		try
		{
			if($id)
			{
				$model::where('id', $id->id)
						->update(['password' => Hash::make($password)]);

				Session::flash('success-msg', 'Password successfully reset');	
			}
			else
			{
				Session::flash('error-msg', $group.' Not found');
			}
			
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back();

	}

}
