<?php

class PermissionByRouteName extends Eloquent
{
	protected $table = 'permission_by_routename';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'PermissionByRouteName';

	public static function getTableName()
	{
		return with(new static)->getTable();
	}

	public $createRule = array();

	public $updateRule = array();

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = $model::where('is_active', $queryString['status']);

		foreach($queryString['filter'] as $filter)
		{
			$result->where($filter['filter_name'], 'LIKE', '%'.$filter['filter_value'].'%');
		}

		if(isset($queryString['orderBy']))
		{
			$result->order($queryString['orderBy'], $queryString['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getEditView($id)
	{
		$model = $this->model_name;
		$result = $model::find($id);
		return $result;
	}
}

?>