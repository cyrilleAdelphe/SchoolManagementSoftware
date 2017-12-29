<?php

class ClassSection extends BaseModel
{
	protected $table = 'classess_sections';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'ClassSection';


	public $createRule = [ 'class_id'	=> array('required'),
						   'section_code' => array('required')];

	public $updateRule = [ 'class_id'	=> array('required'),
						   'section_code' => array('required')];

	//protected $defaultOrder = array('orderBy' => 'sort_order', 'orderOrder' => 'ASC');

	public function getCreateViewData()
	{
		$helper = new ClassSectionHelper;
		$academic_session_id = Input::has('academic_session_id') ? Input::get('academic_session_id'): AcademicSession::where('is_current','yes')->first()['id'];
		$return = array();	
		
		$return['classess'] = $helper->getClassess($academic_session_id);
		$return['sections'] = $helper->getAllSections();
		$return['classessSections'] = $helper->getClassessSections($academic_session_id);
		return $return;
	}

	public function getListViewData($queryString)
	{
		$return = array();
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', $model::getTableName().'.class_id')
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', Classes::getTableName().'.academic_session_id');


		$result = $result->select(array($model::getTableName().'.*', Classes::getTableName().'.class_name', 'session_name'));
		

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
				elseif($col == 'session_name')
				{
					$result->where(AcademicSession::getTableName().'.session_name', 'LIKE', '%'.$query_vals[$index].'%');
				}
				else
				{
					$result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
				}
			
			}
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

		$result = $result->get();//paginate($queryString['paginate']);

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