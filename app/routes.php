<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

include_once('constants.php');
include_once('school_specific_constants.php');

/*$data = (new GcmController)->send_gcm_notify('eEol4UTljm8:APA91bHSO2-tY6bAtuUZU3oBuoosDEsyrrPw32fvM-dGKbZsSYZtiCFHXzbJRUjk4mFugGulLBQVMg8w5VwDRtIDnDlLBSqr6YSgEmtEAA0AN1wAIYNPjdVjmI-S8W7XKN9C0kDnSsYe', 'again');
echo '<pre>';
print_r($data);
die();
*/
/*$gcm = new GcmController;
$gcm->send_gcm_notify('ejDKvTxAUE4:APA91bEKuQ1Zr7SD_sc_zPa19yiEOJUDM30DeTF0peWOr6SO9gYPBO8uVh9IVRJB1tkQyQiOPSrK_C8uHGwdLcVk5MJzbXmWR30QUhYOiPuUSca2TOZxqFNSsYhXuv6pqSN9xvekEZMS', 'test');*/

App::register('DatabaseActionsServiceProvider');

Route::get('extract-insert-sql', function() {
	$contents = File::get('/home/r_shrestha/Downloads/sparklew_school_management_demo.sql');

	echo '<pre>';

	$insert_statements = '';
	while (strlen($contents)) {
		$insert_start = strpos($contents, 'INSERT INTO');
		$insert_end = strpos($contents, ';', $insert_start);

		if ($insert_start === FALSE || $insert_end === FALSE) {
			break;
		}
		
		$insert_statement = substr(
			$contents, 
			$insert_start, 
			$insert_end - $insert_start + 1
		);
		$insert_statements .= $insert_statement . "\n";
		$contents = substr($contents, $insert_end + 1);

		// print($insert_statement);
		// print("\n");
	}

	print($insert_statements);
	
	
});

