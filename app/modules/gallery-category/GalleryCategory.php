<?php

class GalleryCategory extends BaseModel
{
	protected $table = 'gallery_category';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'GalleryCategory';

	public $createRule = [
								'title'	=>	array('required', 'unique:gallery_category,title'),
								'is_active' => array('required', 'in:yes,no')
							];

	public $updateRule = [
								'title'	=>	array('required', 'unique:gallery_category,title'),
								'is_active' => array('required', 'in:yes,no')
							];
}
?>