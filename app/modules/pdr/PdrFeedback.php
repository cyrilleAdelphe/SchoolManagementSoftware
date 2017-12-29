<?php

use Carbon\Carbon;

class PdrFeedback extends BaseModel
{
	protected $table = 'pdr_feedback';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'PdrFeedback';


	public $createRule = [ ];

	public $updateRule = [ ];

	//protected $defaultOrder = array('orderBy' => 'pdr_date', 'orderOrder' => 'DESC');

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$table = $model::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$guardian_table = Guardian::getTableName();
		
		$result = DB::table($model::getTableName())
					->join($student_registration_table, $student_registration_table.'.id', '=', $table.'.student_id')
					->join($guardian_table, $guardian_table.'.id', '=', $table.'.guardian_id')
					->select($table.'.*', $student_registration_table.'.student_name', $guardian_table.'.guardian_name');
		
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
				if($col == 'guardian_name')
				{
					$result = $result->where($guardian_table.'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
				}
				elseif($col == 'student_name')
				{
					$result = $result->where($student_registration_table.'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				}
				else
				{
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
				}
			}
			
		}
		//}

		if(isset($queryString['orderBy']))
		{
			$result = $result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result = $result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->where('pdr_id', $queryString['pdr_id']);

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}
}