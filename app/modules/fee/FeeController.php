<?php

class FeeController extends BaseController
{
	protected $view = 'fee.views.';

	protected $model_name = 'MonthlyStudentFee';

	protected $module_name = 'fee';

	protected $role;

	public function getGenerate()
	{
		return View::make($this->view.'generate');
	}

	public function postGenerate()
	{
		AccessController::allowedOrNot('fee', 'can_create');
		if(!Input::get('class_id'))
		{
			Session::flash('error-msg', 'Form incomplete');
			$error = new Illuminate\Support\MessageBag;
			$error->add('class_id', 'required');
			return Redirect::back()
						->with('errors', $error);
		}


		$data = Input::all();
		
		$sections = array();
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			if ($data['section_id'] == 0)
			{

				// writing the fee for all sections
				$class_section_table = ClassSection::getTableName();
				$section_table = Section::getTableName();

				$sections = DB::table($section_table)
								->join($class_section_table, $class_section_table.'.section_code', '=', $section_table.'.section_code')
								->where($section_table.'.is_active', 'yes')
								->where($class_section_table.'.is_active', 'yes')
								->where($class_section_table.'.class_id', Input::get('class_id'))
								->select($section_table.'.id')
								->lists('id');
			}
			else
			{
				$sections = array($data['section_id']);
			}

			$current_session_id = HelperController::getCurrentSession();

			$monthly_fee  = MonthlyFee::where('class_id', $data['class_id'])
											->first();

			$examination_fee = ExaminationFee::where('class_id', $data['class_id'])
														->where('month', $data['month'])
														->first();

			foreach($sections as $section)
			{
				$students = DB::table(Student::getTableName())
							->join(Section::getTableName(), Section::getTableName().'.section_code', '=', Student::getTableName().'.current_section_code')
							->where('current_session_id', $current_session_id)
							->where('current_class_id', $data['class_id'])
							->where(Section::getTableName().'.id', $section)
							->where(Student::getTableName().'.is_active', 'yes')
							->get();
				
				$misc_class_fees = MiscClassFee::where('class_id', $data['class_id'])
					->where('section_id', $section)
					->where(function($query) use ($data) {
						return $query->where('month', $data['month'])
							->orWhere('month', 0);
					})->get();

				foreach($students as $student)
				{
					$total_fee = 0;

					/*
					 * Update monthly fee
					 */
					// delete old records 
					MonthlyStudentFee::where('student_id', $student->student_id)
									->where('academic_session_id', $current_session_id)
									->where('month', $data['month'])
									->delete();
					
					ScholarshipMonthly::where('student_id', $student->student_id)
										->where('academic_session_id', $current_session_id)
										->where('month', $data['month'])
										->delete();
					
						
					// write new records
					$monthly_data = 
								array(
									'student_id'	=> $student->student_id,
									'academic_session_id' => $current_session_id,
									'amount'		=> 0,
									'month'			=> $data['month'],
									'fee_monthly_id'=> 0,
									'is_active'		=> 'yes'
								);

					
					if($monthly_fee)
					{
						$monthly_data['amount'] = $monthly_fee->amount;
						$monthly_data['fee_monthly_id'] = $monthly_fee->id;

						// monthly scholarship
						$monthly_scholarship = 
									Scholarship::where('student_id', $student->student_id)
																->where('academic_session_id', $current_session_id)
																->where('type', 'monthly')
																->first();

						$monthly_scholarship_amount = $monthly_scholarship ? 
																		round( (float)$monthly_scholarship->percent * $monthly_fee->amount / 100)
																		: 0;

						if($monthly_scholarship)
						{
							$monthly_scholarship_data = 
								array(
									'student_id'	=> $student->student_id,
									'academic_session_id' => $current_session_id,
									'amount'		=> $monthly_scholarship_amount,
									'month'			=> $data['month'],
									'scholarship_id'=> $monthly_scholarship->id,
									'is_active'		=> 'yes'
							);
							
							$this->storeInDatabase($monthly_scholarship_data, 'ScholarshipMonthly');
						}

						$total_fee += $monthly_fee->amount - $monthly_scholarship_amount;
					}
					$this->storeInDatabase($monthly_data, 'MonthlyStudentFee');
					
					
					/*
					 * Update misc class fee for each student
					 */
					MiscClassStudentFee::where('student_id', $student->student_id)
									->where('academic_session_id', $current_session_id)
									->where('month', $data['month'])
									->delete();

					foreach($misc_class_fees as $misc_class_fee)
					{
						$misc_class_data = 
								array(
									'student_id'	=> $student->student_id,
									'academic_session_id' => $current_session_id,
									'amount'		=> $misc_class_fee->amount,
									'month'			=> $data['month'],
									'fee_misc_class_id'=> $misc_class_fee->id,
									'is_active'		=> 'yes'
								);
						$this->storeInDatabase($misc_class_data, 'MiscClassStudentFee');

						$total_fee += $misc_class_fee->amount;

					}

					/*
					 * Update examination fee
					 */
					if($examination_fee)
					{
						ExaminationStudentFee::where('student_id', $student->student_id)
										->where('academic_session_id', $current_session_id)
										->where('month', $data['month'])
										->delete();
						// write new records
						$examination_data = 
									array(
										'student_id'	=> $student->student_id,
										'academic_session_id' => $current_session_id,
										'amount'		=> $examination_fee->amount,
										'month'			=> $data['month'],
										'fee_examination_id'=> $examination_fee->id,
										'is_active'		=> 'yes'
									);

						$total_fee += $examination_fee->amount;
						$this->storeInDatabase($examination_data, 'ExaminationStudentFee');
					}

					/*
					 * Update transportation fee
					 */
					// delete old records
					TransportationStudentFee::where('student_id', $student->student_id)
									->where('academic_session_id', $current_session_id)
									->where('month', $data['month'])
									->delete();

					$transportation_fee = 
							TransportationStudent::where('student_id', $student->student_id)
																			->first();
						
					$transportation_fee_data = 
							array(
								'student_id'	=> $student->student_id,
								'academic_session_id' => $current_session_id,
								'amount'		=> 0,
								'month'			=> $data['month'],
								'transportation_student_id'=> 0,
								'is_active'		=> 'yes'
							);

						
					if($transportation_fee)
					{
						$transportation_fee_data['amount'] = $transportation_fee->fee_amount;
						$transportation_fee_data['transportation_student_id'] = $transportation_fee->id;

						// transportation scholarship
						$transportation_scholarship = 
									Scholarship::where('student_id', $student->student_id)
																->where('academic_session_id', $current_session_id)
																->where('type', 'transportation')
																->first();

						$transportation_scholarship_amount = $transportation_scholarship ? 
																		round((float)$transportation_scholarship->percent * $transportation_fee->fee_amount / 100)
																		: 0;

						if($transportation_scholarship)
						{
							$transportation_scholarship_data = 
								array(
									'student_id'	=> $student->student_id,
									'academic_session_id' => $current_session_id,
									'amount'		=> $transportation_scholarship_amount,
									'month'			=> $data['month'],
									'scholarship_id'=> $transportation_scholarship->id,
									'is_active'		=> 'yes'
								);
							$this->storeInDatabase($transportation_scholarship_data, 'ScholarshipMonthly');
						}
						$total_fee += $transportation_fee->fee_amount - $transportation_scholarship_amount;
					}

					$this->storeInDatabase($transportation_fee_data, 'TransportationStudentFee');
					

					/*
					 * Update hostel fee
					 */
					HostelStudentFee::where('student_id', $student->student_id)
									->where('academic_session_id', $current_session_id)
									->where('month', $data['month'])
									->delete();

					$hostel_fee = 
							DormitoryStudent::where('student_id', $student->student_id)
																			->first();
										
					$hostel_fee_data = 
							array(
								'student_id'	=> $student->student_id,
								'academic_session_id' => $current_session_id,
								'amount'		=> 0,
								'month'			=> $data['month'],
								'dormitory_student_id'=> 0,
								'is_active'		=> 'yes'
							);
					
					if($hostel_fee)
					{
						$hostel_fee_data['amount'] = $hostel_fee->fee_amount;
						$hostel_fee_data['dormitory_student_id'] = $hostel_fee->id;

						// hostel scholarship
						$hostel_scholarship = 
									Scholarship::where('student_id', $student->student_id)
																->where('academic_session_id', $current_session_id)
																->where('type', 'hostel')
																->first();

						$hostel_scholarship_amount = $hostel_scholarship ? 
																		round((float)$hostel_scholarship->percent * $hostel_fee->fee_amount / 100.)
																		: 0;

						if($hostel_scholarship)
						{
							$hostel_scholarship_data = 
								array(
									'student_id'	=> $student->student_id,
									'academic_session_id' => $current_session_id,
									'amount'		=> $hostel_scholarship_amount,
									'month'			=> $data['month'],
									'scholarship_id'=> $hostel_scholarship->id,
									'is_active'		=> 'yes'
								);
							$this->storeInDatabase($hostel_scholarship_data, 'ScholarshipMonthly');
						}
												
						$total_fee += $hostel_fee->fee_amount - $hostel_scholarship_amount;
					}
					$this->storeInDatabase($hostel_fee_data, 'HostelStudentFee');
					

					/*
					 * Misc fee (for individual student)
					 * If this information is already in the the fee_misc_student table
					 * No need to update it
					 * Only update the aggregate table
					 */
					$misc_fees = 
							MiscStudentFee::where('student_id', $student->student_id)
							->where('academic_session_id', $current_session_id)
							->where(function($query) use ($data) {
								return $query->where('month', $data['month'])
									->orWhere('month', 0);
							})->get();

					foreach($misc_fees as $misc_fee)
					{
						$total_fee += $misc_fee->amount;
					}

					// taxes
					$taxes = ['monthly', 'transportation', 'examination', 'hostel'];
					// delete old entries
					foreach ($taxes as $tax) {
						FeeTax::where('type', $tax)
							->where('student_id', $student->student_id)
							->where('academic_session_id', $current_session_id)
							->where('month', $data['month'])
							->delete();
					}
					$config = FeeManagerHelperController::getConfig();
					$tax_percentage = $config['tax_percent'];
					if ($config['on_monthly'] == 'yes' && $monthly_fee) {
						// monthly
						$monthly_tax_data = array(
							'student_id'	=> $student->student_id,
							'academic_session_id' => $current_session_id,
							'amount'	=> (float)($monthly_data['amount'] - $monthly_scholarship_amount) * $tax_percentage / 100.,
							'type'		=> 'monthly',
							'month'			=> $data['month'],
							'is_active'	=> 'yes'
						);
						$this->storeInDatabase($monthly_tax_data, 'FeeTax');
						$total_fee += $monthly_tax_data['amount'];
					} 
					if ($config['on_examination'] == 'yes' && $examination_fee) {
						// exam 
						$examination_tax_data = array(
							'student_id'	=> $student->student_id,
							'academic_session_id' => $current_session_id,
							'amount'	=> (float)$examination_data['amount'] * $tax_percentage / 100.,
							'type'		=> 'examination',
							'month'			=> $data['month'],
							'is_active'	=> 'yes'
						);
						$this->storeInDatabase($examination_tax_data, 'FeeTax');
						$total_fee += $examination_tax_data['amount'];
					} 
					if ($config['on_transportation'] == 'yes' && $transportation_fee) {
						// transportation
						$transportation_tax_data = array(
							'student_id'	=> $student->student_id,
							'academic_session_id' => $current_session_id,
							'amount'	=> (float)($transportation_fee->fee_amount - $transportation_scholarship_amount) * $tax_percentage / 100.,
							'type'		=> 'transportation',
							'month'			=> $data['month'],
							'is_active'	=> 'yes'
						);
						$this->storeInDatabase($transportation_tax_data, 'FeeTax');
						$total_fee += $transportation_tax_data['amount'];
					} 
					if ($config['on_hostel'] == 'yes' && $hostel_fee) {
						// hostel
						$hostel_tax_data = array(
							'student_id'	=> $student->student_id,
							'academic_session_id' => $current_session_id,
							'amount'	=> (float)($hostel_fee->fee_amount - $hostel_scholarship_amount) * $tax_percentage / 100.,
							'type'		=> 'hostel',
							'month'			=> $data['month'],
							'is_active'	=> 'yes'
						);
						$this->storeInDatabase($hostel_tax_data, 'FeeTax');
						$total_fee += $hostel_tax_data['amount'];
					}
					/*
					 * Aggregate fee information
					 */
					// update payment
					$old_payment = FeePayment::where('student_id', $student->student_id)
										->where('academic_session_id', $current_session_id)
										->where('month', $data['month'])
										->first();
					$new_payment = array(
										'student_id'	=> $student->student_id,
										'academic_session_id' => $current_session_id,
										'fee_amount'	=> $total_fee,
										'received_amount'=> 0,
										'is_paid'		=> 'no',
										'month'			=> $data['month'],
										'is_active'		=> 'yes',
									);
					if($old_payment)
					{
						$new_payment['id'] = $old_payment->id;
						$new_payment['received_amount'] = $old_payment->received_amount;
						if($new_payment['received_amount'] >= $total_fee)
						{
							$new_payment['is_paid'] = 'yes';
						}
						$this->updateInDatabase($new_payment, [], 'FeePayment');

					}
					else
					{
						$this->storeInDatabase($new_payment, 'FeePayment');
					}
					
				}
			}

			// send push notifications
			$section_codes = Section::whereIn('id', $sections)
				->lists('section_code');

			$student_ids = Student::where('current_class_id', Input::get('class_id'))
				->whereIn('current_section_code', $section_codes)
				->where('current_session_id', $current_session_id)
				->lists('student_id'); // the id field is to be replaced by gcm_id

			$student_gcm_ids = PushNotifications::where('user_group', 'student')
												->whereIn('user_id', $student_ids)
												->lists('gcm_id');


			$guardian_ids = DB::table(StudentGuardianRelation::getTableName())
							->whereIn('student_id', $student_ids)
							->lists('guardian_id');
			
			
			$guardian_gcm_ids = PushNotifications::where('user_group', 'guardian')
												->whereIn('user_id', $guardian_ids)
												->lists('gcm_id');
			
			$msg = 'fee' . ' # '.
				'Fee generated for month ' . HelperController::getMonthName($data['month']) .
				' on ' . HelperController::getCurrentDate();
						
			if(count($student_ids)) 
			{
				(new GcmController)->send($student_gcm_ids, $msg, $student_ids, 'student');
			}

			if(count($guardian_ids)) 
			{
				(new GcmController)->send($guardian_gcm_ids, $msg, $guardian_ids, 'guardian');
			}

			$success = true;
			$msg = 'Fee successfully generated';

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}

		
		if($success)
		{
			Session::flash('success-msg', $msg);
			return Redirect::to(
								URL::route('fee-class-get')
								.'?academic_session_id='.$current_session_id
								.'&class_id='.$data['class_id']
								.'&section_id='.$data['section_id']
								.'&month='.$data['month']
							);
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::back()
							->withInput();
		}
	}

	public function massPrintFee($class_id, $section_id, /*nepali month*/$month)
	{
		
		AccessController::allowedOrNot('fee', 'can_view');

		$class_name = HelperController::pluckFieldFromId('Classes', 'class_name', $class_id);
		$section_code = HelperController::pluckFieldFromId('Section', 'section_code', $section_id);

		$current_session_id = HelperController::getCurrentSession();

		$student_ids = [];
		$student_ids = DB::table(Student::getTableName())
					->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'current_class_id')
					//->where(Student::getTableName().'.student_id', Input::get('student_id'))
					->where('current_class_id', $class_id)
					->where('current_section_code', $section_code)
					->where(Student::getTableName().'.current_session_id', $current_session_id)
					->select(StudentRegistration::getTableName().'.id')
					->lists('id');

		if(count($student_ids) == 0)
		{
			echo 'no student found';
			die();
		}
		else
		{
			$payment = DB::table(FeePayment::getTableName())
							->whereIn('student_id', $student_ids)
							->where('academic_session_id', $current_session_id)
							->where('month', $month)
							->get();

			if(count($payment) == 0)
			{
				return View::make($this->view.'update-payment-form')
						->with('status', 'error')
						->with('msg', 'Fee not generated for the given month');
			}
			else
			{
				$monthly_fee = DB::table(MonthlyStudentFee::getTableName())
									->whereIn('student_id', $student_ids)
									->where('month', $month)
									->where('academic_session_id', $current_session_id)
									->get();

				$misc_class_fees = 
						DB::table(MiscClassStudentFee::getTableName())
								->join(MiscClassFee::getTableName(), MiscClassFee::getTableName().'.id', '=', MiscClassStudentFee::getTableName().'.fee_misc_class_id')
								->whereIn(MiscClassStudentFee::getTableName().'.student_id', $student_ids)
								->where(MiscClassStudentFee::getTableName().'.month', $month)
								->where(MiscClassStudentFee::getTableName().'.academic_session_id', $current_session_id)
								->get();


				$misc_student_fees = 
					DB::table(MiscStudentFee::getTableName())
						->whereIn('student_id', $student_ids)
						->where('month', $month)
						->where('academic_session_id', $current_session_id)
						->get();

				$examination_fee = 
					DB::table(ExaminationStudentFee::getTableName())
								->join(ExaminationFee::getTableName(), ExaminationFee::getTableName().'.id', '=', ExaminationStudentFee::getTableName().'.fee_examination_id')
								->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', ExaminationFee::getTableName().'.exam_id')
								->whereIn(ExaminationStudentFee::getTableName().'.student_id', $student_ids)
								->where(ExaminationStudentFee::getTableName().'.month', $month)
								->where(ExaminationStudentFee::getTableName().'.academic_session_id', $current_session_id)
								->select(ExaminationStudentFee::getTableName().'.*', ExamConfiguration::getTableName().'.exam_name')
								->get();

				$transportation_fee = 
					DB::table(TransportationStudentFee::getTableName())
						->whereIn('student_id', $student_ids)
						->where('month', $month)
						->where('academic_session_id', $current_session_id)
						->get();

				$hostel_fee = 
					DB::table(HostelStudentFee::getTableName())
						->whereIn('student_id', $student_ids)
						->where('month', $month)
						->where('academic_session_id', $current_session_id)
						->get();

				$scholarships = 
					DB::table(Scholarship::getTableName())
							->join(ScholarshipMonthly::getTableName(), ScholarshipMonthly::getTableName().'.scholarship_id', '=', Scholarship::getTableName().'.id')
							->whereIn(ScholarshipMonthly::getTableName().'.student_id', $student_ids)
							->where(ScholarshipMonthly::getTableName().'.academic_session_id', $current_session_id)
							->where('month', $month)
							//->select(ScholarshipMonthly::getTableName().'.id', 'type')
							->get();

				$transportation_scholarship = 
									DB::table(Scholarship::getTableName())
									->whereIn('student_id', $student_ids)
									->where('academic_session_id', $current_session_id)
									->where('type', 'transportation')
									->get();


				$hostel_scholarship = 
				DB::table(Scholarship::getTableName())
					->whereIn('student_id', $student_ids)
					->where('academic_session_id', $current_session_id)
					->where('type', 'hostel')
					->get();

						
				$taxes = 
					DB::table(FeeTax::getTableName())
						->whereIn('student_id', $student_ids)
						->where('month', $month)
						->where('academic_session_id', $current_session_id)
						->get();

				$previous_dues = DB::table(FeePayment::getTableName())
								->whereIn('student_id', $student_ids)
								->where('academic_session_id', $current_session_id)
								->where('is_paid', 'no')
								->where('month', '!=', $month)
								->orderBy('month')
								->get();

				$student = array();
				foreach($monthly_fee as $monthly)
				{
					$student[$monthly->student_id]['monthly_fee'] = $monthly->amount;
				}

				foreach($examination_fee as $exam)
				{
					$student[$exam->student_id]['examination']['exam_name'] = $exam->exam_name;
					$student[$exam->student_id]['examination']['amount'] = $exam->amount;
				}

				foreach($misc_class_fees as $misc)
				{
					$student[$misc->student_id]['misc_class_fees']['title'][] = $misc->title;
					$student[$misc->student_id]['misc_class_fees']['amount'][] = $misc->amount;
				}

				foreach($misc_student_fees as $misc)
				{
					$student[$misc->student_id]['misc_student_fees']['title'][] = $misc->title;
					$student[$misc->student_id]['misc_student_fees']['amount'][] = $misc->amount;

				}

				foreach($transportation_fee as $t)
				{
					$student[$t->student_id]['transportation_amount'] = $t->amount;
				}

				
				foreach($scholarships as $s)
				{
					//$student[$s->student_id]['scholarship']['type'][] = $s->type;
					$student[$s->student_id]['scholarship'][$s->type]['amount'] = $s->amount;
					$student[$s->student_id]['scholarship'][$s->type]['percent'] = $s->percent; 
				}

				foreach($transportation_scholarship as $s)
				{
					$student[$s->student_id]['scholarship'][$s->type]['amount'] = round( (float)$s->percent * $student[$s->student_id]['transportation_amount'] / 100);
					$student[$s->student_id]['scholarship'][$s->type]['percent'] = $s->percent;
				}

				foreach($taxes as $s)
				{
					$student[$s->student_id]['taxes']['type'][] = $s->type;
					$student[$s->student_id]['taxes']['amount'][] = $s->amount;
				}

				foreach($previous_dues as $p)
				{
					$student[$p->student_id]['previous_dues']['month'][] = $p->month;
					$student[$p->student_id]['previous_dues']['fee_amount'][] = $p->fee_amount;
					$student[$p->student_id]['previous_dues']['received_amount'][] = $p->received_amount;
					$student[$p->student_id]['previous_dues']['is_paid'][] = $p->is_paid;
				}

				foreach($hostel_fee as $h)
				{
					$student[$h->student_id]['hostel'] = $h->amount;
				}

				foreach($hostel_scholarship as $s)
				{
					$student[$s->student_id]['scholarship'][$s->type]['amount'] = round( (float)$s->percent * $student[$s->student_id]['hostel'] / 100);
					$student[$s->student_id]['scholarship'][$s->type]['percent'] = $s->percent;
				}

				foreach($payment as $p)
				{
					$student[$p->student_id]['payment']['fee_amount'] = $p->fee_amount;
					$student[$p->student_id]['payment']['received_amount'] = $p->received_amount;
					$student[$p->student_id]['payment']['is_paid'] = $p->is_paid;
				}

				/*echo '<pre>';
				print_r($student);
				die();*/

				
				return View::make($this->view.'mass-print')
								->with('status', 'success')
								->with('student', $student)
								->with('class_name', $class_name)
								->with('section', $section_code)
								->with('month', $month);
			}
		}
	}

	public function getUpdatePayment()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		return View::make($this->view. 'update-payment')
					->with('role', $this->role)
					->with('current_user', $this->current_user);
	}

	public function postUpdatePayment()
	{
		AccessController::allowedOrNot('fee', 'can_create,can_edit');
		$data = Input::all();
		
		// validating the input field
		$error = '';
		$is_error = false;
		
		// validate fee payment
		$payment_data = array(
											'id' => $data['payment_id'],
											'received_amount' => $data['received_amount'],
										);
		$result = $this->validateInput($payment_data, true, 'FeePayment');
		if($result['status'] == 'error')
		{
			$error .= 'Received Payment: '. $result['data']->first('received_amount') . '<br />';
			$is_error = true;
		}

		// validate monthly fee
		$monthly_fee_data = array(
																'id' => $data['monthly_fee_id'],
																'amount' => $data['monthly_fee']
															);
		$result = $this->validateInput($monthly_fee_data, true, 'MonthlyStudentFee');
		if($result['status'] == 'error')
		{
			$error .= 'Monthly Fee: '. $result['data']->first('amount') . '<br />';
			$is_error = true;
		}

		// validate misc class fee
		$misc_class_data = array();
		if (Input::has('misc_class_fee'))
		{
			for($i=0; $i<count($data['misc_class_fee']); $i++)
			{
				$misc_class_data_temp = array(
														'id' => $data['misc_class_fee_id'][$i],
														'amount' => $data['misc_class_fee'][$i]
													);
				$misc_class_data[] = $misc_class_data_temp;

				$result = $this->validateInput($misc_class_data_temp, true, 'MiscClassStudentFee');
				if($result['status'] == 'error')
				{
					$error .= $data['misc_class_fee_title'][$i]. ': '. $result['data']->first('amount') . '<br />';
					$is_error = true;
				}
			}
		}

		// validate misc student fee
		$misc_student_data = array();
		if (Input::has('misc_student_fee'))
		{
			for($i=0; $i<count($data['misc_student_fee']); $i++)
			{
				$misc_student_data_temp = array(
														'id' => $data['misc_student_fee_id'][$i],
														'amount' => $data['misc_student_fee'][$i]
													);
				$misc_student_data[] = $misc_student_data_temp;

				$result = $this->validateInput($misc_student_data_temp, true, 'MiscStudentFee');
				if($result['status'] == 'error')
				{
					$error .= $data['misc_student_fee_title'][$i]. ': '. $result['data']->first('amount') . '<br />';
					$is_error = true;
				}
			}
		}

		// validate examination fee
		if(Input::has('examination_fee'))
		{
			$examination_fee_data = array(
																'id' => $data['examination_fee_id'],
																'amount' => $data['examination_fee']
															);
			$result = $this->validateInput($examination_fee_data, true, 'ExaminationStudentFee');
			if($result['status'] == 'error')
			{
				$error .= 'Examination Fee: '. $result['data']->first('amount') . '<br />';
				$is_error = true;
			}
		}

		// validate transportation fee
		$transportation_fee_data = array(
																'id' => $data['transportation_fee_id'],
																'amount' => $data['transportation_fee']
															);
		$result = $this->validateInput($transportation_fee_data, true, 'TransportationStudentFee');
		if($result['status'] == 'error')
		{
			$error .= 'Transportation Fee: '. $result['data']->first('amount') . '<br />';
			$is_error = true;
		}

		// validate hostel fee
		$hostel_fee_data = array(
																'id' => $data['hostel_fee_id'],
																'amount' => $data['hostel_fee']
															);
		$result = $this->validateInput($hostel_fee_data, true, 'HostelStudentFee');
		if($result['status'] == 'error')
		{
			$error .= 'Hostel Fee: '. $result['data']->first('amount') . '<br />';
			$is_error = true;
		}

		// validate scholarship
		$scholarship_data = array();
		if(Input::has('scholarship'))
		{
			for($i=0; $i<count($data['scholarship']); $i++)
			{
				$scholarship_data_temp = array(
														'id' => $data['scholarship_id'][$i],
														'amount' => $data['scholarship'][$i]
													);
				$scholarship_data[] = $scholarship_data_temp;

				$result = $this->validateInput($scholarship_data_temp, true, 'ScholarshipMonthly');
				if($result['status'] == 'error')
				{
					$error .= 'Scholarship: '. $result['data']->first('amount') . '<br />';
					$is_error = true;
					break;
				}
			}
		}

		// validate tax
		$tax_data = array();
		if (isset($data['tax_id']))
		{
			for ($i = 0; $i < count($data['tax_id']); $i++)
			{
				$tax_data_temp = array(
														'id' => $data['tax_id'][$i],
														'amount' => $data['tax_amount'][$i]
													);
				$tax_data[] = $tax_data_temp;

				$result = $this->validateInput($tax_data_temp, true, 'FeeTax');
				if($result['status'] == 'error')
				{
					$error .= ucfirst(FeeTax::find($data['tax_id'][$i])->type) .' Tax: ' . $result['data']->first('amount') . '<br />';
					$is_error = true;
					break;
				}
			}
		}
				
		if($is_error)
		{
			
			Session::flash('error-msg', $error);
			return Redirect::to(URL::route('fee-update-payment-get').'?student_id='.$data['student_id'].'&month='.$data['month'])
						->withInput()
						->with('errors', $result['data']);
		}
		
		// recalculating the total fee
		$total_fee = 0;
		$total_fee += $monthly_fee_data['amount'];
		$total_fee += $transportation_fee_data['amount'];
		$total_fee += $hostel_fee_data['amount'];
		if(Input::has('examination_fee')) 
		{
			$total_fee += $examination_fee_data['amount'];
		}
		foreach($misc_class_data as $class_record)
		{
			$total_fee += $class_record['amount'];
		}
		foreach($misc_student_data as $student_record)
		{
			$total_fee += $student_record['amount'];
		}
		foreach($tax_data as $tax_record)
		{
			$total_fee += $tax_record['amount'];
		}

		// deduct scholarship
		foreach($scholarship_data as $scholarship)
		{
			$total_fee -= $scholarship['amount'];
		}

		$payment_data['fee_amount'] = $total_fee;
		$payment_data['is_paid'] = ($payment_data['received_amount'] >= $payment_data['fee_amount']) ? 'yes' : 'no';
		
		// update the payment and individual fees		
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$this->updateInDatabase($payment_data, [], 'FeePayment');
			$this->updateInDatabase($monthly_fee_data, [], 'MonthlyStudentFee');
			$this->updateInDatabase($transportation_fee_data, [], 'TransportationStudentFee');
			$this->updateInDatabase($hostel_fee_data, [], 'HostelStudentFee');
			if(Input::has('examination_fee'))
			{
				$this->updateInDatabase($examination_fee_data, [], 'ExaminationStudentFee');
			}
			foreach($misc_class_data as $class_record)
			{
				$this->updateInDatabase($class_record, [], 'MiscClassStudentFee');
			}
			foreach($misc_student_data as $student_record)
			{
				$this->updateInDatabase($student_record, [], 'MiscStudentFee');
			}
			foreach($scholarship_data as $scholarship)
			{
				$this->updateInDatabase($scholarship, [], 'ScholarshipMonthly');
			}
			foreach($tax_data as $tax_record)
			{
				$this->updateInDatabase($tax_record, [], 'FeeTax');
			}

			$success = true;
			$msg = 'Record successfully created';

			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		if($success)
		{
			Session::flash('success-msg', 'Payment updated');
			return Redirect::to(URL::route('fee-update-payment-get').'?student_id='.$data['student_id'].'&month='.$data['month']);
		}
		else
		{
			Session::flash('error-msg', $msg);
			return Redirect::to(URL::route('fee-update-payment-get').'?student_id='.$data['student_id'].'&month='.$data['month'])
							->withInput();
		}
	}

	public function getUpdatePaymentForm()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		if(!Input::has('student_id') || !Input::has('month'))
		{
			return 'invalid request';
		}

		$current_session_id = HelperController::getCurrentSession();

		$student = DB::table(Student::getTableName())
					->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'current_class_id')
					->where(Student::getTableName().'.student_id', Input::get('student_id'))
					->where(Student::getTableName().'.current_session_id', $current_session_id)
					->first();

		if(!StudentRegistration::find(Input::get('student_id')))
		{
			return View::make($this->view.'update-payment-form')
					->with('status', 'error')
					->with('msg', 'Student Not Registered');
		}
		elseif(!$student)
		{
			return View::make($this->view.'update-payment-form')
					->with('status', 'error')
					->with('msg', 'Student Not Registered For the current session');
		}
		else
		{
			$payment = FeePayment::where('student_id', Input::get('student_id'))
							->where('academic_session_id', $current_session_id)
							->where('month', Input::get('month'))
							->first();

			if(!$payment)
			{
				return View::make($this->view.'update-payment-form')
						->with('status', 'error')
						->with('msg', 'Fee not generated for the given month');
			}
			else
			{
				$monthly_fee = MonthlyStudentFee::where('student_id', Input::get('student_id'))
											->where('month', Input::get('month'))
											->where('academic_session_id', $current_session_id)
											->first();

				$misc_class_fees = 
						DB::table(MiscClassStudentFee::getTableName())
								->join(MiscClassFee::getTableName(), MiscClassFee::getTableName().'.id', '=', MiscClassStudentFee::getTableName().'.fee_misc_class_id')
								->where(MiscClassStudentFee::getTableName().'.student_id', Input::get('student_id'))
								->where(MiscClassStudentFee::getTableName().'.month', Input::get('month'))
								->where(MiscClassStudentFee::getTableName().'.academic_session_id', $current_session_id)
								->get();


				$misc_student_fees = 
					MiscStudentFee::where('student_id', Input::get('student_id'))
					->where(function($query) {
						return $query->where('month', Input::get('month'))
							->orWhere('month', 0);
					})
					->where('academic_session_id', $current_session_id)
					->get();

				$examination_fee = 
					DB::table(ExaminationStudentFee::getTableName())
								->join(ExaminationFee::getTableName(), ExaminationFee::getTableName().'.id', '=', ExaminationStudentFee::getTableName().'.fee_examination_id')
								->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', ExaminationFee::getTableName().'.exam_id')
								->where(ExaminationStudentFee::getTableName().'.student_id', Input::get('student_id'))
								->where(ExaminationStudentFee::getTableName().'.month', Input::get('month'))
								->where(ExaminationStudentFee::getTableName().'.academic_session_id', $current_session_id)
								->select(ExaminationStudentFee::getTableName().'.*', ExamConfiguration::getTableName().'.exam_name')
								->first();

				$transportation_fee = 
					TransportationStudentFee::where('student_id', Input::get('student_id'))
																->where('month', Input::get('month'))
																->where('academic_session_id', $current_session_id)
																->first();

				$hostel_fee = 
					HostelStudentFee::where('student_id', Input::get('student_id'))
																->where('month', Input::get('month'))
																->where('academic_session_id', $current_session_id)
																->first();

				$scholarships = 
					DB::table(Scholarship::getTableName())
							->join(ScholarshipMonthly::getTableName(), ScholarshipMonthly::getTableName().'.scholarship_id', '=', Scholarship::getTableName().'.id')
							->where(ScholarshipMonthly::getTableName().'.student_id', Input::get('student_id'))
							->where(ScholarshipMonthly::getTableName().'.academic_session_id', $current_session_id)
							->select(ScholarshipMonthly::getTableName().'.id', 'type')
							->lists('id', 'type');

				$taxes = 
					FeeTax::where('student_id', Input::get('student_id'))
						->where('month', Input::get('month'))
						->where('academic_session_id', $current_session_id)
						->get();

				$previous_dues = FeePayment::where('student_id', Input::get('student_id'))
								->where('academic_session_id', $current_session_id)
								->where('is_paid', 'no')
								->where('month', '!=', Input::get('month'))
								->orderBy('month')
								->get();

				$total_dues = $payment->fee_amount - $payment->received_amount;
				foreach ($previous_dues as $due)
				{
					$total_dues += $due->fee_amount - $due->received_amount;
				}

				return View::make($this->view.'update-payment-form')
								->with('status', 'success')
								->with('student', $student)
								->with('payment', $payment)
								->with('monthly_fee', $monthly_fee)
								->with('misc_class_fees', $misc_class_fees)
								->with('misc_student_fees', $misc_student_fees)
								->with('examination_fee', $examination_fee)
								->with('transportation_fee', $transportation_fee)
								->with('hostel_fee', $hostel_fee)
								->with('scholarships', $scholarships)
								->with('taxes', $taxes)
								->with('previous_dues', $previous_dues)
								->with('total_dues', $total_dues);

			}
		}
	}

	public function getFeeIndividual()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		return View::make($this->view.'.fee-individual')
					->with('role', $this->role);
	}

	public function getFeeIndividualInfo()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		if(!Input::has('student_id') || !Input::has('month'))
		{
			return 'invalid request';
		}

		$current_session_id = Input::get('academic_session_id');

		

		$student = DB::table(Student::getTableName())
					->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', Student::getTableName().'.student_id')
					->join(Classes::getTableName(), Classes::getTableName().'.id', '=', 'current_class_id')
					->where(Student::getTableName().'.student_id', Input::get('student_id'))
					->where(Student::getTableName().'.current_session_id', $current_session_id)
					->first();
		
		if(!StudentRegistration::find(Input::get('student_id')))
		{
			return View::make($this->view.'update-payment-form')
					->with('status', 'error')
					->with('msg', 'Student Not Registered');
		}
		elseif(!$student)
		{
			return View::make($this->view.'update-payment-form')
					->with('status', 'error')
					->with('msg', 'Student Not Registered For the current session');
		}
		else
		{
			if(Input::get('month')==0)
			{
				// we show all records
				$payments = FeePayment::where('student_id', Input::get('student_id'))
								->where('academic_session_id', $current_session_id)
								->orderBy('month')
								->get();

				return View::make($this->view.'fee-individual-info-all')
								->with('status', 'success')
								->with('student', $student)
								->with('academic_session_id', $current_session_id)
								->with('payments', $payments);	
			}
			else
			{
				$payment = FeePayment::where('student_id', Input::get('student_id'))
								->where('academic_session_id', $current_session_id)
								->where('month', Input::get('month'))
								->first();
				if(!$payment)
				{
					return View::make($this->view.'update-payment-form')
							->with('status', 'error')
							->with('msg', 'Fee not generated for the given month');
				}
				else
				{
					$monthly_fee = MonthlyStudentFee::where('student_id', Input::get('student_id'))
												->where('month', Input::get('month'))
												->where('academic_session_id', $current_session_id)
												->first();

					$misc_class_fees = 
						DB::table(MiscClassStudentFee::getTableName())
								->join(MiscClassFee::getTableName(), MiscClassFee::getTableName().'.id', '=', MiscClassStudentFee::getTableName().'.fee_misc_class_id')
								->where(MiscClassStudentFee::getTableName().'.student_id', Input::get('student_id'))
								->where(MiscClassStudentFee::getTableName().'.month', Input::get('month'))
								->where(MiscClassStudentFee::getTableName().'.academic_session_id', $current_session_id)
								->get();

					$misc_student_fees = 
						MiscStudentFee::where('student_id', Input::get('student_id'))
						->where(function($query) {
							return $query->where('month', Input::get('month'))
								->orWhere('month', 0);
						})
						->where('academic_session_id', $current_session_id)
						->get();

					$examination_fee = 
						DB::table(ExaminationStudentFee::getTableName())
									->join(ExaminationFee::getTableName(), ExaminationFee::getTableName().'.id', '=', ExaminationStudentFee::getTableName().'.fee_examination_id')
									->join(ExamConfiguration::getTableName(), ExamConfiguration::getTableName().'.id', '=', ExaminationFee::getTableName().'.exam_id')
									->where(ExaminationStudentFee::getTableName().'.student_id', Input::get('student_id'))
									->where(ExaminationStudentFee::getTableName().'.month', Input::get('month'))
									->where(ExaminationStudentFee::getTableName().'.academic_session_id', $current_session_id)
									->select(ExaminationStudentFee::getTableName().'.*', ExamConfiguration::getTableName().'.exam_name')
									->first();

					$transportation_fee = 
						TransportationStudentFee::where('student_id', Input::get('student_id'))
						->where('month', Input::get('month'))
						->where('academic_session_id', $current_session_id)
						->first();

					$hostel_fee = 
						HostelStudentFee::where('student_id', Input::get('student_id'))
						->where('month', Input::get('month'))
						->where('academic_session_id', $current_session_id)
						->first();

					$scholarships = 
						DB::table(ScholarshipMonthly::getTableName())
							->join(Scholarship::getTableName(), Scholarship::getTableName().'.id', '=', ScholarshipMonthly::getTableName().'.scholarship_id')
							->where(ScholarshipMonthly::getTableName().'.student_id', Input::get('student_id'))
							->where(ScholarshipMonthly::getTableName().'.month', Input::get('month'))
							->where(ScholarshipMonthly::getTableName().'.academic_session_id', $current_session_id)
							->select('type', 'amount')
							->get();

					$taxes = FeeTax::where('student_id', Input::get('student_id'))
						->where('month', Input::get('month'))
						->where('academic_session_id', $current_session_id)
						->get();

						$previous_dues = FeePayment::where('student_id', Input::get('student_id'))
								->where('academic_session_id', $current_session_id)
								->where('is_paid', 'no')
								->where('month', '!=', Input::get('month'))
								->orderBy('month')
								->get();

				$total_dues = $payment->fee_amount - $payment->received_amount;
				foreach ($previous_dues as $due)
				{
					$total_dues += $due->fee_amount - $due->received_amount;
				}

						

					return View::make($this->view.'fee-individual-info')
									->with('status', 'success')
									->with('student', $student)
									->with('payment', $payment)
									->with('monthly_fee', $monthly_fee)
									->with('misc_class_fees', $misc_class_fees)
									->with('misc_student_fees', $misc_student_fees)
									->with('examination_fee', $examination_fee)
									->with('transportation_fee', $transportation_fee)
									->with('hostel_fee', $hostel_fee)
									->with('scholarships', $scholarships)
									->with('taxes', $taxes)
									->with('total_dues', $total_dues);
				}
			}
				
		}
	}

	public function defaulterNotification()
	{
		$student_id = Input::get('student_id', 0);
		$month = Input::get('month');

		$student = StudentRegistration::find($student_id);
		
		if (!$student)
		{
			Session::flash('error-msg', 'Invalid student Id');
			return Redirect::back();
		}

		if (!($month >= 1 && $month <= 12))
		{
			Session::flash('error-msg', 'Invalid month');
			return Redirect::back();
		}

		$gcm = PushNotifications::where('user_id', $student_id)
			->where('user_group', 'student')
			->select('gcm_id')
			->first();
		
		$student_ids = array(
			$student_id => $gcm ? $gcm->gcm_id : NULL
		);
		
		$guardians = DB::table(Guardian::getTableName())
			->join(StudentGuardianRelation::getTableName(), StudentGuardianRelation::getTableName().'.guardian_id', '=', Guardian::getTableName().'.id')
			->join(Student::getTableName(), Student::getTableName().'.student_id', '=', StudentGuardianRelation::getTableName().'.student_id')
			->where(Student::getTableName().'.student_id', $student_id)
			->where(Student::getTableName().'.current_session_id', HelperController::getCurrentSession())
			->select(Guardian::getTableName().'.id')
			->get();
		
		$guardian_ids = array();

		foreach ($guardians as $guardian)
		{
			$gcm = PushNotifications::where('user_group', 'guardian')
				->where('user_id', $guardian->id)
				->select('gcm_id')
				->first();
			$guardian_ids[$guardian->id] = $gcm ? $gcm->gcm_id : NULL;
		}
		
		$msg_parents = 'fee' . ' # '.
			'Student ' . $student->student_name . ' has unpaid fees for the month ' . HelperController::getMonthName($month) .
			'. Please pay the fees on time';

		$msg_student = 'fee' . ' # '.
			'You have unpaid fees for the month ' . HelperController::getMonthName($month) .
			'. Please pay the fees on time';
					
		if(count($student_ids)) 
		{
			(new GcmController)->send($student_ids, $msg_student, array_keys($student_ids), 'student');
		}

		if(count($guardian_ids)) 
		{
			(new GcmController)->send($guardian_ids, $msg_parents, array_keys($guardian_ids), 'guardian');
		}

		Session::flash('success-msg', 'Notification sent');
		return Redirect::back();
	}

	public function getFeeClass()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		return View::make($this->view.'fee-class')
						->with('role', $this->role);
	}

	public function getFeeClassInfo()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		if(!Input::has('academic_session_id') || !Input::has('class_id') || !Input::has('section_id') || !Input::has('month'))
		{
			return;
		}

		$fee_table = FeePayment::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$student_table = Student::getTableName();
		$class_table = Classes::getTableName();
		$section_code = Section::find(Input::get('section_id'))->section_code;


		$payments = DB::table($fee_table)
						->join($student_registration_table, $student_registration_table.'.id', '=', $fee_table.'.student_id')
						->join($student_table, $student_table.'.student_id', '=', $fee_table.'.student_id')
						->join($class_table, $class_table.'.id', '=', $student_table.'.current_class_id')
						->where($student_table.'.current_session_id', Input::get('academic_session_id'))
						->where($student_table.'.current_class_id', Input::get('class_id'))
						->where($student_table.'.current_section_code', $section_code)
						->where($fee_table.'.month', Input::get('month'))
						->get();
		return View::make($this->view.'fee-class-info')
						->with('payments', $payments)
						->with('academic_session_id', Input::get('academic_session_id'))
						->with('class_id', Input::get('class_id'))
						->with('section_id', Input::get('section_id'))
						->with('month', Input::get('month'));
	}

	public function getFeeDetail()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		return View::make($this->view.'fee-detail')
					->with('role', $this->role);
	}

	public function getFeeDetailInfo()
	{
		AccessController::allowedOrNot('fee', 'can_view');
		if(!Input::has('academic_session_id') || !Input::has('month'))
		{
			return;
		}


		$details = array();

		$classes = Classes::where('academic_session_id', Input::get('academic_session_id'))
							->get();
		foreach($classes as $class)
		{
			$sections = DB::table(ClassSection::getTableName())
							->join(Section::getTableName(), Section::getTableName().'.section_code', '=', ClassSection::getTableName().'.section_code')
							->where(ClassSection::getTableName().'.class_id', $class->id)
							->select(ClassSection::getTableName().'.*', Section::getTableName().'.id as section_id')
							->get();

			foreach($sections as $section)
			{
				$students_query = DB::table(Student::getTableName())
								->where(Student::getTableName().'.current_session_id', Input::get('academic_session_id'))
								->where(Student::getTableName().'.current_class_id', $class->id)
								->where(Student::getTableName().'.current_section_code', $section->section_code);

				$total_students = count($students_query->get());
				
				$payment_query = $students_query
									->join(FeePayment::getTableName(), FeePayment::getTableName().'.student_id', '=', Student::getTableName().'.student_id')
									->where(FeePayment::getTableName().'.month', Input::get('month'));

				if($payment_query->first())
				{
					$generated_at = $payment_query->first()
									->updated_at;
					$generated_at = DateTime::createFromFormat('Y-m-d H:i:s', $generated_at)
											->format('d F Y, g:i A');
				}
				else
				{
					$generated_at = 'Not Generated yet';
				}

				$paid_students = count($payment_query
									->where('is_paid', 'yes')
									->get());

				$details[] = (object)
							[
								'class'			=> $class->class_name,
								'class_id'		=> $class->id,
								'section'		=> $section->section_code,
								'section_id'	=> $section->section_id,
								'total_students'=> $total_students,
								'paid_students'	=> $paid_students,
								'generated_at'	=> $generated_at
							];
			}

		}

		return View::make($this->view.'fee-detail-info')
					->with('details', $details)
					->with('academic_session_id', Input::get('academic_session_id'))
					->with('month', Input::get('month'));

	}
}
