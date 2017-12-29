<?php

class HelperController extends Controller
{
	public static function getStudentIdFromUsername($username)
	{
		$user = Users::where('username', $username)
									->where('role', 'student')
									->first();
		return $user ? $user->user_details_id : 0;
	}
	public static function IsInUserGroup($admin_id, $group_name)
	{
		$group_table = Group::getTableName();
		$employee_position_table = EmployeePosition::getTableName();

		$result = DB::table($employee_position_table)
					->join($group_table, $group_table.'.id', '=', $employee_position_table.'.group_id')
					->where($group_table.'.group_name', $group_name)
					->where($employee_position_table.'.employee_id', $admin_id)
					->count();

		if($result)
		{
			$result = true;
		}
		else
		{
			$result = false;
		}

		return $result;
	}

	public static function getEmployeeIdfromUsername($username)
	{
		$employee = Admin::where('username', $username)
								->select('admin_details_id')
								->first();


		return $employee ? $employee->admin_details_id: 0;

	}

	public static function getSuperadminIdfromUsername($username)
	{
		$employee = Superadmin::where('username', $username)
								->select('id')
								->first();


		return $employee ? $employee->id: 0;

	}

	public static function getStudentsGCM($class_id, $section_id)
	{
		$section = Section::where('id', $section_id)
								->first();

		if(!$section)
		{
			return false;
		}
		else
		{
			$section_code = $section->section_code;
		}

		$current_session_id = HelperController::getCurrentSession();

		$student_table = Student::getTableName();
		$gcm_table = PushNotifications::getTableName();

		$student_ids = DB::table($student_table)
						//->join($gcm_table, $gcm_table.'.user_id', '=', $student_table.'.student_id')
						->where('current_session_id', $current_session_id)
						->where('current_class_id', $class_id)
						->where('current_section_code', $section_code)
						->lists('student_id');

		$gcm_ids = DB::table($gcm_table)
						//->join($gcm_table, $gcm_table.'.user_id', '=', $student_table.'.student_id')
						->whereIn('user_id', $student_ids)
						->where('user_group', 'student')
						->lists('gcm_id');

		return array($student_ids, $gcm_ids);
	}

	public static function getCurrentSessionClassList()
	{
		return DB::table(Classes::getTableName())
					->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', 'academic_session_id')
					->select(Classes::getTableName().'.id', 'class_name')
					->where('is_current', 'yes')
					->lists('class_name', 'id');
	}
	
	public static function generateSelectListWithDefault($modelname, $name, $value, $field_name, $selected = '', $condition = array(), $class = 'form-control',$default_option = '-- Select --', $is_array = false)
	{
		
		$columns = $modelname::where('is_active', 'yes');

		
		foreach($condition as $c)
		{
			if(isset($c['operator']))
				$columns->where($c['field_name'], $c['operator'], $c['value']);
			else
				$columns->where($c['field_name'], $c['value']);
		}
		//array('', '', '')
		

		$columns->distinct();
		$columns = $columns->orderBy($name, 'ASC')->get(array($name, $value));
		

		$select = $is_array ? '<select id = "'.$field_name.'" name = "'.$field_name.'[]" class = "'. $class . '" >'."\n" : '<select id = "'.$field_name.'" name = "'.$field_name.'" class = "'. $class . '" >'."\n";

		$select .= '<option value = "0">'. $default_option . '</option>';
		foreach($columns as $col)
		{
			if($col->$value == $selected)
				$sel = 'selected';
			else
				$sel = '';
			$select .= '<option value = '.$col->$value.' '.$sel.'>'.$col->$name.'</option>'."\n";
		}
		
		$select .= '</select>';	
		
		return $select;
	}
	
	public static function getCurrentSession()
	{
		return AcademicSession::where('is_current', 'yes')
										  ->where('is_active', 'yes')
										  ->pluck('id');
	}

