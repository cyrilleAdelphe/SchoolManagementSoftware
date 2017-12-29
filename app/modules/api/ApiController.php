<?php
//getListViewFeePrintListData($session_id, $class_id, $section_id, $selected_month, $student_id = 0)
use Carbon\Carbon;
define('DAYS_TO_KEEP_NOTIFICATIONS', 5);

class ApiController extends Controller
{
	public $response = array('status' => 'error', 'data' => array(), 'message' => '');
	
	
	public function getNewAttendanceStudentHistory()
	{
	
	$input = Input::all();


		$input['date_range'] = explode(' - ', $input['date_range']);
		foreach($input['date_range'] as $index => $d)
		{
			$input['date_range'][$index] = Carbon::createFromFormat('m/d/Y', $d);
		}

		$data = [];
		for($i = $input['date_range'][0]; $i->lte($input['date_range'][1]); $i->addDay())
		{
			$temp = $i;
			$temp = $temp->format('Y-m-d');
			
			$record = AttendanceHelperController::getAttendanceRecords($temp, $input['class_id'], $input['section_code']);

			if(isset($record[$input['student_id']]))
			{
				$data[] = ['date' => $temp, 'status' => $record[$input['student_id']]['attendance_status'], 'comment' => $record[$input['student_id']]['attendance_comment']];
			}
			
		}

		$student = StudentRegistration::where('id', $input['student_id'])
										->first();
		
		return View::make('attendance.views.mobile-student-history')
				->with('data', $data)
				->with('student', $student)
				->with('class_id', $input['class_id'])
				->with('section_code', $input['section_code']);
	
	}
	
