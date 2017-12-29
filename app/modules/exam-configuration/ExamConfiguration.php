<?php
class ExamConfiguration extends BaseModel
{
	protected $table = 'exam_configurations';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'ExamConfiguration';

	public $createRule = [
							'exam_name' 				=> ['required'],
							'session_id' 				=> ['required'],
							'exam_start_date_in_ad'					=> ['required','date'],
							'exam_end_date_in_ad'					=> ['required', 'date'],
							'weightage'			=> ['required','integer']
						];

	public $updateRule = [
							'exam_name' 				=> ['required'],
							'session_id' 				=> ['required'],
							'exam_start_date_in_ad'					=> ['required','date'],
							'exam_end_date_in_ad'					=> ['required', 'date'],
							'weightage'			=> ['required','integer']
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', $model::getTableName().'.session_id');
		$result = $result->select(array($model::getTableName().'.*', 'session_name'))->where($model::getTableName().'.is_active','yes');
		
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