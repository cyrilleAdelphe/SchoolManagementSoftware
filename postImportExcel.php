public function postImportExcel()
	{
		ini_set('max_execution_time', 300); 
		AccessController::allowedOrNot('student', 'can_import');//300 seconds = 5 minutes
		
		$excel_header_map = ['first_name' => 'First Name', 'last_name' =>	'Last Name', 	'roll_no' => 'Roll No',	'house' => 'House',	'ethnicity' => 'Ethnicity',	'current_address' => 'Current Address',	'permanent_address' => 'Permanent Address', 	'sex_malefemale' => 'Sex (Male/Female)', 	'dob_yy_mm_dd' => 'DOB (YY-MM-DD)', 'primary_contact'=>	'Primary Contact', 	'secondary_contact'=>'Secondary Contact', 'email' => 'Email', 'father_name' => 'Father Name', 	'mother_name' =>'Mother Name',	'multiple_children_number' => 'multiple_children_number',	'class' => 'Class Name', 'section_name' =>	'Section Name'];
		
		$reader = Excel::load(Input::file('excel_file'))
										->get();


		/* Do some validation here */
		
		$current_session_id = (int) HelperController::getCurrentSession();

		if($current_session_id == 0)
		{
			die('No session set as current');
		}

		try
		{
			$has_multiple_children = []; //['multiple_number' => 'student_id']
			DB::connection()->getPdo()->beginTransaction();

			foreach($reader as $row)
			{
				foreach($row as $index => $r)
				{
					$row[$index] = trim($r);
					$row[$index] = $row[$index] == '-' ? '' : $row[$index];
				}

				// getting house id
				$house_id = strlen($row['house']) ? House::firstOrCreate(['house_name' => $row['house'], 'house_code' => $row['house'], 'is_active' => 'yes'])->id : NULL;

				// getting ethnicity id
				$ethnicity_id = strlen($row['ethnicity']) ? Ethnicity::firstOrCreate(['ethnicity_name' => $row['ethnicity'], 'ethnicity_code' => $row['ethnicity'], 'is_active' => 'yes'])->id : NULL;

				if(strlen($row['class_name']) == 0 || strlen($row['section_name']) == 0 || strlen($row['first_name']) == 0)
				{
					continue;
				}

				//getting class id
				$class_id = Classes::firstOrNew(['academic_session_id'=> $current_session_id, 'class_name' => $row['class_name'], 'class_code' => $row['class_name'], 'is_active' => 'yes']);

				if((int) $class_id->sort_order == 0)
				{
					$highest_class_sort_order = (int) DB::table(Classes::getTableName())
												->where('academic_session_id', $current_session_id)
												->max('sort_order') + 1;

					$class_id->sort_order =  $highest_class_sort_order;
					$class_id->save();
				}

				$class_id = $class_id->id;

				//gettting section id
				$section_id = Section::firstOrCreate(['section_name' => $row['section_name'], 'section_code' => $row['section_name'], 'is_active' => 'yes']);
				$section_code = $section_id->section_code;
				$section_id = $section_id->id;

				//map class and section
				ClassSection::firstOrCreate(['class_id' => $class_id, 'section_code' => $section_code]);

				$data_to_store_in_student_registration_table = [];
				
				$data_to_store_in_student_registration_table['student_name'] = $row['first_name'];
				
				$data_to_store_in_student_registration_table['last_name'] = $row['last_name'];
				
				$data_to_store_in_student_registration_table['dob_in_bs'] = $row['dob_yy_mm_dd'];

				$data_to_store_in_student_registration_table['dob_in_ad'] = strlen($row['dob_yy_mm_dd']) ? (new DateConverter)->bs2ad($row['dob_yy_mm_dd']) : NULL;

				$data_to_store_in_student_registration_table['current_address'] = $row['current_address'];

				$data_to_store_in_student_registration_table['permanent_address'] = $row['permanent_address'];

				$data_to_store_in_student_registration_table['sex'] = strtolower($row['sex_malefemale']);

				$data_to_store_in_student_registration_table['email'] = $row['email'];

				$data_to_store_in_student_registration_table['guardian_contact'] = $row['primary_contact'];

				$data_to_store_in_student_registration_table['secondary_contact'] = $row['secondary_contact'];

				$data_to_store_in_student_registration_table['registered_session_id'] = $current_session_id;

				$data_to_store_in_student_registration_table['registered_class_id'] = $class_id;

				$data_to_store_in_student_registration_table['registered_section_code'] = $section_code;

				$data_to_store_in_student_registration_table['photo'] = '';

				$data_to_store_in_student_registration_table['house_id'] = $house_id;

				$data_to_store_in_student_registration_table['ethnicity_id'] = $ethnicity_id;

				$data_to_store_in_student_registration_table['unique_school_roll_number'] = '';

				$data_to_store_in_student_registration_table['is_active'] = 'yes';

				$student_id = StudentRegistration::create($data_to_store_in_student_registration_table)->id;

				//unset($data_to_store_in_student_registration_table) ;

				$data_to_store_in_users_table = [];

				do {
					$data_to_store_in_users_table['username'] = STUDENT_PREFIX_IN_USERNAME . str_pad(rand(0, pow(10, DIGITS_IN_USERNAME)-1), DIGITS_IN_USERNAME, '0', STR_PAD_LEFT);
				} while(Users::where('username', $data_to_store_in_users_table['username'])->first());

				$data_to_store_in_users_table['email'] = $row['email'];

				$data_to_store_in_users_table['password'] = Hash::make(DEFAULT_PASSWORD);

				$data_to_store_in_users_table['name'] = $row['first_name'].' '.$row['last_name'];

				$data_to_store_in_users_table['contact'] = $row['primary_contact'];

				$data_to_store_in_users_table['role'] = 'student';

				$data_to_store_in_users_table['user_details_id'] = $student_id;

				$data_to_store_in_users_table['confirmation'] = '';

				$data_to_store_in_users_table['confirmation_count'] = 0;

				$data_to_store_in_users_table['is_blocked'] = 'no';

				$data_to_store_in_users_table['is_active'] = 'yes';

				Users::create($data_to_store_in_users_table);

				unset($data_to_store_in_users_table);

				$data_to_store_in_student_table = [];
				$data_to_store_in_student_table['current_session_id'] = $current_session_id;
				$data_to_store_in_student_table['current_class_id'] = $class_id;
				$data_to_store_in_student_table['current_section_code'] = $section_code;
				$data_to_store_in_student_table['current_roll_number'] = $row['roll_no'];
				$data_to_store_in_student_table['is_active'] = 'yes';
				$data_to_store_in_student_table['student_id'] = $student_id;
				Student::firstOrCreate($data_to_store_in_student_table);
				unset($data_to_store_in_student_table);


				//if multiple children parent already created so no need to create
				if((int) $row['multiple_children_number'])
				{
					if(in_array($row['multiple_children_number'], array_keys($has_multiple_children)))
					{
						/*$has_multiple_children[$row['multiple_children_number']][] = $student_id;*/
						if($has_multiple_children[$row['multiple_children_number']]['father'])
						{
							StudentGuardianRelation::firstOrCreate(['guardian_id' => $has_multiple_children[$row['multiple_children_number']]['father'], 'student_id' => $student_id, 'relationship' => 'father']);	
						}

						if($has_multiple_children[$row['multiple_children_number']]['mother'])
						{
							StudentGuardianRelation::firstOrCreate(['guardian_id' => $has_multiple_children[$row['multiple_children_number']]['mother'], 'student_id' => $student_id, 'relationship' => 'mother']);	
						}
						
					}
					else
					{
						$return = $this->createGuardianFromImport($row, $student_id);
						$has_multiple_children[$return['index']] = $return['value'];
					}	
				}
				else
				{
					$this->createGuardianFromImport($row, $student_id);
				}
			}
			Session::flash('success-msg', 'Students successfully created');
			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
			
		}
		
		return Redirect::back();
	}
