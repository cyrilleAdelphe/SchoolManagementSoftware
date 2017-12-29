<?php

class Imei extends BaseModel
{
	protected $table = 'imei';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'Imei';

	public $timestamps = false;

	public $createRule = [
							
						];

	public $updateRule = [
							
						];
}
?>