<?php

Class ClassSubjectHelper
{
	public function getAllClassess()
	{
		$result = Classes::where('is_active', 'yes')
							->lists('class_name', 'id');

		return $result;
	}

	public function getAllSubjects()
	{
		$result = Subject::where('is_active', 'yes')
						   ->lists('subject_code');

		return $result;
	}

	public function getAllClassessSubjects()
	{
		$return = array();
		$result = ClassSubject::where('is_active', 'yes')
								->select('class_id', 'subject_code')
								->get();


		foreach($result as $r)
		{
			$return[$r->class_id][] = $r->subject_code;
		}
		return $return;
	}
}