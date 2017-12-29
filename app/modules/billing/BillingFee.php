<?php

class BillingFee extends BaseModel
{
	protected $table = 'billing_fees';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingFee';



	public $createRule = ['fee_category' => ['required', 'unique:billing_fees'],
		'tax_applicable' => ['required', 'in:yes,no'],
		'fee_type' => ['required']
	];

	public $updateRule = ['fee_category' => ['required', 'unique:billing_fees'],
		'tax_applicable' => ['required', 'in:yes,no'],
		'fee_type' => ['required']
	];

	public function getEditViewData($id)
	{
		$data = BillingFee::where('id', $id)->with('studentFee')->first();
		return $data;
	}

	public function studentFee()
	{
		return $this->hasMany('BillingFeeStudent', 'fee_id', 'id')->orderBy('class_id', 'ASC', 'section_id', 'ASC');
	}

	

}