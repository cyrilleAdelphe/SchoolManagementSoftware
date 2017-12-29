<?php
class Events extends BaseModel
{
	protected $table = 'events';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'Events';

	public $createRule = [
							'title' 				=> ['required'],
							'venue' 				=> ['required'],
							'event_type'		=> ['required', 'in:holiday,exam,school_function'],
							'date'					=> ['required','regex:#^([\d]{2}[/][\d]{2}[/][\d]{4} [\d]{1,2}:[\d]{1,2} (AM|am|PM|pm) - [\d]{2}[/][\d]{2}[/][\d]{4} [\d]{1,2}:[\d]{1,2} (AM|am|PM|pm))$#'],
							'from_ad'			=>	['required', 'date_format:Y-m-d H:i:s'],
							'to_ad'			=>	['required', 'date_format:Y-m-d H:i:s'],
							'for'					=> ['required'],
							'for_students'			=> ['required','in:yes,no'],
							'for_teachers'			=> ['required','in:yes,no'],
							'for_management_staff'	=> ['required','in:yes,no'],
							'for_parents'			=> ['required','in:yes,no'],
							'description'			=> [],
							'event_code'			=> ['required', 'unique:events,event_code']
						];

	public $updateRule = [
							'title' 				=> ['required'],
							'venue' 				=> ['required'],
							'event_type'			=> ['required', 'in:holiday,exam,school_function'],
							'date'					=> ['required','regex:#^([\d]{2}[/][\d]{2}[/][\d]{4} [\d]{1,2}:[\d]{1,2} (AM|am|PM|pm) - [\d]{2}[/][\d]{2}[/][\d]{4} [\d]{1,2}:[\d]{1,2} (AM|am|PM|pm))$#'],
							'for'					=> ['required'],
							'for_students'			=> ['required','in:yes,no'],
							'for_teachers'			=> ['required','in:yes,no'],
							'for_management_staff'	=> ['required','in:yes,no'],
							'for_parents'			=> ['required','in:yes,no'],
							'description'			=> [],
							//'event_code'			=> ['required', 'unique:events,event_code']
						];
	public $defaultOrder = array('orderBy' => 'to_ad', 'orderOrder' => 'DESC');
}