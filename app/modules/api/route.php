<?php

Route::get('get-all-lesson-plans/{user_id}/{subject_id}/{class_id}', [
	'as' => 'get-all-lesson-plans',
	'uses' => 'ApiController@getAllLessonPlans']);

Route::group(array('prefix' => 'api'), function()
{
	//Route::group(array('before' => 'api'), function()
	//{
	
	Route::get('new-attendance-student-history', [
		'as' => 'new-attendance-student-history', 
		'uses' => 'ApiController@getNewAttendanceStudentHistory' 		
	]);
		Route::get('progress-report-exam',[
		'as'	=> 'progress-api-report',
		'uses'	=> 'ApiController@getExamProgressReport'
	]);	
		
		Route::get('/exam-details', [
		'as'	=> 'exam-details-api',
		'uses'	=> 'ApiController@getExamDetails'
	]);
	
		Route::get('/teacher-class-section-for-exam', [
		'as'	=> 'teacher-class-section-for-exam',
		'uses'	=> 'ApiController@getTeacherClassSectionforExam'
	
	]);
	
	
	Route::get('/teacher-related-class-section/',[
		'as' 	=> 'teacher-assigned-class-section',
		'uses'	=> 'ApiController@getTeacherAssignedClassSection'	
	
	]);
	
	
	Route::get('/fee-print-student-view', [
			
			'as'	=> 'billing-api-fee-print-student', 
			'uses'	=> 'ApiController@apiGetListViewFeePrintStudent'

			]);
	
	Route::get('/statement-view',[

		'as'	=> 'api-statement-view',
		'uses'	=> 'ApiController@getStudentStatement'

	]);
	

	Route::get('/invoice-statement-view',[

		'as'	=> 'api-invoice-from-statement-view',
		'uses'	=> 'ApiController@getInvoiceFromStudentStatement'

	]);

	Route::post('/login', 
	[
		'as'	=>	'api-login',
		'uses'	=>	'ApiController@postLogin'
	]);

	Route::post('/daily-routine',
		[
			'as'	=>	'api-daily-routine-list',
			'uses'	=>	'ApiController@dailyRoutineList'
		]);

	Route::post('/weekly-routine', [
		'as'	=> 'api-weekly-routine-list',
		'uses'=> 'ApiController@weeklyRoutineList'
	]);

	Route::post('/qr-login', 
	[
		'as'	=>	'api-qr-login',
		'uses'	=>	'ApiController@postQrLogin'
	]);

	Route::post('/change-password', 
  [
		'as'    => 'api-change-password',
		'uses'=> 'ApiController@postChangePassword'
  ]);


	Route::post('vehicle-login', 
	[
		'as'	=> 'api-vehicle-login',
		'uses'=> 'ApiController@postVehicleLogin'
	]);
	Route::get('/vehicle-location', 
	[
		'as'	=> 'api-vehicle-location',
		'uses'=> 'ApiController@getVehicleLocation'
	]);

	Route::post('/vehicle-location', 
	[
		'as'	=> 'api-vehicle-location',
		'uses'=> 'ApiController@postVehicleLocation'
	]);

	Route::get('/vehicles-get', 
	[
		'as'	=> 'api-vehicles-get',
		'uses'=> 'ApiController@getVehicles'
	]);

	Route::get('/vehicle-student/{student_id}',
	[
		'as'	=> 'api-vehicle-student',
		'uses'=> 'ApiController@getVehicleStudent'
	]);

	Route::get('/vehicle-staff/{employee_id}',[
		'as'	=> 'api-vehicle-staff',
		'uses'	=> 'ApiController@getVechileStaff'
		]);
		
	Route::post('/vehicle-log-distance', [
		'as'	=> 'api-vehicle-log-distance',
		'uses'=> 'ApiController@postVehicleLogDistance'
	]);

	/*
		push notification for events
	*/

	/*
		for library
	*/
	Route::get('/library/{student_id}',
		[
			'as'	=>	'api-library-view-books',
			'uses'	=>	'ApiController@libraryViewBooks'
		]);

	/*
		for dailyRoutine
	*/
	Route::post('/daily-routine',
		[
			'as'	=>	'api-daily-routine-list',
			'uses'	=>	'ApiController@dailyRoutineList'
		]);

	Route::get('/attendance/{student_id}/{class_id}/{section_code}/{month}/{start_date}/{end_date}/{year}',//Route::get('/attendance/{school_or_class_or_student}/{student_id}/{class_id}/{section_code}/{year}/{month}',
		[
			'as'	=>	'api-attendance',
			'uses'	=>	'ApiController@getAttendance'
		]);

	Route::get('/attendance-today/{student_id}/{class_id}/{section_code}',//Route::get('/attendance/{school_or_class_or_student}/{student_id}/{class_id}/{section_code}/{year}/{month}',
		[
			'as'	=>	'api-attendance-today',
			'uses'	=>	'ApiController@getAttendanceToday'
		]);

	// these two are for doing attendance of a class (by teacher)
	Route::get('/attendance-class/{class_id}/{section_code}',
		[
			'as'	=>	'api-attendance-class-get',
			'uses'=>	'ApiController@getAttendanceClass'
		]);

	Route::post('/attendance-class/{class_id}/{section_code}',
		[
			'as'	=>	'api-attendance-class-post',
			'uses'=>	'ApiController@postAttendanceClass'
		]);
	
	/*
	 * For fee
	 */
	Route::get('fee/{academic_session_id}/{student_id}/{month}', [
			'as'	=> 'api-fee',
			'uses'	=> 'ApiController@getFee'
		]);
	/*
	/
	/	This for teacher	
	/
	/
	*/
	Route::get('/get-class-id-section-id-from-teacher-id/{teacher_id}/{session_id?}',
		[
			'as'	=>	'api-get-class-id-section-id-from-teacher-id',
			'uses'	=>	'ApiController@getClassIdsAndSectionIdsOfFromTeacherId'
		]);
		
	
/*
/
/
	These are for assignments
/
/
*/
	Route::get('/get-subject-list/{class_id}/{section_id}',
		[
			'as'	=>	'api-get-subject-list-from-class-id-and-section-id',
			'uses'	=>	'ApiController@getSubjectListFromClassIdAndSectionId'
		]);

	Route::get('/get-subject-assignments/{subject_id}',
		[
			'as'	=>	'api-get-subject-list-from-class-id-and-section-id',
			'uses'	=>	'ApiController@getSubjectAssignments'
		]);

	Route::get('/get-assignments/{class_id}/{section_id}', 
		[
			'as'	=> 'api-get-assignments',
			'uses'	=> 'ApiController@getAssignments'
		]);

/*
/
/ These are for exam
/
/
*/
	Route::get('/exam-routine-by-class-id-and-section-id-and-exam-id/{class_id}/{section_id}/{exam_id}',
		[
			'as'	=>	'api-exam-routine-by-class-id-and-section-id-and-exam-id',
			'uses'	=>	'ApiController@getExamRoutine'
		]);	

	/*Route::get('/exam-routine-by-student-id-exam_id/{student_id}/{exam_id}',
		[
			'as'	=>	'exam-routine-by-student-id-exam-id',
			'uses'	=>	'ApiController@getExamRoutineByStudentIdAndExamId'
		]);	*/
	
	Route::get('/exam-get-marks-from-exam-id-and-student-id/{exam_id}/{student_id}',
		[
			'as'	=>	'api-exam-get-marks-from-exam-id-and-student-id',
			'uses'	=>	'ApiController@getExamMarks'
		]);
	

/*
/t
/ These are for academic calendar and events
/
/
*/

	Route::get('/academic-calendar/{event_group?}',
		[
			'as'	=>	'api-academic-calendar',
			'uses'	=>	'ApiController@academicCalendar'
		]);

	Route::get('/upcoming-events/{event_group}/{no_of_events?}/{date?}',
		[
			'as'	=>	'api-upcoming-events',
			'uses'	=>	'ApiController@getUpcomingEvents'
		]);

/*
/
/
	these are for gcm ids 
/
/
*/
	Route::post('/push-notifications/store_in_database',
	[
		'as'	=>	'api-push-notifications',
		'uses'	=>	'ApiController@postStoreInDatabase'
	]);

	Route::post('/push-notifications/change-status/{user_group}/{user_id}/{notification_status}', 
	[
		'as'	=>	'api-push-notifications-change-status',
		'uses'	=>	'ApiController@enableDisableNotification'
	]);

	Route::post('/mark-notification-as-viewed/{notification_id}', [
		'as'	=> 'api-push-notifications-mark-viewed',
		'uses'=> 'ApiController@markNotificationViewed'
	]);

	Route::get('/get-unread-notifications-number/{user_id}/{role}', [
		'as'		=> 'api-push-notifications-get-unread-notifications-number',
		'uses'	=> 'ApiController@getUnreadNotificationsNumber'
	]);

	Route::get('/delete-old-notifications', [
		'as'	=>	'api-push-notifications-delete-old',
		'uses'=>	'ApiController@deleteOldNotifications'
	]);


	/*
	/
	/
		This is for notices
	/
	/
	*/
	Route::get('/get-notice',
		['as'	=>	'api-get-notice',
		'uses'	=>	'ApiController@getNotice']);

	Route::post('/post-notice',
		['as'	=>	'api-post-notice',
		'uses'	=>	'ApiController@postNotice']);

	/*
	/
	/
		This is for messages
	/
	/
	*/
	Route::post('/post-message',
		['as'	=>	'api-post-message',
		 'uses'	=>	'ApiController@postMessage']);

	Route::get('/get-messages',
		['as'	=>	'api-get-messages',
		 'uses'	=>	'ApiController@getMessages']);

	Route::get('/view-message-history',
		['as'	=>	'api-view-message-history',
		 'uses'	=>	'ApiController@viewMessageHistory']);

	Route::get('/guardian-to-teachers-message-list/{class_id}/{section_id}/{guardian_id}', [
		'as'	=>	'api-guardian-to-teachers-message-list',
		'uses'=>	'ApiController@guardianToTeachersMessageList'
	]);

	Route::get('/guardian-to-teacher-messages/{guardian_id}/{teacher_id}/{role?}', [
		'as'	=>	'api-guardian-to-teachers-messages',
		'uses'=>	'ApiController@guardianToTeacherMessages'
	]);

	Route::get('/get-conversations/{user_id1}/{user_group1}/{user_id2}/{user_group2}', [
		'as'	=> 'api-get-conversations',
		'uses'=> 'ApiController@getConversations'
	]);

	Route::post('/send-message', [
		'as'	=>	'api-message-send',
		'uses' => 	'ApiController@apiPostSendMessage'
	]);

	//get list of related teachers
	Route::get('/get-related-teachers',
		['as'	=>	'api-get-related-teachers',
		'uses'	=>	'ApiController@getRelatedTeachers']);

	// get users of related to a class and section
	Route::get('/get-users-related-to-class/{class_id}/{section_id}/{group}',
		[
			'as'	=> 'api-get-guardian-related-to-class',
			'uses'=> 'ApiController@getGuardianRelatedToClass'
		]
	);

	// get list of guardians with whom a teacher has had conversation
	Route::get('/get-teacher-conversations/{teacher_id}/{role?}', [
		'as'	=> 'api-get-teacher-conversations',
		'uses'=> 'ApiController@getTeacherConversations'
	]);

	/*
	/
	/
		These are for notifications
	/
	/
	*/
	Route::get('/get-notifications',
		['as'	=>	'api-get-notifications',
		 'uses'	=>	'ApiController@getNotifications']);

	/*
	/
	/
		These are for Exam Marks
	/
	/
	*/

	//http://localhost/esms-demo/api/exam-marks/get-related-subjects/20
	Route::get('/exam-marks/get-related-subjects/{teacher_id}/{role?}',
		['as'	=>	'api-exam-marks-get-related-subjects',
		 'uses'	=>	'ApiController@getSubjectListForEnteringMarks']);
	/*
	[{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":3,"section_id":1,"subject_id":103,"class_name":"UKG","section_code":"A","subject_name":"English","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":3,"section_id":1,"subject_id":104,"class_name":"UKG","section_code":"A","subject_name":"Nepali","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":3,"section_id":1,"subject_id":105,"class_name":"UKG","section_code":"A","subject_name":"Nature Studies","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":3,"section_id":1,"subject_id":106,"class_name":"UKG","section_code":"A","subject_name":"Number Work","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":2,"section_id":1,"subject_id":107,"class_name":"LKG","section_code":"A","subject_name":"English","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":2,"section_id":1,"subject_id":108,"class_name":"LKG","section_code":"A","subject_name":"Nepali","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":2,"section_id":1,"subject_id":109,"class_name":"LKG","section_code":"A","subject_name":"Nature Studies","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":2,"section_id":1,"subject_id":110,"class_name":"LKG","section_code":"A","subject_name":"Number Work","pass_marks":60,"full_marks":100},{"exam_id":4,"exam_name":"First Terminal Evaluation","class_id":2,"section_id":1,"subject_id":111,"class_name":"LKG","section_code":"A","subject_name":"Social Studies","pass_marks":60,"full_marks":100}]
	*/

	//http://localhost/esms-demo/api/exam-marks/get-input-marks-form-data/11/1/26/3
	Route::get('/exam-marks/get-input-marks-form-data/{class_id}/{section_id}/{subject_id}/{exam_id}/{session_id?}',
		['as'	=>	'api-exam-marks-get-inpurt-marks-form-data',
		 'uses'	=>	'ApiController@getMarksEntryForm']);
	/*
	{"student_marks":[{"marks":80,"student_name":"student_4","student_id":5003,"current_roll_number":0,"subject_id":"26","comments":""},{"marks":60,"student_name":"student_2","student_id":5001,"current_roll_number":0,"subject_id":"26","comments":""},{"marks":90,"student_name":"student_5","student_id":5004,"current_roll_number":0,"subject_id":"26","comments":""},{"marks":70,"student_name":"student_3","student_id":5002,"current_roll_number":0,"subject_id":"26","comments":""},{"marks":50,"student_name":"student_1","student_id":5000,"current_roll_number":0,"subject_id":"26","comments":""}],"full_marks_pass_marks":{"pass_marks":40,"full_marks":100}}
	*/

	
	
	Route::post('/exam-marks/update-marks',
		['as'	=>	'api-exam-marks-update-marks',
		'uses'	=>	'ApiController@updateMarks']);

	/**
	 * Frontend api
	 */
	
	Route::group(['prefix' => 'frontend'], function() {
		// photo gallery
		Route::get('photo-gallery', [
			'as'	=> 'api-frontend-photo-gallery',
			'uses'=> 'ApiController@getPhotoGallery'
		]);
		// video gallery
		Route::get('video-gallery', [
			'as'	=> 'api-frontend-video-gallery',
			'uses'=> 'ApiController@getVideoGallery'
		]);
		// upcoming events
		Route::get('recent-upcoming-events', [
			'as'	=> 'api-frontend-recent-upcoming-events',
			'uses'=> 'ApiController@getRecentUpcomingEvents'
		]);
		// slideshow
		Route::get('slideshow', [
			'as'	=> 'api-frontend-slideshow',
			'uses'=> 'ApiController@getSlideshow'
		]);
		// about us
		Route::get('about-us', [
			'as'	=> 'api-frontend-about-us',
			'uses'=> 'ApiController@getAboutUs'
		]);
		// general downloads
		Route::get('general-downloads', [
			'as'	=> 'api-frontend-general-downloads',
			'uses'=> 'ApiController@getGeneralDownloads'
		]);
	});
		

/*
/
/ These are helper api routes
/
/
*/
	Route::get('/get-section-ids-from-class-id/{class_id}',
	[
		'as'	=>	'api-get-section-ids-from-class-id',
		'uses'	=>	'ApiController@helperGetSectionIdsFromClassId'
	]);

	Route::get('/get-class-ids-from-session-id/{session_id}',
	[
		'as'	=>	'api-get-class-ids-from-session-id',
		'uses'	=>	'ApiController@helperGetClassIdsFromSessionId'
	]);

	Route::get('/get-session-ids',
		['as'	=>	'api-get-session-ids',
		 'uses'	=>	'ApiController@helperGetSessionIds']);

	Route::get('/get-exam-ids/{session_id?}',
		['as'	=>	'api-get-exam-ids',
		 'uses'	=>	'ApiController@helperGetExamIds']);

	//});

	Route::post('/register-imei',
		['as'	=>	'api-register-imei',
		 'uses'	=>	'ApiController@registerImei']);
		 
	Route::get('/get-pdr/{session_id}/{class_id}/{section_id}/{date}',
		['as'	=>	'api-get-pdr',
		'uses'	=>	'ApiController@getPdr']);
		
	Route::get('/get-fee-print-list/{session_id}/{class_id}/{section_id}/{student_id}',
		['as'	=>	'api-get-fee-print-list',
		 'uses'	=>	'ApiController@getFeePrintList']);


	////////// These are for dates ///////////////////
	Route::get('/get-current-month-bs',
	[
		'as'	=>	'api-get-current-month-bs',
		'uses'	=>	'ApiController@getCurrentMonthBs'
	]);

	/////////// This is for superadmin ///////
	Route::get('/get-teacher-list/{class_id}/{section_id}',
	[
		'as'	=>	'get-teacher-list',
		'uses'	=>	'ApiController@getTeacherList'
	]);
});