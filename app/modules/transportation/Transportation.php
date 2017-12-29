<?php

class Transportation extends BaseModel
{
	protected $table = 'transportation';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Transportation';

	public $createRule = [
							'unique_transportation_id'	=>	['required', 'unique:transportation,unique_transportation_id'],
							'number_plate'	=>				['required', 'unique:transportation,number_plate'],
							'driver_number'	=>				['required']
						];

	public $updateRule = [
							'unique_transportation_id'	=>	['required', 'unique:transportation,unique_transportation_id'],
							'number_plate'	=>				['required', 'unique:transportation,number_plate'],
							'driver_number'	=>				['required']
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->select(array($model::getTableName().'.*'));

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

//die('here');
		$result = $result->get();

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		//get assigned students
		$count_data = DB::table(TransportationStudent::getTableName())
						->select(DB::raw('transportation_id, COUNT(student_id) as  total_students'))
						->where('is_active', 'yes')
						->groupBy('transportation_id')
						->lists('total_students', 'transportation_id');
						
		$assigned_students = DB::table(TransportationStudent::getTableName())
								->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', TransportationStudent::getTableName().'.student_id')
								->join(Transportation::getTableName(), transportation::getTableName().'.id', '=', TransportationStudent::getTableName().'.transportation_id')
								->select(array('bus_code', 'student_name', 'unique_transportation_id', TransportationStudent::getTableName().'.id', 'fee_amount', StudentRegistration::getTableName().'.id as student_id'))
								->where(TransportationStudent::getTableName().'.is_active', 'yes')
								->where(StudentRegistration::getTableName().'.is_active', 'yes')
								->where(Transportation::getTableName().'.is_active', 'yes')
								->paginate(Input::get('paginate', 10));


		$transportation = DB::table('transportation')->select('bus_code','id')->lists('bus_code','id');

		$d = array();
		foreach($transportation as $index => $value)
		{
			$d[$index] = DB::table('transportation_staffs')					
					
					->select('transportation_staffs.employee_id','transportation_staffs.transportation_id')
					->where('transportation_id', $index)
					->get();
		

		}
		


	


		return array('data' => $result, 'count' => $count, 'message' => $msg, 'assigned_students' => $assigned_students, 'count_data' => $count_data,
			'transportation' => $transportation, 'd' => $d);
	}
}



