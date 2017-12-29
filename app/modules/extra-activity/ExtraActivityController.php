<?php

class ExtraActivityController extends BaseController
{
	protected $view = 'extra-activity.views.';

	protected $model_name = 'ExtraActivity';

	protected $module_name = 'extra-activity';

	public $role;

	public $current_user;

	public $columnsToShow = array(
									array(
										'column_name' 	=> 'title',
										'alias'			=> 'Event Title'
									),
									
									array(
										'column_name' 	=> 'student_name',
										'alias'			=> 'Student Name(s)'
									),

									array(
										'column_name' 	=> 'from',
										'alias'			=> 'From'
									),

									array(
										'column_name' 	=> 'to',
										'alias'			=> 'To'
									),
								);

	public function validateStudentUsernames($data)
	{
		if(!isset($data['student_username']))
		{
			return array(	'status'	=> 'error', 
										'msg'			=> 'No student selected');
		}

		$invalid_student_usernames = array();
		foreach($data['student_username'] as $key => $student_username)
		{
			if ( !Users::where('username', $student_username)->where('role', 'student')->first() )
			{
				$invalid_student_usernames[] = $student_username;
			}
			else
			{
				$data['student_username'][$key] = $student_username;
			}
		}

		if(count($invalid_student_usernames))
		{
			return array(	'status'	=> 'error', 
										'msg'			=> 'Invalid student username(s): '. str_replace(array('"','[',']'),'',json_encode($invalid_student_usernames)));
		}

		$result['status'] = 'success';
		$result['data'] = [];
		foreach($data['student_username'] as $student_username)
		{
			$result['data'][] = Users::where('username', $student_username)
														->where('role', 'student')
														->first()
														->user_details_id;
													
	
		}
		$result['data'] = array_unique(
														$result['data']
													);
		return $result;
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot('extra-activity', 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateStudentUsernames($data);
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate($result['msg']));
			return Redirect::back()
						->withInput();
		}
		else
		{
			$data['student_ids'] = $result['data'];
		}

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
			DB::connection()->getPdo()->beginTransaction();

			$event_id = Events::where('event_code', $data['event_code'])
												->first()
												->id;
			ExtraActivity::where('event_id', $event_id)
										->delete();
			// in case of edit operation, event_code and old_event_id may point to different event
			if (Input::has('old_event_id'))
			{
				ExtraActivity::where('event_id', Input::get('old_event_id'))
										->delete();
			}
			$dataToStore = $data;

			foreach($data['student_ids'] as $key => $student_id)
			{
				$dataToStore['student_id'] = $student_id;
				$dataToStore['event_id'] = $event_id;
				$dataToStore['remarks'] = $data['remarks'][$key];
				$id = $this->storeInDatabase($dataToStore);
			}

			DB::connection()->getPdo()->commit();

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function postEditView($event_id)
	{
		AccessController::allowedOrNot('extra-activity', 'can_edit');
		return $this->postCreateView();
	}

	public function postDelete()
	{
		AccessController::allowedOrNot('extra-activity', 'can_delete');
		$data = ExtraActivity::find(Input::get('id',0));
		if(!$data)
		{
			Session::flash('error-msg', 'Invalid delete operation');
			return Redirect::back();
		}

		ExtraActivity::where('event_id', $data->event_id)
									->delete();

		Session::flash('sucess-msg', 'Record deleted');
		return Redirect::back();
	}

	public function sendPushNotification($event_id)
	{
		AccessController::allowedOrNot('extra-activity', 'can_send_push_notification');
		$students = DB::table(ExtraActivity::getTableName())
									->join(Events::getTableName(), Events::getTableName().'.id', '=', ExtraActivity::getTableName().'.event_id')
									->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', ExtraActivity::getTableName().'.student_id')
									->where('event_id', $event_id)
									->select('student_id', 'student_name', 'remarks', 'title')
									->get();

		$student_table = Student::getTableName();
		$student_guardian_relation_table = StudentGuardianRelation::getTableName();
		$guardian_table = Guardian::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$gcm_table =  PushNotifications::getTableName();

		foreach($students as $student)
		{
			$student_gcm_id = DB::table(PushNotifications::getTableName())
								->where('user_group', 'student')
								->where('user_id', $student->student_id)
								->lists('gcm_id');

			$msg = $this->module_name . ' # '.
					$student->remarks . ': ' . $student->student_name . ' in event ' . $student->title;


			$parent_ids = DB::table($student_guardian_relation_table)
							->where('student_id', $student->student_id)
							->lists('guardian_id');

			$parent_gcm_ids = DB::table(PushNotifications::getTableName())
							->whereIn('user_id', $parent_ids)
							->where('user_group', 'guardian')
							->lists('gcm_id');

			if ($student->student_id)
				(new GcmController)->send($student_gcm_id, $msg, $student->student_id, 'student');

			if (count($parent_ids))
				(new GcmController)->send($parent_gcm_ids, $msg, $parent_ids, 'guardian');
		}
		
		Session::flash('success-msg', 'Notification sent');
		return Redirect::route($this->module_name . '-list');	
	}
}