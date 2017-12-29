<?php

class AccessController extends Controller
{
	private $current_user;

	private $view = 'access-control.views.';

	////// AccessController-v1-changes-made-here ///////
	public $module_name = 'access-control';
	////// AccessController-v1-changes-made-here ///////

	public function __construct()
	{
		//die('here');
		$this->current_user = HelperController::getCurrentUser();
	}

	public function getAllModules()
	{
		///check the directory modules and get all modules with file access.json
		//list the modules
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$directories = glob(app_path().'/modules' . '/*', GLOB_ONLYDIR);
		$dirs = array();
		foreach($directories as $d)
		{
			$files = scandir($d);
			if(in_array('access.json', $files))
			{
				$dirs[] = substr($d, strrpos($d, '/')+1);
			}
			
		}

		return View::make('access-control.views.list')
					->with('modules', $dirs)
					->with('current_user', $this->current_user);
	}

	public function getSetAccessControl($module_name)
	{
		//get all admin group ids
		//get students and guardians
		AccessController::allowedOrNot($this->module_name, 'can_create');

		$groups = Group::where('is_active', 'yes')
							->lists('group_name', 'id');

		try
		{
			$access = File::get(app_path().'/modules/'.$module_name.'/access.json');
			$access = json_decode($access, true);

			
		}
		catch(Exception $e)
		{
				$access = array();
				Session::put('error-msg', 'Module not found');
		}

		return View::make($this->view.'create')
					->with('access', $access)
					->with('groups', $groups)
					->with('module_name', $module_name)
					->with('current_user', $this->current_user);	
		
		
	}

	public function postSetAccessControl($module_name)
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$input = Input::all();
		
		$access = array();
		
		foreach($input['permission_type'] as $permission_type)
		{
			if(isset($input[$permission_type.'_admin']))
			{
				$access[$permission_type]['admin'] = $input[$permission_type.'_admin'];
			}
			else
			{
				$access[$permission_type]['admin'] = array();
			}

			if(isset($input[$permission_type.'_user']))
			{
				$access[$permission_type]['users'] = $input[$permission_type.'_user'];
			}
			else
			{
				$access[$permission_type]['users'] = array();
			}

			if(isset($input[$permission_type.'_frontend']))
			{
				$access[$permission_type]['frontend'] = 'yes';
			}
			else
			{
				$access[$permission_type]['frontend'] = 'no';
			}

			if(isset($input[$permission_type.'_alias']))
			{
				$access[$permission_type]['alias'] = $input[$permission_type.'_alias'];
			}
			else
			{
				$access[$permission_type]['alias'] = '';
			}
			if(isset($input[$permission_type.'_routes']))
			{
				$access[$permission_type]['routes'] = $input[$permission_type.'_routes'];
			}
			else
			{
				$access[$permission_type]['routes'] = array();
			}
		}

		//store in access.json
		try
		{
			File::put(app_path().'/modules/'.$module_name.'/access.json', json_encode($access, JSON_PRETTY_PRINT));	
			Session::flash('success-msg', 'Permission successfully set');
		}
		catch(Exception $e)
		{
			//die($e->getMessage());
			Session::flash('error-msg', 'Permission could not be set');
		}
		

