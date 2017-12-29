<?php

class Gallery extends BaseModel
{
	protected $table = 'gallery';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'Gallery';

	public $createRule = [
								'image'	=> array('required', 'image', 'max:5000'),
								'is_active' => array('required', 'in:yes,no'),
								'category_id' => array('required', 'exists:gallery_category,id')
							];

	public $updateRule = [
								'image'	=> array('image', 'max:5000'),
								'is_active' => array('required', 'in:yes,no'),
								'category_id' => array('required', 'exists:gallery_category,id')
							];

	public function getListViewData($queryString)
	{
		$return = array();
		$model = $this->model_name;
		$result = DB::table($model::getTableName())
					->join(GalleryCategory::getTableName(), GalleryCategory::getTableName().'.id', '=', $model::getTableName().'.category_id');
		$result = $result->select(array($model::getTableName().'.*', GalleryCategory::getTableName().'.title as category_name'));
		

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
			
				if($col == 'category_name')
				{
					$result->where(GalleryCategory::getTableName().'.title', 'LIKE', '%'.$query_vals[$index].'%');
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
		
		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}					
}
?>