<?php

class LanguageController extends Controller
{
	public static function translate($text)
	{
		$lang = strlen(trim(Input::get('language_used'))) ? Input::get('language_used') : 'en';
		return $this->getTranslation($text, $lang);
	}

	public function getTranslation($text, $lang)
	{
		return $text;
	}
}

?>