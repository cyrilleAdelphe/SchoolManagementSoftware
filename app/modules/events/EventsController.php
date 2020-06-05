<?php

class EventsController extends BaseController
{
	protected $view = 'events.views.';

	protected $model_name = 'Events';

	protected $module_name = 'events';

	public $role;

	public $current_user;

	
	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'title',
										'alias'			=> 'Event Title'
									),

									array
									(
										'column_name' 	=> 'event_code',
										'alias'			=> 'Event Code'
									),

									array
									(
										'column_name' 	=> 'event_type',
										'alias'			=> 'Event Type'
									),

									array
									(
										'column_name' 	=> 'for_students',
										'alias'			=> 'For'
									),

									array
									(
										'column_name' 	=> 'from_bs',
										'alias'			=> 'Start Date'
									),

									array
									(
										'column_name' 	=> 'to_bs',
										'alias'			=> 'End Date'
									),

									
								 );

	public function preProcessCreateEdit()
	{
		if(Input::get('for_students')==='yes' || Input::get('for_teachers')==='yes' || Input::get('for_management_staff')==='yes' || Input::get('for_parents')==='yes' || Input::get('for_all')==='yes')
		{
			Input::merge(array('for'=>'ok'));
		}
		else
		{
			Input::merge(array('for'=>''));
		}

		if(Input::get('for_all')==='yes')
		{
			Input::merge(array('for_students'=>'yes','for_teachers'=>'yes','for_management_staff'=>'yes','for_parents'=>'yes','for_all'=>'no'));
		}

		$create_rule = (new Events)->createRule;
		$date_validator = Validator::make(Input::all(),
											[
												'date'	=> $create_rule['date'],
										]);
		if($date_validator->passes())
		{
			$date_converter = new DateConverter;

			$from_to = explode('-', Input::get('date'));
			$from = trim($from_to[0]);
			$from = DateTime::createFromFormat('m/d/Y g:i A', $from)->format('Y-m-d H:i:s');
			$to = trim($from_to[1]);
			$to = DateTime::createFromFormat('m/d/Y g:i A', $to)->format('Y-m-d H:i:s');

			$from_bs = $date_converter->ad2bs(substr($from, 0,10)) . substr($from,10);
			$to_bs 	= $date_converter->ad2bs(substr($to, 0,10)) . substr($to,10);

			Input::merge(array(
								'from_ad'	=> $from,
								'to_ad'		=> $to,
								'from_bs'	=> $from_bs,
								'to_bs'		=> $to_bs
							));

		}
	}

	public function postCreateView()
	{
	AccessController::allowedOrNot($this->module_name, 'can_create');
		$event_code = '';
		do {
			$event_code = str_pad(rand(0, pow(10, DIGITS_IN_EVENT_CODE)-1), DIGITS_IN_EVENT_CODE, '0', STR_PAD_LEFT);
		} while (Events::where('event_code', $event_code)->first());
		Input::merge(array('event_code' => $event_code));

		$this->preProcessCreateEdit();
		$result = $this->validateInput(Input::all());
		if (Input::get('pushNotification') === 'yes' && $result['status'] != 'error')
		{
			$from_time = DateTime::createFromFormat('Y-m-d H:i:s', Input::get('from_ad'))->format('g:i A');
			$to_time = DateTime::createFromFormat('Y-m-d H:i:s', Input::get('to_ad'))->format('g:i A');
			$from = (CALENDAR == 'BS') ? HelperController::formatNepaliDate(substr(Input::get('from_bs'), 0, 10)) . ' ' . $from_time : DateTime::createFromFormat('Y-m-d H:i:s', Input::get('from_ad'))->format('d F Y, g:i A');
			$to = (CALENDAR == 'BS') ? HelperController::formatNepaliDate(substr(Input::get('to_bs'), 0, 10)) . ' ' . $to_time : DateTime::createFromFormat('Y-m-d H:i:s', Input::get('to_ad'))->format('d F Y, g:i A');
			$msg = 'event # ' .
									'Event: ' . Input::get('title') . "\n" .
									'Venue: ' . Input::get('venue') . "\n" . 
									'Type: ' . HelperController::underscoreToSpace(Input::get('event_type')) . "\n" .
									'From: ' . $from . "\n" .
									'To: ' . $to . "\n";

			if(Input::get('description'))
			{
				$msg .= 'Details: ' . Input::get('description') ;
			}
			
			if (Input::get('for_students') === 'yes')
			{
				$user_ids = DB::table(Users::getTableName())
								->where('role', 'student')
								->lists('user_details_id');

				$gcm_ids = DB::table(PushNotifications::getTableName())
								->where('user_group', 'student')
								->whereIn('user_id', $user_ids)
								->lists('gcm_id');

						
				if(count($gcm_ids))
				{
					(new GcmController)->send($gcm_ids, $msg, $user_ids, 'student');
				}
			}

			if (Input::get('for_parents') === 'yes')
			{
				$user_ids = Users::where('role', 'guardian')
								->lists('user_details_id');

				$gcm_ids = PushNotifications::whereIn('user_id', $user_ids)
											->where('user_group', 'guardian')
											->lists('gcm_id');

				if(count($user_ids))
				{
					(new GcmController)->send($gcm_ids, $msg, $user_ids, 'guardian');
				}
			}
		}
		
		if ($result['status'] == 'error')
		{
			$error = '<ul>';
			foreach ($result['data']->all('<li>:message</li>') as $message)
			{
			    $error .= $message;
			}
			$error .= '</ul>';
			Session::flash('error-msg', $error);
			return Redirect::back()
										->withErrors($result['data'])
										->withInput();
		}

		return parent::postCreateView();
	}

	public function postEditView($id)
	{
	AccessController::allowedOrNot($this->module_name, 'can_edit');
		$this->preProcessCreateEdit();
		return parent::postEditView($id);
	}

	public function remind($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_send_reminder');
		$event = Events::find($id);
		if(!$event)
		{
			Session::flash('error-msg', 'Invalid message id');
			return Redirect::back();
		}
		else
		{
			$from_time = DateTime::createFromFormat('Y-m-d H:i:s', $event->from_ad)->format('g:i A');
			$to_time = DateTime::createFromFormat('Y-m-d H:i:s', $event->to_ad)->format('g:i A');
			$from = (CALENDAR == 'BS') ? HelperController::formatNepaliDate(substr($event->from_bs, 0, 10)) . ' ' . $from_time : DateTime::createFromFormat('Y-m-d H:i:s', $event->from_ad)->format('d F Y, g:i A');
			$to = (CALENDAR == 'BS') ? HelperController::formatNepaliDate(substr($event->to_bs, 0, 10)) . ' ' . $to_time : DateTime::createFromFormat('Y-m-d H:i:s', $event->to_ad)->format('d F Y, g:i A');
			$msg = 'event # ' .
									'Event: ' . $event->title . "\n" .
									'Venue: ' . $event->venue . "\n" . 
									'Type: ' . HelperController::underscoreToSpace($event->event_type) . "\n" .
									'From: ' . $from . "\n" .
									'To: ' . $to . "\n";

			if($event->description)
			{
				$msg .= 'Details: ' . $event->description ;
			}
			$return = [];
			if ($event->for_students === 'yes')
			{
				$user_ids = DB::table(Users::getTableName())
								->where('role', 'student')
								->lists('user_details_id');

				$gcm_ids = PushNotifications::where('user_group', 'student')
											->whereIn('user_id', $user_ids)
											->lists('gcm_id');
																		
				if(count($gcm_ids))
				{
					$return[] = (new GcmController)->send($gcm_ids, $msg, $user_ids, 'student');
				}
			}

			if ($event->for_parents === 'yes')
			{
				$user_ids = DB::table(Users::getTableName())
								->where('role', 'guardian')
								->lists('user_details_id');

				$gcm_ids = PushNotifications::where('user_group', 'guardian')
											->whereIn('user_id', $user_ids)
											->lists('gcm_id');
				
				if(count($user_ids))
				{
					$return[] = (new GcmController)->send($gcm_ids, $msg, $user_ids, 'guardian');
				}
			}

			if ($event->for_teachers === 'yes')
			{
				$user_ids = DB::table(Teacher::getTableName())
								//->where('role', 'guardian')
								->distinct()
								->lists('teacher_id');

				$gcm_ids = PushNotifications::where('user_group', 'admin')
											->whereIn('user_id', $user_ids)
											->lists('gcm_id');
				
				if(count($user_ids))
				{
					$return[] = (new GcmController)->send($gcm_ids, $msg, $user_ids, 'admin');
				}
			}

			if ($event->for_management_staff === 'yes')
			{
				$user_ids = DB::table(Employee::getTableName())
								//->where('role', 'guardian')
								//->distinct()
								->lists('id');

				$gcm_ids = PushNotifications::where('user_group', 'admin')
											->whereIn('user_id', $user_ids)
											->lists('gcm_id');
				
				if(count($user_ids))
				{
					$return[] = (new GcmController)->send($gcm_ids, $msg, $user_ids, 'admin');
				}
			}
			
			Session::flash('success-msg', 'Reminder sent');
			return Redirect::back();			
		}
	}

	public function getCalendar()
	{
	AccessController::allowedOrNot($this->module_name, 'can_view');
		$events_array = EventsHelper::getAllEvents();
		
		return View::make($this->view . 'calendar')
					->with('events',json_encode($events_array))
					->with('role',$this->role);
	}

	public function getCalendarFrontend()
	{
		$events_array = EventsHelper::getAllEvents();

		return View::make($this->view . 'calendar-frontend')
							->with('events', json_encode($events_array));
	}

}
