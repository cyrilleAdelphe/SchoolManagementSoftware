<?php

class HostelFee extends BaseModel
{
	protected $table = 'fee_hostel';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'HostelFee';



	public $createRule = [
							'class_id' => array('required', 'exists:classess,id'),
							'section_id' => array('required', 'exists:sections,id'),
							'type' => array('required', 'in:day,full'),
							'amount'	=> array('required', 'integer', 'min:1')
						];

	public $updateRule = [
							'class_id' => array('required', 'exists:classess,id'),
							'section_id' => array('required', 'exists:sections,id'),
							'type' => array('required', 'in:day,full'),
							'amount'	=> array('required', 'integer', 'min:1')
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		
		$result = $result->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id');
		
		$result = $result->join(Section::getTableName(), Section::getTableName().'.id', '=', 'section_id');
		
		$result = $result->select(array($model::getTableName().'.*', 'class_name', 'section_name', 'class_code', 'section_code'));

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