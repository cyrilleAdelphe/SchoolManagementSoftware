<?php

class PushNotificationsController extends BaseController
{
	protected $view = 'push-notifications.views.';

	protected $model_name = 'PushNotifications';

	protected $module_name = 'push-notifications';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'name',
										'alias'			=> 'Name'
									),
									array
									(
										'column_name' 	=> 'gcm_id',
										'alias'			=> 'Gcm'
									)
								 );

	public function postCreateView()
	{
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
			PushNotificationsHelperController::createAssignmentFolder($this, $data, $id);
			
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

}
