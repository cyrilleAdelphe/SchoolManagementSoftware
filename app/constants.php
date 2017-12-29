<?php
$base_url = asset('/');
$base_url = implode('/', array_slice(explode('/', $base_url), 0, -1));

define('BASE_URL', $base_url); 
define('VENDOR_PATH', $base_url.'/vendor/');
define('DOWNLOAD_MANAGER_FOLDER','root');
define('ASSIGNMENT_CONFIG', app_path().'/modules/assignments/config/assignment_config.json');
define('ATTENDANCE_RECORD_LOCATION',app_path().'/modules/attendance/assets/attendance-records/');
//define("GOOGLE_GCM_URL", "https://android.googleapis.com/gcm/send");
define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
define('CALENDAR', 'BS');

define('DIGITS_IN_USERNAME', 4);
define('STUDENT_PREFIX_IN_USERNAME', 's');
define('GUARDIAN_PREFIX_IN_USERNAME', 'p');
define('EMPLOYEE_PREFIX_IN_USERNAME', 'e');

define('MAIN_SERVER_URL', 'http://sajiloschoolmanager.com/public/');
//define('MAIN_SERVER_URL', 'http://localhost/esms-demo/');
define('FINAL_SMS_STATUS', 'accepted,delivered,failed');
define('FINAL_SMS_SUCCESS_STATUS', 'accepted,delivered');

///////// billing-cancel-v1-changes /////////
$const_billing_types = [
					'credit' => ['credit', 'invoice', 'cancel_credit'],
					'debit'	=>	['discount', 'advance', 'debit_note','cash', 'cheque', 'bank_deposit', 'prev_balance', 'credit_note', 'cancel_debit']
				];

class SsmConstants
{
	public static $const_billing_types = [
					'credit' => ['credit', 'invoice', 'cancel_credit'],
					'debit'	=>	['discount', 'advance', 'debit_note', 'cash', 'cheque', 'bank_deposit', 'prev_balance', 'credit_note', 'cancel_debit']
				];
}
///////// billing-cancel-v1-changes /////////