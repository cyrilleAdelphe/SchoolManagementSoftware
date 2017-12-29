<?php

class Position extends BaseModel
{
	protected $table = 'positions';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Position';


	public $createRule = [ 'position_name'	=> array('required', 'unique:positions,position_name',)
						  ];

	public $updateRule = ['position_name'	=> array('required', 'unique:positions,position_name',)
						   ];
}