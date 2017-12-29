<?php

class DormitoryStudentController extends BaseController
{
	protected $view = 'dormitory-student.views.';

	protected $model_name = 'DormitoryStudent';

	protected $module_name = 'dormitory-student';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'student_id',
										'alias'			=> 'Student ID'
									),
									array
									(
										'column_name' 	=> 'dormitory_code',
										'alias'			=> 'Dormitory Code'
									),
									array
									(
										'column_name' 	=> 'academic_session_id',
										'alias'			=> 'Academic Session ID'
									),
								 );

	// @override
	protected function validateInput($data, $update = false, $modelName = '')
	{
		$result = parent::validateInput($data, $update, $modelName);



		$student = Student::where('student_id', $data['student_id'])
							->where('current_session_id', $data['academic_session_id'])
							->first();
		if(!$student)
		{
			if($result['status']=='success')
			{
				$result['status'] = 'error';
				$result['data'] = new Illuminate\Support\MessageBag;
			}
			$result['data']->add('student_id', 'Student Not registered for current session');
		}
		if($data['fee_amount']=='' && $student)
		{
			$class_id = $student->current_class_id;
			$section_id = Section::where('section_code', $student->current_section_code)
									->first()
									->id;
			
			$fee = HostelFee::where('class_id', $class_id)	
							->where('section_id', $section_id)
							->where('type', $data['type'])
							->first();
			if(!$fee)
			{
				if($result['status']=='success')
				{
					$result['status'] = 'error';
					$result['data'] = new Illuminate\Support\MessageBag;
				}
				$result['data']->add('fee_amount', 'default fee not provided in fee manager');
			}
			else
			{
				$data['fee_amount']	= $fee->amount;
			}
		}

		return $result;

	}
	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		// the visible student ID is username!!
		$data['student_id'] = HelperController::getStudentIdFromUsername($data['student_id']);

		$result = $this->validateInput($data);
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		
		try
		{

			$id = $this->storeInDatabase($data);	

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return Redirect::to(
					URL::route('dormitory-student-list')
					.'?academic_session_id='.Input::get('academic_session_id')
					.'&dormitory_id='.Input::get('dormitory_id')
				);
	}

	public function getEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$model = new $this->model_name;
		
		$data = $model->getEditViewData($id);	

		// the visible student ID is username!!
		$data->student_id = Users::where('user_details_id', $data->student_id)
															->where('role', 'student')
															->first()
															->username;	
		
		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('id', $id)
					->with('actionButtons', $this->getActionButtons());
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		// the visible student ID is username!!
		$data['student_id'] = HelperController::getStudentIdFromUsername($data['student_id']);

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
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}


	public function getListViewSimple()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		return View::make($this->view . 'select-session-dormitory')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('role', $this->role);
	}

	public function postListViewSimple()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		if(!Input::has('academic_session_id') && !Input::has('dormitory_id'))
		{
			return;
		}

		$model = new $this->model_name;
		$result = DB::table($model->getTableName())
						->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', 'student_id')
						->where('academic_session_id', Input::get('academic_session_id'))
						->where('dormitory_id', Input::get('dormitory_id'))
						->select($model->getTableName().'.*', StudentRegistration::getTableName().'.student_name')
						->get();

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		$data =  array('data' => $result, 'count' => $count, 'message' => $msg);

		return View::make($this->view.'list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('role', $this->role);

	}
	

}