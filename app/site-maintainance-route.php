<?php 

Route::get('/clean-images', function()
{
	$employee_images = DB::table(Employee::getTableName())
						->lists('photo');

	$employee_cvs = DB::table(Employee::getTableName())
						->lists('cv');

	$student_images = DB::table(StudentRegistration::getTableName())
						->lists('photo');

	$guardian_images = DB::table(Guardian::getTableName())
						->lists('photo');

	$employee_folder_images = File::files(app_path().'/modules/employee/assets/images');

	$employee_folder_cv = File::files(app_path().'/modules/employee/assets/cv');

	$guardian_folder_images = File::files(app_path().'/modules/guardian/assets/images');

	$student_folder_images = File::files(app_path().'/modules/student/assets/images');

	foreach($employee_folder_images as $employee)
	{
		//substr(string, start)
		if(!in_array(substr($employee, strlen(app_path().'/modules/employee/assets/images/')), $employee_images) && $employee != app_path().'/modules/employee/assets/images/no-img.png')
		{
			//delete
			unlink($employee);
		}
	}

	foreach($employee_folder_cv as $employee)
	{
		//substr(string, start)
		if(!in_array(substr($employee, strlen(app_path().'/modules/employee/assets/cv/')), $employee_cvs))
		{
			//delete
			unlink($employee);
		}
	}

	foreach($guardian_folder_images as $guardian)
	{
		//substr(string, start)
		if(!in_array(substr($guardian, strlen(app_path().'/modules/guardian/assets/images/')), $guardian_images) && $guardian != app_path().'/modules/guardian/assets/images/no-img.png')
		{
			//delete
			unlink($guardian);
		}
	}

	foreach($student_folder_images as $student)
	{
		//substr(string, start)
		if(!in_array(substr($student, strlen(app_path().'/modules/student/assets/images/')), $student_images) && $student != app_path().'/modules/student/assets/images/no-img.png')
		{
			//delete
			unlink($student);
		}
	}

});

Route::get('/generate-qr-code', function()
{
	//echo 'here';

	//select class
	//select section
	$class_table = Classes::getTableName();
	$section_table = Section::getTableName();
	$class_section_table = ClassSection::getTableName();

	$current_session = HelperController::getCurrentSession();

	$classes = DB::table($class_section_table)
				->join($class_table, $class_table.'.id', '=', $class_section_table.'.class_id')
				->where('academic_session_id', $current_session)
				->get();

	foreach($classes as $c)
	{
		$class_name = Classes::where('id', $c->class_id)->pluck('class_name');
		$section_name = $c->section_code;

		$student_registration_table = StudentRegistration::getTableName();
		$student_table = Student::getTableName();
		$guardian_table = Guardian::getTableName();
		$student_guardian_relation_table = StudentGuardianRelation::getTableName();
		$users_table = Users::getTableName();

		echo $c->class_id.' '.$c->section_code.'<br>';

		$students = DB::table($student_guardian_relation_table)
						->join($student_registration_table, $student_guardian_relation_table.'.student_id', '=', $student_registration_table.'.id')
						->join($student_table, $student_table.'.student_id', '=', $student_registration_table.'.id')
						
						->join($guardian_table, $guardian_table.'.id', '=', $student_guardian_relation_table.'.guardian_id')

						->join($users_table, function ($join) use ($users_table, $student_registration_table){
				            $join->on($users_table.'.user_details_id', '=', $student_registration_table.'.id')
				                 ->where($users_table.'.role', '=', 'student');
				        })
				        ->join($users_table.' AS '.Config::get('database.connections.mysql.prefix').'table2', function ($join) use ($guardian_table, $student_registration_table){
				            $join->on('table2.user_details_id', '=', $guardian_table.'.id')
				                 ->where('table2.role', '=', 'guardian');
				        })
				        ->where('current_session_id', $current_session)
						->where('current_class_id', $c->class_id)
						->where('current_section_code', $c->section_code)
						->select('student_name', $student_table.'.student_id', 'guardian_id', $users_table.'.username as student_username', 'table2.username as guardian_username', $student_registration_table.'.photo as student_image', $guardian_table.'.photo as guardian_image', 'current_roll_number', 'guardian_contact', 'sex', $student_registration_table.'.current_address')
						->orderBy('student_id', 'ASC')
						->get();

		echo '<pre>';
		print_r($students);

		//create folder with class and section
		if(!File::exists(base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code))
		File::makeDirectory(base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code, 0777);
		$qr_codes = [];
		foreach($students as $s)
		{
			$qr_codes[$s->student_username][] = $s->guardian_username;
			
			if(!File::exists(base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code.'/'.$s->student_name))
				File::makeDirectory(base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code.'/'.$s->student_name, 0777);

				echo QrCode::format('svg')->encoding('UTF-8')->size(300)->generate(implode(':', $qr_codes[$s->student_username]).':'.$s->student_username, base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code.'/'.$s->student_name.'/'.$s->student_name.'.svg');

			if(strlen($s->student_image))
				File::copy(app_path().'/modules/student/assets/images/'.$s->student_image, base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code.'/'.$s->student_name.'/'.$s->student_image);

			File::put(base_path().'/public/qr-code/'.$class_name.'-'.$c->section_code.'/'.$s->student_name.'/description.txt', json_encode($s, JSON_PRETTY_PRINT));
		}
	}
});