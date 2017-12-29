<?php

class MonthlyFee extends BaseModel
{
	protected $table = 'fee_monthly';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'MonthlyFee';



	public $createRule = [
							'class_id' => array('required', 'exists:classess,id'),
							'amount'	=> array('required', 'integer', 'min:1')
						];

	public $updateRule = [
							'class_id' => array('required', 'exists:classess,id'),
							'amount'	=> array('required', 'integer', 'min:1')
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id');
		$result = $result->select(array($model::getTableName().'.*', 'class_name', 'class_code'));

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$result = $result->where($model::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
		}
		//}

		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

}