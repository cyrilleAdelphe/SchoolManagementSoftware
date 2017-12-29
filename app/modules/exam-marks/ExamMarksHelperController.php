<?php

class ExamMarksHelperController
{
	public static function apiListAllRelatedSubjects($user_id = 0, $role = 'admin')
	{
		
		$table1 = SubjectTeacher::getTableName();
		$table2 = ExamDetails::getTableName();
		$table3 = Subject::getTableName();
		$table4 = Classes::getTableName();
		$table5 = Section::getTableName();
		$table6 = ExamConfiguration::getTableName();
		$current_session = HelperController::getCurrentSession();

		if($role == 'superadmin')
		{
			$data = DB::table($table3)
					->join($table2, $table2.'.subject_id', '=', $table3.'.id')
					->join($table4, $table4.'.id', '=', $table3.'.class_id')
					->join($table5, $table5.'.id', '=', $table3.'.section_id')
					->join($table6, $table6.'.id', '=', $table2.'.exam_id')
					->where($table6.'.session_id', $current_session)
					->select('exam_id', 'exam_name', $table3.'.class_id', $table3.'.section_id', $table3.'.id as subject_id', 'exam_name', 'class_name', 'section_code', 'subject_name', $table2.'.pass_marks', $table2.'.full_marks', $table2.'.practical_full_marks', $table2.'.practical_pass_marks')
					->orderBy('exam_name', 'ASC')
					->orderBy('class_id', 'ASC')
					->orderBy('section_id', 'ASC')
					->get();
		}
		else
		{

			$data = DB::table($table1)
					->join($table2, $table2.'.subject_id', '=', $table1.'.subject_id')
					->join($table3, $table3.'.id', '=', $table2.'.subject_id')
					->join($table4, $table4.'.id', '=', $table1.'.class_id')
					->join($table5, $table5.'.id', '=', $table1.'.section_id')
					->join($table6, $table6.'.id', '=', $table2.'.exam_id')
					->where($table1.'.session_id', $current_session)
					->where($table6.'.session_id', $current_session)
					->where('teacher_id', $user_id)
					->select('exam_id', 'exam_name', $table1.'.class_id', $table1.'.section_id', $table1.'.subject_id', 'exam_name', 'class_name', 'section_code', 'subject_name', $table2.'.pass_marks', $table2.'.full_marks', $table2.'.practical_full_marks', $table2.'.practical_pass_marks')
					->get();
		}
		
		return json_encode($data);

		//check if class teacher or not
		//
	}

