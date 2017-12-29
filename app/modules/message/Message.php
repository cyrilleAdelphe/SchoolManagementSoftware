<?php

class Message extends BaseModel
{
	protected $table = 'messages';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Message';


	public $createRule = [ 
						
							'message'		=> array('required'),
						   'message_from_group' => array('required', 'in:student,guardian,admin,superadmin'),
						   'message_from_id' => array('required'),
						   'message_to_id' => array('required'),
						   'message_to_group' => array('required', 'in:student,guardian,admin,superadmin')
						   ];

	public $updateRule = [ 'message'		=> array('required'),
						   'message_from_group' => array('required', 'in:student,guardian,admin,superadmin'),
						   'message_from_id' => array('required'),
						   'message_to_id' => array('required'),
						   'message_to_group' => array('required', 'in:student,guardian,admin,superadmin')
						   ];

	protected $defaultOrder = array('orderBy' => 'id', 'orderOrder' => 'DESC');

	public function getViewViewData($id)
	{
		$model = $this->model_name;
		$result = $model::find($id);

		$related_students = DB::table(StudentMessageRelation::getTableName())
							  ->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', StudentMessageRelation::getTableName().'.student_id')
							  ->where('guardian_id', $id)
							  ->select('student_name')
							  ->get();
	
		return array('data' => $result, 'related_students' => $related_students);
	}
	
}