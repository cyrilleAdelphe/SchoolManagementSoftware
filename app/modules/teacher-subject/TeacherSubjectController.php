<?php

class TeacherSubjectController extends BaseController
{
	protected $view = 'teacher-subject.views.';

	protected $model_name = 'TeacherSubject';

	protected $module_name = 'teacher-subject';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'class_name',
										'alias'			=> 'Class Name'
									),
									array
									(
										'column_name' 	=> 'subject_code',
										'alias'			=> 'Subject Code'
									)
								 );

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$model = new $this->model_name;
		$data = $model->getCreateViewData();
		$actionButtons = $this->getActionButtons();

		return View::make($this->view.'create')
					->with('role', $this->role)
					->with('module_name', $this->module_name)
					->with('data', $data)
					->with('actionButtons', $actionButtons);
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		
		$data = Input::all();

		try
		{
			DB::connection()->getPdo()->beginTransaction();
			
			//empty the table
			TeacherSubject::where('id', '>', 0)->delete();
			
			foreach($data['class_id'] as $class_id)
			{
				
				$dataToStore = array();
				$dataToStore['is_active'] = $data['is_active'];
				$dataToStore['class_id'] = $class_id;
				if(isset($data['subjects_of_class_'.$class_id]))
				{
					foreach($data['subjects_of_class_'.$class_id] as $subject_code)
					{
						$dataToStore['subject_code'] = $subject_code;
						$this->storeInDatabase($dataToStore);
					}
				}
				
			}
			DB::connection()->getPdo()->commit();
			$success = true;
			$msg = 'Record successfully created';
			Session::flash('success-msg', ConfigurationController::translate($msg));
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
			Session::flash('error-msg', ConfigurationController::translate($msg));
		}

		return Redirect::route('teacher-subject-create-get');
	}
}
