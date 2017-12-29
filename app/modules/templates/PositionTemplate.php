<?php

class PositionTemplate extends BaseModel
{
	protected $table = 'position_template';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'PositionTemplate';
}