<?php

Route::group(['prefix' => 'billing-discount', 'before' => 'reg-superadmin-admin'], function()
{
	Route::get('create-flat-discounts',
		['as'	=>	'billing-discount-create-flat-discounts-get',
		 'uses'	=>	'BillingDiscountController@getCreateFlatDiscountsView']);

	Route::get('create', 
		['as'	=>	'billing-discount-create-get',
		 'uses'	=>	'BillingDiscountController@getCreateView']);

	Route::get('edit/{id}', 
		['as'	=>	'billing-discount-edit-get',
		 'uses'	=>	'BillingDiscountController@getEditView']);

	Route::get('view/{id}', 
		['as'	=>	'billing-discount-view',
		 'uses'	=>	'BillingDiscountController@getViewView']);

	Route::get('list', 
		['as'	=>	'billing-discount-list',
		 'uses'	=>	'BillingDiscountController@getListView']);

	Route::get('organization-create', 
		['as'	=>	'billing-discount-organization-create-get',
		 'uses'	=>	'BillingDiscountController@getOrganizationCreateView']);

	Route::get('organization-edit/{id}', 
		['as'	=>	'billing-discount-organization-edit-get',
		 'uses'	=>	'BillingDiscountController@getOrganizationEditView']);

	Route::get('organization-view/{id}', 
		['as'	=>	'billing-discount-organization-view',
		 'uses'	=>	'BillingDiscountController@getOrganizationViewView']);

	Route::get('organization-list', 
		['as'	=>	'billing-discount-organization-list',
		 'uses'	=>	'BillingDiscountController@getOrganizationListView']);

	Route::group(['before' => 'csrf'], function()
	{
		Route::post('create-flat-discounts',
		['as'	=>	'billing-discount-create-flat-discounts-post',
		 'uses'	=>	'BillingDiscountController@postCreateFlatDiscountsView']);

		Route::post('create', 
		['as'	=>	'billing-discount-create-post',
		 'uses'	=>	'BillingDiscountController@postCreateView']);

		Route::post('edit/{id}', 
		['as'	=>	'billing-discount-edit-post',
		 'uses'	=>	'BillingDiscountController@postEditView']);

		Route::post('organization-create', 
		['as'	=>	'billing-discount-organization-create-post',
		 'uses'	=>	'BillingDiscountController@postOrganizationCreateView']);

		Route::post('organization-edit/{id}', 
		['as'	=>	'billing-discount-organization-edit-post',
		 'uses'	=>	'BillingDiscountController@postOrganizationEditView']);

		Route::post('delete-discount/{id}', 
			['as'	=>	'billing-delete-discount-post',
			 'uses'	=>	'BillingDiscountController@postDeleteDiscountView']);

		Route::post('delete-organization/{id}', 
			['as'	=>	'billing-delete-organization-post',
			 'uses'	=>	'BillingDiscountController@postDeleteOrganizationView']);
	});


});

Route::get('ajax-student-id-autocomplete', 
	['as'	=>	'ajax-student-id-autocomplete',
	 'uses'	=>	'BillingDiscountController@ajaxStudentIdAutoComplete']);
