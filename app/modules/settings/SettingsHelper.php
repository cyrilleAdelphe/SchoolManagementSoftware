<?php

class SettingsHelper {
	public static function getGeneralSetting($key) {
		if(File::exists(GENERAL_SETTINGS)) {
			$general_settings = json_decode(File::get(GENERAL_SETTINGS));
			if(isset($general_settings->$key)) {
				return $general_settings->$key;	
			}
		} 
		
		return '';
		
	}
}