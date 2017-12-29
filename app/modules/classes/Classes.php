<?php

class Classes extends BaseModel
{
	protected $table = 'classess';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Classes';


	public $createRule = [ 'class_name'	=> array('required', 'unique_with:classess,academic_session_id'),
						   'class_code' => array('required', 'unique_with:classess,academic_session_id'),
						   'academic_session_id' => array('required', 'exists:academic_session,id')];

	public $updateRule = [ 'class_name'	=> array('required', 'unique_with:classess,academic_session_id'),
						   'class_code' => array('required', 'unique_with:classess,academic_session_id'),
						   'academic_session_id' => array('required', 'exists:academic_session,id')];

	protected $defaultOrder = array('orderBy' => 'sort_order', 'orderOrder' => 'ASC');

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', Classes::getTableName().'.academic_session_id');
		$result = $result->select(array($model::getTableName().'.*', 'session_name'));
		
		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if($queryString['filter']['field'] == 'session_name')
			$result = $result->where(AcademicSession::getTableName().'.session_name', 'LIKE', '%'.$queryString['filter']['value'].'%');
			else
			$result = $result->where($model::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
		}
		//}

		$result = $result->orderBy(AcademicSession::getTableName().'.id', 'ASC');
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