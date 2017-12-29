<?php
class EmployeeDocument extends BaseModel
{
	protected $table = 'employee_documents';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'EmployeeDocument';
	
	public $createRule = [
							'employee_id' 	=> ['required', 'exists:employees,id'],
							//'download_id'	=> ['required', 'exists:download_manager,id'],
							'fileToUpload'=> ['required', 'max:20480'],
							'is_active' 	=> ['required','in:yes,no']
						];

	public $updateRule = [
							'employee_id' 	=> ['required', 'exists:employees,id'],
							//'download_id'	=> ['required', 'exists:download_manager,id'],
							'fileToUpload'=> ['required', 'max:20480'],
							'is_active' 	=> ['required','in:yes,no']
						];

	public function getListViewData($queryString)
	{
		$employee = DB::table(Employee::getTableName())
									->join(Admin::getTableName(), Admin::getTableName().'.admin_details_id', '=', Employee::getTableName().'.id')
									->where(Admin::getTableName().'.username', Input::get('employee_username'))
									->select(Employee::getTableName() . '.id', 'employee_name', 'username')
									->first();

		$model = $this->model_name;
		$result = DB::table($model::getTableName())
								->join(DownloadManager::getTableName(), DownloadManager::getTableName().'.id', '=', $model::getTableName().'.download_id');
		$result = $result->select(array($model::getTableName().'.*', 'filename', 'google_file_id'));
		
		$result = $result->where('employee_id', $employee ? $employee->id : 0);

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
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

		return array( 'data' => $result, 
									'count' => $count, 
									'message' => $msg,
									'employee' => $employee);
	}
}