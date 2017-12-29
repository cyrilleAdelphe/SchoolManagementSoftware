<?php

	Route::group(array('prefix' => 'exam-marks', 'before' => 'reg-superadmin'), function(){

		Route::get('/',
				['as'	=>	'exam-marks-update-get',
				 'uses'	=>	'ExamMarksController@getUpdate']);

		Route::group(array('before'=>'csrf'),function() {

			Route::post('/',
					['as'	=>	'exam-marks-update-post',
					 'uses'	=>	'ExamMarksController@postUpdate']);
		});
	});