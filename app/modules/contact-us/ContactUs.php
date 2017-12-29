<?php
class ContactUs extends BaseModel
{
	protected $table = 'contact_us';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'ContactUs';


	public $createRule = [  'sender_email'		=> array('required','email'),
							'sender_location'	=> array('required'),
							'subject'			=> array('required'),
							'query'				=> array('required'),
							'is_active'			=> array('required','in:yes,no')
						  ];

	public $updateRule = [  'sender_email'		=> array('required','email'),
							'sender_location'	=> array('required'),
							'subject'			=> array('required'),
							'query'				=> array('required'),
							//'is_active'			=> array('required','in:yes,no')
						  ];

}