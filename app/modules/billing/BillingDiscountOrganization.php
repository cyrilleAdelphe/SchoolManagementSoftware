<?php

class BillingDiscountOrganization extends BaseModel
{
	protected $table = 'billing_discount_organization';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingDiscountOrganization';



	public $createRule = [
	];

	public $updateRule = [
	];

	public function getEditViewData($id)
	{
		$data = BillingDiscountOrganization::where('id', $id)
											->first();

		return $data;
	}

	public function getListViewData($queryString)
	{
		$data = BillingDiscountOrganization::get();

		return $data;
	}

}