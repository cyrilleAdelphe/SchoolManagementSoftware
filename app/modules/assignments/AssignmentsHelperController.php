<?php

class AssignmentsHelperController
{
	public static function uploadFile($data, $file_to_upload)
	{
		
		$assignment_folder_config = json_decode(File::get(
			app_path() .
			'/modules/assignments/config/assignment_folder_config.json'
		));

		$parent = DownloadManager::find(
			$assignment_folder_config->assignment_folder_id
		);

		if (!$parent)
		{
			Session::flash('error-msg', 'folder for assignment not configured');
			return Redirect::back();
		}

		if ($data['filename'] == '')
		{
			Input::merge(array(
				'filename' => $file_to_upload->getClientOriginalName()
			));
		}

		$data = array_merge(
			$data,
			array(
				'no_of_downloads' => 0,
				'parent_id'				=> $parent->id,
				'mime_type'				=> $file_to_upload->getMimeType(),
				'google_file_id'	=> 'to be filled later',
				'filename'				=> Input::get('filename')
			)
		);

		$file = $file_to_upload;
		$google_drive = new EasyDriveAPI2(Request::url());
		$file = $google_drive->insertFile(
			$data['filename'],
			'file created by google drive api',
			$parent->google_file_id,
			$data['mime_type'],
			$file
 		);

		if (!$file)
		{
			throw new Exception('Unable to create file');
		}

 		$data['google_file_id'] = $file->getId();
 		
 		return $data;

	}

}