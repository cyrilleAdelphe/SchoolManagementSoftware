<?php

class TransportationStudent extends BaseModel
{
	protected $table = 'transportation_students';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'TransportationStudent';

	public $createRule = ['student_id' => array('required', 'exists:student_registration,id', 'unique:transportation_students,student_id'),
				 'transportation_id'	=>	array('required'),
				 'fee_amount' => array('required', 'integer', 'min:0')];

	public $updateRule = ['student_id' => array('required', 'exists:student_registration,id', 'unique:transportation_students,student_id'),
				 'transportation_id'	=>	array('required'),
				 'fee_amount' => array('required', 'integer', 'min:0')];
}
