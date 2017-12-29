<?php
class CasSubTopics extends BaseModel
{
	protected $table = 'cas_subjects_subtopics';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'CasSubTopics';

	public $createRule = [
							
						];

	public $updateRule = [
							
						];
}