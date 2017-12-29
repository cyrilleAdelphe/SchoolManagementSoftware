<?php

class GradeController extends BaseController
{
	protected $view = 'grade.views.';

	protected $module_name = 'grade';

	protected $role;

	public function getUpdate()
	{
		AccessController::allowedOrNot('config', 'can_view,can_edit,can_create');
		$config = json_decode(File::get(GRADE_CONFIG_FILE));

		return View::make($this->view.'.update')
					->with('config', $config)
					->with('role', $this->role);
	}

	public function postUpdate()
	{
		AccessController::allowedOrNot('config', 'can_edit,can_create');
		$from = Input::get('from');
		$to = Input::get('to');
		$grade = Input::get('grade');
		$grade_point = Input::get('grade_point');

		$output = array();

		for($i = 0; $i < count($from); $i++)
		{
			$current_output = array(
				'from'	=>	$from[$i],
				'to'		=>	$to[$i],
				'grade'	=>	$grade[$i],
				'grade_point'	=>	$grade_point[$i],
			);

			if (
				!strlen($current_output['from']) || 
				!strlen($current_output['to']) ||
				!strlen($current_output['grade']) ||
				!strlen($current_output['grade_point'])
			)
			{
				continue;
			}

			$validator = Validator::make(
				$current_output,
				array(
					'from' => ['required','numeric','min:0', 'max:100'],
					'to' => ['required','numeric','min:0', 'max:100'],
					'grade' => ['required', 'max:2'],
					'grade_point' => ['required','numeric','min:0', 'max:4'],
				)
			);

			if($validator->fails())
			{
				Session::flash('error-msg', 'Validation Error');
				//redirect with error message
				return Redirect::back()
					->withErrors($validator)
					->with('error_index', $i);
			}
				
			$output[] = $current_output;
		}

		$json_string = json_encode($output, JSON_PRETTY_PRINT);

		if(File::put(GRADE_CONFIG_FILE, $json_string))
		{
			Session::flash('success-msg', 'config saved');
		}
		else
		{
			Session::flash('error-msg', 'error!');
		}

		return Redirect::back();
	}
}