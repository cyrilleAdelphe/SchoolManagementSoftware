<?php

class Group extends Eloquent
{
	protected $table = 'groups';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public static function 	getTableName()
	{
		return with(new static)->getTable();
	}

	public function getGroups()
	{
		$result = Group::where('is_active', 'yes')
						->get(array('id', 'group_name'));

		return $result;
	}
}

?>