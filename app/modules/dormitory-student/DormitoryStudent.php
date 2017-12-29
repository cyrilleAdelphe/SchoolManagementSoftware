<?php

class DormitoryStudent extends BaseModel
{
	protected $table = 'dormitory_students';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'DormitoryStudent';

	public $createRule = [
							'student_id' => array('required', 'exists:student_registration,id', 'unique_with:dormitory_students,academic_session_id'),	
							'dormitory_id' => array('required', 'exists:dormitory_rooms,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id'),
							'type' => array('required', 'in:day,full'),
							'fee_amount' => array('integer', 'min:0')
						];

	public $updateRule = [
							'student_id' => array('required', 'exists:student_registration,id', 'unique_with:dormitory_students,academic_session_id'),	
							'dormitory_id' => array('required', 'exists:dormitory_rooms,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id'),
							'type' => array('required', 'in:day,full'),
							'fee_amount' => array('required', 'integer', 'min:0')
						];
}
?>