<?php

class PdrController extends BaseController
{
	protected $view = 'pdr.views.';

	protected $model_name = 'Pdr';

	protected $module_name = 'pdr';

	public $role;

	public $current_user;

	
	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'session_name',
										'alias'			=> 'Session'
									),

									array
									(
										'column_name' 	=> 'class_name',
										'alias'			=> 'Class'
									),

									array
									(
										'column_name' 	=> 'section_code',
										'alias'			=> 'Section'
									),

									array
									(
									 	'column_name' 	=> 'pdr_date',
										'alias'			=> 'Date'
									)
								 );

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');

		$input_parameters = $this->getInputParameters();

		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('input_parameters', $input_parameters);
	}

	public function partialsGetCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		

		$input_parameters = $this->getInputParameters();

		$check_if_pdr_exists_for_given_date_class_and_section = Pdr::where('pdr_date', $input_parameters['date'])
		->where('session_id', $input_parameters['session_id'])
		->where('class_id', $input_parameters['class_id'])
		->where('section_id', $input_parameters['section_id'])
		->first();

		
		if($check_if_pdr_exists_for_given_date_class_and_section)
		{
			$data = [];
			$message = 'PDR already created for given date';
		}
		else
		{
			$data = (new Pdr)->getCreateViewData($input_parameters);
			$message = '';	
		}

		
		return View::make($this->view.'partials.create')
					->with('data', $data)
					->with('message', $message)
					->with('input_parameters', $input_parameters);
		
	}

	public function apiPostCreateView($data)
	{
		$check_if_pdr_exists_for_given_date_class_and_section = Pdr::where('pdr_date', $data['pdr_date'])
		->where('session_id', $data['session_id'])
		->where('class_id', $data['class_id'])
		->where('section_id', $data['section_id'])
		->first();

		if($check_if_pdr_exists_for_given_date_class_and_section)
		{
			//Session::flash('error-msg', 'PDR already created for given date');
			return ['status' => 'error', 'message' => 'PDR already created for given date'];
			/*return Redirect::back()
							->withInput();*/
		}

		$result = $this->validateInput($data);
		
		if($result['status'] == 'error')
		{
			$result['message'] = 'Validation error has occured';
			return $result;
		}

		$data_to_store = [];
		$json = [];
		foreach($data['subject_name'] as $index => $subject_name)
		{
			$json[] = ['subject_name' => $subject_name, 'chapter' => $data['chapter'][$index], 'class_activity' => $data['class_activity'][$index], 'learning_achievement' => $data['learning_achievement'][$index], 'homework' => $data['homework'][$index], 'comment' => $data['comment'][$index]];
		}

		$data_to_store['pdr_details'] = json_encode($json);
		$data_to_store['pdr_date'] = $data['pdr_date'];
		$data_to_store['class_id'] = $data['class_id'];
		$data_to_store['session_id'] = $data['session_id'];
		$data_to_store['section_id'] = $data['section_id'];
		$data_to_store['is_active'] = $data['is_active'];
		
		
		try
		{
			$id = $this->storeInDatabase($data_to_store);	

			$success = 'success';
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(PDOException $e)
		{
			$success = 'error';
			$msg = $e->getMessage();
		}

		return ['status' => $success, 'message' => $msg];
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$status = $this->apiPostCreateView($data);

		Session::flash($status['status'].'-msg', $status['message']);

		
		if($status['status'] == 'error')
			return Redirect::back();
		else
			return Redirect::route('pdr-list');
		
		//return $this->redirectAction($success, 'create', $param, $msg);
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

		$data_to_store = [];
		foreach($data['subject_name'] as $index => $subject_name)
		{
			$json[] = ['subject_name' => $subject_name, 'chapter' => $data['chapter'][$index], 'class_activity' => $data['class_activity'][$index], 'learning_achievement' => $data['learning_achievement'][$index], 'homework' => $data['homework'][$index], 'comment' => $data['comment'][$index]];
		}

		$data_to_store['pdr_details'] = json_encode($json);
		
		$data_to_store['id'] = $id;

		try
		{
			$id = $this->updateInDatabase($data_to_store);	

			

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

	private function getInputParameters()
	{
		$input_parameters = [];
		$input_parameters['date'] = Input::get('pdr_date' , date('Y-m-d'));
		$input_parameters['session_id'] = Input::get('session_id' , 0);
		$input_parameters['class_id'] = Input::get('class_id' , 0);
		$input_parameters['section_id'] = Input::get('section_id' , 0);

		return $input_parameters;
	}

	public function getPdrFeedBackListView($pdr_id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');

		$columns_to_show = [
							['column_name' => 'student_name',
							 'alias'	=>	'Student Name'],
							['column_name'	=>	'guardian_name',
							 'alias'	=>	'Guardian Name'],
							['column_name'	=>	'feedback',
							  'alias'	=>	'Feedback'],

							];
		
		$model = new PdrFeedback;
		$queryString = $this->getQueryString();
		$queryString['pdr_id'] = $pdr_id;
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns($columns_to_show);
		$tableHeaders = $this->getTableHeader($columns_to_show);
		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'pdr-feedback-list')
					//->with('module_name', $this->module_name)
					//->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					->with('pdr_id', $pdr_id);
					//->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					//->with('role', $this->role);
	}

	public function getFeedBackViewview($id)
	{

		AccessController::allowedOrNot($this->module_name, 'can_view');
		$model = new PdrFeedback;
		
		$data = $model->getViewViewData($id);

		/*$house_name = DB::table('houses')
						->join('student_registration','student_registration.house_id','=','houses.id')
						->where('student_registration.id',$id)
						->pluck('houses.house_name');*/



		return View::make($this->view.'pdr-feedback-view')
					//->with('module_name', $this->module_name)
					//->with('role', $this->role)
					//->with('current_user', $this->current_user)
					->with('data', $data);
					//->with('actionButtons', $this->getActionButtons());
					//->with('house_name', $house_name);

	
	}

}
