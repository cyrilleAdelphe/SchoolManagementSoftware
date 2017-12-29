<?php

class FeeTax extends BaseModel
{
	protected $table = 'fee_taxes';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'FeeTax';
	
	public $createRule = [
							'student_id' => array('required', 'exists:student_registration,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id'),
							'amount' => array('required', 'integer', 'min:0'),
							'month' => array('required', 'integer', 'min:1', 'max:12'),
						];

	public $updateRule = [
							'student_id' => array('exists:student_registration,id'),
							'academic_session_id' => array('exists:academic_session,id'),
							'amount' => array('required', 'integer', 'min:0'),
							'month' => array('integer', 'min:1', 'max:12'),
						];

}