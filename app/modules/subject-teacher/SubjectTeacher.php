<?php

Class SubjectTeacher extends BaseModel
{
	protected $table = 'map_subject_teachers';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'SubjectTeacher';


	public $createRule = [ 'subject_id'	=> array('required', 'exists:subjects,id'),
						   'teacher_id' => array('required', 'exists:teachers,id'),
						   'session_id' => array('required'),
						   'class_id' => array('required'),
						   'section_id' => array('required')
						   ];

	public $updateRule = [ 'subject_id'	=> array('required', 'exists:subjects,id'),
						   'teacher_id' => array('required', 'exists:teachers,id'),
						   'session_id' => array('required'),
						   'class_id' => array('required'),
						   'section_id' => array('required')];

	protected $defaultOrder = array('orderBy' => 'id', 'orderOrder' => 'DESC');

	
}