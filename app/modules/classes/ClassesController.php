<?php

class ClassesController extends BaseController
{
	protected $view = 'classes.views.';

	protected $model_name = 'Classes';

	protected $module_name = 'classes';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'session_name',
										'alias'			=> 'Session'
									),
									array
									(
										'column_name' 	=> 'class_name',
										'alias'			=> 'Class Name'
									),
									array
									(
										'column_name' 	=> 'class_code',
										'alias'			=> 'Class Code'
									),
									array
									(
										'column_name' 	=> 'sort_order',
										'alias'			=> 'Order'
									)
								 );

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
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->storeInDatabase($data);	
			
			DB::connection()->getPdo()->commit();
			
			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function ajaxGetClasses()
	{
		if(!Input::has('academic_session_id')) return;

		$classes = Classes::where('academic_session_id', Input::get('academic_session_id'))
							->get();

		return json_encode($classes);
	}
}
