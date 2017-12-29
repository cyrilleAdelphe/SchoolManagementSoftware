<?php

class MiscClassStudentFee extends BaseModel
{
	protected $table = 'fee_misc_class_student';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'MiscClassStudentFee';

	public $createRule = [
							'student_id' => array('required', 'exists:student_registration,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id'),
							'amount' => array('required', 'integer', 'min:0'),
							'month' => array('required', 'integer', 'min:1', 'max:12'),
							'fee_misc_class_id' => array('required', 'exists:fee_misc_class,id')
						];

	public $updateRule = [
							'student_id' => array('exists:student_registration,id'),
							'academic_session_id' => array('exists:academic_session,id'),
							'amount' => array('required', 'integer', 'min:0'),
							'month' => array('integer', 'min:1', 'max:12'),
							'fee_misc_class_id' => array('exists:fee_misc_class,id')
						];

}