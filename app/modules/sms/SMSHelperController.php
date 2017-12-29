<?php

class SMSHelperController
{
	public static function getOnlyMessage($message)
	{
		$message = explode('#', $message);

		$message = strtolower(trim($message[0])) == 'message' ? trim(substr($message[1], strrpos($message[1], ':') + 1)) : trim($message[1]);
		$message = $message.' - ' . json_decode(File::get(GENERAL_SETTINGS))->short_school_name;
		return $message;
	}

	public static function sendSMS($phone_nos, $message) //
	{

		//extract list of numbers and messages
	}

	public static function updateSmsStatus($message_group_id, $status, $phone_numbers)
	{
		$success_status = FINAL_SMS_SUCCESS_STATUS;
		$success_status = explode(',', $success_status);

		$update_array = array('sms_status' => $status);
		if(in_array($status, $success_status) )
		{
			$update_array['is_active'] = 'no';
		}

		//dd($update_array);
		
		DB::table(SMS::getTableName())
			->whereIn('phone_no', $phone_numbers)
			->where('message_group_id', $message_group_id)
			->update($update_array);
	}
}