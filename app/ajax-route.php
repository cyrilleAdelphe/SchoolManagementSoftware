<?php

	Route::get('ajax-get-exam-ids-from-session-id',
		  ['as'	=>	'ajax-get-exam-ids-from-session-id',
		   'uses'	=>	'AjaxController@getExamIdsFromSessionId']); //ajax-get-section-ids-from-class-id

	Route::get('ajax-get-section-ids-from-class-id',
		  ['as'	=>	'ajax-get-section-ids-from-class-id',
		   'uses'	=>	'AjaxController@getSectionIdsFromClassId']);

	Route::get('ajax-get-students-from-class-id-and-student-id',
		['as'	=>	'ajax-get-students-from-class-id-and-student-id',
		 'uses'	=>	'AjaxController@getStudentsFromClassIdAndStudentId']);

	Route::get('ajax-get-students-from-session-id-and-student-id',
		['as'	=>	'ajax-get-students-from-session-id-and-student-id',
		 'uses'	=>	'AjaxController@getStudentsFromSessionIdAndStudentId']);

	Route::get('ajax-get-class-ids-from-session-id',
		  ['as'	=>	'ajax-get-class-ids-from-session-id',
		   'uses'	=>	'AjaxController@getClassIdsFromSessionId']);

	Route::get('ajax-get-class-ids-from-exam-id',
		  ['as'	=>	'ajax-get-class-ids-from-exam-id',
		   'uses'	=>	'AjaxController@getClassIdsFromExamId']);

	Route::get('ajax-get-dashboard-modal-search-list',
		  ['as'	=>	'ajax-get-dashboard-modal-search-list',
		   'uses'	=>	'AjaxController@getDashboardModalSearchList']);

	Route::get('ajax-get-modal-employee-search-list',
		  ['as'	=>	'ajax-get-modal-employee-search-list',
		   'uses'	=>	'AjaxController@getModalEmployeeSearchList']);
	
	Route::get('ajax-get-subject-list-and-teacher-list-from-session-id-class-id-section-id',
		['as'	=>	'ajax-get-subject-list-and-teacher-list-from-session-id-class-id-section-id',
		 'uses'	=>	'AjaxController@AjaxGetSubjectListAndTeacherListFromClassIdAndSectionId']);

	Route::get('ajax-get-class-ids-from-session-id-data',
		['as'	=>	'ajax-get-class-ids-from-session-id-data',
		 'uses'	=>	'AjaxController@getClassIdsFromSessionIdData']);

	Route::get('ajax-get-section-ids-from-class-id-data',
		['as'	=>	'ajax-get-section-ids-from-class-id-data',
		 'uses'	=>	'AjaxController@getSectionIdsFromClassIdData']);

	Route::get('ajax-get-class-ids-from-session-id-html',
		['as'	=>	'ajax-get-class-ids-from-session-id-html',
		 'uses'	=>	'AjaxController@getClassIdsFromSessionIdHtml']);

	Route::get('ajax-get-section-ids-from-class-id-html',
		['as'	=>	'ajax-get-section-ids-from-class-id-html',
		 'uses'	=>	'AjaxController@getSectionIdsFromClassIdHtml']);

	///// Exam-Marks-v1-changes-made-here ////////
	////////////// Teacher specific routes added here ////////////
	/// session_id and default_class_id as parameters
	Route::get('ajax-get-related-classes',
		['as'	=>	'ajax-get-related-classes',
		 'uses'	=>	'AjaxController@getRelatedClasses']);

	/// class_id and default_section_id as parameters
	Route::get('ajax-get-related-sections',
		['as'	=>	'ajax-get-related-sections',
		 'uses'	=>	'AjaxController@getRelatedSections']);

	////////////// Teacher specific routes added here /////////////
	///// Exam-Marks-v1-changes-made-here ////////

?>