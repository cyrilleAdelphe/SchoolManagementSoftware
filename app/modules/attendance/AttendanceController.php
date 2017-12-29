<?php
use Carbon\Carbon;

class AttendanceController extends BaseController
{
	protected $view = 'attendance.views.';

	protected $model_name = 'Attendance';

	protected $module_name = 'attendance';

	protected $role;

	public $current_user;

	public $columnsToShow = array(
									array(
										'column_name' 	=> 'session_name',
										'alias'			=> 'Session'
									),
									array(
										'column_name' 	=> 'attendance_date',
										'alias'			=> 'Date'
									),
									array(
										'column_name' 	=> 'student_id',
										'alias'			=> 'Student'
									),
									array(
										'column_name' 	=> 'student_status',
										'alias'			=> 'Status'
									)
								 );

	public function getViewStudent()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$input = Input::all();


		$input['date_range'] = explode(' - ', $input['date_range']);
		foreach($input['date_range'] as $index => $d)
		{
			$input['date_range'][$index] = Carbon::createFromFormat('m/d/Y', $d);
		}

		$data = [];
		for($i = $input['date_range'][0]; $i->lte($input['date_range'][1]); $i->addDay())
		{
			$temp = $i;
			$temp = $temp->format('Y-m-d');
			
			$record = AttendanceHelperController::getAttendanceRecords($temp, $input['class_id'], $input['section_code']);

			if(isset($record[$input['student_id']]))
			{
				$data[] = ['date' => $temp, 'status' => $record[$input['student_id']]['attendance_status'], 'comment' => $record[$input['student_id']]['attendance_comment']];
			}
			
		}

		$student = StudentRegistration::where('id', $input['student_id'])
										->first();
		
