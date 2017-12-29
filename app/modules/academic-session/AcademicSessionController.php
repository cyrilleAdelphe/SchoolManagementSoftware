<?php

define('ASSIGNMENT_FOLDER_CONFIG', app_path().'/modules/assignments/config/assignment_folder_config.json');

class AcademicSessionController extends BaseController
{
	protected $view = 'academic-session.views.';

	protected $model_name = 'AcademicSession';

	protected $module_name = 'academic-session';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'session_name',
										'alias'			=> 'Session Name'
									),
									array
									(
										'column_name' 	=> 'session_start_date_in_ad',
										'alias'			=> 'Start Date (AD)'
									),
									array
									(
										'column_name' 	=> 'session_start_date_in_bs',
										'alias'			=> 'Start Date (BS)'
									),
									array
									(
										'column_name' 	=> 'session_end_date_in_ad',
										'alias'			=> 'End Date (AD)'
									),
									array
									(
										'column_name' 	=> 'session_end_date_in_bs',
										'alias'			=> 'End Date (BS)'
									)

								 );

	protected function validateInput($data, $update = false, $modelName = '')
	{
		$result = parent::validateInput($data, $update, $modelName);


		if($data['is_current']=='yes')
		{
			$current_session = AcademicSession::where('is_current','yes')->first();

			if($current_session)
			{
				if ( !isset($data['id']) || $current_session->id != $data['id'] )
				{
					if($result['status']=='success' || !(bool)$result['data'])
					{
						$result['status'] = 'error';
						$result['data'] = new Illuminate\Support\MessageBag;
					}
					$result['data']->add('is_current',$current_session->session_name.' is set to current session');
				}
			}
		}

		return $result;
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();
		
		$data['session_start_date_in_bs'] = (new DateConverter)->ad2bs($data['session_start_date_in_ad']);
		$data['session_end_date_in_bs'] = (new DateConverter)->ad2bs($data['session_end_date_in_ad']);

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
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->storeInDatabase($data);	

			DB::connection()->getPdo()->commit();
			
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
		$data['session_start_date_in_bs'] = (new DateConverter)->ad2bs($data['session_start_date_in_ad']);
		$data['session_end_date_in_bs'] = (new DateConverter)->ad2bs($data['session_end_date_in_ad']);

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

	
}
