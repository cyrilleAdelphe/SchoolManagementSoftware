<?php

Route::group(['prefix' => 'sms'], function() {
	Route::post('update-sms-status',
				['as'	=>	'update-sms-status',
				'uses'	=>	'SMSController@updateSmsStatus']);

	Route::group(['before' => 'reg-superadmin-admin'], function() {
		Route::get('/list', [
			'as'	=> 'sms-list',
			'uses'=> 'SMSController@getListView'
		]);

		Route::get('/view/{message_group_id}', [
			'as'	=> 'sms-view',
			'uses'=> 'SMSController@getViewView'
		]);

		Route::group(['before' => 'csrf'], function() {
			Route::post('send-sms', [
				'as'	=> 'sms-send-post',
				'uses'=> 'SMSController@postSendSMS'
			]);
		});

		Route::get('send-sms-test', function() {
			$args = http_build_query(array(
        'token' => 'Akrho7civkzrAWuRmFKd',
        'from'  => 'Demo',
        'to'    => '9823088784',
        'text'  => 'Sparrow SMS Test'
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
	    curl_close($ch);

    	var_dump($response);
    	var_dump($status_code);
    	//string(111) "{"count": 1, "response_code": 200, "response": "1 mesages has been queued for delivery", "message_id": 9221153}" int(200)
		});
	});
});