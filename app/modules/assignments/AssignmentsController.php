<?php


class AssignmentsController extends BaseController
{
	protected $view = 'assignments.views.';

	protected $model_name = 'Assignments';

	protected $module_name = 'assignments';

	protected $role;

	// @overrride
	protected function validateInput($data, $update = false, $modelName = '')
	{
		$result = parent::validateInput($data, $update, $modelName);

		$assignment_upload_config = json_decode(File::get(ASSIGNMENT_CONFIG));
		
		if(isset($data['fileToUpload']) && $data['fileToUpload']->getSize() > $assignment_upload_config->max_file_size * 1024)
		{
			if($result['status']=='success')
			{
				$result['status'] = 'error';
				$result['data'] = new Illuminate\Support\MessageBag;
			}
			$result['data']->add('fileToUpload','File size exceeds max limit');
		}
		
		return $result;
	}

	public function pushNotification($assignment_id, $class_id, $section_id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_send_push_notification');
		$student_ids_gcm_ids = HelperController::getStudentsGCM($class_id, $section_id);
		$gcm_ids = $student_ids_gcm_ids[1];
		$student_ids = $student_ids_gcm_ids[0];
		if($student_ids)
		{
			$subject_map_table = DownloadsSubjectMap::getTableName();
			$subject_table = Subject::getTableName();
			$download_table = DownloadManager::getTableName();

			$assignment = DB::table($subject_map_table)
									->join($subject_table, $subject_table.'.id', '=', $subject_map_table.'.subject_id')
									->join($download_table, $download_table.'.id', '=', $subject_map_table.'.download_id')
									->where($subject_map_table.'.id', $assignment_id)
									->select('filename', 'subject_name', $subject_map_table.'.created_by')
									->first();

			$message = $this->module_name . ' # '.
						'Assignment '. $assignment->filename . 
						' for ' . $assignment->subject_name .
						' uploaded by ' . $assignment->created_by;

			$result = (new GcmController)->send($gcm_ids, $message, $student_ids); //add user ids as 3rd parameter

			if($result['status'] == 'success')
			{
				$message = json_decode($result['message'], true);

				if($message['status'] == 'success')
				{
					Session::flash('success-msg', 'Notifications successfully sent');
				}
				else
				{
					Session::flash('error-msg', 'Notifications not sent: '. $message['message']);
				}
				
				return Redirect::back();
			}
			else
			{
				Session::flash('error-msg', $result['message']);

				return Redirect::back()
								->withInput();
			}	
		}
		else
		{
			Session::flash('warning-msg', 'Sorry there is no one to send notification to');
			return Redirect::back();
		}
		

	}

	public function getRecent()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view_files');
		$subject_map_table = DownloadsSubjectMap::getTableName();
		$subject_table = Subject::getTableName();
		$section_table = Section::getTableName();
		$class_table = Classes::getTableName();
		$download_table = DownloadManager::getTableName();

		if(File::exists(ASSIGNMENT_CONFIG))
		{
			$assignment_config = json_decode(File::get(ASSIGNMENT_CONFIG));
			if(isset($assignment_config->max_frontend_show))
			{
				$max_frontend_show = $assignment_config->max_frontend_show;
			}
		}
		
		if(!isset($max_frontend_show))
		{
			$max_frontend_show = 10;
		}

