<?php
class CasGradeSettings extends BaseModel
{
	protected $table = 'cas_grading_settings';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'CasGradeSettings';

	public $createRule = [
							
						];

	public $updateRule = [
							
						];
}