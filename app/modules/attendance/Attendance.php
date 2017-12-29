<?php

class Attendance extends BaseModel
{
	protected $table = 'attendance';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'Attendance';



	public $createRule = [];

	public $updateRule = [];

	public function allowedToTakeAttendance($class, $section, $session_id)
	{
		$return = false;
		$controller = new AttendanceController;

		if($controller->current_user->role == 'superadmin')
		{
			$return = true;
		}
		else if($controller->current_user->role == 'admin')
		{
			$post = DB::table(Admin::getTableName())
						//->join(Employees::getTableName(), Admin::getTableName().'.admin_details_id', '=', Employees::getTableName().'.id')
						->join(EmployeePosition::getTableName(), EmployeePosition::getTableName().'.employee_id', '=', Admin::getTableName().'.id')
						->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
						->where(Admin::getTableName().'.id', Auth::admin()->user()->id)
						->list(Group::getTableName().'.group_name');

			foreach($post as $p)
			{
				if($p == 'Teacher')
				{
					//check if class teacher or not
					$result = DB::table(Teacher::getTableName())
								->where('teacher_id', Auth::admin()->user()->id)
								->where('is_class_teacher_id', 'yes')
								->where('class', $class)
								->where('section', $section)
								->where('session_id', $session_id)
								->where('is_active', 'yes')
								->first();

					if($result)
					{
						$return = true;
						break;
					}
				}
				else
				{
					//check if the group is allowed to insert student attendance
					$result = DB::table(PermissionByRouteName::getTableName())
								->where('route_name', Route::currentRouteName())
								->where('group_id', $post)
								->count();

					if($result)
					{
						$return = true;
						break;
					}

				}
			}

			return $return;
		}
	}


}