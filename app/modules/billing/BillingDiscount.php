<?php

class BillingDiscount extends BaseModel
{
	protected $table = 'billing_discount';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingDiscount';



	public $createRule = [
	];

	public $updateRule = [
	];

	public function discountDetails()
	{
		return $this->hasMany('BillingDiscountDetails', 'discount_id', 'id');
	}
}