<?php

class DormitoryRoom extends BaseModel
{
	protected $table = 'dormitory_rooms';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'DormitoryRoom';

	public $createRule = [
							'dormitory_code' => array('required', 'unique:dormitory_rooms,dormitory_code'),
						];

	public $updateRule = [
							'dormitory_code' => array('required', 'unique:dormitory_rooms,dormitory_code'),
						];
}
?>