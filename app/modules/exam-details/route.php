<?php
Route::group(array('prefix'=>'exam-details','before'=>'reg-superadmin'),function(){
	Route::get('/create',
				['as'	=>	'exam-details-create-get',
				 'uses'	=>	'ExamDetailsController@getCreateView']);

	Route::get('/list',
				['as'	=>	'exam-details-list',
				 'uses'	=>	'ExamDetailsController@getListView']);

	Route::get('/view/{id}',
					['as'	=>	'exam-details-view',
					 'uses'	=>	'ExamDetailsController@getViewview']);

	Route::get('/edit/{id}',
			['as'	=>	'exam-details-edit-get',
			 'uses'	=>	'ExamDetailsController@getEditView']);

	Route::get('/calendar',
				[
					'as'	=> 'exam-details-calendar-get',
					'uses'	=> 'ExamDetailsController@getCalendar'
				]);

	Route::get('/delete-all-records-of-an-exam/{exam_id}',
				[
					'as'	=> 'delete-all-records-of-an-exam',
					'uses'	=> 'ExamDetailsController@deleteAllRecordsOfAnExam'
				]);

	Route::get('/view-routine',
					['as'	=>	'exam-details-view-routine',
					 'uses'	=>	'ExamDetailsController@viewRoutine']);

	Route::group(array('before' => 'csrf'), function(){

		Route::post('/create',
				['as'	=>	'exam-details-create-and-edit',
				 'uses'	=>	'ExamDetailsController@postCreateEditView']);

		Route::post('/edit/{id}',
				['as'	=>	'exam-details-edit-post',
				 'uses'	=>	'ExamDetailsController@postEditView']);

		Route::post('/delete',
				['as'	=>	'exam-details-delete-post',
				 'uses'	=>	'ExamDetailsController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'exam-details-purge-post',
				 'uses'	=>	'ExamDetailsController@purgeRows']);


	});

	Route::get('ajax-get-section-ids', 
		['as'	=>	'exam-details-get-section-ids',
		 'uses' =>	'ExamDetailsController@ajaxGetSectionIds']);

});

?>