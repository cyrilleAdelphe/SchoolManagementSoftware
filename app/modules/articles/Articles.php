<?php
class Articles extends BaseModel
{
	protected $table = 'articles';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'Articles';

	public $createRule = [
							'title' 	=> ['required'],
							'alias' 	=> ['required','unique:articles,alias'],
							'content' 	=> ['required'],
							'category_id' => ['required','exists:article_categories,id'],
							// 'meta_tag'	=> ['required'],
							// 'meta_description'	=> ['required']
						];

	public $updateRule = [
							'title' => ['required'],
							'alias' => ['required'],//the new alias may be the same as the previous alias or a completely new one!!!
							'content' => ['required'],
							'category_id' => ['required','exists:article_categories,id'],
							// 'meta_tag'	=> ['required'],
							// 'meta_description'	=> ['required']
						];
}