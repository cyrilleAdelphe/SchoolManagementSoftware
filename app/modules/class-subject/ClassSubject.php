<?php

class ClassSubject extends BaseModel
{
	protected $table = 'class_subjects';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'ClassSubject';


	public $createRule = [ 'class_id'	=> array('required'),
						   'subject_id' => array('required')];

	public $updateRule = [ 'class_id'	=> array('required'),
						   'subject_id' => array('required')];

	//protected $defaultOrder = array('orderBy' => 'sort_order', 'orderOrder' => 'ASC');

	public function getCreateViewData()
	{
		$helper = new ClassSubjectHelper;

		$return = array();
		$return['classess'] = $helper->getAllClassess();
		$return['subjects'] = $helper->getAllSubjects();
		$return['classessSubjects'] = $helper->getAllClassessSubjects();

		return $return;
	}

	public function getListViewData($queryString)
	{
		$return = array();
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', $model::getTableName().'.class_id');
		$result = $result->select(array($model::getTableName().'.*', Classes::getTableName().'.class_name'));
		

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
			
				if($col == 'class_name')
				{
					$result->where(Classes::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				}
				else
				{
					$result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				}
			
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

		/*foreach($result as r)
		{
			$return->class_id = $r->class_id;
			$return->class_name = $r->class_name;
			$return->is_active = $r->is_active;
			$return->created_by = $r->created_by;
			$return->created_at = $r->created_at;
			$return->updated_by = $r->updated_by;
			$return->updated_at = $r->updated_at;
			$return->section_code[] = $r->section_code
		}*/

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}
}