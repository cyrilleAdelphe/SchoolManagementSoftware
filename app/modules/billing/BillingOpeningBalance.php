<?php

class BillingOpeningBalance extends BaseModel
{
	protected $table = 'billing_opening_balance';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingOpeningBalance';



	public $createRule = [
	];

	public $updateRule = [
	];
}