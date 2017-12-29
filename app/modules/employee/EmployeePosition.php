<?php

class EmployeePosition extends BaseModel
{
	protected $table = 'employee_position';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'EmployeePosition';


	public $createRule = [];

	public $updateRule = [];

	protected $defaultOrder = array('orderBy' => 'employee_id', 'orderOrder' => 'ASC');
}