<?php
class Student extends BaseModel
{
	protected $table = 'students';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	public $model_name = 'Student';
	
	public $createRule = [
							'student_id' => ['required'],
							'current_session_id' => ['required','not_in:0'],
							'current_class_id' => ['required', 'not_in:0'],
							'current_section_code' => ['required', 'not_in:0'],
							'current_roll_number' => ['required', 'unique_with:students,current_session_id,current_class_id,current_section_code'],
							'is_active' => ['required','in:yes,no']
						];

	public $updateRule = [
							'student_id' => ['required'],
							'current_session_id' => ['required','not_in:0'],
							'current_class_id' => ['required', 'not_in:0'],
							'current_section_code' => ['required', 'not_in:0'],
							'current_roll_number' => ['required'],
							'current_roll_number' => ['required', 'unique_with:students,current_session_id,current_class_id,current_section_code'],
							'is_active' => ['required','in:yes,no']
						];
}