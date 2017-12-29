<?php

class AjaxController extends Controller
{
	public function getExamIdsFromSessionId()
	{
		$session_id = Input::get('session_id', 0);
		$field_name = Input::get('field_name', 'exam_id');

		return HelperController::generateSelectList('ExamConfiguration', 'exam_name', 'id', 'exam_id', '', array(array('field' => 'session_id', 'operator' => '=', 'value' => $session_id)), true);
	}

	public function getSectionIdsFromClassId()
	{
		$class_id = Input::get('class_id', 0);
		$field_name = Input::get('field_name', 'section_id');

		//$select_list_value = Input::get('get_section_id', "false") == "false" ? 'section_code' : 'id';
		$section_ids_codes = DB::table(ClassSection::getTableName()) 
								->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
								->select(Section::getTableName().'.id', Section::getTableName().'.section_code')
								->where(ClassSection::getTableName().'.is_active', 'yes')
								->where(Section::getTableName().'.is_active', 'yes')
								->where('class_id', $class_id)
								->lists('section_code', 'id');

		//dd($section_ids_codes);

		return HelperController::generateStaticSelectList($section_ids_codes, 'section_id', $selected = 0);
	}

	public function getStudentsFromClassIdAndStudentId()
	{
		$student_id = Input::get('student_id', 0);
		$class_id = Input::get('class_id', 0);

		$students = DB::table(Report::getTableName())
						->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', 'exam_id')
						->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', Report::getTableName().'.exam_id')
						->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', 'student_id')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id')
						->join(Section::getTableName(), Section::getTableName().'.id', '=', 'section_id')
						->select(array('exam_name', 'student_name', 'class_name', 'section_code', 'session_name', Report::getTableName().'.*' ))
						->where('class_id', $class_id)
						->where('student_id', $student_id)
						->get();
		
