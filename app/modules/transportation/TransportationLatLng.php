<?php

class TransportationLatLng extends BaseModel
{
	protected $table = 'transportation_lat_lng';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'TransportationLatLng';
}