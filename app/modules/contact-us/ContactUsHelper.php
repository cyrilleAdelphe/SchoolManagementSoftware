<?php
class ContactUsHelper
{
	private $module_name = 'contact-us';
	public function getConfig($config_file='')
	{
		$config_file = ($config_file=='')?app_path().'/modules/'.$this->module_name.'/config.json':$config_file;
		$config = json_decode(
						File::get($config_file), 
						true
					);

		return $config;

	}

	public function writeConfig($config,$config_file='')
	{
		$config_file = ($config_file=='')?app_path().'/modules/'.$this->module_name.'/config.json':$config_file;
		$result = File::put($config_file, json_encode($config));
		if ($result === false)
		{
		    $response['status'] = 'error';
		    $response['message'] = '';
		}
		else
		{
			$response['status'] = 'success';
		    $response['message'] = '';
		}

		return $response;
	}
}