<?php
// these constants are index in the csv file where student_id, attendence_status and comments are stored
// each row is (id,status,comment) tuple
define('ID',0);
define('ATTENDANCE_STATUS',1);
define('ATTENDANCE_COMMENT',2);

Route::group(array('prefix' => 'attendance', 'before'=>'reg-superadmin'), function()
{
	Route::get('/create',
		[
			'as'	=> 'attendance-create-get',
			'uses'	=>	'AttendanceController@getCreate'
		]
	);

	Route::get('/view-student',
		[
			'as'	=> 'attendance-view-student-get',
			'uses'	=> 'AttendanceController@getViewStudent'
		]
	);

	Route::get('/view-class-section-history',
		[
			'as'	=> 'attendance-view-class-section-history',
			'uses'	=> 'AttendanceController@getViewHistoryClassSection'
		]
	);

	Route::get('/view-student-history/{start_date}/{end_date}/{student_id}',
		[
			'as'	=> 'attendance-view-student-history',
			'uses'	=> 'AttendanceController@getViewHistoryStudent'
		]
	);

	Route::group(array('before'=>'csrf'),function(){
		Route::post('/create',
				[
					'as'	=> 'attendance-create-post',
					'uses'	=> 'AttendanceController@postCreate'
				]
			);

		Route::get('/view-student-post',
			[
				'as'	=> 'attendance-view-student-post',
				'uses'	=> 'AttendanceController@postViewStudent'
			]
		);

		Route::post('/delete-attendance-file/{filename}',
			[
				'as'	=> 'attendance-delete-attendance-file-post',
				'uses'	=> 'AttendanceController@postDeleteAttendanceFile'
			]
		);
	});

	Route::get('/ajax-get-class-section',
		[
			'as' 	=> 'ajax-attendance-get-class-section',
			'uses'	=> 'AttendanceController@ajaxGetClassSection'
		]
	);

	Route::post('/ajax-get-students',[
			'as' 	=> 'ajax-attendance-get-students-post',
			'uses'	=> 'AttendanceController@ajaxPostStudents'
		]
	);

	Route::post('/ajax-get-attendance-form',[
			'as' 	=> 'ajax-attendance-get-attendance-form-post',
			'uses'	=> 'AttendanceController@ajaxPostAttendanceForm'
		]
	);

	Route::get('/ajax-get-class-section-history',[
			'as' 	=> 'ajax-get-class-section-history',
			'uses'	=> 'AttendanceController@ajaxGetViewHistoryClassSection'
		]
	);

	//ajaxGetViewHistoryClassSection

});
