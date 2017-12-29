<?php
class CasExamMark extends BaseModel
{
	protected $table = 'cas_exam_marks';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'CasExamMark';

	public $createRule = [
							
						];

	public $updateRule = [
							
						];
}