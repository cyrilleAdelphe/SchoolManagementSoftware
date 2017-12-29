<?php

class TransportationController extends BaseController
{
	protected $view = 'transportation.views.';

	protected $model_name = 'Transportation';

	protected $module_name = 'transportation';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'number_plate',
										'alias'			=> 'Number Plate'
									),
									array
									(
										'column_name' 	=> 'route',
										'alias'			=> 'Route'
									),
									array
									(
										'column_name' 	=> 'driver_number',
										'alias'			=> 'Driver Contact'
									),
									array
									(
										'column_name' 	=> 'fee_amount',
										'alias'			=> 'Fee'
									)
								 );
				
				
public function getAssignedStudentList()
		{
		
AccessController::allowedOrNot($this->module_name, 'can_view');

			$data = Input::all();
			$search = $data['search_assigned_student'];
			if(Request::ajax())
			{

			$assigned_students = DB::table('transportation_students')
								->join('transportation', 

'transportation.id','=','transportation_students.transportation_id')
								->join

('student_registration','student_registration.id','=','transportation_students.student_id')
								->select

('transportation.bus_code','transportation_students.student_id','student_registration.student_name','student_registration.last_name','transportation_students.fee_amount','transportation.unique_transportation_id','transportation_students.id')
								->where('transportation.is_active', 'yes')
								->where('student_registration.is_active', 'yes')
								->where('transportation_students.is_active', 'yes')
								->where('student_registration.student_name', 'LIKE' , '%'.$search.'%')
								->get();		

			return View::make($this->view.'student-list')
						->with('assigned_students', $assigned_students);


			}

		}

				
				
								 
		public function getListView()
	{
		//
		AccessController::allowedOrNot($this->module_name, 'can_view');
		


		$staff_transportation = DB::table('transportation')					
					->join('transportation_staffs','transportation_staffs.transportation_id','=','transportation.id')
					->select('transportation_staffs.employee_id')
					->count();					

		$transportation = DB::table('transportation')->select('bus_code','id')->lists('bus_code','id');
	
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
					->with('role', $this->role)
					->with('staff_transportation', $staff_transportation)
					->with('transportation', $transportation);

	}
								 
	
	public function getViewStaffVechileLocation($unique_bus_id)
	{
		AccessController::allowedOrNot('transportation', 'can_view_locations');
		//get the locations
		
		$initial_lat_lng = TransportationLatLng::where(TransportationLatLng::getTableName().'.unique_transportation_id', $unique_bus_id)
											->leftJoin(Transportation::getTableName(), Transportation::getTableName().'.unique_transportation_id', '=', TransportationLatLng::getTableName().'.unique_transportation_id')
											->select(
												TransportationLatLng::getTableName() . '.*',
												Transportation::getTableName() . '.number_plate'
											)->first();

		$distance_log = TransportationDistance::join(
			Transportation::getTableName(),
			Transportation::getTableName() . '.id', '=',
			TransportationDistance::getTableName() . '.bus_id'
		)->where(
			Transportation::getTableName() . '.unique_transportation_id',
			$unique_bus_id
		);

		if (Input::has('daterange'))
		{
			$daterange = Input::get('daterange');
			$daterange = explode('-', $daterange);
			$daterange = array_map('trim', $daterange);
			$daterange = array_map(function($d) { 
				return DateTime::createFromFormat('m/d/Y', $d)->format('Y-m-d');
			}, $daterange);
			if (count($daterange) == 2)
			{
				$distance_log = $distance_log->where(
					'start_date_time', '>=', $daterange[0] . ' 00:00:00'
				)->where(
					'end_date_time', '<=', $daterange[1] . '23:59:59'
				);
			}
		}

		$distance_log = $distance_log->get();

		$transportations = Transportation::where('is_active', 'yes')
										 ->select(array('unique_transportation_id', 'bus_code', 'number_plate', 'route'))
										 ->get();


		return View::make($this->view.'transportation-staff-view')
					->with('transportations', $transportations)
					->with('role', $this->role)
					->with('unique_transportation_id', $unique_bus_id)
					->with('distance_log', $distance_log)
					->with('initial_lat_lng', $initial_lat_lng);
	}

	public function viewLocations($unique_bus_id)
	{
		AccessController::allowedOrNot('transportation', 'can_view_locations');
		//get the locations
		
		$initial_lat_lng = TransportationLatLng::where(TransportationLatLng::getTableName().'.unique_transportation_id', $unique_bus_id)
											->leftJoin(Transportation::getTableName(), Transportation::getTableName().'.unique_transportation_id', '=', TransportationLatLng::getTableName().'.unique_transportation_id')
											->select(
												TransportationLatLng::getTableName() . '.*',
												Transportation::getTableName() . '.number_plate'
											)->first();

		$distance_log = TransportationDistance::join(
			Transportation::getTableName(),
			Transportation::getTableName() . '.id', '=',
			TransportationDistance::getTableName() . '.bus_id'
		)->where(
			Transportation::getTableName() . '.unique_transportation_id',
			$unique_bus_id
		);

		if (Input::has('daterange'))
		{
			$daterange = Input::get('daterange');
			$daterange = explode('-', $daterange);
			$daterange = array_map('trim', $daterange);
			$daterange = array_map(function($d) { 
				return DateTime::createFromFormat('m/d/Y', $d)->format('Y-m-d');
			}, $daterange);
			if (count($daterange) == 2)
			{
				$distance_log = $distance_log->where(
					'start_date_time', '>=', $daterange[0] . ' 00:00:00'
				)->where(
					'end_date_time', '<=', $daterange[1] . '23:59:59'
				);
			}
		}

		$distance_log = $distance_log->get();

		$transportations = Transportation::where('is_active', 'yes')
										 ->select(array('unique_transportation_id', 'bus_code', 'number_plate', 'route'))
										 ->get();
$transportation_list = Transportation::where('is_active', 'yes')
												->select('bus_code','unique_transportation_id')
												->lists('bus_code','unique_transportation_id');


		return View::make($this->view.'view')
					->with('transportations', $transportations)
					->with('role', $this->role)
					->with('unique_transportation_id', $unique_bus_id)
					->with('distance_log', $distance_log)
					->with('initial_lat_lng', $initial_lat_lng)
					->with('transportation_list', $transportation_list);;
	}

	public function makeXml($unique_transportation_id)
	{
		$dom = new DOMDocument("1.0");
		$node = $dom->createElement("markers");
		$parnode = $dom->appendChild($node);

		$locations = TransportationLatLng::where('unique_transportation_id', $unique_transportation_id)
									->get();
		foreach($locations as $l)
		{
			$node = $dom->createElement("marker");
			$newnode = $parnode->appendChild($node);
			$newnode->setAttribute("name",$l->name);
			$newnode->setAttribute("address", $l->address);
			$newnode->setAttribute("lat", $l->lat);
			$newnode->setAttribute("lng", $l->lng);
			$newnode->setAttribute("type", 'restaurant');	
		}
		
		header("Content-type: text/xml");

		echo $dom->saveXML();
		die();
	}

	public function addLocations()
	{
		AccessController::allowedOrNot('transportation', 'can_add_locations');
		$locations = Input::get('locations');

		//locations in the form unique_bus_id, address, name, lat, lng
		$locations = json_decode($locations);
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();
				foreach($locations as $location)
				{
					TransportationLatLng::create($location);
				}
			DB::connection()->getPdo()->commit();
			$status = "success";
			$message = "Locations successfully added";
		}
		catch(Exception $e)
		{
			$status = 'error';
			$message = $e->getMessage();
		}

		return json_encode(array('status' => $status, 'message' => $message));
		
	}

	public function deleteLocations($unique_transportation_id = 0)
	{
		AccessController::allowedOrNot('transportation', 'can_delete');
		if($unique_transportation_id)
		{
			TransportationLatLng::where('unique_transportation_id', $unique_transportation_id )->delete();
		}
		else
		{
			TransportationLatLng::where('id', '>', 0)->delete();
		}
	}

		public function postAssignStudent()
	{
		AccessController::allowedOrNot('transportation', 'can_create');

		$data = Input::all();

		$student_id_array = explode(",", $data['student_id']);

		/*echo '<pre>';
		print_r($student_id_array);
		die();*/
				// the visible student ID is username!!
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			foreach($student_id_array as $student_username)
			{
				$data['student_id'] = HelperController::getStudentIdFromUsername($student_username);
				$result = $this->validateInput($data, false, 'TransportationStudent');

				if($result['status'] == 'error')
				{
					Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
					
					return Redirect::back()
								->withInput()
								->with('errors', $result['data']);
				}


						
				$id = $this->storeInDatabase($data, 'TransportationStudent');								
			}

		$success = true;

		DB::connection()->getPdo()->commit();
		$msg = 'Record successfully created';
			
		}
		catch(PDOException $e)
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

	public function postAssignStaffs() 
	{
		AccessController::allowedOrNot('transportation','can_create');

		$data = Input::all();

		$employee_id_array = explode(",", $data['employee_id']);

		try
		{
			DB::connection()->getPDO()->beginTransaction();			

			foreach ($employee_id_array as $employee_username)
		    {
				
				$data['employee_id'] = HelperController::getEmployeeIdfromUsername($employee_username);

				$result = $this->validateInput($data, false, 'TransportationStaff');

				if($result['status'] == 'error')
				{
					Session::flash('error-msg', ConfigurationController::translate('some validation error occured'));

					return Redirect::back()
									->withInput()
									->with('errors', $result['data']);
				}

				$id = $this->storeInDatabase($data, 'TransportationStaff');
			}

			$success = true;
			DB::connection()->getPdo()->commit();
			$msg = 'Record successfully created';
		}

		catch(PDOException $e)
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



	public function getEditAssignStudents($id)
	{
		AccessController::allowedOrNot('transportation', 'can_edit');
		
		$data = TransportationStudent::where('id', $id)->first();
		
		// the visible student ID is username!!
		$data->student_id = Users::where('user_details_id', $data->student_id)
															->where('role', 'student')
															->first()
															->username;

		return View::make($this->view.'edit-assign-students')
					->with('role', $this->role)
					->with('data', $data);
	}

	public function postEditAssignStudents($id)
	{
		AccessController::allowedOrNot('transportation', 'can_edit');
		$data = Input::all();
		
		// the visible student ID is username!!
		$data['student_id'] = HelperController::getStudentIdFromUsername($data['student_id']);

		$result = $this->validateInput($data, true, 'TransportationStudent');
		
		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		try
		{
			$id = $this->updateInDatabase($data, array(), 'TransportationStudent');	

			$success = true;
			$msg = 'Record successfully updated';
		}
		catch(PDOException $e)
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

	public function getEditAssignStaffs($id) {

		AccessController::allowedOrNot('transportation', 'can_edit');
		
		
		
		$data = TransportationStaff::find($id);

		
		$employee_id = Admin::where('admin_details_id', $data->employee_id)
									->pluck('username');

		// the visible student ID is username!!
		return View::make($this->view.'edit-assign-staffs')
					->with('role', $this->role)
					->with('employee_id', $employee_id)
					->with('data', $data);

	}

	public function postEditAssignStaffs($id) {

		$data = Input::all();

		AccessController::allowedOrNot('transportation', 'can_edit');
		
		$validator  =Validator::make(Input::all(), array(
			'employee_id'			=> 'required|exists:admins,admin_details_id|unique:transportation_staffs,employee_id'
			

			));	

		if ($validator->fails())
		{

			Session::flash('error-msg', 'Staff Id is invalid');
			return Redirect::back()
							->withErrors($validator)
							->withInput();
		}					
		
		$data = TransportationStaff::find($id);
		$data->transportation_id = Input::get('transportation_id');
		$data->fee 				 = Input::get('fee');
		$data->is_active		 = Input::get('is_active');
		$data->save();


		
		$employee_username = Input::get('employee_id');
		

		Admin::where('admin_details_id', $data->employee_id)->update(['username' => $employee_username ]);

		// the visible student ID is username!!
		Session::flash('success-msg', 'Staff Edited Successfully');
		return View::make($this->view.'edit-assign-staffs')
					->with('role', $this->role)
					->with('employee_username', $employee_username)
					->with('data', $data);

	
	}

	public function getStudentList()
	{
		AccessController::allowedOrNot('transportation', 'can_view');
		$bus_id = Input::get('bus_id');

		if($bus_id == 0)
		{
			$assigned_students = DB::table('transportation_students')
								->join('transportation', 'transportation.id','=','transportation_students.transportation_id')
								->join('student_registration','student_registration.id','=','transportation_students.student_id')
								->select('transportation.bus_code','transportation_students.student_id','student_registration.student_name','student_registration.last_name','transportation_students.fee_amount','transportation.unique_transportation_id','transportation_students.id')
								->where('transportation.is_active', 'yes')
								->where('student_registration.is_active', 'yes')
								->where('transportation_students.is_active', 'yes')
								->get();		


			return View::make($this->view.'student-list')
						->with('assigned_students', $assigned_students);


		}	
		else
		{
			$assigned_students = DB::table('transportation_students')
								->join('transportation', 'transportation.id','=','transportation_students.transportation_id')
								->join('student_registration','student_registration.id','=','transportation_students.student_id')
								->select('transportation.bus_code','transportation_students.student_id','student_registration.student_name','student_registration.last_name','transportation_students.fee_amount','transportation.unique_transportation_id','transportation_students.id')
								->where('transportation.id',$bus_id)
								->where('transportation.is_active', 'yes')
								->where('student_registration.is_active', 'yes')
								->where('transportation_students.is_active', 'yes')
								->get();

			return View::make($this->view.'student-list')
						->with('assigned_students', $assigned_students);
		}

	}

	public function getTransportationStaff()

	{
		AccessController::allowedOrNot('transportation', 'can_view');	

		$bus_id = Input::get('bus_id', 0);
			
		if($bus_id == 0)
		{
			$assigned_staffs =  DB::table('transportation_staffs')
							->join('transportation', 'transportation.id','=','transportation_staffs.transportation_id')
							->join('employees', 'employees.id','=','transportation_staffs.employee_id')
							->select('transportation.bus_code','transportation_staffs.employee_id','employees.employee_name','transportation_staffs.fee','transportation.unique_transportation_id','transportation_staffs.id')
							->where('transportation.is_active', 'yes')
							->where('employees.is_active', 'yes')
							->where('transportation_staffs.is_active', 'yes')
							->get();
			return View::make($this->view.'staff-list')
						->with('assigned_staffs', $assigned_staffs);
		}
		else
		{
			$assigned_staffs = DB::table('transportation_staffs')
							->join('transportation', 'transportation.id','=','transportation_staffs.transportation_id')
							->join('employees', 'employees.id','=','transportation_staffs.employee_id')
							->select('transportation.bus_code','transportation_staffs.employee_id','employees.employee_name','transportation_staffs.fee','transportation.unique_transportation_id', 'transportation_staffs.id')
							->where('transportation.id',$bus_id)
							->where('transportation.is_active', 'yes')
							->where('employees.is_active', 'yes')
							->where('transportation_staffs.is_active', 'yes')
							->get();

			return View::make($this->view.'staff-list')
						->with('assigned_staffs', $assigned_staffs);

		}
		
	}

	
	public function deleteTransportation($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$model = new Transportation;
		//$id = Input::get('id');
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

	public function deleteTransportationStudent($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$model = new TransportationStudent;
		//$id = Input::get('id');
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

	public function postDeleteAssginedStaff($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');

		$data = TransportationStaff::find($id);
		$data->delete();
		Session::flash('success-msg', 'Successfully Deleted');
		return Redirect::back();
	}
}
