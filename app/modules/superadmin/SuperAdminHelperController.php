<?php 
class SuperAdminHelperController
{
	public function createGroupTypeSelectList($nameValueArray = array(), $fieldname = '', $selected = '')
	{
		//$nameValueArray = array('unregistered' => 'unregistered', 'admin' => 'admin', 'student' => 'student', 'guardian' => 'guardian');
		//$fieldname = ''
		$html = '';
		$html .= "<select id = '".$fieldname."' class = '".$fieldname." form-control' name = '".$fieldname."'>";
		$html .= '<option value = "0">-- Select --</option>';
		foreach($nameValueArray as $index => $value)
		{
			$status = $selected == $value ? 'selected' : '';
			$html .= '<option value = "'.$value.'" '.$status.' >'.$index."</option>";
		}

		$html .= '</select>';

		return $html;
	}

}