	public function getExamProgressReport()
	{

		$exam_id = Input::get('exam_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$student_id = Input::get('student_id', NULL);
		$is_final = Input::get('is_final', 'no');

		$exam_name = '';
		try
		{
			$exam_name = ExamConfiguration::find($exam_id)->exam_name;
		}
		catch (Exception $e)
		{
			// default exam_name has already been assigned as empty string
		}

		$tablename = $is_final == 'no' ? Report::getTableName() : FinalReport::getTableName();
		$view_name = $is_final == 'no' ? 'mass-print' : 'mass-print-final';
		
		if(is_null($student_id))
		{
			$data = DB::table($tablename)
						->where('exam_id', $exam_id)
						->where('class_id', $class_id)
						->where('section_id', $section_id)
						->get();
		}
		else
		{
			$data = DB::table($tablename)
						->where('student_id', $student_id)
						->where('exam_id', $exam_id)
						->where('class_id', $class_id)
						->where('section_id', $section_id)
						->get();

			//check if fee cleared or not
			$remaining_balance = BillingTransaction::where('related_user_group', 'student')
													->where('related_user_id', $student_id)
													->plunk('balance_amount');

			if($remaining_balance > 0)
			{
				die('Fee not cleared');
			}
		}

		$class_name = HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0));
		$section_code = HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0));


	return View::make('report.views.' . $view_name)
							->with('data', $data)
							->with('class_name', $class_name)
							->with('section_code', $section_code)
							->with('exam_name', $exam_name);


	}
	
	public function getTeacherClassSectionforExam()
	
	{	
		$teacher_id = Input::get('teacher_id');

		
		if(!$teacher_id)
		{
			return['status' => 'error', 'msg' => 'No teacher ID found', 'data'=> []];
			
		}
		$session_id = DB::table('academic_session')->where('is_active', 'yes')->where('is_current', 'yes')->pluck('id');
		
		
		$class_section_details = DB::table('teachers')
			->join('classess','classess.id','=','teachers.class_id')
			->join('sections','sections.section_code','=','teachers.section_code')
			->where('teachers.teacher_id', $teacher_id)
			->where('academic_session_id', $session_id)
			->where('teachers.is_active', 'yes')
			->where('sections.is_active', 'yes')
			->where('teachers.session_id', $session_id)
			->select('classess.class_name','classess.id as class_id','classess.class_code','sections.id as section_id','teachers.section_code')
			->get();
		
		if(!$class_section_details )
			{
				return['status'=> 'error', 'msg'=> 'You are not assigned as a teacher in any of classes',  'data'=> []];
			}		
						
			
		
						
		return['status' => 'success', 'msg' => 'Teacher Class Section from teacher Id', 'data'=>$class_section_details ];
	
		
	}
	public function getExamDetails()
	{	
		$table6 = ExamConfiguration::getTableName();
		$current_session = HelperController::getCurrentSession();
		
		if(!$current_session)
		{
			return['status'=> 'error','msg'=> 'No current session','data'=> []];
		}
		
		$exam_details = DB::table($table6)->select('id','exam_name')->where($table6.'.session_id', $current_session)->get();
		
		if(!$exam_details)
		{
			return['status'=> 'error','msg'=> 'No Exam found','data'=> []];
		}
		
		return['status'=>'success', 'msg'=> 'Exam Details', 'data'=> $exam_details ];
		
	}

	
	
		public function getTeacherAssignedClassSection()
	{
	
		$teacher_id = Input::get('teacher_id');

		
		if(!$teacher_id)
		{
			return['status' => 'error', 'msg' => 'No teacher ID found', 'data'=> []];
			
		}
		$session_id = DB::table('academic_session')->where('is_active', 'yes')->where('is_current', 'yes')->pluck('id');
		
		$class_teacher_section_details = DB::table('teachers')
			->join('classess','classess.id','=','teachers.class_id')
			->join('sections','sections.section_code','=','teachers.section_code')
			->where('teachers.teacher_id', $teacher_id)
			->where('teachers.is_class_teacher','yes')
			->where('academic_session_id', $session_id)
			->where('teachers.is_active', 'yes')
			->where('sections.is_active', 'yes')
			->where('teachers.session_id', $session_id)
			->select('classess.class_name','classess.id as class_id','classess.class_code','sections.id as section_id','teachers.section_code')
			->get();
		
		if(!$class_teacher_section_details )
			{
				return['status'=> 'error', 'msg'=> 'You are not assigned as class teacher in any of classes',  'data'=> []];
			}		
						
		return['status' => 'success', 'msg' => 'Teacher Class Section from teacher Id', 'data'=>$class_teacher_section_details ];
	

	}

	
	public function apiGetListViewFeePrintStudent()
	{


		$selected_month = Input::get('nepDate'); 
		$class_id = Input::get('class_id');

		$section_id = Input::get('section_id');
		$session_id = Input::get('academic_session_id'); //shows id of 2073 = 4
		$student_id = Input::get('student_id');
		
		
		if(!$class_id)
		{
			return['status' => 'error', 'msg' => 'No class Id found', 'data' => []];
		}

		if(!$section_id)
		{
			return['status' => 'error', 'msg' => 'No Section Id found', 'data' => []];
		}

		if(!$session_id)
		{
			return['status' => 'error', 'msg' => 'No Session Id found', 'data' => []];
		}

		if(!$student_id)
		{
			return['status' => 'error', 'msg' => 'No Student Id found', 'data' => []];
		}

		if(!$selected_month)
		{
			return['status' => 'error', 'msg' => 'No Student Id found', 'data' => []];
		}

		$class = Classes::where('id', $class_id)->first()->class_name;
		$section = Section::where('id', $section_id)->first()->section_code;
		
		$parts = explode('-',$selected_month);
		$year = $parts[0];
		$month = $parts[1];



		//$return_data = $this->apigetListViewFeePrintListDataStudent($session_id, $class_id, $section_id, $selected_month, $student_id);	


		$return_data = (new BillingController)->getListViewFeePrintListData($session_id, $class_id, $section_id, $selected_month, $student_id = 0);
			

		return['status' => 'success', 'msg' => 'Fee Print of Student', 'data' => $return_data];

	}
	
	public function apigetListViewFeePrintListDataStudent($session_id, $class_id, $section_id, $selected_month, $student_id)
	{
		$parts = explode('-',$selected_month);
		$year = $parts[0];
		$month = $parts[1];
		$section_code = DB::table('sections')
							->select('id', 'section_code')
							->where('id', '=', $section_id)
							->first();

		$student_details = [];

		//$student_details

		$student_guardian_relation_table = StudentGuardianRelation::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$users_table = Users::getTableName();
		$guardian_table = Guardian::getTableName();
		$billing_invoice_table = BillingInvoice::getTableName();
		$student_table = Student::getTableName();


		$temp_student_details = DB::table($student_registration_table)
							->join($users_table, $users_table.'.user_details_id', '=', $student_registration_table.'.id')
							->join($student_table, $student_table.'.student_id', '=', $student_registration_table.'.id')
							->leftJoin($student_guardian_relation_table, $student_guardian_relation_table.'.student_id', '=', $student_registration_table.'.id')
							->leftJoin($guardian_table, $guardian_table.'.id', '=', $student_guardian_relation_table.'.guardian_id')
							->select($users_table.'.username',$guardian_table.'.guardian_name', $student_registration_table.'.current_address', $student_registration_table.'.student_name', $student_registration_table.'.last_name', $student_registration_table.'.id', 'current_roll_number')
							->where('role', 'student')
							->where('current_session_id', '=', $session_id)
							->where('current_class_id', '=',  $class_id)
							->where('current_section_code', '=',  $section_code->section_code)
							->where($student_table.'.student_id', '=',$student_id)
							->get();



		$student_details = [];
		foreach($temp_student_details as $index => $t)
		{
			if(!isset($student_details[$t->id]))
			{
				$student_details[$t->id] = $t;
			}	
			else
			{
				$student_details[$t->id]->guardian_name .= ','.$t->guardian_name;
			}
		}
		
		$student_ids = array_keys($student_details);

		unset($temp_student_details);
		
		
		$temp_current_month_bills = DB::table($billing_invoice_table)
							  ->where($billing_invoice_table.'.is_cleared', '<>','yes')
							->where($billing_invoice_table.'.related_user_group', 'student')
							->whereIn($billing_invoice_table.'.related_user_id',$student_ids)
							->where($billing_invoice_table.'.year_in_bs', $year)
							->where($billing_invoice_table.'.month_in_bs', '=', $month)
							//->where('is_direct_invoice', 'yes')
							//->where('is_opening_balance', 'no')
							->orderBy($billing_invoice_table.'.id', 'DESC')
							->get();

		$current_month_bills = [];

		foreach($temp_current_month_bills as $t)
		{
			$current_month_bills[$t->related_user_id][] = $t;

		}

		$temp_previous_month_bills = DB::table($billing_invoice_table)
									    ->where($billing_invoice_table.'.is_cleared', '<>','yes')
									    ->whereIn('related_user_id', $student_ids)
									    ->where($billing_invoice_table.'.related_user_group', 'student')
										->where(function($query) use ($month, $year, $billing_invoice_table)
										{
											$query->where(function($query) use ($month, $year, $billing_invoice_table)
											   {
												$query->where($billing_invoice_table.'.month_in_bs', '<', $month )
												->where($billing_invoice_table.'.year_in_bs','=', $year);
											   })
												->orWhere($billing_invoice_table.'.year_in_bs', '<', $year);
										})
										->orderBy('id', 'DESC')
										->get();

		$previous_month_bills = [];
		foreach($temp_previous_month_bills as $t)
		{
			$previous_month_bills[$t->related_user_id][] = $t;
		}

		$return_data = [];
		foreach($student_details as $student_id => $student_detail)
		{
			if((isset($current_month_bills[$student_id]) && count($current_month_bills[$student_id])) || (isset($previous_month_bills[$student_id]) && count($previous_month_bills[$student_id])))
			{
				$temp = [];
				$temp['student_detail'] = $student_detail;
				if(isset($current_month_bills[$student_id]))
				{
					$temp['current_month'] = $current_month_bills[$student_id];
				}
				else
				{
					$temp['current_month'] = [];	
				}

				if(isset($previous_month_bills[$student_id]))
				{
					$temp['previous_month'] = $previous_month_bills[$student_id];
				}
				else
				{
					$temp['previous_month'] = [];	
				}
				$return_data[] = $temp;	
			}
			
		}

		return $return_data;
	}
	
	
	
	
	public function getStudentStatement()
	{
		
		
		$date_range = Input::get('date_range');
		$student_id = Input::get('student_id');
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$session_id = Input::get('session_id');

		$dates = BillingHelperController::getDateRange($date_range);

			
		$data = (new BillingController)->apiGetStatementListData($date_range, $student_id);
		$data['condition']  = SsmConstants::$const_billing_types;


		if(!$data)
		{
			return ['status' => 'error', 'message' => 'Statement Report Not Found' ,'data' => ''];

		}
			
		return ['status' => 'success', 'message' => 'Student Statement Report' ,'data' => $data];
		
		
	}

	public function getInvoiceFromStudentStatement()

	{
		$transaction_number = Input::get('transaction_number');
		$invoice_id = BillingTransaction::where('transaction_no', $transaction_number)
									->pluck('related_invoice_id');

		if($invoice_id)
		{
			$data['data'] = BillingInvoice::getInvoiceDetails($invoice_id);	
		}
		else
		{
			$data['data'] = [];
			return['status' => 'error', 'message' => 'Invoice Id not found', 'data' => ''];
		}

		return['status' => 'success', 'message' => 'Invoice details from transaction_number' , 'data' => $data];

	}
	
	
	public function getAllLessonPlans($user_id, $subject_id, $class_id)
	{
		$data = Objectives::where('is_active', 1)
							->select('id','unit_name','chapter_name')
							->where('subject_id', $subject_id)
							->where('user_id', $user_id)
							->get();

		$count = count($data);
		return json_encode(array('count' => $count, 'data' => $data, 'status' => 'success'));
	}
	public function getVechileStaff($employee_id) {

	$response = array();
		
		$transportation_student = TransportationStaff::where('employee_id', $employee_id)
																											->first();
		if (!$transportation_student)	
		{
			$response['status'] = 'error';
			$response['message'] = 'Staff id not registered for transportation';
		}
		else
		{
			$transportation = DB::table(Transportation::getTableName())
													->join(TransportationLatLng::getTableName(), TransportationLatLng::getTableName().'.unique_transportation_id', '=', Transportation::getTableName().'.unique_transportation_id')
													->where(Transportation::getTableName().'.id', $transportation_student->transportation_id)
													->select('lat', 'lng', 'average_speed', 'bus_code', 'number_plate', 'driver_number', TransportationLatLng::getTableName().'.unique_transportation_id')
													->first();

			if (!$transportation)
			{
				$response['status'] = 'error';
				$response['message'] = 'vehicle record not found';
			}
			else
			{
				$response['status'] = 'success';
				$response['data'] = $transportation;
			}
		}
		return json_encode($response);
	}



	public function postQrLogin()
	{
		if (!Input::has('username') ||  !Input::has('gcm_id'))
		{
			return json_encode([
				'status' => 'error',
				'message' => 'incomplete credentials'
			]);
		}

		$username = Input::get('username', '');
		$imei = Input::get('imei', '');

		$username = explode(':', $username);
		
		$data = DB::table(Imei::getTableName())
					->whereIn('username', $username)
					->where('imei', $imei)
					->first();

		if(!$data)
		{
			return json_encode([
				'status' => 'error',
				'message' => 'Imei not registered'
			]);

		}

		$username = $data->username;

		if(preg_match('/^['.STUDENT_PREFIX_IN_USERNAME.'?'.GUARDIAN_PREFIX_IN_USERNAME.'?'.EMPLOYEE_PREFIX_IN_USERNAME.'][0-9]{4}$/', $username))
		{
			if($username[0] == EMPLOYEE_PREFIX_IN_USERNAME) {
				// for school staff
				$user = Admin::where('username', $username)
											->first();
				$role = 'admin';
			} else {
				// for students and parents
				$user = User::where('username', $username)
								->first();
				$role = $user ? $user->role : '';
			}
		}
		else
		{
			$user = SuperAdmin::where('username', $username)
											->first();
				$role = 'superadmin';
		}

		if(!$user) {
			return json_encode([
				'status' => 'error',
				'message' => 'User not registered'
			]);
		}

		$username = $data->username;
		
		$this->response['username'] = $username;
		$this->response['role'] = $role;

		switch($role)
		{
			case 'student':
			case 'guardian':
				$model = 'Users';
				//$events_for = "for_students";
				break;
			case 'superadmin':
				$model = 'SuperAdmin';
				//$events_for = "for_parents";
				break;
			case 'admin':
				$model = 'Admin';
				//$events_for = "for_teachers";
				break;
			default: 
				$model = '';
				break;
		}

		$data = $model::where('username', $username)
							 ->where('is_active', 'yes')
							 ->first();

		if($data)
		{
			// these elements are for compatibility with normal users (student and parents) during gcm_id updating
			if ($role == 'admin') {
				$data->role = 'admin';
				$data->user_details_id = $data->admin_details_id;
			}
			elseif($role == 'superadmin')
			{
				$data->role = 'superadmin';
				$data->user_details_id = $data->id;
			}

			if($data->is_blocked == 1)
			{
				$this->response['status'] = "error";
				$this->response['message'] = 'blocked';
			}
			else
			{
				$this->response['data']['user_details'] = $data;
				
				if($model == 'Users')
				{
					if($data->role == 'student')
					{
						$this->response['data']['details'] = DB::table(Student::getTableName())
							->join(Section::getTableName(), Section::getTableName().'.section_code', '=', 'current_section_code')
							->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.current_class_id')
							->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
							->where(Student::getTableName().'.is_active', 'yes')
							->where('current_session_id', HelperController::getCurrentSession())
							->where('student_id', $data->user_details_id)
							->select(
								Student::getTableName().'.*', 
								StudentRegistration::getTableName().'.current_address',
								StudentRegistration::getTableName().'.permanent_address',
								Section::getTableName().'.id as current_section_id',
								Classes::getTableName().'.class_name as current_class_name'
							)
							->get();

						$user_details_model = 'StudentRegistration';
						$module_name = 'student';

					}
					elseif($data->role == 'guardian')
					{
						//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->where('for_students', 'yes');
						
						$this->response['data']['details'] = ApiHelperController::getRelatedStudents('guardian', $data->user_details_id);
						
						
						$user_details_model = 'Guardian';
						$module_name = 'guardian';
					}
				}
				elseif($data->role == 'superadmin')
				{
					//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->where('for_teachers', 'yes')->orWhere('for_management_staff', 'yes');
					/*
					*/
					$this->response['data']['exam_details'] = DB::table(ExamConfiguration::getTableName())
						->where('session_id', HelperController::getCurrentSession())
						->select('exam_name', 'id as exam_id')
						->get(); 
						
					$this->response['data']['details'] = ['superadmin', 0];

					$this->response['data']['classes'] = DB::table(ClassSection::getTableName())
						->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', ClassSection::getTableName().'.class_id')
						->where('academic_session_id', HelperController::getCurrentSession())
						->select(Classes::getTableName().'.class_name','class_id', Section::getTableName().'.section_code', Section::getTableName().'.id as section_id', 'academic_session_id as session_id')
						->get();

					$user_details_model = 'SuperAdmin';
					$module_name = 'superadmin';
				
				}
				else
				{
					//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->where('for_teachers', 'yes')->orWhere('for_management_staff', 'yes');
					/*
					*/
					$this->response['data']['exam_details'] = DB::table(ExamConfiguration::getTableName())
						->where('session_id', HelperController::getCurrentSession())
						->select('exam_name', 'id as exam_id')
						->get(); 
						
					$this->response['data']['details'] = DB::table(EmployeePosition::getTableName())
															->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
															->select(array(Group::getTableName().'.id', 'group_name'))
															->where('employee_id', $data->admin_details_id)
															->get();

					$this->response['data']['classes'] = DB::table(ClassSection::getTableName())
						->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', ClassSection::getTableName().'.class_id')
						->where('academic_session_id', HelperController::getCurrentSession())
						->select(Classes::getTableName().'.class_name','class_id', Section::getTableName().'.section_code', Section::getTableName().'.id as section_id', 'academic_session_id as session_id')
						->get();

					/*$this->response['data']['details'] = $this->response['data']['details']->toJson();*/

					$user_details_model = 'Employee';
					$module_name = 'employee';
				}

				$user_details = $user_details_model::find($data->user_details_id);

				if ($user_details) {
					foreach ($user_details->toArray() as $key => $value) {
						$this->response['data']['user_details']->$key = $value;
					}
				}

				$this->response['data']['photo'] =  ( $user_details && strlen(trim($user_details->photo)) ) ?
																							Config::get('app.url').'app/modules/'.$module_name.'/assets/images/'. $user_details->photo :
																							'';

				$this->response['data']['sex'] =  (isset($user_details->sex)) ?									$user_details->sex :
																						'male'; // NB: I am not sexist


				$this->response['status'] = "success";

				// unread nofication count
				$this->response['unread_notifications'] = (string)SavePushNotifications::where('user_id', $user_details->id)				
					->where('user_group', $data->role)
					->count();

				// delete old entries with the same gcm_id

				try
				{
					DB::connection()->getPdo()->beginTransaction();
					PushNotifications::where('gcm_id', Input::get('gcm_id'))
															->delete();

					$gcm_record = PushNotifications::where('user_id', $data->user_details_id)
																						->where('user_group', $data->role)
																						->first();
					if(!$gcm_record)
					{
						$gcm_record = new PushNotifications;
						$gcm_record->user_id = $data->user_details_id;
						$gcm_record->user_group = $data->role;
						$gcm_record->created_at = date('Y-m-d H:i:s');
					}
					$gcm_record->gcm_id = Input::get('gcm_id');
					$gcm_record->updated_at = date('Y-m-d H:i:s');
					$gcm_record->save();
					DB::connection()->getPdo()->commit();
				}
				catch(Exception $e)
				{
					DB::connection()->getPdo()->rollback();
					return json_encode(
										array(
											'status' => 'error', 
											'message'=> 'error updating gcm id'
										)
									);			
				}
					


				//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->get();
			}
		}
		
		else
		{
			$this->response['status'] = "error";
			$this->response['message'] = 'User not found or user not active';
		}

		return json_encode($this->response, JSON_PRETTY_PRINT);//
	}

	//done
	public function postLogin()
	{
		if (!Input::has('username') || !Input::has('password') || !Input::has('gcm_id'))
		{
			return json_encode([
				'status' => 'error',
				'message' => 'incomplete credentials'
			]);
		}
		$username = Input::get('username');

		//$username = Input::get('username');//$input['username'];
		$password = Input::get('password');

		if(preg_match('/^['.STUDENT_PREFIX_IN_USERNAME.'?'.GUARDIAN_PREFIX_IN_USERNAME.'?'.EMPLOYEE_PREFIX_IN_USERNAME.'][0-9]{4}$/', $username))
		{
			if($username[0] == EMPLOYEE_PREFIX_IN_USERNAME) {
				// for school staff
				$user = Admin::where('username', $username)
											->first();
				$role = 'admin';
			} else {
				// for students and parents
				$user = User::where('username', $username)
								->first();
				$role = $user ? $user->role : '';
			}
		}
		else
		{
			$user = SuperAdmin::where('username', $username)
											->first();
				$role = 'superadmin';
		}

		if(!$user) {
			return json_encode([
				'status' => 'error',
				'message' => 'User not registered'
			]);
		}

		$username = $user->username;
		
		$this->response['username'] = $username;
		$this->response['role'] = $role;

		//$role = Input::get('role');//$input['role'];

		switch($role)
		{
			case 'student':
			case 'guardian':
				$model = 'Users';
				//$events_for = "for_students";
				break;
			case 'superadmin':
				$model = 'SuperAdmin';
				//$events_for = "for_parents";
				break;
			case 'admin':
				$model = 'Admin';
				//$events_for = "for_teachers";
				break;
			default: 
				$model = '';
				break;
		}

		$data = $model::where('username', $username)
							 ->where('is_active', 'yes')
							 ->first();

		if($data)
		{
			// these elements are for compatibility with normal users (student and parents) during gcm_id updating
			if ($role == 'admin') {
				$data->role = 'admin';
				$data->user_details_id = $data->admin_details_id;
			}
			elseif($role == 'superadmin')
			{
				$data->role = 'superadmin';
				$data->user_details_id = $data->id;
			}

			if($data->is_blocked == 1)
			{
				$this->response['status'] = "error";
				$this->response['message'] = 'blocked';
			}
			elseif(Hash::check($password, $data->password) == false)
			{
				$this->response['status'] = "error";
				$this->response['message'] = 'Username password combination is invalid';
			}
			else
			{
				$this->response['data']['user_details'] = $data;
				
				if($model == 'Users')
				{
					if($data->role == 'student')
					{
						$this->response['data']['details'] = DB::table(Student::getTableName())
							->join(Section::getTableName(), Section::getTableName().'.section_code', '=', 'current_section_code')
							->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.current_class_id')
							->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
							->where(Student::getTableName().'.is_active', 'yes')
							->where('current_session_id', HelperController::getCurrentSession())
							->where('student_id', $data->user_details_id)
							->select(
								Student::getTableName().'.*', 
								StudentRegistration::getTableName().'.current_address',
								StudentRegistration::getTableName().'.permanent_address',
								Section::getTableName().'.id as current_section_id',
								Classes::getTableName().'.class_name as current_class_name'
							)
							->get();

						$user_details_model = 'StudentRegistration';
						$module_name = 'student';

					}
					elseif($data->role == 'guardian')
					{
						//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->where('for_students', 'yes');
						
						$this->response['data']['details'] = ApiHelperController::getRelatedStudents('guardian', $data->user_details_id);
						
						
						$user_details_model = 'Guardian';
						$module_name = 'guardian';
					}
				}
				elseif($data->role == 'superadmin')
				{
					//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->where('for_teachers', 'yes')->orWhere('for_management_staff', 'yes');
					/*
					*/
					$this->response['data']['exam_details'] = DB::table(ExamConfiguration::getTableName())
						->where('session_id', HelperController::getCurrentSession())
						->select('exam_name', 'id as exam_id')
						->get(); 
						
					$this->response['data']['details'] = ['superadmin', 0];

					$this->response['data']['classes'] = DB::table(ClassSection::getTableName())
						->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', ClassSection::getTableName().'.class_id')
						->where('academic_session_id', HelperController::getCurrentSession())
						->select(Classes::getTableName().'.class_name','class_id', Section::getTableName().'.section_code', Section::getTableName().'.id as section_id', 'academic_session_id as session_id')
						->get();

					$user_details_model = 'SuperAdmin';
					$module_name = 'superadmin';
				
				}
				else
				{
					//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->where('for_teachers', 'yes')->orWhere('for_management_staff', 'yes');
					/*
					*/
					$this->response['data']['exam_details'] = DB::table(ExamConfiguration::getTableName())
						->where('session_id', HelperController::getCurrentSession())
						->select('exam_name', 'id as exam_id')
						->get(); 
						
					$this->response['data']['details'] = DB::table(EmployeePosition::getTableName())
															->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
															->select(array(Group::getTableName().'.id', 'group_name'))
															->where('employee_id', $data->admin_details_id)
															->get();

					$this->response['data']['classes'] = DB::table(ClassSection::getTableName())
						->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', ClassSection::getTableName().'.class_id')
						->where('academic_session_id', HelperController::getCurrentSession())
						->select(Classes::getTableName().'.class_name','class_id', Section::getTableName().'.section_code', Section::getTableName().'.id as section_id', 'academic_session_id as session_id')
						->get();

					/*$this->response['data']['details'] = $this->response['data']['details']->toJson();*/

					$user_details_model = 'Employee';
					$module_name = 'employee';
				}

				$user_details = $user_details_model::find($data->user_details_id);

				if ($user_details) {
					foreach ($user_details->toArray() as $key => $value) {
						$this->response['data']['user_details']->$key = $value;
					}
				}

				$this->response['data']['photo'] =  ( $user_details && strlen(trim($user_details->photo)) ) ?
																							Config::get('app.url').'app/modules/'.$module_name.'/assets/images/'. $user_details->photo :
																							'';

				$this->response['data']['sex'] =  (isset($user_details->sex)) ?									$user_details->sex :
																						'male'; // NB: I am not sexist


				$this->response['status'] = "success";

				// unread nofication count
				$this->response['unread_notifications'] = (string)SavePushNotifications::where('user_id', $user_details->id)				
					->where('user_group', $data->role)
					->count();

				// delete old entries with the same gcm_id

				try
				{
					DB::connection()->getPdo()->beginTransaction();
					PushNotifications::where('gcm_id', Input::get('gcm_id'))
															->delete();

					$gcm_record = PushNotifications::where('user_id', $data->user_details_id)
																						->where('user_group', $data->role)
																						->first();
					if(!$gcm_record)
					{
						$gcm_record = new PushNotifications;
						$gcm_record->user_id = $data->user_details_id;
						$gcm_record->user_group = $data->role;
						$gcm_record->created_at = date('Y-m-d H:i:s');
					}
					$gcm_record->gcm_id = Input::get('gcm_id');
					$gcm_record->updated_at = date('Y-m-d H:i:s');
					$gcm_record->save();
					DB::connection()->getPdo()->commit();
				}
				catch(Exception $e)
				{
					DB::connection()->getPdo()->rollback();
					return json_encode(
										array(
											'status' => 'error', 
											'message'=> 'error updating gcm id'
										)
									);			
				}
					


				//$this->response['data']['upcoming_events'] = $this->response['data']['upcoming_events']->get();
			}
		}
		
		else
		{
			$this->response['status'] = "error";
			$this->response['message'] = 'User not found or user not active';
		}

		return json_encode($this->response, JSON_PRETTY_PRINT);//json_encode($this->response, JSON_PRETTY_PRINT);
	}

	 public function postChangePassword()
  {
    $response = array(
      'status'	=> 'error',
      'message'	=> ''
    );

    if (!Input::has('username') || !Input::has('role') || !Input::has('old_password') || !Input::has('new_password')) {
      $response['status'] = 'error';
      $response['message'] = 'Incomplete credentials';
    } else {
      $username = Input::get('username');
      $role = Input::get('role');
      $old_password = Input::get('old_password');
      $new_password = Input::get('new_password');

      if ($role == 'student' || $role == 'guardian') {
        $data = User::where('role', $role);
      } else if ($role == 'admin') {
        $data = new Admin;
      } else if ($role == 'superadmin') {
        $data = new Superadmin;
      } else {
        $response['status'] = 'error';
        $response['status'] = 'error';
        $response['message'] = 'Undefined role';
        return json_encode($response);
      }

      $data = $data->where('username', $username);
      
      if (!$data) {
        $response['status'] = 'error';
        $response['message'] = 'Unknown user';
      } else {
        $data = $data->first();
        if (Hash::check($old_password, $data->password)) {
	        $data->password = Hash::make($new_password);
	        $data->save();
	        $response['status'] = 'success';
	        $response['message'] = 'password updated';
		    } else {
          $response['status'] = 'error';
          $response['message'] = 'Invalid Password';
        }       
      }
    }
    return json_encode($response);
  }

	/*
	 * Login for vehicle tracking app
	 */
	public function postVehicleLogin()
	{
		$response = array();
		if (!Input::has('bus_code'))
		{
			$response['status'] = 'error';
			$response['message'] = 'Incomplete Credentials: bus code required';
		}
		else
		{
			$vehicle = Transportation::where('bus_code', Input::get('bus_code'))
															->select('unique_transportation_id', 'bus_code', 'number_plate', 'route', 'driver_number')
															->first();
			if(!$vehicle)
			{
				$response['status'] = 'error';
				$response['message'] = 'number plate not registered';
			}
			else
			{

				$response['status'] = 'success';
				$response['data'] = $vehicle;
			}
		}
		return json_encode($response, JSON_PRETTY_PRINT);
	}

	/*
	 * Update vehicle location
	 */
	public function postVehicleLocation()
	{
		if (!Input::has('lat') || !Input::has('lng') || !Input::has('unique_transportation_id'))
		{
			return json_encode(array(
					'status'	=>	'error',
					'message'	=>	'Incomplete Input'
				));
		}

		$data = array();
		$data = TransportationLatLng::where('unique_transportation_id', Input::get('unique_transportation_id'))
																			->first();
		if(!$data)
		{
			$data = new TransportationLatLng;
			$data->created_at = date('Y-m-d H:i:s');
		}
		
		$data->unique_transportation_id = Input::get('unique_transportation_id');
		$data->lat = Input::get('lat');
		$data->lng = Input::get('lng');
		$data->average_speed = Input::get('average_speed');
		$data->updated_at = date('Y-m-d H:i:s');

		$data->save();

		$response = array();
		if($data)
		{
			$response['status'] = 'success';
			$response['message'] = 'location updated';
			$response['data'] = Transportation::where('unique_transportation_id', Input::get('unique_transportation_id'))
																					->select('driver_number', 'number_plate')
																					->first();
		}
		else
		{
			$response['status'] = 'error';
			$response['message'] = 'location not updated';	
		}
		return json_encode($response, JSON_PRETTY_PRINT);


	}

	public function getVehicleLocation()
	{
		if(!Input::has('unique_transportation_id'))
		{
			return json_encode(array(
				'status'	=>	'error', 
				'message'	=>	'transportation id not given'
			));
		}

		$vehicle = DB::table(TransportationLatLng::getTableName())
									->join(Transportation::getTableName(), Transportation::getTableName().'.unique_transportation_id', '=', TransportationLatLng::getTableName().'.unique_transportation_id')
									->where(TransportationLatLng::getTableName().'.unique_transportation_id', Input::get('unique_transportation_id'))
									->select('lat', 'lng', 'bus_code', 'number_plate')
									->first();

		if(!$vehicle)
		{
			return json_encode(array(
				'status'	=>	'error', 
				'message'	=>	'transportation id not registered'
			));
		}
		else
		{
			return json_encode(array(
				'status' => 'success',
				'data'	=> $vehicle
			));
		}
	}

	/*
	 * Get vehicles available
	 */
	public function getVehicles()
	{
		$response = array();
		$vehicles = Transportation::all();
		$response['status'] = 'success';
		$response['data'] = $vehicles;

		return json_encode($response, JSON_PRETTY_PRINT);


	}

	/*
	 * Get vehicles locaation that a student is assigned to
	 */
	public function getVehicleStudent($student_id)
	{
		$response = array();
		
		$transportation_student = TransportationStudent::where('student_id', $student_id)
																											->first();
		if (!$transportation_student)	
		{
			$response['status'] = 'error';
			$response['message'] = 'student id not registered for transportation';
		}
		else
		{
			$transportation = DB::table(Transportation::getTableName())
													->join(TransportationLatLng::getTableName(), TransportationLatLng::getTableName().'.unique_transportation_id', '=', Transportation::getTableName().'.unique_transportation_id')
													->where(Transportation::getTableName().'.id', $transportation_student->transportation_id)
													->select('lat', 'lng', 'average_speed', 'bus_code', 'number_plate', 'driver_number', TransportationLatLng::getTableName().'.unique_transportation_id')
													->first();

			if (!$transportation)
			{
				$response['status'] = 'error';
				$response['message'] = 'vehicle record not found';
			}
			else
			{
				$response['status'] = 'success';
				$response['data'] = $transportation;
			}
		}
		return json_encode($response);
	}

	/*
	 * Log distance travelled by a bus
	 */
	public function postVehicleLogDistance()
	{
		$input = Input::all();
		$response = ['status' => 'error', 'message' => ''];

		$validator = Validator::make(
			$input,
			[
				'unique_transportation_id'	=> ['required', 'exists:transportation,unique_transportation_id'],
				'start_date_time'		=> ['required', 'date_format:Y-m-d H:i:s'],
				'end_date_time'		=> ['required', 'date_format:Y-m-d H:i:s'],
				'total_distance'	=> ['required']
			]
		);

		if ($validator->fails())
		{
			$messages = '';
	    foreach ($validator->messages()->all(':message'."\n") as $message)
			{
				$messages .= $message;
			}

			$response['status'] = 'error';
			$response['message'] = $messages;
		}
		else
		{
			try
			{
				TransportationDistance::create([
					'start_date_time'	=> $input['start_date_time'],
					'end_date_time'		=> $input['end_date_time'],
					'total_distance'	=> $input['total_distance'],
					'bus_id'	=> Transportation::where('unique_transportation_id', $input['unique_transportation_id'])
													->first()->id,
					'created_by'	=> 'api',
					'updated_by'	=> 'api',
					'created_at' 	=> date('Y-m-d H:i:s'),
					'updated_at' 	=> date('Y-m-d H:i:s'),
					'is_active'		=> 'yes'
				]);
			}
			catch (Exception $e)
			{
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
				return json_encode($response, JSON_PRETTY_PRINT);
			}

			$response['status'] = 'success';
			$response['message'] = 'distance logged';

			return json_encode($response, JSON_PRETTY_PRINT);
		}	
		

	}

	/*
		View library books
	*/
	
	//done
	public function libraryViewBooks($student_id) //student_id is id in registration
	{
		$this->response['data']['library_details'] = DB::table(BooksAssigned::getTableName())
														->join(Books::getTableName(), Books::getTableName().'.id', '=', BooksAssigned::getTableName().'.books_id')
														->select(
															BooksAssigned::getTableName() . '.*', 
															'title', 'author', 
															Books::getTableName() . '.max_holding_days'
														)
														->where('student_id', $student_id)
														->get();

		if(count($this->response['data']['library_details']))
		{
			$this->response['status'] = 'success';
		}
		else
		{
			$this->response['message'] = 'No books found';
		}

		return json_encode($this->response);
	}

	/*
		Daily routine list
	*/
	public function dailyRoutineList()
	{
		$data = (new DailyRoutine)->getListViewData();
		
		// change display format for start/end time
		foreach($data as $key => $value)
		{
			$data[$key]->start_time = DateTime::createFromFormat('H:i:s', $value->start_time)
																				->format('g:i A');
			$data[$key]->end_time = DateTime::createFromFormat('H:i:s', $value->end_time)
																				->format('g:i A');																			
		}
		return json_encode($data, JSON_PRETTY_PRINT);
	}

	/*
		write code for push notification here
	*/

	//done

	public function getAttendanceToday($student_id, $class_id, $section_code) 
	{
		$class = Classes::find($class_id);
		
		if($class) {
			$academic_session_id = $class->academic_session_id;
		} else {
			return json_encode(['status' => 'error', 'data' => 'invalid class']);
		}

		$academic_session = AcademicSession::find($academic_session_id);
		$session_start_date_in_bs = $academic_session->session_start_date_in_bs;
		$session_end_date_in_bs = $academic_session->session_end_date_in_bs;

		$start_date_array = explode('-', $session_start_date_in_bs);
		$end_date_array = explode('-', $session_end_date_in_bs);

		if (count($start_date_array) != 3 || count($end_date_array) != 3) {
			return json_encode(['status' => 'error', 'data' => '']);
		}
		
		$date_ad = date('Y-m-d');
		$filename = app_path().'/modules/attendance/assets/attendance-records/'.$date_ad .'_'.$class_id.'_'.$section_code.'.csv';

		if(file_exists($filename) && is_file($filename)) {
			$attendance_contents = file_get_contents($filename);
			$temp = HelperController::csvToArray($filename, $search_for_key = 0, $search_for_value = $student_id);
			if(CALENDAR == 'BS') {
				$today = HelperController::formatNepaliDate((new DateConverter)->ad2bs(date('Y-m-d')));
			} else {
				$today = DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d F Y');
			}

			$temp[] = $today;
			$this->response['data']['attendance_details'] = $temp;
			$this->response['status'] = 'success';
			$this->response['message'] = isset($this->response['data']['attendance_details']) ? '' : 'Attendance not found';
		}
		else {
			$this->response['status'] = 'error';
			$this->response['message'] = 'Attendance not done';
		}
		
		return json_encode($this->response);
	}



	public function getAttendanceClass($class_id, $section_code) 
	{
		$class = Classes::find($class_id);
		
		if($class) {
			$academic_session_id = $class->academic_session_id;
		} else {
			return json_encode(['status' => 'error', 'data' => 'invalid class']);
		}

		$academic_session = HelperController::getCurrentSession();
		$date_ad = date('Y-m-d');

		$date_converter = new DateConverter;

		$date_bs = $date_converter->ad2bs($date_ad);
		
		$filename = app_path().'/modules/attendance/assets/attendance-records/'.$date_ad .'_'.$class_id.'_'.$section_code.'.csv';
		$data = ApiHelperController::getStudentsForAttendance($class_id, $section_code, $date_ad);
		
		if(CALENDAR == 'BS') {
			$date_string = HelperController::formatNepaliDate($date_bs);
		} else {
			$date_string = DateTime::createFromFormat('Y-m-d', $date_ad)->format('d F Y');
		}
		$this->response['data']['attendance_details'] = $data;
		$this->response['data']['date'] = $date_string;
		$this->response['data']['date_ad'] = $date_ad;
				

		$this->response['status'] = 'success';
		$this->response['message'] = isset($this->response['data']['attendance_details']) ? '' : 'Attendance not found';
		return json_encode($this->response);
	}
	public function getAttendance($student_id, $class_id, $section_code, $month, $start_day, $end_day, $year) 
	{
		
		
		for($start_day; $start_day <= $end_day; $start_day++)
		{

			$filename[] = $year.'-'.$month.'-'.$start_day.'_'.$class_id.'_'.$section_code.'.csv';

		}
		$directory = base_path('/app/modules/attendance/assets/attendance-records');
			
			$files = File::allFiles($directory);
			foreach ($files as $file)
			{
				$d[] = (string)$file;	   
			
			}
			
			
		

		$this->response['status'] = 'success';
		$this->response['message'] = isset($this->response['data']['attendance_details']) ? '' : 'Attendance not found';
		return json_encode($this->response);
	}		
	

	public function postAttendanceClass($class_id, $section_code) 
	{

		$data = json_decode(Input::get('data'), true);
		$post_type = Input::get('type', 's'); //s means only save the attendance. p means save and send push notification
		$date = Input::get('date_ad');
		$attendance_status_to_send_push_notifications = ['a', 'l'];
		$student_table = Student::getTableName();
		$student_guardian_relation_table = StudentGuardianRelation::getTableName();
		$guardian_table = Guardian::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$gcm_table =  PushNotifications::getTableName();
		//$push_notifications['a'] = [];
		//$push_notifications['l'] = [];
		$response = array();
		if (isset($data['status']) && $data['status'] == 'success') {
			if (isset($data['data']) && gettype($data['data']) == 'array') {
				if (isset($data['data']['attendance_details']) && gettype($data['data']['attendance_details']) == 'array') {
					$output_array = array();
					foreach($data['data']['attendance_details'] as $row) {
						$output_array[] = [$row['student_id'], $row['attendance_status'], $row['attendance_comment']];
						if(in_array($row['attendance_status'], $attendance_status_to_send_push_notifications) && $post_type == 'p')
						{
							//$gcm = new GcmController;
							


							$parent_ids = DB::table($guardian_table)
							->join($student_guardian_relation_table, $student_guardian_relation_table.'.guardian_id', '=', $guardian_table.'.id')
							->join($student_registration_table, $student_registration_table.'.id', '=', $student_guardian_relation_table.'.student_id')
							//->leftJoin($gcm_table, $gcm_table.'.user_id', '=', $guardian_table.'.id')
							->where($student_registration_table.'.id', $row['student_id'])
							//->where($gcm_table.'.user_group', 'guardian');
							->select($guardian_table . '.id')
							->lists('id');

							$gcm_ids = PushNotifications::where('user_group', 'guardian')
														->whereIn('user_id', $parent_ids)
														->select('gcm_id')
														->lists('gcm_id');
								
							
							$module_name = 'attendance';

							$student_name = StudentRegistration::find($row['student_id'])
																	->student_name;

							$notification_status = $row['attendance_status'] == 'a' ? 'absent' : 'late';
							$msg = $module_name . ' # '.
										$student_name . ' was '.$notification_status.' on '.
										substr($date, 0, 10);

							if($row['attendance_comment'])
							{
								$msg = $msg . ' (' . $row['attendance_comment'] .')';
							}

							if(count($parent_ids)) 
							{
								(new GcmController)->send($gcm_ids, $msg, $parent_ids, 'guardian');
							}
						}
					}
					$filename = ATTENDANCE_RECORD_LOCATION. $date .'_'.$class_id.'_'.$section_code.'.csv';
					$file_handle = fopen($filename,'w');
					foreach($output_array as $record)
					{
						fputcsv($file_handle, $record);
					}
					fclose($file_handle);

					$response['status'] = 'success';
					$response['message'] = 'attendance updated';	
				} else {
					$response['status'] = 'error';
					$response['message'] = 'attendance_details field missing';	
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = 'data field missing';
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = 'status field missing';
		}

		return json_encode($response);
	}

	// public function getAttendance($class_or_student_or_school, $student_id, $class_id, $section_code, $year, $month)
	// {
	// 	$no_of_days = HelperController::getNumberOfDaysInAMonth($month, $year);

	// 	for($i = 1; $i<=$no_of_days; $i++)
	// 	{
	// 		$filename = Carbon::create($year, $month, $i)->format('Y-m-d');
			
	// 		$filename = app_path().'/modules/attendance/assets/attendance-records/'.$filename.'_'.$class_id.'_'.$section_code.'.csv';

	// 		if(file_exists($filename) && is_file($filename))
	// 		{
	// 			$attendance_contents = file_get_contents($filename);
	// 			switch($class_or_student_or_school)
	// 			{
	// 				case 'student':

	// 							$temp = HelperController::csvToArray($filename, $search_for_key = 0, $search_for_value = $student_id);
	// 							$temp[] = count($temp) ? Carbon::create($year, $month, $i)->format('Y-m-d') : array();
	// 							$this->response['data']['attendance_details'][] = $temp;
	// 							if(Carbon::today()->format('d') == $i)
	// 							{
	// 								$this->response['data']['today'] = $temp;
	// 							}
	// 							break;

	// 				case 'class': 
	// 							$this->response['data']['attendance_details'][] = HelperController::csvToArray($filename);
	// 							break;

	// 			}

	// 			$this->response['status'] = 'success';
				
	// 		}
			
	// 	}


	// 	$this->response['message'] = isset($this->response['data']['attendance_details']) ? '' : 'Attendance not found';
			

	// 	return json_encode($this->response);
	// }

	/*
	 * For fee
	 */
	public function getFee($academic_session_id, $student_id, $month) 
	{
	
		$current_session_id = $academic_session_id;

		$student = DB::table(Student::getTableName())
					->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'current_class_id')
					->where(Student::getTableName().'.student_id', $student_id)
					->where(Student::getTableName().'.current_session_id', $current_session_id)
					->first();
		
		if(!StudentRegistration::find($student_id))
		{
			return json_encode([
							'status' => 'error',
							'msg' =>  'Student Not Registered'
						]);
					
		}
		elseif(!$student)
		{
			return json_encode([
							'status' => 'error',
							'msg' =>  'Student Not Registered For the given session'
						]);
		}
		else
		{
			if($month == 0)
			{
				// we show all records
				$payments = FeePayment::where('student_id', $student_id)
								->where('academic_session_id', $current_session_id)
								->orderBy('month')
								->select('fee_amount', 'received_amount', 'is_paid', 'month')
								->get();

				return json_encode([
									'status'	=> 'success',
									'data'		=> $payments
							]);			
			}
			else
			{
				$payment = FeePayment::where('student_id', $student_id)
								->where('academic_session_id', $current_session_id)
								->where('month', $month)
								->select('fee_amount', 'received_amount', 'is_paid')
								->first();

				$other_dues = FeePayment::where('student_id', $student_id)
								->where('academic_session_id', $current_session_id)
								->where('is_paid', 'no')
								->where('month', '!=', $month)
								->orderBy('month')
								->select(
									'month',
									'fee_amount',
									'received_amount',
									'is_paid'
								)
								->get()
								->toArray();

				$other_dues = array_map(function($due) {
					if ($due['is_paid'] == 'yes')
					{
						$due['status'] = 'Paid';
					}
					else
					{
						if ($due['received_amount'])
						{
							$due['status'] = 'Partially Paid';
						}
						else
						{
							$due['status'] = 'Unpaid';
						}
					}
					return $due;
				}, $other_dues);

				if(!$payment) 
				{
					return json_encode([
							'status' => 'error',
							'msg' => 'Fee not generated for the given month',
							'other_dues'	=> $other_dues
						]);
							
				}
				else
				{
					$monthly_fee = MonthlyStudentFee::where('student_id', $student_id)
												->where('month', $month)
												->where('academic_session_id', $current_session_id)
												->select('amount')
												->first();

					$misc_class_fees = 
						DB::table(MiscClassStudentFee::getTableName())
								->join(MiscClassFee::getTableName(), MiscClassFee::getTableName().'.id', '=', MiscClassStudentFee::getTableName().'.fee_misc_class_id')
								->where(MiscClassStudentFee::getTableName().'.student_id', $student_id)
								->where(MiscClassStudentFee::getTableName().'.month', $month)
								->where(MiscClassStudentFee::getTableName().'.academic_session_id', $current_session_id)
								->select(MiscClassStudentFee::getTableName().'.amount', MiscClassFee::getTableName().'.title')
								->get();

					$misc_student_fees = 
						MiscStudentFee::where('student_id', $student_id)
																	->where('month', $month)
																	->where('academic_session_id', $current_session_id)
																	->select('amount', 'title')
																	->get();

					$examination_fee = 
						DB::table(ExaminationStudentFee::getTableName())
									->join(ExaminationFee::getTableName(), ExaminationFee::getTableName().'.id', '=', ExaminationStudentFee::getTableName().'.fee_examination_id')
									->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', ExaminationFee::getTableName().'.exam_id')
									->where(ExaminationStudentFee::getTableName().'.student_id', $student_id)
									->where(ExaminationStudentFee::getTableName().'.month', $month)
									->where(ExaminationStudentFee::getTableName().'.academic_session_id', $current_session_id)
									->select(ExaminationStudentFee::getTableName().'.*', ExamConfiguration::getTableName().'.exam_name')
									->select(ExaminationStudentFee::getTableName().'.amount')
									->first();

					$transportation_fee = 
						TransportationStudentFee::where('student_id', $student_id)
																	->where('month', $month)
																	->where('academic_session_id', $current_session_id)
																	->select('amount')
																	->first();

					$hostel_fee = 
						HostelStudentFee::where('student_id', $student_id)
																	->where('month', $month)
																	->where('academic_session_id', $current_session_id)
																	->select('amount')
																	->first();

					$scholarships = 
						DB::table(ScholarshipMonthly::getTableName())
							->join(Scholarship::getTableName(), Scholarship::getTableName().'.id', '=', ScholarshipMonthly::getTableName().'.scholarship_id')
							->where(ScholarshipMonthly::getTableName().'.student_id', $student_id)
							->where(ScholarshipMonthly::getTableName().'.month', $month)
							->where(ScholarshipMonthly::getTableName().'.academic_session_id', $current_session_id)
							->lists('amount', 'type');							
					
					$fee_details = [];
					if ($monthly_fee && $monthly_fee->amount) 
						$fee_details[] = ['title' => 'Tuition Fee', 'amount' => $monthly_fee->amount];
					if ($examination_fee && $examination_fee->amount) 
						$fee_details[] = ['title' => 'Examination Fee',  'amount' => $examination_fee->amount];
					if ($transportation_fee && $transportation_fee->amount) 
						$fee_details[] = ['title' => 'Transportation Fee', 'amount' => $transportation_fee->amount];
					if ($hostel_fee && $hostel_fee->amount) 
						$fee_details[] = ['title' => 'Hostel Fee', 'amount' => $hostel_fee->amount];

					foreach($misc_class_fees as $misc_fee) 
					{
						if ($misc_fee->amount)
						{
							$fee_details[] = ['title' => $misc_fee->title, 'amount' => $misc_fee->amount];
						}
					}

					foreach($misc_student_fees as $misc_fee) 
					{
						if ($misc_fee->amount)
						{
							$fee_details[] = ['title' => $misc_fee->title, 'amount' => $misc_fee->amount];
						}
					}

					$total_dues = $payment->fee_amount - $payment->received_amount;
					foreach ($other_dues as $due)
					{
						$total_dues += $due['fee_amount'] - $due['received_amount'];
					}

					return json_encode([
						'status' => 'success',
						'payment' =>  $payment,
						'fee_details' => $fee_details,
						'scholarships' => $scholarships,
						'other_dues'	=> $other_dues,
						'total_dues'	=> $total_dues
					], JSON_PRETTY_PRINT);
				}
			}
		}
	}

	/*
	/
	/
		These are for assignments
	/
	/
	*/

	//done
	public function getSubjectListFromClassIdAndSectionId($class_id, $section_id)
	{
		$data = DB::table(Subject::getTableName())
					->where('class_id', $class_id)
					->where('section_id', $section_id)
					->where('is_active', 'yes')
					->select(array('subject_name', 'id'))
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));
	}

	//done
	public function getSubjectAssignments($subject_id)
	{
		$data = DB::table(DownloadsSubjectMap::getTableName())
					->join(Assignments::getTableName(), Assignments::getTableName().'.id', '=', 'download_id')
					->select(array(DownloadsSubjectMap::getTableName().'.id', 'filename', 'google_file_id'))
					->where(Assignments::getTableName().'.is_active', 'yes')
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));
	}

	public function getAssignments($class_id, $section_id) {
		$children = Subject::join(
			DownloadsSubjectMap::getTableName(),
			DownloadsSubjectMap::getTableName() . '.subject_id', '=',
			Subject::getTableName() . '.id'
		)->join(
			DownloadManager::getTableName(),
			DownloadManager::getTableName() . '.id', '=',
			DownloadsSubjectMap::getTableName() . '.download_id'
		)->where(
			Subject::getTableName() . '.class_id', $class_id
		)->where(
			Subject::getTableName() . '.section_id', $section_id
		)->orderBy(
			DownloadManager::getTableName() . '.updated_at', 'DESC'
		)->select(
			DownloadManager::getTableName() . '.*',
			DownloadsSubjectMap::getTableName() . '.id as assignment_id',
			Subject::getTableName() . '.subject_name',
			Subject::getTableName() . '.class_id',
			Subject::getTableName() . '.section_id'
		)->get()->toArray();

		$google_drive = new EasyDriveAPI2(Request::url());

		$children_file_array =array_map(
				function($child) use ($google_drive) {
					$child = (object)$child;
					$child->download_link = $google_drive->getDownloadLink($child->google_file_id);
					return $child;
				},
				$children
			);
		
		return json_encode(array('status' => 'success', 'data' => $children_file_array));
	}


	
	public function getClassIdsAndSectionIdsOfFromTeacherId($teacher_id, $session_id = 0)
	{
		$session_id = $session_id ? $session_id : HelperController::getCurrentSession();
		$data = DB::table(Teacher::getTableName())
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id')
					->join(Section::getTableName(), Section::getTableName().'.section_code', '=', Teacher::getTableName().'.section_code')
					->where('teacher_id', $teacher_id)
					->where(Teacher::getTableName().'.session_id', $session_id)
					->where(Teacher::getTableName().'.is_active', 'yes')
					->select(Section::getTableName().'.section_code', Section::getTableName().'.id as section_id', 'class_id', 'class_name')
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));

	}
	


	/*
	/
	/ These are for exam routine
	/
	/
	*/

	//done
	public function getExamRoutine($class_id, $section_id, $exam_id)
	{
		$data = ExamDetails::join(Subject::getTableName(), Subject::getTableName().'.id', '=', ExamDetails::getTableName().'.subject_id')
					->where('exam_id', $exam_id)
					->where('class_id', $class_id)
					->where('section_id', $section_id)
					->where(ExamDetails::getTableName().'.is_active', 'yes')
					->where(Subject::getTableName().'.is_active', 'yes')
					->select(array(
						'subject_name', 
						ExamDetails::getTableName().'.pass_marks', 
						ExamDetails::getTableName().'.full_marks', 
						'start_date_in_ad', 
						'start_date_in_bs', 
						'duration'
					))
					->orderBy('start_date_in_ad', 'ASC')
					->get()
					->toArray();

		
		$data = array_map(function($d) {
			$d['start_date_in_ad'] = DateTime::createFromFormat('Y-m-d H:i:s', $d['start_date_in_ad'])->format('d F Y*g:i A');

			$d['start_date_in_bs'] = HelperController::formatNepaliDate($d['start_date_in_bs']);

			return $d;
		}, $data);


		return json_encode(array('status' => 'success', 'data' => $data));
	}

	/*
	/
	/ These are for exam results
	/
	/
	*/
	public function getExamMarks($exam_id, $student_id)
	{
		$unpaid_fees = FeePayment::where('student_id', $student_id)
			->orderBy('month')
			->where('is_paid', 'no')
			->get();
		
		if (count($unpaid_fees))
		{
			return json_encode(array(
				'status' => 'error',
				'msg'		=> 'You have unpaid fees'
			));
		}

		$data = DB::table(ExamMarks::getTableName())
				->join(Report::getTableName(), Report::getTableName().'.student_id', '=', ExamMarks::getTableName().'.student_id')
				->join(ExamDetails::getTableName(), ExamDetails::getTableName().'.subject_id', '=', ExamMarks::getTableName().'.subject_id')
				->join(Subject::getTableName(), Subject::getTableName().'.id', '=', ExamMarks::getTableName().'.subject_id')
				->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', ExamMarks::getTableName().'.student_id')
				->where(ExamMarks::getTableName().'.exam_id', $exam_id)
				->where(ExamDetails::getTableName().'.exam_id', $exam_id)
				->where(Report::getTableName().'.exam_id', $exam_id)
				->where(ExamMarks::getTableName().'.student_id', $student_id)
				->where(ExamDetails::getTableName().'.is_active', 'yes')
				->select(ExamMarks::getTableName().'.marks', ExamDetails::getTableName().'.pass_marks', ExamDetails::getTableName().'.full_marks', Subject::getTableName().'.subject_name', Report::getTableName().'.*', StudentRegistration::getTableName().'.student_name')
				->get();

		$sum_grade_points = 0;
		foreach ($data as $d)
		{
			$grade = GradeHelperController::convertPercentageToGrade((float)$d->marks / $d->full_marks * 100);
			$grade_point = GradeHelperController::convertPercentageToGradePoint((float)$d->marks / $d->full_marks * 100);
      $sum_grade_points += $grade_point;

      $d->grade = $grade;
      $d->grade_point = $grade_point;
		}


		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		
		$result = array();

		if(isset($data[0]))
		{
			$cgpa = round($sum_grade_points / count($data), 1);

			$result[] = array('title' => 'Total',
	    									'value' => $data[0]->total_marks);

			$result[] = array('title' => 'Status',
	    									'value' => $data[0]->status);

			$result[] = array('title' => 'Rank',
	    									'value' => $data[0]->rank);

			// if($config->show_percentage == 'yes')
	      $result[] = array('title' => 'Percentage',
	      									'value'	=> $data[0]->percentage);
	    
	    // if($config->show_grade == 'yes')
	    	$result[] = array(
					'title' => 'Grade',
					'value' => GradeHelperController::convertGradePointToGrade($cgpa)
											// GradeHelperController::convertPercentageToGrade($data[0]->percentage)
				);
	    // if($config->show_grade_point == 'yes')
	    	
	    	$result[] = array(
	    		'title' => 'Grade Point Average',
	    		'value' => $cgpa
	    	);

    }	
		
		return json_encode(array(
				'status'	=> 'success', 
				'data' 		=> $data,
				'result'	=> $result,
				'show_fields' => $config
			), 
			JSON_PRETTY_PRINT
		);
	}

	//public function getExamRoutineByStudentIdAndExamId

	/*
	/
	/ These are for academic calendar
	/
	/
	*/

	//done
	public function academicCalendar($event_group = 'all')
	{
		//get current session
		$current_session = DB::table(AcademicSession::getTableName())
							  ->where('is_active', 'yes')
							  ->where('is_current', 'yes')
							  ->select('session_start_date_in_ad', 'session_end_date_in_ad')
							  ->first();

		if($current_session)
		{
			$data = Events::where(
				'from_ad', '>=', Carbon::createFromFormat('Y-m-d', $current_session->session_start_date_in_ad)->format('Y-m-d H:i:s')
				)
				->where('to_ad', '<=', Carbon::createFromFormat('Y-m-d', $current_session->session_end_date_in_ad)->format('Y-m-d H:i:s'));
						
			if($event_group == 'students')
				$data = $data->where('for_students', 'yes');
			elseif($event_group == 'teachers')
				$data = $data->where('for_teachers', 'yes');
			elseif($event_group == 'parents')
				$data = $data->where('for_parents', 'yes');
			elseif($event_group == 'management_staff')
				$data = $data->where('for_management_staff', 'yes');
			else
			{

			}

			$data = $data->select(array('title', 'from_ad', 'to_ad', 'from_bs', 'to_bs', 'id', 'description'))
						 ->orderBy('from_ad', 'ASC')
						 ->get()
						 ->toArray();

			// format datetime
			$data = array_map(function($event) {
				$event['from_ad'] = DateTime::createFromFormat(
					'Y-m-d H:i:s', $event['from_ad']
				)->format('Y-m-d*g:i A');

				$event['to_ad'] = DateTime::createFromFormat(
					'Y-m-d H:i:s', $event['to_ad']
				)->format('Y-m-d*g:i A');

				$event['from_bs'] = HelperController::formatNepaliDate(substr($event['from_bs'], 0, 10)) . ' ' .
					DateTime::createFromFormat(
						'H:i:s',
						substr($event['from_bs'], 11)
					)->format('g:i A');
					
				$event['to_bs'] = HelperController::formatNepaliDate(substr($event['to_bs'], 0, 10)) . ' ' .
					DateTime::createFromFormat(
						'H:i:s',
						substr($event['to_bs'], 11)
					)->format('g:i A');

				return $event;

			}, $data);

			$return = array('status' => 'success', 'data' => $data, 'message' => '');
		}
		else
		{
			$return = array('status' => 'error', 'data' => array(), 'message' => 'Session not set to active');	
		}
		
		return json_encode($return);

	}

	//done
	public function getUpcomingEvents($event_group = 'all', $no_of_events = 10, $current_date = '')
	{
		$current_date = strlen($current_date) ? $current_date : Carbon::now()->format('Y-m-d');
		

		$data = Events::select(array(
			'id', 'title', 'from_ad', 'from_bs', 'to_ad', 'to_bs', 'description'
		))
		->where('is_active', 'yes')
		->where('from_ad', '>=', $current_date)

		->where('is_active', 'yes');

		if($event_group == 'students')
			$data = $data->where('for_students', 'yes');
		elseif($event_group == 'teachers')
			$data = $data->where('for_teachers', 'yes');
		elseif($event_group == 'parents')
			$data = $data->where('for_parents', 'yes');
		elseif($event_group == 'management_staff')
			$data = $data->where('for_management_staff', 'yes');
		else
		{

		}

		$data = $data->orderBy('from_ad','ASC')
					 ->take($no_of_events)
					 ->get()
					 ->toArray();

		// format datetime
		$data = array_map(function($event) {
			$event['from_ad'] = DateTime::createFromFormat(
				'Y-m-d H:i:s', $event['from_ad']
			)->format('Y-m-d*g:i A');

			$event['to_ad'] = DateTime::createFromFormat(
				'Y-m-d H:i:s', $event['to_ad']
			)->format('Y-m-d*g:i A');

			$event['from_bs'] = HelperController::formatNepaliDate(substr($event['from_bs'], 0, 10)) . ' ' .
				DateTime::createFromFormat(
					'H:i:s',
					substr($event['from_bs'], 11)
				)->format('g:i A');
				
			$event['to_bs'] = HelperController::formatNepaliDate(substr($event['to_bs'], 0, 10)) . ' ' .
				DateTime::createFromFormat(
					'H:i:s',
					substr($event['to_bs'], 11)
				)->format('g:i A');

			return $event;

		}, $data);

		return json_encode(array('status' => 'success', 'data' => $data));
	}



