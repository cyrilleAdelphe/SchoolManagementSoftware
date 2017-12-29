<?php

	class ApiHelperController
	{
		public static function getRelatedStudents($role, $id)
		{
			$data = array();
			
			if($role == 'guardian')
			{
				$current_session_id = HelperController::getCurrentSession();
				$data = DB::table(StudentGuardianRelation::getTableName())
						   ->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.student_id')
						   ->join(Student::getTableName(), Student::getTableName().'.student_id', '=', StudentRegistration::getTableName().'.id')
						   ->join(Section::getTableName(), Section::getTableName().'.section_code', '=', Student::getTableName().'.current_section_code')
						   ->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.current_class_id')
						   ->where(StudentGuardianRelation::getTableName().'.guardian_id', $id)
						   ->where(StudentGuardianRelation::getTableName().'.is_active', 'yes')
						   ->where('current_session_id', $current_session_id)
						   ->select(
						   		'student_name', 
						   		Student::getTableName().'.student_id', 
						   		'current_session_id', 
						   		'current_section_code', 
						   		'current_roll_number', 
						   		'current_class_id', 
						   		Section::getTableName().'.id as current_section_id',
						   		Classes::getTableName().'.class_name as current_class_name'
						   	)
						   ->get();
			}

			return $data;
		}

		public static function getStudentsForAttendance($class_id, $section_code, $date)
	{
		$section = ClassSection::where('section_code',$section_code)
									->where('class_id',$class_id)
									->where('is_active','yes')
									->first();

		if(!$section)
		{
			return $false;
		}

		$students = Student::where('current_class_id',$class_id)
							->where('current_section_code',$section_code)
							->where('is_active','yes')
							->select('student_id')
							->get();
		
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
					$attendance_record[$record_array[0]] = [$record_array[1],$record_array[2]];
			}
		}

		foreach($students as $student)
		{
			$student['name'] = StudentRegistration::where('id',$student['student_id'])->first()['student_name'];
			$student['last_name'] = StudentRegistration::where('id',$student['student_id'])->first()['last_name'];
			$student['name'] = $student['name'] .' '. $student['last_name'];
			$student['attendance_status'] = (isset($attendance_record[$student['student_id']][0])) ? $attendance_record[$student['student_id']][0] : 'p';
			$student['attendance_comment'] = (isset($attendance_record[$student['student_id']][1])) ? $attendance_record[$student['student_id']][1] : '';
		}

		return $students;
	}
}