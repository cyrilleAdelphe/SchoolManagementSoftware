<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;

class Admin extends BaseModel implements UserInterface
{
	use UserTrait;

	protected $table = 'admins';

	protected $guarded = ['id', 'created_at', 'updated_at'];
	
	public $createRule = [ 'username' 		 => array('required', 'unique:admins,username', 'min:5'),
						  'password' 		 => array('required', 'min:6'),
						  'confirm_password' => array('required_with:password', 'same:password'),
						  'admin_details_id' => array('required'),
						  'name'			 => array('required'),
						  'address'			 => array(),
						  'email'			 => array(),
						  'contact'			 => array()];
	
	public $updateRule = ['username' 		 => array('required', 'unique:admins,username', 'min:5'),
						  'password' 		 => array('required', 'min:6'),
						  'confirm_password' => array('required_with:password', 'same:password'),
						  'admin_details_id' => array('required'),
						  'name'			 => array('required'),
						  'address'			 => array(),
						  'email'			 => array(),
						  'contact'			 => array()];

	public static function getTableName()
	{
		return with (new static)->getTable();
	}
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
}

?>