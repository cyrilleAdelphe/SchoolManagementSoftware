<?php
define('TAX_CONFIG', app_path().'/modules/fee-manager/assets/config.json');
class FeeManagerHelperController {
	public static function getConfig() {
		if (File::exists(TAX_CONFIG)) {
			$config = json_decode(File::get(TAX_CONFIG), true);
		} else {
			$config = [
				'tax_percent' => 0,
				'on_monthly' => 'no',
				'on_examination' => 'no',
				'on_transportation' => 'no',
				'on_hostel' => 'no'
			];
		}
		return $config;
	}

	public static function setConfig($data) {
		$config = array();
		if(File::exists(TAX_CONFIG))
		{
			$config = json_decode(File::get(TAX_CONFIG), true);
		}

		$config['tax_percent'] = $data['tax_percent'];
		
		$taxes = array('monthly', 'examination', 'transportation', 'hostel');
		foreach ($taxes as $tax) {
			$config['on_' . $tax] = $data['on_' . $tax];
		}
		return File::put(TAX_CONFIG, json_encode($config, JSON_PRETTY_PRINT));
		
	}
}