<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'teacher', 'before' => 'reg-superadmin'), function(){

		Route::get('/list',
				['as'	=>	'teacher-list',
				 'uses'	=>	'TeacherController@getListView']);

		Route::get('/create',
				['as'	=>	'teacher-create-get',
				 'uses'	=>	'TeacherController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'teacher-view',
				 'uses'	=>	'TeacherController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'teacher-edit-get',
				 'uses'	=>	'TeacherController@getEditView']);

		Route::get('/home',
				['as'	=> 'teacher-dashboard',
				'uses'	=> 'TeacherController@getDashboard'
				]);

		Route::get('/logout',
				['as'	=> 'teacher-logout',
				'uses'	=> 'TeacherController@getLogout'
				]);

		Route::get('/teacher-profile/',
				['as'	=> 'teacher-profile',
				'uses'	=> 'TeacherController@getProfile'
				]);

		Route::get('/attendance-create',[
				'as'	=> 'attendance-create-teacher',
				'uses'	=> 'TeacherController@getCreateAttendance'

				]);

		Route::get('/attendance-history', [
				'as'	=> 'attendance-teacher-view-class-section-history',
				'uses'	=> 'TeacherController@getAttendanceViewHistory'

			]);

		Route::get('/attendance-teacher-ajax-classes-list', [
				'as'	=> 'ajax-get-teacher-classes',
				'uses'	=> 'TeacherController@getAjaxTeacherClasses'
			]);

		Route::get('/attendance-teacher-ajax-classes-section-list', [
				'as'	=> 'ajax-get-classes-section-from-teacher-id',
				'uses'	=> 'TeacherController@getAjaxTeacherClassSection'

			]);
		
		Route::get('/attendance-teacher-ajax-get-class-section-history', [
				'as'	=> 'teacher-ajax-get-class-section-history',
				'uses'	=> 'TeacherController@getAjaxViewHistory'
			]);

		Route::get('/ajax-teacher-class-view', [
				'as' 	=> 'teacher-ajax-class-view',
				'uses'  => 'TeacherController@getAjaxTeacherClassesShow'
			]);

		Route::get('/teacher-marks-update', [
				'as'	=> 'teacher-update-marks',
				'uses'	=> 'TeacherController@getTeacherUpdateMarks'

			]);

		Route::get('/teacher-classes-from-exam',[
				'as'	=> 'teacher-classes-from-exam',
				'uses'	=> 'TeacherController@getAjaxTeacherClassesFromExam'
			]);



		Route::get('/teacher-classes-exam-show-section-list',[
				'as'	=>'teacher-exam-show-class-section-list',
				'uses'	=> 'TeacherController@getAjaxTeacherExamClassSectionList'
			]);

		Route::get('/teacher-subjects-from-exam', [
				'as'	=>'teacher-subjects-from-exam-class',
				'uses'	=> 'TeacherController@getAjaxTeacherSubjects'	
			]);

		Route::get('/teacher-subjects-cas', [
				'as'	=>'teacher-subject-cas-from-classsection',
				'uses'	=> 'TeacherController@getAjaxCasTeacherSubjects'
			]);

		Route::get('/cas-subtopics-list', [
				'as'	=>'teacher-cas-subtopics-list',
				'uses'  =>'TeacherController@getTeacherSubTopicsListView'
			]);

		Route::get('/cas-subtopics-class-list',[
				'as'   =>'teacher-cas-class-list',
				'uses' =>'TeacherController@getAjaxTeacherClassListCas'

			]);

		Route::get('/cas-subtopics-section-list',[
				'as'   =>'teacher-cas-section-list',
				'uses' =>'TeacherController@getAjaxTeacherSectionListCas'

			]);



		Route::get('/cas-subtopics-create-edit/{subject_id}', [
				'as'	=>'teacher-cas-sub-topics-create-edit',
				'uses'	=> 'TeacherController@getTeacherCasSubTopicCreateEditView'
			]);

		Route::get('/cas-subtopics-teacher-assign-marks/{subject_id}', [	
				'as'	=> 'teacher-cas-subtopic-assign-get',
				'uses'	=> 'TeacherController@getTeacherCasAssignSubTopics'

			]);



		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'teacher-create-post',
					 'uses'	=>	'TeacherController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'teacher-edit-post',
					 'uses'	=>	'TeacherController@postEditView']);

			Route::post('/delete',
					['as'	=>	'teacher-delete-post',
					 'uses'	=>	'TeacherController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'teacher-purge-post',
					 'uses'	=>	'TeacherController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'teacher-purge-record-post',
					 'uses'	=>	'TeacherController@postDelete']);

			Route::post('/teacher-update-marks-post', [
					'as'	=>  'teacher-exam-update-marks-post',
					'uses'	=> 'TeacherController@postTeacherUpdateMarks'
				
				]);

			Route::post('/cas-sub-topics-create-edit-post/{subject_id}', [
					'as'	=> 'teacher-cas-sub-topics-create-edit-post',
					'uses'	=> 'TeacherCasController@postTeacherSubTopicsCreateEdit'
				]);

			Route::post('cas-sub-topic-post-assign/{subject_id}', [
					'as'	=> 'teacher-cas-subtopics-assign-post',
					'uses'	=> 'TeacherCasController@postTeacherCasSubTopicMarks'

				]);

		});
	
		Route::group(array('prefix' => 'ajax'), function()
		{
			Route::get('active-classes',
			[
				'as'	=>	'ajax-active-classes',
				'uses'	=>	'TeacherController@ajaxGetACtiveClasses'
			]);

			Route::get('active-sections',
			[
				'as'	=>	'ajax-active-sections',
				'uses'	=>	'TeacherController@ajaxGetACtiveSections'
			]);
		});
	});



//});

	