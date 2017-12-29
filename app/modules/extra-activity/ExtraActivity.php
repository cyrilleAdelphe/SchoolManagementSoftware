<?php
class ExtraActivity extends BaseModel
{
	protected $table = 'extra_activities';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'ExtraActivity';

	public $createRule = [
							'event_code'			=> ['required', 'exists:events,event_code'],
							//'student_id'		=> ['required', 'exists:student_registration,id'],
							'remarks'				=> []
						];

	public $updateRule = [
							'event_code'			=> ['required', 'exists:events,event_code'],
							//'student_id'		=> ['required', 'exists:student_registration,id'],
							'remarks'				=> []
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->join(Events::getTableName(), Events::getTableName().'.id', '=', $model::getTableName().'.event_id');
		$result = $result->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', $model::getTableName().'.student_id');
		$result = $result->groupBy('event_id');
		$result = $result->select(array($model::getTableName().'.*',Events::getTableName().'.title',Events::getTableName().'.from_ad',Events::getTableName().'.from_bs',Events::getTableName().'.to_ad',Events::getTableName().'.to_bs',));
		
		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}

		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
			
				if ($col == 'title')
					$result = $result->where(Events::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
				elseif ($col == 'student_name')
					$result = $result->where(StudentRegistration::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
				elseif ($col == 'from')
						$result = $result->where(Events::getTableName().'.'.(CALENDAR == 'BS' ? 'from_bs' : 'from_ad'), 'LIKE', '%'.$query_vals[$index].'%');	
				elseif ($col == 'to')
						$result = $result->where(Events::getTableName().'.'.(CALENDAR == 'BS' ? 'to_bs' : 'to_ad'), 'LIKE', '%'.$query_vals[$index].'%');	
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			
			}
		}

		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		foreach($result as $key => $row)
		{
			$result[$key]->student_list = DB::table($model::getTableName())
																			->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', $model::getTableName().'.student_id')
																			->where($model::getTableName().'.event_id', $row->event_id)
																			->select(StudentRegistration::getTableName().'.student_name', $model::getTableName().'.remarks')
																			->get();

		}

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}
	
	public function getViewViewData($event_id)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
								->join(Events::getTableName(), Events::getTableName().'.id', '=', $model::getTableName().'.event_id')
								->where('event_id', $event_id)
								->groupBy('event_id')
								->select(
													$model::getTableName().'.*', 
													Events::getTableName().'.event_code',
													Events::getTableName().'.title',
													Events::getTableName().'.description',
													Events::getTableName().'.from_ad',
													Events::getTableName().'.from_bs',
													Events::getTableName().'.to_ad',
													Events::getTableName().'.to_bs'
												)
								->first();

		if($result)
		{
			$result->student_list = 
								DB::table($model::getTableName())
									->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', $model::getTableName().'.student_id')
									->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', 'student_id')
									->where('role', 'student')
									->where('event_id', $event_id)
									->select(Users::getTableName().'.username', 
														StudentRegistration::getTableName().'.student_name', 
														$model::getTableName().'.remarks'
													)
									->get();
		}

		return $result;
	}
}