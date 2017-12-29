<?php

class ModuleController extends BaseController
{
	protected $view = 'module.views.';

	protected $model_name = 'Module';

	protected $module_name = 'module';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'module_name',
										'alias'			=> 'Module Name'
									),
									array
									(
										'column_name' 	=> 'module_alias',
										'alias'			=> 'Module Alias'
									),
									array
									(
										'column_name' 	=> 'is_active',
										'alias'			=> 'Is Active'
									)
								 );
}
