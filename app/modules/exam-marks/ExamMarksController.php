<?php

class ExamMarksController extends BaseController
{
	protected $view = 'exam-marks.views.';

	protected $model_name = 'ExamMarks';

	protected $module_name = 'grade';

	protected $role;

	public function getUpdate()
	{
		AccessController::allowedOrNot('exam-marks', 'can_view');
		$student_marks = '';
		$last_updated_data = ExamMarks::getLastUpdated($condition = array(/*array('field_name' => 'is_active', 'operator' => '=', 'compare_value' => 'yes')*/), $fields_required = array('updated_by', 'id'), 'ExamMarks');

		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$subject_id = Input::get('subject_id', 0);
		$exam_id = Input::get('exam_id', 0);
		$session_id = Input::get('session_id', HelperController::getCurrentSession());
		
		$response = ExamMarksHelperController::getExamMarksList($class_id, $section_id, $subject_id, $exam_id, $session_id);


		$response = json_decode($response);

		return View::make($this->view . 'update')
						->with('role', $this->role)
						->with('student_marks', $response->student_marks)
						->with('last_updated_data', $last_updated_data)
						->with('full_marks_pass_marks', $response->full_marks_pass_marks);
	}

	public function postUpdate()
	{
		AccessController::allowedOrNot('exam-marks', 'can_create,can_edit');
		
		$response = ExamMarksHelperController::apiPostUpdateMarks(Input::all());

		$response = json_decode($response, true);

		Session::flash($response['status'].'-msg', $response['message']);

		$class_id = Input::get('default_class');
		$section_id = Input::get('default_section');
		$subject_id = Input::get('default_subject');
		
		return Redirect::back();
	}

}

