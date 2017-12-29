<?php

class Menus extends BaseModel
{
	protected $table = 'menus';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'Menus';

	public $createRule = [
							'title' => ['required'],
							'alias' => ['required','unique:menus,alias'],
							'order_index' => ['required','integer'],
							
						];

	public $updateRule = [
							'title' => ['required'],
							'alias' => ['required','unique:menus,alias'],
							'order_index' => ['required','integer'],
							
						];

	
}
?>