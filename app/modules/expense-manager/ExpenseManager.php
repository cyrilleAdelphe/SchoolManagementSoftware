<?php


class ExpenseManager extends BaseModel {

	protected $table = 'expense';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'ExpenseManager';

	public $rules = [
		'title'			=> 'required',
		'account_id'	=> 'required',
		'paid_to'		=> 'required',
		'amount'		=> 'required|numeric|between:0,99999999.99',
		'payment_type'	=> 'required',
		'payment_date'	=> 'required',
		'pic'			=> 'mimes:jpg,jpeg,png,bmp'

	];	

	
}