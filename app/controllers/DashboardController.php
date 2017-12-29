<?php

class DashboardController
{
	public static function dashboardGetUnreadMessages($group, $to_id)
	{
		//get id of current superadmin user
		//$to_id = Auth::superadmin()->user()->id;
		return DB::table('messages')
					->select(array('id', 'message', 'message_from_group', 'message_from_id', 'created_at', 'created_by'))
					->where('is_viewed', 'no')
					->where('message_to_group', $group)
					->where('message_to_id', $to_id)
					->where('is_active', 'yes')
					->get();
	}

	public static function dashboardGetTeachers()
	{
		return DB::table(EmployeePosition::getTableName())
					->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
					->where('group_name', 'Teacher')
					->count();

	}

	public static function dashboardGetTotalStudents()
	{
		return DB::table(Student::getTableName())
				  ->where('current_session_id', HelperController::getCurrentSession())
				  ->where('is_active', 'yes')
				  ->count();
	}

	public static function dashboardGetUpcomingEvents($event_group = 'all', $no_of_events = 10, $current_date = '')
	{	
		$current_date = strlen($current_date) ? $current_date : Carbon\Carbon::now()->format('Y-m-d');
		$data = DB::table(Events::getTableName())
				->select(array('id', 'title', 'from_ad', 'from_bs', 'to_ad', 'to_bs'))
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

		return $data->take($no_of_events)
				->get();
	}

	public static function dashboardGetNotice()
	{
		return json_decode(File::get(app_path().'/modules/notice/notice.json'));
	}
}