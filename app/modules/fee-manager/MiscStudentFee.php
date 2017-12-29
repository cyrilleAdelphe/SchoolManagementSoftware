<?php

class MiscStudentFee extends BaseModel
{
	protected $table = 'fee_misc_student';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'MiscStudentFee';



	public $createRule = [
							'title' => array('required'),
							'month' => array('required', 'integer', 'min:0', 'max:12'),
							'amount'	=> array('required', 'integer', 'min:1'),
							'student_id'=> array('required', 'exists:student_registration,id'),
							'academic_session_id' => array('required', 'exists:academic_session,id,is_current,yes')
						];

	public $updateRule = [
							'title' => array(),
							'month' => array('integer', 'min:0', 'max:12'),
							'amount'	=> array('required', 'integer', 'min:1'),
							'student_id'=> array('exists:student_registration,id'),
							'academic_session_id' => array('exists:academic_session,id')
						];
						
	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		
		$result = $result->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', 'academic_session_id');

		$result = $result->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', 'student_id');
		
		$result = $result->select(array($model::getTableName().'.*', 'session_name', 'student_name'));

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