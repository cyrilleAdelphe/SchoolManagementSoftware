<?php

class BillingDiscountDetails extends BaseModel
{
	protected $table = 'billing_discount_details';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingDiscountDetails';



	public $createRule = [
	];

	public $updateRule = [
	];
}