<?php

Route::group(['before' => 'reg-superadmin-admin'], function()
{
	Route::group(['prefix' => 'advance-billing'], function()
	{	
		Route::get('/create',
			['as'	=>	'advance-billing-create-get',
			 'uses'	=>	'AdvanceBillingPaymentController@getAdvanceBillingView']);


			Route::group(['before' => 'csrf'], function()
			{
				Route::post('/create',
			['as'	=>	'advance-billing-create-post',
			 'uses'	=>	'AdvanceBillingPaymentController@postAdvanceBillingView']);
			});
	});
});
		