		return View::make($this->view.'view-student')
				->with('data', $data)
				->with('student', $student)
				->with('class_id', $input['class_id'])
				->with('section_code', $input['section_code']);
	}

	public function postViewStudent()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$result = array();
		$attendance_records = array();
		
		if(!StudentRegistration::find(Input::get('student_id')))
		{
			$result['status'] = 'error';
			$result['msg'] = 'Student ID not found';
		}
		else
		{
			$current_session_id = AcademicSession::where('is_current', 'yes')
												->first()
												->id;

			$student = Student::where('current_session_id', $current_session_id)
								->where('student_id', Input::get('student_id'))
								->first();

			if(!$student)
			{
				$result['status'] = 'error';
				$result['msg'] = 'student is not registered for the current session';
			}
			else
			{
				$class_id = $student->current_class_id;	
				$section_code = $student->current_section_code;
				$date_range = Input::get('date_range', '');
				if(strlen($date_range) == 0)
				{
					$start_date = Carbon::today()->subDays(7)->format('Y-m-d');
					$end_date = Carbon::today()->addDay()->format('Y-m-d');
					
				}
				else
				{
					$date_range = explode(' - ', $date_range);
					$start_date = Carbon::createFromFormat('m/d/Y', trim($date_range[0]))->format('Y-m-d');
					
					$end_date = Carbon::createFromFormat('m/d/Y', trim($date_range[1]))->format('Y-m-d');
					
				}

				$date = Carbon::createFromFormat('Y-m-d', $start_date);
				$end_date = Carbon::createFromFormat('Y-m-d', $end_date);

				
				$attendance_records['name'] = StudentRegistration::where('id',$student->student_id)->first()['student_name'];
				$attendance_records['roll'] = $student->current_roll_number;
				$attendance_records['attendace_status'] = array();
				
				while($date->lte($end_date))
				{
					$data = AttendanceHelperController::getAttendanceRecords($date->format('Y-m-d'), $class_id, $section_code);
					
					if(is_array($data))
					{
						foreach($data as $student_id => $d)
						{

							if($student_id == $student->student_id)
							{
								if($d['attendance_status'] == 'p')
								{
									$attendance_records['attendance_status'][$date->format('Y-m-d')]['status'] = 'Present';
								}
								elseif($d['attendance_status'] == 'a')
								{
									$attendance_records['attendance_status'][$date->format('Y-m-d')]['status'] = 'Absent';
								}
								else
								{
									$attendance_records['attendance_status'][$date->format('Y-m-d')]['status'] = 'Late';
								}	
							}
							$attendance_records['attendance_status'][$date->format('Y-m-d')]['remarks'] = $d['attendance_comment'];
						}	
					}
					$date->addDay();
				}

			}

			$result['status'] = 'success';


		}

				
		return View::make($this->view.'js-template.view-student')
						->with('result', $attendance_records)
						->with('res', $result);
	}

	public function getCreate()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$classes = Classes::where('is_active','yes')->get();
		$class_options = array();
		foreach($classes as $class)
		{
			$class_options[$class['id']] = $class['class_code'];
		}
		
		if(CALENDAR == 'BS')
		{
			$date = (new DateConverter)->ad2bs(date('Y-m-d'));
			$date_array = explode('-', $date);
			$date = $date_array[2] . '/' . 
							str_pad($date_array[1], 2, '0', STR_PAD_LEFT) . '/' .
							$date_array[0];
		}
		else
		{
			$date = date('d/m/Y');
		}
		
		return View::make('attendance.views.select-class-section')
				->with('class_options',$class_options)
				->with('date', $date);
	}

	public function postCreate()
	{
		//die('here');
		AccessController::allowedOrNot($this->module_name, 'can_create,can_edit');

		$validator = Validator::make(
										array('date' => Input::get('date')),
										array('date' => array('required', 'regex:#^[\d]{1,2}/[\d]{2}/[\d]{4}$#'))
									);
		if ($validator->fails())
		{
			return '<h3>' . 'Invalid date' . '</h3>';
		}

		
		$student_ids = explode(',', Input::get('student_ids'));
		$output_array = array();
		foreach($student_ids as $id)
		{
			$output_array[] = [$id, Input::get('attendance'.$id),Input::get('comment'.$id)];
		}
		
		$section_code = Section::find(Input::get('section_id'))->section_code;

		if(CALENDAR == 'BS')
		{
			$date_array = explode('/', Input::get('date'));
			$date = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
			$date = (new DateConverter)->bs2ad($date);
		}
		else
		{
			$date = DateTime::createFromFormat('d/m/Y', Input::get('date'))->format('Y-m-d');
		}
		$filename = ATTENDANCE_RECORD_LOCATION.$date ."_".Input::get('class_id')."_".$section_code.".csv";

		$file_handle = fopen($filename,'w');
		foreach($output_array as $record)
		{
			fputcsv($file_handle, $record);
		}

		fclose($file_handle);
		//chmod($filename, 0777);

		// send push notifications
		if(Input::get('pushNotification')=='yes')
		{
			foreach($student_ids as $id)
			{
				if(Input::get('attendance'.$id) == 'a')
				{
					AttendanceHelperController::sendPushNotification($id, 
															Input::get('date'), 
															Input::get('attendance'.$id),
															Input::get('comment'.$id));
				}
			}
		}


		/* remove this code Attendance-v1-changes-made-here 
		if(Auth::superadmin()->check())
		{
			Session::flash('success-msg', 'Record successfully created');
			return Redirect::route('attendance-create-get');
		}
		elseif(Auth::admin()->check())
		{
			Session::flash('success-msg', 'Record successfully created');
			return Redirect::route('attendance-create-teacher');
		}*/

		////////// Attendance-v1-changes-made-here ///////
		Session::flash('success-msg', 'Record successfully created');
			return Redirect::back();
		////////// Attendance-v1-changes-made-here ///////
		
	}

	public function postDeleteAttendanceFile($filename)
	{
		$original_filename = $filename;
		$filename = ATTENDANCE_RECORD_LOCATION.$filename;
		try
		{

			if(File::exists($filename))
			{
				file_put_contents(ATTENDANCE_RECORD_LOCATION.'_'.$original_filename, File::get($filename));
				unlink($filename);
				Session::flash('success-msg', 'Attendance Record Successfully Deleted')	;
			}

			
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}


		return Redirect::back();
	}

	/*
	 * Returns a class's section (with class_id as a get parameter)
	 */
	public function ajaxGetClassSection() 
	{
		if(!Input::has('class_id')) return;
		
		$class_id = Input::get('class_id');

		// old way
		// $section = ClassSection::where('class_id',$class_id)
		// 							->where('is_active','yes')
		// 							->get();

		// new way
		$class_section_table = ClassSection::getTableName();
		$section_table = Section::getTableName();

		$section = DB::table($section_table)
						->join($class_section_table, $class_section_table.'.section_code', '=', $section_table.'.section_code')
						->where($section_table.'.is_active', 'yes')
						->where($class_section_table.'.is_active', 'yes')
						->where($class_section_table.'.class_id', $class_id)
						->select($section_table.'.id', $section_table.'.section_code')
						->get();
		return json_encode($section);

	}


	/*
	 * Returns the students of a give class & section
	 */
	public function ajaxPostStudents()
	{
		$students = AttendanceHelperController::getStudents();

		//return json_encode($students);

		return View::make($this->view.'js-template.student-list')
					->with('students', $students);
	}

	/*
	 * Returns attendance form
	 */
	public function ajaxPostAttendanceForm()
	{
		$validator = Validator::make(
										array('date' => Input::get('date')),
										array('date' => array('required', 'regex:#^[\d]{1,2}/[\d]{2}/[\d]{4}$#'))
									);
		if ($validator->fails())
		{
			return '<h3>' . 'Invalid date' . '</h3>';
		}

		if (CALENDAR == 'BS')
		{
			// checking if the BS date actually exists
			$date_array = explode('/', Input::get('date'));
			$date = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
			$date = (new DateConverter)->bs2ad($date);
			if (!$date)
			{
				return '<h3>' . 'Invalid date' . '</h3>';
			}
		}

		$students = AttendanceHelperController::getStudents();
		
		// TODO: make this better
		$student_ids = array();
		foreach($students as $student)
		{
			$student_ids[] = $student['student_id'];
		}

		// echo '<pre>';
		// return json_encode($students, JSON_PRETTY_PRINT);

		return View::make($this->view.'js-template.attendance-form')
					->with('students', $students)
					->with('class_id', Input::get('class_id'))
					->with('section_id', Input::get('section_id'))
					->with('student_ids', implode(',', $student_ids));
	}

	public function getViewHistoryStudent($start_date, $end_date, $student_id)
	{
		
		AccessController::allowedOrNot($this->module_name, 'can_view');
		return View::make($this->view.'view-student-history');
	}

	public function getViewHistoryClassSection()
	{

		AccessController::allowedOrNot($this->module_name, 'can_view');
		return View::make($this->view.'view-class-section-history');
	}

	public function ajaxGetViewHistoryClassSection()
	{
		$class_id = Input::get('class_id', 0);
		$section_code = HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0));
		$date_range = Input::get('date_range', '');
		$academic_session_id = Input::get('academic_session_id', '');
		if(strlen($date_range) == 0)
		{
			$start_date = Carbon::today()->subDays(7)->format('Y-m-d');
			$end_date = Carbon::today()->addDay()->format('Y-m-d');
			
		}
		else
		{
			$range = $date_range;
			$date_range = explode(' - ', $date_range);
			$start_date = Carbon::createFromFormat('m/d/Y', trim($date_range[0]))->format('Y-m-d');
			
			$end_date = Carbon::createFromFormat('m/d/Y', trim($date_range[1]))->format('Y-m-d');
			
		}

		$date = Carbon::createFromFormat('Y-m-d', $start_date);
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date);

		$attendance_records = array();
		
		while($date->lte($end_date))
		{
			$data = AttendanceHelperController::getAttendanceRecords($date->format('Y-m-d'), $class_id, $section_code);
			
			if(is_array($data))
			{
				foreach($data as $student_id => $d)
				{
					if(isset($attendance_records[$student_id]))
						{
							if($d['attendance_status'] == 'p')
							{
								$attendance_records[$student_id]['present_days'] += 1;
							}
							elseif($d['attendance_status'] == 'a')
							{
								$attendance_records[$student_id]['absent_days'] += 1;
							}
							else
							{
								$attendance_records[$student_id]['late_days'] += 1;
							}
						}
						else
						{
							$attendance_records[$student_id]['name'] = StudentRegistration::where('id',$student_id)->first()['student_name'];
							$attendance_records[$student_id]['last_name'] = StudentRegistration::where('id',$student_id)->first()['last_name'];
							$attendance_records[$student_id]['student_id'] = $student_id;
							$attendance_records[$student_id]['roll'] = Student::where('student_id',$student_id)
							->where('current_session_id', $academic_session_id)
							->where('current_class_id', $class_id)
							->where('current_section_code', $section_code)->first()['current_roll_number'];
							if($d['attendance_status'] == 'p')
							{
								$attendance_records[$student_id]['present_days'] = 1;
								$attendance_records[$student_id]['absent_days'] = 0;
								$attendance_records[$student_id]['late_days'] = 0;
							}
							elseif($d['attendance_status'] == 'a')
							{
								$attendance_records[$student_id]['present_days'] = 0;
								$attendance_records[$student_id]['absent_days'] = 1;
								$attendance_records[$student_id]['late_days'] = 0;
							}
							else
							{
								$attendance_records[$student_id]['present_days'] = 0;
								$attendance_records[$student_id]['absent_days'] = 0;
								$attendance_records[$student_id]['late_days'] = 1;
							}	
						}
				}	
			}
			

			$date->addDay();
		}
			
		return View::make($this->view.'js-template.class-section-history')
				->with('start_date', Carbon::createFromFormat('Y-m-d', $start_date))
				->with('end_date', $end_date)
				->with('class_id', $class_id)
				->with('section_code', $section_code)
				->with('data', $attendance_records)
				->with('date_range', $range);
	}

	
}