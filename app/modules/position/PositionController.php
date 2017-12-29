<?php

class PositionController extends BaseController
{
	protected $view = 'position.views.';

	protected $model_name = 'Position';

	protected $module_name = 'position';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'position_name',
										'alias'			=> 'Position Name'
									)
								 );

	
}
