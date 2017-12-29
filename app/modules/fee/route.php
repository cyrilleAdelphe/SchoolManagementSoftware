<?php

Route::group(array('prefix'=>'fee', 'before'=>'reg-superadmin-admin'), function()
{
	Route::get('generate-fees', 
		[
			'as'	=>	'fee-generate-get',
			'uses'	=>	'FeeController@getGenerate'
		]);

	Route::get('update-payment',
		[
			'as'	=>	'fee-update-payment-get',
			'uses'	=>	'FeeController@getUpdatePayment'
		]);

	Route::get('fee-individual',
		[
			'as'	=>	'fee-fee-individual-get',
			'uses'	=>	'FeeController@getFeeIndividual'
		]);

	Route::get('fee-class', 
		[
			'as'	=> 'fee-class-get',
			'uses'	=>	'FeeController@getFeeClass'
		]);

	Route::get('fee-detail', 
		[
			'as'	=> 'fee-detail-get',
			'uses'	=>	'FeeController@getFeeDetail'
		]);

	Route::get('mass-print/{class_id}/{section_id}/{month}', 
		[
			'as'	=> 'fee-mass-print',
			'uses'	=>	'FeeController@massPrintFee'
		]);


	Route::group(array('before'=>'csrf'), function()
	{
		Route::get('update-payment-form',
			[
				'as'	=>	'fee-update-payment-form-get',
				'uses'	=>	'FeeController@getUpdatePaymentForm'
			]);

		Route::get('fee-individual-info',
			[
				'as'	=>	'fee-fee-individual-info-get',
				'uses'	=>	'FeeController@getFeeIndividualInfo'
			]);

		Route::get('fee-class-info',
			[
				'as'	=>	'fee-fee-class-info-get',
				'uses'	=>	'FeeController@getFeeClassInfo'
			]);

		Route::get('fee-detail-info',
			[
				'as'	=>	'fee-fee-detail-info-get',
				'uses'	=>	'FeeController@getFeeDetailInfo'
			]);

		Route::get('/fee-defaulter-notification', [
			'as'	=> 'fee-defaulter-notification',
			'uses' => 'FeeController@defaulterNotification'
		]);

		Route::post('generate-fees', 
			[
				'as'	=>	'fee-generate-post',
				'uses'	=>	'FeeController@postGenerate'
			]);

		Route::post('update-payment',
			[
				'as'	=>	'fee-update-payment-post',
				'uses'	=>	'FeeController@postUpdatePayment'
			]);

	});
});