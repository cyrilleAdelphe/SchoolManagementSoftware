<?php
class GeneralDownloads extends BaseModel
{
	protected $table = 'general_downloads';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'GeneralDownloads';
	
	public $createRule = [
							'fileToUpload'=> ['required', 'max:20480'],
							'is_active' 	=> ['required','in:yes,no']
						];

	public $updateRule = [
							'fileToUpload'=> ['required', 'max:20480'],
							'is_active' 	=> ['required','in:yes,no']
						];

	public function getListViewData($queryString)
	{
		
		$model = $this->model_name;
		$result = DB::table($model::getTableName())->join(
			DownloadManager::getTableName(), 
			DownloadManager::getTableName().'.id', '=', 
			$model::getTableName().'.download_id'
		);
		$result = $result->select(
			$model::getTableName().'.*', 
			'filename', 
			'google_file_id'
		);
		
		
		if(isset($queryString['status']))
		{
			$result = $result->where(
			$model::getTableName().'.is_active', 
			$queryString['status']
		);
		}
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			if ($queryString['filter']['field'] == 'filename') {
				$result = $result->where(DownloadManager::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			} else {
				$result = $result->where($model::getTableName().'.'.$queryString['filter']['field'], 'LIKE', '%'.$queryString['filter']['value'].'%');
			}
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

		foreach($result as $key => $child) {
			$result[$key]->download_link = URL::route('download-manager-backend-file-download', [$child->download_id, $child->google_file_id]);
		}

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array( 
			'data' => $result, 
			'count' => $count, 
			'message' => $msg
		);
	}
}