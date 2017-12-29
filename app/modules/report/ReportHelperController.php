<?php

class ReportHelperController 
{
	public static function getReportData($exam_id, $student_id) {
		$session_id = 0;
		$config = json_decode(File::get(REPORT_CONFIG_FILE));

		try
		{
			$session_id = ExamConfiguration::find($exam_id)->session_id;
		}
		catch (Exception $e)
		{
			// do nothing (session_id has already been set to 0)
		}

		$data = ExamMarks::join(
			Report::getTableName(), 
			Report::getTableName().'.student_id', '=', 
			ExamMarks::getTableName().'.student_id'
		)->join(
			ExamDetails::getTableName(), 
			ExamDetails::getTableName().'.subject_id', '=', 
			ExamMarks::getTableName().'.subject_id'
		)->join(
			Subject::getTableName(), 
			Subject::getTableName().'.id', '=', 
			ExamMarks::getTableName().'.subject_id'
		)->join(
			StudentRegistration::getTableName(), 
			StudentRegistration::getTableName().'.id', '=', 
			ExamMarks::getTableName().'.student_id'
		)->join(
			Users::getTableName(),
			Users::getTableName() . '.user_details_id', '=',
			StudentRegistration::getTableName() . '.id'
		)->join(
			Student::getTableName(),
			Student::getTableName() . '.student_id', '=',
			StudentRegistration::getTableName() . '.id'
		)
		->where(
			ExamMarks::getTableName() . '.exam_id', 
			$exam_id
		)->where(
			ExamMarks::getTableName() . '.session_id',
			$session_id
		)->where(
			ExamMarks::getTableName().'.student_id', 
			$student_id
		)->where(
			ExamDetails::getTableName().'.exam_id', 
			$exam_id
		)->where(
			Report::getTableName() . '.exam_id', 
			$exam_id
		)->where(
			Report::getTableName() . '.student_id', 
			$student_id
		)->where(
			Student::getTableName() . '.student_id', 
			$student_id
		)->where(
			Student::getTableName() . '.current_session_id', 
			$session_id
		)->where(
			ExamDetails::getTableName().'.is_active', 
			'yes'
		)->where(
			Subject::getTableName() . '.is_graded', 
			'yes'
		)->where(
			Users::getTableName() . '.role', 
			'student'
		)->select(
			Student::getTableName().'.current_roll_number', 'current_class_id as class_id',
	 		ExamMarks::getTableName().'.marks', 
	 		ExamMarks::getTableName().'.practical_marks', 'grade', 
	 		ExamDetails::getTableName().'.pass_marks', 
	 		ExamDetails::getTableName().'.full_marks', ExamMarks::getTableName().'.subject_id',
	 		ExamDetails::getTableName().'.practical_pass_marks', 
	 		ExamDetails::getTableName().'.practical_full_marks', 
	 		Subject::getTableName().'.subject_name', 
	 		Report::getTableName().'.*', 
	 		StudentRegistration::getTableName().'.student_name', StudentRegistration::getTableName().'.id as student_id',
	 		Users::getTableName() . '.username'
	 	)
	 	->orderBy('sort_order', 'ASC')
	 	->get();


	 	$cas_data = ReportHelperController::getCasMarksFromClassIdAndSectionId(['session_id' => $session_id, 'student_id' => $student_id, 'exam_id' => $exam_id]); //arguments have session_id, student_id exam_id

	 	if(isset($data[0]))
	 		$grade_conditions = GradeHelperController::getCasGradeSettings($session_id, $data[0]->class_id);
	 	else
	 		$grade_conditions = [];

	 	$return_data = [];
	 	$return_data['cas_total'] = 0;

	 	foreach($data as $index => $d)
	 	{
	 		$return_data['subjects'][$d->subject_id]['subject_name'] = $d->subject_name;
	 		$return_data['subjects'][$d->subject_id]['full_marks'] = $d->full_marks;
	 		$return_data['subjects'][$d->subject_id]['pass_marks'] = $d->pass_marks;
	 		$return_data['subjects'][$d->subject_id]['practical_pass_marks'] = $d->practical_pass_marks;
	 		$return_data['subjects'][$d->subject_id]['practical_full_marks'] = $d->practical_full_marks;
	 		$return_data['subjects'][$d->subject_id]['marks'] = $d->marks;
	 		$return_data['subjects'][$d->subject_id]['practical_marks'] = $d->practical_marks;
	 		


	 		//$return_data['subjects'][]
	 		if(isset($cas_data['cas_marks_data'][$d->student_id][$d->subject_id]))
	 		{
	 			$return_data['subjects'][$d->subject_id]['cas_marks'] =  $cas_data['cas_marks_data'][$d->student_id][$d->subject_id];
	 			$return_data['cas_total'] +=  $cas_data['cas_marks_data'][$d->student_id][$d->subject_id];
	 		}
	 		else
	 		{
	 			$return_data['subjects'][$d->subject_id]['cas_marks'] =  'NA';
	 		}

	 		

	 		$return_data['rank'] = $d->rank;
	 		$return_data['total_marks'] = $d->total_marks;
	 		$return_data['percentage'] = $d->percentage;
	 		$return_data['cgpa'] = $d->cgpa;
	 		$return_data['status'] = $d->status;
	 		$return_data['student_name'] = $d->student_name;
	 		$return_data['remarks'] = $d->remarks;

	 		$return_data['subjects'][$d->subject_id]['marks_percent'] = $return_data['subjects'][$d->subject_id]['marks'] / $return_data['subjects'][$d->subject_id]['full_marks'] * 100;

	 		$return_data['subjects'][$d->subject_id]['practical_marks_percent'] = $return_data['subjects'][$d->subject_id]['practical_full_marks'] == 0 ? 'NA' : $return_data['subjects'][$d->subject_id]['practical_marks'] / $return_data['subjects'][$d->subject_id]['practical_full_marks'] * 100;

	 		$return_data['subjects'][$d->subject_id]['theory_and_practical_marks'] = ($return_data['subjects'][$d->subject_id]['marks'] + $return_data['subjects'][$d->subject_id]['practical_marks'])/($return_data['subjects'][$d->subject_id]['full_marks'] + $return_data['subjects'][$d->subject_id]['practical_full_marks']) * 100;
		 	
		 	$return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_marks'] = $return_data['subjects'][$d->subject_id]['cas_marks'] == 'NA' ? $return_data['subjects'][$d->subject_id]['theory_and_practical_marks'] : ($return_data['subjects'][$d->subject_id]['theory_and_practical_marks'] * (100 - $config->cas_percentage)/100) + ($return_data['subjects'][$d->subject_id]['cas_marks'] * $config->cas_percentage / 100);

		 	$end = count($grade_conditions) - 1;
		 	foreach($grade_conditions as $g)
	 		{
	 			if(!isset($return_data['subjects'][$d->subject_id]['cas_grade']) && $return_data['subjects'][$d->subject_id]['cas_marks'] > $g->from_percent)
	 			{
	 				//$status = true;
	 				$return_data['subjects'][$d->subject_id]['cas_grade'] = $g->grade;
	 				$return_data['subjects'][$d->subject_id]['cas_grade_point'] = $g->grade_point;
	 			}
	 			else
	 			{
		 			if( $return_data['subjects'][$d->subject_id]['cas_marks'] === 'NA')
		 			{
		 				$return_data['subjects'][$d->subject_id]['cas_grade'] = 'NA';
		 				$return_data['subjects'][$d->subject_id]['cas_grade_point'] = 'NA';
		 			}
		 			elseif( $return_data['subjects'][$d->subject_id]['cas_marks'] == 0)
		 			{
		 				$return_data['subjects'][$d->subject_id]['cas_grade'] = $grade_conditions[$end]->grade;
		 				$return_data['subjects'][$d->subject_id]['cas_grade_point'] = $grade_conditions[$end]->grade_point;
		 			}	
	 			}
	 			

	 			if(!isset($return_data['subjects'][$d->subject_id]['marks_grade']) && $return_data['subjects'][$d->subject_id]['marks_percent'] > $g->from_percent)
	 			{
	 				//$status = true;
	 				$return_data['subjects'][$d->subject_id]['marks_grade'] = $g->grade;
	 				$return_data['subjects'][$d->subject_id]['marks_grade_point'] = $g->grade_point;
	 			}
	 			else
	 			{
		 			if($return_data['subjects'][$d->subject_id]['marks_percent'] == 0)
		 			{
		 				$return_data['subjects'][$d->subject_id]['marks_grade'] = $grade_conditions[$end]->grade;
		 				$return_data['subjects'][$d->subject_id]['marks_grade_point'] = $grade_conditions[$end]->grade_point;
		 			}	
	 			}
	 			

	 			if(!isset($return_data['subjects'][$d->subject_id]['practical_marks_grade']) && $return_data['subjects'][$d->subject_id]['practical_marks_percent'] > $g->from_percent)
	 			{
	 				//$status = true;
	 				$return_data['subjects'][$d->subject_id]['practical_marks_grade'] = $g->grade;
	 				$return_data['subjects'][$d->subject_id]['practical_marks_grade_point'] = $g->grade_point;
	 			}
	 			else
	 			{
		 			if($return_data['subjects'][$d->subject_id]['practical_marks_percent'] === 'NA')
		 			{
		 			
		 				//$status = true;
		 				$return_data['subjects'][$d->subject_id]['practical_marks_grade'] = 'NA';
		 				$return_data['subjects'][$d->subject_id]['practical_marks_grade_point'] = 'NA';
		 			
		 			}
		 			elseif($return_data['subjects'][$d->subject_id]['practical_marks_percent'] == 0)
		 			{

		 				$return_data['subjects'][$d->subject_id]['practical_marks_grade'] = $grade_conditions[$end]->grade;
		 				
		 				$return_data['subjects'][$d->subject_id]['practical_marks_grade_point'] = $grade_conditions[$end]->grade_point;
		 			}	
	 			}
	 			


	 			if(!isset($return_data['subjects'][$d->subject_id]['theory_and_practical_grade']) && $return_data['subjects'][$d->subject_id]['theory_and_practical_marks'] > $g->from_percent)
	 			{
	 				//$status = true;
	 				$return_data['subjects'][$d->subject_id]['theory_and_practical_grade'] = $g->grade;
	 				$return_data['subjects'][$d->subject_id]['theory_and_practical_grade_point'] = $g->grade_point;
	 			}
	 			else
	 			{
		 			if($return_data['subjects'][$d->subject_id]['theory_and_practical_marks'] == 0)
		 			{
		 				$return_data['subjects'][$d->subject_id]['theory_and_practical_grade'] = $grade_conditions[$end]->grade;
		 				$return_data['subjects'][$d->subject_id]['theory_and_practical_grade_point'] = $grade_conditions[$end]->grade_point;
		 			}	
	 			}
	 			

	 			if(!isset($return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_grade']) && $return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_marks'] > $g->from_percent)
	 			{
	 				//$status = true;
	 				$return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_grade'] = $g->grade;
	 				$return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_grade_point'] = $g->grade_point;
	 			}
	 			else
	 			{
		 			if($return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_marks'] == 0)
		 			{
		 				$return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_grade'] = $grade_conditions[$end]->grade;
		 				$return_data['subjects'][$d->subject_id]['theory_and_practical_and_cas_grade_point'] = $grade_conditions[$end]->grade_point;
		 			}	
	 			}
	 			

	 			if(!isset($return_data['cgpa_grade']) && $return_data['cgpa'] > $g->grade_point)
	 			{
	 				//$status = true;
	 				$return_data['cgpa_grade'] = $g->grade;
	 			}
	 			else
	 			{
	 				if($return_data['cgpa'] == 0)
	 				{
	 					$return_data['cgpa_grade'] = $grade_conditions[$end]->grade;
	 				}
	 			}
	 		}

	 		unset($data[$index]);
	 	}

		return $return_data;

	}


	public static function getCasMarksFromClassIdAndSectionId($arguments = []) //arguments has session_id, class_id, section_id, exam_id, *student_id
	{
		$cas_marks_data = [];
		$cas_subjects = [];

		$config = json_decode(File::get(REPORT_CONFIG_FILE));

		//if($config->cas_percentage > 0)
		//{
			$cas_sub_topic_table = CasSubTopics::getTableName();
			$cas_marks_table = CasExamMark::getTableName();
			$cas_marks = DB::table($cas_marks_table)
							->join($cas_sub_topic_table, $cas_sub_topic_table.'.id', '=', $cas_marks_table.'.sub_topic_id');

			if(isset($arguments['student_id']))
			{
				$cas_marks = $cas_marks->where('student_id', $arguments['student_id']);
			}
			else
			{
				$cas_marks = $cas_marks->where('class_id', $arguments['class_id'])
				->where('section_id', $arguments['section_id']);
							
			}
			
			$cas_marks = $cas_marks->where('session_id', $arguments['session_id'])
									->where('exam_id', $arguments['exam_id'])
									->select($cas_marks_table.'.*', $cas_sub_topic_table.'.subject_id')
									->get();

			foreach($cas_marks as $c)
			{
				if(isset($cas_marks_data[$c->student_id][$c->subject_id]))
				{
					$cas_marks_data[$c->student_id][$c->subject_id] += 	$c->sub_topic_marks;
				}
				else
				{
					$cas_marks_data[$c->student_id][$c->subject_id] = 	$c->sub_topic_marks;
				}

				$cas_subjects[$c->subject_id][$c->sub_topic_id] = 1;
				
			}

			foreach($cas_subjects as $subject_id => $c)
			{
				$cas_subjects[$subject_id] = count($c);
			}



			foreach($cas_marks_data as $student_id => $cas)
			{

				foreach($cas as $subject_id => $c)
				{
					$cas_marks_data[$student_id][$subject_id] /= $cas_subjects[$subject_id];
				}
			}

		//}

		return ['cas_marks_data' => $cas_marks_data, 'cas_subjects' => $cas_subjects];

	}

	public static function checkCasPassOrFail($mark)
	{
		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		$status = 'Passed';
		if($config->cas_pass_percentage > 0)
		{
			
				$status = $marks >= $config->cas_pass_percentage ? 'Passed' : 'Failed';
		}

		return $status;
	}

	public static function getCasTotalMarks($subject_marks, $subjects)
	{
		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		if(($config->cas_percentage > 0))
		{
			$total = 0;
			foreach(/*$cas_marks_data[$d->student_id]*/ $subject_marks as $subject_id => $marks)
			{
				$total += $marks;
	
			}

			$total = $total / (count($subjects) * 100);
		}

		return $total;
	}

	public static function getPercentageAfterCas($percentage, $cas_total_marks)
	{
		$return = $percentage;
		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		if($config->cas_percentage > 0)
		{
			$return = ($cas_total_marks * $config->cas_percentage / 100) + ($percentage * (100 - $config->cas_percentage) / 100);
		}

		return $return;

	}

	public static function calculateSumGradePoint($session_id, $class_id, $non_cas_subjects, $cas_subjects) //marks in the form subject_id => marks_in_percentage
	{
		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		$sum_grade_point = 0;
		$count = 0;
		
		foreach($non_cas_subjects as $subject_id => $percentage)
		{
			$count++;
			if(isset($cas_subjects[$subject_id]))
			{
				$percent = ($percentage * $config->cas_percentage / 100 ) + ((100 - $config->cas_percentage) * $cas_subjects[$subject_id] / 100);	
			}

			$sum_grade_point += GradeHelperController::convertPercentageToGrade($session_id, $class_id, $percentage)->grade_point;
		}

		$sum_grade_point /= $count;

		return $sum_grade_point;
	}

	public static function convertMarksJson($data) 
	{
		$cas_config = json_decode(File::get(app_path().'/modules/report/config/config.json'));

		/*$exam_name = DB::table(ExamConfiguration::getTableName())
									->where('id', $data['exam_id'][''])
									->pluck('exam_name');


		$exam_name = $json_data['is_final'] == 'yes' ? $json_data['exam_name'].' Final Report' : $exam_name;*/

		$result = [];
		$json_data = [];

		/**/

		foreach($data as $exam_id => $dat)
		{
			$session_id = $dat['session_id'];
			$highest_marks = [];
			
			if($exam_id == $dat['exam_id'])
			{
				$exam_full_marks_total = 0;
				foreach($dat['exam_conditions'] as $e)
				{
					if($e['is_graded'] == 'yes')
						$exam_full_marks_total += $e['practical_full_marks'] + $e['full_marks'];
				}

				$session_id = $dat['session_id'];
				$class_id = $dat['class_id'];
				$section_id = $dat['section_id'];
				$exam_id = $dat['exam_id'];
				$section_code = Section::where('id', $section_id)
										->pluck('section_code');
				$exam_name = ExamConfiguration::where('id', $exam_id)
												->pluck('exam_name');
				$class_name = Classes::where('id', $class_id)
										->pluck('class_name');
			}
				
			
			foreach($dat['sub_details'] as $d)
			{
				if(!isset($json_data[$d->student_id]))
				{
					$json_data[$d->student_id]['exam_details']['exam_id'] = $exam_id;
					$json_data[$d->student_id]['exam_details']['exam_name'] = DB::table(ExamConfiguration::getTableName())
											->where('id', $exam_id)
											->pluck('exam_name');
					$json_data[$d->student_id]['exam_details']['weightage'] = $dat['weightage'];

				}

					if($d->is_graded == 'yes')
					{
						if(!isset($json_data[$d->student_id]['summary']['status']) || ($json_data[$d->student_id]['summary']['status'] == 'Passed'))
						{
							$json_data[$d->student_id]['summary']['status'] = ($d->marks < $dat['exam_conditions'][$d->subject_id]['pass_marks']) || ((float) $d->practical_marks < $dat['exam_conditions'][$d->subject_id]['practical_pass_marks'])  ? 'Failed' : 'Passed';	
						}

						if($json_data[$d->student_id]['summary']['status'] == 'Passed' && $cas_config->cas_pass_percentage > 0 && isset($dat['cas_details']['cas_marks_data'][$d->student_id][$d->subject_id]))
						{
							$json_data[$d->student_id]['summary']['status'] = $dat['cas_details']['cas_marks_data'][$d->student_id][$d->subject_id] < $cas_config->cas_pass_percentage ? 'Failed' : 'Passed';
						}
						

						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['subject_name'] = $d->subject_name;

						//getting theory details
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_marks'] = $d->marks;
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_full_marks'] = $dat['exam_conditions'][$d->subject_id]['full_marks'];
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_pass_marks'] = $dat['exam_conditions'][$d->subject_id]['pass_marks'];
						$percentage = $d->marks * 100 / $dat['exam_conditions'][$d->subject_id]['full_marks'];
						
						$grade = GradeHelperController::convertPercentageToGrade($dat['session_id'], $dat['class_id'], $percentage);
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_grade'] = $grade->grade;	
						
						
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_gpa'] = $grade->grade_point;

						
						if($dat['exam_conditions'][$d->subject_id]['practical_pass_marks'] == 0)
						{
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_marks'] = 'NA';
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_full_marks'] = $dat['exam_conditions'][$d->subject_id]['practical_full_marks'];
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_pass_marks'] = $dat['exam_conditions'][$d->subject_id]['practical_pass_marks'];
							
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_grade'] = 'NA';
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_gpa'] = 'NA';
						}
						else
						{
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_marks'] = $d->practical_marks;
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_full_marks'] = $dat['exam_conditions'][$d->subject_id]['practical_full_marks'];
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_pass_marks'] = $dat['exam_conditions'][$d->subject_id]['practical_pass_marks'];
							$percentage = $d->practical_marks * 100 / $dat['exam_conditions'][$d->subject_id]['practical_full_marks'];
							$grade = GradeHelperController::convertPercentageToGrade($dat['session_id'], $dat['class_id'], $percentage);
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_grade'] = $grade->grade;
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_gpa'] = $grade->grade_point;	
						}
						


						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_marks'] + $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_marks'];
						$percentage = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_marks'] * 100 / ($json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_full_marks'] + $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_full_marks']);
						$grade = GradeHelperController::convertPercentageToGrade($dat['session_id'], $dat['class_id'], $percentage);
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_grade'] = $grade->grade;
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_gpa'] = $grade->grade_point;
						
						if(isset($dat['cas_details']['cas_marks_data'][$d->student_id][$d->subject_id]))
						{
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'] = $dat['cas_details']['cas_marks_data'][$d->student_id][$d->subject_id] * (($json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_full_marks'] + $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_full_marks'])) / 100;

							$percentage = $dat['cas_details']['cas_marks_data'][$d->student_id][$d->subject_id];
						}
						else
						{
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'] = 'NA';
							$percentage = 'NA';
						}


						if($json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'] !== 'NA')
						{
							$grade = GradeHelperController::convertPercentageToGrade($dat['session_id'], $dat['class_id'], $percentage);

							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_grade'] = $grade->grade;
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_gpa']	= $grade->grade_point;

							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'] = ($json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'] * $cas_config->cas_percentage / 100) + (100 - $cas_config->cas_percentage) / 100 *  $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_marks'];


						}
						else
						{
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_grade'] = 'NA';
							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_gpa']	= 'NA';

							$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_marks'];
						}

						$json_data[$d->student_id]['summary']['cas_sub_combined_total'] = isset($json_data[$d->student_id]['summary']['cas_sub_combined_total']) ? $json_data[$d->student_id]['summary']['cas_sub_combined_total'] + $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'] : $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'];

