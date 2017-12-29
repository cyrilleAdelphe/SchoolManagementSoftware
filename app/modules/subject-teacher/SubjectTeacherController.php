<?php

class SubjectTeacherController extends BaseController
{
	protected $view = 'subject-teacher.views.';

	protected $model_name = 'SubjectTeacher';

	protected $module_name = 'subject-teacher';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'subject_name',
										'alias'			=> 'Subject Name'
									),
									array
									(
										'column_name' 	=> 'teacher_name',
										'alias'			=> 'Teacher Name'
									)
								 );

	
	public function getCreateView()
	{
		AccessController::allowedOrNot('subject-teacher', 'can_create');
		//get all required subjects according to session, subject id, class id
		//get all teachers according to session, subject_id, class_id

		$session_id = Input::has('session_id') ?
		      		Input::get('session_id') : AcademicSession::where('is_current','yes')->first()['id'];
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);

		$section_code = Section::where('id', $section_id)->pluck('section_code');

		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					//->with('teachers', $teachers)
					//->with('subjects', $subjects)
					->with('section_id', $section_id)
					->with('section_code', $section_code)
					->with('class_id', $class_id)
					->with('session_id', $session_id)
					->with('actionButtons', $this->getActionButtons());
	}

	// gets teacher that teach particular class and sections

	public function getListView()
	{
		AccessController::allowedOrNot('subject-teacher', 'can_view');
		$subjects = '';
		if(Input::has('class_id') && Input::has('section_id'))
		{
			$class_id = Input::get('class_id');
			$section_id = Input::get('section_id');
			$subject_table = Subject::getTableName();
			$employee_table = Employee::getTableName();

			$subjects = DB::table($subject_table)
							->join($employee_table, $employee_table.'.id', '=', $subject_table.'.teacher_id')
							->select('subject_name','subject_code','class_id','section_id','full_marks','pass_marks','remarks',
									'teacher_id', 
									'employee_name as teacher_name', 
									$subject_table.'.id as id',
									$subject_table.'.is_active as is_active')
							->where('class_id', $class_id)
							->where('section_id', $section_id)
							->get();
		}

		return View::make($this->view . 'list')
					->with('role', $this->role)
					->with('subjects', $subjects)
					->with('module_name', $this->module_name);
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot('subject-teacher', 'can_create,can_edit');
		$success = false;
		$msg = '';
		
		$data = Input::all();
		$dataToStore = array();
		
		foreach($data as $index => $value)
		{
			if(strpos($index, 'subject_teacher'))
			{
				$subject_id = (int) substr($index, strlen('_subject_teacher') + 1);
				foreach($value as $v)
				{
					$dataToStore[] = [
						'subject_id'	=> $subject_id,
						'teacher_id'	=> $v,
						'session_id'	=> $data['session_id'],
						'section_id'	=> $data['section_id'],
						'class_id'		=> $data['class_id'],
						'is_active'		=> 'yes'
					];
				}
			}
		}
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();
				SubjectTeacher::where('session_id', $data['session_id'])
								->where('section_id', $data['section_id'])
								->where('class_id', $data['class_id'])
								->delete();

				foreach($dataToStore as $d)
				{
					$this->storeInDatabase($d);	
				}


				$success = true;
				$msg = 'Record successfully created';
			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		Session::flash($success ? 'success-msg' : 'error-msg', $msg);

		return Redirect::to(URL::route('subject-teacher-create-get'));
		
		
	}

	public function deleteSubject($id)
	{
		AccessController::allowedOrNot('subject-teacher', 'can_delete');
		Session::flash('success-msg', $id . ' deleted. LOL!') ;
		return \Redirect::back();
	}
}