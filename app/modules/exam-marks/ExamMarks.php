<?php

Class ExamMarks extends BaseModel
{
	protected $table = 'exam_marks';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'ExamMarks';


	public $createRule = [
							'student_id'	=> array('required', 'exists:student_registration,id'),
							'subject_id'	=> array('required', 'exists:subjects,id'),
							'marks'			=> array('required', 'integer', 'min:0')
						];

	public $updateRule = [
							'student_id'	=> array('required', 'exists:student_registration,id'),
							'subject_id'	=> array('required', 'exists:subjects,id'),
							'marks'			=> array('required', 'integer', 'min:0')
						];

	protected $defaultOrder = array('orderBy' => 'subject_id', 'orderOrder' => 'ASC');

	
}