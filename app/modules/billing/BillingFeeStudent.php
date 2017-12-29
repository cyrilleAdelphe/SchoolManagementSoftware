<?php

class BillingFeeStudent extends BaseModel
{
	protected $table = 'billing_fee_student';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingFeeStudent';



	public $createRule = [
	];

	public $updateRule = [
	];

	

}