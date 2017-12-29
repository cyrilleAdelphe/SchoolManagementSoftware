<?php
////////////// These are for api //////////////////////////////

//apiGetStudentStatement

////////////////////////////////////////////////////////////////
Route::group(['before' => 'reg-superadmin-admin'], function()
{
	Route::group(['prefix' => 'billing'], function()
	{	
		/*Route::get('api-get-statement-list-view',
			['as'	=>	'billing-api-get-statement-list-view',
			 'uses'	=>	'BillingController@apiGetStatementListView']);*/

		Route::get('show-receipts',
			['as'	=>	'billing-show-receipts-get',
			 'uses'	=>	'BillingController@getTotalReceipts']);

		Route::get('/direct-invoice-tab', [
			'as' 	=> 'direct-invoice-tab-get',
			'uses'	=> 'BillingController@getDirectInvoiceTabView'
 		
			]);
			
		Route::get('/opening-balance-tab', [
			'as'    => 'opening-balance-tab-get',
			'uses'  => 'BillingController@getOpeningBalanceTabView'
			
			]);
		
		Route::get('/direct-invoice-fee-print',[
			'as'	=> 'billing-fee-print-direct-invoice',
			'uses'	=> 'BillingController@getFeePrintDirectInvoice'
		
		]);
		
		Route::get('/opening-balance-fee-print',[
			'as'	=> 'billing-fee-print-opening-balance',
			'uses'	=> 'BillingController@getFeePrintOpeningBalance'
		
		]);
			
		Route::get('/send-due-report/{student_id}/{invoice_balance}/{received_amount}', [
			'as'	=> 'send-due-report-api',
			'uses'	=> 'BillingController@sendDuesPushNotification'

			]);	
	
		Route::get('get-receipt-list',
			[
				'as'	=>	'billing-get-receipt-list',
				'uses'	=>	'BillingController@getReceiptListView'
			]);

		Route::get('income-report-student', 
			['as'	=>	'billing-income-report-student',
			'uses'	=>	'BillingController@getIncomeReportStudents']);
		
		Route::get('create-extra-fees', 
			['as'	=>	'billing-create-extra-fee-get',
			'uses'	=>	'BillingController@getExtraCreateFeeView']);

		Route::get('api/billing-extra-fee-student-list',
			['as'	=>	'api-billing-extra-fee-student-list',
			 'uses'	=>	'BillingController@apiGetExtraFeeStudentListView']);

		Route::get('create-fee', 
			['as'	=>	'billing-create-fee-get',
			'uses'	=>	'BillingController@getCreateFeeView']);

		Route::get('edit-fee/{id}',
			['as'	=>	'billing-edit-fee-get',
			 'uses'	=>	'BillingController@getEditFee']);

		Route::get('generate-fee',
			['as'	=>	'billing-generate-fee-get',
			 'uses'	=>	'BillingController@getGenerateFeeView']);

		Route::get('direct-invoice', 
			['as'	=> 'billing-direct-invoice-get',
			 'uses'	=>	'BillingController@getDirectInvoiceView']);
			 
		Route::get('direct-invoice-organization', 
			['as'	=> 'billing-direct-invoice-organization-get',
			 'uses'	=>	'BillingController@getDirectInvoiceOrganizationView']);

		Route::get('recieve-payment', 
			['as'	=>	'billing-recieve-payment-get',
			 'uses'	=>	'BillingController@getRecievePaymentView']);

		Route::get('statement',
			['as'	=>	'billing-statement',
			 'uses'	=>	'BillingController@getStatementView']);

		Route::get('show-invoice-from-invoice-number/{invoice_number}', 
			['as'	=>	'show-invoice-from-invoice-number',
			 'uses'	=>	'BillingController@showInvoiceFromInvoiceNumber']);

		Route::get('remaining-due', 
		['as'	=>	'billing-remaining-due-list',
		 'uses'	=>	'BillingController@getRemainingDueList']);

		Route::get('opening-balance', 
		['as'	=>	'billing-opening-balance-get',
		 'uses'	=>	'BillingController@getOpeningBalance']);

		Route::get('late-fee',
			['as' => 'billing-late-fee-get',
			'uses' => 'BillingController@getLateFee']);

		//apiGetStatementListView($date_range, $student_id, $class_id, $section_id, $session_id)
		

		Route::get('transaction-list',
			['as'	=>	'billing-transaction-list',
			 'uses'	=>	'BillingController@getTransactionListView']);

		Route::get('invoice-list',
			['as'	=>	'billing-invoice-list',
			 'uses'	=>	'BillingController@getInvoiceListView']);

		//apiGetTransactionListView()
		Route::get('api-get-transaction-list-view', 
			['as'	=>	'billing-api-get-transaction-list-view',
			 'uses'	=>	'BillingController@apiGetTransactionListView']);

		Route::get('tax-report', 
			['as'	=>	'billing-tax-report',
			 'uses'	=>	'BillingController@getTaxReportView']);

		Route::get('api-get-tax-report-list-iew', 
			['as'	=>	'billing-api-tax-report-list-view',
			 'uses'	=>	'BillingController@apiGetTaxReportView']);

		Route::get('income-report',
			['as'	=>	'billing-income-report',
			 'uses'	=>	'BillingController@getIncomeReportView']);

		Route::get('api-get-income-report-list-view',
			['as'	=>	'billing-api-get-income-report-list-view',
			 'uses'	=>	'BillingController@apiGetIncomeReportView']);

		Route::get('api-remaining-due', 
		['as'	=>	'billing-api-remaining-due-list',
		 'uses'	=>	'BillingController@apiGetDueInvoices']);

		Route::get('api-remaining-due-details', 
		['as'	=>	'billing-api-remaining-due-details-list',
		 'uses'	=>	'BillingController@apiGetDueInvoicesDetails']);

		Route::get('credit-note/{invoice_id}', 
			['as'	=> 'billing-credit-note-get',
			 'uses'	=>	'BillingController@getCreditNoteView']);
		
			 Route::get('get-student-select-list-fee-print',
				['as'	=>	'billing-ajax-get-student-select-list-fee-print',
				 'uses'	=>	'BillingController@ajaxGetStudentSelectListForFeePrint'
				]);

		Route::get('fee-print',
			['as'	=>	'billing-fee-print-get',
			 'uses'	=>	'BillingController@getFeePrint']);

		Route::get('fee-print-list',
			['as'	=>	'billing-fee-print-list',
			 'uses'	=>	'BillingController@getFeePrintList']);
			 
		Route::get('list-view-fee-print',
			['as'	=>	'billing-list-view-fee-print-get',
			 'uses'	=>	'BillingController@getListViewFeePrint']);

		Route::get('list-view-fee-print-list',
			['as'	=>	'billing-list-view-fee-print-list',
			 'uses'	=>	'BillingController@getListViewFeePrintList']);
		
		Route::get('fee-print-organization-get',
			['as'	=>	'billing-fee-print-organization-get',
			 'uses'	=>	'BillingController@getFeePrintOrganization']);

		Route::get('fee-print-organization-list',
			['as'	=>	'billing-fee-print-organization-list',
			 'uses'	=>	'BillingController@getFeePrintOrganizationList']);


		Route::get('view-receipt-from-receipt-id/{receipt_id}',
			['as'	=>	'billing-view-receipt-from-receipt-id',
			 'uses'	=>	'BillingController@getViewReceiptFromReceiptId']);

		//apiGetIncomeReportView()

		Route::group(['before' => 'csrf'], function()
		{
			///////// billing-cancel-v1-changes /////////
			Route::post('cancel-invoice/{invoice_id}', 
				['as'	=>	'billing-cancel-invoice-post',
				 'uses'	=>	'BillingController@postCancelInvoice']);
			///////// billing-cancel-v1-changes /////////
			Route::post('create-extra-fees', 
			['as'	=>	'billing-create-extra-fee-post',
			'uses'	=>	'BillingController@postExtraCreateFeeView']);

			Route::post('create-fee',
				['as'	=>	'billing-create-fee-post',
				 'uses'	=>	'BillingController@postCreateFeeView']);

			Route::post('edit-fee/{id}',
			['as'	=>	'billing-edit-fee-post',
			 'uses'	=>	'BillingController@postEditFee']);

			Route::post('generate-fee',
				['as'	=>	'billing-generate-fee-post',
				 'uses'	=>	'BillingController@postGenerateFeeView']);

			Route::post('direct-invoice', 
			['as'	=> 'billing-direct-invoice-post',
			 'uses'	=>	'BillingController@postDirectInvoiceView']);
			 
			Route::post('direct-invoice-organization', 
			['as'	=> 'billing-direct-invoice-organization-post',
			 'uses'	=>	'BillingController@postDirectInvoiceOrganizationView']);

			Route::post('recieve-payment', 
			['as'	=>	'billing-recieve-payment-post',
			 'uses'	=>	'BillingController@postReceivePaymentView']);

			Route::post('delete-fee/{id}', 
			['as'	=>	'billing-delete-fee-post',
			 'uses'	=>	'BillingController@postDeleteFeeView']);

			Route::post('opening-balance', 
			['as'	=>	'billing-opening-balance-post',
			 'uses'	=>	'BillingController@postOpeningBalance']);

			Route::post('credit-note/{invoice_id}', 
			['as'	=> 'billing-credit-note-post',
			 'uses'	=>	'BillingController@postCreditNoteView']);

			Route::post('late-fee',
			['as' => 'billing-late-fee-post',
			'uses' => 'BillingController@postLateFee']);
		});

		
	});

	Route::group(['prefix' => 'api/v1/billing'], function()
	{

		Route::post('create-fee', 
			['as'	=>	'api-billing-create-fee-post',
			 'uses'	=>	'BillingController@apiPostCreateFee']);

	});

	

	Route::group(['prefix' => 'billing'], function()
	{

		Route::group(['prefix' => 'ajax'], function()
		{
			Route::get('api-show-receipts',
			['as'	=>	'api-billing-ajax-show-receipts-get',
			 'uses'	=>	'BillingController@apiGetTotalReceipts']);

			Route::get('get-edit-view/{id}',
				['as'	=>	'billing-ajax-get-edit-view',
				 'uses'	=>	'BillingController@ajaxGetEditFeeView'
				]);

			Route::get('get-class-list',
				['as'	=>	'billing-ajax-get-class-list',
				 'uses'	=>	'BillingController@ajaxGetClassList'
				]);

			Route::get('get-section-list',
				['as'	=>	'billing-ajax-get-section-list',
				 'uses'	=>	'BillingController@ajaxGetSectionList'
				]);

			Route::get('get-student-list',
				['as'	=>	'billing-ajax-get-student-list',
				 'uses'	=>	'BillingController@ajaxGetStudentList'
				]);

			//ajaxGetStudentListData
			Route::get('get-student-list-data',
				['as'	=>	'billing-ajax-get-student-list-data',
				 'uses'	=>	'BillingController@ajaxGetStudentListData'
				]);

			//ajaxGetStudentFeeListView()
			Route::get('get-student-fee-list-view',
				['as'	=>	'billing-ajax-get-student-fee-list-view',
				 'uses'	=>	'BillingController@ajaxGetStudentFeeListView'
				]);

			//ajaxGetStudentSelectList()
			Route::get('get-student-select-list',
				['as'	=>	'billing-ajax-get-student-select-list',
				 'uses'	=>	'BillingController@ajaxGetStudentSelectList'
				]);

			Route::get('get-student-remaining-due-view',
				['as'	=>	'billing-ajax-get-student-remaining-due-view',
				 'uses'	=>	'BillingController@ajaxGetStudentRemainingDueView'
				]);

			Route::get('get-opening-balance-student-list',
				['as'	=>	'billing-ajax-get-opening-balance-student-list',
				 'uses'	=>	'BillingController@apiGetStudentListView'
				]);

			//ajaxGetStudentFeeFromClassIdSectionIdStudentId()
			Route::get('get-student-fee-from-class-id-section-id-student-id',
				['as'	=>	'billing-ajax-get-student-fee-from-class-id-section-id-student-id',
				 'uses'	=>	'BillingController@ajaxGetStudentFeeFromClassIdSectionIdStudentId'
				]);

			//ajaxCreateEditStudentFee()
			Route::get('get-create-edit-student-fee',
				['as'	=>	'billing-ajax-get-create-edit-student-fee',
				 'uses'	=>	'BillingController@ajaxCreateEditStudentFee']);

			Route::get('calculate-tax',
				['as'	=>	'billing-ajax-calculate-tax',
				 'uses'	=>	'BillingController@ajaxCalculateTax'
				]);

			///////////////////////////////////////////////////////////////////
			Route::get('get-class-ids-from-session-id',
				['as'	=>	'billing-ajax-get-class-ids-from-session-id',
				 'uses'	=>	'BillingController@getClassIdsFromSessionId']);

			Route::get('get-section-ids-from-session-id-and-class-id',
				['as'	=>	'billing-ajax-get-section-ids-from-session-id-and-class-id',
				 'uses'	=>	'BillingController@getClassIdsFromSessionIdAndClassId']);
		});

	});

});

Route::group(['before' => 'reg-superadmin-admin-user'], function()
{
	Route::get('/api/billing/view-fee-of-student',
			['uses' => 'BillingController@apiGetListViewFeePrintList']);

	Route::get('/api/billing/view-statement-of-student',
		['uses' => 'BillingController@apiGetStudentStatement']);

	Route::group(['prefix' => 'billing'], function()
	{	
		Route::get('show-invoice-from-transaction-number/{transaction_number}', 
			['as'	=>	'show-invoice-from-transaction-number',
			 'uses'	=>	'BillingController@showInvoiceFromTransactionNumber']);

		Route::get('api-get-statement-list-view',
			['as'	=>	'billing-api-get-statement-list-view',
			 'uses'	=>	'BillingController@apiGetStatementListView']);
	});

});

require_once(app_path().'/modules/billing/billing-route.php');
require_once(app_path().'/modules/billing/advance-route.php');