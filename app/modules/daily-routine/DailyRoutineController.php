<?php

class DailyRoutineController extends BaseController
{
	protected $view = 'daily-routine.views.';

	protected $model_name = 'DailyRoutine';

	protected $module_name = 'daily-routine';

	protected $role;

	public $current_user;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'period',
										'alias'			=> 'Period'
									),
									array
									(
										'column_name' 	=> 'subject',
										'alias'			=> 'subject'
									),
									array
									(
										'column_name' 	=> 'teacher',
										'alias'			=> 'Teacher'
									),
									array
									(
										'column_name' 	=> 'start_time',
										'alias'			=> 'Start'
									),
									array
									(
										'column_name' 	=> 'end_time',
										'alias'			=> 'End'
									)
								 );

	public function dailyRoutineList()
	{
		AccessController::allowedOrNot('daily-routine', 'can_view');
		$data = (new DailyRoutine)->getListViewData();
		return View::make($this->view.'daily-routine')
					->with('role', $this->role)
					->with('data', $data);
	}

	/* this is not needed */
	public function getCreateDailyRoutine()
	{
		AccessController::allowedOrNot('daily-routine', 'can_create');
		return View::make($this->view.'create-daily-routine')
					->with('role', $this->role);
	}

	public function postCreateDailyRoutine()
	{
		AccessController::allowedOrNot('daily-routine', 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();
		$data['start_time'] = DateTime::createFromFormat('g:i A', $data['start_time'])->format('H:i:s');
		$data['end_time'] = DateTime::createFromFormat('g:i A', $data['end_time'])->format('H:i:s');
		
		$result = $this->validateInput($data);
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		try
		{
			$id = $this->storeInDatabase($data);	

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
			Session::flash('success-msg', $msg);
			return Redirect::route('daily-routine-list');
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
			Session::flash('error-msg', $msg);
			return Redirect::back()->withInput();
		}
		
		
		
	}

	public function getEditDailyRoutine()
	{
		AccessController::allowedOrNot('daily-routine', 'can_edit');
		$data = (new DailyRoutine)->getEditView();
		
		$teachers = Teacher::where('session_id', Input::get('session_id', 0))
			->where('class_id', Input::get('class_id', 0))
			->join(Section::getTableName(), Section::getTableName().'.section_code', '=', Teacher::getTableName().'.section_code')
			->where(Section::getTableName().'.id', Input::get('section_id', 0))
			->join(Employee::getTableName(), Employee::getTableName().'.id', '=', Teacher::getTableName().'.teacher_id')
			->groupBy(Employee::getTableName().'.id')
			->orderBy(Employee::getTableName() . '.employee_name')
			->lists('employee_name');

		$subject_teachers = Subject::leftJoin(
			SubjectTeacher::getTableName(),
			SubjectTeacher::getTableName() . '.subject_id', '=',
			Subject::getTableName() . '.id'
		)->leftJoin(
			Employee::getTableName(),
			Employee::getTableName() . '.id', '=',
			SubjectTeacher::getTableName() . '.teacher_id'
		)->where(
			Subject::getTableName() . '.class_id', 
			Input::get('class_id', 0)
		)->where(
			Subject::getTableName() . '.section_id', 
			Input::get('section_id', 0)
		)->orderBy(
			Subject::getTableName() . '.subject_name'
		)->groupBy(
			Subject::getTableName() . '.subject_name'
		)->select(
			Subject::getTableName() . '.subject_name',
			DB::raw("GROUP_CONCAT(employee_name SEPARATOR ',') as employee_names")
		)->get();

		$subjects = array_map(
			function($i) {
				return $i['subject_name'];
			}, 
			$subject_teachers->toArray()
		);

		$subject_teacher_map = array();
		foreach($subject_teachers as $subject_teacher)
		{
			$teachers_list = explode(',', $subject_teacher->employee_names);
			// filter empty strings from teachers_list
			$teachers_list = array_filter($teachers_list);
			sort($teachers_list);
			$subject_teacher_map[$subject_teacher->subject_name] = $teachers_list;
		}

		$class_data = Classes::find(Input::get('class_id', 0));
		$section_data = Section::find(Input::get('section_id', 0));

		$class_name = $class_data ? $class_data->class_name : '';
		$section_code = $section_data ? $section_data->section_code : '';

		return View::make($this->view.'edit-daily-routine')
					->with('role', $this->role)
					->with('data', $data)
					->with('module_name', $this->module_name)
					->with('subjects', $subjects)
					->with('teachers', $teachers)
					->with('subject_teacher_map', $subject_teacher_map)
					->with('class_name', $class_name)
					->with('section_code', $section_code);
	}

	public function postEditDailyRoutine()
	{
		AccessController::allowedOrNot('daily-routine', 'can_edit');
		$success = false;
		$msg = '';
		//$param = array('id' => 0);

		$data = Input::all();
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			DailyRoutine::where('session_id', $data['session_id'])
				->where('class_id', $data['class_id'])
				->where('section_id', $data['section_id'])
				->where('day', $data['day'])
				->delete();
			foreach($data['start_time'] as $index => $d)
			{
				//die('here');
				$data_to_store = array();
				$data_to_store['teacher'] = $data['teacher'][$index];
				$data_to_store['subject'] = $data['subject'][$index];
				
				$data_to_store['start_time'] = DateTime::createFromFormat('g:i A', $data['start_time'][$index])->format('H:i:s');
				$data_to_store['end_time'] = DateTime::createFromFormat('g:i A', $data['end_time'][$index])->format('H:i:s');

				$data_to_store['session_id'] = $data['session_id'];
				$data_to_store['class_id'] = $data['class_id'];
				$data_to_store['section_id'] = $data['section_id'];
				$data_to_store['day'] = $data['day'];
				$data_to_store['is_active'] = 'yes';

				$result = $this->validateInput($data_to_store);

				if($result['status'] == 'error')
				{
					Session::flash('error-msg', 'validation error');
					return Redirect::back()
									->withInput()
									->with('errors', $result['data']);
				}

				$this->storeInDatabase($data_to_store);	

			}

			DB::connection()->getPdo()->commit();
			Session::flash('success-msg', 'Routine successfully updated');
			$redirect_url = URL::route('daily-routine-list').'?session_id='.$data['session_id'].'&class_id='.$data['class_id'].'&section_id='.$data['section_id'].'&day='.$data['day'];
			return Redirect::to($redirect_url);
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$msg = $e->getMessage();
			Session::flash('error-msg', $msg);
			return Redirect::back()->withInput();
		}
		
	}

	public function postDeleteDay()
	{
	AccessController::allowedOrNot($this->module_name, 'can_delete');
		$session_id = Input::get('session_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$day = Input::get('day', '');	

		DailyRoutine::where('class_id', $class_id)
							->where('section_id', $section_id)
							->where('session_id', $session_id)
							->where('day', $day)
							->delete();

		Session::flash('success-msg', 'Routine deleted');
		return Redirect::back();
	}

	
}