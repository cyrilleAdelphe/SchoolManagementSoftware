<?php

class DailyRoutine extends BaseModel
{
	protected $table = 'daily_routine';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'DailyRoutine';



	public $createRule = [
							'session_id'	=>	['required'],
							'class_id'		=>	['required'],
							'section_id'	=>	['required'],
							'teacher'		=>	['required'],
							'subject'		=>	['required'],
							'day'			=>	['required'],
							//'period'		=>	['required'],
							'start_time'	=>	['required'],
							'end_time'		=>	['required']

						 ];

	public $updateRule = [
							'teacher'		=>	['required'],
							'subject'		=>	['required'],
							//'period'		=>	['required'],
							'start_time'	=>	['required'],
							'end_time'		=>	['required']
						];


	public function getListViewData($queryString = array())
	{
		$session_id = Input::get('session_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$day = Input::get('day', 'Sunday');

		$data = DailyRoutine::where('class_id', $class_id)
							->where('section_id', $section_id)
							->where('session_id', $session_id)
							->where('is_active', 'yes')
							->where('day', $day)
							->orderBy('start_time', 'ASC')
							->get();

		return $data;
	}

	public function getEditView()
	{
		$session_id = Input::get('session_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$day = Input::get('day', 'Sunday');

		$data = DailyRoutine::where('class_id', $class_id)
							->where('section_id', $section_id)
							->where('session_id', $session_id)
							->where('is_active', 'yes')
							->where('day', $day)
							->orderBy('start_time', 'ASC')
							->get();

		return $data;
	}

	
}