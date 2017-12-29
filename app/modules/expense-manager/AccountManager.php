<?php

class AccountManager extends BaseModel {

	protected $table = 'account';

	protected $model_name = 'AccountManager';

	protected $guarded = ['id', 'created_at', 'updated_at'];
	

}