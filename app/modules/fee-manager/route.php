<?php

Route::group(array('prefix'=>'fee-manager', 'before'=>'reg-superadmin-admin'), function()
{
	Route::get('monthly-fee', 
		[
			'as'	=> 'fee-manager-monthly-fee-get',
			'uses'	=> 'FeeManagerController@getMonthlyFee'
		]);

	Route::get('hostel-fee', 
		[
			'as'	=> 'fee-manager-hostel-fee-get',
			'uses'	=> 'FeeManagerController@getHostelFee'
		]);

	Route::get('examination-fee', 
		[
			'as'	=> 'fee-manager-examination-fee-get',
			'uses'	=> 'FeeManagerController@getExaminationFee'
		]);

	Route::get('miscellaneous-class-fee', 
		[
			'as'	=> 'fee-manager-misc-class-fee-get',
			'uses'	=> 'FeeManagerController@getMiscClassFee'
		]);

	Route::get('miscellaneous-student-fee', 
		[
			'as'	=> 'fee-manager-misc-student-fee-get',
			'uses'	=> 'FeeManagerController@getMiscStudentFee'
		]);

	Route::get('miscellaneous-class-fee-edit/{id}', 
		[
			'as'	=> 'fee-manager-misc-class-fee-edit-get',
			'uses'	=> 'FeeManagerController@getMiscClassFeeEdit'
		]);

	Route::get('miscellaneous-student-fee-edit/{id}', 
		[
			'as'	=> 'fee-manager-misc-student-fee-edit-get',
			'uses'	=> 'FeeManagerController@getMiscStudentFeeEdit'
		]);

	Route::get('scholarship', [
			'as'	=> 'fee-manager-scholarship-get',
			'uses'	=> 'FeeManagerController@getScholarship'
		]);

	Route::get('tax-configuration', [
		'as'	=> 'fee-manager-tax-config-get',
		'uses'=> 'FeeManagerController@getTaxConfig'
	]);

	Route::group(array('before'=>'csrf'), function()
	{
		Route::post('monthly-fee',
			[
				'as'	=> 'fee-manager-monthly-fee-post',
				'uses'	=> 'FeeManagerController@postMonthlyFee'
			]);
		
		Route::post('monthly-fee-delete',
			[
				'as'	=>	'fee-manager-monthly-fee-delete-post',
				'uses'	=>	'FeeManagerController@postMonthlyFeeDelete'
			]);

		Route::post('hostel-fee', 
			[
				'as'	=> 'fee-manager-hostel-fee-post',
				'uses'	=> 'FeeManagerController@postHostelFee'
			]);

		Route::post('hostel-fee-delete',
			[
				'as'	=>	'fee-manager-hostel-fee-delete-post',
				'uses'	=>	'FeeManagerController@postHostelFeeDelete'
			]);

		Route::post('examination-fee', 
			[
				'as'	=> 'fee-manager-examination-fee-post',
				'uses'	=> 'FeeManagerController@postExaminationFee'
			]);

		Route::post('examination-fee-delete',
			[
				'as'	=>	'fee-manager-examination-fee-delete-post',
				'uses'	=>	'FeeManagerController@postExaminationFeeDelete'
			]);

		Route::post('miscellaneous-class-fee', 
			[
				'as'	=> 'fee-manager-misc-class-fee-post',
				'uses'	=> 'FeeManagerController@postMiscClassFee'
			]);

		Route::post('miscellaneous-class-fee-delete', 
			[
				'as'	=> 'fee-manager-misc-class-fee-delete-post',
				'uses'	=> 'FeeManagerController@postMiscClassFeeDelete'
			]);

		Route::post('miscellaneous-class-fee-edit/{id}', 
			[
				'as'	=> 'fee-manager-misc-class-fee-edit-post',
				'uses'	=> 'FeeManagerController@postMiscClassFeeEdit'
			]);

		Route::post('miscellaneous-student-fee', 
			[
				'as'	=> 'fee-manager-misc-student-fee-post',
				'uses'	=> 'FeeManagerController@postMiscStudentFee'
			]);

		Route::post('miscellaneous-student-fee-edit/{id}', 
			[
				'as'	=> 'fee-manager-misc-student-fee-edit-post',
				'uses'	=> 'FeeManagerController@postMiscStudentFeeEdit'
			]);

		Route::post('miscellaneous-student-fee-delete', 
			[
				'as'	=> 'fee-manager-misc-student-fee-delete-post',
				'uses'	=> 'FeeManagerController@postMiscStudentFeeDelete'
			]);

		Route::post('scholarship', [
			'as'	=> 'fee-manager-scholarship-post',
			'uses'	=> 'FeeManagerController@postScholarship'
		]);

		Route::post('scholarship-delete', 
			[
				'as'	=> 'fee-manager-scholarship-delete-post',
				'uses'	=> 'FeeManagerController@postScholarshipDelete'
			]);

		Route::post('tax-configuration', [
			'as'	=> 'fee-manager-tax-config-post',
			'uses'=> 'FeeManagerController@postTaxConfig'
		]);

	});
});