<?php

	class CasSubTopicsController extends BaseController
	{


		protected $view = 'cas.views.';
		protected $model_name = 'CasSubTopics';
		protected $module_name = 'cas';

		public $current_user;

		public function getSubTopicsListView()
		{
			return View::make($this->view.'sub-topics.list');
		}

		public function getSubTopicsCreateEditView($subject_id)
		{
			$sub_topics_table = CasSubTopics::getTableName();
			$data = DB::table($sub_topics_table)
							->where('subject_id', $subject_id)
							->get();

			return View::make($this->view.'.partials.sub-topics.sub-topics-create-edit')->with('data', $data)->with('subject_id', $subject_id);
		}

		public function postSubTopicsCreateEditView($subject_id)
		{
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
							$this->updateInDatabase(['id' => $id, 'subject_id' => $subject_id, 'topic_name' => $input['topic_name'][$index], 'topic_description' => $input['topic_description'][$index], 'weightage' => $input['topic_weightage'][$index],
								'full_marks' => $input['topic_full'][$index]]);	
						}
						else
						{
							$this->storeInDatabase(['topic_name' => $input['topic_name'][$index], 'topic_description' => $input['topic_description'][$index], 'subject_id' => $subject_id, 'weightage' => $input['topic_weightage'][$index],
								'full_marks' => $input['topic_full'][$index]]);
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
						$data_to_store['weightage'] = $input['topic_weightage'][$index];
						$data_to_store['full_marks'] = $input['topic_full'][$index];

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
				//echo $e->getMessage();
				//die();
			}
			
			
			return Redirect::back();
		}



		/////////////// these are for apis
		public function getClassIdsFromSessionId()
		{
			$session_id = Input::get('session_id');
			$default_class_id = Input::get('default_class_id');

			$class_ids = Classes::where('academic_session_id', $session_id)
								->where('is_active', 'yes')
								->select('id', 'class_name')
								->orderBy('sort_order', 'ASC')
								->get();

			$html = '';
			foreach($class_ids as $c)
			{
				$sel = $c->id == $default_class_id ? 'selected' : '';
				$html .= '<option value = "'.$c->id.'" '.$sel.'>'.$c->class_name.'</option>';
			}

			return $html;
		}

		public function getClassIdsFromSessionIdAndClassId()
		{
			$session_id = Input::get('session_id');
			$class_id = Input::get('class_id');
			$default_section_id = Input::get('default_section_id');

			$class_table = Classes::getTableName();
			$section_table = Section::getTableName();
			$class_section_table = ClassSection::getTableName();

			$section_ids = DB::table($class_section_table)
								->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
								->where('class_id', $class_id)
								->select($section_table.'.id', $class_section_table.'.section_code')
								->get();

			$html = '';
			foreach($section_ids as $c)
			{
				$sel = $c->id == $default_section_id ? 'selected' : '';
				$html .= '<option value = "'.$c->id.'" '.$sel.'>'.$c->section_code.'</option>';
			}

			return $html;
		}

		///// CAS-v1-changes-made-here ////
		public function getSubjectIdsFromSessionIdClassIdAndSectionId()
		{
		
		$session_id = Input::get('session_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$section_code = Section::where('id', $section_id)->first()['section_code'];

		$subject_table = Subject::getTableName();
		$employee_table = Employee::getTableName();

		
		$current_user = HelperController::getCurrentUser();

		$map_subject_teacher_table = SubjectTeacher::getTableName();
		$data = DB::table($map_subject_teacher_table)
						->join($subject_table, $subject_table.'.id', '=', $map_subject_teacher_table.'.subject_id')
						->where($subject_table.'.class_id', $class_id)
						->where($subject_table.'.section_id', $section_id)
						->where($map_subject_teacher_table.'.teacher_id', $current_user->user_id)
						->select('subject_name', 'subject_code', 'sort_order', 'is_graded', 'include_in_report_card', $subject_table.'.id')
						->get();

		if(count($data) == 0 || Auth::superadmin()->check())
		{
			$data = DB::table($subject_table)
							->select('subject_name', 'subject_code', 'sort_order', 'is_graded', 'include_in_report_card', 'id')
							->where($subject_table.'.class_id', $class_id)
							->where($subject_table.'.section_id', $section_id)
							->orderBy('sort_order', 'ASC')
							->get();	
		}
		
								
			return View::make($this->view.'partials.sub-topics.subject-list')
						->with('data', $data)
						->with('session_id', $session_id)
						->with('class_id', $class_id)
						->with('section_id', $section_id);
		}
		///// CAS-v1-changes-made-here ////


		public function postAssignSubTopicMarks($subject_id)
		{
			$input = Input::all();
		
			try
			{
				DB::connection()->getPdo()->beginTransaction();
				
				foreach($input['exam_marks'] as $index =>  $cas_exam_marks)
				{
					$check = CasExamMark::where('exam_id', $input['exam_id'])
							->where('session_id', $input['session_id'])
							->where('section_id', $input['section_id'])
							->where('class_id', $input['class_id'])
							->where('sub_topic_id', $input['sub_topic_id'])
							->where('student_id', $input['student_id'][$index])
							->pluck('id');
					
					$ratio = 100/ $input['full_marks'];
					$convertedMarks = $cas_exam_marks * $ratio;
					if((int) $check)
					{
						$this->updateInDatabase(['id'=>$check, 'exam_id' => $input['exam_id'], 'student_id' => $input['student_id'][$index], 'session_id' => $input['session_id'], 'class_id' => $input['class_id'], 'section_id' => $input['section_id'], 'sub_topic_id' => $input['sub_topic_id'], 'comments' => '', 'sub_topic_marks' => $convertedMarks], [],'CasExamMark');

					}
					else
					{
						$this->storeInDatabase(['exam_id' => $input['exam_id'], 'student_id' => $input['student_id'][$index], 'session_id' => $input['session_id'], 'class_id' => $input['class_id'], 'section_id' => $input['section_id'], 'sub_topic_id' => $input['sub_topic_id'], 'comments' => '', 'sub_topic_marks' => $convertedMarks], 'CasExamMark');

					}
					
				}


				Session::flash('success-msg', 'Marks successfully assigned');

				DB::connection()->getPdo()->commit();	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
				Session::flash('error-msg', $e->getMessage());
				DB::connection()->getPdo()->rollback();
				die();
			}

			$url = URL::route('cas-sub-topics-assign-marks-get', $subject_id);
			$url .= '?sub_topic_id='.$input['sub_topic_id'].'&current_exam_id='.$input['exam_id'];

			return Redirect::to($url);
		}

		public function getAssignSubTopicMarks($subject_id)
		{
			//get subject_id
			//get class id and section id
			//get students
			$current_sub_topic_id = Input::get('sub_topic_id', 0);
			$current_exam_id = Input::get('current_exam_id', 0);
			$sub_topic_list = CasSubTopics::where('subject_id', $subject_id)
										->lists('topic_name', 'id');
			
			$data = ['current_sub_topic_id' => $current_sub_topic_id, 'current_exam_id' => $current_exam_id, 'sub_topic_list' => $sub_topic_list];

			return View::make($this->view.'sub-topics.assign')
						->with('data', $data)
						->with('subject_id', $subject_id);

		}

		public function apiGetExamListHtml()
		{
			$default_exam_id = Input::get('default_exam_id', 0);
			$sub_topic_id = Input::get('sub_topic_id');

			$sub_topic_table = CasSubTopics::getTableName();
			$subject_table = Subject::getTableName();
			$class_table = Classes::getTableName();

			$session_id = DB::table($sub_topic_table)
							->join($subject_table, $subject_table.'.id', '=', $sub_topic_table.'.subject_id')
							->join($class_table, $class_table.'.id', '=', $subject_table.'.class_id')
							->where($sub_topic_table.'.id', $sub_topic_id)
							->pluck('academic_session_id');

			$exam_table = ExamConfiguration::getTableName();
			$exam_list = DB::table($exam_table)
							->where('session_id', $session_id)
							->lists('exam_name', 'id');
			
			$html = '';
			foreach($exam_list as $id => $val)
			{

				$html .= '<option value = "'.$id.'" ';
				if($id == $default_exam_id)
				{
					$html .= 'selected ';
				}
				$html .='>'.$val.'</option>';
			}
			return $html;
		}

		public function apiGetStudentAssignSubTopicMark()
		{
			$sub_topic_id = Input::get('sub_topic_id', 0);
			$exam_id = Input::get('exam_id', 0);

			$full_marks = CasSubTopics::where('id', $sub_topic_id)
										->pluck('full_marks');

			$data = ['data' => []];

			if($sub_topic_id)
				$data = $this->apiGetStudentAssignSubTopicMarkData($sub_topic_id, $exam_id);

			return View::make($this->view.'partials.sub-topics.student-list')
						->with('data', $data)
						->with('full_marks', $full_marks);

		}

		public function apiGetStudentAssignSubTopicMarkData($sub_topic_id, $exam_id)
		{
			$sub_topic_table = CasSubTopics::getTableName();
			$subject_table = Subject::getTableName();
			$class_table = Classes::getTableName();
			$student_table = Student::getTableName();
			$student_registration = StudentRegistration::getTableName();
			$cas_exam_mark_table = CasExamMark::getTableName();

			$session_id_class_id_section_id = DB::table($sub_topic_table)
							->join($subject_table, $subject_table.'.id', '=', $sub_topic_table.'.subject_id')
							->join($class_table, $class_table.'.id', '=', $subject_table.'.class_id')
							->where($sub_topic_table.'.id', $sub_topic_id)
							->select('academic_session_id as session_id', 'class_id', 'section_id')
							->first();

			$full_marks = DB::table($sub_topic_table)
							->where($sub_topic_table.'.id', $sub_topic_id)
							->pluck('full_marks');
							



			$section_code = Section::where('id', (int) $session_id_class_id_section_id->section_id)
									->pluck('section_code');

								

			$data = DB::table($student_table)
						->leftJoin($cas_exam_mark_table, function($join) use ($student_table, $cas_exam_mark_table, $session_id_class_id_section_id, $section_code, $sub_topic_id, $exam_id)
						{
							$join->on($cas_exam_mark_table.'.student_id', '=', $student_table.'.student_id')
								->where('current_session_id', '=', $session_id_class_id_section_id->session_id)
								//->where('current_class_id', '=', $session_id_class_id_section_id->class_id)
								//->where('current_section_code', '=', $section_code)
								->where('exam_id', '=', $exam_id)
								->where('sub_topic_id', '=', $sub_topic_id);
						})
						->join($student_registration, $student_registration.'.id', '=', $student_table.'.student_id')
						->select('student_name', 'current_roll_number', $student_table.'.student_id', 'sub_topic_marks', 'last_name')
						->where('current_session_id', '=', $session_id_class_id_section_id->session_id)
						->where('current_class_id', '=', $session_id_class_id_section_id->class_id)
						->where('current_section_code', '=', $section_code)	
						->orderBy('current_roll_number', 'ASC')
						/*->where(function($query) use ($exam_id)
						{
							$query->where('exam_id', '=', $exam_id)
								  ->orWhere('exam_id', NULL);

						})
						->where(function($query) use ($sub_topic_id)
						{
							$query->where('sub_topic_id', '=', $sub_topic_id)
								  ->orWhere('sub_topic_id', NULL);

						})*/	
						->get();

			$return_data = [];

			$ratio = 100/ $full_marks;

			foreach($data as $index => $d)
			{	$revertedMarks = $d->sub_topic_marks / $ratio;
				$return_data[$d->student_id]['student_name'] = $d->student_name;
				$return_data[$d->student_id]['last_name'] = $d->last_name;
				$return_data[$d->student_id]['current_roll_number'] = $d->current_roll_number;
				$return_data[$d->student_id]['sub_topic_marks'] = $revertedMarks;
				unset($data[$index]);
			}

			return ['data' => $return_data, 'session_id_class_id_section_id' => $session_id_class_id_section_id];
		}
	}