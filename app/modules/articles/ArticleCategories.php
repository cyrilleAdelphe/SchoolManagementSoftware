<?php
class ArticleCategories extends BaseModel
{
	protected $table = 'article_categories';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'ArticleCategories';

	public $createRule = [
							'title' => ['required','unique:article_categories,title'],
							'frontend_publishable' => ['required'],
						];

	public $updateRule = [
							'title' => ['required'],
							'frontend_publishable' => ['required'],
						];
}