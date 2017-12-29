<?php

class SmsMainController extends Controller
{
	/******************* this is master function ******************/
	public static function sendSMS($phone_numbers, $message, $sms_from, $sms_token) //delete this in client side
	{
		/*$phone_numbers = is_array($phone_numbers) ? $phone_numbers : (array) $phone_numbers;
		
		$args = http_build_query(array(
	        'token' => $sms_token,
	        'from'  => $sms_from,
	        'to'    => implode(',', $phone_numbers),
	        'text'  => $message
	       ));
	
		    $url = "http://api.sparrowsms.com/v2/sms/";
	
		    # Make the call using API.
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS,$args);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		    // Response
		    $response = curl_exec($ch);
		    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		    curl_close($ch);*/
		    
		    $response = '{"count": 1, "response_code": 200, "response": "1 mesages has been queued for delivery", "message_id": 9906786}';
		    $status_code = 200;

			$logFile = 'log-sms-request'.php_sapi_name().'.txt';

			Log::useDailyFiles(storage_path().'/logs/'.$logFile);
			Log::info('Log message', array('server_response' => $response));
			//{"count": 1, "response_code": 200, "response": "1 mesages has been queued for delivery", "message_id": 9906584}
			
			if($status_code == 200)
			{
				$response = json_decode($response, true);
				//print_r($response);
				$message = $response['response'];
				$id = $response['message_id'];
				
			}
			else
			{
				$message = 'Problem with Server';
				$id = 0;
			}

			$status_code = $status_code == (int) 200 ? 'success' : 'error';
			
		    return json_encode(array('status' => $status_code, 'message' => $message, 'message_id' => $id));
		   
	    	//string(111) "{"count": 1, "response_code": 200, "response": "1 mesages has been queued for delivery", "message_id": 9221153}" int(200)
		}
		/********************************************************************************/

	public static function makeCurlRequest($url, $arguments = array(), $get_or_post = 'get')
	{
		$args = '';
		if(count($arguments))
			$args = http_build_query($arguments);
	
		    # Make the call using API.
		    
	    if(strtolower($get_or_post) == 'post')
	    {
	    	$ch = curl_init();
	    	curl_setopt($ch, CURLOPT_URL, $url);

	    	curl_setopt($ch, CURLOPT_POST, 1);
	    	curl_setopt($ch, CURLOPT_POSTFIELDS,$args);	
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		    // Response
		    $response = curl_exec($ch);
		    //$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		    curl_close($ch);
	    }
	    else
	    {
	    	$response = file_get_contents($url.'?'.$args);

	    }
			    
		return $response;
	    	//var_dump($response);
	    	//var_dump($status_code);
	    	//string(111) "{"count": 1, "response_code": 200, "response": "1 mesages has been queued for delivery", "message_id": 9221153}" int(200)
	}
}