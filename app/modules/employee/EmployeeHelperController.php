<?php

class EmployeeHelperController
{
	public function getAllGroups()
	{
		$result = Group::where('is_active', 'yes')
						->where('id', '>', 1)
						->lists('group_name', 'id');

		return $result;
	}
}