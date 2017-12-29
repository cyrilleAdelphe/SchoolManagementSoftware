<?php

class TransportationStaff extends BaseModel
{
	protected $table = 'transportation_staffs';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'TransportationStaff';

	public $createRule = ['employee_id' => array('required', 'exists:admins,admin_details_id', 'unique:transportation_staffs,employee_id'),
				 'transportation_id'	=>	array('required')];

	public $updateRule = ['employee_id' => array('required', 'exists:admins,admin_details_id', 'unique:transportation_staffs,employee_id'),
				 'transportation_id'	=>	array('required')
				];
}
