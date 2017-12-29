<?php

use Carbon\Carbon;

class MessageHelperController
{
	public function getSentMessages($start_date, $end_date, $to_group, $from_group, $from_id)
	{
		
		if($to_group == 'superadmin')
			$table_1 = SuperAdmin::getTableName();
		elseif($to_group == 'admin')
			$table_1 = Admin::getTableName();
		else
			$table_1 = Users::getTableName();

		//$table_1 = SuperAdmin::getTableName();
		$table_2 = Message::getTableName();
		
		if($to_group == 'superadmin')
		{
			$data = DB::table(Message::getTableName())
			->join($table_1,  $table_1.'.id', '=', $table_2.'.message_from_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' = "no" then 1 else null end) as new_message_counts,'.Config::get('database.connections.mysql.prefix').$table_1.'.id as sender_id, '.Config::get('database.connections.mysql.prefix').$table_1.'.name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity, username as sender_username'));	
		}
		elseif($to_group == 'admin')
		{
			$data = DB::table(Message::getTableName())
			->join($table_1,  $table_1.'.admin_details_id', '=', $table_2.'.message_from_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' > "no" then 1 else null end) as new_message_counts,'.Config::get('database.connections.mysql.prefix').$table_1.'.id as sender_id, '.Config::get('database.connections.mysql.prefix').$table_1.'.name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity, username as sender_username'));
		}
		else
		{
			$data = DB::table(Message::getTableName())
			->join($table_1,  $table_1.'.user_details_id', '=', $table_2.'.message_from_id')
			  ->select(DB::raw('count(case when '.Config::get('database.connections.mysql.prefix').$table_2.'.is_viewed'.' > "no" then 1 else null end) as new_message_counts,'.Config::get('database.connections.mysql.prefix').$table_1.'.id as sender_id, '.Config::get('database.connections.mysql.prefix').$table_1.'.name as sender_name, max('.Config::get('database.connections.mysql.prefix').$table_2.'.created_at) as last_activity, username as sender_username'));
		}
		
		
			$data = $data->where('message_to_group', $to_group);
			$data = $data->where('message_from_id', $from_id);

			$data = $data->where($table_2.'.created_at', '>=', $start_date)
			->where($table_2.'.created_at', '<=', $end_date)
			->where('message_from_group', 'superadmin')
			->groupBy('message_to_id')->orderBy($table_2.'.created_at', 'DESC')
			//->get();
			->paginate(Input::get('paginate', 10));

	return $data;

	}

	public function apiGetMessageList($sender_group, $sender_id)
	{
		$to_group = Input::get('message_from', 'guardian');
		$date_range = Input::get('calendar_range', '');
		
		if(strlen($date_range) == 0)
		{
			$start_date = Carbon::today()->subDays(7)->toDateTimeString();
			$end_date = Carbon::today()->toDateTimeString();
		}
		else
		{
			$date_range = explode(' - ', $date_range);
			$start_date = Carbon::createFromFormat('F j, Y', trim($date_range[0]))->format('Y-m-d');
			
			$end_date = Carbon::createFromFormat('F j, Y', trim($date_range[1]))->format('Y-m-d H:i:s');
			
		}

		$data = $this->getSentMessages($start_date, $end_date, $to_group, $sender_group, $sender_id);

		return $data;
	}
}