<?php

class Routine extends BaseModel
{
	protected $table = 'routines';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'Routine';

	public $createRule = [
							'subject_id' 		=> ['required'],
							'day'				=> ['required','date_format:Y-m-j'],
							'period'			=> ['required','integer']
						];

	public $updateRule = [
							'subject_id' 		=> ['required'],
							'day'				=> ['required','date_format:Y-m-j'],
							'period'			=> ['required','integer']
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->select(array($model::getTableName().'.*'));
		
		$result = $result->join(BookCategories::getTableName(),$this->getTableName().'.category_id', '=', BookCategories::getTableName().'.id');
		
		$result = $result->select($this->getTableName().'.*',
									BookCategories::getTableName().'.title as category_title');
		
		foreach($result->get() as $key=>$row)
		{
			$book_id = $row->id;
			$row->out_routine = 0;
		}

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if($queryString['filter']['field']=='out_routine' || $queryString['filter']['field']=='in_routine')
				$result = $result->where($queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			else
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