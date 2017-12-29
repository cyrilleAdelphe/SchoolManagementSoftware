<?php

class ListModel extends BaseModel
{
	protected $table = 'list';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'ListModel';

	public $createRule = [
							'title' => ['required','unique:list,title'],
							'icon' => ['required','unique:list,icon'],
							'information' => ['required'],
							'order_index' => ['required','unique:list,order_index'],
							

						];

	public $updateRule = [
							'title' => ['required','unique:list,title'],
							'icon' => ['required','unique:list,icon'],
							'information' => ['required'],
							'order_index' => ['required','integer','unique:list,order_index'],
							
						];

	
}
