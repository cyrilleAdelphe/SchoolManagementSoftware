<?php

class Template extends BaseModel
{
	protected $table = 'templates';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Template';


	public $createRule = [ 'template_name'	=> array('required', 'unique:templates,template_name',),
						   'template_alias'		=> array('required')
						  ];

	public $updateRule = ['template_name'	=> array('required', 'unique:templates,template_name',),
						   'template_alias'		=> array('required')
						   ];

	protected $defaultOrder = array('orderBy' => 'position_name', 'orderOrder' => 'DESC');

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->leftJoin(PositionTemplate::getTableName(), PositionTemplate::getTableName().'.template_id', '=', $model::getTableName().'.id')
					->leftJoin(Position::getTableName(), Position::getTableName().'.id', '=', PositionTemplate::getTableName().'.position_id');
		$result = $result->select(array($model::getTableName().'.*', Position::getTableName().'.position_name', PositionTemplate::getTableName().'.sort_order'));
		$result = $result->where(Position::getTableName().'.is_active', 'yes')
						 ->orWhere(Position::getTableName().'.is_active', NULL);

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}

		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if($queryString['filter']['field'] == 'position_name')
				$result->where(Position::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			elseif($queryString['filter']['field'] == 'sort_order')
				$result->where(PositionTemplate::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			else
				$result->where($model::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
		}
		//}

		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy(Position::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getViewViewData($id)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->leftJoin(PositionTemplate::getTableName(), PositionTemplate::getTableName().'.template_id', '=', $model::getTableName().'.id')
					->leftJoin(Position::getTableName(), Position::getTableName().'.id', '=', PositionTemplate::getTableName().'.position_id')
					->where($model::getTableName().'.is_active', 'yes')
					->select(array($model::getTableName().'.*', Position::getTableName().'.position_name', PositionTemplate::getTableName().'.sort_order'))
					->first();
	
		return $result;
	}
}