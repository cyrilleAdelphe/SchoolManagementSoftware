<?php

class ExaminationFee extends BaseModel
{
	protected $table = 'fee_examination';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'ExaminationFee';



	public $createRule = [
							'class_id' => array('required', 'exists:classess,id'),
							'exam_id' => array('required', 'exists:exam_configurations,id'),
							'amount'	=> array('required', 'integer', 'min:1'),
							'month' => array('required', 'integer', 'min:1', 'max:12'),
						];

	public $updateRule = [
							'class_id' => array('required', 'exists:classess,id'),
							'exam_id' => array('required', 'exists:exam_configurations,id'),
							'amount'	=> array('required', 'integer', 'min:1'),
							'month' => array('required', 'integer', 'min:1', 'max:12'),
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		
		$result = $result->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id');
		
		$result = $result->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', 'exam_id');
		
		$result = $result->select(array($model::getTableName().'.*', 'class_name', 'class_code', 'exam_name',));
		
		
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