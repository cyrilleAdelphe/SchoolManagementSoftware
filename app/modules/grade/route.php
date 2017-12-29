<?php
	
	define('GRADE_CONFIG_FILE', app_path().'/modules/grade/config/config.json');
	define('NO_OF_GRADES', 9);

	Route::group(array('prefix' => 'grade', 'before' => 'reg-superadmin-admin'), function(){
		Route::get('/',[
			'as'	=>	'grade-update-get',
			'uses'	=>	'GradeController@getUpdate']);

		Route::group(array('before' => 'csrf'), function() {
			Route::post('/',[
				'as'	=>	'grade-update-post',
				'uses'	=>	'GradeController@postUpdate']);
		});
	});
