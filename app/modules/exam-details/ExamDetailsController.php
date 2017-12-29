<?php

class ExamDetailsController extends BaseController
{
	protected $view = 'exam-details.views.';

	protected $model_name = 'ExamDetails';

	protected $module_name = 'exam-details';

	public $role;

	public $current_user;

	
	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'exam_name',
										'alias'			=> 'Event'
									),

									array
									(
										'column_name' 	=> 'subject_id',
										'alias'			=> 'Session'
									),

									array
									(
										'column_name' 	=> 'full_marks',
										'alias'			=> 'Full Marks'
									),

									array
									(
										'column_name' 	=> 'pass_marks',
										'alias'			=> 'Pass Marks'
									),

									array
									(
										'column_name' 	=> 'remarks',
										'alias'			=> 'Remarks'
									)
								 );

	public function postCreateEditView()
	{
		AccessController::allowedOrNot('exam-details', 'can_create,can_edit');
		$input = Input::all();
		$modelName = $this->model_name;
		//write validation here
		
		/*echo '<pre>';
		print_r($input);
		die();*/

		if($input['exam_id'] == 0)
		{
			Session::flash('error-msg', ConfigurationController::translate('Please select Exam First'));
					return Redirect::back()
								->withInput();
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$class_subjects = Subject::where('class_id', $input['class_id'])
				->where('section_id', $input['section_id'])
				->get();

			foreach($class_subjects as $subject) 
			{
				ExamDetails::where('exam_id', $input['exam_id'])
					->where('subject_id', $subject->id)
					->delete();	
			}
			

			$date_converter = new DateConverter;
			foreach($input['subject_id'] as $index => $subject_id)
			{
				$date_time = DateTime::createFromFormat('m/d/Y g:i A', $input['start_date_in_ad'][$index]);

				if ($date_time) 
				{
					$input['start_date_in_ad'][$index] = $date_time->format('Y-m-d h:i:s');	
					$start_date_in_bs = $date_converter->ad2bs(substr($input['start_date_in_ad'][$index], 0, 10));
				} 
				else 
				{
					$input['start_date_in_ad'][$index] = '';	
					$start_date_in_bs = '';
				}

				$dataToStore = array(
					'exam_id' => $input['exam_id'], 
					'subject_id' => $subject_id, 
					'pass_marks' => $input['pass_marks'][$index], 
					'full_marks' => $input['full_marks'][$index], 
					'practical_pass_marks' => $input['practical_pass_marks'][$index], 
					'practical_full_marks' => $input['practical_full_marks'][$index], 
					'remarks' => $input['remarks'][$index], 
					'is_active' => 'yes', 
					'duration' => $input['duration'][$index], 
					'start_date_in_ad' => $input['start_date_in_ad'][$index], 
					'start_date_in_bs' => $start_date_in_bs
				);

				/*echo '<pre>';
				print_r($dataToStore);
				die();*/
				
				$result = $this->validateInput($dataToStore);
		
				if($result['status'] == 'error')
				{
					Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured<br>'));
					return Redirect::back()
								->withInput()
								->with('errors', $result['data']);
				}
				$result = $this->storeInDatabase($dataToStore);	
			}

			DB::connection()->getPdo()->commit();

			Session::flash('success-msg', 'Successfully created');
			return Redirect::back();
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back()->withInput();
		}
		
	}

	public function deleteAllRecordsOfAnExam($exam_id)
	{
		AccessController::allowedOrNot('exam-details', 'can_delete');
		ExamDetails::where('exam_id', $exam_id)->delete();
		Session::flash('success-msg', 'Successfully deleted');
		return Redirect::route('exam-details-list');
	}

	public function ajaxGetSectionIds()
	{
		$class_id = Input::get('class_id');
		$condition = array();
		$condition['field'] = 'class_id';
		$condition['operator'] = '=';
		$condition['value'] = $class_id;

		//$select_list_value = Input::get('get_section_id', "false") == "false" ? 'section_code' : 'id';
		$section_ids_codes = DB::table(ClassSection::getTableName()) 
								->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
								->select(Section::getTableName().'.id', Section::getTableName().'.section_code')
								->where(ClassSection::getTableName().'.is_active', 'yes')
								->where(Section::getTableName().'.is_active', 'yes')
								->where('class_id', $class_id)
								->lists('section_code', 'id');

		//dd($section_ids_codes);

		return HelperController::generateStaticSelectList($section_ids_codes, 'section_id', $selected = 0);
	}

	public function viewRoutine()
	{	
		AccessController::allowedOrNot('exam-details', 'can_view');
		$input = Input::all();
		$class_id = isset($input['class_id']) ? $input['class_id'] : 0;
		$section_id = isset($input['section_id']) ? $input['section_id'] : 0;
		$exam_id = isset($input['exam_id']) ? $input['exam_id'] : 0;
		/*
		->leftJoin('bookings', function($join)
                         {
                             $join->on('rooms.id', '=', 'bookings.room_type_id');
                             $join->on('arrival','>=',DB::raw("'2012-05-01'"));
                             $join->on('arrival','<=',DB::raw("'2012-05-10'"));
                             $join->on('departure','>=',DB::raw("'2012-05-01'"));
                             $join->on('departure','<=',DB::raw("'2012-05-10'"));
                         })
		*/
		$required_subjects = Subject::where('class_id', $class_id)
									 ->where('section_id', $section_id)
									 ->where('is_active', 'yes')
									 ->lists('subject_name', 'id');


		$data = ExamDetails::whereIn('subject_id', array_keys($required_subjects))
							->where('exam_id', $exam_id)
							->orderBy('start_date_in_ad', 'ASC')
							->get();

		$last_updated_data = ExamDetails::getLastUpdated($condition = array(array('field_name' => 'is_active', 'operator' => '=', 'compare_value' => 'yes')), $fields_required = array('updated_by', 'id'), 'ExamDetails');

		return View::make($this->view.'exam-routine')
					->with('role', $this->role)
					->with('data', $data)
					->with('required_subjects', $required_subjects)
					->with('last_updated_data', $last_updated_data);
					
	}

	
}