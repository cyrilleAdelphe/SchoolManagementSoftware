<?php

class FeePayment extends BaseModel
{
	protected $table = 'fee_payment';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'FeePayment';



	public $createRule = [
							'student_id' => array('required', 'exists:student_registration,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id'),
							'fee_amount' => array('required', 'integer', 'min:0'),
							'received_amount' => array('required', 'integer', 'min:0'),
							'month' => array('required', 'integer', 'min:1', 'max:12'),
							'is_paid' => array('required', 'in:yes,no'),
						];

	public $updateRule = [
							'student_id' => array('exists:student_registration,id'),
							'academic_session_id' => array('exists:academic_session,id'),
							'fee_amount' => array('integer', 'min:0'),
							'received_amount' => array('required', 'integer', 'min:0'),
							'month' => array('integer', 'min:1', 'max:12'),
							'is_paid' => array('in:yes,no'),
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'class_id');
		$result = $result->select(array($model::getTableName().'.*', 'class_name'));
		

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