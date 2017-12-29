<?php

class DownloadManager extends BaseModel
{
	protected $table = 'download_manager';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'DownloadManager';

	public $createRule = [
							'filename' => ['required'],
							'google_file_id' => ['required','unique:download_manager,google_file_id'],
							'parent_id' => ['required'],
							'mime_type' => ['required'],
							'description' => [],
							'no_of_downloads' => []
						];

	public $updateRule = [
							'filename' => ['required'],
							'description' => [],
						];

	
}
?>