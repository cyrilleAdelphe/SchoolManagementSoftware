<?php

class Slides extends BaseModel
{
	protected $table = 'slides';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'Slides';

	public $createRule = [
							'slide_no'		=> ['required','integer','unique:slides,slide_no'],
							'title'			=> ['required'],
							'text'			=> ['required'],
							'link'			=> ['url'],
							'profile_pic'	=> ['required','mimes:png,jpg,jpeg,mp4'],
							'is_active'		=> ['required', 'in:yes,no'],
							
						];

	public $updateRule = [
							'slide_no'		=> ['required','integer','unique:slides,slide_no'],
							'title'			=> ['required'],
							'text'			=> ['required'],
							'link'			=> ['url'],
							'is_active'		=> ['required', 'in:yes,no'],
							
						];


	
}
?>