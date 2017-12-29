<?php

class StudentHelperController
{
	public static function generateStaticSelectList($data = array('id' => 'name'), $field_name, $selected = 0, $default = '<option value = "all">  All  </option>', $is_array = false)
	{
		$select = '';

		$select = '<select id = "'.$field_name.'" name = "'.$field_name.'" class = "form-control '.$field_name.'" >'."\n";
		$select .= $default;
		
		foreach($data as $id => $name)
		{
			if($selected == $id)
					$sel = 'selected';
			else
				$sel = '';
			$select .= '<option value = '.$id.' '.$sel.'>'.$name.'</option>'."\n";

		}

		$select .= '</select>';	
		return $select;
	}

	///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////
	public static function getColumnsForReport()
	{
		if(File::exists(REPORT_CONFIG_FILEPATH))
		{
			$content = json_decode(File::get(REPORT_CONFIG_FILEPATH));

			$array_for_select_statment = [];
			$array_for_formating_data = [];
			foreach($content as $c)
			{
				if($c->show == 'yes')
				{
					$array_for_select_statment[] = $c->table.'.'.$c->column_name;
					$array_for_formating_data[] = ['column_name' => $c->column_name, 'alias' => $c->alias];
				}
			}
		}
		else
		{
			die('Config File does not exist');
		}

		return ['array_for_select_statment' => $array_for_select_statment, 'array_for_formating_data' => $array_for_formating_data];
	}
	///////// StudentReport-dynamic-header-titles-v1-changes-made-here //////

}