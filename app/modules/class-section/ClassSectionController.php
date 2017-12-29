<?php

class ClassSectionController extends BaseController
{
	protected $view = 'class-section.views.';

	protected $model_name = 'ClassSection';

	protected $module_name = 'class-section';

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
										'column_name' 	=> 'section_code',
										'alias'			=> 'Section Code'
									)
								 );

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create,can_edit');
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
		AccessController::allowedOrNot($this->module_name, 'can_create,can_edit');
		$success = false;
		$msg = '';
		
		$data = Input::all();

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			

			$classes = Classes::where('academic_session_id', $data['academic_session_id'])
								->lists('id');

			foreach($classes as $class)
			{
				ClassSection::where('class_id',$class)->delete();
			}
			
			
			foreach($data['class_id'] as $class_id)
			{
				
				$dataToStore = array();
				$dataToStore['is_active'] = $data['is_active'];
				$dataToStore['class_id'] = $class_id;
				if(isset($data['sections_of_class_'.$class_id]))
				{
					foreach($data['sections_of_class_'.$class_id] as $section_code)
					{
						$dataToStore['section_code'] = $section_code;
						$dataToStore['section_id'] = Section::where('section_code',$section_code)->first()->id;
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

		return Redirect::to(
			URL::route('class-section-create-get').'?academic_session_id='.$data['academic_session_id']
		);
	}
}
