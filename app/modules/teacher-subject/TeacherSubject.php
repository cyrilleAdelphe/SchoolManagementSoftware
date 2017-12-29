<?php

class TeacherSubject extends BaseModel
{
	protected $table = 'teacher_subjects';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'TeacherSubject';
}