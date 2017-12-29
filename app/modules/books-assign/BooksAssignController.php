<?php
define('NO_OF_BOOK_TITLE_INPUT', 4);

class BooksAssignController extends BaseController
{
	protected $view = 'books-assign.views.';

	protected $model_name = 'BooksAssigned';

	protected $module_name = 'books-assign';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'book_copy_id',
										'alias'			=> 'Title'
									),
									array
									(
										'column_name' 	=> 'username',
										'alias'			=> 'Username'
									),
									array
									(
										'column_name' 	=> 'assigned_date',
										'alias'			=> 'Assigned'
									),
									array
									(
										'column_name' 	=> 'returned_date',
										'alias'			=> 'Status'
									),
									array
									(
										'column_name'	=> 'due_date',
										'alias'			=> 'Due (in days)'
									)
									
								);

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		if(CALENDAR == 'BS')
		{
			$date = HelperController::getCurrentNepaliDate();
		}
		else
		{
			$date = date('d/m/Y');
		}

		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('actionButtons', $this->getActionButtons())
					->with('date', $date);
	}

		public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		/**
		 * The book_copy_id input is broken down to individual book IDs and stored separately
		 */


		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		

		// the visible student ID is username!!
		

		$result = $this->validateInput($data);


		//do validation here
		if($data['find_group'] == 'student')
		{
			$data['student_id'] = HelperController::getStudentIdFromUsername($data['username']);
		}
		elseif($data['find_group'] == 'admin')
		{
			$data['student_id'] = HelperController::getEmployeeIdfromUsername($data['username']);
		}
		else
		{
			$data['student_id'] = HelperController::getSuperadminIdfromUsername($data['username']);
		}

		if($data['student_id'] == 0)
		{
			Session::flash('error-msg', 'Not a valid username');
			return Redirect::back();
		}



		$book_ids_array = array();
		
		// if we know that we have a valid non-empty string of book_ids, check if each exists and is available
		if(!empty($data['book_ids']) && ($result['status']!='error' || !$result['data']->has('book_ids')) )
 		{

 			$book_ids_array = array_unique(explode(',', $data['book_ids']));
 			$result_book_ids = BookCopiesHelper::validateForAssignment($book_ids_array); // validates if each id exists/*
 			if($result_book_ids['status']=='error')
 			{

 				$result['status'] = 'error';
 				if(empty($result['data'])) $result['data'] = new Illuminate\Support\MessageBag;

 				if($result_book_ids['data']->has('unique_book_ids'))
 				{

 					$result['data']->getMessageBag()->add('book_ids',$result_book_ids['data']->first('unique_book_ids'));

 				}
 				if($result_book_ids['data']->has('unavailable_book_ids'))
 				{
 					$result['data']->getMessageBag()->add('book_ids',$result_book_ids['data']->first('unavailable_book_ids'));
 				}
 				
 			}
 		}

 		if (CALENDAR == 'BS' && !$result['data'])
		{
			// checking if the BS date actually exists
			$date_array = explode('/', Input::get('assigned_date'));
			$date = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
			$date = (new DateConverter)->bs2ad($date);
			if (!$date)
			{
				$result['status'] = 'error';
				if(empty($result['data'])) $result['data'] = new Illuminate\Support\MessageBag;
				$result['data']->getMessageBag()->add('assigned_date', 'Invalid Date');
			}
		}

 		
 		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		if(CALENDAR == 'BS')
		{
			$date_array = explode('/', Input::get('assigned_date'));
			$date = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
			$data['assigned_date'] = (new DateConverter)->bs2ad($date);
		}
		else
		{
			$data['assigned_date'] = DateTime::createFromFormat('d/m/Y', $data['assigned_date'])->format('Y-m-d');
		}

		$assigned_date = date($data['assigned_date']);


		try
		{
			DB::connection()->getPdo()->beginTransaction();


			foreach($book_ids_array as $book_id)
			{
			
				$book_data = $data;			

				$books_id = BookCopies::where('book_id',$book_id)
											->first()['books_id'];


				
				$max_holding_days = Books::find($books_id)['max_holding_days'];
				$book_data['book_copy_id'] = $book_id;
				$book_data['books_id']	= $books_id;
				$today = $assigned_date; // Or can put $today = date ("Y-m-d");

				$book_data['due_date'] = date ("Y-m-d", strtotime ($today . "+".$max_holding_days."days"));

				
				/*$book_data['due_date'] = $assigned_date->add(new DateInterval('P'.$max_holding_days.'D'))->format('Y-m-d');*/

				$book_data['related_group'] = $data['find_group'];
				$id = $this->storeInDatabase($book_data);
			}

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function sendNotification($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_send_push_notification');
		$d = BooksAssigned::find($id);
		if(!$d)
		{
			Session::flash('error-msg', 'Invalid book record');
			return Redirect::back();
		}
		$due_days = BooksAssignHelper::getDueDays($d->id);
		if (CALENDAR == 'BS')
		{
			$due_date = HelperController::formatNepaliDate((new DateConverter)->ad2bs($d->due_date));
		}
		else
		{
			$due_date = DateTime::createFromFormat('Y-m-d', $d->due_date)->format('d F Y');
		}

		$msg = 'library # '.
						'Book ' . Books::find($d->books_id)->title . ' due in ' . $due_days . ' days (' . $due_date .')';
		
		$gcm_ids = DB::table(PushNotifications::getTableName())
						->where('user_group', $d->related_group)
						->where('user_id', $d->student_id)
						->lists('gcm_id');

		//$gcm_ids = 'fW09A132Vec:APA91bGV8-rIjCRsE6NvWiU88QfOTIZadXmZEkj4RL8wDvTywLDrAJfLZ3WQJ5HUd8EKoSYSFb79adOrR2l_USWjjXvinELsDmh67ez2-qYOsd-PWFf64aWitRMMW7J6BWR4mC1P1CTe';
		if(count($d->student_id))
		{
			$result = (new GcmController)->send($gcm_ids, $msg, $d->student_id, $d->related_group);
		}

		if($d->related_group == 'student')
		{
			$parent_user_ids = StudentGuardianRelation::where(
				'student_id', $d->student_id
			)->lists('guardian_id');

			if (count($parent_user_ids))
			{
				$parent_gcm_ids = PushNotifications::whereIn(
					'user_id', $parent_user_ids
				)->where(
					'user_group', 'guardian'
				)->lists('gcm_id');
				
				$msg = 'library # '.
					'Book ' . Books::find($d->books_id)->title . 
					' borrowed by ' . StudentRegistration::where('id', $d->student_id)->pluck('student_name') .
					' due in ' . $due_days . ' days (' . $due_date .')';
				
				(new GcmController)->send(
					$parent_gcm_ids, 
					$msg,
					$parent_user_ids,
					'guardian'
				);
			}	
		}
		
		
		Session::flash('success-msg', 'notification_sent');
		// dd($gcm_ids);
		return Redirect::back();
	}

	public function getReturnView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		if(CALENDAR == 'BS')
		{
			$date = HelperController::getCurrentNepaliDate();
		}
		else
		{
			$date = date('d/m/Y');
		}

		return View::make($this->view.'return')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('actionButtons', $this->getActionButtons())
					->with('date', $date);
	}

	public function postReturnView($id) //id of book_assigned_table
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		/**
		 * The book_copy_id input is broken down to individual book IDs and stored separately
		 */
		try
		{
			$record = BooksAssigned::where('id', $id)->first();
			if($record)
			{
				if(!is_null($record->returned_date))	
				{
					Session::flash('error-msg', 'Book already returned');
				}
				else
				{
					$record->returned_date = date('Y-m-d');
					$record->save();
					Session::flash('success-msg', 'Book successfully returned');
				}
			}
			else
			{
				Session::flash('error-msg', 'Record does not exist');
			}
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
			Session::flash('error-msg', $e->getMessage());
		}
		
		return Redirect::back();
	}
}
?>