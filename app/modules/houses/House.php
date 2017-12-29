<?php

class House extends BaseModel
{
	protected $table = 'houses';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'House';

	public $rules = [
		'house_name' => 'required',
		'is_active'  => 'required',
		'house_code' =>'required'
	];
	
}
