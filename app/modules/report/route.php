<?php
define('REPORT_CONFIG_FILE', app_path().'/modules/report/config/config.json');
Route::group(array('prefix' => 'report'), function()
{
	Route::get('mass-print', [
		'as'	=> 'report-mass-print',
		'uses'=>	'ReportController@getMassPrint'
	]);

	Route::get('/list', 
		['as'	=>	'report-list',
		 'uses'	=>	'ReportController@getReportList']);

	Route::get('/class', 
		['as'	=>	'report-class',
		 'uses'	=>	'ReportController@getReportClass']);

	Route::get('/single', 
		['as'	=>	'report-single',
		 'uses'	=>	'ReportController@getReportSingle']);

	Route::get('/generate',
		['as'	=>	'report-generate-get',
		 'uses'	=>	'ReportController@getReportGenerate']);

	Route::get('/generate-final-report',
		['as'	=>	'report-generate-final-report-get',
		 'uses'	=>	'ReportController@getFinalReportGenerate']);

	//getFinalClassReport()
	Route::get('/api/final-class-report',
		['as'	=>	'api-report-final-class-report',
		 'uses'	=>	'ReportController@getFinalClassReport']);

	Route::get('/final-single-report/{exam_id}/{student_id}', 
		['as'	=>	'report-final-single-report',
		 'uses'	=>	'ReportController@getFinalReportSingle']);

	Route::get('/generate-ledger',
		['as'	=>	'report-generate-ledger-get',
		 'uses'	=>	'ReportController@getReportGenerateLedger']);

	
	Route::get('/generate-rank',
		['as'	=>	'report-generate-rank-get',
		 'uses'	=>	'ReportController@getReportGenerateRank']);
	Route::get('cas-settings-get', 
		['as'	=>	'cas-setting-get',
		'uses'	=>	'ReportController@getCasSetting']);

	Route::group(array('before' => 'csrf'), function()
	{
		Route::post('/generate',
			['as'	=>	'report-generate-post',
			'uses'	=>	'ReportController@postReportGenerate']);

		Route::post('/generate-final-report',
		['as'	=>	'report-generate-final-report-post',
		 'uses'	=>	'ReportController@postFinalReportGenerate']);

		Route::post('/config', 
			['as' => 	'report-config-post',
			 'uses' =>	'ReportController@postConfig']);

		Route::post('/edit-remarks', [
			'as'	=> 'report-edit-remarks-post',
			'uses'=> 'ReportController@postEditRemarks'
		]);

		Route::post('/enter-remarks', [
			'as'	=>	'report-enter-remarks-post',
			'uses'	=>	'ReportController@postEnterRemarks']);

				Route::post('/generate-rank',
		['as'	=>	'report-generate-rank-post',
		 'uses'	=>	'ReportController@postReportGenerateRank']);


		Route::post('/delete-class', [
			'as'	=> 'report-delete-class-section-post',
			'uses'=> 'ReportController@postDeleteClassSection'
		]);
		Route::post('cas-settings-post', 
			['as'	=>	'cas-setting-post',
			'uses'	=>	'ReportController@postCasSetting']);


	});

	Route::group(['prefix' => 'ajax'], function()
	{
		Route::get('get-remarks',
			['as'	=>	'report-ajax-get-remarks',
			 'uses'	=>	'ReportController@ajaxGetRemarks']);
	});

});