<?php

class StudentGuardianRelation extends BaseModel
{
	protected $table = 'student_guardian_relation';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'StudentGuardianRelation';


	public $createRule = [ 'student_id'		=> array('required', 'not_in:0'),
						   'guardian_id'	 => array('required', 'not_in:0'),
						   'relationship'	=> array('required'),
						   'is_active'		=> array('requried', 'in:yes,no')];

	public $updateRule = [ 'student_id'		=> array('required', 'not_in:0'),
						   'guardian_id'	 => array('required', 'not_in:0'),
						   'relationship'	=> array('required'),
						   'is_active'		=> array('requried', 'in:yes,no')];

	protected $defaultOrder = array('orderBy' => 'student_id', 'orderOrder' => 'ASC');

	public function getViewViewData($id)
	{
		$return = array();
		$model = $this->model_name;
		$result = $model::find($id);

		//getting guardian positions

		$positions = DB::table(StudentGuardianRelationPosition::getTableName())
						->join(Group::getTableName(), Group::getTableName().'.id', '=', StudentGuardianRelationPosition::getTableName().'.group_id')
						->where(StudentGuardianRelationPosition::getTableName().'.guardian_id', $id)
						->where(Group::getTableName().'.is_active', 'yes')
						->lists('group_name', 'group_id');


		$return['data'] = $result;
		$return['groups'] = $positions;
	
		return $return;
	}

	
}