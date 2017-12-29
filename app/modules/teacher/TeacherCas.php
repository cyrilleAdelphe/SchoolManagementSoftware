<?php


class TeacherCas extends BaseModel 
{

	protected $table = 'cas_subjects_subtopics';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Teacher';


	/*public $createRule = [ 'teacher_id'	=> array('required', 'min:1'),
						   'session_id'	=> array('required', 'min:1'),
						   'is_class_teacher' => array('required', 'in:yes,no'),
						   'class_id' => array('required', 'min:1'),
						   'section_code' => array('required', 'not_in:0'),
						   'is_active' => array('required', 'in:yes,no'),
						  ];

	public $updateRule = [ 'teacher_id'	=> array('required', 'min:1'),
						   'session_id'	=> array('required', 'min:1'),
						   'is_class_teacher' => array('required', 'in:yes,no'),
						   'class_id' => array('required', 'min:1'),
						   'section_code' => array('required', 'not_in:0'),
						   'is_active' => array('required', 'in:yes,no'),
						  ];
*/
	//protected $defaultOrder = array('orderBy' => 'sort_order', 'orderOrder' => 'ASC');

	
}