Route::get('excel-test', function() {
	ini_set('max_execution_time', 300);

	// TODO: use this line to set the row from where the header of file starts 
	Config::set('excel::import.startRow', 1);
	
	// TODO: this array maps sheet name to class/section
	// 'sheet name' => ['class_name', 'section_code']
	$class_map = array(
		'Class-Two' => ['Two', 'A'],
		'Class-Three' => ['Three', 'A'],
		'Class-Four' => ['Four', 'A'],
		'Class-five' => ['Five', 'A'],
		'Class-Six' => ['Six', 'A'],
		'Class-seven' => ['Seven', 'A'],
		'Class-eight' => ['Eight', 'A'],
		'Class-Nine' => ['Nine', 'A'],
		'Class-ten' => ['Ten', 'A'],
		'Class-one' => ['One', 'A'],
		'Class-UKG' => ['UKG', 'A'],
		'Class-LKG' => ['LKG', 'A'],
		'Class Nursery' => ['Nursery', 'A'],
	);


	// TODO: give location of the file
	
	$reader = Excel::load(base_path() . '/public/DetailStudents2073.xlsx')
				->get();

	// For file with multiple sheet
	echo '<pre>';
	$parent_names = array();

	foreach ($reader as $sheet)
	{
		print_r($sheet->getTitle() . "\n");

		if (!isset($class_map[trim($sheet->getTitle())])) {
			continue;
		}
		print_r($class_map[trim($sheet->getTitle())]);
		echo "\n";

		foreach ($sheet as $row)
		{
			// convert object to array
			$data = json_decode(json_encode($row), true);
			if (!isset($data['students_name']))
			{
				continue;
			}

			if (isset($data['fathers_name']))
			{
				$data['fathers_name'] = ucwords(trim($data['fathers_name']));
			}
			else
			{
				$data['fathers_name'] = '';
			}
			
			if (isset($data['mothers_name']))
			{
				$data['mothers_name'] = ucwords(trim($data['mothers_name']));
			}
			else
			{
				$data['mothers_name'] = '';
			}
			$parent_name = implode(', ', [$data['fathers_name'], $data['mothers_name']]);
			// print_r($parent_name);
			// print_r("\n");

			$data = array(
				trim($sheet->getTitle()),
				$data['students_name'],
				// TODO: place the parent information available here
				$data['contact_number'], // 'primary_contact',
				'', // 'secondary_contact' 
				$data['address'], // 'current_address', 
				'', // 'permanent_address', 
				'', // 'occupation'
				'', // 'email'
			);
			if (isset($parent_names[$parent_name]))
			{
				$parent_names[$parent_name][] = $data;
			}
			else
			{
				$parent_names[$parent_name] = array($data);
			}
			
			// print_r($data);
		}
	}

	echo "\n";
	$data = array(
		[
			'guardian_name', 
			'student_usernames', 
			'student_relationships', 
			'primary_contact',
			'secondary_contact',
			'current_address', 
			'permanent_address', 
			'occupation',
			'email'
		]
	);

	foreach ($parent_names as $name => $info)
	{
		$parents = explode(', ', $name);
		$parents = array_map('trim', $parents);

		echo $name . "\n";
		print_r($info);

		$student_usernames = array_map(
			function ($i) use($name, $class_map) {

				if ( isset($class_map[$i[0]]) ) {
					echo $name . "\n";

					$students = DB::table(Student::getTableName())
						->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
						->join(Classes::getTableName(), Classes::getTableName().'.id', '=', Student::getTableName().'.current_class_id')
						->join(ClassSection::getTableName(), ClassSection::getTableName().'.class_id', '=', Classes::getTableName().'.id')
						->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
						->where(Classes::getTableName().'.class_name', $class_map[$i[0]][0])
						->where(ClassSection::getTableName().'.section_code', $class_map[$i[0]][1])
						->where(StudentRegistration::getTableName().'.student_name', $i[1])
						->where(Users::getTableName().'.role', 'student')
						->select(Users::getTableName().'.username')
						->get();

					if (count($students) > 1) {
						echo 'Error: Multiple Student: ' . $i[1] . ' (' . $i[0] . ')' . "\n";
					} elseif (count($students) == 0) {
						echo 'Error: No student: ' . $i[1] . ' (' . $i[0] . ')' . "\n";
					} else {
						echo $students[0]->username . "\n";
						return $students[0]->username;
						// return $i[1] . ' ('. $i[0] .')';
					}
				}
				
				// return $i[1] . '(' . $i[0] . ')';
			}, 
			$info
		);

		$student_usernames = array_filter($student_usernames, function($i) {
			return (bool)$i;
		});

		if (count($student_usernames)) {
			$no_students = count($student_usernames);

			$student_usernames = implode(',', $student_usernames);

			if ($parents[0])
			{
				$data[] = array(
					$parents[0], 
					$student_usernames, 
					implode(',',array_fill(0, $no_students, 'father')),
					$info[0][2],
					$info[0][3],
					$info[0][4],
					$info[0][5],
					$info[0][6],
					$info[0][7],
				);
			}

			if ($parents[1])
			{
				$data[] = array(
					$parents[1], 
					$student_usernames, 
					implode(',',array_fill(0, $no_students, 'mother')),
					// TODO: place the mother information available here
					$info[0][2],
					$info[0][3],
					$info[0][4],
					$info[0][5],
					$info[0][6],
					'',
				);
			}
		}

		// if (len($info) > 1)
		// {
		// 	print $name;
		// 	echo "\n";
		// 	print_r($info);
		// 	echo "\n";
		// }
	}

	// print_r($data);

	Excel::create('parent', function($excel) use($data) 
		{
			$excel->sheet('Parents', function($sheet) use($data) 
		    {
		    	$sheet->fromArray($data);
				}
	    );

		}
	)->store('xls');

	Config::set('excel::import.startRow', 1);
	
});


