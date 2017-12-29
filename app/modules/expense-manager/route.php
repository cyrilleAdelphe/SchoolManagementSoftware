<?php

Route::group(array('prefix'=>'expense-manager'),function() {

	Route::get('/expenses',array(
		'as'	=>'expense-list',
		'uses'	=>'ExpenseManagerController@getExpenseList'
		));

	Route::get('/accounts', array(
		'as'	=>'accounts-list',
		'uses'  =>'ExpenseManagerController@getAccountList'
		));

	Route::get('/cash', array(
		'as'	=>'cash-list',
		'uses'	=>'ExpenseManagerController@getCashList'
		));

	Route::get('/expense-note/{id}', array(
		'as'	=>'expense-notes',
		'uses'	=>'ExpenseManagerController@getExpenseNote'
		));

	Route::get('/expense-edit/{id}', array(
		'as'	=>'expense-edit',
		'uses'	=>'ExpenseManagerController@getExpenseEdit'
		));

	Route::get('/account-edit/{id}', array(
		'as'	=>'account-edit',
		'uses'	=>'ExpenseManagerController@getAccountEdit'
		));

	Route::get('/income-type', array(
		'as'	=>'income-type',
		'uses'	=>'ExpenseManagerController@getIncomeType'
		));

	Route::get('/transfer', array(
		'as'	=>'transfer',
		'uses'	=>'ExpenseManagerController@getTransfer'
		));

	Route::get('/transfer-info/{id}', array(
		'as'	=>'transfer-info',
		'uses'	=>'ExpenseManagerController@getTransferInfo'
		));

	Route::get('/transfer-edit/{id}', array(
		'as'	=>'transfer-edit',
		'uses'	=>'ExpenseManagerController@getTransferEdit'
		));

	Route::get('/expense-delete/{id}', array(
		'as'	=>'expense-delete',
		'uses'	=> 'ExpenseManagerController@getExpenseDelete'
		));

	Route::get('/cash-delete/{id}', array(
		'as'	=>'cash-delete',
		'uses'	=>'ExpenseManagerController@getCashDelete'
		));

	Route::get('/account-delete/{id}', array(
		'as'	=>'account-delete',
		'uses'	=>'ExpenseManagerController@getAccountDelete'
		));

	Route::get('/search-expense', array(
		'as'	=>'search-expense',
		'uses'	=>'ExpenseManagerController@searchExpense'
		));

	Route::get('/remaining-dues', array(
		'as'	=>'remaining-dues',
		'uses'  =>'ExpenseManagerController@getRemainingDues'
		));


	Route::get('/search-cash', array(
		'as'	=>'search-cash',
		'uses'	=>'ExpenseManagerController@searchCash'
		));


	Route::get('/search-date-expense', array(
		'as'	=>'search-date-expense',
		'uses'	=>'ExpenseManagerController@searchDateExpense'

		));

	
	Route::get('/search-date-cash', array(
		'as'	=>'search-date-cash',
		'uses'	=>'ExpenseManagerController@searchDateCash'
		));

	
	Route::post('/add-income', array(
		'as'	=>'add-income',
		'uses'	=>'ExpenseManagerController@addIncome'
		));

	
	Route::group(array('before'=>'csrf'), function() {

		Route::post('/create-expense', array(
		'as'	=>'create-expense',
		'uses'	=>'ExpenseManagerController@postCreateExpense'
		));		

		Route::post('/update-expense/{id}', array(
		'as'	=>'edit-expense',
		'uses'	=>'ExpenseManagerController@postExpenseEdit'
		));

		Route::post('/create-account', array(
		'as'	=>'create-account',
		'uses'	=>'ExpenseManagerController@postcreateAccount'
		));

		Route::post('/edit-account/{id}', array(
		'as'	=>'edit-account',
		'uses'	=>'ExpenseManagerController@postAccountEdit'
		));

		Route::post('/cash-transfer', array(
		'as'	=>'cash-transfer',
		'uses'	=>'ExpenseManagerController@postCashTransfer'
		));

		Route::post('/transfer-edit/{id}', array(
		'as'	=>'transfer-edit',
		'uses'	=>'ExpenseManagerController@postTransferEdit'
		));


	});

});
