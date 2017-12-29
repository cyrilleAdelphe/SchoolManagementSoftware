<?php

class TransportationDistance extends BaseModel
{
	protected $table = 'transportation_distances';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'TransportationDistance';
}