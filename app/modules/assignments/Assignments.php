<?php

class Assignments extends BaseModel
{
	protected $table = 'download_manager';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'Assignments';

	public $createRule = [
							'fileToUpload' => ['required'],
							'academic_session_id' => ['required','exists:academic_session,id'],
							'class_id' => ['required','exists:classess,id'],
							'section_id' => ['required','exists:sections,id'],
							'subject_id' => ['required','exists:subjects,id']
						];

	public $updateRule = [
							'fileToUpload' => ['required'],
							'academic_session_id' => ['required','exists:academic_session,id'],
							'class_id' => ['required','exists:classess,id'],
							'section_id' => ['required','exists:sections,id'],
							'subject_id' => ['required','exists:subjects,id']
						];
}
?>