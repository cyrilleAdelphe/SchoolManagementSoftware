<?php

class SettingsController  extends BaseController {
	protected $view = 'settings.views.';

	protected $module_name = 'settings';

	public $current_user;

	public $role;

	public function getGeneral() {

		AccessController::allowedOrNot($this->module_name, 'can_view');

		if(File::exists(GENERAL_SETTINGS))
		{
			$general_settings = json_decode(File::get(GENERAL_SETTINGS));
		}
		else
		{
			die('General settings not created. Contact site administrators');
		}
		return View::make($this->view . 'general')
				->with('role', $this->role)
				->with('general_settings', $general_settings);
	}

	public function postGeneral()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$validator = Validator::make(
										Input::all(),
										array(
												'long_school_name'	=> ['required'],
												'short_school_name'	=> ['required'],
												'address'	=> ['required'],
												'school_logo' => ['image']
											)
									);
		if($validator->fails())
		{
			Session::flash('error-msg', 'Validation Error!!');
			return Redirect::back()
					->withInput()
					->withErrors($validator->messages());
		}

		$general_settings = new stdClass;
		
		$general_settings = Input::all();
		unset($general_settings['_token']);
		unset($general_settings['school_logo']);

		if (Input::hasFile('school_logo'))
		{
			Input::file('school_logo')->move(SCHOOL_LOGO_LOCATION, SCHOOL_LOGO_FILENAME);
		}
		
		if (File::put(GENERAL_SETTINGS, json_encode($general_settings, JSON_PRETTY_PRINT)))
		{
			Session::flash('success-msg', 'Settings updated');
		}
		else
		{
			Session::flash('error-msg', 'Error updating settings');
		}

		return Redirect::back();

	}
}