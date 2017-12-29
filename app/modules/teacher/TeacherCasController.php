<?php

use Carbon\Carbon;

class TeacherCasController extends BaseController
{
	protected $view = 'teacher.views.';

	protected $model_name = 'TeacherCas';

	protected $module_name = 'teacher';

	protected $role;

	

	

	public function postTeacherSubTopicsCreateEdit($subject_id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_create,can_edit');
			$input = Input::all();
			
		
			try
			{
				DB::connection()->getPdo()->beginTransaction();
				if(isset($input['id']))
				{
					CasSubTopics::whereNotIn('id', $input['id'])
								->where('subject_id', $subject_id)
								->delete();

					foreach($input['id'] as $index => $id)
					{
						if($id)
						{
							$this->updateInDatabase(['id' => $id, 'subject_id' => $subject_id, 'topic_name' => $input['topic_name'][$index], 'topic_description' => $input['topic_description'][$index]]);	
						}
						else
						{
							$this->storeInDatabase(['topic_name' => $input['topic_name'][$index], 'topic_description' => $input['topic_description'][$index], 'subject_id' => $subject_id]);
						}
						
					}
					Session::flash('success-msg', 'Successfully updated');
				}
				else
				{
					foreach($input['topic_name'] as $index => $topic_name)	
					{
						$data_to_store = [];
						$data_to_store['subject_id'] = $subject_id;
						$data_to_store['topic_name'] = $input['topic_name'][$index];
						$data_to_store['topic_description'] = $input['topic_description'][$index];

						$this->storeInDatabase($data_to_store);
					}

					Session::flash('success-msg', 'Successfully created');
				}

				DB::connection()->getPdo()->commit();
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', $e->getMessage());
				echo $e->getMessage();
				die();
			}
			
			
			return Redirect::back();
	}


	public function postTeacherCasSubTopicMarks($subject_id)

	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$input = Input::all();

			try
			{
				DB::connection()->getPdo()->beginTransaction();
				
				CasExamMark::where('exam_id', $input['exam_id'])
							->where('session_id', $input['session_id'])
							->where('section_id', $input['section_id'])
							->where('class_id', $input['class_id'])
							->where('sub_topic_id', $input['sub_topic_id'])
							->whereIn('student_id', $input['student_id'])
							->delete();

				foreach($input['exam_marks'] as $index =>  $cas_exam_marks)
				{
					$this->storeInDatabase(['exam_id' => $input['exam_id'], 'student_id' => $input['student_id'][$index], 'session_id' => $input['session_id'], 'class_id' => $input['class_id'], 'section_id' => $input['section_id'], 'sub_topic_id' => $input['sub_topic_id'], 'comments' => '', 'sub_topic_marks' => $cas_exam_marks], 'CasExamMark');
				}

				Session::flash('success-msg', 'Marks successfully assigned');

				DB::connection()->getPdo()->commit();	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
				Session::flash('error-msg', $e->getMessage());
				DB::connection()->getPdo()->rollback();
			}

			$url = URL::route('teacher-cas-subtopic-assign-get', $subject_id);
			$url .= '?sub_topic_id='.$input['sub_topic_id'].'&current_exam_id='.$input['exam_id'];

			return Redirect::to($url);

			
			
	}
}
