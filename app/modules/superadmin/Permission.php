<?php

class Permission extends Eloquent
{
	protected $table = 'permissions';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public static function getTableName()
	{
		return with(new static)->getTable();
	}

	public $createRule = array('ok');

	public $updateRule = array();

	public function getSelectedPermissions($module_id)
	{
		$return = array();
		$data = Permission::where('is_active', 'yes')
							->where('module_id', $module_id)
							->get();

		foreach($data as $d)
		{
			$temp = new stdClass;
			$temp->canView = $d->canView;
			$temp->canAdd = $d->canAdd;
			$temp->canEdit = $d->canEdit;
			$temp->canDelete = $d->canDelete;
			$temp->canPurge = $d->canPurge;

			$return[$d->group_id] = $temp;
		}

		return $return;
	}
}

?>