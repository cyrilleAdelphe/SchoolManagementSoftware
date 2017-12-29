<?php

class DownloadsSubjectMap extends BaseModel
{
	protected $table = 'downloads_subjects_map';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	private $model_name = 'DownloadsSubjectMap';
}
?>