	public static function csvToArray($filename, $search_for_key = -1, $search_for_value = '', $delimiter = ',', $is_single_value = true)
	{
		$data = array();
		$header = NULL;
		if (($handle = fopen($filename, 'r')) !== FALSE ) 
		{
	        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
	        {   
	        	//print_r($row);
	           		if($search_for_key > -1)
	           		{
	           			if($row[$search_for_key] == $search_for_value)
	           				if($is_single_value)
	           				{
	           					$data = $row;
	           					break;
	           				}
	           				else
	           					$data[] = $row;
	           		}
	           		else
	           		{
	           			$data[] = $row;
	           		}
	        }
	    }
        fclose($handle);

        return $data;
	}

	public static function getNumberOfDaysInAMonth($month, $year = 2001)
	{
		$start_date = Carbon\Carbon::create($year, $month, 1, 0,0,0, Config::get('app.timezone'));
		$end_date = Carbon\Carbon::parse(join('-', [$year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year)]));			
		$end_date = $end_date->diffInDays($start_date) + 1;
		return $end_date;
	}

	public static function getNepaliMonths()
	{
		return ['Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'];
	}

	public static function getNepaliMonth($month)
	{
		$month = (int) $month - 1;
		$months = ['Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'];

		return isset($months[$month])? $months[$month] : 'NA';
	}

	public static function formatNepaliDate($date)
	{
		$array = explode('-', $date);
		if (count($array) != 3) return '';
		if($array[1]<1 || $array[1]>12) return '';

		$months = HelperController::getNepaliMonths();

		return $array[2] . ' ' . $months[$array[1]-1] . ' ' . $array[0];
	}

	public static function getMonthName($month_number)
	{
		if (CALENDAR == 'BS')
		{
      $months = HelperController::getNepaliMonths();
		}
    else
    {
      $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December');
    }

    if ($month_number >= 1 && $month_number <= count($months))
    {
    	return $months[$month_number - 1];
    }
    else
    {
    	return '';
    }
  }

	public static function getCurrentNepaliMonth()
	{
		$nepali_date = (new DateConverter)->ad2bs(date('Y-m-d'));
		$array = explode('-', $nepali_date);
		return $array[1];
	}

	public static function getCurrentDate()
	{
		if (CALENDAR == 'BS')
		{
			$slash_date = HelperController::getCurrentNepaliDate();
			$date_array = explode('/', $slash_date);
			$dash_date = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
			return HelperController::formatNepaliDate($dash_date);
		}
		else
		{
			return date('d F Y');
		}
	}

	public static function getCurrentNepaliDate()
	{
		$date = (new DateConverter)->ad2bs(date('Y-m-d'));
		$date_array = explode('-', $date);
		$date = $date_array[2] . '/' . 
						str_pad($date_array[1], 2, '0', STR_PAD_LEFT) . '/' .
						$date_array[0];
		return $date;
	}

	// @param English date 'Y-m-d H:i:s' 
	// @output pretty date format (d F Y g:i A) in English or Nepali
	public static function dateTimePrettyConverter($date)
	{
		if (!HelperController::validateDate($date))
		{
			// in case format not Y-m-d H:i:s, return as is
			return $date;
		}
		if (CALENDAR == 'BS')
		{
			$dash_date = (new DateConverter)->ad2bs(substr($date, 0, 10));
			return HelperController::formatNepaliDate($dash_date) .
				' ' . 
				DateTime::createFromFormat('H:i:s', substr($date, 11))->format('g:i A');
		}
		else
		{
			return DateTime::createFromFormat('Y-m-d H:i:s', $date)
				->format('g:i A');
		}
	}


	public static function validateDate($date, $format = 'Y-m-d H:i:s')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}
	
	public static function pluckFieldFromId($model_name, $field_name, $id, $not_id = false, $search_field_name = '')
	{
		if($not_id)
		{
			return $model_name::where($search_field_name, $id)
						  ->pluck($field_name);
		}
		else
		{
			return $model_name::where('id', $id)
						  ->pluck($field_name);	
		}
		/*return $model_name::where('id', $id)
						  ->pluck($field_name);*/
	}
	public static function getAllowedGroups($allowed_groups_string = '')
	{
		$groups = array();
		
		preg_match_all('/[0-9]+/', $allowed_groups_string, $matches);
		
		return $matches[0];
	}

	public static function getStringAfterLastSlash($string)
	{
		$string = substr($string, strrpos($string, '/')+1);
		return $string;
	}

	public static function checkDuplicateNameInDatabase($model_name, $column_name, $text_to_check)
	{
		$status = "error";
		
		if(trim($model_name) == '' || trim($column_name) == '' || trim($text_to_check) == '')
		{
			$message = "model name and/or column name and/or text to check not given";
		}
		else
		{
			try
			{
				$data = $model_name::where($column_name, $text_to_check)
									->get();

				if(count($data) == 0)
				{
					$status = 'success';
					$message = 'you can add this name';
				}
				else
				{
					$message = 'A record already exists with name '.$text_to_check;
				}

			}
			catch(PDOException $e)
			{
				$message = $e->getMessage();
			}

			return array('status' => $status, 'message' => $message);
		}
	}

	public static function generateSelectList($modelname, $name, $value, $field_name, $selected = '', $condition = array(), $allow_non_active_data = false)
	{
		
		if($allow_non_active_data)
		{
			$columns = new $modelname;
		}
		else
		{
			$columns = $modelname::where('is_active', 'yes');	
		}

		if(count($condition))
		{
			foreach($condition as $con)
			{
				$columns = $columns->where($con['field'], $con['operator'], $con['value']);
			}
		}

		$columns->distinct();
		
		$model = new $modelname;
		if (
			isset($model->defaultOrder) && 
			isset($model->defaultOrder['orderBy']) &&
			isset($model->defaultOrder['orderOrder'])
		)
		{
			$columns = $columns->orderBy( $model->defaultOrder['orderBy'], 
																		$model->defaultOrder['orderOrder'] );
		}
		else
		{
			$columns = $columns->orderBy($name, 'ASC');
		}
			$columns = $columns->get(array($name, $value));
		
		$select = '<select id = "'.$field_name.'" name = "'.$field_name.'" class = "form-control" >';

		$select .= '<option value = "0"> -- Select -- </option>';
		foreach($columns as $col)
		{
			if($col->$value == $selected)
				$sel = 'selected';
			else
				$sel = '';
			$select .= '<option value = "'.$col->$value.'" '.$sel.'>'.$col->$name.'</option>';
		}
		
		$select .= '</select>';	
		
		return $select;
	}

	public static function generateStaticSelectList($data = array('id' => 'name'), $field_name, $selected = 0, $default = '<option value = "0"> -- Select -- </option>', $is_array = false)
	{
		$select = '';

		$select = '<select id = "'.$field_name.'" name = "'.$field_name.'" class = "form-control '.$field_name.'" >'."\n";
		$select .= $default;
		
		foreach($data as $id => $name)
		{
			if($selected == $id)
					$sel = 'selected';
			else
				$sel = '';
			$select .= '<option value = '.$id.' '.$sel.'>'.$name.'</option>'."\n";

		}

		$select .= '</select>';	
		return $select;
	}

	public static function getUser()
	{
		if(Auth::user()->check())
		{
			$user = Auth::user()->username;
			$id = Auth::user()->id;
			$group = 'user';
		}
		else if(Auth::superAdmin()->check())
		{
			$user = Auth::superAdmin()->username;
			$id = Auth::superAdmin()->id;
			$group = 'superadmin';
		}
		else if(Auth::admin()->check())
		{
			$user = Auth::admin()->username;
			$id = Auth::admin()->id;
			$group = 'admin';
		}
		else
		{
			$user = 'unsubscribed';
			$id = 0;
			$group = 'unsubscribed';
		}

		return array('user' => $user, 'id' => $id, 'group' => $group);
	}

	public static function getUserRole()
	{
		//HelperController::loginWithToken();
		if(Auth::superadmin()->check())
			return 'superadmin';
		else if(Auth::admin()->check())
			return 'admin';
		else if(Auth::user()->check())
				return 'user';
		else
			return 'frontend';
	}

	public static function checkAdminGroup($group_name) 
	{
		if ( Auth::admin()->guest() )
		{
			return false;
		}
		else {
			$admin = DB::table(Admin::getTableName())
								->join(EmployeePosition::getTableName(), EmployeePosition::getTableName().'.employee_id', '=', Admin::getTableName().'.admin_details_id')
								->join('groups', 'groups.id', '=', EmployeePosition::getTableName().'.group_id')
								->where('groups.group_name', $group_name)
								->where(Admin::getTableName().'.id', Auth::admin()->user()->id)
								->first();
			return (bool)$admin;
		}	
	}

	public static function underscoreToSpace($word)
	{
		$words = explode('_', $word);

		$temp = '';
		if(count($words))
		{
			foreach($words as $word)
			{
				$temp .= ucfirst($word).' ';
			}
		}

		$temp = $temp == '' ? ucfirst($word) : trim($temp);

		return $temp; 
	}

	public static function dashToSpace($word)
	{
		$words = explode('-', $word);

		$temp = '';
		if(count($words))
		{
			foreach($words as $word)
			{
				$temp .= ucfirst($word).' ';
			}
		}

		$temp = $temp == '' ? ucfirst($word) : trim($temp);

		return $temp; 
	}

	public static function getCurrentUser()
	{
		$user = new stdClass;

		//HelperController::loginWithToken();
		//if token exists then login the user

		if(Auth::user()->check())
		{
			$user = new stdClass;
			$user->role = Auth::user()->user()->role;
			$user->user_id = Auth::user()->user()->user_details_id;
			$user->user_details_id = Auth::user()->user()->user_details_id;
		}
		elseif(Auth::superadmin()->check())
		{
			$user = new stdClass;
			$user->role = 'superadmin';
			$user->user_id = Auth::superadmin()->user()->id;
			$user->id = Auth::superadmin()->user()->id;
		}
		elseif(Auth::admin()->check())
		{
			$user = new stdClass;
			$user->role = 'admin';
			$user->user_id = Auth::admin()->user()->admin_details_id;
			$user->admin_details_id = Auth::admin()->user()->admin_details_id;
		}
		else
		{
			$user = new stdClass;
			$user->id = 0;
			$user->role = 'guest';
			$user->user_id = 0;
		}

		return $user;
	}

	public static function limitTextSize($text, $size)
	{
		//$text = '';
		$text = trim(strip_tags($text));
		if(strlen($text) > $size)
		{
			$text = substr($text, 0, $size).'...';
		}

		return $text;
	}

	public static function changeValidationErrorMessagesToString($messages = array())
	{
		echo '<pre>';
		$return = '';
		foreach($messages as $field)
		{
			print_r($field);
			$return .= implode('<br>', $field);
		}

		echo $return;
		die();

		return $return;
	}

	public static function getGroupIdsOfUser($user_type, $user_id)
	{
		$return  = array(0);

		if($user_type == 'admin')
		{
			$return = DB::table(EmployeePosition::getTableName())
						->where('employee_id', $user_id)
						->where('is_active', 'yes')
						->lists('group_id');
		}
		elseif($user_type == 'user')
		{
			$return = DB::table('map_user_group')
						->where('user_id', $user_id)
						->where('is_active', 'yes')
						->lists('group_id');
		}
		else
		{
			$return = array(0);
		}

		return $return;
	}

	/**
	 * Finds the already existing elements within the array $list in the $field_name of model $model_name
	 */
	public static function findNonUniques($list, $model_name, $field_name)
	{
		$non_uniques = array();
		
		$model = new $model_name;
		foreach($list as $element)
		{
			$collection = $model->select($field_name)
								->where($field_name,$element)
								->get();

			if(!$collection->isEmpty())
			{
				$non_uniques[] = $element;
			}
		}
		return $non_uniques;
	}

	/**
	 * Finds the unique elements within the array $list in the $field_name of model $model_name
	 */
	public static function findUniques($list, $model_name, $field_name)
	{
		$uniques = array();
		
		$model = new $model_name;
		foreach($list as $element)
		{
			$collection = $model->select($field_name)
								->where($field_name,$element)
								->get();

			if($collection->isEmpty())
			{
				$uniques[] = $element;
			}
		}
		return $uniques;
	}

	/*
	 * Convert a csv string where each element can be a range (separated by -) or a number to csv of numbers only 
	 * E.g. : Input: 1,2,4-7,15,17-20  Output: 1,2,4,5,6,7,15,17,18,19,20
	 */
	public static function csvRangeToCsv($csv_range)
	{
		$csv_range_exploded = explode(',',$csv_range);
 		$csv = array();
 		/*foreach($csv_range_exploded as $range)
 		{
 			$limits = explode('-',$range);
 			if(sizeof($limits)==1)
 			{
 				$csv[] = $limits[0];	
 			}
 			elseif(sizeof($limits)==2)
 			{
 				for($i_item=$limits[0];$i_item<=$limits[1];$i_item++)
 				{
 					$csv[] = $i_item;
 				}
 			}
 		}
 		$csv = array_unique($csv,SORT_NUMERIC);
 		sort($csv,SORT_NUMERIC);*/
 		return implode(',',$csv);
	}

	public static function getNumberOfDays($from, $to){
	    $from_date = new DateTime($from);
	    $to_date = new DateTime($to);
	    $multiplier = ($from_date > $to_date) ? -1 : 1;
	    return $multiplier * $from_date->diff($to_date)->days;
		// - See more at: https://arjunphp.com/how-to-count-days-between-two-dates-in-php/#sthash.XLxlOrZB.dpuf
	}

	public static function limitWordCount($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
						unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}

	public static function restoreDatabase()
	{
		$mysql = Config::get('database.connections.mysql');

		$colname = 'Tables_in_' . $mysql['database'];

		$tables = DB::select('SHOW TABLES');

		foreach($tables as $table) {
			$droplist[] = substr($table->$colname, strlen($mysql['prefix']));
		}

		$noDroplist = array();

		try
		{
			DB::beginTransaction();
			//turn off referential integrity
			DB::statement('SET FOREIGN_KEY_CHECKS = 0');


			// truncate all the tables
			foreach ($droplist as $table) {
				if (!in_array($table, $noDroplist)) {
					DB::table($table)->truncate();
				}
			}

			// restore original content
			DB::unprepared(File::get(base_path() . '/database_basic.sql'));

			//turn referential integrity back on
			DB::statement('SET FOREIGN_KEY_CHECKS = 1');

			DB::commit();

			// restore the configuration files

			// article config
			$article_helper = new ArticlesHelper;
			$article_helper->writeConfig(array(
				'max_articles'	=> '4',
				'max_words' 		=> '50'
			));
			$article_helper->writeConfig(array(
				'max_articles'	=> '4',
				'max_words' 		=> '50'
			), app_path().'/modules/articles/configcategory.json');

			// contact-us config
			(new ContactUsHelper)->writeConfig(array(
				'recipient_email' => 'info@etonins.com'
			));

			// fee (tax) config
			FeeManagerHelperController::setConfig(array(
				"tax_percent" => "1.13",
		    "on_monthly" => "yes",
		    "on_examination" => "yes",
		    "on_transportation" => "yes",
		    "on_hostel" => "yes"
		  ));

			// Grade Config
		  File::put(GRADE_CONFIG_FILE, json_encode(
		  	[
			    [
			        "from" => "0",
			        "to" => "20",
			        "grade" => "E",
			        "grade_point" => "0.8"
			    ],
			    [
			        "from" => "20",
			        "to" => "30",
			        "grade" => "D",
			        "grade_point" => "1.2"
			    ],
			    [
			        "from" => "30",
			        "to" => "40",
			        "grade" => "D+",
			        "grade_point" => "1.6"
			    ],
			    [
			        "from" => "40",
			        "to" => "50",
			        "grade" => "C",
			        "grade_point" => "2.0"
			    ],
			    [
			        "from" => "50",
			        "to" => "60",
			        "grade" => "C+",
			        "grade_point" => "2.4"
			    ],
			    [
			        "from" => "60",
			        "to" => "70",
			        "grade" => "B",
			        "grade_point" => "2.8"
			    ],
			    [
			        "from" => "70",
			        "to" => "80",
			        "grade" => "B+",
			        "grade_point" => "3.2"
			    ],
			    [
			        "from" => "80",
			        "to" => "90",
			        "grade" => "A",
			        "grade_point" => "3.6"
			    ],
			    [
			        "from" => "90",
			        "to" => "100",
			        "grade" => "A+",
			        "grade_point" => "4.0"
			    ]
			  ],
			  JSON_PRETTY_PRINT
			 ));

		  // video-gallery config
		  VideoGalleryHelperController::setConfig([
		  	"vimeo_user_id" => "9806016",
    		"youtube_playlist_id" => "PLmo4pBukfRoN8SB5RKvfiY9CTl9pI_IFc"
		  ]);

		  // report config
		  File::put(REPORT_CONFIG_FILE, json_encode(
		  	array(
					'show_percentage' => 'yes',
					'show_grade'	=> 'yes',
					'show_grade_point'	=> 'yes'
				), 
				JSON_PRETTY_PRINT
			));

		  // assignment config
		  File::put(ASSIGNMENT_CONFIG, json_encode(
		  	array(
					'max_file_size' => 20000,
					'max_frontend_show' => 5
				),
				JSON_PRETTY_PRINT
			));

			// delete attendance records
			File::deleteDirectory(app_path() . '/modules/attendance/assets/attendance-records', true);

			// restore old attendance records
			File::copyDirectory(
				base_path() . '/attendance_records', 
				app_path() . '/modules/attendance/assets/attendance-records'
			);
			
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}

		return true;
	}

	public static function generateNepaliMonthsSelectList($start_month = 1, $selected = 0, $default = '')
	{
		$months = ['1' => 'Baishakh',
	                     '2' => 'Jestha',
	                     '3' => 'Asar',
	                     '4' => 'Shrawan',
	                     '5'	=>	'Bhadra',
	                     '6'	=>	'Ashwin',
	                     '7'	=> 'Kartik',
	                     '8'	=>	'Mangsir',
	                     '9'	=>	'Poush',
	                     '10'	=>	'Magh',
	                     '11'	=>	'Falgun',
	                     '12'	=>	'Chaitra'];

	    $list = [];
	    for($i=$start_month, $counter = 0; $counter <12; $counter++)
	    {
	    	$list[$start_month] = $months[$start_month];
	    	$start_month++;
	    	$start_month  = $start_month == 13 ? 1 : $start_month; 
	    }

	    $list = HelperController::generateStaticSelectList($list, 'months', $selected, $default);
	    return $list;
	}

	public static function loginWithToken()
	{
		if(Auth::user()->guest() && Auth::superadmin()->guest() && Auth::admin()->guest())
		{
			$token = Input::get('apiToken', base64_encode('0:0'));
			$token = base64_decode($token);

			$token = explode(':', $token);
			if(isset($token[0])  && isset($token[1]))
			{
				if($token[1] == 'student')
				{
					$record = Users::where('user_details_id', $token[0])
									->where('role', 'student')
									->first();

					if($record)
					{
						Auth::user()->login($record);
					}
					//check if id exists

				}
				elseif($token[1] == 'guardian')
				{
					$record = Users::where('user_details_id', $token[0])
									->where('role', 'guardian')
									->first();

					if($record)
					{
						Auth::user()->login($record);
					}

				}
				elseif($token[1] == 'admin')
				{
					$record = Admin::where('admin_details_id', $token[0])
									//->where('role', 'student')
									->first();

					if($record)
					{
						Auth::admin()->login($record);
					}
				}
				elseif($token[1] == 'superadmin')
				{
					$record = SuperAdmin::where('id', $token[0])
									//->where('role', 'student')
									->first();

					if($record)
					{
						Auth::superadmin()->login($record);
					}
				}
			}	
		}
	}
}

?>