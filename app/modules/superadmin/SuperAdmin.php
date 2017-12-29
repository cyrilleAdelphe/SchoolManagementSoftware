<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;

class SuperAdmin extends BaseModel implements UserInterface
{
	use UserTrait;

	protected $table = 'superadmin';

	protected $fillable = ['name', 'username', 'password', 'contact', 'is_active'];
	
	public $createRule = array
				  (
					/*'product_name'			=> 	array('required', 'max:100', 'regex:/^[0-9a-zA-Z" "-]+$/'),
					'price'					=>	array('required', 'regex:/^([0-9]+)$|^([0-9]+\.[0-9]{2})$/'),
					'quality'				=>	'required',
					'description'			=> 	'required',
					'delivery_process'		=> 	'required'*/

					'username'	=>	array('unique:superadmin,username', 'required', 'min:1')
				);

	public $updateRule = ['username'	=>	array('unique:superadmin,username', 'required', 'min:1')];

	public static function getTableName()
	{
		return with(new static)->getTable();
	}
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function dashboardGetUnreadMessages()
	{
		//get id of current superadmin user
		return DashboardController::dashboardGetUnreadMessages('superadmin', Auth::superadmin()->user()->id);
	}

	/*public function dashboardGetTeachers()
	{
		return DB::table(EmployeePosition::getTableName())
					->join(Group::getTableName(), Group::getTableName().'.id', '=', EmployeePosition::getTableName().'.group_id')
					->where('group_name', 'Teacher')
					->count();

	}

	public function dashboardGetTotalStudents()
	{
		return DB::table(StudentRegistration::getTableName())
				  ->where('is_active', 'yes')
				  ->count();
	}*/

	public function dashboardGetUpcomingEvents($no_of_events = 10, $current_date = '')
	{	
		$current_date = strlen($current_date) ? $current_date : Carbon\Carbon::now()->format('Y-m-d');
		return DB::table(Events::getTableName())
				->select(array('id', 'title', 'from_ad', 'from_bs', 'to_ad', 'to_bs'))
				->where('is_active', 'yes')
				->where('from_ad', '>=', $current_date)
				->where('is_active', 'yes')
				->take($no_of_events)
				->get();
	}

	public function dashboardGetNotice()
	{
		return json_decode(File::get(app_path().'/modules/notice/notice.json'));
	}


}

?>