						$percentage = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'] * 100 / ($json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_full_marks'] + $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_full_marks']);

						$grade = GradeHelperController::convertPercentageToGrade($dat['session_id'], $dat['class_id'], $percentage);

						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_grade'] = $grade->grade;
						$json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_gpa'] = $grade->grade_point;
						



						if(isset($highest_marks[$d->subject_id]))
						{
							if($highest_marks[$d->subject_id]['theory_highest_marks'] < $d->marks)
							{
								$highest_marks[$d->subject_id]['theory_highest_marks'] = $d->marks;

								$highest_marks[$d->subject_id]['theory_highest_grade']	= $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_grade'];

								$highest_marks[$d->subject_id]['theory_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_gpa'];
							}

							if($highest_marks[$d->subject_id]['practical_highest_marks'] < $d->practical_marks)
							{
								$highest_marks[$d->subject_id]['practical_highest_marks'] = $d->practical_marks;

								$highest_marks[$d->subject_id]['practical_highest_grade']	= $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_grade'];

								$highest_marks[$d->subject_id]['practical_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_gpa'];
							}

							if($highest_marks[$d->subject_id]['combined_highest_marks'] < $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_marks'])
							{
								$highest_marks[$d->subject_id]['combined_highest_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_marks'];

								$highest_marks[$d->subject_id]['combined_highest_grade']	= $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_grade'];

								$highest_marks[$d->subject_id]['combined_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_gpa'];
							}

							if($highest_marks[$d->subject_id]['cas_highest_marks'] < $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'])
							{
								$highest_marks[$d->subject_id]['cas_highest_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'];

								$highest_marks[$d->subject_id]['cas_highest_grade']	= $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_grade'];

								$highest_marks[$d->subject_id]['cas_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_gpa'];
							}

							if($highest_marks[$d->subject_id]['cas_sub_combined_highest_marks'] < $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'])
							{
								$highest_marks[$d->subject_id]['cas_sub_combined_highest_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'];

								$highest_marks[$d->subject_id]['cas_sub_combined_highest_grade']	= $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_grade'];

								$highest_marks[$d->subject_id]['cas_sub_combined_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_gpa'];
							}	
						}
						else
						{
							$highest_marks[$d->subject_id]['theory_highest_marks'] = $d->marks;

							$highest_marks[$d->subject_id]['theory_highest_grade'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_grade'];

							$highest_marks[$d->subject_id]['theory_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['theory_gpa'];

							$highest_marks[$d->subject_id]['practical_highest_marks'] = $d->practical_marks;

							$highest_marks[$d->subject_id]['practical_highest_grade'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_grade'];

							$highest_marks[$d->subject_id]['practical_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['practical_gpa'];

							$highest_marks[$d->subject_id]['combined_highest_marks'] = $d->marks + $d->practical_marks;

							$highest_marks[$d->subject_id]['combined_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_gpa'];

							$highest_marks[$d->subject_id]['combined_highest_grade'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['combined_grade'];

							$highest_marks[$d->subject_id]['cas_highest_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_marks'];

							$highest_marks[$d->subject_id]['cas_highest_grade'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_grade'];

							$highest_marks[$d->subject_id]['cas_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_gpa'];

							$highest_marks[$d->subject_id]['cas_sub_combined_highest_marks'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_marks'];

							$highest_marks[$d->subject_id]['cas_sub_combined_highest_grade'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_grade'];

							$highest_marks[$d->subject_id]['cas_sub_combined_highest_gpa'] = $json_data[$d->student_id]['exam_details']['graded_sub_details'][$d->subject_id]['cas_sub_combined_gpa'];
						}
					}
					else
					{
						$json_data[$d->student_id]['exam_details']['non_graded_sub_details'][$d->subject_id]['subject_name'] = $d->subject_name;

						//getting theory details
						$json_data[$d->student_id]['exam_details']['non_graded_sub_details'][$d->subject_id]['theory_marks'] = $d->marks;
						$json_data[$d->student_id]['exam_details']['non_graded_sub_details'][$d->subject_id]['theory_full_marks'] = $dat['exam_conditions'][$d->subject_id]['full_marks'];
						$json_data[$d->student_id]['exam_details']['non_graded_sub_details'][$d->subject_id]['theory_pass_marks'] = $dat['exam_conditions'][$d->subject_id]['pass_marks'];
						$percentage = $d->marks * 100 / $dat['exam_conditions'][$d->subject_id]['full_marks'];
						
						$grade = GradeHelperController::convertPercentageToGrade($dat['session_id'], $dat['class_id'], $percentage);
						$json_data[$d->student_id]['exam_details']['non_graded_sub_details'][$d->subject_id]['theory_grade'] = $grade->grade;	
						
						
						$json_data[$d->student_id]['exam_details']['non_graded_sub_details'][$d->subject_id]['theory_gpa'] = $grade->grade_point;						
					}
			}	
		}
		

		$grade_settings = GradeHelperController::convertGradeToFrom($session_id, $class_id);

		foreach($json_data as $student_id => $data)
		{
			
			$json_data[$student_id]['summary']['cas_sub_combined_percentage'] = $json_data[$student_id]['summary']['cas_sub_combined_total'] * 100 / $exam_full_marks_total;

			$combined_gpa = 0;
			$counter = 0;
			foreach($data['exam_details']['graded_sub_details'] as $d)
			{
				$combined_gpa += $d['cas_sub_combined_gpa'];
				$counter++;
			}

			$json_data[$student_id]['summary']['cas_sub_combined_gpa'] = $combined_gpa/$counter;

			$json_data[$student_id]['summary']['cas_sub_combined_grade'] = GradeHelperController::convertGradePointToGrade($grade_settings, $json_data[$student_id]['summary']['cas_sub_combined_gpa']);

		}

		$student_registration_table = StudentRegistration::getTableName();
		$student_table = Student::getTableName();

		unset($data);

		$data = DB::table($student_table)
					->join($student_registration_table, function($query) use ($student_table,$student_registration_table, $session_id, $class_id, $section_id, $section_code)
					{
						$query->on($student_registration_table.'.id', '=', $student_table.'.student_id')
							->where('current_session_id', '=', $session_id)
							->where('current_class_id', '=', $class_id)
							->where('current_section_code', '=', $section_code);
					})
					->select($student_registration_table.'.id', $student_registration_table.'.student_name', 'last_name', $student_table.'.current_roll_number')
					->get();

		foreach($data as $d)
		{
			$json_data[$d->id]['personal_details']['student_name'] = $d->student_name.' '.$d->last_name;
			$json_data[$d->id]['personal_details']['roll'] = $d->current_roll_number;
			$json_data[$d->id]['personal_details']['class'] = $class_name;
			$json_data[$d->id]['personal_details']['section'] = $section_code;
			$json_data[$d->id]['personal_details']['exam_name'] = $exam_name;
			$json_data[$d->id]['report_settings']['show_cas'] = Input::get('show_cas', 'no');
			$json_data[$d->id]['report_settings']['print_type'] = Input::get('print_type', 'one');
			$json_data[$d->id]['report_settings']['paid_only'] = Input::get('paid_only', 'no');
		}

		unset($data);

		foreach($json_data as $student_id => $data)
		{
			foreach($data['exam_details']['graded_sub_details'] as $subject_id => $d)
			{
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['theory_highest_marks'] = $highest_marks[$subject_id]['theory_highest_marks'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['practical_highest_marks'] = $highest_marks[$subject_id]['practical_highest_marks'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['combined_highest_marks'] = $highest_marks[$subject_id]['combined_highest_marks'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['theory_highest_grade'] = $highest_marks[$subject_id]['theory_highest_grade'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['theory_highest_gpa'] = $highest_marks[$subject_id]['theory_highest_gpa'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['practical_highest_grade'] = $highest_marks[$subject_id]['practical_highest_grade'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['practical_highest_gpa'] = $highest_marks[$subject_id]['practical_highest_gpa'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['combined_highest_grade'] = $highest_marks[$subject_id]['combined_highest_grade'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['combined_highest_gpa'] = $highest_marks[$subject_id]['combined_highest_gpa'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['cas_highest_marks'] = $highest_marks[$subject_id]['cas_highest_marks'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['cas_highest_grade'] = $highest_marks[$subject_id]['cas_highest_grade'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['cas_highest_gpa'] = $highest_marks[$subject_id]['cas_highest_gpa'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['cas_sub_combined_highest_marks'] = $highest_marks[$subject_id]['cas_sub_combined_highest_marks'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['cas_sub_combined_highest_grade'] = $highest_marks[$subject_id]['cas_sub_combined_highest_grade'];
				$json_data[$student_id]['exam_details']['graded_sub_details'][$subject_id]['cas_sub_combined_highest_gpa'] = $highest_marks[$subject_id]['cas_sub_combined_highest_gpa'];
			}
		}

		return $json_data;
			
	}

	public static function convertMarksJsonFinal($weights, $condition, $full_marks, $data, $input)
	{
		$cas_config = json_decode(File::get(app_path().'/modules/report/config/config.json'));

		
		$json_data = [];

		$highest_marks = [];
		$marks = [];

		$flag = 1;
		$total_exams = count($weights);
		$counter = 0;

		foreach($weights as $exam_id => $weight)
		{
			++$counter;
			foreach($data[$exam_id] as $d)
			{
				$array = json_decode($d->exam_details, true);
				if($exam_id == $input['exam_id'])
				{
					
						$json_data[$d->student_id]['personal_details']['student_name'] = $array['personal_details']['student_name']; 
						$json_data[$d->student_id]['personal_details']['roll'] = $array['personal_details']['roll']; 
						$json_data[$d->student_id]['personal_details']['class'] = $array['personal_details']['class']; 
						$json_data[$d->student_id]['personal_details']['section'] = $array['personal_details']['section']; 
						$json_data[$d->student_id]['personal_details']['exam_name'] = $array['personal_details']['exam_name']. ' Final Report';
					
				}

				$json_data[$d->student_id]['exam_details'][$exam_id]['exam_id'] = $array['exam_details']['exam_id'];
				$json_data[$d->student_id]['exam_details'][$exam_id]['weight'] = $weight;
				$json_data[$d->student_id]['exam_details'][$exam_id]['exam_name'] = $array['exam_details']['exam_name'];

				foreach($array['exam_details']['graded_sub_details'] as $subject_id => $graded_sub_details)
				{
					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['subject_name'] = $graded_sub_details['subject_name'];

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_theory_marks'] = $graded_sub_details['theory_marks'] / $graded_sub_details['theory_full_marks'] * $condition[$subject_id]['full_marks'] * $weight / 100;

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_theory_grade'] = $graded_sub_details['theory_grade'];

					if($condition[$subject_id]['practical_full_marks'] == 0 || $graded_sub_details['practical_full_marks'] == 0)
					{
						$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_marks'] = 'NA';

						$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_grade'] = 'NA';
					}
					else
					{
						$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_grade'] = $graded_sub_details['practical_grade'];	
					}
					

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_combined_marks'] = $graded_sub_details['combined_marks'] / ($graded_sub_details['practical_full_marks'] + $graded_sub_details['theory_full_marks']) * ($condition[$subject_id]['full_marks'] + $condition[$subject_id]['practical_full_marks']) * $weight / 100;

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_combined_grade'] = $graded_sub_details['combined_grade'];

					if($graded_sub_details['cas_marks'] === 'NA')
					{
						$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_marks'] = 'NA';
					}
					else
					{
						$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_marks'] = $graded_sub_details['cas_marks'] / ($graded_sub_details['practical_full_marks'] + $graded_sub_details['theory_full_marks']) * ($condition[$subject_id]['full_marks'] + $condition[$subject_id]['practical_full_marks']) * $weight / 100;	
					}

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_grade'] = $graded_sub_details['cas_grade'];

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_sub_combined_marks'] = $graded_sub_details['cas_sub_combined_marks'] * ( $condition[$subject_id]['full_marks'] + $condition[$subject_id]['practical_full_marks']) / ($graded_sub_details['theory_full_marks'] + $graded_sub_details['practical_full_marks']) * $weight / 100;

					$json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_sub_combined_grade'] = $graded_sub_details['cas_sub_combined_grade'];

					
					if(isset($json_data[$d->student_id]['exam_summary'][$subject_id]))
					{

						$json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'] += $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_theory_marks'];
						
						$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_marks'] += isset($json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_marks']) ? $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_marks'] : '';

						$json_data[$d->student_id]['exam_summary'][$subject_id]['combined_marks'] += $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_combined_marks'];

						$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'] += $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_marks'];

						$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'] += $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_sub_combined_marks'];

					}
					else
					{
						$json_data[$d->student_id]['exam_summary'][$subject_id]['subject_name'] = $graded_sub_details['subject_name'];
						$json_data[$d->student_id]['exam_summary'][$subject_id]['theory_full_marks'] = $condition[$subject_id]['full_marks'];
						$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_full_marks'] = $condition[$subject_id]['practical_full_marks'] > 0 ? $condition[$subject_id]['practical_full_marks'] : 'NA';
						$json_data[$d->student_id]['exam_summary'][$subject_id]['theory_pass_marks'] = $condition[$subject_id]['pass_marks'];
						$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_pass_marks'] = $condition[$subject_id]['practical_full_marks'] > 0 ? $condition[$subject_id]['practical_pass_marks'] : 'NA';

						$json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'] = $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_theory_marks'];


						$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_marks'] = isset($json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_marks']) ? $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_practical_marks'] : '';

						$json_data[$d->student_id]['exam_summary'][$subject_id]['combined_marks'] = $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_combined_marks'];

						$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'] = $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_marks'];

						$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'] = $json_data[$d->student_id]['exam_details'][$exam_id]['graded_sub_details'][$subject_id]['weighted_cas_sub_combined_marks'];
					}

					if($counter == $total_exams)
					{
						$percentage = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'] * 100 / ($condition[$subject_id]['full_marks'] + $condition[$subject_id]['practical_full_marks']);
						$grade = GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $percentage);
						$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_grade'] = $grade->grade;
						$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_gpa'] = $grade->grade_point;

						$percentage = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'] * 100 / $condition[$subject_id]['full_marks'];
						$grade = GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $percentage);
						$json_data[$d->student_id]['exam_summary'][$subject_id]['theory_grade'] = $grade->grade;
						$json_data[$d->student_id]['exam_summary'][$subject_id]['theory_gpa'] = $grade->grade_point;

						if($condition[$subject_id]['practical_full_marks'] > 0)
						{
							$percentage = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_marks'] * 100 / $condition[$subject_id]['practical_full_marks'];
							$grade = GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $percentage);
							$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_grade'] = $grade->grade;
							$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_gpa'] = $grade->grade_point;	
						}
						else
						{
							$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_grade'] ='NA';
							$json_data[$d->student_id]['exam_summary'][$subject_id]['practical_gpa'] = 'NA';
						}
						

						$percentage = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_marks'] * 100 / ($condition[$subject_id]['practical_full_marks'] + $condition[$subject_id]['full_marks']);
						$grade = GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $percentage);
						$json_data[$d->student_id]['exam_summary'][$subject_id]['combined_grade'] = $grade->grade;
						$json_data[$d->student_id]['exam_summary'][$subject_id]['combined_gpa'] = $grade->grade_point;

						if($json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'] !== 'NA')
						{
							$percentage = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'] * 100 / ($condition[$subject_id]['practical_full_marks'] + $condition[$subject_id]['full_marks']);
							$grade = GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $percentage);
							$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_grade'] = $grade->grade;
							$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_gpa'] = $grade->grade_point;	
						}
						else
						{
							$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_grade'] = 'NA';
							$json_data[$d->student_id]['exam_summary'][$subject_id]['cas_gpa'] = 'NA';	
						}
						

						if(!isset($json_data[$d->student_id]['summary']['status']) || ($json_data[$d->student_id]['summary']['status'] == 'Passed'))
						{
							if($json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'] < $condition[$subject_id]['pass_marks'] || $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'] < $condition[$subject_id]['practical_pass_marks'])
							{
								$json_data[$d->student_id]['summary']['status'] = 'Failed';
							}
							else
							{
								$json_data[$d->student_id]['summary']['status'] = 'Passed';
							}

							if($json_data[$d->student_id]['summary']['status'] == 'Passed' && $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'] != 'NA' && $cas_config->cas_pass_percentage > 0)
							{
								$percentage = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'] / ($condition[$subject_id]['full_marks'] + $exam_conditions[$subject_id]['practical_full_marks']) * 100;

								$json_data[$d->student_id]['summary']['status'] = $percenrage < $cas_config->cas_pass_percentage ? 'Failed' : 'Passed';

							}
						}
						if(isset($highest[$subject_id]))
						{
							if($highest[$subject_id]['theory_highest_marks'] < $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'])
							{
								$highest[$subject_id]['theory_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'];
								$highest[$subject_id]['theory_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_grade'];
								$highest[$subject_id]['theory_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_gpa'];
							}

							if($highest[$subject_id]['practical_highest_marks'] < $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_marks'])
							{
								$highest[$subject_id]['practical_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_marks'];
								$highest[$subject_id]['practical_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_grade'];
								$highest[$subject_id]['practical_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_gpa'];
							}

							if($highest[$subject_id]['combined_highest_marks'] < $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_marks'])
							{
								$highest[$subject_id]['combined_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_marks'];
								$highest[$subject_id]['combined_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_grade'];
								$highest[$subject_id]['combined_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_gpa'];
							}

							if($highest[$subject_id]['cas_highest_marks'] < $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'])
							{
								$highest[$subject_id]['cas_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'];
								$highest[$subject_id]['cas_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_grade'];
								$highest[$subject_id]['cas_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_gpa'];
							}

							if($highest[$subject_id]['cas_sub_combined_highest_marks'] < $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'])
							{
								$highest[$subject_id]['cas_sub_combined_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'];
								$highest[$subject_id]['cas_sub_combined_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_grade'];
								$highest[$subject_id]['cas_sub_combined_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_gpa'];
							}
						}
					else
					{
						$highest[$subject_id]['theory_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_marks'];
						$highest[$subject_id]['theory_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_grade'];
						$highest[$subject_id]['theory_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['theory_gpa'];

						$highest[$subject_id]['practical_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_marks'];
						$highest[$subject_id]['practical_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_grade'];
						$highest[$subject_id]['practical_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['practical_gpa'];

						$highest[$subject_id]['combined_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_marks'];
						$highest[$subject_id]['combined_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_grade'];
						$highest[$subject_id]['combined_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['combined_gpa'];

						$highest[$subject_id]['cas_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_marks'];
						$highest[$subject_id]['cas_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_grade'];
						$highest[$subject_id]['cas_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_gpa'];

						$highest[$subject_id]['cas_sub_combined_highest_marks'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'];
						$highest[$subject_id]['cas_sub_combined_highest_grade'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_grade'];
						$highest[$subject_id]['cas_sub_combined_highest_gpa'] = $json_data[$d->student_id]['exam_summary'][$subject_id]['cas_sub_combined_gpa'];
					}
					}

					
				}

				if(isset($array['exam_details']['non_graded_sub_details']))
				{
					foreach($array['exam_details']['non_graded_sub_details'] as $subject_id => $graded_sub_details)
					{
						$json_data[$d->student_id]['exam_details'][$exam_id]['non_graded_sub_details'][$subject_id]['subject_name'] = $graded_sub_details['subject_name'];

						$json_data[$d->student_id]['exam_details'][$exam_id]['non_graded_sub_details'][$subject_id]['weighted_theory_marks'] = $graded_sub_details['theory_marks'];

						$json_data[$d->student_id]['exam_details'][$exam_id]['non_graded_sub_details'][$subject_id]['weighted_theory_grade'] = $graded_sub_details['theory_grade'];
					}	
				}
				

			}
		}

		$grade_settings = GradeHelperController::convertGradeToFrom($input['session_id'], $input['class_id']);
		
		
		foreach($json_data as $student_id => $data)
		{	
			$combined_gpa = 0;
			$counter = 0;
			$json_data[$student_id]['summary']['cas_sub_combined_total'] = 0;
			foreach($data['exam_summary'] as $subject_id => $d)
			{
				$json_data[$student_id]['summary']['cas_sub_combined_total'] += $json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_marks'];

				$combined_gpa += $d['cas_sub_combined_gpa'];
				$counter++;
				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_marks'] = $highest[$subject_id]['theory_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_grade'] = $highest[$subject_id]['theory_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_gpa'] = $highest[$subject_id]['theory_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['practical_highest_marks'] = $highest[$subject_id]['practical_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['practical_highest_grade'] = $highest[$subject_id]['practical_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['practical_highest_gpa'] = $highest[$subject_id]['practical_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['combined_highest_marks'] = $highest[$subject_id]['combined_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['combined_highest_grade'] = $highest[$subject_id]['combined_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['combined_highest_gpa'] = $highest[$subject_id]['combined_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['cas_highest_marks'] = $highest[$subject_id]['cas_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_highest_grade'] = $highest[$subject_id]['cas_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_highest_gpa'] = $highest[$subject_id]['cas_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_highest_marks'] = $highest[$subject_id]['cas_sub_combined_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_highest_grade'] = $highest[$subject_id]['cas_sub_combined_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_highest_gpa'] = $highest[$subject_id]['cas_sub_combined_highest_gpa'];				
			}

			$json_data[$student_id]['summary']['cas_sub_combined_percentage'] = $json_data[$student_id]['summary']['cas_sub_combined_total'] * 100 / $full_marks;

			$json_data[$student_id]['summary']['cas_sub_combined_gpa'] = $combined_gpa/$counter;

			$json_data[$student_id]['summary']['cas_sub_combined_grade'] = GradeHelperController::convertGradePointToGrade($grade_settings, $json_data[$student_id]['summary']['cas_sub_combined_gpa']);

			
				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_marks'] = $highest[$subject_id]['theory_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_grade'] = $highest[$subject_id]['theory_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_gpa'] = $highest[$subject_id]['theory_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['theory_highest_marks'] = $highest[$subject_id]['practical_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['practical_highest_grade'] = $highest[$subject_id]['practical_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['practical_highest_gpa'] = $highest[$subject_id]['practical_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['combined_highest_marks'] = $highest[$subject_id]['combined_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['combined_highest_grade'] = $highest[$subject_id]['combined_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['combined_highest_gpa'] = $highest[$subject_id]['combined_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['cas_highest_marks'] = $highest[$subject_id]['cas_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_highest_grade'] = $highest[$subject_id]['cas_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_highest_gpa'] = $highest[$subject_id]['cas_highest_gpa'];

				$json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_highest_marks'] = $highest[$subject_id]['cas_sub_combined_highest_marks'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_highest_grade'] = $highest[$subject_id]['cas_sub_combined_highest_grade'];
				$json_data[$student_id]['exam_summary'][$subject_id]['cas_sub_combined_highest_gpa'] = $highest[$subject_id]['cas_sub_combined_highest_gpa'];
		}

		return $json_data;
	}

			
	
}