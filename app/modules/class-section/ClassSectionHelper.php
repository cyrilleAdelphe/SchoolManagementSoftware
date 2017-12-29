<?php

Class ClassSectionHelper
{
	public function getClassess($academic_session_id)
	{
		$result = Classes::where('is_active', 'yes')
							->where('academic_session_id', $academic_session_id)
							->lists('class_name', 'id');

		return $result;
	}
	

	public function getClassessSections($academic_session_id)
	{
		$return = array();

		$class_section_table = ClassSection::getTableName();
		$class_table = Classes::getTableName();

		$result = DB::table($class_section_table)
						->join($class_table, $class_table.'.id', '=', $class_section_table.'.class_id')
						->where($class_table.'.academic_session_id', $academic_session_id)
						->get();

		foreach($result as $r)
		{
			$return[$r->class_id][] = $r->section_code;
		}
		return $return;
	}


	public function getAllClassess()
	{
		$result = Classes::where('is_active', 'yes')
							->lists('class_name', 'id');

		return $result;
	}

	public function getAllSections()
	{
		$result = Section::where('is_active', 'yes')
						   ->lists('section_code');

		return $result;
	}

	public function getAllClassessSections()
	{
		$return = array();
		$result = ClassSection::where('is_active', 'yes')
								->select('class_id', 'section_code')
								->get();


		foreach($result as $r)
		{
			$return[$r->class_id][] = $r->section_code;
		}
		return $return;
	}
}