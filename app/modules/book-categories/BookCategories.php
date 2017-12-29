<?php


class BookCategories extends BaseModel
{
	protected $table = 'book_categories';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'BookCategories';

	public $createRule = [
							'title' 		=> ['required'],
							//'class_range'	=> ['regex:/^[\d]{1,}-[\d]{1,}$/'],
							'description'	=> []
						];

	public $updateRule = [
							'title' 		=> ['required'],
							//'class_range'	=> ['regex:/^[\d]{1,}-[\d]{1,}$/'],
							'description'	=> []
						];
}