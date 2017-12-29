<?php

use Carbon\Carbon;

class TeacherController extends BaseController
{
	protected $view = 'teacher.views.';

	protected $model_name = 'Teacher';

	protected $module_name = 'teacher';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'session_name',
										'alias'			=> 'Session'
									),
									array
									(
										'column_name' 	=> 'employee_name',
										'alias'			=> 'Teacher'
									),
									///// Teacher-show-username-v1-changes-here //
									array
									(
										'column_name' 	=> 'username',
										'alias'			=> 'Username'
									),
									///// Teacher-show-username-v1-changes-here //
									array
									(
										'column_name' 	=> 'class_name',
										'alias'			=> 'Class'
									),
									array
									(
										'column_name' 	=> 'section_code',
										'alias'			=> 'Section'
									),
									array
									(
										'column_name' 	=> 'is_class_teacher',
										'alias'			=> 'Class Teacher'
									)
								 );


	public function getDashboard() {
		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot('teacher', 'can_view');
			return View::make($this->view.'dashboard');
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}
	}


	public function getProfile() {

		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot('teacher', 'can_view');
			$id = Auth::admin()->user()->id;
			$teacher_details = DB::table('employees')
							->join('admins','admin_details_id','=','employees.id')
							->select('employees.*','admins.username')
							->where('employees.id',$id)	
							->first();

			$current_session_id = DB::table('academic_session')->where('is_current','yes')->where('is_active','yes')->pluck('id');

																	
			return View::make($this->view. 'teacher-profile')
							->with('teacher_details', $teacher_details)
							->with('current_session_id', $current_session_id);
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}


	}

	public function getLogout() {
		if(Auth::admin()->check())
		{
			Auth::admin()->logout();
			Session::flash('success-msg', 'Logged out Successfully');
     		return Redirect::route('superadmin-login');
		}
		else
		{	
			App::abort(403, 'You are not allowed to view this page');
		}
	}


	public function getCreateView()
	{   
		AccessController::allowedOrNot('teacher', 'can_create');
		$query_string = Input::all();
		$query_string['session_id'] = isset($query_string['session_id']) ? $query_string['session_id'] : 0;
		$query_string['class_id'] = isset($query_string['class_id']) ? $query_string['class_id'] : 0;
		$query_string['section_code'] = isset($query_string['section_code']) ? $query_string['section_code'] : 0;

		$model = $this->model_name;
		$model = new $model;

		$teachers = $model->getActiveTeachers();
		//get teacher here
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('role', $this->role)
					->with('teachers', $teachers)
					->with('query_string', $query_string)
					->with('actionButtons', $this->getActionButtons());
	}

	public function getEditView($id)
	{   
		AccessController::allowedOrNot('teacher', 'can_edit');
		$model = $this->model_name;
		$model = new $model;

		$teachers = $model->getActiveTeachers();
		//get teacher here
		$model = new $this->model_name;
		
		$data = $model->getEditViewData($id);		
		
		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('teachers', $teachers)
					->with('data', $data)
					->with('actionButtons', $this->getActionButtons());
	}


	public function getAttendanceViewHistory() {
		
		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot($this->module_name, 'can_view');
			$teacher_id = Auth::admin()->id();
			return View::make('teacher.attendance.views.view-class-section-history')
					->with('teacher_id', $teacher_id);
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}
		
	}

	public function getCreateAttendance() 
	{
		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot($this->module_name, 'can_create');
		/*$classes = Classes::where('is_active','yes')->get();

		echo '<pre>';
			print_r($classes);
			die();*/

		$class_options = array();
		$msg = '';

		$id = Auth::admin()->id();

		$classes = DB::table('classess')
						->join('teachers','teachers.class_id','=','classess.id')
						->select('class_name','classess.id','classess.class_code')
						->where('teachers.teacher_id',$id)
						->where('is_class_teacher','yes')
						->get();
						
		foreach($classes as $class)
		{
			$class_options[$class->id] = $class->class_code;
		}

		if(!$class_options)
		{

			$msg = "Sorry you are not assigned as a class teacher in any of the classes";
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

		
		return View::make('teacher.attendance.views.select-class-section')
				->with('class_options',$class_options)
				->with('date', $date)
				->with('msg', $msg);
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}
		
				
	}


	public function getTeacherUpdateMarks() 
	{			

		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot('exam-marks', 'can_create,can_edit');
			$student_marks = '';
			$last_updated_data = ExamMarks::getLastUpdated($condition = array(/*array('field_name' => 'is_active', 'operator' => '=', 'compare_value' => 'yes')*/), $fields_required = array('updated_by', 'id'), 'ExamMarks');

			

			$class_id = Input::get('class_id', 0);
			$section_id = Input::get('section_id', 0);
			$subject_id = Input::get('subject_id', 0);
			$exam_id = Input::get('exam_id', 0);
			$session_id = Input::get('session_id', HelperController::getCurrentSession());
			

			$response = ExamMarksHelperController::getExamMarksList($class_id, $section_id, $subject_id, $exam_id, $session_id);

			$teacher_id = Auth::admin()->user()->id;

			$response = json_decode($response);
			return View::make('teacher.exam-marks.update')
							->with('student_marks', $response->student_marks)
							->with('last_updated_data', $last_updated_data)
							->with('full_marks_pass_marks', $response->full_marks_pass_marks)
							->with('teacher_id', $teacher_id);	
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}

		
	}

	public function postTeacherUpdateMarks() {

		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot('exam-marks', 'can_create,can_edit');
		
			$response = ExamMarksHelperController::apiPostUpdateMarks(Input::all());

			$response = json_decode($response, true);

			Session::flash($response['status'].'-msg', $response['message']);

			$class_id = Input::get('default_class');
			$section_id = Input::get('default_section');
			$subject_id = Input::get('default_subject');
			
		return Redirect::back();

		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}


		

	}

	public function getTeacherSubTopicsListView()
	{	
		if(Auth::admin()->check())
		{
		AccessController::allowedOrNot('teacher', 'can_create');
		return View::make('teacher.cas.list');
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}
	}

	public function getTeacherCasSubTopicCreateEditView($subject_id)
	{
		if(Auth::admin()->check())
		{

		AccessController::allowedOrNot('exam-marks', 'can_create,can_edit');
		$sub_topics_table = CasSubTopics::getTableName();
		$data = DB::table($sub_topics_table)
							->where('subject_id', $subject_id)
							->get();

		return View::make('teacher.cas.sub-topics-create-edit')
					->with('data', $data)
					->with('subject_id', $subject_id);
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}

	}

	public function getTeacherCasAssignSubTopics($subject_id) 
	{
		if(Auth::admin()->check())
		{
			AccessController::allowedOrNot('exam-marks', 'can_assign');	
			$current_sub_topic_id = Input::get('sub_topic_id', 0);
			$current_exam_id = Input::get('current_exam_id', 0);
			$sub_topic_list = CasSubTopics::where('subject_id', $subject_id)
										->lists('topic_name', 'id');

			$data = ['current_sub_topic_id' => $current_sub_topic_id, 'current_exam_id' => $current_exam_id, 'sub_topic_list' => $sub_topic_list];

			return View::make('teacher.cas.assign')
						->with('data', $data)
						->with('subject_id', $subject_id);
		}
		else
		{
			App::abort(403, 'You are not allowed to view this page');
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////// These are ajax functions /////////////////////////////////////////////////////////////////////////////////////////////////
	public function ajaxGetACtiveClasses()
	{

		$session_id = Input::get('session_id');
		$condition = array();
		$condition['field'] = 'academic_session_id';
		$condition['operator'] = '=';
		$condition['value'] = $session_id;

		return HelperController::generateSelectList('Classes', 'class_name', 'id', 'class_id', '', array($condition));
	}

	public function ajaxGetACtiveSections()
	{
		$class_id = Input::get('class_id');
		$condition = array();
		$condition['field'] = 'class_id';
		$condition['operator'] = '=';
		$condition['value'] = $class_id;

		//$select_list_value = Input::get('get_section_id', "false") == "false" ? 'section_code' : 'id';

		return HelperController::generateSelectList('ClassSection', 'section_code', 'section_code', 'section_code', '', array($condition));
	}

	public function getAjaxTeacherClasses()
	{
		if(!Input::has('academic_session_id')) return;

		$id = Auth::admin()->id();
			
		$classes = DB::table('classess')
						->join('teachers','teachers.class_id','=','classess.id')
						->select('classess.class_name','classess.id','classess.class_code','classess.academic_session_id','classess.is_active')
						->where('teachers.teacher_id',$id)
						->where('is_class_teacher','yes')	
						->where('academic_session_id', Input::get('academic_session_id'))
						->where('teachers.session_id', Input::get('academic_session_id'))
						->where('teachers.is_active','yes')
						->get();
			
		return json_encode($classes);

	}

	public function getAjaxTeacherClassSection() 
	{
		if(!Input::has('class_id')) return;

		$session_id = DB::table('academic_session')->where('is_active', 'yes')->where('is_current', 'yes')->pluck('id');

		

		$class_id = Input::get('class_id');

		$id = Auth::admin()->id();

		$section = DB::table('sections')
							->join('teachers', 'teachers.section_code','=','sections.section_code')
							->where('teachers.class_id', $class_id)
							->where('teachers.teacher_id', $id)
							->where('teachers.is_active', 'yes')
							->where('teachers.is_class_teacher','yes')
							->where('sections.is_active', 'yes')
							->where('teachers.session_id', $session_id)
							->select('teachers.section_code','sections.id')
							->get();

			// new way
		/*$class_section_table = ClassSection::getTableName();
		$section_table = Section::getTableName();

		$section = DB::table($section_table)
						->join($class_section_table, $class_section_table.'.section_code', '=', $section_table.'.section_code')
						->where($section_table.'.is_active', 'yes')
						->where($class_section_table.'.is_active', 'yes')
						->where($class_section_table.'.class_id', $class_id)
						->select($section_table.'.id', $section_table.'.section_code')
						->get();*/

		return json_encode($section);


	}

	public function getAjaxTeacherClassesShow() {


		$teacher_id = Input::get('teacher_id');
		$academic_session = Input::get('academic-session');


		$classes = DB::table('teachers')
					->join('classess','classess.id','=','teachers.class_id')
					->where('teachers.teacher_id',$teacher_id)
					->where('teachers.session_id',$academic_session)
					->where('classess.academic_session_id', $academic_session)
					->where('teachers.is_active','yes')
					->where('classess.is_active', 'yes')
					->get();

		return View::make($this->view. 'class-view')
						->with('classes', $classes);

	}

		public function getAjaxViewHistory() {

		if(!Input::has('class_id')) return;
		
		$class_id = Input::get('class_id');

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
			
		return View::make('teacher.attendance.views.js-template.class-section-history')
				->with('start_date', Carbon::createFromFormat('Y-m-d', $start_date))
				->with('end_date', $end_date)
				->with('data', $attendance_records)
				->with('date_range', $range);
	}

	public function getAjaxTeacherClassesFromExam() {

		$exam_id  	= Input::get('exam_id', 0);
		$teacher_id = Input::get('teacher_id');
		$session_id = DB::table('academic_session')->where('is_active', 'yes')->where('is_current', 'yes')->pluck('id');

		$data = DB::table(Classes::getTableName())
					->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.session_id','=',Classes::getTableName().'.academic_session_id')
					->join(Teacher::getTableName(),Teacher::getTableName().'.class_id','=',Classes::getTableName().'.id')
					->select('class_name', Classes::getTableName().'.id')
					->where(ExamConfiguration::getTableName().'.id', $exam_id)
					->where(Teacher::getTableName().'.teacher_id', $teacher_id)
					->where(ExamConfiguration::getTableName().'.session_id',$session_id)
					->where(Classes::getTableName().'.academic_session_id', $session_id)
					->where(Teacher::getTableName().'.session_id', $session_id)
					->where(ExamConfiguration::getTableName().'.is_active','yes')
					->where(Teacher::getTableName().'.is_active', 'yes')
					->where(Classes::getTableName().'.is_active', 'yes')
					->lists('class_name', 'id');

		/*$data = DB::table(ExamConfiguration::getTableName())
					->join(Classes::getTableName(), Classes::getTableName().'.academic_session_id', '=', ExamConfiguration::getTableName().'.session_id')

					->select('class_name', Classes::getTableName().'.id')
					->where(ExamConfiguration::getTableName().'.id', $exam_id)
					->lists('class_name', 'id');*/
		return HelperController::generateStaticSelectList($data, 'class_id');



	}


	public function getAjaxTeacherExamClassSectionList() {

		if(!Input::has('class_id')) return;
		
		$class_id = Input::get('class_id');

		$teacher_id = Auth::admin()->id();

		$condition = array();
		$condition['field'] = 'class_id';
		$condition['operator'] = '=';
		$condition['value'] = $class_id;

		$session_id = DB::table('academic_session')->where('is_active', 'yes')->where('is_current', 'yes')->pluck('id');

		$section = DB::table('teachers')
					->join('sections','sections.section_code','=','teachers.section_code')
					->select('teachers.section_code', 'sections.id')
					->where('teachers.class_id', $class_id)
					->where('teachers.teacher_id', $teacher_id)
					->where('teachers.is_active', 'yes')
					->where('sections.is_active','yes')
					->where('teachers.session_id', $session_id)
					->lists('section_code', 'id');
					
		return HelperController::generateStaticSelectList($section, 'section_id', $selected = 0);

	}

	public function getAjaxTeacherSubjects()
	{

		if(!Input::has('class_id')) return;
		if(!Input::has('section_id')) return;
		
		
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$section_code = Section::where('id', $section_id)->first()['section_code'];
		$session_id = DB::table('academic_session')->where('is_active','yes')->where('is_current', 'yes')->pluck('id');

		

		$teacher_id = Auth::admin()->user()->id;



		$subject_table = Subject::getTableName();
		$employee_table = Employee::getTableName();

		$subjects = DB::table('map_subject_teachers')
						->join('subjects', 'subjects.id','=','map_subject_teachers.subject_id')
						->select('subject_name', 'subject_code', 'subjects.id')
						->where('subjects.class_id', $class_id)
						->where('subjects.section_id', $section_id)
						->where('map_subject_teachers.teacher_id',$teacher_id)
						->where('map_subject_teachers.session_id',$session_id)
						->where('map_subject_teachers.is_active', 'yes')
						->where('subjects.is_active','yes')
						->get();

		
		return json_encode($subjects);
	}


	public function getAjaxTeacherClassListCas()
	{
		$teacher_id = Auth::admin()->user()->id;
		$session_id = Input::get('session_id');
		$default_class_id = Input::get('default_class_id');

		$class_ids = DB::table(Classes::getTableName())
					->join(Teacher::getTableName(),Teacher::getTableName().'.class_id','=',Classes::getTableName().'.id')
					->select( Classes::getTableName().'.id', 'class_name')
					->where(Teacher::getTableName().'.teacher_id', $teacher_id)
					->where(Classes::getTableName().'.academic_session_id',$session_id)
					->where(Teacher::getTableName().'.session_id', $session_id)
					->where(Classes::getTableName().'.is_active','yes')
					->where(Teacher::getTableName().'.is_active','yes')
					->get();

		$html = '';
			foreach($class_ids as $c)
			{
				$sel = $c->id == $default_class_id ? 'selected' : '';
				$html .= '<option value = "'.$c->id.'" '.$sel.'>'.$c->class_name.'</option>';
			}

			return $html;
					

	}

	public function getAjaxTeacherSectionListCas()
	{
		$class_id = Input::get('class_id');
		$session_id = Input::get('session_id');
		$teacher_id = Auth::admin()->id();
		$default_section_id = Input::get('default_section_id');

		$section_ids = DB::table('teachers')
					->join('sections','sections.section_code','=','teachers.section_code')
					->select('sections.id','teachers.section_code')
					->where('teachers.class_id', $class_id)
					->where('teachers.teacher_id', $teacher_id)
					->where('teachers.is_active', 'yes')
					->where('teachers.session_id', $session_id)
					->where('sections.is_active','yes')
					->get();


			$html = '';
			foreach($section_ids as $c)
			{
				$sel = $c->id == $default_section_id ? 'selected' : '';
				$html .= '<option value = "'.$c->id.'" '.$sel.'>'.$c->section_code.'</option>';
			}

			return $html;

	}

	public function getAjaxCasTeacherSubjects()
	{

		if(!Input::has('class_id')) return;
		if(!Input::has('section_id')) return;
		
		
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$section_code = Section::where('id', $section_id)->first()['section_code'];
		$session_id = Input::get('session_id');
		
		

		$teacher_id = Auth::admin()->user()->id;



		$subject_table = Subject::getTableName();
		$employee_table = Employee::getTableName();

		$data = DB::table('map_subject_teachers')
						->join('subjects', 'subjects.id','=','map_subject_teachers.subject_id')
						->select('subject_name', 'subject_code', 'sort_order', 'is_graded', 'include_in_report_card', 'subjects.id')
						->where('subjects.class_id', $class_id)
						->where('subjects.section_id', $section_id)
						->where('map_subject_teachers.teacher_id',$teacher_id)
						->where('map_subject_teachers.session_id',$session_id)
						->where('subjects.is_active','yes')
						->where('subjects.is_graded','yes')
						->where('map_subject_teachers.is_active','yes')
						->orderBy('sort_order', 'ASC')
						->get();

		
		return View::make('teacher.cas.subject-list')
						->with('data', $data)
						->with('session_id', $session_id)
						->with('class_id', $class_id)
						->with('section_id', $section_id);
	}

//Ajax Function Ends////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
}
