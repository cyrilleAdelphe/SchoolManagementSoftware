<?php

class DormitoryRoomController extends BaseController
{
	protected $view = 'dormitory-room.views.';

	protected $model_name = 'DormitoryRoom';

	protected $module_name = 'dormitory-room';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'dormitory_name',
										'alias'			=> 'Name'
									),
									array
									(
										'column_name' 	=> 'dormitory_code',
										'alias'			=> 'Code'
									),
									array
									(
										'column_name' 	=> 'location',
										'alias'			=> 'Location'
									)
								 );

	public function postDelete()
	{
		AccessController::allowedOrNot('dormitory-room', 'can_delete');
		$model = new $this->model_name;
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$students = DormitoryStudent::where('dormitory_id', $id)
						->get();

			if(count($students))
			{
				Session::flash('error-msg', 'The dormitory has/had students');
			}
			else
			{
				$record->delete();
				Session::flash('success-msg', 'Delete Successful');
			}
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	} 
	

}