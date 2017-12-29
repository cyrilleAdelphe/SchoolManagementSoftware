<?php

class ReportController extends BaseController
{
	protected $view = 'report.views.';

	protected $model_name = 'Report';

	protected $module_name = 'report';

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
										'column_name' 	=> 'subject_id',
										'alias'			=> 'Session'
									),

									array
									(
										'column_name' 	=> 'full_marks',
										'alias'			=> 'Full Marks'
									),

									array
									(
										'column_name' 	=> 'pass_marks',
										'alias'			=> 'Pass Marks'
									),

									array
									(
										'column_name' 	=> 'remarks',
										'alias'			=> 'Remarks'
									)
								 );

	public function getFinalReportGenerate()
	{
		AccessController::allowedOrNot($this->module_name, 'can_generate_report');
		return View::make($this->view.'generate-final-report');
	}

	public function postFinalReportGenerate()
	{
		AccessController::allowedOrNot($this->module_name, 'can_generate_report');
		$input = Input::all();
		
		$exam_configuration_table = ExamConfiguration::getTableName();
		$exam_marks_table = ExamMarks::getTableName();
		$subject_table = Subject::getTableName();
		$exam_details_table = ExamDetails::getTableName();

		//get child exams
		$child_exams = DB::table($exam_configuration_table)
							->where('parent_exam_id', $input['exam_id'])
							->where('session_id', $input['session_id'])
							->select('id as child_exam_id', 'weightage')
							->orderBy('id', 'ASC')
							->get();

		$weightage = [];
		$wt = 100;
		foreach($child_exams as $c)
		{
			$weightage[$c->child_exam_id] = $c->weightage;
			$wt -= $c->weightage;
		}

		$weightage[$input['exam_id']] = $wt;

		$exam_conditions = [];
		$exam_conditions_result = [];
		$result  = []; //this contains the aggregate result of all the exams
		//$grand_total = 0;
		$full_marks = 0;
		
		$exam_conditions = DB::table($exam_details_table)->join(
				$subject_table,
				$subject_table . '.id', '=',
				$exam_details_table . '.subject_id'
			)->where(
				$subject_table . '.class_id',
				$input['class_id']
			)->where(
				$subject_table . '.section_id',
				$input['section_id']
			)/*->where(
				$subject_table . '.is_graded', 'yes'
			)*/->where(
				$exam_details_table . '.exam_id', 
				$input['exam_id']
			)->where(
				$exam_details_table . '.is_active', 'yes'
			)->select(
				$exam_details_table . '.subject_id', 
				$exam_details_table . '.pass_marks', 
				$exam_details_table . '.full_marks',
				$exam_details_table . '.practical_pass_marks', 
				$exam_details_table . '.practical_full_marks',
				$subject_table . '.is_graded'
			)->get();

			foreach($exam_conditions as $e)
			{
				$exam_conditions_result[$e->subject_id] = array('pass_marks' => $e->pass_marks, 'full_marks' => $e->full_marks, 'practical_pass_marks' => $e->practical_pass_marks, 'practical_full_marks' => $e->practical_full_marks, 'is_graded' => $e->is_graded);

				if($e->is_graded == 'yes')
					$full_marks += $e->full_marks + $e->practical_full_marks;
			}


			$data = [];
			foreach($weightage as $exam_id => $weight)
			{
				$data[$exam_id] = DB::table(Report::getTableName())
									->where('exam_id', $exam_id)
									->where('session_id', $input['session_id'])
									->where('class_id', $input['class_id'])
									->where('section_id', $input['section_id'])
									->select('exam_details', 'student_id')
									->get();	
			}


		try
		{
			$json_data = ReportHelperController::convertMarksJsonFinal($weightage, $exam_conditions_result, $full_marks, $data, $input);
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', 'Something went wrong while generating report.<br> Please verify the following: <Br> - Grade is set for the given class. <br> - routines of all the child exams and final exam is same <br> - marks for all the graded subjects are given in all examinations <br> - Please generate all the reports again. <Br>If the problem still persists contact the administrator. Sorry for the inconvenience');
			return Redirect::back();
		}
		
		
		
		try
		{
			$rank = array();
			DB::connection()->getPdo()->beginTransaction();
				$data_to_store = [];
				foreach($json_data as $student_id => $r) //student_id is student_id
				{
					
					$data_to_store[$student_id]['cgpa'] = $json_data[$student_id]['summary']['cas_sub_combined_gpa'];


					$data_to_store[$student_id]['percentage'] = $json_data[$student_id]['summary']['cas_sub_combined_percentage'];

					
					$rank[] = array('student_id' => $student_id, 'percentage' => $json_data[$student_id]['summary']['cas_sub_combined_total']);
					

					$data_to_store[$student_id]['session_id'] = $input['session_id'];
					$data_to_store[$student_id]['class_id'] = $input['class_id'];
					$data_to_store[$student_id]['section_id'] = $input['section_id'];
					$data_to_store[$student_id]['exam_id'] = $input['exam_id'];
					$data_to_store[$student_id]['student_id'] = $student_id;
					//$data_to_store[$student_id]['remarks'] = '';
					$data_to_store[$student_id]['rank'] = 'NA';
					$data_to_store[$student_id]['grade']= $json_data[$student_id]['summary']['cas_sub_combined_grade'];
					$data_to_store[$student_id]['is_active'] = 'yes';
					$data_to_store[$student_id]['total_marks'] = $r['summary']['cas_sub_combined_total'];
					$data_to_store[$student_id]['status'] = $r['summary']['status'];
					$data_to_store[$student_id]['exam_details'] = json_encode($json_data[$student_id]);

					//check if exists
					$check = 
					
					
					
					
					FinalReport::where('student_id', $student_id)
									->where('section_id', $input['section_id'])
									->where('class_id', $input['class_id'])
									->where('session_id', $input['session_id'])
									->where('exam_id', $input['exam_id'])
									->pluck('id');

					if($check)
					{
						$data_to_store[$student_id]['id'] = $check;
						$this->updateInDatabase($data_to_store[$student_id], [], 'FinalReport');
					}
					else
					{
						$this->storeInDatabase($data_to_store[$student_id], 'FinalReport');
					}

				}
				usort($rank, function($a, $b) 
				{
				    return $b['percentage'] - $a['percentage'];
				});
				
				$prev_rank = -1;
				$prev_marks = -2;

				foreach($rank as $r)
				{
					if($data_to_store[$r['student_id']]['status'] == 'Passed')
					{
						if($prev_marks == -2)
						{
							$prev_marks = $r['percentage'];
							$prev_rank = 1;
							$rank = 1;
						}
						elseif($prev_marks == $r['percentage'])
						{

						}
						else
						{
							$prev_marks = $r['percentage'];
							$prev_rank++;
							$rank = $prev_rank;
						}	
					}
					else
					{
						$rank = 'none';	
					}

					$data_to_store[$r['student_id']]['rank'] = $rank;
					

					FinalReport::where('student_id', $r['student_id'])
							->where('section_id', $input['section_id'])
							->where('class_id', $input['class_id'])
							->where('session_id', $input['session_id'])
							->where('exam_id', $input['exam_id'])
							->update(array('rank' => $rank));
				}

			DB::connection()->getPdo()->commit();
			Session::flash('success-msg', 'Report successfully generated');
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());

		}

		$remarks_attendance_data = FinalReport::where('section_id', $input['section_id'])
								->where('class_id', $input['class_id'])
								->where('session_id', $input['session_id'])
								->where('exam_id', $input['exam_id'])
								->select('student_id', 'remarks', 'attendance')
								->get();
								
		
		$remarks = [];

		foreach($remarks_attendance_data as $index => $r)
		{
			$remarks[$r->student_id] = ['remarks' => $r->remarks, 'attendance' => $r->attendance];
			unset($remarks_attendance_data[$index]);
		}

		unset($remarks_attendance_data);

		return View::make($this->view.'.generate-final-report-enter-remarks')
					->with('data', $data_to_store)
					->with('session_id', $input['session_id'])
					->with('class_id', $input['class_id'])
					->with('section_id', $input['section_id'])
					->with('remarks', $remarks)
					->with('exam_id', $input['exam_id']);


	}

	public function getFinalClassReport()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$exam_id = Input::get('exam_id');
		$session_id = Input::get('session_id');

		$data = $this->getFinalClassReportData($class_id, $section_id, $exam_id, $session_id);
		

		return View::make($this->view.'partials.final-class-report')
						->with('data', $data)
						->with('class_id', $class_id)
						->with('session_id', $session_id)
						->with('exam_id', $exam_id)
						->with('section_id', $section_id);


	}

	public function getFinalReportSingle($exam_id, $student_id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$final_report_table = FinalReport::getTableName();
		$exam_configuration_table = ExamConfiguration::getTableName();
		$exam_marks_table = ExamMarks::getTableName();
		$subject_table = Subject::getTableName();
		$exam_details_table = ExamDetails::getTableName();
		//die('here');
		//get class id and section id from exam id and student id
		$class_id_and_section_id = DB::table($final_report_table)
										->where('exam_id', $exam_id)
										->where('student_id', $student_id)
										->select(['class_id', 'section_id', 'session_id'])
										->first();

		$input = [];
		$input['section_id'] = $class_id_and_section_id->section_id;
		$input['class_id'] = $class_id_and_section_id->class_id;
		$input['session_id'] = $class_id_and_section_id->session_id;
		//get child ids
		

		$child_exams = DB::table($exam_configuration_table)
							->where('parent_exam_id', $exam_id)
							//->where('session_id', $input['session_id'])
							->select('id as child_exam_id', 'weightage')
							->orderBy('id', 'ASC')
							->get();

		$weightage = [];
		$weightage[$exam_id] = 100;
		$data = [];

		$exam_conditions = DB::table($exam_details_table)
								->join(
				$subject_table,
				$subject_table . '.id', '=',
				$exam_details_table . '.subject_id'
			)->where(
				$subject_table . '.class_id',
				$input['class_id']
			)->where(
				$subject_table . '.section_id',
				$input['section_id']
			)->where(
				$subject_table . '.is_graded', 'yes'
			)->where(
				$exam_details_table . '.exam_id', 
				$exam_id
			)->where(
				$exam_details_table . '.is_active', 'yes'
			)->select(
				$exam_details_table . '.subject_id', 
				$exam_details_table . '.pass_marks', 
				$exam_details_table . '.full_marks',
				$exam_details_table . '.practical_pass_marks', 
				$exam_details_table . '.practical_full_marks',
				'subject_name'
			)->orderBy($subject_table.'.sort_order', 'ASC')->get();

		foreach($child_exams as $c)
		{
			$weightage[$c->child_exam_id] = $c->weightage;
			$weightage[$exam_id] -= $c->weightage;
			$data[$c->child_exam_id] = ReportHelperController::getReportData($c->child_exam_id, $student_id);
		}

		$data[$exam_id] = ReportHelperController::getReportData($exam_id, $student_id);

		/*echo '<pre>';
		print_r($data);
		die();*/

		$summary = DB::table($final_report_table)
						->where('exam_id', $exam_id)
						->where('student_id', $student_id)
						->first();

		$config = json_decode(File::get(REPORT_CONFIG_FILE));

		return View::make($this->view.'final-report-single')
					->with('data', $data)
					->with('weightage', $weightage)
					->with('exam_id', $exam_id)
					->with('exam_conditions', $exam_conditions)
					->with('summary', $summary)
					->with('input', $input)
					->with('config', $config);

		
		
	}

	public function getFinalReportSingleData($session_id, $class_id, $section_id, $ids = [])
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$exam_configuration_table = ExamConfiguration::getTableName();
		$exam_marks_table = ExamMarks::getTableName();
		$subject_table = Subject::getTableName();
		$exam_details_table = ExamDetails::getTableName();

		//get child exams
		$child_exams = DB::table($exam_configuration_table)
							->where('parent_exam_id', $input['exam_id'])
							->where('session_id', $input['session_id'])
							->select('id as child_exam_id', 'weightage')
							->get();

		//get marks in each subject
		$data = [];
		$weightage = [];
		$weightage[$input['exam_id']] = 100;
		foreach($child_exams as $c)
		{
			$weightage[$c->child_exam_id] = $c->weightage;
			$weightage[$input['exam_id']] -= $c->weightage;

			$data[$c->child_exam_id] = DB::table($exam_marks_table)
										->join(
					$subject_table,
					$subject_table . '.id', '=',
					$exam_marks_table . '.subject_id'
				)
				->where(
					$exam_marks_table . '.exam_id', 
					$c->child_exam_id
				)
				->where(
					$exam_marks_table . '.session_id', 
					$input['session_id'])
				->where(
					$exam_marks_table . '.class_id', 
					$input['class_id']
				)->where(
					$exam_marks_table . '.section_id', 
					$input['section_id']
				)->where(
					$subject_table . '.class_id',
					$input['class_id']
				)->where(
					$subject_table . '.section_id',
					$input['section_id']
				)->where(
					$subject_table . '.is_graded', 'yes'
				)
				//->groupBy('student_id')
				->orderBy('student_id', 'ASC')
				->select($exam_marks_table . '.*')
				->get();

						
		}


		$data[$input['exam_id']] = DB::table($exam_marks_table)->join(
					$subject_table,
					$subject_table . '.id', '=',
					$exam_marks_table . '.subject_id'
				)
				->where(
					$exam_marks_table . '.exam_id', 
					$input['exam_id']
				)
				->where(
					$exam_marks_table . '.session_id', 
					$input['session_id'])
				->where(
					$exam_marks_table . '.class_id', 
					$input['class_id']
				)->where(
					$exam_marks_table . '.section_id', 
					$input['section_id']
				)->where(
					$subject_table . '.class_id',
					$input['class_id']
				)->where(
					$subject_table . '.section_id',
					$input['section_id']
				)->where(
					$subject_table . '.is_graded', 'yes'
				)
				//->groupBy('student_id')
				->orderBy('student_id', 'ASC')
				->select($exam_marks_table . '.*')
				->get();

		$exam_conditions = DB::table($exam_details_table)
								->join(
				$subject_table,
				$subject_table . '.id', '=',
				$exam_details_table . '.subject_id'
			)->where(
				$subject_table . '.class_id',
				$input['class_id']
			)->where(
				$subject_table . '.section_id',
				$input['section_id']
			)->where(
				$subject_table . '.is_graded', 'yes'
			)->where(
				$exam_details_table . '.exam_id', 
				$input['exam_id']
			)->where(
				$exam_details_table . '.is_active', 'yes'
			)->select(
				$exam_details_table . '.subject_id', 
				$exam_details_table . '.pass_marks', 
				$exam_details_table . '.full_marks',
				$exam_details_table . '.practical_pass_marks', 
				$exam_details_table . '.practical_full_marks'
			)->get();
	}


	public function getFinalClassReportData($class_id, $section_id, $exam_id, $session_id)
	{
		$final_report_table = FinalReport::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$student_table = Student::getTableName();
		//$result = [];
		$section_code = Section::where('id', $section_id)->pluck('section_code');
		$result = DB::table($final_report_table)
					->join($student_registration_table, $student_registration_table.'.id', '=', $final_report_table.'.student_id')
					->join($student_table, function($query) use ($student_registration_table, $student_table, $session_id, $class_id, $section_code)
					{
						$query->on($student_table.'.student_id', '=', $student_registration_table.'.id')
							->where('current_session_id', '=', $session_id)
							->where('current_class_id', '=', $class_id)
							->where('current_section_code', '=',  $section_code);
					})
					->where('session_id', $session_id)
					->where('class_id', $class_id)
					->where('exam_id', $exam_id)
					->select($final_report_table.'.*', $student_table.'.current_roll_number', $student_registration_table.'.student_name')
					->get();


		return $result;

	}

	public function getMassPrint()
	{
		AccessController::allowedOrNot($this->module_name, 'can_print');
		$exam_id = Input::get('exam_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$student_id = Input::get('student_id', NULL);
		$is_final = Input::get('is_final', 'no');

		$exam_name = '';
		try
		{
			$exam_name = ExamConfiguration::find($exam_id)->exam_name;
		}
		catch (Exception $e)
		{
			// default exam_name has already been assigned as empty string
		}

		$tablename = $is_final == 'no' ? Report::getTableName() : FinalReport::getTableName();
		$view_name = $is_final == 'no' ? 'mass-print' : 'mass-print-final';
		
		if(is_null($student_id))
		{
			$data = DB::table($tablename)
						->where('exam_id', $exam_id)
						->where('class_id', $class_id)
						->where('section_id', $section_id)
						->get();
		}
		else
		{
			$data = DB::table($tablename)
						->where('student_id', $student_id)
						->where('exam_id', $exam_id)
						->where('class_id', $class_id)
						->where('section_id', $section_id)
						->get();
		}

		$class_name = HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0));
		$section_code = HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0));

		return View::make($this->view . $view_name)
							->with('data', $data)
							->with('class_name', $class_name)
							->with('section_code', $section_code)
							->with('exam_name', $exam_name);
	}

	public function getReportList()
	{
		AccessController::allowedOrNot('report', 'can_view');
		//$input = Input::all();
		$result = array();
		$data = Report::select(DB::raw("`section_id`, COUNT(student_id) as total_students, `section_code`, `status`, ".Config::get("database.connections.mysql.prefix").Report::getTableName().".created_at, ".Config::get("database.connections.mysql.prefix").Report::getTableName().".updated_at"))
					  ->join(Section::getTableName(), Section::getTableName().'.id', '=', Report::getTableName().'.section_id')
					  ->where(Report::getTableName().'.is_active', 'yes')
					  ->where('class_id', Input::get('class_id_list', 0))
					  ->where('exam_id', Input::get('exam_id_list',0))
					  ->where('session_id', Input::get('session_id_list',0))
					  ->groupBy('section_id')
					  ->groupBy('status')
					  ->orderBy('section_id')
					  ->get();

		foreach($data as $d)
		{
			if(isset($result[$d->section_id]['total_students']))
			{
				$result[$d->section_id]['total_students'] += $d->total_students;	
			}
			else
			{
				$result[$d->section_id]['total_students'] = 0;
				$result[$d->section_id]['total_students'] += $d->total_students;
			}

			if($d->status == 'Passed')
			{
				$result[$d->section_id]['passed'] = $d->total_students;
			}
			else
			{
				$result[$d->section_id]['failed'] = $d->total_students;	
			}

			$result[$d->section_id]['section_code'] = $d->section_code;
			$result[$d->section_id]['created_at'] = $d->created_at;
			$result[$d->section_id]['updated_at'] = $d->updated_at;
			
		}

		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		
		return View::make($this->view.'report')
					->with('role', $this->role)
					->with('data', $result)
					->with('config', $config);
	}

	public function postConfig()
	{
		AccessController::allowedOrNot('report', 'can_config');
		$config = array(
								'show_percentage' => Input::get('show_percentage'),
								'show_grade'	=> Input::get('show_grade'),
								'show_grade_point'	=> Input::get('show_grade_point'),
							);
		if(File::put(REPORT_CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT)))
		{
			Session::flash('success-msg', 'config saved');
		}
		else
		{
			Session::flash('error-msg', 'error!');
		}
		return Redirect::back();

	}

	public function getReportClass()
	{
		AccessController::allowedOrNot('report', 'can_view');
		$data = Report::join(
			StudentRegistration::getTableName(),
			StudentRegistration::getTableName().'.id', '=', 
			Report::getTableName().'.student_id'
		)
		
		->where(Report::getTableName() . '.exam_id', Input::get('exam_id', 0))
		->where(Report::getTableName() . '.section_id', Input::get('section_id', 0))
		->where(Report::getTableName() . '.class_id', Input::get('class_id', 0))
		->where(Report::getTableName().'.is_active', 'yes')
		->select(array(
			Report::getTableName().'.*', 
			'sex', 
			'student_name',
		))
		->orderBy('percentage', 'DESC')
		->groupBy(StudentRegistration::getTableName() . '.id')
		->get();

		return View::make($this->view.'report-class')
					->with('role', $this->role)
					->with('data', $data)
					->with('module_name', $this->module_name);
	}

	public function getReportSingle()
	{
		AccessController::allowedOrNot('report', 'can_view');
		$data = ReportHelperController::getReportData(
			Input::get('exam_id', 0),
			Input::get('student_id', 0)
		);
		
		return View::make($this->view.'report-single')
					->with('role', $this->role)
					->with('data', $data);
	}

	public function getReportGenerate()
	{

	}

	public function getReportGenerateLedger()
	{
		AccessController::allowedOrNot($this->module_name, 'can_generate_ledger');

		$session_id = Input::get('ledger_session_id');
		$exam_id = Input::get('exam_id');
		$class_id = Input::get('class_id');

		$marks_table = ExamMarks::getTableName();
		$subjects_table = Subject::getTableName();
		$exam_detail_table = ExamDetails::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$student_table = Student::getTableName();
		$report_table = Report::getTableName();
		
		$unprocessed_data = DB::table($report_table)
				->join($student_table, function ($join) use ($student_table, $marks_table, $exam_id, $class_id, $session_id, $report_table)
				{
					$join->on($student_table.'.student_id', '=', $report_table.'.student_id')
						->where($student_table.'.current_session_id', '=', $session_id)
						->where($student_table.'.current_class_id', '=', $class_id)	
						->where($report_table.'.exam_id', '=', $exam_id);
				})
				->where('exam_id', $exam_id)
				->where('session_id', $session_id)
				->where('class_id', $class_id)
				->orderBy($report_table.'.class_id', 'ASC')
				->orderBy($report_table.'.section_id', 'ASC')
				->orderBy('current_roll_number', 'ASC')
				->get();

		$data['section_data'] = [];
		foreach($unprocessed_data as $d)
		{
			$data['section_data'][$d->section_id][] = $d;
		}

		$class_name = Classes::where('id', $class_id)->pluck('class_name');
		$exam_name = ExamConfiguration::where('id', $exam_id)->pluck('exam_name');
/////////// Here /////////////////////
		//$data = ['data' => $return_data, 'section_data' => $section_data];

		Excel::create($class_name.' - '.$exam_name, function($excel) use ($data, $class_name, $exam_name)
		{
			foreach($data['section_data'] as $section_id => $marks)
			{
				$excel->sheet(Section::where('id', $section_id)->pluck('section_code'), function($sheet) use ($data, $section_id, $class_name, $exam_name)
				{
					$sheet->row(1, ['Marks Ledger of ' . $exam_name . ' - '. $class_name . ' '.(Section::where('id', $section_id)->pluck('section_code'))]);

					$i = 1;

					foreach($data['section_data'][$section_id] as $student_id => $d)
					{
					
						$json = json_decode($d->exam_details, true);
						
						if($i == 1  && isset($data['section_data'][$section_id][0]))
						{
							$row = [];
							$row[] = 'SN';
							$row[] = 'Roll';
							$row[] = 'Name';

							//new line added here for Ledger Issue
							$ledger_subject_id = [];
							foreach($json['exam_details']['graded_sub_details'] as $subject_id => $subject)
							{
								//new line added here for Ledger Issue
								$ledger_subject_id[] = $subject_id;
								$row[] = $subject['subject_name'] . ' Th FM: '.$subject['theory_full_marks'].' Th PM: '.$subject['theory_pass_marks'];
								
								$row[] = $subject['subject_name'] . ' Pr FM: '.$subject['practical_full_marks'].' Pr PM: '.$subject['practical_pass_marks'];

								$row[] = 'Combined';

								$row[] = 'Cas Marks';

								$row[] = 'Cas + Combined';
								//$row[] = 'Th + Pr';
							}

							
							$row[] = 'Total';
							$row[] = 'Percentage';
							$row[] = 'CGPA';
							$row[] = 'Grade';
							$row[] = 'Rank';

							$sheet->row(2, $row);	
						}
						

						$row = [];
						$row[] = $i++;
						$row[] = $json['personal_details']['roll'];
						$row[] = $json['personal_details']['student_name'];
						
						//new line aded here for Ledger Issue
						foreach($ledger_subject_id as $subject_id)
						{
							$row[] = $json['exam_details']['graded_sub_details'][$subject_id]['theory_marks'];
							
							$row[] = $json['exam_details']['graded_sub_details'][$subject_id]['practical_marks'];

							$row[] = $json['exam_details']['graded_sub_details'][$subject_id]['combined_marks'];

							$row[] = $json['exam_details']['graded_sub_details'][$subject_id]['cas_marks'];

							$row[] = $json['exam_details']['graded_sub_details'][$subject_id]['cas_sub_combined_marks'];
						}

						$row[] = $json['summary']['cas_sub_combined_total'];
						$row[] = $d->percentage;
						$row[] = $d->cgpa;
						$row[] = $d->grade;
						if($d->status == 'Passed')
							$row[] = $d->rank;
						else
							$row[] = $d->status;

						$sheet->row($i+1, $row);
					}
			
				});	
			}
			
		})->download('xls');

		die();

			
//////////////////////////////////

			View::make($this->view.'report-ledger')
					->with('data', ['data' => $return_data, 'section_data' => $section_data])
					->with('session_id', $session_id)
					->with('class_id', $class_id)
					->with('exam_id', $exam_id);
		
	}

	public function postEditRemarks() 
	{
		AccessController::allowedOrNot('report', 'can_create,can_edit');
		$data = Input::all();

		$id = $this->updateInDatabase($data);

		Session::flash('success-msg', 'Record successfully created');

		return Redirect::to(
			URL::previous()
		);
	}

	//has error when exam details id does not contain the given subject
	public function postReportGenerate()
	{
		AccessController::allowedOrNot('report', 'can_generate_report');
		$input = Input::all();
		if(isset($input['session_id']) && isset($input['exam_id']) && isset($input['class_id']) && isset($input['section_id']))
		{
			//get graded subjects marks
			$exam_marks_table = ExamMarks::getTableName();
			$subject_table = Subject::getTableName();
			$exam_details_table = ExamDetails::getTableName();
			//get all students marks
			$data = DB::table($exam_marks_table)
						->join(
					$subject_table,
					$subject_table . '.id', '=',
					$exam_marks_table . '.subject_id'
				)
				->where(
					$exam_marks_table . '.exam_id', 
					$input['exam_id']
				)
				->where(
					$exam_marks_table . '.session_id', 
					$input['session_id'])
				->where(
					$exam_marks_table . '.class_id', 
					$input['class_id']
				)->where(
					$exam_marks_table . '.section_id', 
					$input['section_id']
				)->where(
					$subject_table . '.class_id',
					$input['class_id']
				)->where(
					$subject_table . '.section_id',
					$input['section_id']
				)/*->where(
					$subject_table . '.is_graded', 'yes'
				)*/
				//->groupBy('student_id')
				->orderBy('student_id', 'ASC')
				->orderBy($subject_table.'.sort_order', 'ASC')
				->select($exam_marks_table . '.*', $subject_table.'.subject_name', $subject_table.'.is_graded')
				->get();

				//get not graded subject marks
				

			//get exam conditions
			$exam_conditions = ExamDetails::join(
				Subject::getTableName(),
				Subject::getTableName() . '.id', '=',
				ExamDetails::getTableName() . '.subject_id'
			)->where(
				Subject::getTableName() . '.class_id',
				$input['class_id']
			)->where(
				Subject::getTableName() . '.section_id',
				$input['section_id']
			/*)->where(
				Subject::getTableName() . '.is_graded', 'yes'*/
			)->where(
				ExamDetails::getTableName() . '.exam_id', 
				$input['exam_id']
			)->where(
				ExamDetails::getTableName() . '.is_active', 'yes'
			)->select(
				ExamDetails::getTableName() . '.subject_id', 
				ExamDetails::getTableName() . '.pass_marks', 
				ExamDetails::getTableName() . '.full_marks',
				ExamDetails::getTableName() . '.practical_pass_marks', 
				ExamDetails::getTableName() . '.practical_full_marks'
				,'is_graded'
			)->orderBy(Subject::getTableName().'.sort_order', 'ASC')
			->get();

			
			$exam_conditions_result = array();
			$non_graded_subs = array();

			$full_marks = 0;
			foreach($exam_conditions as $e)
			{
					$exam_conditions_result[$e->subject_id] = array('pass_marks' => $e->pass_marks, 'full_marks' => $e->full_marks, 'practical_pass_marks' => $e->practical_pass_marks, 'practical_full_marks' => $e->practical_full_marks, 'is_graded' => $e->is_graded);

					if($e->is_graded == 'yes')
						$full_marks += $e->full_marks + $e->practical_full_marks;	
				
			}

			$cas_data = ReportHelperController::getCasMarksFromClassIdAndSectionId(['session_id' => $input['session_id'], 'class_id' => $input['class_id'], 'section_id' => $input['section_id'], 'exam_id' => $input['exam_id']]); //arguments has session_id, class_id, section_id, exam_id


			$exam = [];
			$exam[$input['exam_id']]['weightage'] = 100;
			//$exam[$input['exam_id']]['non_graded_sub_details'] = $non_graded_data;
			$exam[$input['exam_id']]['sub_details'] = $data;
			$exam[$input['exam_id']]['cas_details'] = $cas_data;
			$exam[$input['exam_id']]['exam_conditions'] = $exam_conditions_result;
			$exam[$input['exam_id']]['exam_id'] = $input['exam_id'];
			$exam[$input['exam_id']]['is_final'] = 'no';
			$exam[$input['exam_id']]['session_id'] = $input['session_id'];
			$exam[$input['exam_id']]['class_id'] = $input['class_id'];
			$exam[$input['exam_id']]['section_id'] = $input['section_id'];


			//try
			//{
				$json_data = ReportHelperController::convertMarksJson($exam);
			//}
			/*catch(Exception $e)
			{
				Session::flash('error-msg', 'Something went wrong while generating report.<br> Please verify the following: <Br> - Grade is set for the given class. <br> - routines of all the child exams and final exam is same <br> - marks for all the graded subjects are given in all examinations <br> - Please generate all the reports again. <Br>If the problem still persists contact the administrator. Sorry for the inconvenience');
				return Redirect::back();
			}*/

			try
			{
				$rank = array();
				DB::connection()->getPdo()->beginTransaction();
					$data_to_store = [];
					foreach($json_data as $student_id => $r) //index is student_id
					{
						$data_to_store[$student_id]['cgpa'] = $r['summary']['cas_sub_combined_gpa'];


						$data_to_store[$student_id]['percentage'] = $r['summary']['cas_sub_combined_percentage'];

						
						$rank[] = array('student_id' => $student_id, 'percentage' => (int) ($r['summary']['cas_sub_combined_percentage'] * 100));
						

						$data_to_store[$student_id]['session_id'] = $input['session_id'];
						$data_to_store[$student_id]['class_id'] = $input['class_id'];
						$data_to_store[$student_id]['section_id'] = $input['section_id'];
						$data_to_store[$student_id]['exam_id'] = $input['exam_id'];
						$data_to_store[$student_id]['student_id'] = $student_id;
						//$data_to_store[$student_id]['remarks'] = '';
						$data_to_store[$student_id]['rank'] = 'NA';
						$data_to_store[$student_id]['is_active'] = 'yes';
						$data_to_store[$student_id]['grade'] = $r['summary']['cas_sub_combined_grade'];
						$data_to_store[$student_id]['status'] = $r['summary']['status'];
						$data_to_store[$student_id]['exam_details'] = json_encode($r);
						$data_to_store[$student_id]['total_marks'] = $r['summary']['cas_sub_combined_total'];

						//check if exists
						$check = Report::where('student_id', $student_id)
										->where('section_id', $input['section_id'])
										->where('class_id', $input['class_id'])
										->where('session_id', $input['session_id'])
										->where('exam_id', $input['exam_id'])
										->pluck('id');

						if($check)
						{
							$data_to_store[$student_id]['id'] = $check;
							$this->updateInDatabase($data_to_store[$student_id]);
						}
						else
						{
							$this->storeInDatabase($data_to_store[$student_id]);
						}

					}
					usort($rank, function($a, $b) 
					{
						if ($a['percentage'] == $b['percentage']) 
						{
						    return 0;
						}
						return ($a['percentage'] > $b['percentage']) ? -1 : 1;
						//return (float) ( (float) $b['percentage'] - (float) $a['percentage']);
					});

					$prev_rank = -1;
					$prev_marks = -2;

					foreach($rank as $r)
					{
						if($data_to_store[$r['student_id']]['status'] == 'Passed')
						{
							if($prev_marks == -2)
							{
								$prev_marks = $r['percentage'];
								$prev_rank = 1;
								$rank = 1;
							}
							elseif($prev_marks == $r['percentage'])
							{

							}
							else
							{
								$prev_marks = $r['percentage'];
								$prev_rank++;
								$rank = $prev_rank;
							}	
						}
						else
						{
							$rank = 'none';	
						}

						$data_to_store[$r['student_id']]['rank'] = $rank;

						Report::where('student_id', $r['student_id'])
								->where('section_id', $input['section_id'])
								->where('class_id', $input['class_id'])
								->where('session_id', $input['session_id'])
								->where('exam_id', $input['exam_id'])
								->update(array('rank' => $rank));
					}


				DB::connection()->getPdo()->commit();
				Session::flash('success-msg', 'Report successfully generated');
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', $e->getMessage());
			}
		}
		else
		{
			Session::flash('error-msg', 'Please select all session_id, exam_id, class_id, section_id');
		}

		$remarks_attendance_data = Report::where('section_id', $input['section_id'])
								->where('class_id', $input['class_id'])
								->where('session_id', $input['session_id'])
								->where('exam_id', $input['exam_id'])
								->select('student_id', 'remarks', 'attendance')
								->get();

		$remarks = [];

		foreach($remarks_attendance_data as $index => $r)
		{
			$remarks[$r->student_id] = ['remarks' => $r->remarks, 'attendance' => $r->attendance];
			unset($remarks_attendance_data[$index]);
		}

		unset($remarks_attendance_data);

		return View::make($this->view.'.generate-report-enter-remarks')
					->with('data', $data_to_store)
					->with('remarks', $remarks)
					->with('session_id', $input['session_id'])
					->with('class_id', $input['class_id'])
					->with('section_id', $input['section_id'])
					->with('exam_id', $input['exam_id']);
		/*return Redirect::route('report-list', array(
				'session_id_list' => $input['session_id'],
				'class_id_list'		=> $input['class_id'],
				'exam_id_list'	=> $input['exam_id']
		));*/
	}

	public function postDeleteClassSection()
	{
		AccessController::allowedOrNot('report', 'can_delete');

		$input = Input::all();
		
		Report::where('section_id', $input['section_id'])
				->where('class_id', $input['class_id'])
				->where('session_id', $input['session_id'])
				->where('exam_id', $input['exam_id'])
				->delete();

		Session::flash('success-msg', 'Report Deleted!');

		return Redirect::to(URL::previous());
	}

	public function getReportGenerateRank()
	{
		AccessController::allowedOrNot($this->module_name, 'can_generate_report_rank');
		return View::make($this->view.'generate-rank');
	}

	public function postReportGenerateRank()
	{
    	AccessController::allowedOrNot($this->module_name, 'can_generate_report_rank');

		$input = Input::all();

		$session_id = $input['session_id'];
		$class_id = $input['class_id'];
		$exam_id = $input['exam_id'];

		$marks = Report::where('exam_id', $exam_id)
						->where('class_id', $class_id)
						->where('session_id', $session_id)
						->where('is_active', 'yes')
						->select('percentage', 'student_id', 'status', 'rank')
						->get();

		$students = [];
		$ranks = [];

		foreach($marks as $m)
		{
			$students[$m->student_id]['status'] = $m->status;
			$students[$m->student_id]['percentage'] = $m->percentage;
			$students[$m->student_id]['student_id'] = $m->student_id;
			$ranks[] = ['student_id' => $m->student_id, 'percentage' => $m->percentage];
		}

		unset($marks);

		usort($ranks, function($a, $b) 
		{
		    return $b['percentage'] > $a['percentage'];
		});
		
		$prev_rank = -1;
		$prev_marks = -2;

		try
		{
			DB::connection()->getPdo()->beginTransaction();
			foreach($ranks as $r)
			{
				if($students[$r['student_id']]['status'] == 'Passed')
				{
					if($prev_marks == -2)
					{
						$prev_marks = $r['percentage'];
						$prev_rank = 1;
						$rank = 1;
					}
					elseif($prev_marks == $r['percentage'])
					{
						$rank = $prev_rank;
					}
					else
					{
						$prev_marks = $r['percentage'];
						$prev_rank++;
						$rank = $prev_rank;
					}	
				}
				else
				{
					$rank = 'none';	
				}
				

				Report::where('student_id', $r['student_id'])
						->where('class_id', $input['class_id'])
						->where('session_id', $input['session_id'])
						->where('exam_id', $input['exam_id'])
						->update(array('rank' => $rank));
			}	
			DB::connection()->getPdo()->commit();
			Session::flash('success-msg', 'Ranks successfully updated');
			$route = URL::route('report-list').'?class_id_list='.$class_id.'&session_id_list='.$session_id.'&exam_id_list='.$exam_id;
			return Redirect::to($route);
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}
		

	}

	public function postEnterRemarks()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create_remarks');
		$input = Input::all();

		try
		{
			DB::connection()->getPdo()->beginTransaction();
			if($input['report_type'] == 'terminal')
			{
				foreach($input['remarks'] as $student_id => $remark)
				{
					$data_to_update = ['remarks' => $remark, 'attendance' => $input['attendance'][$student_id]];

					DB::table(Report::getTableName())
						->where('student_id', $student_id)
						->where('class_id', $input['class_id'])
						->where('section_id', $input['section_id'])
						->where('exam_id', $input['exam_id'])
						->update($data_to_update);
				}
			}
			else
			{
				foreach($input['remarks'] as $student_id => $remark)
				{
					$data_to_update = ['remarks' => $remark, 'attendance' => $input['attendance'][$student_id]];

					DB::table(FinalReport::getTableName())
						->where('student_id', $student_id)
						->where('class_id', $input['class_id'])
						->where('section_id', $input['section_id'])
						->where('exam_id', $input['exam_id'])
						->update($data_to_update);
				}
			}	
			Session::flash('success-msg', 'Remarks successfully entered');
			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::route('report-list');
	}
	public function getCasSetting(){

	AccessController::allowedOrNot('cas', 'can_view');
	
	$access = File::get(app_path().'/modules/'.$this->module_name.'/config/config.json');
	$access = json_decode($access, true);
	return View::make($this->view.'.cas-setting')->with('access', $access);
	}

	public function postCasSetting(){
	AccessController::allowedOrNot('cas', 'can_create');
	$json =Input::all();
	File::put(app_path().'/modules/'.$this->module_name.'/config/config.json', json_encode($json, JSON_PRETTY_PRINT));
	if ($json) {
			return Redirect::back()->with('success-msg','CAS Setting Set Successfully');
		}
		return Redirect::back()->with('error-msg','CAS Setting Set Error');
	}

	public function ajaxGetRemarks()
	{
		$term = Input::get('term', 0);

		$remarks = DB::table(RemarkSetting::getTableName())
					->where('remarks_number', 'LIKE', '%'.$term.'%')
					->select('remarks')
					->get();

		$data_to_return = [];
		$status = false;
		foreach($remarks as $r)
		{
			$status = true;
			$data_to_return[] = ['id' => $r->remarks, 'label' => $r->remarks];

		}

		if(!$status)
		{
			$data_to_return[] = ['id' => 'No remarks found', 'label' => 'No remarks found'];
		}

		return $data_to_return;
	}
	
}