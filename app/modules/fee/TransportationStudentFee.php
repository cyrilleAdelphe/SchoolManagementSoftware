<?php

class TransportationStudentFee extends BaseModel
{
	protected $table = 'fee_transportation_student';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'TransportationStudentFee';



	public $createRule = [
							'student_id' => array('required', 'exists:student_registration,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id'),
							'amount' => array('required', 'integer', 'min:0'),
							'month' => array('required', 'integer', 'min:1', 'max:12'),
							'transportation_student_id' => array('required', 'exists:transportation_students,id')
						];

	public $updateRule = [
							'student_id' => array('exists:student_registration,id'),
							'academic_session_id' => array('exists:academic_session,id'),
							'amount' => array('required', 'integer', 'min:0'),
							'month' => array('integer', 'min:1', 'max:12'),
							'transportation_student_id' => array('exists:transportation_students,id')
						];

}