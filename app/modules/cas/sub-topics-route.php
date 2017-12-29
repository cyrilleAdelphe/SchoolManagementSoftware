<?php

Route::group(['prefix' => 'cas'], function()
{
Route::group(['before' => 'reg-superadmin-admin'], function()
{

	
	Route::get('sub-topics-list',
		['as'	=>	'cas-sub-topics-list',
		 'uses'	=>	'CasSubTopicsController@getSubTopicsListView']);

	Route::get('sub-topics-create-edit/{subject_id}',
		['as'	=>	'cas-sub-topics-create-edit-get',
		 'uses'	=>	'CasSubTopicsController@getSubTopicsCreateEditView']);

	Route::get('sub-topics-assign-marks/{subject_id}',
		['as'	=>	'cas-sub-topics-assign-marks-get',
		 'uses'	=>	'CasSubTopicsController@getAssignSubTopicMarks']);
	//getAssignSubTopicMarks

	Route::group(['before' => 'csrf'], function()
	{
		Route::post('sub-topics-create-edit/{subject_id}',
		['as'	=>	'cas-sub-topics-create-edit-post',
		 'uses'	=>	'CasSubTopicsController@postSubTopicsCreateEditView']);

		Route::post('sub-topics-delete',
			['as'	=>	'cas-sub-topics-delete-post',
			 'uses'	=>	'CasSubTopicsController@postSubTopicsdeleteView']);

		Route::post('sub-topics-assign-marks/{subject_id}',
		['as'	=>	'cas-sub-topics-assign-marks-post',
		 'uses'	=>	'CasSubTopicsController@postAssignSubTopicMarks']);
	
	});

});
	/// these are for apis
	Route::group(['prefix' => 'api'], function()
	{

		Route::get('get-class-ids-from-session-id',
				['as'	=>	'cas-ajax-get-class-ids-from-session-id',
				 'uses'	=>	'CasSubTopicsController@getClassIdsFromSessionId']);

			Route::get('get-section-ids-from-session-id-and-class-id',
				['as'	=>	'cas-ajax-get-section-ids-from-session-id-and-class-id',
				 'uses'	=>	'CasSubTopicsController@getClassIdsFromSessionIdAndClassId']);

			Route::get('get-subject-list-from-session-id-class-id-and-section-id',
				['as'	=>	'cas-ajax-get-subject-list-from-session-id-class-id-and-section-id',
				 'uses'	=>	'CasSubTopicsController@getSubjectIdsFromSessionIdClassIdAndSectionId']);

			Route::get('get-cas-assign-sub-topic-assign-mark-exam-list',
				['as'	=>	'cas-ajax-get-cas-assign-sub-topic-assign-mark-exam-list',
				 'uses'	=>	'CasSubTopicsController@apiGetExamListHtml']);

			Route::get('get-cas-assign-sub-topic-assign-mark-student-list',
				['as'	=>	'cas-ajax-get-cas-assign-sub-topic-assign-mark-student-list',
				 'uses'	=>	'CasSubTopicsController@apiGetStudentAssignSubTopicMark']);
	});

});


