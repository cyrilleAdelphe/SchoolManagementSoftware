<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;

class Users extends BaseModel implements UserInterface
{
	use UserTrait;

	protected $table = 'users';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Users';
	
	public $createRule = [
							'username' => ['unique:users,username'],
							'email'	   => ['unique:users,email'],
							'password' => ['required'],
							'confirm_password' => ['required_with:password', 'same:password'],
							'contact' => [],
							'role' => ['required', 'in:student,guardian'],
							'name' => ['required'],
							'user_details_id' => ['required']
						];

	public $updateRule = [
							/*'username' => ['unique:users,username'],
							'email'	   => ['unique:users,email'],
							'new_password' => ['required'],
							'confirm_password' => ['required_with:password', 'same:password'],
							'contact' => [],
							'role' => ['required', 'in:student,guardian'],
							'name' => ['required'],
							'user_details_id' => ['required']*/
						];
}

?>