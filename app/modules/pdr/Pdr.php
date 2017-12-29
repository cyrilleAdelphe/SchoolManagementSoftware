<?php

use Carbon\Carbon;

class Pdr extends BaseModel
{
	protected $table = 'pdr';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Pdr';


	public $createRule = [ 'session_id' => ['required'],
						   'class_id'	=> ['required'],
						   'section_id'	=> ['required'],
						   'pdr_date'	=> ['required']];

	public $updateRule = [ ];

	protected $defaultOrder = array('orderBy' => 'pdr_date', 'orderOrder' => 'DESC');

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$table = $model::getTableName();
		$academic_session_table = AcademicSession::getTableName();
		$class_table = Classes::getTableName();
		$section_table = Section::getTableName();

		$result = DB::table($model::getTableName())
					->join($academic_session_table, $academic_session_table.'.id', '=', $table.'.session_id')
					->join($class_table, $class_table.'.id', '=', $table.'.class_id')
					->join($section_table, $section_table.'.id', '=', $table.'.section_id')
					->select([$table.'.*', $academic_session_table.'.session_name', $class_table.'.class_name', $section_table.'.section_code']);
		//$result = $result->where($model::getTableName().'.is_active', $queryString['status']);

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
				if($col == 'session_name')
				{
					$result = $result->where(AcademicSession::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
				}
				elseif($col == 'class_name')
				{
					$result = $result->where(Classes::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				}
				elseif($col == 'section_code')
				{
					$result = $result->where(Section::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
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

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getCreateViewData($data)
	{

		$day = Carbon::createFromFormat('Y-m-d', trim($data['date']));

		$day = $day->format('l');

		$table = DailyRoutine::getTableName();
		$data = DB::table($table)
					->where('day', $day)
					->where('session_id', $data['session_id'])
					->where('class_id', $data['class_id'])
					->where('section_id', $data['section_id'])
					->orderBy('period', 'ASC')
					->select('subject', 'teacher')
					->get();

		return $data;
	}

}