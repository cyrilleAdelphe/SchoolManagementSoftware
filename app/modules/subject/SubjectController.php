<?php

class SubjectController extends BaseController
{
	protected $view = 'subject.views.';

	protected $model_name = 'Subject';

	protected $module_name = 'subject';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'subject_name',
										'alias'			=> 'Subject Name'
									),
									array
									(
										'column_name' 	=> 'subject_code',
										'alias'			=> 'Subject Code'
									)
								 );

	
	public function getCreateView()
	{
		AccessController::allowedOrNot('subject', 'can_create');
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('actionButtons', $this->getActionButtons());
	}

	// gets teacher that teach particular class and sections
	public function ajaxGetTeachers()
	{

		if(!Input::has('class_id')) return;
		if(!Input::has('section_id')) return;
		
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$section_code = Section::where('id', $section_id)->first()['section_code'];

		$teacher_table = Teacher::getTableName();
		$employee_table = Employee::getTableName();

		$teachers = DB::table($teacher_table)
						->join($employee_table, $employee_table.'.id', '=', $teacher_table.'.teacher_id')
						->select('teacher_id', 'employee_name as teacher_name')
						->where('class_id', $class_id)
						->where('section_code', $section_code)
						->get();

		return json_encode($teachers);
	}

	// gets subjects that are taught in particular class and sections
	public function ajaxGetSubjects()
	{

		if(!Input::has('class_id')) return;
		if(!Input::has('section_id')) return;
		
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$section_code = Section::where('id', $section_id)->first()['section_code'];

		$subject_table = Subject::getTableName();
		$employee_table = Employee::getTableName();

		//////// Exam-Marks-v1-changes-made-here //////
		$current_user = HelperController::getCurrentUser();

		$map_subject_teacher_table = SubjectTeacher::getTableName();
		$subjects = DB::table($map_subject_teacher_table)
						->join($subject_table, $subject_table.'.id', '=', $map_subject_teacher_table.'.subject_id')
						->where($subject_table.'.class_id', $class_id)
						->where($subject_table.'.section_id', $section_id)
						->where($map_subject_teacher_table.'.teacher_id', $current_user->user_id)
						->select('subject_name', 'subject_code', $subject_table.'.id')
						->get();

		if(count($subjects) == 0)
		{
			$subjects = DB::table($subject_table)
							->select('subject_name', 'subject_code', $subject_table.'.id')
							->where($subject_table.'.class_id', $class_id)
							->where($subject_table.'.section_id', $section_id)
							->orderBy('sort_order', 'ASC')
							->get();	
		}
		//////// Exam-Marks-v1-changes-made-here //////

		
		
		
		return json_encode($subjects);
	}

	public function getListView()
	{
		AccessController::allowedOrNot('subject', 'can_view');
		$subjects = '';
		if(Input::has('class_id') && Input::has('section_id'))
		{
			$class_id = Input::get('class_id');
			$section_id = Input::get('section_id');
			$subject_table = Subject::getTableName();
			//$employee_table = Employee::getTableName();

			$subjects = DB::table($subject_table)
							//->leftJoin($employee_table, $employee_table.'.id', '=', $subject_table.'.teacher_id')
							->select(
								'subject_name',
								'sort_order', 
								'subject_code',
								'class_id',
								'section_id',
								'full_marks',
								'pass_marks',
								'remarks',
								'is_graded',
								'include_in_report_card',
								$subject_table.'.id as id',
								$subject_table.'.is_active as is_active'
							)
							->where('class_id', $class_id)
							->where('section_id', $section_id)
							->orderBy('sort_order', 'ASC')
							->get();
		}

		return View::make($this->view . 'list')
					->with('role', $this->role)
					->with('subjects', $subjects)
					->with('module_name', $this->module_name);
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot('subject', 'can_edit');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data, true);

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-edit-get', array($id))
						->withInput()
						->with('errors', $result['data']);
		}
		
		try
		{
			$id = $this->updateInDatabase($data);	

			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id; 
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
			$param['id'] = $data['id'];
		}
		Session::flash($success ? 'success-msg' : 'error-msg', $msg);

		return Redirect::to(URL::route('subject-list').'?class_id='.Input::get('class_id').'&section_id='.Input::get('section_id'));
		
		
	}

	public function deleteSubject($id)
	{
		//die('here');
		AccessController::allowedOrNot('subject', 'can_delete');
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$model = new Subject;
		//$id = Input::get('id');
		$record = $model->find($id);
		
		if($record)
		{
			try
			{
				$record->delete();
				Session::flash('success-msg', 'Delete Successful');	
			}
			catch(Exception $e)
			{
				Session::flash('error-msg', ConfigurationController::errorMsg($e->getMessage()));
			}
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}
}