		return Redirect::route('access-permissions', $module_name);

	}

	public static function checkPermission($module_name, $permission_type)
	{
		$permission_type = explode(',', $permission_type);
		if(Auth::user()->check())
		{
			$role = Auth::user()->user()->role;
			try
			{
				$access = json_decode(File::get(app_path().'/modules/'.$module_name.'/access.json'), true);	

				$return = false;
				foreach ($permission_type as $per)
				{
					if (in_array($role, $access[$per]['users']))
					{
						$return = true;
						break;
					}
				}
				return $return;
				
			}
			catch(Exception $e)
			{
				return true;
			}
		}
		elseif(Auth::admin()->check())
		{
			$role = Auth::admin()->user()->admin_details_id;
			$role = EmployeePosition::where('employee_id', $role)
									->where('is_active', 'yes')
									->lists('group_id');
			try
			{
				$return = false;
				$access = json_decode(File::get(app_path().'/modules/'.$module_name.'/access.json'), true);	
				
				
				foreach($permission_type as $per)
				{
					if(count(array_intersect($role, $access[$per]['admin'])))
					{
						$return = true;
						break;
					}
				}
				
				return $return;
			}
			catch(Exception $e)
			{
				return true;
			}
		}
		elseif(Auth::superadmin()->check())
		{
			return true;
		}
		else
		{
			// check for frontend user
			
			$access = json_decode(File::get(app_path().'/modules/'.$module_name.'/access.json'), true);	
			$return = true;
			foreach($permission_type as $per)
				$return &= isset($access[$per]['frontend']) && $access[$per]['frontend'] == 'yes';
			return $return;

		}
		
		
		//get current user
		//if admin get the group ids
		//if user get whether guardian or student

		//return true or false
	}

	public static function allowedOrNot($module_name, $permission_type)
	{
		if(!AccessController::checkPermission($module_name, $permission_type))
		{
			App::abort(403, 'You are not allowed to view this page');
		}
	}

	private function getAllRoutes($module_name)
	{
		$route = array();
		$content = File::get(app_path().'/modules/'.$module_name.'/route.php');
		//echo $content;
		$routes = preg_match_all("/'as'\s*=>\s*'[a-z\-]+'/", $content, $matches);
		foreach($matches[0] as $match)
		{
			preg_match_all("/'[a-z\-]+'/", $match, $route_name);
			//echo $route_name[0][1].'<br>';
			
			$route[] = substr($route_name[0][1], 1, strlen($route_name[0][1]) - 2);
		}
/*
		foreach($route as $index => $r)
		{
			//echo $r.'<br>';
			$route[$index] = preg_replace('/%7.*%7D/', 'any_id', URL::route($r));
		}*/

		return $route;
	}

	// private function getAllGroups()
	// {
	// 	$return = array('user_groups' => array(), 'admin_groups' => array());
	// 	//get all user groups
	// 	$return['user_groups'] = UserGroup::where('is_active', 'yes')
	// 						->lists('user_group_name', 'id');

	// 	$return['admin_groups'] = Group::where('is_active', 'yes')
	// 						->lists('group_name', 'id');

	// 	return $return;
	// }

	public function getListAccess($module_name)
	{
		/*
			array('route_name' => array('user'	=> array(1,2,3,4,5),
										'admin'	=> array(1,2,3,4,5),
										'all' => 'yes'),
				'route_name1' => array('user'	=> array(1,2,3,4,5),
										'admin'	=> array(1,2,3,4,5),
										'all' => 'yes')
										);
		*/


	}

	public function getCurrentPermissions($module_name)
	{
		$content = File::get(app_path().'/modules/'.$module_name.'/access.json');

		return json_decode($content, true);
	}

	public function getViewAccess($module_name)
	{

	}

	private function writeInAccessFile($module_name, $data, $json_encode = false)
	{
		$return = array('status' => 'error', 'message' => '');
		try
		{
			File::put(app_path().'/modules/'.$module_name.'/access.json', json_encode($data));
			$return['status'] = 'success';
			$return['message'] = ConfigurationController::translate('Permissions successfully created');
		}
		catch(Exception $e)
		{
			$return['message'] = ConfigurationController::errorMsg($e->getMessage());
		}

		if($json_encode)
		{
			$return = json_encode($return);
		}

		return $return;
	}

	public function getCreateAccess($module_name)
	{
		$all_groups = $this->getAllGroups();
		$data = $this->getCurrentPermissions($module_name);
		$routes = $this->getAllRoutes($module_name);

		return View::make($this->view.'create')
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('routes', $routes)
					->with('all_groups', $all_groups)
					->with('module_name', $module_name);
	}

	public function postCreateAccess($module_name)
	{
		$data = array();
		echo '<pre>';
		/*
		print_r(Input::all());
		die();*/

		$input = Input::all();

		foreach($input['route_name'] as $index => $route_name)
		{
			$temp  = array();
			$temp[$route_name]['user'] = isset($input['user_group_'.$index]) ? $input['user_group_'.$index] : array();
			$temp[$route_name]['admin'] = isset($input['admin_group_'.$index]) ? $input['admin_group_'.$index] : array();
			$temp[$route_name]['all'] = isset($input['all_'.$index]) ? 'yes' : 'no';
			$temp[$route_name]['description'] = trim($input['description'][$index]);
			$data = array_merge($data, $temp);
		}

		$status = $this->writeInAccessFile($module_name, $data);

		Session::flash($status['status'].'-msg', $status['message']);

		return Redirect::back()
						->withInput();
	}
}