<?php

class Ethnicity extends BaseModel
{
	protected $table = 'ethnicity';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'Ethnicity';

	public $rules = [
		'ethnicity_name' => 'required',
		'is_active'  => 'required',
		'ethnicity_code' =>'required'
	];	
	
}