////////////////////////// these are for gcm ids /////////////////////////////////////////////////////////////////
	
	//done
	public function postStoreInDatabase()
	{
		$gcm_id = Input::get('gcm_id', 'null');
		$user_group = Input::get('user_group', 'null');
		$user_id = Input::get('user_id', 'null');

		//return json_encode(array('status' => 'error', 'data' => $user_id));

		if($gcm_id == 'null' || $user_group == 'null' || $user_id == 'null')
		{
			$return = array('status' => 'error', 'message' => 'GCM Id or user group or user id not provided', 'data' => 0);
		}
		else
		{
			try
			{
				$data = PushNotifications::create(array('gcm_id' => $gcm_id, 'user_group' => $user_group, 'user_id' => $user_id, 'is_active' => 'yes'))->id;	
				$return = array('status' => 'success', 'message' => 'Gcm Id successfully stored', 'data' => $data);
			}
			catch(Exception $e)
			{
				$return = array('status' => 'error', 'message' => $e->getMessage(), 'data' => 0);	
			}			
		}

		return json_encode($return);
	}

	//done
	public function enableDisableNotification($user_group, $user_id, $notification_status)
	{
		$record = PushNotifications::where('user_group', $user_group)
									->where('user_id', $user_id)
									->first();

		if($record)
		{
			if($record->is_active == $notification_status)
			{
				$message = $notification_status == 'yes' ? 'Notifications alredy enabled' : 'Notifications already disabled';
				$return = array('status' => 'error', 'message' => $message, 'data' => $record->id);
			}	
			else
			{
				try
				{
					$record->is_active = $notification_status;
					$record->save();
					$message = $notification_status == 'yes' ? 'Notifications successfully enabled' : 'Notifications succesfully disabled';
					$retrn = array('status' => 'success', 'message' => $message, 'data' => $record->id);
				}
				catch(Exception $e)
				{
					$retrn = array('status' => 'error', 'message' => 'Error in changing status. Please try again', 'data' => $record->id);
				}
			}
		}
		else
		{
			$retrn = array('status' => 'error', 'message' => 'Record Not Found', 'data' => 0);
		}

		return json_encode($return);
	}

	public function markNotificationViewed($notification_id)
	{
		$notification = SavePushNotifications::find($notification_id);
		$result = array();
		if ($notification)
		{
			$notification->is_active = 'no';
			$notification->updated_at = date('Y-m-d H:i:s');
			$notification->updated_by = 'app api call';
			$notification->save();
			
			$result['status'] = 'success';
			$result['msg'] = '';
		}
		else
		{
			$result['status'] = 'error';
			$result['msg'] = 'Invalid notification_id';
		}
		return json_encode($result, JSON_PRETTY_PRINT);
	}

	public function getUnreadNotificationsNumber($user_id, $role)
	{
		$notifications_number = (string)SavePushNotifications::where('user_id', $user_id)
			->where('user_group', $role)
			->where('is_active', 'yes')
			->count();

		return json_encode([
			'status' => 'success', 
			'data' => $notifications_number
		]);
	}

	/* This is done by Prabal */

		//API For Dashboard Notice

		public function getNotice()
		{
			try
			{
				$data = File::get(app_path().'/modules/notice/notice.json');
				$data = json_decode($data, true);
				if ($data)
				{
					foreach ($data as $key => $value)
					{
						$data[$key] = strip_tags($value);
					}
				}
				return json_encode(array('status' => 'success', 'data' => $data));
			}
			catch(Exception $e)
			{
				return json_encode(array('status' => 'error', 'message' => 'Notice not found', 'data' => $e->getMessage()));
			}
			
		}

		public function postNotice()
		{
			$data = array('title' => '', 'body' => '', 'created_at' => '');
			$data['title'] = Input::get('title', 'No notice to display');
			$data['body'] = Input::get('body', 'No notice to display');
			$data['created_at'] = date('Y-m-d H:i:s');

			try
			{
				File::put(app_path().'/modules/notice/notice.json', json_encode($data, JSON_PRETTY_PRINT));	
				return json_encode(array('status' => 'success', 'message' => 'Notice successfully published', 'data' => $data));
			}
			catch(Exception $e)
			{
				return json_encode(array('status' => 'error', 'message' => 'Notice could not be published successfully', 'data' => $e->getMessage()));
			}
			
			
		}

		/*
		/
		/
		/
			These are messaging apis
		/
		/
		/
		*/

		public function postMessage()
		{
			//post parameters -> message_to_id, message_to_group
			$controller = new MessageController;
			try
			{
				$controller->apiPostSendMessage();	
			}
			catch(Exception $e)
			{
				return json_encode(array('status' => 'error', 'message' => $e->getMessage()));
			}
		}

		public function getMessages()
		{
			$controller = new MessageController;
			try
			{
				//date_range, message_from, role, details_id
				$data = $controller->apiGetMessageList();
				$return = array();
				foreach($data[0] as $d)
				{
					$return[] = $d;
				}

				return json_encode(array('status' => 'success', 'data' => $return));
			}
			catch(Exception $e)
			{
				return json_encode(array('status' => 'error', 'message' => $e->getMessage()));
			}
		}

		public function getRelatedTeachers()
		{
			$id = Input::get('id', 0);
			$group = Input::get('group', 'student');
			//$session = Input::get('session_id', HelperController::getCurrentSession());
			$session_id = HelperController::getCurrentSession();

			switch($group)
			{

				case 'guardian':
				
					//get related students first
					$return = array();
					$student_ids = DB::table(StudentGuardianRelation::getTableName())
								->where('guardian_id', $id)
								->lists('student_id');

					//print_r($student_ids);
					//die();

					if(count($student_ids))
					{
						$class_sections = DB::table(Student::getTableName())
									->select(array('current_section_code', 'current_class_id'))
									->whereIn('student_id', $student_ids)
									->where('current_session_id', $session_id)
									->get();
									//->lists('current_section_code', 'current_class_id');


						
						if($class_sections)
						{
							$data = DB::table(Teacher::getTableName())
										->join(Admin::getTableName(), Admin::getTableName().'.admin_details_id', '=', Teacher::getTableName().'.teacher_id')
										->where('session_id', $session_id)
										->whereIn('class_id', array_keys($class_sections))
										->where('section_code', array_values($class_sections))
										->select(array('name', 'teacher_id'))
										->get();	

							$return = $data;
						}	
					}

				case 'student':
				default:
					$return = array();
					$class_sections = DB::table(Student::getTableName())
								->select(array('current_section_code', 'current_class_id'))
								->where('student_id', $id)
								->where('current_session_id', $session_id)
								->first();

					if($class_sections)
					{
						$data = DB::table(Teacher::getTableName())
									->join(Admin::getTableName(), Admin::getTableName().'.admin_details_id', '=', Teacher::getTableName().'.teacher_id')
									->where('session_id', $session_id)
									->where('class_id', $class_sections->current_class_id)
									->where('section_code', $class_sections->current_section_code)
									->select(array('name', 'teacher_id'))
									->get();	

						$return = $data;
					}
					break;

			}

			return json_encode($return);
		}

		public function viewMessageHistory()
		{
			$group = Input::get('group', '');
			$id = Input::get('id', '');
			//$details_role = Input::get('details_role', '');
			//$details_role = Input::get('details_id', '');


			$controller = new MessageController;
			try
			{
				//date_range, message_from, role, details_id
				$data = $controller->apiGetViewData($group, $id);
				$other_data = $controller->apiGetViewOtherData($group, $id);

				$return = array();

				foreach($data as $d)
				{
					$return[] = $d;
				}

				return json_encode(array('status' => 'success', 'data' => $return, 'other_data' => $other_data));
			}
			catch(Exception $e)
			{
				return json_encode(array('status' => 'error', 'message' => $e->getMessage()));
			}

		}

		public function guardianToTeachersMessageList($class_id, $section_id, $guardian_id)
		{
			$section_code = Section::find($section_id)->section_code;
			$current_session_id = HelperController::getCurrentSession();

			$teachers = Teacher::where(Teacher::getTableName().'.class_id', $class_id)
				->where(Teacher::getTableName().'.section_code', $section_code)
				->where(Teacher::getTableName().'.session_id', $current_session_id)
				->join(Employee::getTableName(), Employee::getTableName().'.id', '=', Teacher::getTableName().'.teacher_id')
				->select("employee_name as teacher_name", Teacher::getTableName().'.teacher_id', 'photo')
				->get();

			foreach ($teachers as $teacher)
			{
				if (strlen(trim($teacher->photo)))
				{
					$teacher->photo = Config::get('app.url').'app/modules/employee/assets/images/' . $teacher->photo;
				}
				else
				{
					$teacher->photo = '';
				}

				$teacher->subjects = SubjectTeacher::where(SubjectTeacher::getTableName().'.class_id', $class_id)
				->where(SubjectTeacher::getTableName() . '.teacher_id', $teacher->teacher_id)
				->where(SubjectTeacher::getTableName().'.section_id', $section_id)
				->where(SubjectTeacher::getTableName().'.session_id', $current_session_id)
				->leftJoin(Subject::getTableName(), Subject::getTableName().'.id', '=', SubjectTeacher::getTableName().'.subject_id')
				->lists('subject_name');
				//->groupBy(SubjectTeacher::getTableName().'.teacher_id')
				// ->select(DB::raw("GROUP_CONCAT(subject_name SEPARATOR ', ') as subjects"))
				// ->get();

				$teacher->unread_messages = (string)Message::where('message_from_id', $teacher->teacher_id)
					->where('message_from_group', 'admin')
					->where('message_to_id', $guardian_id)
					->where('message_to_group', 'guardian')
					->where('is_viewed', 'no')
					->count();
			}
			return json_encode(
				array(
					'status' => 'success',
					'data' => $teachers
				),
				JSON_PRETTY_PRINT
			);
		}

		public function getConversations($user_id1, $user_group1, $user_id2, $user_group2)
		{
			$data = Message::
				where(
					function($query) use ($user_id1, $user_group1, $user_id2, $user_group2)
					{
						return $query->where('message_from_group', $user_group1)
								->where('message_from_id',$user_id1)
								->where('message_to_group', $user_group2)
								->where('message_to_id', $user_id2);
					}
				)
				->orWhere(
					function($query) use ($user_id1, $user_group1, $user_id2, $user_group2)
					{
						return $query->where('message_to_group', $user_group1)
									->where('message_to_id',$user_id1)
									->where('message_from_group', $user_group2)
									->where('message_from_id', $user_id2);
					}
				)
				->orderBy('id', 'DESC')
				->paginate(20)
				->toArray();

			return json_encode(
				array(
					'status' => 'success',
					'data' => $data
				),
				JSON_PRETTY_PRINT
			);	
		}

		public function guardianToTeacherMessages($guardian_id, $teacher_id, $role = '')
		{
			switch ($role)
			{
				case 'admin':
				Message::where('message_to_id', $teacher_id)
					->where('message_to_group', 'admin')
					->where('message_from_id', $guardian_id)
					->where('message_from_group', 'guardian')
					->where('is_viewed', 'no')
					->update(['is_viewed' => 'yes']);

				case 'guardian':
				Message::where('message_to_id', $guardian_id)
					->where('message_to_group', 'guardian')
					->where('message_from_id', $teacher_id)
					->where('message_from_group', 'admin')
					->where('is_viewed', 'no')
					->update(['is_viewed' => 'yes']);

				case 'superadmin':
					Message::where('message_to_id', $teacher_id)
					->where('message_to_group', 'superadmin')
					->where('message_from_id', $guardian_id)
					->where('message_from_group', 'guardian')
					->where('is_viewed', 'no')
					->update(['is_viewed' => 'yes']);

			}
			return $this->getConversations($guardian_id, 'guardian', $teacher_id, $role);
		}

		public function apiPostSendMessage()
		{
			$controller = new MessageController;
			$input = Input::all();

			$result = $controller->apiValidateInput($input);

			if($result['status'] == 'error')
				return json_encode($result);

			$result = $controller->apiSendMessage($input);

			if ($result['status'] == 'success')
			{
				$result['data'] = json_decode($this->getConversations($input['message_to_id'], $input['message_to_group'], $input['message_from_id'], $input['message_from_group']), true);
				$result['data'] = $result['data']['data'];
			}

			return json_encode($result, JSON_PRETTY_PRINT);
		}

		/*
		 * Get related guardian
		 */
		public function getGuardianRelatedToClass($class_id, $section_id, $group)
		{
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
								->select(array('username', StudentRegistration::getTableName().'.id', 'student_name'))
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
								->select(array('username', 'guardian_name', 'student_name', Guardian::getTableName().'.id', Guardian::getTableName().'.photo'))
								->orderBy('student_name', 'ASC')
								->get();
					foreach ($data as $guardian)
					{
						if (strlen(trim($guardian->photo)))
						{
							$guardian->photo = Config::get('app.url').'app/modules/guardian/assets/images/' . $guardian->photo;
						}
						else
						{
							$guardian->photo = '';
						}
					}

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

				default: 
				$data = array();
			}
			
			return json_encode(array(
				'status'	=> 'success',
				'data'		=> $data
			), JSON_PRETTY_PRINT);
		}

		// get list of guardians with whom a teacher has had conversation
		public function getTeacherConversations($teacher_id, $role='admin')
		{
			$data = Message::
				where(
					function($query) use ($teacher_id, $role)
					{
						return $query->where('message_from_group', $role)
								->where('message_from_id', $teacher_id)
								->where('message_to_group', 'guardian');
					}
				)
				->orWhere(
					function($query) use ($teacher_id, $role)
					{
						return $query->where('message_to_group', $role)
									->where('message_to_id', $teacher_id)
									->where('message_from_group', 'guardian');
					}
				)
				->orderBy('id', 'DESC')
				->groupBy('message_to_group', 'message_to_id', 'message_from_group', 'message_from_id')
				->get();

			$guardian_ids = array_map(
				function($record) 
				{
					if ($record['message_to_group'] == 'guardian')
					{
						return $record['message_to_id'];
					}
					else
					{
						return $record['message_from_id'];
					}
				},
				$data->toArray()
			);
			
			$guardian_ids = array_unique($guardian_ids);

			$data = Guardian::whereIn('id', $guardian_ids)
				->select('id', 'guardian_name', 'photo')
				->get();

			foreach ($data as $guardian)
			{
				if (strlen(trim($guardian->photo)))
				{
					$guardian->photo = Config::get('app.url').'app/modules/guardian/assets/images/' . $guardian->photo;
				}
				else
				{
					$guardian->photo = '';
				}

				$guardian->unread_messages = (string)Message::where('message_from_id', $guardian->id)
					->where('message_from_group', 'guardian')
					->where('message_to_group', 'admin')
					->where('message_to_id', $teacher_id)
					->where('is_viewed', 'no')
					->count();
			}

			return json_encode(array(
				'status'=> 'success',
				'data'	=> $data
			));
			
		}

		/*
		/
		/
			This is for notifications
		/
		/
		*/
		public function getNotifications()
		{
			$data = SavePushNotifications::where('user_group', Input::get('user_group', ''))
									  ->where('user_id', Input::get('user_id', ''))
									  ->paginate(Input::get('paginate', 10));

			$return = array();
			foreach($data as $d)
			{
				$return[] = $d;
			}

			return json_encode(array('status' => 'success', 'data' => $return));
		}

		public function deleteOldNotifications()
		{
			$delete_before = Carbon::now()
				->subDays(DAYS_TO_KEEP_NOTIFICATIONS)
				->format('Y-m-d H:i:s');

			SavePushNotifications::where('created_at', '<=', $delete_before)
				->where('is_active', 'no')
				->delete();
		}

	/* */

	/*
	/
	/
		These are helper functions
	/
	/
	*/

	

	//done
	public function helperGetSessionIds()
	{
		$data = DB::table(AcademicSession::getTableName())
					->where('is_active', 'yes')
					->select(array('id', 'session_name', 'session_start_date_in_ad', 'session_start_date_in_bs', 'session_end_date_in_ad', 'session_end_date_in_bs', 'is_current'))
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));
	}

	//done
	public function helperGetClassIdsFromSessionId($session_id)
	{
		$data = DB::table(Classes::getTableName())
					->where('is_active', 'yes')
					->where('academic_session_id', $session_id)
					->select(array('id', 'class_name', 'class_code'))
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));
	}

	//done
	public function helperGetSectionIdsFromClassId($class_id)
	{
		$data = DB::table(ClassSection::getTableName())
					->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
					->select(array(Section::getTableName().'.id', Section::getTableName().'.section_code', Section::getTableName().'.section_name'))
					->where(ClassSection::getTableName().'.is_active', 'yes')
					->where('class_id', $class_id)
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));
	}

	public function helperGetExamIds($session_id = 0)
	{
		$session_id = $session_id ? $session_id : HelperController::getCurrentSession();
		$data = DB::table(ExamConfiguration::getTableName())
					->where('is_active', 'yes')
					->where('session_id', $session_id)
					->select(array('id', 'exam_name', 'exam_start_date_in_ad', 'exam_end_date_in_ad', 'exam_start_date_in_bs', 'exam_end_date_in_bs', 'remarks'))
					->orderBy('exam_start_date_in_ad', 'ASC' )
					->get();

		return json_encode(array('status' => 'success', 'data' => $data));
	}

	/**
	 * Frontend api
	 */

	// photo gallery
	public function getPhotoGallery() 
	{
		$categories = GalleryCategory::where('is_active', 'yes')
			->select('id', 'title', 'description')
			->get();

		foreach ($categories as $category)
		{
			$images = Gallery::where('category_id', $category->id)
				->where('is_active', 'yes')
				->select('id', 'title', 'description')
				->get();

			foreach ($images as $image)
			{
				$image->original_image_url = Config::get('app.url').
					'/app/modules/gallery/assets/images/original/'.
					$image->id;

				$image->thumbnail_url = Config::get('app.url').
					'/app/modules/gallery/assets/images/thumbnails/'.
					$image->id;
			
			}

			$category->images = $images;

		}

		return json_encode(
			[
			'status'	=> 'success',
			'data'		=> $categories
			], 
			JSON_PRETTY_PRINT
		);
		
	}

	// video gallery
	public function getVideoGallery() 
	{
		return json_encode(
			[
				'status'	=> 'success',
				'data'		=> VideoGalleryHelperController::getConfig()
			],
			JSON_PRETTY_PRINT
		);
	}

	// upcoming events
	public function getRecentUpcomingEvents()
	{
		$upcoming_events = Events::where('from_ad', '>=', date('Y-m-d'))
															->orderBy('from_ad', 'ASC')
															->take(5)
															->get()
															->toArray();

		$upcoming_events = array_map(function($event) {
			$event['from_ad'] = DateTime::createFromFormat(
				'Y-m-d H:i:s', $event['from_ad']
			)->format('d F Y g:i A');

			$event['to_ad'] = DateTime::createFromFormat(
				'Y-m-d H:i:s', $event['to_ad']
			)->format('d F Y g:i A');

			$event['from_bs'] = HelperController::formatNepaliDate(substr($event['from_bs'], 0, 10)) . ' ' .
				DateTime::createFromFormat(
					'H:i:s',
					substr($event['from_bs'], 11)
				)->format('g:i A');
				
			$event['to_bs'] = HelperController::formatNepaliDate(substr($event['to_bs'], 0, 10)) . ' ' .
				DateTime::createFromFormat(
					'H:i:s',
					substr($event['to_bs'], 11)
				)->format('g:i A');

			return $event;

		}, $upcoming_events);
		return json_encode(
			[
				'status'	=> 'success',
				'data'		=> $upcoming_events
			],
			JSON_PRETTY_PRINT
		);

	}

	// slideshow
	public function getSlideshow()
	{
		$slides = Slides::where('is_active','yes')
						->orderBy('slide_no')
						->select('title', 'text', 'link', 'id')
						->get()
						->toArray();

		$slides = array_map(
			function($slide) {
				$slide['image_url'] = Config::get('app.url').
					'/app/modules/slides/asset/images/'.
					$slide['id'] . '.jpg';
				return $slide;
			}, 
			$slides
		);

		return json_encode(
			[
				'status'	=> 'success',
				'data'		=> $slides
			],
			JSON_PRETTY_PRINT
		);
	}

	// about us
	public function getAboutUs()
	{
		return json_encode(
			[
				'status'	=> 'success',
				'data'		=> ArticlesHelper::getAppData()
			]
		);
	}

	// general downloads
	public function getGeneralDownloads()
	{
		$model = 'GeneralDownloads';
		$result = DB::table($model::getTableName())->join(
			DownloadManager::getTableName(), 
			DownloadManager::getTableName().'.id', '=', 
			$model::getTableName().'.download_id'
		);
		$result = $result->select(
			$model::getTableName().'.download_id', 
			$model::getTableName().'.created_at as uploaded_at', 
			DownloadManager::getTableName() . '.filename', 
			DownloadManager::getTableName() . '.google_file_id'
		);
		$result = $result->where(
			$model::getTableName().'.is_active', 
			'yes'			
		)->orderBy(
			$model::getTableName().'.created_at', 'DESC'
		)->get();

		$google_drive = new EasyDriveAPI2(Request::url());

		foreach($result as $key => $child) {
			// $result[$key]->download_link = URL::route(
			// 	'download-manager-backend-file-download', 
			// 	[$child->download_id, $child->google_file_id]
			// );

			$result[$key]->download_link = $google_drive->getDownloadLink($child->google_file_id);

			$result[$key]->uploaded_at = HelperController::dateTimePrettyConverter(
				$result[$key]->uploaded_at
			);

			unset($result[$key]->download_id);
			unset($result[$key]->google_file_id);
		}

		return json_encode(
			[
				'status'	=> 'success',
				'data'		=> $result
			],
			JSON_PRETTY_PRINT
		);
	}

	//get list of subjects
	public function getSubjectListForEnteringMarks($user_id, $role = 'admin')
	{
		$data = ExamMarksHelperController::apiListAllRelatedSubjects($user_id, $role);

		return $data;
	}

	//get marks entry form
	public function getMarksEntryForm($class_id, $section_id, $subject_id, $exam_id, $session_id = 0)
	{
		//{class_id}/{section_id}/{subject_id}/{exam_id}/{session_id?}
		$data = ExamMarksHelperController::getExamMarksList($class_id, $section_id, $subject_id, $exam_id, $session_id);

		return $data;
	}

	public function updateMarks()
	{
		/*
		{
	"session_id": "2",
	"exam_id": "3",
	"class_id": "11",
	"section_id": "1",
	"subject_id": ["26", "26", "26", "26", "26"],
	"marks": ["80", "60", "90", "70", "50"],
	"comments": ["", "", "", "", ""],
	"student_id": ["5003", "5001", "5004", "5002", "5000"],
	"default_class": "11",
	"default_section": "1",
	"default_subject": "26"
	}
		*/
		$data_to_store = Input::get('data_to_store', '');
		if(strlen($data_to_store))
		{
			$data_to_store = json_decode($data_to_store, true);
			$response = ExamMarksHelperController::apiPostUpdateMarks($data_to_store);
			return $response;	
		}
		else
		{
			$return = array();
			$return['status'] = 'error';
			$return['message'] = 'No data sent';
			return json_encode($return);
		}
	}

	public function registerImei()
	{
		try
		{
			$imei = Imei::firstOrCreate(array('username' => Input::get('username')));
			$imei->imei = Input::get('imei');
			$imei->save();	
			$status = 'success';
			$message = 'successfully registered';
		}catch(Exception $e)
		{
			$status = 'error';
			$message = $e->getMessage();
		}

		return json_encode(array('status' => $status, 'message' => $message));
		
	}

	public function getCurrentMonthBs()
	{
		$month = new DateConverter;
		return json_encode(array('month' => $month->getCurrentMonthBs()));
	}

	///////////// This is for superadmin //////////////

	public function getTeacherList($class_id, $section_id)
	{
		$section_table = Section::getTableName();
		$teacher_table = Teacher::getTableName();
		//$class_table = Classes::getTableName();
		$employee_table = Employee::getTableName();

		$teachers = DB::table($teacher_table)
						->join($section_table, $section_table.'.section_code', '=', $teacher_table.'.section_code')
						//->join($class_table, $class_table.'.id', '=', $teacher_table.'.class_id')
						->join($employee_table, $employee_table.'.id', '=', $teacher_table.'.teacher_id')
						->select($teacher_table.'.teacher_id', $employee_table.'.employee_name')
						->where($teacher_table.'.class_id', $class_id)
						->where($section_table.'.id', $section_id)
						//->orderBy($teacher_table.'.class_id', 'ASC')
						//->orderBy($teacher_table.'.section_code', 'ASC')
						->orderBy('employee_name', 'ASC')
						->get();

		return $teachers;
	}
	
	public function getPdr($session_id, $class_id, $section_id, $date)
	{

		$data = DB::table(Pdr::getTableName())
					->where('session_id', $session_id)
					->where('class_id', $class_id)
					->where('section_id', $section_id)
					->where('pdr_date', $date)
					->get();
					

		$return_data = [];

		foreach($data as $d)
		{
			$return_data = ['data' => json_decode($d->pdr_details), 'id' => $d->id];
		}

	
		return json_encode(['status' => 'success', 'data' => $return_data, 'message' => '']);
	}

	public function getCreatePdr()
	{
		$input = Input::all();

		$check_if_pdr_exists_for_given_date_class_and_section = Pdr::where('pdr_date', $input['date'])
		->where('session_id', $input['session_id'])
		->where('class_id', $input['class_id'])
		->where('section_id', $input['section_id'])
		->first();

		
		if($check_if_pdr_exists_for_given_date_class_and_section)
		{
			$data = [];
			$message = 'PDR already created for given date';
			$status = 'error';
		}
		else
		{
			$data = (new Pdr)->getCreateViewData($input);
			$message = '';	
			$status = 'success';
		}
		
		return ['status' => $status, 'data' => $data, 'message' => $message];

	}

	public function postCreatePdr()
	{
		$data = Input::all();
		$data['pdr_date'] = $data['date'];

		$data['subject_name'] = json_decode($data['subject_name'], true);
		$data['learning_achievement'] = json_decode($data['learning_achievement'], true);
		$data['comment'] = json_decode($data['comment'], true);
		$data['homework'] = json_decode($data['homework'], true);
		$data['class_activity'] = json_decode($data['class_activity'], true);
		$data['chapter'] = json_decode($data['chapter'], true);


		/*echo '<pre>';
		print_r($data);
		die();
*/
		$result = (new PdrController)->apiPostCreateView($data);

		return $result;
	}

	public function postPdrFeedback($pdr_id)
	{
		$feedback = Input::get('feedback', '');
		$student_id = Input::get('student_id', 0);
		$guardian_id = Input::get('guardian_id', 0);

		if($student_id == 0 || $guardian_id == 0 || strlen(trim($feedback)) == 0)
		{
			$status = 'error';
			$message = 'All Fields are Required';
		}
		else
		{
			try
			{
				$check_if_feedback_exists = PdrFeedback::where('student_id', $student_id)
					->where('guardian_id', $guardian_id)
					->where('pdr_id', $pdr_id)
					->first();


				if($check_if_feedback_exists)
				{
					PdrFeedback::where('student_id', $student_id)
					->where('guardian_id', $guardian_id)
					->where('pdr_id', $pdr_id)
					->update(['feedback' => $feedback]);

					$status = 'success';
					$message = 'Feedback Succesfully updated';
				}
				else
				{
					PdrFeedback::create(['student_id' => $student_id, 'guardian_id' => $guardian_id, 'feedback' => $feedback, 'created_by' => 'Api', 'updated_by' => 'Api', 'pdr_id' => $pdr_id]);

					$status = 'success';
					$message = 'Feedback successfully submited';	
				}
				
			}
			catch(Exception $e)
			{
				$status = 'error';
				$message = $e->getMessage();
			}
		}

		return ['status' => $status, 'message' =>$message];
		

	}
	
	public function getFeePrintList($session_id, $class_id,  $section_id, $student_id)
	{
		$return_data = (new BillingController)->getFeePrintListData($session_id, $class_id,  $section_id, $student_id);

		return ['status' => 'success', 'data' => $return_data, 'message' => ''];
	}
}