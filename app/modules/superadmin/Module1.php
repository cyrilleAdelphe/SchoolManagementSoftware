<?php

class Module extends Eloquent
{
	protected $table = 'modules';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public $createRule = array( 
        
							   );

	public $updateRule = array();

	public static function getTableName()
	{
		return with(new static)->getTable();
	}

	public $timestamps = false;

	public function permissions()
    {
        return $this->hasManyThrough('Permission', 'ModuleFunction', 'module_id', 'module_function_code_id');
        //return $this->hasManyThrough('BucketTravelagents', 'BucketTravelagentsServices', 'service_id', 'travel_agent_id');
    }

    public function modulefunction()
    {
    	return $this->hasMany('ModuleFunction', 'module_id', 'id');
    }

    public function getModules()
    {
    	$data = Module::where('is_active', 'yes')
    					->get();

    	return $data;
    }
}

?>