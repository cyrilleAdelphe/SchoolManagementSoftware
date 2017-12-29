<?php

class JsonConfigurationController extends Controller
{
	//get the config file
	public function showConfigFile($filename)
	{
		$current_user = HelperController::getCurrentUser();
		//$role = HelperController::getUserRole();

		$content = array();
		try
		{
			$content = File::get(app_path().'/modules/configuration/configs/'.$filename.'.json');
			$content = json_decode($content, true);
			$status = 'success';
			$msg = '';
		}
		catch(Exception $e)
		{
			$status = 'error';
			$msg = 'file not found';//$e->getMessage();
		}

		return View::make('configuration.views.config')
					->with('content', $content)
					->with('status', $status)
					->with('msg', $msg)
					->with('current_user', $current_user)
					->with('filename', $filename);
	}

	public function updateConfigurationFile($filename)
	{
		$data = array();
		
		$input = Input::all();
		//print_r($input);
		
		$data['header'] = $input['header'];
		foreach($input['category_type_name'] as $index => $category_name)
		{
			$data['category_groups'][$category_name]['group_display_name'] = $input['display_name'][$index];
			$data['category_groups'][$category_name]['display'] = $input['display'][$index];

			foreach($input[$category_name.'_field_name'] as $_index => $field_name)
			{
				$temp = array();
				$temp['field_name'] = $field_name;
				$temp['field_value'] = $input[$category_name.'_field_value'][$_index];
				$temp['display_name'] = $input[$category_name.'_display_name'][$_index];
				$data['category_groups'][$category_name]['fields'][] = $temp;
			}

		}

		//echo json_encode($data);
		try
		{
			$content = File::put(app_path().'/modules/configuration/configs/'.$filename.'.json', json_encode($data, JSON_PRETTY_PRINT));
			$status = 'success';
			$msg = '';
			Session::flash('success-msg', 'Configuration successfully updated');
		}
		catch(Exception $e)
		{
			$status = 'error';
			$msg = 'file not found';//$e->getMessage();
			Session::flash('error-msg', 'Oops! something went wrong. Please try again');
		}

		return Redirect::back();
	}

	public static function getFieldValue($filename, $category_group, $field_name)
	{
		$status = 'error';
		$msg = 'Parameter not found';
		try
		{
			$content = File::get(app_path().'/modules/configuration/configs/'.$filename.'.json');
			$content = json_decode($content, true);
			$content = $content['category_groups'][$category_group]['fields'];

			foreach($content as $c)
			{
				if($c['field_name'] == $field_name)
				{
					return $c['field_value'];
					$status = 'success';
					break;
				}
			}
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
		}

		App::abort(403, $msg);
	}
}