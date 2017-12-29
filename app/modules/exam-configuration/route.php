<?php
Route::group(array('prefix'=>'exam-configuration','before'=>'reg-superadmin-admin'),function(){
	Route::get('/test',function(){
		return View::make('exam-configuration.views.test');
	});

	Route::get('/create',
				['as'	=>	'exam-configuration-create-get',
				 'uses'	=>	'ExamConfigurationController@getCreateView']);

	Route::get('/list',
				['as'	=>	'exam-configuration-list',
				 'uses'	=>	'ExamConfigurationController@getListView']);

	Route::get('/view/{id}',
					['as'	=>	'exam-configuration-view',
					 'uses'	=>	'ExamConfigurationController@getViewview']);

	Route::get('/edit/{id}',
			['as'	=>	'exam-configuration-edit-get',
			 'uses'	=>	'ExamConfigurationController@getEditView']);

	Route::get('/calendar',
				[
					'as'	=> 'exam-configuration-calendar-get',
					'uses'	=> 'ExamConfigurationController@getCalendar'
				]);

	Route::get('/admit-card/{id}', [
		'as'	=> 'exam-configuration-admit-card',
		'uses'=> 'ExamConfigurationController@admitCard'
	]);

	Route::get('/api/get-exam-list-from-session-id',
		[
			'as'	=>	'exam-configuration-api-get-exam-list-from-session-id',
			'uses'	=>	'ExamConfigurationController@generateExamListFromSessionId'
		]);

	Route::group(array('before' => 'csrf'), function(){

		Route::post('/create',
				['as'	=>	'exam-configuration-create-post',
				 'uses'	=>	'ExamConfigurationController@postCreateView']);

		Route::post('/edit/{id}',
				['as'	=>	'exam-configuration-edit-post',
				 'uses'	=>	'ExamConfigurationController@postEditView']);

		Route::post('/delete',
				['as'	=>	'exam-configuration-delete-post',
				 'uses'	=>	'ExamConfigurationController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'exam-configuration-purge-post',
				 'uses'	=>	'ExamConfigurationController@purgeRows']);

		Route::post('/delete-exam-configuration/{id}',
					['as'	=>	'exam-configuration-delete-single-post',
					 'uses'	=>	'ExamConfigurationController@deleteExamConfiguration']);


	});
});

?>