////////////////////////
/////THESE ARE SYSTEM Routes
////////////////////////////////
Route::post('/remove-global/{type}', array(
	'as'	=>	'remove-global',
	'uses'	=>	'ConfigurationController@removeGlobal'));


// Route::get('/', 'HomeController@getTest');
/*************** This is test controller **********************************/
Route::get('/debug1', //array(
	//'as' => 'debug1',
	//'uses' => 'DebugController@index'));
	function(){
		return View::make('attendance-detail');
	});

Route::post('/debug2', array(
	'as' => 'debug2',
	'uses' => 'DebugController@check'));

Route::post('global-export-to-excel',
	['as'	=>	'global-export-to-excel',
	 'uses'	=>	'ExportToExcelController@getExportExcel']);
/****************************************************************************/

Route::get('/generate', 'GeneratorController@getGeneratorForm');
Route::post('/generate-post', 'GeneratorController@postGeneratorForm');


$routes = scandir(app_path().'/modules');
$routes = array_splice($routes, 2);
foreach($routes as $index => $module)
{
	if(File::exists(app_path().'/modules/'.$module.'/route.php'))
	{
		$routes[$index] = app_path().'/modules/'.$module.'/route.php';
	}
	else
	{
		unset($routes[$index]);
	}
}

/*************** These are required in all apps ****************************/
foreach($routes as $route)
{
	require_once($route);
}


require_once(app_path().'/ajax-route.php');



Route::get('ajax-get-exam-ids-from-session-id',
	  ['as'	=>	'ajax-get-exam-ids-from-session-id',
	   'uses'	=>	'AjaxController@getExamIdsFromSessionId']); //ajax-get-section-ids-from-class-id

Route::get('ajax-get-section-ids-from-class-id',
	  ['as'	=>	'ajax-get-section-ids-from-class-id',
	   'uses'	=>	'AjaxController@getSectionIdsFromClassId']);

Route::get('ajax-get-students-from-class-id-and-student-id',
	['as'	=>	'ajax-get-students-from-class-id-and-student-id',
	 'uses'	=>	'AjaxController@getStudentsFromClassIdAndStudentId']);

Route::get('ajax-get-class-ids-from-session-id',
	  ['as'	=>	'ajax-get-class-ids-from-session-id',
	   'uses'	=>	'AjaxController@getClassIdsFromSessionId']);


/****************************************************************************/
Route::get('/', [function()
		{
			return View::make('frontend.dashboard');
		},
		'as' => 'home-frontend'
	]
);

Route::get('/our-team', [
	'as'	=> 'our-team',
	'uses'	=> function() {
		return View::make('frontend.our-team');
	}
]);


/*
 * Truncate database
 */
Route::group(array('before' => 'reg-superadmin'), function() {
	Route::get('/truncate-database', function() {

		$mysql = Config::get('database.connections.mysql');

		$colname = 'Tables_in_' . $mysql['database'];

		$tables = DB::select('SHOW TABLES');

		foreach($tables as $table) {
			$droplist[] = substr($table->$colname, strlen($mysql['prefix']));
		}

		$noDroplist = array(
			'superadmin',
			'groups'
		);

		DB::beginTransaction();
		//turn off referential integrity
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');

		foreach ($droplist as $table) {
			if (!in_array($table, $noDroplist)) {
				DB::table($table)->truncate();
			}
		}

		//turn referential integrity back on
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
		DB::commit();

		echo 'Database truncated';

	});
});

Route::group(['before' => 'csrf'], function()
{
	Route::post('reset-password',
		['as'	=>	'system-reset-password',
		 'uses'	=>	'HomeController@postResetPassword']);
});



/************* Basic system routes ************************/

Route::post('/delete-session-data/{session_name}', 
		['as'	=>	'delete-session-data-post',
		 'uses'	=>	'SystemController@deleteSessionData']);

/***********************************************************/
/************ site maintainance ******************/
require_once('site-maintainance-route.php');
/**************************************************************/
//////////// for error handling ///////////////////////////////
include_once('errors.php');
/****************************************************************/
?>