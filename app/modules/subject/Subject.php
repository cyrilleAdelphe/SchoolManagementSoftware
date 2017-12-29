<?php

Class Subject extends BaseModel
{
	protected $table = 'subjects';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Subject';


	public $createRule = [ 'subject_name'	=> array('required', 'unique_with:subjects,class_id,section_id'),
						   'subject_code' => array('required', 'unique_with:subjects,class_id,section_id'),
						   //'teacher_id'	=>	array('required'),
						   'class_id'	=> array('required'),
						   'section_id'	=> array('required'),
						   'full_marks'	=> array('required', 'integer'),
						   'pass_marks'	=> array('required', 'integer')];

	public $updateRule = [ 'subject_name'	=> array('required', 'unique_with:subjects,class_id,section_id'),
						   'subject_code' => array('required', 'unique_with:subjects,class_id,section_id'),
						   //'teacher_id'	=>	array('required'),
						  // 'class_id'	=> array('required'),
						   //'section_id'	=> array('required'),
						   'full_marks'	=> array('required', 'integer'),
						   'pass_marks'	=> array('required', 'integer')];

	protected $defaultOrder = array('orderBy' => 'subject_code', 'orderOrder' => 'ASC');

	
}