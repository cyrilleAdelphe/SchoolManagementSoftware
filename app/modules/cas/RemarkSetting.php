<?php

class RemarkSetting extends BaseModel
{
	protected $table = 'cas_remark_setting';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'RemarkSetting';

	public $createRule = [
							'remarks_number'	=>	array('required', 'unique:cas_remark_setting,remarks_number'),
							'remarks'		=>	array('required')
						 ];

	public $updateRule = [
							'remarks_number'	=>	array('required', 'unique:cas_remark_setting,remarks_number'),
							'remarks'		=>	array('required')
						];



	public function getListViewData($queryString = array())
	{	
     	$data = RemarkSetting::all();

        return $data;
	}

	
	
}

