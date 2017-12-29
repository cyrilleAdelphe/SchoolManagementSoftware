<?php

class BillingExtraFees extends BaseModel
{
	protected $table = 'billing_extra_fees';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingExtraFees';



	public $createRule = [
	];

	public $updateRule = [
	];
}