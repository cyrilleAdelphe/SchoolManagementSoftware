<?php

class BlocksModel extends BaseModel
{
	protected $table = 'blocks';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'BlockModel';

	public $createRule = [
							'title' => ['required','unique:blocks,title'],
							'icon' => ['required','unique:blocks,icon'],
							'information' => ['required'],
							'order_index' => ['required','unique:blocks,order_index'],
							'class' => ['required']
						];

	public $updateRule = [
							'title' => ['required','unique:blocks,title'],
							'icon' => ['required','unique:blocks,icon'],
							'information' => ['required'],
							'order_index' => ['required','integer','unique:blocks,order_index'],
							'class' => ['required']
						];

	
}
