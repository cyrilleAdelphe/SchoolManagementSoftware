<?php

Class Module extends BaseModel
{
	protected $table = 'modules';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Module';


	public $createRule = [ 'module_name'	=> array('required', 'unique:modules,module_name'),
						   'module_alias' => array('required', 'unique:modules,module_alias')];

	public $updateRule = [ 'module_name'	=> array('required', 'unique:modules,module_name'),
						   'module_alias' => array('required', 'unique:modules,module_alias') ];

	protected $defaultOrder = array('orderBy' => 'module_name', 'orderOrder' => 'ASC');
}