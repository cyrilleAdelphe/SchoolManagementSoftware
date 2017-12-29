<?php

class StaffRequest extends BaseModel {
	protected $table = 'staff_requests';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'StaffRequest';

	public $createRule = [ 
		'request_type'		=> array('required', 'in:requisition,leave'),
		'message'		=> array('required'),
	  'message_from_group' => array('required', 'in:student,guardian,admin,superadmin'),
	  'message_from_id' => array('required'),

	];

	public $updateRule = [ 
		'message'		=> array('required'),
		'message_from_group' => array('required', 'in:student,guardian,admin,superadmin'),
	  'message_from_id' => array('required'),
	  'is_approved' => array('required', 'in:yes,no')
	];


}