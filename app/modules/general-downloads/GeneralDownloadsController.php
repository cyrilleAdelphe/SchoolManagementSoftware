<?php

class GeneralDownloadsController extends BaseController
{
	protected $view = 'general-downloads.views.';

	protected $model_name = 'GeneralDownloads';

	protected $module_name = 'general-downloads';

	protected $role;

	protected $columnsToShow = array(
		array(
			'column_name'	=>	'filename',
			'alias'				=>	'Filename'
		)
	);

	// @override
	public function getSearchColumns($columnsToShow = array())
	{
		
		$columns = count($columnsToShow) ? $columnsToShow : $this->columnsToShow ;
		$queryString = $this->getQueryString();
		
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$filter_field = $queryString['filter']['field'];
			$filter_value = $queryString['filter']['value'];
		}
		else
		{
			$filter_field = $filter_value = '';
		}


		$i = 1;//2
		//$html = '<tr><td></td><td></td>';
		$html = '<tr><td></td>';
		foreach($columns as $cols)
		{
			$html .= '<td><input type = "text" class = "input-sm search_column" id = "'.$i++. 
								'" value="'. ($filter_field==$cols['column_name']?$filter_value:'').
								'"><input type = "hidden" class = "field_name" value = "'.$cols['column_name'].'"></td>';
		}	
		//$html .= '<td colspan = "2"></td>';
		$html .=	'<td>'.
          		'<a  href = "'.URL::current() . 
          			'?_token=' . csrf_token() .
          		'">Cancel Query</a>'.
        			'</td>';
		$html .= "</tr>";
		return $html;
	}

	// @override
	public function getListView()
	{
		AccessController::allowedOrNot('general-downloads', 'can_view');
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		$queries = $this->getQueries();

		return View::make($this->view . 'main')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					->with('paginateBar', $this->getPaginateBar())
					->with('role', $this->role);

	}


	public function postFile()
	{
		AccessController::allowedOrNot('general-downloads', 'can_create');
		$data = Input::all();

		$result = $this->validateInput($data);
		
		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-main') 
				->withInput()
				->with('role', $this->role)
				->with('errors', $result['data']);
		}
		
		// TODO: validate that the filename isn't repeated within the folder

 		try
		{
			DB::connection()->getPdo()->beginTransaction();

			// uploading and storing in download_manager table
			$download_data = array();
			$download_data['no_of_downloads'] = 0;
			$parent = DownloadManager::where('filename', DOWNLOAD_MANAGER_FOLDER)
															->where('mime_type', EasyDriveAPI2::$folder_mime_type)
															->first();
			if (!$parent)
			{
				throw new Exception("Download Folder Not Configured! Contact site administrator", 1);
			}
			else
			{
				$download_data['parent_id'] = $parent->id;
			}
			$download_data['mime_type'] = Input::file('fileToUpload')->getMimeType();
			$download_data['google_file_id'] = 'to be filled later';
			if($data['filename'] == '') {
				$download_data['filename'] = Input::file('fileToUpload')->getClientOriginalName();	
				Input::merge(array('filename' => $data['filename']));
			} else {
				$download_data['filename'] = $data['filename'];
			}
			$file = Input::file('fileToUpload');
			$google_drive = new EasyDriveAPI2(Request::url());
			$file = $google_drive->insertFile(
				$download_data['filename'],
				'file created by google drive api',
				$parent->google_file_id,
				$download_data['mime_type'],
				$file
			);

			if(!$file)
			{
				throw new Exception('Unable to create file');
			}
	 		$download_data['google_file_id'] = $file->getId();		
	 		$download_data['is_active'] = 'yes';
	 		$download_data['is_featured'] = 'no';

			$download_id = $this->storeInDatabase($download_data, 'DownloadManager');

			// storing in general_downloads table
			$general_downloads_data = array();
			$general_downloads_data['download_id'] = $download_id;
			$general_downloads_data['is_active'] = $data['is_active'];
			$this->storeInDatabase($general_downloads_data);

			DB::connection()->getPdo()->commit();
			$success = true;
			$msg = 'Successfully uploaded';
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

 		Session::flash($success ? 'success-msg' : 'error-msg', $msg);
				
		return Redirect::route($this->module_name.'-main');
	}

	public function postDelete()
	{
		AccessController::allowedOrNot('general-downloads', 'can_delete');
		$id = Input::get('id', 0);

		$data = GeneralDownloads::find($id);

		if (!$data) {
			return Response::make('Invalid request', 404);
		} else {
			$file = DownloadManager::find($data->download_id);
			
			$google_drive = new EasyDriveAPI2(Request::url());
			try
			{
				DB::connection()->getPdo()->beginTransaction();
				if($google_drive->trashFile($file->google_file_id))
				{
					DownloadManager::destroy($file->id);
					GeneralDownloads::destroy($data->id);
				}
				else
				{
					throw new Exception('Error deleting the item');
				}

				DB::connection()->getPdo()->commit();
				$success = true;
				$msg = 'File successfully deleted';
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				$success = false;
				$msg = $e->getMessage();
			}

			Session::flash($success ? 'success-msg' : 'error-msg', $msg);

			return Redirect::back();
		}

	}
}