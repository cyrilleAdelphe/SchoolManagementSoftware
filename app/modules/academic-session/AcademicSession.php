<?php

class AcademicSession extends BaseModel
{
	protected $table = 'academic_session';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'AcademicSession';


	public $createRule = [ 'session_name'	=>	array('required', 'unique:academic_session,session_name'),
						   'session_start_date_in_ad'	=> array('required', 'unique:academic_session,session_start_date_in_ad', 'date_format:Y-m-d'),
						   'session_start_date_in_bs'	=> array('required', 'unique:academic_session,session_start_date_in_bs'),
						   'session_end_date_in_ad'		=> array('required', 'unique:academic_session,session_end_date_in_ad', 'date_format:Y-m-d'),
						   'session_end_date_in_bs'		=> array('required', 'unique:academic_session,session_end_date_in_bs') ];

	public $updateRule = [ 'session_name'	=> array('required', 'unique:academic_session,session_name'),
						   'session_start_date_in_ad'	=> array('required', 'date_format:Y-m-d', 'unique:academic_session,session_start_date_in_ad'),
						   'session_start_date_in_bs'	=> array('required', 'unique:academic_session,session_start_date_in_bs'),
						   'session_end_date_in_ad'		=> array('required', 'date_format:Y-m-d', 'unique:academic_session,session_end_date_in_ad'),
						   'session_end_date_in_bs'		=> array('required', 'unique:academic_session,session_end_date_in_bs') ];
}