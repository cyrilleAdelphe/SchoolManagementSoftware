<?php

class ExamConfigurationController extends BaseController
{
	protected $view = 'exam-configuration.views.';

	protected $model_name = 'ExamConfiguration';

	protected $module_name = 'exam-configuration';

	public $role;

	public $current_user;

	
	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'exam_name',
										'alias'			=> 'Event'
									),

									array
									(
										'column_name' 	=> 'session_name',
										'alias'			=> 'Session'
									),

									array
									(
										'column_name' 	=> 'exam_start_date_in_ad',
										'alias'			=> 'Start Date'
									),

									array
									(
										'column_name' 	=> 'exam_end_date_in_ad',
										'alias'			=> 'End Date'
									),

									array
									(
										'column_name' 	=> 'weightage',
										'alias'			=> 'Weightage'
									),

									array
									(
										'column_name' 	=> 'remarks',
										'alias'			=> 'Remarks'
									),
								 );

	public function deleteExamConfiguration($id)
	{
		AccessController::allowedOrNot('exam-configuration', 'can_delete');
		$model = new $this->model_name;
		
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

	public function admitCard($id) 
	{
		AccessController::allowedOrNot('exam-configuration', 'can_generate_admit_card');
		
		$exam = ExamConfiguration::find($id);
		if (!$exam)
		{
			App::abort(404);
		}

		$data = ExamConfiguration::join(
			Student::getTableName(),
			Student::getTableName() . '.current_session_id', '=',
			ExamConfiguration::getTableName() . '.session_id'
		)->join(
			StudentRegistration::getTableName(),
			StudentRegistration::getTableName() . '.id', '=',
			Student::getTableName() . '.student_id'
		)->join(
			Classes::getTableName(),
			Classes::getTableName() . '.id', '=', 
			Student::getTableName() . '.current_class_id'
		)->join(
			Section::getTableName(),
			Section::getTableName() . '.section_code', '=',
			Student::getTableName() . '.current_section_code'
		)->groupBy(
			Student::getTableName() . '.student_id'
		)->where(
			ExamConfiguration::getTableName() . '.id', $id
		)->select(
			//////////// ExamConfigurate-v1-changes-made-here ///////
			StudentRegistration::getTableName() . '.student_name', 'last_name',
			//////////// ExamConfigurate-v1-changes-made-here ///////
			Student::getTableName() . '.current_roll_number',
			Classes::getTableName() . '.class_name',
			Section::getTableName() . '.section_name'
		)->orderBy(
			Classes::getTableName() . '.sort_order', 'ASC'
		)->get();

		// echo '<pre>'; print_r($data->toArray()); die();

		return View::make($this->view . 'admit-card')
			->with('exam', $exam)
			->with('data', $data);
	}

	public function generateExamListFromSessionId()
	{
		$session_id = Input::get('session_id', 0);
		$default_exam_id = Input::get('exam_id', 0);

		$exams = ExamConfiguration::where('session_id', $session_id)
									->lists('exam_name', 'id');

		$html = '<option value = "0">Root</option>';
		foreach($exams as $exam_id => $exam_name)
		{
			$checked = $exam_id == $default_exam_id ? 'checked' : '';
			$html .= '<option value = "'.$exam_id.'" '.$checked.' >'.$exam_name.'</option>';
		}

		return $html;
	}

}