	public static function getExamMarksList($class_id = 0, $section_id = 0, $subject_id = 0, $exam_id = 0, $session_id = 0)
	{

		//$class_id, $section_id, $subject_id, $exam_id, $session_id
		$full_marks_pass_marks = new stdClass;
		$full_marks_pass_marks->full_marks = 0;
		$full_marks_pass_marks->pass_marks = 0;
		$full_marks_pass_marks->practical_full_marks = 0;
		$full_marks_pass_marks->practical_pass_marks = 0;

		$session_id = $session_id ? $session_id : HelperController::getCurrentSession();
		$student_marks = array();
		
		
		if($class_id && $section_id && $subject_id && $exam_id)
		{
			$section_code = Section::where('id', $section_id)->first()['section_code'];


			$exam_marks_table = ExamMarks::getTableName();
			$student_registration_table = StudentRegistration::getTableName();
			$student_table = Student::getTableName();
			$subject_table = Subject::getTableName();
			$section_table = Section::getTableName();

			$marks = DB::table($exam_marks_table)
									->rightJoin($subject_table, $subject_table.'.id', '=', $exam_marks_table.'.subject_id')
									->join($section_table, $section_table.'.id', '=', $subject_table.'.section_id')
									->where($subject_table.'.id', $subject_id)
									->where($exam_marks_table . '.session_id', $session_id)
									->where('exam_id', $exam_id)
									->select($exam_marks_table.'.marks',$exam_marks_table.'.practical_marks',
											$exam_marks_table.'.comments',
											$subject_table.'.id as subject_id',
											$exam_marks_table.'.student_id')
									->get();

			$students = DB::table($student_table)
								->join($student_registration_table, $student_registration_table.'.id','=', $student_table.'.student_id')
								->where($student_table.'.current_class_id', $class_id)
								->where($student_table.'.current_section_code', $section_code)
								->select($student_table.'.current_roll_number', 
										$student_registration_table.'.student_name',
										 $student_registration_table.'.last_name',
										$student_table.'.student_id')
								->orderBy($student_table . '.current_roll_number', 'ASC')
								->get();

			//dd($students);

			foreach($students as $student)
			{
				$record = new stdClass;

				$record->student_name = $student->student_name;
				$record->last_name = $student->last_name;
				$record->student_id = $student->student_id;
				$record->current_roll_number = $student->current_roll_number;
				$record->subject_id = $subject_id;
				
				$record->marks = 0;
				$record->practical_marks = 0;
				$record->comments = '';

				foreach($marks as $mark)
				{
					if($mark->student_id == $student->student_id)
					{
						$record->marks = $mark->marks;
						$record->practical_marks = $mark->practical_marks;
						$record->comments = $mark->comments;
						continue;
					}
				}

				$student_marks[] = $record;
			}

			//also get max and min marks
			//exam_id, class_id, section_id
			$full_marks_pass_marks = ExamDetails::where('exam_id', $exam_id)
												->where('subject_id', $subject_id)
												->where('is_active', 'yes')
												->select('pass_marks', 'full_marks', 'practical_full_marks', 'practical_pass_marks')
												->first();
												
			if($full_marks_pass_marks)
			{
				$full_marks_pass_marks = $full_marks_pass_marks;
			}
			else
			{
				$full_marks_pass_marks = new stdClass;
				$full_marks_pass_marks->full_marks = 0;
				$full_marks_pass_marks->pass_marks = 0;
				$full_marks_pass_marks->practical_full_marks = 0;
				$full_marks_pass_marks->practical_pass_marks = 0;
			}


		}


		$response = array('student_marks' => $student_marks, 'full_marks_pass_marks' => $full_marks_pass_marks);

		return  json_encode($response);
	}

	public static function apiPostUpdateMarks($data_to_store = array())
	{
		//$data_to_store = Input::all();
		//dd($data_to_store);
		$success = false;
		$msg = 'No data sent';

		if(count($data_to_store))
		{
			$no_of_entries = sizeof($data_to_store['marks']);

			try
			{
				DB::connection()->getPdo()->beginTransaction();
			
				for($i=0; $i<$no_of_entries; $i++)
				{
					$record = array();
					$record['marks'] = $data_to_store['marks'][$i];
					$record['practical_marks'] = $data_to_store['practical_marks'][$i];
					$record['subject_id'] = $data_to_store['subject_id'][$i];
					$record['student_id'] = $data_to_store['student_id'][$i];
					$record['comments'] = $data_to_store['comments'][$i];
					$record['exam_id'] = $data_to_store['exam_id'];
					$record['session_id'] = $data_to_store['session_id'];
					$record['class_id'] = $data_to_store['class_id'];
					$record['section_id'] = $data_to_store['section_id'];

					$previous_record = ExamMarks::where('subject_id', $record['subject_id'])
										->where('student_id', $record['student_id'])
										->where('exam_id', $record['exam_id'])
										->where('session_id', $record['session_id'])
										->where('class_id', $record['class_id'])
										->where('section_id', $record['section_id'])
										->first();
					if($previous_record)
					{
						$record['id'] = $previous_record->id;
						(new ExamMarksController)->updateInDatabase($record, $condition = array(), $modelName = 'ExamMarks');
						//$this->updateInDatabase($record);
					}
					else
					{
						(new ExamMarksController)->storeInDatabase($record, 'ExamMarks');		
					}
						
				}
				DB::connection()->getPdo()->commit();
				$success = true;
				$msg = 'Successfully updated';
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				$success = false;
				$msg = $e->getMessage();
			}	
		}

		

		$return = array();
		$return['status'] = $success ? 'success' : 'error';
		$return['message'] = $msg;
		return json_encode($return);
	}

	
	//public static function checkIfAllowedToEditMarks($user_id, $role, $class_id, $section_id, )
}