		$files = DB::table($subject_map_table)
								->join($subject_table, $subject_table.'.id', '=', $subject_map_table.'.subject_id')
								->join($class_table, $class_table.'.id', '=', $subject_table.'.class_id')
								->join($section_table, $section_table.'.id', '=', $subject_table.'.section_id')
								->join($download_table, $download_table.'.id', '=', $subject_map_table.'.download_id')
								->select('download_id', 'class_id', 'section_id','filename','google_file_id', 'subject_name', 'no_of_downloads', $subject_map_table.'.created_by', $subject_map_table.'.created_at')
								->orderBy($subject_map_table.'.created_at', 'DESC')
								->take($max_frontend_show)
								->get();

		
		return View::make($this->view. 'recent')
					->with('files', $files)
					->with('role', $this->role);


	}

	public function getConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view_config');
		if(File::exists(ASSIGNMENT_CONFIG))
		{
			$assignment_config = json_decode(File::get(ASSIGNMENT_CONFIG));
		}
		else
		{
			$assignment_config = new stdClass;
			$assignment_config->max_file_size = 0;
			$assignment_config->max_frontend_show = 0;
		}
		return View::make($this->view . 'config')
				->with('role', $this->role)
				->with('assignment_config', $assignment_config);
	}

	public function postConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$validator = Validator::make(
										Input::all(),
										array(
												'max_file_size'	=> ['required', 'integer', 'min:1'],
												'max_frontend_show'	=> ['required', 'integer', 'min:1'],
											)
									);
		if($validator->fails())
		{
			Session::flash('error-msg', 'Validation Error!!');
			return Redirect::back()
					->withInput()
					->withErrors($validator->messages());
		}
		
		$assignment_config = new stdClass;
		if(File::exists(ASSIGNMENT_CONFIG))
		{
			$assignment_config = json_decode(File::get(ASSIGNMENT_CONFIG));
		}

		$assignment_config->max_file_size = Input::get('max_file_size');
		$assignment_config->max_frontend_show = Input::get('max_frontend_show');

		if(File::put(ASSIGNMENT_CONFIG, json_encode($assignment_config)))
		{
			Session::flash('success-msg', 'Configuration updated');
		}
		else
		{
			Session::flash('error-msg', 'Error updating Configuration');
		}

		return Redirect::back();

	}

	public function getFiles()
	{
		AccessController::allowedOrNot($this->module_name, 'can_get_study_materials');
		$children_file_array = '';
		$last_updated_data = false;

		if(Input::has('academic_session_id') && Input::has('class_id') && Input::has('section_id'))
		{
			$academic_session_id = Input::get('academic_session_id');
			$class_id = Input::get('class_id');
			$section_id = Input::get('section_id');
						
			$children  = Subject::join(
				DownloadsSubjectMap::getTableName(),
				DownloadsSubjectMap::getTableName() . '.subject_id', '=',
				Subject::getTableName() . '.id'
			)->join(
				DownloadManager::getTableName(),
				DownloadManager::getTableName() . '.id', '=',
				DownloadsSubjectMap::getTableName() . '.download_id'
			)->where(
				Subject::getTableName() . '.class_id', $class_id
			)->where(
				Subject::getTableName() . '.section_id', $section_id
			)->orderBy(
				DownloadManager::getTableName() . '.updated_at', 'DESC'
			)->select(
				DownloadManager::getTableName() . '.*',
				DownloadsSubjectMap::getTableName() . '.id as assignment_id',
				Subject::getTableName() . '.subject_name',
				Subject::getTableName() . '.class_id',
				Subject::getTableName() . '.section_id'
			)->get()->toArray();

			if (count($children))
			{
				$last_updated_data = (object)$children[0];
			}
			
			// add download link and delete link to the files
			$children_file_array = array_map(
				function($child) {
					$child = (object)$child;
					$child->download_link = URL::route(
						'download-manager-backend-file-download',
						[
							$child->id,
							$child->google_file_id
						]
					);
					$child->delete_link = URL::route(
						'download-manager-backend-file-remove',
						[
							$child->id,
							$child->google_file_id
						]
					);
					return $child;
				},
				$children
			);
		}
		
		return View::make($this->view . 'files')
					->with('role', $this->role)
					->with('children_file_array', $children_file_array)
					->with('class_id', Input::get('class_id', false))
					->with('section_id', Input::get('section_id', false))
					->with('last_updated_data', $last_updated_data);
	}

	public function postRemoveFile()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$file_id = Input::get('file_id');
		$google_file_id = Input::get('google_file_id');
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$academic_session_id = Input::get('academic_session_id');
		

		$file = DownloadManager::where('id',$file_id)
								->where('google_file_id',$google_file_id)
								->first();
		if(!$file)
		{
			Session::flash('error-msg', 'Delete failed. Invalid file specification');
			return Redirect::back();
		}
		elseif ($file['mime_type'] == EasyDriveAPI2::$folder_mime_type)
		{
			Session::flash('error-msg', 'Can\'t delete a folder');
			return Redirect::back();
		}

		$google_drive = new EasyDriveAPI2(Request::url());
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			if ($google_drive->trashFile($google_file_id))
			{
				// the subject map table's record will be auto-deleted if download manager table's corresponding record in delete (via foreign key)
				DownloadsSubjectMap::where('download_id', $file->id)
					->delete();
				DownloadManager::destroy($file_id);
			}
			else
			{
				throw new Exception('Error deleting the item');
			}

			DB::connection()->getPdo()->commit();
			$success = true;
			$msg = 'File successfully deleted';
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		Session::flash($success ? 'success-msg' : 'error-msg', $msg);

		return Redirect::to(
				URL::route('assignments-files').
				'?class_id='.$class_id.
				'&section_id='.$section_id.
				'&academic_session_id='.$academic_session_id
			);
	}

	public function getUpload()
	{
		AccessController::allowedOrNot($this->module_name, 'can_upload');
		return View::make($this->view . 'upload')
					->with('role', $this->role);
	}

	public function postUploadFile()
	{
		AccessController::allowedOrNot($this->module_name, 'can_upload');
		$data = Input::all();

		$result = $this->validateInput($data);
		
		if($result['status'] == 'error')
		{
			
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::back()
						->withInput()
						->with('role', $this->role)
						->with('errors', $result['data']);
		}

		// $result = AssignmentsHelperController::validateParents();

		// if(!$result['success'])
		// {
		// 	Session::flash('error-msg', $result['msg']);
		// 	return Redirect::back()
		// 				->withInput()
		// 				->with('role', $this->role);
		// }

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$data = AssignmentsHelperController::uploadFile($data, Input::file('fileToUpload'));
			
			$download_id = $this->storeInDatabase($data);

			$downloads_subjects_map = array(
				'download_id'	=>	$download_id,
				'subject_id'	=>	$data['subject_id'],
				'is_active'		=>	'yes'
			);
			$this->storeInDatabase(
				$downloads_subjects_map, 
				'DownloadsSubjectMap'
			);

			DB::connection()->getPdo()->commit();
			$success = true;
			$msg = 'Successfully updated';
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

 		if($success)
		{
			Session::flash('success-msg', 'File created');
			return Redirect::to(
					URL::route('assignments-files').
					'?class_id='.Input::get('class_id').
					'&section_id='.Input::get('section_id').
					'&academic_session_id='.Input::get('academic_session_id')
				);
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back();
		}

		
		
	}

	public function postUploadFiles()
	{
		AccessController::allowedOrNot($this->module_name, 'can_upload');
		$data = Input::all();

		$files_to_upload = Input::file('filesToUpload');


		foreach($files_to_upload as $file_to_upload)
		{
			$data['fileToUpload'] = $file_to_upload;
			$result = $this->validateInput($data);

			if($result['data'] && $result['data']->has('fileToUpload'))
			{
				$result['data']->add('filesToUpload', $result['data']->first('fileToUpload'));
			}
			
			if($result['status'] == 'error')
			{
				Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
				return Redirect::back()
							->withInput()
							->with('role', $this->role)
							->with('errors', $result['data']);
			}
		}

		// TODO: validate that the filename isn't repeated within the folder

		try
		{
			DB::connection()->getPdo()->beginTransaction();
			foreach($files_to_upload as $file_to_upload)
			{
				$data['filename'] = '';	
				$data = AssignmentsHelperController::uploadFile($data, $file_to_upload);
				
					
				$download_id = $this->storeInDatabase($data);

				$downloads_subjects_map = array(
					'download_id'=>$download_id,
					'subject_id'=>$data['subject_id'],
					'is_active'=>'yes'
				);
				$this->storeInDatabase($downloads_subjects_map, 'DownloadsSubjectMap');

				
				$success = true;
				$msg = 'Successfully updated';
				
			}
			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		if($success)
		{
			Session::flash('success-msg', 'File created');
			return Redirect::to(
					URL::route('assignments-files').
					'?class_id='.Input::get('class_id').
					'&section_id='.Input::get('section_id').
					'&academic_session_id='.Input::get('academic_session_id')
				);
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back();
		}

		
	}
}