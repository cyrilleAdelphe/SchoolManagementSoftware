<?php

Class Section extends BaseModel
{
	protected $table = 'sections';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Section';


	public $createRule = [ 'section_name'	=> array('required', 'unique:sections,section_name'),
						   'section_code' => array('required', 'unique:sections,section_code')];

	public $updateRule = [ 'section_name'	=> array('required', 'unique:sections,section_name'),
						   'section_code' => array('required', 'unique:sections,section_code') ];

	protected $defaultOrder = array('orderBy' => 'section_code', 'orderOrder' => 'ASC');
}