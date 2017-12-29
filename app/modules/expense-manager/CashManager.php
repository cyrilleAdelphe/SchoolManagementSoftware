<?php

class CashManager extends BaseModel {

	protected $table = 'cash_in_hand';

	protected $model_name = 'CashManager';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public $rules = [
		'date'		=> 'required',
		'amount'	=> 'required|numeric|between:0,99999999.99'

	];
	
}