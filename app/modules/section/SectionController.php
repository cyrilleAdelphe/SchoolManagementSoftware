<?php

class SectionController extends BaseController
{
	protected $view = 'section.views.';

	protected $model_name = 'Section';

	protected $module_name = 'section';

	protected $role;

	public $columnsToShow = array(
		array
		(
			'column_name' 	=> 'section_name',
			'alias'			=> 'Section Name'
		),
		array
		(
			'column_name' 	=> 'section_code',
			'alias'			=> 'Section Code'
		)
	);

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
			DB::connection()->getPdo()->beginTransaction();

			$model = $this->model_name;
			$previous_data = $model::find($data['id']);
			
			$id = $this->updateInDatabase($data);	

			
			if ($previous_data->section_code != $data['section_code']) 
			{
				// update other tables with section_code as a field

				$other_tables = array(
					array(
						'model' => 'ClassSection', 
						'column' => 'section_code'
					),
					array(
						'model' => 'Student', 
						'column' => 'current_section_code'
					),
					array(
						'model' => 'StudentRegistration',
						'column' => 'registered_section_code'
					),
					array(
						'model' => 'Teacher', 
						'column' => 'section_code'
					),
				);

				// Find a way to update all the rows without having to loop
				foreach ($other_tables as $table)
				{
					$records = $table['model']::where($table['column'], $previous_data->section_code)
						->get();
					foreach ($records as $record)
					{
						$record->$table['column'] = $data['section_code'];
						$record->save();
					}
				}
				

			}

			DB::connection()->getPdo()->commit();

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