		return json_encode($students);
	}

	public function getStudentsFromSessionIdAndStudentId()
	{
		$student_id = Input::get('student_id', 0);
		try
		{
			$student_id = Users::where('username', $student_id)->first()->user_details_id;	
		}
		catch(Exception $e)
		{
			return json_encode(array('status' => 'error', 'data' => 'student not found'));
		}
		
		$session_id = Input::get('session_id', 0);

		$students = DB::table(Report::getTableName())
						->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', 'exam_id')
						->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', Report::getTableName().'.session_id')
						->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', 'student_id')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id')
						->join(Section::getTableName(), Section::getTableName().'.id', '=', 'section_id')
						->select(array('exam_name', 'student_name', 'class_name', 'section_code', 'session_name', Report::getTableName().'.*' ))
						->where(AcademicSession::getTableName().'.id', $session_id)
						->where('student_id', $student_id)
						->get();
		
		return json_encode(array('status' => 'success', 'data' => $students));
	}

	public function getClassIdsFromSessionIdData($session_id, $default_class_id)
	{
		$data = DB::table(Classes::getTableName())
					->where('academic_session_id', $session_id)
					->orderBy('sort_order', 'ASC')
					->select('class_code', 'id')
					->get();

		$return_data = [];
		foreach($data as $d)
		{
			$temp = [];
			$temp['class_id'] = $d->id;
			$temp['class_code'] = $d->class_code;
			$temp['select'] = $d->id == $default_class_id ? 'yes' : 'no';
			$return_data[] = $temp;
		}

		unset($data);

		return $return_data;

		echo '<pre>';
		print_r($return_data);
		die();
	}

	public function getSectionIdsFromClassIdData($class_id, $default_section_id)
	{
		$class_id = Input::get('class_id');
		$default_section_id = Input::get('default_section_id');

		$class_section_table = ClassSection::getTableName();
		$section_table = Section::getTableName();

		$data = DB::table($class_section_table)
					->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
					->where('class_id', $class_id)
					->select($section_table.'.id', $section_table.'.section_code')
					->orderBy('section_code', 'ASC')
					->get();

		$return_data = [];
		foreach($data as $d)
		{
			$temp = [];
			$temp['section_id'] = $d->id;
			$temp['section_code'] = $d->section_code;
			$temp['select'] = $d->id == $default_section_id ? 'yes' : 'no';
			$return_data[] = $temp;
		}

		unset($data);

		return $return_data;
	}

	public function getClassIdsFromSessionIdHtml()
	{
		$session_id = Input::get('session_id');
		$default_class_id = Input::get('default_class_id');

		$data = $this->getClassIdsFromSessionIdData($session_id, $default_class_id);

		


		$html = '';
		$html .= '<select name="class_id" id="class_id" class="form-control">';

		foreach($data as $d)
		{
			$sel = $d['select'] == 'yes' ? 'selected' : '';
			$html .= '<option value = "'.$d['class_id'].'" '.$sel.'>'.$d['class_code'].'</option>';
		}
		$html .= '</select>';

		return $html;
	}

	public function getSectionIdsFromClassIdHtml()
	{
		$class_id = Input::get('class_id');
		$default_section_id = Input::get('default_section_id');

		$data = $this->getSectionIdsFromClassIdData($class_id, $default_section_id);

		$html = '';
		$html .= '<select name="section_id" id="section_id" class="form-control">';
		foreach($data as $d)
		{
			$sel = $d['select'] == 'yes' ? 'selected' : '';
			$html .= '<option value = "'.$d['section_id'].'" '.$sel.'>'.$d['section_code'].'</option>';
		}
		$html .= '</select>';

		return $html;	
	}

	public function getClassIdsFromSessionId()
	{
		$session_id = Input::get('session_id', 0);

		$data = DB::table(Classes::getTableName())
					->where('is_active', 'yes')
					->where('academic_session_id', $session_id)
					->orderBy('sort_order', 'ASC')
					->lists('class_code', 'id');

		//generateSelectList($modelname, $name, $value, $field_name, $selected = '', $condition = array())
		//return HelperController::generateSelectList('Classes', 'class_name', 'id', 'class_id', '', $condition = array(array('field' => 'academic_session_id', 'operator' => '=', 'value' => $session_id)));
		return HelperController::generateStaticSelectList($data, 'class_id');
	}

	public function getClassIdsFromExamId()
	{
		$exam_id = Input::get('exam_id', 0);

		$data = DB::table(ExamConfiguration::getTableName())
					->join(Classes::getTableName(), Classes::getTableName().'.academic_session_id', '=', ExamConfiguration::getTableName().'.session_id')
					->select('class_name', Classes::getTableName().'.id')
					->where(ExamConfiguration::getTableName().'.id', $exam_id)
					->lists('class_name', 'id');

		return HelperController::generateStaticSelectList($data, 'class_id');
	}

	public function getDashboardModalSearchList()
	{
		$group = Input::get('group', 'student');
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$checkbox = Input::get('checkbox', 'no');
		$module_name = Input::get('module_name');
		switch($group)
		{
			case 'student':
				$data = DB::table(StudentRegistration::getTableName())
							->join(Student::getTableName(), Student::getTableName().'.student_id', '=', StudentRegistration::getTableName().'.id')
							->join(Section::getTableName(), Student::getTableName().'.current_section_code', '=', Section::getTableName().'.section_code')
							->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
							->where(StudentRegistration::getTableName().'.is_active', 'yes')
							->where('current_class_id', $class_id)
							->where(Section::getTableName().'.id', $section_id)
							->where('role', 'student')
							->select(array('username', StudentRegistration::getTableName().'.id', 'student_name','last_name'))
							->orderBy('student_name', 'ASC')
							->get();

				break;

			case 'guardian':
				$data = DB::table(StudentGuardianRelation::getTableName())
							->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', 'student_id')
							->join(Guardian::getTableName(), Guardian::getTableName().'.id', '=', 'guardian_id')
							->join(Student::getTableName(), Student::getTableName().'.student_id', '=', StudentRegistration::getTableName().'.id')
							->join(Section::getTableName(), Student::getTableName().'.current_section_code', '=', Section::getTableName().'.section_code')
							->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', Guardian::getTableName().'.id')
							->where(StudentRegistration::getTableName().'.is_active', 'yes')
							->where(Guardian::getTableName().'.is_active', 'yes')
							->where(Users::getTableName().'.role', 'guardian')
							->where(Student::getTableName().'.current_session_id', HelperController::getCurrentSession())
							->where(Student::getTableName().'.current_class_id', $class_id)
							->where(Section::getTableName().'.id', $section_id)
							->select(array('username', 'guardian_name', 'student_name', Guardian::getTableName().'.id'))
							->orderBy('student_name', 'ASC')
							->get();

				break;

			case 'superadmin':
				$data = DB::table(SuperAdmin::getTableName())
							->where('is_active', 'yes')
							->select(array('username', 'name', 'id'))
							->orderBy('name', 'ASC')
							->get();

				break;

			case 'admin':
				$data = DB::table(Employee::getTableName())
							->join(Admin::getTableName(), Admin::getTableName().'.admin_details_id', '=', Employee::getTableName().'.id')
							->where(Employee::getTableName().'.is_active', 'yes')
							//->where('')
							->select('username', 'employee_name', Employee::getTableName().'.id')
							->orderBy('employee_name', 'ASC')
							->get();
				break;

			default: $data = array();
		}
		
		$all_usernames = array();
		foreach($data as $d) 
		{
			$all_usernames[] = $d->username;
		}
		$all_usernames = implode(',', $all_usernames);
		
		return View::make('ajax-views.dashboard-modal-search-list')
					->with('data', $data)
					->with('all_usernames', $all_usernames)
					->with('class_id', $class_id)
					->with('section_id', $section_id)
					->with('checkbox', $checkbox)
					->with('group', $group)
					->with('module_name', $module_name);
	}

	public function getModalEmployeeSearchList() 
	{
		$group_id = Input::get('group_id', 0);
		$data = DB::table(Group::getTableName())
			->join(
				EmployeePosition::getTableName(), 
				EmployeePosition::getTableName().'.group_id', 
				'=',
				Group::getTableName().'.id'
			)
			->join(
				Admin::getTableName(), 
				Admin::getTableName().'.admin_details_id', 
				'=', 
				EmployeePosition::getTableName().'.employee_id'
			)
			->join(
				Employee::getTableName(), 
				Employee::getTableName().'.id',
				'=', 
				EmployeePosition::getTableName().'.employee_id'
			)
			->where(Group::getTableName().'.id', $group_id)
			->select(Admin::getTableName().'.username', Employee::getTableName().'.employee_name', Employee::getTableName().'.id')
			->orderBy(Employee::getTableName().'.employee_name', 'ASC')
			->get();

		$all_usernames = array();
		foreach($data as $d) 
		{
			$all_usernames[] = $d->username;
		}
		$all_usernames = implode(',', $all_usernames);
		return View::make('ajax-views.modal-employee-search-list')
					->with('data', $data)
					->with('all_usernames', $all_usernames)
					->with('checkbox', 'no');
	}
	public function AjaxGetSubjectListAndTeacherListFromClassIdAndSectionId()
	{
		$session_id = Input::has('session_id') ?
		      		Input::get('session_id') : AcademicSession::where('is_current','yes')->first()['id'];
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);

		$section_code = Section::where('id', $section_id)->pluck('section_code');

		$teachers = Teacher::join(Admin::getTableName(), Admin::getTableName().'.admin_details_id', '=', Teacher::getTableName().'.teacher_id')
							->where('session_id', $session_id)
							->where('class_id', $class_id)
							->where('section_code', $section_code)
							->select('name', 'admin_details_id')
							->get();
							
		

		$subjects = Subject::where('class_id', $class_id)
							->where('section_id', $section_id)
							->select('subject_name', 'subject_code', 'id')
							->get();

		//return json_encode($subjects);
		$selectedSubjectTeacher = SubjectTeacher::where('class_id', $class_id)
												->where('section_id', $section_id)
												->where('session_id', $session_id)
												->get();

		$temp = array();
		foreach($selectedSubjectTeacher as $data)
		{
			$temp[$data->subject_id][] = $data->teacher_id; 
		}

		$selectedSubjectTeacher = $temp;

		return View::make('subject-teacher.ajax.subject-teacher-list')
					->with('subjects', $subjects)
					->with('teachers', $teachers)
					->with('selectedSubjectTeacher', $selectedSubjectTeacher);

	}

	///////// Exam-Marks-v1-changes-made-here ////////////
	/// if record present in teachers table then related subjects else all subjects
	public function getRelatedClasses()
	{
		$session_id = Input::get('session_id', 0);
		$default_class_id = Input::get('default_class_id', 0);
		//get user_id
		$current_user = HelperController::getCurrentUser();

		$related_classes = $this->returnRelatedClasses($session_id, $current_user->user_id);

		$html = '';

		$html .= '<option value = "0"> -- Select -- </option>';

		foreach($related_classes as $r)
		{
			$sel = $r->class_id == $default_class_id ? 'selected' : '';
			$html .= '<option value = "'.$r->class_id.'" '.$sel.'>'.$r->class_name.'</option>';
		}

		return $html;
	}



	/// this is helper function to check where user in teachers table or not
	public function returnRelatedClasses($session_id, $user_id) //returns array of classes
	{
		$teachers_table = Teacher::getTableName();
		$class_table = Classes::getTableName();

		$related_classes = DB::table($teachers_table)
							->join($class_table, $class_table.'.id', '=', $teachers_table.'.class_id')
							->where('teacher_id', $user_id)
							->where('session_id', $session_id)
							->select('class_id', 'class_name')
							->get();

		if(count($related_classes) == 0 || Auth::superadmin()->check())
		{
			$related_classes = DB::table($class_table)
								->where('academic_session_id', $session_id)
								->select('id as class_id', 'class_name')
								->get();
		}

		return $related_classes;
	}

	public function getRelatedSections()
	{
		$class_id = Input::get('class_id', 0);
		$default_section_id = Input::get('default_section_id', 0);
		//get user_id
		$current_user = HelperController::getCurrentUser();

		$related_classes = $this->returnRelatedSections($class_id, $current_user->user_id);

		$html = '';

		$html .= '<option value = "0"> -- Select -- </option>';

		foreach($related_classes as $r)
		{
			$sel = $r->section_id == $default_section_id ? 'selected' : '';
			$html .= '<option value = "'.$r->section_id.'" '.$sel.'>'.$r->section_code.'</option>';
		}

		return $html;
	}
	/// this is helper function to get related sections
	public function returnRelatedSections($class_id, $user_id) //returns array of classes
	{
		$teachers_table = Teacher::getTableName();
		$section_table = Section::getTableName();
		$class_section_table = ClassSection::getTableName();

		$related_sections = DB::table($teachers_table)
							->join($section_table, $section_table.'.section_code', '=', $teachers_table.'.section_code')
							->where('teacher_id', $user_id)
							->where('class_id', $class_id)
							->select($teachers_table.'.section_code', $section_table.'.id as section_id')
							->get();

		if(count($related_sections) == 0 || Auth::superadmin()->check())
		{
			$related_sections = DB::table($class_section_table)
								->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
								->where('class_id', $class_id)
								->select($section_table.'.id as section_id', $section_table.'.section_code')
								->get();
		}

		return $related_sections;
	}
	////////// Exam-Marks-v1-changes-made-here ///////////


}