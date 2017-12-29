<?php


class AttendanceHelperController
{
	public static function sendPushNotification($id, $date, $attendance_status, $attendance_comment)
	{
		$student_table = Student::getTableName();
		$student_guardian_relation_table = StudentGuardianRelation::getTableName();
		$guardian_table = Guardian::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$gcm_table =  PushNotifications::getTableName();

		$student_id = $id;

		$parent_ids = DB::table($guardian_table)
						->join($student_guardian_relation_table, $student_guardian_relation_table.'.guardian_id', '=', $guardian_table.'.id')
						->join($student_registration_table, $student_registration_table.'.id', '=', $student_guardian_relation_table.'.student_id')
						//->leftJoin($gcm_table, $gcm_table.'.user_id', '=', $guardian_table.'.id')
						->where($student_registration_table.'.id', $student_id)
						//->where($gcm_table.'.user_group', 'guardian');
						->select($guardian_table . '.id')
						->lists('id');

		$gcm_ids = PushNotifications::where('user_group', 'guardian')
									->whereIn('user_id', $parent_ids)
									->select('gcm_id')
									->lists('gcm_id');
			
		
		$module_name = 'attendance';

		$student_name = StudentRegistration::find($student_id)
												->student_name;

		$msg = $module_name . ' # '.
					$student_name . ' was absent on '.
					substr($date, 0, 10);

		if($attendance_comment)
		{
			$msg = $msg . ' (' . $attendance_comment .')';
		}

		if(count($parent_ids)) 
		{
			(new GcmController)->send($gcm_ids, $msg, $parent_ids, 'guardian');
		}
	}

	public function getStudentIdFromRollNumber($roll_numbers = array())
	{
		$students = array();
		if(count($roll_numbers))
		{
			$students = DB::table(Students::getTableName())
							->where('current_session_id', Session::get('current_session_id'))
							->whereIn('current_roll_number', $roll_numbers)
							->list('student_id', 'current_roll_number');
		}
		
		return $students;
	}

	public static function getStudents()
	{
		//TODO: validation: validate that user has selected a class / section
		if(!Input::has('class_id') || !Input::has('section_id'))
		{
			return;
		}

		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$section_code = Section::find($section_id)->section_code;
		
		$section = ClassSection::where('section_code',$section_code)
									->where('class_id',$class_id)
									->where('is_active','yes')
									->first();

		if(!$section)
		{
			return 'section no present in the given class';
		}

		$student_table = Student::getTableName();
		$student_registration_table = StudentRegistration::getTableName();

		$students = Student::join($student_registration_table, $student_registration_table.'.id', '=', 'student_id')
						->where('current_class_id',$class_id)
						->where('current_section_code',$section_code)
						->where($student_table.'.is_active','yes')
						->select($student_table.'.*')
						->orderBy('current_roll_number', 'ASC')
						->get();

		
		if(Input::has('date'))
		{
			if (CALENDAR == 'BS')
			{
				$date_array = explode('/', Input::get('date'));
				$date = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
				$date = (new DateConverter)->bs2ad($date);
			}
			else
			{
				$date = DateTime::createFromFormat('d/m/Y', Input::get('date'))->format('Y-m-d');
			}
		}
		else
		{
			$date = date('Y-m-d');
		}

		// reading attendance csv file

		$filename = ATTENDANCE_RECORD_LOCATION . $date .'_'.$class_id.'_'.$section_code.'.csv';
		if(File::exists($filename))
		{
			$csv = File::get($filename);
			$records = explode("\n", $csv);
			$attendance_record = array();
			foreach($records as $record)
			{
				$record_array = explode(',', $record);
				
				if(sizeof($record_array)==3)
					$attendance_record[$record_array[ID]] = [$record_array[ATTENDANCE_STATUS],$record_array[ATTENDANCE_COMMENT]];
			}
		}
		
		foreach($students as $student)
		{
			$student['name'] = StudentRegistration::where('id',$student['student_id'])->first()['student_name'];
			$student['last_name'] = StudentRegistration::where('id',$student['student_id'])->first()['last_name'];
			$student['attendance_status'] = (isset($attendance_record[$student['student_id']][0])) ? $attendance_record[$student['student_id']][0] : 'p';
			$student['attendance_comment'] = (isset($attendance_record[$student['student_id']][1])) ? $attendance_record[$student['student_id']][1] : '';
		}

		return $students;
	}

	public static function getAttendanceRecords($date, $class_id, $section_code)
	{
		
		$filename = ATTENDANCE_RECORD_LOCATION.$date.'_'.$class_id.'_'.$section_code.'.csv';
		

		$attendance_record = array();

		if(File::exists($filename))
		{
			$csv = File::get($filename);
			$records = explode("\n", $csv);
			$attendance_record = array();
			foreach($records as $record)
			{
				$record_array = explode(',', $record);
				if(sizeof($record_array)==3)
					$attendance_record[$record_array[ID]] = 
						[
							'attendance_status' => $record_array[ATTENDANCE_STATUS],
							'attendance_comment'=> $record_array[ATTENDANCE_COMMENT]
						];
			}
		}
		else
		{
			$attendance_record = false;
		}

		return $attendance_record;
	}
}