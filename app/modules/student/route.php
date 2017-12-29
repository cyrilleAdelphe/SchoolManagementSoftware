<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'student', 'before' => 'reg-superadmin-admin'), function(){

		Route::get('deactive-student-list',
			['as'	=>	'student-deactive-student-list-get',
			 'uses'	=>	'StudentController@getDeactiveStudentList']);

		Route::get('merge-parents', 
			['as'	=>	'student-merge-parents-get',
			 'uses'	=>	'StudentController@getMergeParents']);

		Route::post('merge-parents',
			['as'	=>	'student-merge-parents-post',
			'uses'	=>	'StudentController@postMergeParents']);

		Route::post('/generate-student-report-excel', [
				'as'	=> 'generate-student-excel',
				'uses'	=> 'StudentController@getStudentReportExcel'	
			]);


		Route::get('/list',
				['as'	=>	'student-list',
				 'uses'	=>	'StudentController@getListView']);

		Route::get('/create',
				['as'	=>	'student-create-get',
				 'uses'	=>	'StudentController@getCreateView']);

		

		Route::get('/edit/{id}',
				['as'	=>	'student-edit-get',
				 'uses'	=>	'StudentController@getEditView']);

		Route::get('/import-excel',
			[
				'as'	=> 'student-import-excel-get',
				'uses'=> 'StudentController@getImportExcel'
			]
		);
		
		Route::get('/migrate-students',
			[
				'as'	=> 'student-migrate-students-get',
				'uses'=> 'StudentController@getMigrateStudent'
			]
		);

		Route::get('/export-excel',
			[
				'as'	=> 'student-export-excel-get',
				'uses'=> 'StudentController@getExportExcel'
			]
		);
		
		Route::get('/mass-roll-assignment',
			[
				'as'	=> 'student-mass-roll-assignment-get',
				'uses'=> 'StudentController@getMassRollAssignment'
			]
		);
		
			Route::get('/student-report',[
				'as'	=> 'student-report-get',
				'uses'	=> 'StudentController@getStudentReport'

			]);

		Route::get('/show-report', [
				'as'	=> 'show-report-get',
				'uses'	=> 'StudentController@getShowReport'
			]);

		Route::get('/class-section-report', [
				'as'	=> 'class-section-report-get',
				'uses'	=> 'StudentController@getAjaxClassSectionReport'
			]);

		Route::get('/discount-type-report', [
				'as'	=> 'discount-type-report-get',
				'uses'	=> 'StudentController@getAjaxDiscountTypeReport'

			]);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('deactive', 
			['as'	=>	'student-deactive-post',
			 'uses'	=>	'StudentController@postDeactivateStudents']);

			Route::post('restore-deactive', 
			['as'	=>	'student-restore-deactive-post',
			 'uses'	=>	'StudentController@postRestoreDeactivateStudents']);
	

			Route::post('/mass-roll-assignment',
			[
				'as'	=> 'student-mass-roll-assignment-post',
				'uses'=> 'StudentController@postMassRollAssignment'
			]);
			
			Route::post('/migrate-students',
			[
				'as'	=> 'student-migrate-students-post',
				'uses'=> 'StudentController@postMigrateStudent'
			]);
			
			Route::post('/create',
					['as'	=>	'student-create-post',
					 'uses'	=>	'StudentController@postCreateView']);


			Route::post('/delete',
					['as'	=>	'student-delete-post',
					 'uses'	=>	'StudentController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'student-purge-post',
					 'uses'	=>	'StudentController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'student-purge-record-post',
					 'uses'	=>	'StudentController@postDelete']);

			Route::post('/import-excel',
				[
					'as'	=> 'student-import-excel-post',
					'uses'=> 'StudentController@postImportExcel',

				]
			);

			

		});

		///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////
		Route::get('show-report-config',
			['as'	=>	'student-show-report-config-get', 
			 'uses'	=>	'StudentController@getShowReportConfig']);

		Route::post('show-report-config',
			['as'	=>	'student-show-report-config-post',
			 'uses'	=>	'StudentController@postShowREportConfig']);
		///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////
	
		
	});

	Route::post('/change-password/{id}',
				[
					'as'	=> 'student-change-password-post',
					'uses'=> 'StudentController@postChangePassword',
					'before' => array('reg-superadmin-admin-user', 'csrf'),
					'prefix' => 'student'
				]
			);

	Route::post('/edit/{id}',
					['as'	=>	'student-edit-post',
					 'uses'	=>	'StudentController@postEditView',
					 'before' => array('reg-superadmin-admin-user', 'csrf'),
					 'prefix' => 'student']);

	Route::get('/view/{id}',
				['as'	=>	'student-view',
				 'uses'	=>	'StudentController@getViewview',
				 'before' => array('reg-superadmin-admin-user'),
				 'prefix' => 'student']);



	Route::group(array('prefix' => 'ajax'), function()
		{
			Route::get('active-classes',
			[
				'as'	=>	'student-ajax-active-classes',
				'uses'	=>	'StudentController@ajaxGetACtiveClasses',

			]);

			Route::get('active-sections',
			[
				'as'	=>	'student-ajax-active-sections',
				'uses'	=>	'StudentController@ajaxGetACtiveSections'
			]);

			Route::get('document-list', 
			[
				'as'	=>	'student-ajax-document-list',
				'uses'=>	'StudentController@ajaxDocumentList'
			]);

			Route::get('payments',
			[
				'as'	=>	'student-ajax-payments',
				'uses'=>	'StudentController@ajaxGetFee'
			]);

			Route::get('exam-report', 
			[
				'as'	=>	'student-ajax-exam-report',
				'uses'=>	'StudentController@ajaxExamReport'
			]);

			Route::get('extra-activities',
			[
				'as'	=>	'student-ajax-extra-activities',
				'uses'=>	'StudentController@ajaxExtraActivities'
			]);

			Route::get('library', 
			[
				'as'	=>	'student-ajax-library',
				'uses'=>	'StudentController@ajaxLibrary'
			]);

			Route::get('attendance-select-month',
			[
				'as'	=>	'student-ajax-attendance-select-month',
				'uses'=>	'StudentController@ajaxAttendanceSelectMonth'
			]);

			Route::get('attendance-get-month',
			[
				'as'	=>	'student-ajax-attendance-get-month',
				'uses'=>	'StudentController@ajaxAttendanceGetMonth'
			]);

		});



//});

	