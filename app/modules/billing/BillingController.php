<?php

define('GENERATE_FEE_LOCATION', app_path().'/modules/billing/files');

use Carbon\Carbon;

class BillingController extends BaseController
{

	protected $view = 'billing.views.';

	protected $model_name = 'Billing';

	protected $module_name = 'billing';

	protected $role;

	public $current_user;
	
		public function getListView()
	{
		
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					//->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					->with('role', $this->role);

	}

	public function getTotalReceipts()
	{
		AccessController::allowedOrNot('billing', 'can_view_receipt_transaction');
		return View::make($this->view.'receipt-transaction');
	}

	public function apiGetTotalReceipts()
	{
		$date_range = Input::get('date_range');
		$type = Input::get('type', 'date');
		$data['data'] = $this->apiGetTotalReceiptsData($date_range);

		return View::make($this->view.'.partials.partial-receipt')
					->with('data', $data)
					/* Billing-v1-changed-made-here */
					->with('date_range', $date_range);
					/* Billing-v1-changed-made-here */
	}

	public function apiGetTotalReceiptsData($date_range, $search_type = 'date')
	{
		$dates = BillingHelperController::getDateRange($date_range);

		$receipt_table = BillingReceipt::getTableName();
		//$student_registration_table = StudentRegistration::getTableName();
		$data = DB::table($receipt_table)
								//->join($student_registration_table, $student_registration_table.'.id', '=', $transaction_table.'.related_user_id')
								->where('received_on', '>=', $dates[0])
								->where('received_on', '<=', $dates[1])
								//->where('related_user_id', $student_id)
								//->where('related_user_group', 'student')
								->orderBy('received_on', 'ASC')
								->orderBy('receipt_no', 'ASC')
								->get();

		return $data;
	}

	///////// billing-cancel-v1-changes /////////
	public function postCancelInvoice($invoice_id)
	{
		try
		{
			DB::connection()->getPdo()->beginTransaction();

				$record = BillingInvoice::find($invoice_id);
				
				if($record->is_cleared == 'cancel')
				{
					throw new Exception('Invoice already cancelled');
				}

				$record->is_cleared = 'cancel';
				$record->note .= '\nThis invoice is Cancelled';
				$record->save();

				$related_transactions = DB::table(BillingTransaction::getTableName())
											->where('related_invoice_id', $invoice_id)
											->get();

				foreach($related_transactions as $r)
				{
					BillingTransaction::setReverseStatus($r->id);
				}



				Session::flash('success-msg', 'Invoice successfully cancelled. Please dont forget to cancel the corresponding receipt');

				DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back();
	}
	///////// billing-cancel-v1-changes /////////
	
	public function getReceiptListView()
	{
		AccessController::allowedOrNot('billing', 'can_view_receipt_list');
		
		$columns_to_show = [
								['column_name' 	=> 'financial_year',
								 'alias'		=> 'Financial Year'],

								['column_name' 	=> 'receipt_no',
								 'alias'		=> 'Receipt No'],

								['column_name' 	=> 'invoice_no',
								 'alias'		=> 'Invoice No'],

								['column_name' 	=> 'paid_amount',
								 'alias'		=> 'Paid Amount'],

								['column_name' 	=> 'amount_to_be_paid',
								 'alias'		=> 'Amount To Be Paid'],

								['column_name' 	=> 'received_from',
								 'alias'		=> 'Received From'],

								['column_name' 	=> 'name',
								 'alias'		=> 'Name'],

								['column_name' 	=> 'received_on',
								 'alias'		=> 'Received On'],

								['column_name' 	=> 'created_by',
								 'alias'		=> 'Received By'],
							];

		$model = new BillingReceipt;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns($columns_to_show);
		$tableHeaders = $this->getTableHeader($columns_to_show);
		$queries = $this->getQueries();

		return View::make($this->view.'receipt-list')
					->with('module_name', $this->module_name)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders);
	}
	
	public function sendDuesPushNotification($student_id, $invoice_balance, $received_amount)
	{
		AccessController::allowedOrNot('billing', 'can_send_push_notification');
		$guardian_ids = DB::table('student_registration')						
					->join('student_guardian_relation','student_guardian_relation.student_id','=','student_registration.id')
					->join('guardians', 'guardians.id' , '=' , 'student_guardian_relation.guardian_id')
					->select('guardians.id')
							->where('student_registration.id',$student_id)
							->lists('id');

		if(!$guardian_ids)

		{
			return Redirect::back()
			->with('error-msg', 'No guardian found for this student');
		}



		$gcm_ids = DB::table('push_notifications')
					->whereIn('user_id',$guardian_ids)
					->where('user_group', 'guardian')
					->lists('gcm_id', 'user_id');
		$dues = $invoice_balance -  $received_amount;

		$todays_date = date('Y-m-d');

		$todays_time = date('H:i:s');

		$new_date = (new DateConverter)->ad2bs($todays_date);
		

		$msg = 'Remaning Dues # ' ."\n".
									'message_subject: ' . 'Dues' . "\n". 
									
									'message: ' . 'You have Rs. ' . $dues. ' remaining dues' . "\n";
									
									
									

		
		if(count($gcm_ids))
		{
			(new GcmController)->send(array_values($gcm_ids), $msg, array_keys($gcm_ids), 'guardian');
			return Redirect::back()->with('success-msg', 'Successfully Sent Message to parent');
		}
		else
		{
			return Redirect::back()
							->with('error-msg','No GCM id registered for parents');
		}


	}

	

	public function getViewReceiptFromReceiptId($receipt_id)
	{
		AccessController::allowedOrNot('billing', 'can_view_receipt_list');

		$data = BillingReceipt::where('id', $receipt_id)
								->first();

		return View::make($this->view.'receipt')
					->with('data', $data);
	}

	public function getExtraCreateFeeView()
	{
		AccessController::allowedOrNot('billing', 'can_create_extra_fee');
		return View::make($this->view.'extra-fees.create');
	}

	public function apiGetExtraFeeStudentListView()
	{
		$session_id = Input::get('academic_session_id', 0);
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);

		$fee_id = Input::get('fee_id', 0);

		$data = $this->ajaxGetStudentListData($session_id, $class_id, $section_id);

		$related_student_ids = [];
		foreach($data as $d)
		{
			$related_student_ids[] = $d->id;
		}

		$existing_data = BillingExtraFees::where('fee_id', $fee_id)
										->whereIn('student_id', $related_student_ids)
										->lists('fee_amount', 'student_id');

		return View::make($this->view.'extra-fees.partials-student-list')
					->with('data', $data)
					->with('existing_data', $existing_data);
	}

	public function postExtraCreateFeeView()
	{
		AccessController::allowedOrNot('billing', 'can_create_extra_fee');
		$input = Input::all();

		$data_to_store = [];
		$data_to_store['fee_id'] = $input['fee_id'];
		$data_to_store['is_active'] = 'yes';

		try
		{
			DB::connection()->getPdo()->beginTransaction();
			foreach($input['student_id'] as $index => $student_id)
			{
				$data_to_store['student_id'] = $student_id;
				$data_to_store['fee_amount'] = $input['fee_amount'][$index];

				
				//check if student id exits
				$check = BillingExtraFees::where('student_id', $student_id)
										->where('fee_id', $input['fee_id'])
										->first();

				if($check)
				{
					$this->updateInDatabase($data_to_store, [['field' => 'student_id', 'operator' => '=', 'value' => $data_to_store['student_id']], ['field' => 'fee_id', 'operator' => '=', 'value' => $data_to_store['fee_id']]], 'BillingExtraFees');
				}
				else
				{
					if($data_to_store['fee_amount'] == 0)
					{
						continue;
					}
					else
					{
						$this->storeInDatabase($data_to_store, 'BillingExtraFees');
					}
					
				}
			}
			Session::flash('success-msg', 'Successfully stored');
			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
		}
		
		return Redirect::back();
	}

	public function getCreateFeeView()
	{
		AccessController::allowedOrNot('billing', 'can_create_fee');
		$fees = BillingFee::select('id', 'fee_category', 'fee_type')->get();
		return View::make($this->view.'create-fee')
					->with('fees', $fees);
	}

	public function postCreateFeeView()
	{
		AccessController::allowedOrNot('billing', 'can_create_fee');
		$input = Input::all();

		$created_by_updated_by = $this->getCreatedByUpdatedBy($update = false);

		$data_to_store_in_fee_table = [];
		$data_to_store_in_fee_table['fee_type'] = $input['fee_type'];
		$data_to_store_in_fee_table['fee_category'] = $input['fee_category'];
		$data_to_store_in_fee_table['description'] = $input['description'];
		$data_to_store_in_fee_table['tax_applicable'] = $input['tax_applicable'];
		$data_to_store_in_fee_table['is_active'] = 'yes';

		//////////////////////////////
		try
		{
			DB::connection()->getPdo()->beginTransaction();
			$fee_id = $this->storeInDatabase($data_to_store_in_fee_table, 'BillingFee');
			
			if($input['fee_type'] != 'onetime')
			{
				$class_section_table = ClassSection::getTableName();
				$section_table = Section::getTableName();

				foreach($input['fee_amount'] as $index => $fee)
				{
					$class_id = $input['class_id'][$index];
					if($input['section_id'][$index] == 'all')
					{
						$excluded_section_ids = [];
						foreach($input['class_id'] as $class_index => $class_index_id)
						{
							if($class_index_id == $class_id)
							{
								$excluded_section_ids[] = $input['section_id'][$class_index];
								
							}
						}


						$section_ids = DB::table($class_section_table)
										->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
										->where($section_table.'.is_active', 'yes')
										->where($class_section_table.'.is_active', 'yes')
										->where('class_id', $class_id)
										->whereNotIn($section_table.'.id', $excluded_section_ids)
										->select($section_table.'.id')
										->lists('id');
					}
					else
					{
						$section_ids = [$input['section_id'][$index]];

					}

					foreach($section_ids as $section_id)
					{
						$data_to_store_in_fee_students_table= [];
						$data_to_store_in_fee_students_table['fee_id'] = $fee_id;
						$data_to_store_in_fee_students_table['class_id'] = $class_id;
						$data_to_store_in_fee_students_table['fee_amount'] = $input['fee_amount'][$index];
						$data_to_store_in_fee_students_table['section_id'] = $section_id;
						$data_to_store_in_fee_students_table['is_active'] = 'yes';
						$data_to_store_in_fee_students_table = array_merge($data_to_store_in_fee_students_table, $created_by_updated_by);
						BillingFeeStudent::create($data_to_store_in_fee_students_table);
					}
					
				}	
			}
			
			DB::connection()->getPdo()->commit();			
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back();
		}

		/////////////////////////////////////////////////////////

		Session::flash('success-msg', 'Fee successfully created');
		return Redirect::back();

	}

	public function getEditFee($id)
	{
		AccessController::allowedOrNot('billing', 'can_edit_fee');
		$data = (new BillingFee)->getEditViewData($id);
		return View::make($this->view.'edit-fee')->with('id', $id)->with('data', $data);
					//->with('data', $data);
	}

	public function postEditFee($id)
	{
		AccessController::allowedOrNot('billing', 'can_edit_fee');
		$input = Input::all();

		$created_by_updated_by = $this->getCreatedByUpdatedBy($update = true);

		$data_to_store_in_fee_table = [];
		$data_to_store_in_fee_table['fee_type'] = $input['fee_type'];
		$data_to_store_in_fee_table['fee_category'] = $input['fee_category'];
		$data_to_store_in_fee_table['description'] = $input['description'];
		$data_to_store_in_fee_table['tax_applicable'] = $input['tax_applicable'];
		$data_to_store_in_fee_table['is_active'] = 'yes';
		$data_to_store_in_fee_table['id'] = $id;

		//////////////////////////////
		try
		{
			DB::connection()->getPdo()->beginTransaction();
			$fee_id = $this->updateInDatabase($data_to_store_in_fee_table, [], 'BillingFee');
			
			BillingFeeStudent::where('fee_id', $id)->delete();
			if($input['fee_type'] != 'onetime')
			{
				$class_section_table = ClassSection::getTableName();
				$section_table = Section::getTableName();

				foreach($input['fee_amount'] as $index => $fee)
				{
					$class_id = $input['class_id'][$index];
					if($input['section_id'][$index] == 'all')
					{
						$excluded_section_ids = [];
						foreach($input['class_id'] as $class_index => $class_index_id)
						{
							if($class_index_id == $class_id)
							{
								$excluded_section_ids[] = $input['section_id'][$class_index];
								
							}
						}


						$section_ids = DB::table($class_section_table)
										->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
										->where($section_table.'.is_active', 'yes')
										->where($class_section_table.'.is_active', 'yes')
										->where('class_id', $class_id)
										->whereNotIn($section_table.'.id', $excluded_section_ids)
										->select($section_table.'.id')
										->lists('id');
					}
					else
					{
						$section_ids = [$input['section_id'][$index]];

					}

					foreach($section_ids as $section_id)
					{
						$data_to_store_in_fee_students_table= [];
						$data_to_store_in_fee_students_table['fee_id'] = $fee_id;
						$data_to_store_in_fee_students_table['class_id'] = $class_id;
						$data_to_store_in_fee_students_table['fee_amount'] = $input['fee_amount'][$index];
						$data_to_store_in_fee_students_table['section_id'] = $section_id;
						$data_to_store_in_fee_students_table['is_active'] = 'yes';
						$data_to_store_in_fee_students_table = array_merge($data_to_store_in_fee_students_table, $created_by_updated_by);
						BillingFeeStudent::create($data_to_store_in_fee_students_table);
					}
					
				}				
			}

			DB::connection()->getPdo()->commit();			
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back();
		}			

		Session::flash('success-msg', 'Fee successfully updated');
		return Redirect::back();
	}

	public function postDeleteFeeView($id)
	{
		AccessController::allowedOrNot('billing', 'can_delete_fee');
		try
		{
			BillingFee::where('id', $id)->delete();	
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}


		Session::flash('success-msg', 'Successfully deleted');
		return Redirect::back();
	}

	public function getIncomeReportView()
	{
		AccessController::allowedOrNot('billing', 'can_view_income_report');
		return View::make($this->view.'income-report');
	}

	public function getIncomeReportStudents()
	{
		AccessController::allowedOrNot('billing', 'can_view_income_report');
		$daterange = Input::get('daterange');
		$class_section = Input::get('class_section_');
		$data = $this->getIncomeReportStudentsData($daterange, $class_section);
		
		/*echo '<pre>';
		print_r($data);
		die();*/

		$fee_titles = array_keys($data['fee_titles']);
		return View::make($this->view.'partials.partial-income-report-students')
					->with('data', $data)
					->with('fee_titles', $fee_titles)
					->with('class_section', $class_section);

	}

	public function getIncomeReportStudentsData($daterange, $class_section)
	{
		AccessController::allowedOrNot('billing', 'can_view_income_report');	
		$dates = BillingHelperController::getDateRange($daterange);

		$billing_invoice_table = BillingInvoice::getTableName();

		$data = DB::table($billing_invoice_table)
					->where('issued_date', '>=', $dates[0])
					->where('issued_date', '<=', $dates[1])
					->where($billing_invoice_table.'.is_active', 'yes')
					->where('is_final', 'yes')
					->where('class_section', $class_section )
					->whereIn('invoice_type', SsmConstants::$const_billing_types['credit'])
					->select($billing_invoice_table.'.*')
					///////// billing-cancel-v1-changes /////////
					->where('is_cleared', '!=', 'cancel')
					///////// billing-cancel-v1-changes /////////
					->get();
		
		$credit_notes_data = DB::table($billing_invoice_table)
					->where('issued_date', '>=', $dates[0])
					->where('issued_date', '<=', $dates[1])
					->where($billing_invoice_table.'.is_active', 'yes')
					->where('is_final', 'yes')
					->where('class_section', $class_section)
					->whereIn('invoice_type', ['credit_note'])
					///////// billing-cancel-v1-changes /////////
					->where('is_cleared', '!=', 'cancel')
					///////// billing-cancel-v1-changes /////////
					->select($billing_invoice_table.'.*')
					->get();
		

		$return_data = [];
		$fee_titles = [];
		$total_amount = [];
		$received_amount = [];
		$flat_discounts = [];
		$unpaid_amount = [];
		foreach($data as $index => $d)
		{
			$json = json_decode($d->invoice_details, true);
			$student_names['group'] = $json['personal_details']['group'];
			$student_names['id'] = $json['personal_details']['id'];
			$student_names[$json['personal_details']['group']][$json['personal_details']['id']]['name'] = $json['personal_details']['name'];

			if(isset($flat_discounts[$student_names['group']][$student_names['id']]))
			{
				$flat_discounts[$student_names['group']][$student_names['id']] = $flat_discounts[$student_names['group']][$student_names['id']] + $d->flat_discounts;
			}
			else
			{
				$flat_discounts[$student_names['group']][$student_names['id']] = ($d->flat_discounts);
			}

			if(isset($total_amount[$student_names['group']][$student_names['id']]))
			{
				$total_amount[$student_names['group']][$student_names['id']] = $total_amount[$student_names['group']][$student_names['id']] + $d->invoice_balance;
			}
			else
			{
				$total_amount[$student_names['group']][$student_names['id']] = $d->invoice_balance;
			}

			if(isset($unpaid_amount[$student_names['group']][$student_names['id']]))
			{
				$unpaid_amount[$student_names['group']][$student_names['id']] = $unpaid_amount[$student_names['group']][$student_names['id']] + ($d->invoice_balance - $d->received_amount - $d->flat_discounts);
			}
			else
			{
				$unpaid_amount[$student_names['group']][$student_names['id']] = ($d->invoice_balance - $d->received_amount - $d->flat_discounts);
			}

			if(isset($received_amount[$student_names['group']][$student_names['id']]))
			{
				$received_amount[$student_names['group']][$student_names['id']] = $received_amount[$student_names['group']][$student_names['id']] + $d->received_amount;
			}
			else
			{
				$received_amount[$student_names['group']][$student_names['id']] = $d->received_amount;
			}	

			foreach($json['fees'] as $index => $fee)
			{
				$return_data[$student_names['group']][$student_names['id']]['fees'][$fee['fee_title']] = isset($return_data[$student_names['group']][$student_names['id']]['fees'][$fee['fee_title']]) ? $return_data[$student_names['group']][$student_names['id']]['fees'][$fee['fee_title']] + $fee['fee_amount'] : $fee['fee_amount'];

				$return_data[$student_names['group']][$student_names['id']]['total'] = isset($return_data[$student_names['group']][$student_names['id']]['total']) ? $return_data[$student_names['group']][$student_names['id']]['total'] + $fee['fee_amount'] : $fee['fee_amount'];

				$fee_titles[$fee['fee_title']] = isset($fee_titles[$fee['fee_title']]) ? $fee_titles[$fee['fee_title']] + $fee['fee_amount'] : $fee['fee_amount'];
			}

			if(isset($json['discount']))
			{
				foreach($json['discount'] as $discount)
				{
					if(isset($return_data[$student_names['group']][$student_names['id']]['fees'][$discount['fee_title']]))
					{
						$return_data[$student_names['group']][$student_names['id']]['fees'][$discount['fee_title']] -= $discount['discount_amount'];
						$return_data[$student_names['group']][$student_names['id']]['total'] -= $discount['discount_amount'];
						$fee_titles[$discount['fee_title']] -= $discount['discount_amount'];
					}
				}
			}


			unset($data[$index]);
		}

		$credit_note = [];
		$credit_note['total'] = 0;
		foreach($credit_notes_data as $index => $d)
		{	
			$json = json_decode($d->invoice_details, true);
			$student_names['group'] = $json['personal_details']['group'];
			$student_names['id'] = $json['personal_details']['id'];

			$credit_note[$student_names['group']][$student_names['id']]['total'] = isset($credit_note[$student_names['group']][$student_names['id']]['total']) ? ($credit_note[$student_names['group']][$student_names['id']]['total'] + $d->invoice_balance) : $d->invoice_balance;

			$credit_note[$student_names['group']][$student_names['id']]['tax'] = isset($credit_note[$student_names['group']][$student_names['id']]['tax']) ? ($credit_note[$student_names['group']][$student_names['id']]['tax'] + $json['summary']['tax']) : $json['summary']['tax'];

			$unpaid_amount[$student_names['group']][$student_names['id']] -= $d->invoice_balance;
			
			unset($credit_notes_data[$index]);
		}

		return ['data' => $return_data, 
				'fee_titles' => $fee_titles, 
				'unpaid_amount' => $unpaid_amount, 
				'credit_note' => $credit_note, 
				'student_names' => $student_names, 
				'flat_discounts' => $flat_discounts, 
				'total_amount' => $total_amount,
				'received_amount' => $received_amount];
	}

	public function apiGetIncomeReportView()
	{
		$date_range = Input::get('date_range');

		$data = $this->apiGetIncomeReportData($date_range);
		
		return View::make($this->view.'partials.partial-income-report')
					->with('data', $data);
	}

	///// Billing-v1-changed-made-here /////////
	public function apiGetIncomeReportData($date_range)
	{

		$dates = BillingHelperController::getDateRange($date_range);

		$billing_invoice_table = BillingInvoice::getTableName();
		$data = DB::table($billing_invoice_table)
					->where('issued_date', '>=', $dates[0])
					->where('issued_date', '<=', $dates[1])
					->where($billing_invoice_table.'.is_active', 'yes')
					->where('is_final', 'yes')
					->whereIn('invoice_type', SsmConstants::$const_billing_types['credit'])
					->select($billing_invoice_table.'.*')
					///////// billing-cancel-v1-changes /////////
					->where('is_cleared', '!=', 'cancel')
					///////// billing-cancel-v1-changes /////////
					->orderBy('class_section', 'ASC')
					->get();

		$credit_notes_data = DB::table($billing_invoice_table)
					->where('issued_date', '>=', $dates[0])
					->where('issued_date', '<=', $dates[1])
					->where($billing_invoice_table.'.is_active', 'yes')
					->where('is_final', 'yes')
					->whereIn('invoice_type', ['credit_note'])
					->select($billing_invoice_table.'.*')
					///////// billing-cancel-v1-changes /////////
					->where('is_cleared', '!=', 'cancel')
					///////// billing-cancel-v1-changes /////////
					->orderBy('class_section', 'ASC')
					->get();

		$return_data = [];
		$fee_titles = [];
		$unpaid_amount = [];
		$total_amount = [];
		$total_amount_total = 0;
		$received_amount = [];
		$received_amount_total = 0;
		$unpaid_amount_total = 0;
		$flat_discounts_total = 0;
		$flat_discounts = [];
		
		foreach($data as $index => $d)
		{
			$json = json_decode($d->invoice_details, true);

			if(isset($unpaid_amount[$d->class_section]))
			{
				$unpaid_amount[$d->class_section] = $unpaid_amount[$d->class_section] + ($d->invoice_balance - $d->received_amount - $d->flat_discounts);
			}
			else
			{
				$unpaid_amount[$d->class_section] = ($d->invoice_balance - $d->received_amount - $d->flat_discounts);
			}
			$unpaid_amount_total += ($d->invoice_balance - $d->received_amount - $d->flat_discounts);

			if(isset($received_amount[$d->class_section]))
			{
				$received_amount[$d->class_section] = $received_amount[$d->class_section] + $d->received_amount;
			}
			else
			{
				$received_amount[$d->class_section] = $d->received_amount;
			}
			$received_amount_total += $d->received_amount;

			if(isset($total_amount[$d->class_section]))
			{
				$total_amount[$d->class_section] = $total_amount[$d->class_section] + $d->invoice_balance;
			}
			else
			{
				$total_amount[$d->class_section] = $d->invoice_balance;
			}
			$total_amount_total += $d->invoice_balance;

			if(isset($flat_discounts[$d->class_section]))
			{
				$flat_discounts[$d->class_section] = $flat_discounts[$d->class_section] + $d->flat_discounts;
			}
			else
			{
				$flat_discounts[$d->class_section] = $d->flat_discounts;
			}
			$flat_discounts_total += $d->flat_discounts;



			foreach($json['fees'] as $index => $fee)
			{
				$return_data[$d->class_section]['fees'][$fee['fee_title']] = isset($return_data[$d->class_section]['fees'][$fee['fee_title']]) ? $return_data[$d->class_section]['fees'][$fee['fee_title']] + $fee['fee_amount'] : $fee['fee_amount'];

				$return_data[$d->class_section]['total'] = isset($return_data[$d->class_section]['total']) ? $return_data[$d->class_section]['total'] + $fee['fee_amount'] : $fee['fee_amount'];

				$fee_titles[$fee['fee_title']] = isset($fee_titles[$fee['fee_title']]) ? $fee_titles[$fee['fee_title']] + $fee['fee_amount'] : $fee['fee_amount'];
			}

			if(isset($json['discount']))
			{
				foreach($json['discount'] as $discount)
				{
					if(isset($return_data[$d->class_section]['fees'][$discount['fee_title']]))
					{
						$return_data[$d->class_section]['fees'][$discount['fee_title']] -= $discount['discount_amount'];
						$return_data[$d->class_section]['total'] -= $discount['discount_amount'];
						$fee_titles[$discount['fee_title']] -= $discount['discount_amount'];
					}
				}
			}


			unset($data[$index]);
		}

		$credit_note['total'] = [];
		$credit_note['tax'] = [];
		$credit_note_total = 0;
		$credit_note_tax_total = 0;
		foreach($credit_notes_data as $index => $d)
		{
			$json = json_decode($d->invoice_details, true);

			$credit_note[$d->class_section]['total'] = isset($credit_note[$d->class_section]['total']) ? ($credit_note[$d->class_section]['total'] + $d->invoice_balance) : $d->invoice_balance;
			$credit_note[$d->class_section]['tax'] = isset($credit_note[$d->class_section]['tax']) ? ($credit_note[$d->class_section]['tax'] + $json['summary']['tax']) : $json['summary']['tax'];

			$unpaid_amount[$d->class_section] -= $d->invoice_balance;
			$unpaid_amount_total -= $d->invoice_balance;

			$credit_note_total +=  $d->invoice_balance;
			$credit_note_tax_total += $json['summary']['tax'];
			unset($credit_notes_data[$index]);
		}


		return ['data' => $return_data, 
				'fee_titles' => $fee_titles, 
				'unpaid_amount' => $unpaid_amount, 
				'unpaid_amount_total' => $unpaid_amount_total, 
				'credit_note' => $credit_note, 
				'credit_note_total' => $credit_note_total,
				'credit_note_tax_total' => $credit_note_tax_total,
				'total_amount' => $total_amount,
				'total_amount_total' => $total_amount_total,
				'flat_discounts_total' => $flat_discounts_total, 
				'flat_discounts' => $flat_discounts,
				'received_amount' => $received_amount,
				'received_amount_total' => $received_amount_total];

	}
	//////// Billing-v1-changed-made-here ///////////

	public function getTaxReportView()
	{
		AccessController::allowedOrNot('billing', 'can_view_tax_report');
		return View::make($this->view.'tax-report');
	}

	public function getRemainingDueList()
	{
		AccessController::allowedOrNot('billing', 'can_view_remaining_due_list');
		return View::make($this->view.'.due-reports.list');
	}

	public function getLateFee()
	{
		AccessController::allowedOrNot('billing', 'can_set_late_fee');
		$access = File::get(app_path().'/modules/'.$this->module_name.'/config/config.json');
		$access = json_decode($access, true);
	

		return View::make($this->view.'.late-fee')->with('access', $access);
	}

	public function postLateFee()
	{
		AccessController::allowedOrNot('billing', 'can_set_late_fee');
		$get_input_late_fee['start_month'] = 9;
		$get_input_late_fee['start_day'] = 1;
		$get_input_late_fee['late_fee_days'] = (int)Input::get('late_fee_days');
		$get_input_late_fee['late_fee_amount'] = (int)Input::get('late_fee_amount');
		$get_input_late_fee['late_fee_tax_applicable'] = Input::get('late_fee_tax_applicable');

		File::put(app_path().'/modules/'.$this->module_name.'/config/config.json', json_encode($get_input_late_fee, JSON_PRETTY_PRINT)); 
		if ($get_input_late_fee) {
			return Redirect::route('billing-late-fee-get')->with('success-msg','Late Fee Setting Set Successfully');
		}
		return Redirect::route('billing-late-fee-get')->with('error-msg','Late Fee Setting Set Error');
	}

	public function apiGetDueInvoices()
	{
		$due_days = Input::get('due_days');
		$start_date = Input::get('start_date');

		$data = $this->apiGetDueInvoicesData($due_days, $start_date);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d M Y');
		return View::make($this->view.'due-reports.ajax-remaining-due')
					->with('data', $data)
					->with('start_date', $start_date);

	}

	public function apiGetDueInvoicesData($due_days, $start_date)
	{
		$start_date = Carbon::createFromFormat('Y-m-d', trim($start_date))->second(59)->minute(59)->hour(23);
		$start_date = $start_date->subDays((int) $due_days)->format('Y-m-d H:i:s');
		
		//$sql = " DATEDIFF( '".date('Y-m-d')."', `created_at`) >= ".$due_days;
		$data = BillingInvoice::select(array('invoice_balance', 'received_amount', 'id', 'related_user_group', 'class_section', 'created_at'))
								//->whereRaw( $sql )
								->where('issued_date', '<=', $start_date)
								->where('is_final','yes')
								->where('is_cleared', '!=', 'yes')
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->orderBy('related_user_group', 'ASC')
								->orderBy('class_section', 'DESC')
								->get();
		
		$return_data = [];
		foreach($data as $d)
		{
			$credit_note_sum = BillingHelperController::calculateSumOfCreditNotes($d->id);
			if(isset($return_data[$d->related_user_group][$d->class_section]))
				$return_data[$d->related_user_group][$d->class_section] += ($d->invoice_balance - $d->received_amount);
			else
				$return_data[$d->related_user_group][$d->class_section] = ($d->invoice_balance - $d->received_amount);
			$return_data[$d->related_user_group][$d->class_section] -= $credit_note_sum;
		}



		return $return_data;
	}

	public function apiGetDueInvoicesDetails()
	{
		$due_days = Input::get('due_days');
		$start_date = Input::get('start_date');
		$related_user_group = Input::get('related_user_group');
		$class_section = Input::get('class_section');

		$data = [];
		$temp_data = $this->apiGetDueInvoiceDetailsData($start_date, $due_days, $related_user_group, $class_section);

		foreach($temp_data as $index => $d)
		{
			if(isset($data[$d->related_user_id]))
			{
				$data[$d->related_user_id]->invoice_balance += $d->invoice_balance - BillingHelperController::calculateSumOfCreditNotes($d->id);
				$data[$d->related_user_id]->received_amount += $d->received_amount;
				$data[$d->related_user_id]->financial_year .= ','.$d->financial_year;
				$data[$d->related_user_id]->invoice_number .= ','.$d->invoice_number;
			}
			else
			{
				$d->invoice_balance -= BillingHelperController::calculateSumOfCreditNotes($d->id);
				$data[$d->related_user_id] = $d;
			}

			unset($temp_data[$index]);
		}
		unset($temp_data);

		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d M Y');
		return View::make($this->view."due-reports.ajax-remaining-due-details")
					->with('data', $data)
					->with('related_user_group', $related_user_group)
					->with('class_section', $class_section)
					->with('start_date', $start_date);

		
	}

	public function apiGetDueInvoiceDetailsData($start_date, $due_days, $related_user_group, $class_section)
	{
		$invoice_table = BillingInvoice::getTableName();
		$start_date = Carbon::createFromFormat('Y-m-d', trim($start_date))->second(59)->minute(59)->hour(23);

		$start_date = $start_date->subDays((int) $due_days)->format('Y-m-d H:i:s');
		
		if($related_user_group == 'organization')
		{
			
			$organization_table = BillingDiscountOrganization::getTableName();

			$data = DB::table($invoice_table)
						->join($organization_table, function($join) use ($invoice_table, $organization_table)
							{
								$join->on($invoice_table.'.related_user_id', '=', $organization_table.'.id')
									 ->where('related_user_group', '=', 'organization');
							})
						->select(array('invoice_balance', 'received_amount', $invoice_table.'.id', 'related_user_group', 'class_section', $invoice_table.'.issued_date as as created_at', 'organization_name as name', 'related_user_id', 'financial_year', 'invoice_number'))
						//->whereRaw( $sql )
						->where($invoice_table.'.issued_date', '<=', $start_date)
						->where('is_final','yes')
						->where('is_cleared', '!=', 'yes')
						///////// billing-cancel-v1-changes /////////
						->where('is_cleared', '!=', 'cancel')
						///////// billing-cancel-v1-changes /////////
						->where('related_user_group', $related_user_group)
						->orderBy('name', 'ASC')
						->get();
		}
		elseif($related_user_group == 'student')
		{
			
			$student_registration_table = StudentRegistration::getTableName();

			$data = DB::table($invoice_table)
					->join($student_registration_table, function($join) use ($invoice_table, $student_registration_table)
					{
						$join->on($invoice_table.'.related_user_id', '=', $student_registration_table.'.id')
							 ->where('related_user_group', '=', 'student');
					})
					->select(array('invoice_balance', 'received_amount', $invoice_table.'.id', 'related_user_group', 'class_section', $invoice_table.'.issued_date as as created_at', 'student_name as name', 'last_name', 'related_user_id', 'financial_year', 'invoice_number'))
					//->whereRaw( $sql )
					->where($invoice_table.'.issued_date', '<=', $start_date)
					->where('is_final','yes')
					->where('is_cleared', '!=', 'yes')
					///////// billing-cancel-v1-changes /////////
					->where('is_cleared', '!=', 'cancel')
					///////// billing-cancel-v1-changes /////////
					->where('related_user_group', $related_user_group)
					->where('class_section', $class_section)
					->orderBy('name', 'ASC')
					->get();
		}

		return $data;
	}

	public function apiGetTaxReportData($date_range, $only_cleared)
	{
		$dates = BillingHelperController::getDateRange($date_range);

		$data = BillingInvoice::where('issued_date', '>=', $dates[0])
								->where('issued_date', '<=', $dates[1])
								->where('is_active', 'yes')
								->where('is_final', 'yes')
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->whereIn('invoice_type', SsmConstants::$const_billing_types['credit']);

		$data = $only_cleared == 'yes' ? $data->where('is_cleared', 'yes') : $data;
		$data= $data->get();
		
		$aggregate = ['taxable_amount' => 0, 'untaxable_amount' => 0, 'tax' => 0, 'total' => 0, 'count' => 0];
		
		$i = 0;
		foreach($data as $index => $d)
		{
			$i++;
			$data[$index]->invoice_details = json_decode($d->invoice_details);
			$aggregate['taxable_amount'] += $data[$index]->invoice_details->summary->taxable_amount;
			$aggregate['untaxable_amount'] += $data[$index]->invoice_details->summary->untaxable_amount;
			$aggregate['tax'] += $data[$index]->invoice_details->summary->tax;
			$aggregate['total'] += $data[$index]->invoice_details->summary->total;
		}

		$aggregate['count'] = $i;

		return ['data' => $data, 'aggregate' => $aggregate];
	}

	public function apiGetTaxReportView()
	{
		$date_range = Input::get('date_range');
		$only_cleared = Input::get('only_cleared');

		$data = $this->apiGetTaxReportData($date_range, $only_cleared);

		return View::make($this->view.'partials.partial-tax-report')
					->With('data', $data);
	}

	public function getRecievePaymentOrganizationView()
	{
		AccessController::allowedOrNot('billing', 'can_receive_payment');
		////// Billing-v1-changed-made-here ///////
		$invoice_number = BillingReceipt::getLatestReceiptNumber();
		////// Billing-v1-changed-made-here ///////
		return View::make($this->view.'receive-payment-organization')
					->with('invoice_number', $invoice_number);	
	}

	public function getRecievePaymentView()
	{
		AccessController::allowedOrNot('billing', 'can_receive_payment');
		////// Billing-v1-changed-made-here ///////
		$invoice_number = BillingReceipt::getLatestReceiptNumber();
		////// Billing-v1-changed-made-here ///////
		return View::make($this->view.'receive-payment')
					->with('invoice_number', $invoice_number);	
	}

	/*
		IF Flat discount is selected, only 1 invoice can be processed. 
		check if flat discount and paid amount adds up to the invoice balance
		do transaction
	*/
	public function postReceivePaymentView()
	{
		AccessController::allowedOrNot('billing', 'can_receive_payment');
		$input = Input::all();
		$msg = '';
		$status = false;
		$input['amount'] = trim($input['amount']);

		$input['student_id'] = ($input['received_from'] == 'organization') ? $input['organization_id'] : $input['student_id'];

		if(!isset($input['invoice_id']))	
		{
			Session::flash('error-msg', 'No Invoice checked');
			return Redirect::back();
		}

		$invoice_table = BillingInvoice::getTableName();
		$transaction_table = BillingTransaction::getTableName();

		$invoices = DB::table($invoice_table)
						->whereIn('id', $input['invoice_id'])
						->orderBy('id', 'ASC')
						->get();

		$successfully_paid = [];
		$partially_paid = [];

		//// for receipt ////////////////////
		$total_receipt_amount_to_be_paid = 0;
		$receipt_total_amount = $input['amount'];
		$invoice_no_in_receipt_payment = [];
		///////////////////////////////////////

		/*
			IF Flat discount is selected, only 1 invoice can be processed. 
			check if flat discount and paid amount adds up to the invoice balance
			do transaction
		*/
		$input['flat_discount_amount'] = trim($input['flat_discount_amount']);
		if(strlen(trim($input['flat_discount_amount'])))
		{
			if(strlen(trim($input['flat_discount_description'])) == 0)
			{
				Session::flash('error-msg', 'Flat discount description is required');
				return Redirect::back();
			}

			if(count($invoices) > 1)
			{
				Session::flash('error-msg', 'Only 1 invoice can be selected when flat discount is provided');
				return Redirect::back();
			}

			//// billing code added here Billing-v1-changed-made-here ////
			$credit_note_sum = BillingHelperController::calculateSumOfCreditNotes($invoices[0]->id);

			$amount_to_be_paid = $invoices[0]->invoice_balance - $invoices[0]->received_amount - $input['flat_discount_amount'] - $credit_note_sum;
			//// billing code added here Billing-v1-changed-made-here ////

			$total_receipt_amount_to_be_paid += $amount_to_be_paid;

			if($amount_to_be_paid > $input['amount'])
			{
				Session::flash('error-msg', 'Warning! Flat discount amount and received amount do not add up to remaining invoice balance');
				return Redirect::back();
				//error
			}

			try
			{
				DB::connection()->getPdo()->beginTransaction();
				//Here invoices only contains 1 element
				foreach($invoices as $invoice)
				{
					$invoice_no_in_receipt_payment[] = $invoice->financial_year.'-'.$invoice->invoice_number;

					/// Billing-v1-changed-made-here ////
					$data = ['received_amount' => ($invoice->received_amount + $amount_to_be_paid), 'is_cleared' => 'yes', 'id' => $invoice->id, 'note' => $invoice->note.'\n'.$input['flat_discount_description'], 'flat_discounts' => $input['flat_discount_amount']];
					
					$this->updateInDatabase($data, [], 'BillingInvoice');
					/// Billing-v1-changed-made-here ////
					//add the remaining amount in transaction
					$successfully_paid[] = $invoice->invoice_number;
					
					BillingTransaction::recordTransaction($input['daterange'], $input['receipt_mode'], $amount_to_be_paid, $input['student_id'], $input['received_from'], $input['number'].':###:'.$input['description'], $invoice->id);

					BillingTransaction::recordTransaction($input['daterange'], 'discount', $input['flat_discount_amount'], $input['student_id'], $input['received_from'], $input['number'].':###:'.$input['flat_discount_description'], $invoice->id);

					$input['amount'] -= $amount_to_be_paid;
				}

				//////// Add code for receipt here /////////
				$invoice_no_in_receipt_payment = implode(',', $invoice_no_in_receipt_payment);
		
				$receipt_id = BillingReceipt::storeInReceiptPayment($invoice_no_in_receipt_payment, $total_receipt_amount_to_be_paid, $receipt_total_amount, $input['received_from'], $input['student_id'], $input['daterange']);

				///////////////////////////////////////////

				DB::connection()->getPdo()->commit();
			}
			catch(PDOException $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', $e->getMessage());
				return Redirect::back();
			}
		}
		else
		{	
			try
			{
				DB::connection()->getPdo()->beginTransaction();

				foreach($invoices as $invoice)
				{
					$invoice_no_in_receipt_payment[] = $invoice->financial_year.'-'.$invoice->invoice_number;
					///////// This was changed. Issue Fee not cleared when credit note cleared /////////////////////////
					$credit_note_sum = BillingHelperController::calculateSumOfCreditNotes($invoice->id);
					$amount_to_be_paid = $invoice->invoice_balance - $invoice->received_amount - $credit_note_sum;
					///////// This was changed. Issue Fee not cleared when credit note cleared /////////////////////////
					$total_receipt_amount_to_be_paid += $amount_to_be_paid;
						
					if($input['amount'] >= $amount_to_be_paid)
					{
							$data = ['received_amount' => $invoice->invoice_balance, 'is_cleared' => 'yes', 'id' => $invoice->id];
							$input['amount'] -= $amount_to_be_paid;
							
							$this->updateInDatabase($data, [], 'BillingInvoice');
							//add the remaining amount in transaction
							$successfully_paid[] = $invoice->invoice_number;
							
							BillingTransaction::recordTransaction($input['daterange'], $input['receipt_mode'], $amount_to_be_paid, $input['student_id'], $input['received_from'], $input['number'].':###:'.$input['description'], $invoice->id);
					}
					else
					{
						$data = ['received_amount' => ($invoice->received_amount + $input['amount']), 'is_cleared' => 'partial', 'id' => $invoice->id];
						
						$this->updateInDatabase($data, [], 'BillingInvoice');
						
						$partially_paid[] = $invoice->invoice_number;

						BillingTransaction::recordTransaction($input['daterange'], $input['receipt_mode'], $input['amount'], $input['student_id'], $input['received_from'], $input['number'].' : '.$input['description'], $invoice->id);
						
						$input['amount'] = 0;

						break;
					}

				}

				if(count($successfully_paid))
				{
					$status = true;

					if($input['amount'] > 0)
					{
						BillingTransaction::recordTransaction($input['daterange'], $input['receipt_mode'], $input['amount'], $input['student_id'], $input['received_from'], $input['number'].':###:'.$input['amount'].' extra paid and added to balance', end($invoices)->id);
						$msg = $input['amount'].' extra paid and added to balance';
					}
				}

				/////////// Add receipt code here ///////////
				$invoice_no_in_receipt_payment = implode(',', $invoice_no_in_receipt_payment);

				$receipt_id = BillingReceipt::storeInReceiptPayment($invoice_no_in_receipt_payment, $total_receipt_amount_to_be_paid, $receipt_total_amount, $input['received_from'], $input['student_id'], $input['daterange']);
				////////////////////////////////////////////

				DB::connection()->getPdo()->commit();
			}
			catch(Exception $e)
			{
				Session::flash('error-msg', $e->getMessage());
				return Redirect::back();
			}
		}

		if(count($successfully_paid))
		{
			$status = true;
			$msg .= implode(',', $successfully_paid).' successfully cleared';

			Session::flash('success-msg', $msg);
		}
		
		if(count($partially_paid))
		{
			$status = true;
			Session::flash('warning-msg', implode(',', $partially_paid).' partially cleared');
		}

		
		if(!$status)
		{
			Session::flash('error-msg', 'No invoice cleared');
		}
		
		if(isset($input['save_and_generate']))
		{
			$financial_year = $input['financial_year'];
			$invoice_number = $input['invoice_number'];

			$data['data'] = [];
			foreach($financial_year as $index => $val)
			{
				$invoice_id = BillingInvoice::where('invoice_number', $invoice_number[$index])
										->where('financial_year', $financial_year[$index])
										->pluck('id');

				if($invoice_id)
				{
					$data['data'][] = BillingInvoice::getInvoiceDetails($invoice_id);	
				}
			}

			////////// Add View Receipt here //////////////////////
			return Redirect::route('billing-view-receipt-from-receipt-id', $receipt_id);
						//->with('data', $data);

			////////////////////////////////////////////////////////
		}
		else
		{
			return Redirect::back();
		}
	
	}
	
	public function getDirectInvoiceOrganizationView()
	{
		AccessController::allowedOrNot('billing', 'can_view_invoice');
		$invoice_number = BillingHelperController::generateInvoiceNumber();
		return View::make($this->view.'direct-invoice-organization')
					->with('invoice_number', $invoice_number);
	}

	public function postDirectInvoiceOrganizationView()
	{
		$input = Input::all();
		
		//////////// Direct Invoice Validation Here ///////////////
		AccessController::allowedOrNot('billing', 'can_receive_payment');
		$date_type = 'AD';
		$input = Input::all();

		if((int)($input['student_id']) == 0)
		{
			Session::flash('error-msg', 'No Student Selected');
			return Redirect::back();
		}

		if(strlen($input['organization_id']) == 0)
		{
			Session::flash('error-msg', 'No Organization Selected');
			return Redirect::back();
		}

		$temp = [];
		$data = [];
		
		//calculate taxable and untaxable income
		$org_taxable = 0;
		$org_untaxable = 0;

		$student_name = StudentRegistration::where('id', $input['student_id'])->pluck('student_name').' '.StudentRegistration::where('id', $input['student_id'])->pluck('last_name');

		foreach($input['fee_type'] as $index => $fee_type)
		{

			$data['fees'][] = ['fee_title' => $fee_type, 'fee_amount' => $input['fee_amount'][$index], 'taxable' => $input['tax_applicable'][$index], 'recipient' => $student_name];
			$data['discount'] = [];
			if($input['tax_applicable'][$index] == 'yes')
			{
				$org_taxable += $input['fee_amount'][$index];
			}
			else
			{
				$org_untaxable += $input['fee_amount'][$index];
			}

		}

		if(($org_taxable + $org_untaxable) == 0)
		{
			Session::flash('error-msg', 'Invoice of 0 balance cannot be created');
			return Redirect::back();
		}

		//////////// Direct Invoice Validation Here////////////////


		///////////// Inserting Direct Invoice data here /////////////////
		$dates = BillingHelperController::getMonthAndYearInBsAndAd($input['issued_date'], $format = 'Y-m-d');
		
		$engMonth = $input['issued_date'];

		$nepMonth = new DateConverter();
		$nepaliMonth = $nepMonth->ad2bs($engMonth);
		$parts = explode('-',$nepaliMonth);
		$month = $parts[1];

		
		if($date_type == 'AD')
		{
			if($month == 1){ $mont ='Jan';}
			elseif($month == 2){$mont = 'Feb';}
			elseif($month == 3){ $mont = 'Mar';}
			elseif($month == 4){ $mont = 'Apr';}
			elseif($month == 5){ $mont = 'May';}
			elseif($month == 6){ $mont = 'Jun';}
			elseif($month == 7){ $mont = 'Jul';}
			elseif($month == 8){ $mont = 'Aug';}
			elseif($month == 9){ $mont = 'Sep';}
			elseif($month == 10){ $mont = 'Oct';}
			elseif($month == 11){ $mont = 'Nov';}
			elseif($month == 12){ $mont = 'Dec';}	
			$invoice_group_id = $dates['year_in_ad'].'-'.$mont;
		}
		else
		{
			if($month == 1){ $mont ='baishak';}
			elseif($month == 2){$mont = 'jestha';}
			elseif($month == 3){ $mont = 'ashad';}
			elseif($month == 4){ $mont = 'shrawan';}
			elseif($month == 5){ $mont = 'bhadra';}
			elseif($month == 6){ $mont = 'ashwin';}
			elseif($month == 7){ $mont = 'kartik';}
			elseif($month == 8){ $mont = 'mangsir';}
			elseif($month == 9){ $mont = 'poush';}
			elseif($month == 10){ $mont = 'magh';}
			elseif($month == 11){ $mont = 'falgun';}
			elseif($month == 12){ $mont = 'chaitra';}	
			$invoice_group_id = $dates['year_in_bs'].'-'.$mont;
		}

		$json_data = [];
		$total = 0;
		
		$organization_name = BillingDiscountOrganization::where('id', $input['organization_id'])->pluck('organization_name');
		
		$json_data['personal_details']['id'] = $input['organization_id'];
		$json_data['personal_details']['roll_number'] =0;
		$json_data['personal_details']['name'] = $organization_name;
		$json_data['personal_details']['group'] = 'organization';
		$json_data['personal_details']['class'] = 'None';
		$json_data['personal_details']['section'] = 'None';

				

		$json_data['fees'] = $data['fees'];
			
		
		$json_data['summary']['sum_without_tax'] = $org_taxable + $org_untaxable;
		$json_data['summary']['taxable_amount'] = $org_taxable;
		$json_data['summary']['untaxable_amount'] = $org_untaxable;
		$json_data['summary']['tax'] = BillingHelperController::calculateTax($json_data['summary']['taxable_amount']);
		$json_data['summary']['total'] = $json_data['summary']['sum_without_tax'] + $json_data['summary']['tax'];

		$temp = [];
		$temp['invoice_details'] = json_encode($json_data);
		$temp['invoice_number'] = BillingHelperController::generateInvoiceNumber();
		$temp['related_user_id'] = $input['organization_id'];
		$temp['related_user_group'] = 'organization';
		$temp['invoice_type'] = 'credit';
		$temp['note'] = '';
		foreach($input['note'] as $n)
		{
			$temp['note'] .= $n.'\n';
		}
		$temp['invoice_balance'] = $json_data['summary']['total'];
		$temp['issued_date'] = $input['issued_date'];
		$temp['is_final'] = 'yes'; //change this
		$temp['is_active'] = 'yes';
		$temp['is_cleared'] = isset($input['is_paid']) ? 'yes' : 'no';
		$temp['received_amount'] = isset($input['is_paid']) ? $json_data['summary']['total'] : 0;
		////// Billing-v1-changed-made-here ///////
		$temp['class_section'] = $organization_name;
		////// Billing-v1-changed-made-here ///////
		$temp['invoice_group_id'] = $invoice_group_id; //should be year-month-class - section
		$temp['financial_year'] = BillingHelperController::getFiscalYear($input['issued_date']);
		
		////// Billing-v1-changed-made-here ///////
		$temp['is_direct_invoice'] = 'yes';
		$temp['is_opening_balance'] = 'no';
		////// Billing-v1-changed-made-here ///////

		$temp['month_in_ad'] = $dates['month_in_ad'];
		$temp['month_in_bs'] = $dates['month_in_bs'];
		$temp['year_in_ad'] = $dates['year_in_ad'];
		$temp['year_in_bs'] = $dates['year_in_bs'];
		$temp['created_at'] = $temp['updated_at'] = date('Y-m-d H:i:s');
		$createdByUpdatedBy = $this->getCreatedByUpdatedBy();
		foreach($createdByUpdatedBy as $index => $val)
		{
			$temp[$index] = $val;
		}
	
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($temp);
			


			BillingTransaction::recordTransaction($temp['issued_date'], $temp['invoice_type'], $temp['invoice_balance'], $temp['related_user_id'], $temp['related_user_group'], $description = $temp['note'], $invoice_id);
			
			if($temp['is_cleared'] == 'yes')
			{
				BillingTransaction::recordTransaction($temp['issued_date'], 'cash', $temp['invoice_balance'], $temp['related_user_id'], $temp['related_user_group'], $description = $temp['note'], $invoice_id);

				$invoice_number = $temp['financial_year'].'-'.$temp['invoice_number'];

				$receipt_id = BillingReceipt::storeInReceiptPayment($invoice_number, $temp['invoice_balance'], $temp['invoice_balance'], $temp['related_user_group'], $temp['related_user_id'], $temp['issued_date']);
			}

			Session::flash('success-msg', 'Successfully created invoice');
		
			DB::connection()->getPdo()->commit();

		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();

			Session::flash('error-msg', $e->getMessage());
		}
			

		if(isset($input['is_paid']))
		{
			return Redirect::route('billing-view-receipt-from-receipt-id', $receipt_id);
		}
		else
		{
			return Redirect::back();
		}


		///////////// Inserting Direct Invoice data here /////////////////


	}

	public function getDirectInvoiceView()
	{
		AccessController::allowedOrNot('billing', 'can_view_invoice');
		$invoice_number = BillingHelperController::generateInvoiceNumber();
		return View::make($this->view.'direct-invoice')
					->with('invoice_number', $invoice_number);
	}

	public function postDirectInvoiceView()
	{
		AccessController::allowedOrNot('billing', 'can_receive_payment');
		$date_type = 'AD';
		$input = Input::all();
		
		
		if(strlen($input['student_id']) == 0)
		{
			Session::flash('error-msg', 'No Payee Selected');
			return Redirect::back();
		}

		$temp = [];
		$data = [];
		
		//calculate taxable and untaxable income
		$taxable_amount = 0;
		$untaxable_amount = 0;

		foreach($input['fee_type'] as $index => $fee_type)
		{

			$data['fees'][] = ['fee_title' => $fee_type, 'fee_amount' => $input['fee_amount'][$index], 'taxable' => $input['tax_applicable'][$index], 'recipient' => 'self'];
			$data['discount'] = [];
			if($input['tax_applicable'][$index] == 'yes')
			{
				$taxable_amount += $input['fee_amount'][$index];
			}
			else
			{
				$untaxable_amount += $input['fee_amount'][$index];
			}

		}

		if(($taxable_amount + $untaxable_amount) == 0)
		{
			Session::flash('error-msg', 'Invoice of 0 balance cannot be created');
			return Redirect::back();
		}

		$student_details = [];
		if((int) $input['student_id'])
		{
			$student_table = Student::getTableName();
			$student_registration_table = StudentRegistration::getTableName();

			$student_detail = DB::table($student_registration_table)
								->join($student_table, function ($join) use ($student_table, $student_registration_table, $input){
												            $join->on($student_table.'.student_id', '=', $student_registration_table.'.id')
												                 ->where($student_table.'.current_session_id', '=', $input['academic_session_id']);
												        })
								->where($student_registration_table.'.id', $input['student_id'])
								->select('student_name', 'current_roll_number', 'current_class_id', 'current_section_code')
								->first();//->toArray();

			//dd($student_detail);
			$student_details['current_roll_number'] = $student_detail->current_roll_number;
			$student_details['student_name'] = $student_detail->student_name;
			$student_details['related_user_group'] = 'student';
			$class = (Classes::where('id', $student_detail->current_class_id)->pluck('class_name'));
			$section = $student_detail->current_section_code;
			$student_details['class_section'] = $class.' - '.$section;
			unset($student_detail);
		}
		else
		{
			$student_details['student_name'] = $input['student_id'];
			$student_details['current_roll_number'] = 0;
			$student_details['related_user_group'] = 'other';
			$class = 'None';
			$section = 'None';
			$student_details['class_section'] = 'None - None'; 
			
		}
	
		$dates = BillingHelperController::getMonthAndYearInBsAndAd($input['issued_date'], $format = 'Y-m-d');
		$class_section = $class .' - '. $section;

		$engMonth = $input['issued_date'];

		$nepMonth = new DateConverter();
		$nepaliMonth = $nepMonth->ad2bs($engMonth);
		$parts = explode('-',$nepaliMonth);
		$month = $parts[1];

		
		if($date_type == 'AD')
		{
			if($month == 1){ $mont ='Jan';}
			elseif($month == 2){$mont = 'Feb';}
			elseif($month == 3){ $mont = 'Mar';}
			elseif($month == 4){ $mont = 'Apr';}
			elseif($month == 5){ $mont = 'May';}
			elseif($month == 6){ $mont = 'Jun';}
			elseif($month == 7){ $mont = 'Jul';}
			elseif($month == 8){ $mont = 'Aug';}
			elseif($month == 9){ $mont = 'Sep';}
			elseif($month == 10){ $mont = 'Oct';}
			elseif($month == 11){ $mont = 'Nov';}
			elseif($month == 12){ $mont = 'Dec';}	
			$invoice_group_id = $dates['year_in_ad'].'-'.$mont.'-'.$class_section;
		}
		else
		{
			if($month == 1){ $mont ='baishak';}
			elseif($month == 2){$mont = 'jestha';}
			elseif($month == 3){ $mont = 'ashad';}
			elseif($month == 4){ $mont = 'shrawan';}
			elseif($month == 5){ $mont = 'bhadra';}
			elseif($month == 6){ $mont = 'ashwin';}
			elseif($month == 7){ $mont = 'kartik';}
			elseif($month == 8){ $mont = 'mangsir';}
			elseif($month == 9){ $mont = 'poush';}
			elseif($month == 10){ $mont = 'magh';}
			elseif($month == 11){ $mont = 'falgun';}
			elseif($month == 12){ $mont = 'chaitra';}	
			$invoice_group_id = $dates['year_in_bs'].'-'.$mont.'-'.$class_section;
		}
		
		
	
			$data['personal_details']['id'] = (int) $input['student_id'];
			$data['personal_details']['roll_number'] = $student_details['current_roll_number'];
			$data['personal_details']['name'] = $student_details['student_name']; 
			$data['personal_details']['group'] = $student_details['related_user_group'];
			$data['personal_details']['class'] = $class;
			$data['personal_details']['section'] = $section;

			$tax = BillingHelperController::calculateTax($taxable_amount);
			$sum_without_tax = $taxable_amount + $untaxable_amount;
			$total = $taxable_amount + $untaxable_amount + $tax;

			$data['summary']['taxable_amount'] = $taxable_amount;
			$data['summary']['untaxable_amount'] = $untaxable_amount;
			
			$data['summary']['sum_without_tax'] = $sum_without_tax;
			$data['summary']['tax'] = $tax;
			$data['summary']['total'] = $total;
			

			$temp['invoice_details'] = json_encode($data);
			unset($data);

			$temp['invoice_number'] = BillingHelperController::generateInvoiceNumber();
			$temp['related_user_id'] = (int) $input['student_id'];
			$temp['related_user_group'] = $student_details['related_user_group'];
			$temp['invoice_type'] = 'invoice';
			$temp['invoice_balance'] = $total;
			$temp['issued_date'] = $input['issued_date'];
			$temp['is_final'] = 'yes'; //change this
			$temp['is_active'] = 'yes';
			$temp['class_section'] = $student_details['class_section']; /* TODO: incase of organization write organization name here */
			$temp['is_cleared'] = isset($input['is_paid']) ? 'yes' : 'no';
			$temp['received_amount'] = isset($input['is_paid']) ? $total : 0;
			$temp['financial_year'] = BillingHelperController::getFiscalYear($input['issued_date']);
			$temp['note'] = implode('\n', $input['note']);
			$temp['month_in_ad'] = $dates['month_in_ad'];
			$temp['month_in_bs'] = $dates['month_in_bs'];
			$temp['year_in_ad'] = $dates['year_in_ad'];
			$temp['year_in_bs'] = $dates['year_in_bs'];
			$temp['is_direct_invoice'] = 'yes';
			$temp['invoice_group_id'] = $invoice_group_id;
			$createdByUpdatedBy = $this->getCreatedByUpdatedBy();
			foreach($createdByUpdatedBy as $index => $val)
			{
				$temp[$index] = $val;
			}

			$temp['created_at'] = $temp['updated_at'] = date('Y-m-d H:i:s');

			try
			{
				DB::connection()->getPdo()->beginTransaction();

				$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($temp);
				


				BillingTransaction::recordTransaction($temp['issued_date'], $temp['invoice_type'], $temp['invoice_balance'], $temp['related_user_id'], $temp['related_user_group'], $description = $temp['note'], $invoice_id);
				
				if($temp['is_cleared'] == 'yes')
				{
					BillingTransaction::recordTransaction($temp['issued_date'], 'cash', $temp['invoice_balance'], $temp['related_user_id'], $temp['related_user_group'], $description = $temp['note'], $invoice_id);

					$invoice_number = $temp['financial_year'].'-'.$temp['invoice_number'];

					$receipt_id = BillingReceipt::storeInReceiptPayment($invoice_number, $temp['invoice_balance'], $temp['invoice_balance'], $temp['related_user_group'], $temp['related_user_id'], $temp['issued_date'],  $student_details['student_name']);
				}

				Session::flash('success-msg', 'Successfully created invoice');
			
				DB::connection()->getPdo()->commit();

			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();

				Session::flash('error-msg', $e->getMessage());
				return Redirect::back();
			}
				

		if(isset($input['is_paid']))
		{
			return Redirect::route('billing-view-receipt-from-receipt-id', $receipt_id);
		}
		else
		{
			return Redirect::back();
		}
	}
	
	public function getGenerateFeeView()
	{
		AccessController::allowedOrNot('billing', 'can_generate_fee');
		return View::make($this->view.'generate-fee');

	}

	public function postGenerateFeeView()
	{
		AccessController::allowedOrNot('billing', 'can_generate_fee');
		$input = Input::all();

		$createdByUpdatedBy = $this->getCreatedByUpdatedBy();

		$dates = BillingHelperController::getMonthAndYearInBsAndAd($input['issued_date'], $format = 'Y-m-d');
		//$dates = explode('-', $input['issued_date']);
		$class = isset($input['class_id']) ? Classes::where('id', $input['class_id'])->pluck('class_name'): 'None';
		$section = isset($input['section_id']) ? Section::where('id', $input['section_id'])->pluck('section_code'): 'None';
		$class_section = $class .' - '. $section;
			
		/* this is for nepali $invoice_group_id = $dates['year_in_bs'].'-'.$input['month'].'-'.$class_section; */
		/* this is for english $invoice_group_id = $dates['year_in_ad'].'-'.$input['month'].'-'.$class_section; */
		$invoice_group_id = $dates['year_in_ad'].'-'.$input['month'].'-'.$class_section;
		if(isset($input['save']))
		{
			if(isset($input['fees']))
				$json['data']['fees'] = $input['fees'];
			
			if(isset($input['discount']))
				$json['data']['discount'] = $input['discount'];

			if(isset($input['invoice']))
				$json['data']['invoice'] = $input['invoice'];

			$json['data']['taxable'] = $input['taxable'];
			$json['data']['untaxable'] = $input['untaxable'];
			$json['data']['sum_without_tax'] = $input['sum_without_tax'];
			$json['data']['tax'] = $input['tax'];
			$json['data']['sum'] = $input['sum'];

			$json['json_data'] = $input['json_data'];

			file_put_contents(GENERATE_FEE_LOCATION.'/'.$input['month'].'-'.$input['academic_session_id'].'-'.$input['class_id'].'-'.$input['section_id'].'.json', json_encode($json, JSON_PRETTY_PRINT));
			
			Session::flash('success-msg', 'Successfully stored');
			return Redirect::back();
		}
		elseif(isset($input['generate_invoice']))
		{

			$data_to_store = [];
			$json = [];

			try
			{
				DB::connection()->getPdo()->beginTransaction();
				

				DB::table(BillingInvoice::getTableName())
										->where('invoice_group_id', $invoice_group_id)
										->where('is_final', 'no')
										->delete();

				foreach($input['student_id'] as $index => $student_id)
				{
					$temp = [];
					$data = [];
					$json_data = [];
					//$data['invoice_details'] = $
					$json_data['personal_details']['id'] = $student_id;
					$json_data['personal_details']['roll_number'] = $input['roll_number'][$student_id];
					$json_data['personal_details']['name'] = $input['name'][$student_id];
					$json_data['personal_details']['class'] = $class;
					$json_data['personal_details']['section'] = $section;
					$json_data['personal_details']['group'] = 'student';

					
					$json_data['summary']['total'] = $input['sum'][$student_id];
					$json_data['summary']['tax'] = $input['tax'][$student_id];
					$json_data['summary']['sum_without_tax'] = $input['sum_without_tax'][$student_id];
					$json_data['summary']['taxable_amount'] = $input['taxable'][$student_id];
					$json_data['summary']['untaxable_amount'] = $input['untaxable'][$student_id];

					if(isset($input['fees']))
					{
						foreach($input['fees'][$student_id] as $fee_title => $fee)
						{
							$json_data['fees'][] = ['fee_title' => $fee_title, 'fee_amount' => $fee, 'taxable' => $input['fee_type'][$fee_title], 'recipient' => 'self'];
						}	
					}
					

					if(isset($input['discount']))
					{
						foreach($input['discount'] as $organization_name => $o)
						{
							foreach($o as $o_student_id => $discount)
							{
								if($o_student_id == $student_id)
								{
									foreach($discount as $discount_name => $f)
									{
										foreach($f as $fee_title => $discount_amount)
										{
											$temp = [];
											$temp['organization_name'] = $organization_name;
											$temp['discount_title'] = $discount_name;
											$temp['fee_title'] = $fee_title;
											$temp['discount_amount'] = $discount_amount;
											$json_data['discount'][] = $temp;
											unset($temp);
										}
									}									
								}

							}
						}	
					}
					

					$json[] = $temp['invoice_details'] = json_encode($json_data);

					$temp['invoice_number'] = BillingHelperController::generateInvoiceNumber();
					$temp['related_user_id'] = $student_id;
					$temp['class_section'] = $class_section;
					$temp['related_user_group'] = 'student';
					$temp['invoice_type'] = 'credit';
					$temp['invoice_balance'] = $input['sum'][$student_id];
					$temp['issued_date'] = $input['issued_date'];
					$temp['is_final'] = 'yes'; //change this
					$temp['is_active'] = 'yes';
					$temp['is_cleared'] = 'no'; //change this
					$temp['received_amount'] = 0; //if already balance present check this
					$temp['note'] = $input['note'][$student_id];
					$temp['is_direct_invoice'] = 'no';
					$temp['invoice_group_id'] = $invoice_group_id;
					$temp['financial_year'] = BillingHelperController::getFiscalYear($input['issued_date']);
					$temp['month_in_ad'] = $dates['month_in_ad'];
					$temp['month_in_bs'] = $dates['month_in_bs'];
					$temp['year_in_ad'] = $dates['year_in_ad'];
					$temp['year_in_bs'] = $dates['year_in_bs'];
					$temp['created_at'] = $temp['updated_at'] = date('Y-m-d H:i:s');
					
					foreach($createdByUpdatedBy as $index => $val)
					{
						$temp[$index] = $val;
					}
				
					
					$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($temp);
					if(isset($input['generate_invoice']))
					{
						BillingTransaction::recordTransaction($temp['created_at'], 'invoice', $temp['invoice_balance'], $temp['related_user_id'], 'student', $description = '', $invoice_id);	
					}
					
				}

				if(file_exists(GENERATE_FEE_LOCATION.'/'.$input['month'].'-'.$input['academic_session_id'].'-'.$input['class_id'].'-'.$input['section_id'].'.json'))
				{
					unlink(GENERATE_FEE_LOCATION.'/'.$input['month'].'-'.$input['academic_session_id'].'-'.$input['class_id'].'-'.$input['section_id'].'.json');	
				}

				///////////////////
				//for organization
				
				if(isset($input['invoice']))
				{

					foreach($input['invoice'] as $organization_id => $org)
					{
						

						foreach($org as $organization_name => $generate_invoice)
						{
							$org_taxable = 0;
							$org_untaxable = 0;

							if($generate_invoice == 'yes')
							{
								

								$json_data = [];
								$total = 0;
								foreach($input['discount'][$organization_name] as $student_id => $discounts)
								{
									$json_data['personal_details']['id'] = $organization_id;
									$json_data['personal_details']['roll_number'] =0;
									$json_data['personal_details']['name'] = $organization_name;
									$json_data['personal_details']['group'] = 'organization';
									$json_data['personal_details']['class'] = 'None';
									$json_data['personal_details']['section'] = 'None';

									foreach($discounts as $discount_name => $fees)
									{
										foreach($fees as $fee_title => $fee_amount)
										{
											if($input['fee_type'][$fee_title] == 'yes')
											{
												$org_taxable += $fee_amount;
											}
											else
											{
												$org_untaxable += $fee_amount;
											}
											

											$json_data['fees'][] = ['fee_title' => $fee_title, 'fee_amount' => $fee_amount, 'taxable' => $input['fee_type'][$fee_title], 'recipient' => $input['name'][$student_id]];
										}
									}
								}
								
								$json_data['summary']['sum_without_tax'] = $org_taxable + $org_untaxable;
								$json_data['summary']['taxable_amount'] = $org_taxable;
								$json_data['summary']['untaxable_amount'] = $org_untaxable;
								$json_data['summary']['tax'] = BillingHelperController::calculateTax($json_data['summary']['taxable_amount']);
								$json_data['summary']['total'] = $json_data['summary']['sum_without_tax'] + $json_data['summary']['tax'];

								$temp = [];
								$temp['invoice_details'] = json_encode($json_data);
								$temp['invoice_number'] = BillingHelperController::generateInvoiceNumber();
								$temp['related_user_id'] = $organization_id;
								$temp['related_user_group'] = 'organization';
								$temp['invoice_type'] = 'credit';
								$temp['invoice_balance'] = $json_data['summary']['total'];
								$temp['issued_date'] = $input['issued_date'];
								$temp['is_final'] = 'yes'; //change this
								$temp['is_active'] = 'yes';
								$temp['is_cleared'] = 'no'; //change this
								$temp['received_amount'] = 0;
								////// Billing-v1-changed-made-here ///////
								$temp['class_section'] = $organization_name;
								////// Billing-v1-changed-made-here ///////
								$temp['invoice_group_id'] = $invoice_group_id;
								$temp['financial_year'] = BillingHelperController::getFiscalYear($input['issued_date']);
								
								////// Billing-v1-changed-made-here ///////
								$temp['is_direct_invoice'] = 'no';
								$temp['is_opening_balance'] = 'no';
								////// Billing-v1-changed-made-here ///////

								$temp['month_in_ad'] = $dates['month_in_ad'];
								$temp['month_in_bs'] = $dates['month_in_bs'];
								$temp['year_in_ad'] = $dates['year_in_ad'];
								$temp['year_in_bs'] = $dates['year_in_bs'];
								$temp['created_at'] = $temp['updated_at'] = date('Y-m-d H:i:s');

								foreach($createdByUpdatedBy as $index => $val)
								{
									$temp[$index] = $val;
								}
							
								
								$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($temp);
								if(isset($input['generate_invoice']))
								{
									BillingTransaction::recordTransaction($temp['issued_date'], 'invoice', $temp['invoice_balance'], $temp['related_user_id'], $temp['related_user_group'], $description = '', $invoice_id);	
								}
								


							}
						}
					}
					
				}

				
				//////////////////
				

				DB::connection()->getPdo()->commit();
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
				die();
			}	
		}

		

		if(isset($input['generate_invoice']))
		{
			

		}
		else
		{
			
			
		}	

		Session::flash('success-msg', 'Successfully created invoice');
		return Redirect::back();			
							
	}

	public function getOpeningBalance()
	{
		AccessController::allowedOrNot('billing', 'can_set_opening_balance');
		return View::make($this->view.'opening-balance.opening-balance');
	}

	public function apiGetStudentListView()
	{
		$session_id = Input::get('session_id');
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$date = Input::get('date');

		$section_code = Section::where('id', $section_id)->pluck('section_code');

		$data = $this->apiGetStudentListData($session_id, $class_id, $section_code);

		return View::make($this->view.'opening-balance.ajax-opening-balance-list')
					->with('data', $data)
					->with('date', Carbon::createFromFormat('Y-m-d', $date))
					->with('class_id', $class_id)
					->with('section_code', $section_code);

	}

	public function apiGetStudentListData($session_id, $class_id, $section_code)
	{
		$student_table = Student::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$users_table = Users::getTableName();
		$opening_balance_table = BillingOpeningBalance::getTableName();

		$data = DB::table($student_table)
		
					->join($student_registration_table, function($join) use ($student_table, $student_registration_table, $session_id, $class_id, $section_code)
					{
						$join->on($student_registration_table.'.id', '=', $student_table.'.student_id')
							 ->where($student_table.'.current_session_id', '=', $session_id)
							 ->where('current_section_code', '=', $section_code)
							 ->where('current_class_id', '=', $class_id);
					})
					->join($users_table, function($join) use ($student_registration_table, $users_table)
					{
						$join->on('user_details_id', '=', $student_registration_table.'.id')
							->where('role', '=', 'student');
					})
					->leftJoin($opening_balance_table, function($join) use ($student_registration_table, $opening_balance_table)
					{
						$join->on($opening_balance_table.'.related_user_id', '=', $student_registration_table.'.id')
							->where($opening_balance_table.'.related_user_group', '=', 'student');
					})
					->select($student_registration_table.'.id', 'username', 'student_name','last_name', 'username', 

'opening_balance')
					->orderBy('student_name', 'ASC')
					->get();

		return $data;
	}

	public function postOpeningBalance()
	{
		AccessController::allowedOrNot('billing', 'can_set_opening_balance');
		$data = Input::all();
		$date = date('y-m-d H:i:s');
		//check if already present
		$count = BillingOpeningBalance::whereIn('related_user_id', $data['related_user_id'])
									 ->where('related_user_group', 'student')
									 ->where('is_final', 'yes')
									 ->lists('id');

		if(count($count))
		{
			Session::flash('error-msg', 'Opening Balance already final');
			return Redirect::back();
		}

		$data_to_store = [];
		$data_to_store['opening_date'] = $data['daterange'];
		try
		{
			DB::connection()->getPdo()->beginTransaction();
			BillingOpeningBalance::whereIn('related_user_id', $data['related_user_id'])
								->where('related_user_group', 'student')
								->where('is_final', 'no')
								->delete();

			foreach($data['related_user_id'] as $index => $related_user_id)
			{
				$data_to_store['related_user_id'] = $related_user_id;
				$data_to_store['related_user_group'] = 'student';
				$data_to_store['opening_balance'] = $data['opening_balance'][$index];
				$data_to_store['is_final'] = $data['save'] == 'save' ? 'no' : 'yes';
				$this->storeInDatabase($data_to_store, 'BillingOpeningBalance');
				if($data_to_store['is_final'] == 'yes')
				{
					//make invoice
					if($data['opening_balance'][$index] > 0)
					{
						$invoice_data = BillingHelperController::generateInvoiceDetails($related_user_id, 'student', $data['session'], $fees = [['fee_title' => 'opening_balance', 'fee_amount' => $data['opening_balance'][$index], 'taxable' => 'no', 'recipient' => 'self']], $discounts = [], $summary= ['total' => $data['opening_balance'][$index], 'tax' => 0, 'untaxable_amount' => $data['opening_balance'][$index], 'taxable_amount' => 0, 'sum_without_tax' => $data['opening_balance'][$index]], $data_to_store['opening_date'], 'invoice', $data['opening_balance'][$index], 'yes', 'no', 0, 'Opening Balance of '.$data['opening_balance'][$index], $name = '', 'no', 'yes');	

							$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($invoice_data);
					}
					elseif($data['opening_balance'][$index] < 0)
					{
						$invoice_data = BillingHelperController::generateInvoiceDetails($related_user_id, 'student', $data['session'], $fees = [], $discounts = [], $summary= ['total' => 0, 'tax' => 0, 'untaxable_amount' => 0, 'taxable_amount' => 0, 'sum_without_tax' => 0], $data_to_store['opening_date'], 'invoice', 0, 'yes', 'yes', 0, 'Opening Balance of '.$data['opening_balance'][$index], $name = '', 'no', 'yes');

						$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($invoice_data);	
					}

					if($data['opening_balance'][$index] > 0)
					{
						BillingTransaction::recordTransaction($data['daterange'], 'invoice', $data_to_store['opening_balance'], $related_user_id, 'student', 'opening balance', $invoice_id);	
					}
					elseif($data['opening_balance'][$index] < 0)
					{
						BillingTransaction::recordTransaction($data['daterange'], 'prev_balance', (-1 * $data_to_store['opening_balance']), $related_user_id, 'student', 'opening balance', $invoice_id);
					}
					
				}
			}
			Session::flash('success-msg', 'Successfully saved');


			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back()
						->withInput();
		
		
	}

	public function showInvoiceFromInvoiceNumber($invoice_number)
	{
		AccessController::allowedOrNot('billing', 'can_view_invoice');
		$financial_year = Input::get('financial_year', '');
		$invoice_id = BillingInvoice::where('invoice_number', $invoice_number)
									->where('financial_year', $financial_year)
									->pluck('id');

		if($invoice_id)
		{
			$data['data'] = BillingInvoice::getInvoiceDetails($invoice_id);	
		}
		else
		{
			$data['data'] = [];
		}			


		return View::make($this->view.'show-invoice')
					->with('data', $data);
		
	}

	public function showInvoiceFromTransactionNumber($transaction_number)
	{
		AccessController::allowedOrNot('billing', 'can_view_invoice');
		$invoice_id = BillingTransaction::where('transaction_no', $transaction_number)
									->pluck('related_invoice_id');

		if($invoice_id)
		{
			$data['data'] = BillingInvoice::getInvoiceDetails($invoice_id);	
		}
		else
		{
			$data['data'] = [];
		}

		

		return View::make($this->view.'show-invoice')
					->with('data', $data);
		
	}	

	public function generateInvoicePdf()
	{

	}

	public function getStatementView()
	{
		AccessController::allowedOrNot('billing', 'can_view_statement');
		return View::make($this->view.'statement');
	}

	public function apiGetStatementListData($date_range, $student_id)
	{
		$dates = BillingHelperController::getDateRange($date_range);

		$transaction_table = BillingTransaction::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		

		$data = DB::table($transaction_table)
								//->join($student_registration_table, $student_registration_table.'.id', '=', $transaction_table.'.related_user_id')
								->where('transaction_date', '>=', $dates[0])
								->where('transaction_date', '<=', $dates[1])
								->where('related_user_id', $student_id)
								->where('related_user_group', 'student')
								->select($transaction_table.'.*')
								->orderBy('id', 'ASC')
								->get();


		$opening_balance = (float) DB::table($transaction_table)
								->where('transaction_date', '<', $dates[0])
								->orderBy('id', 'DESC')
								->where('related_user_id', $student_id)
								->take(1)
								->pluck('balance_amount');

		return ['data' => $data, 'opening_balance' => $opening_balance, 'dates' =>$dates];

	}

	public function apiGetStatementListView()
	{
		$date_range = Input::get('date_range');
		$student_id = Input::get('student_id');
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$session_id = Input::get('session_id');

		$data = $this->apiGetStatementListData($date_range, $student_id);
		return View::make($this->view.'partials.partial-statement')
					->with('data', $data)
					->with('class_id', $class_id)
					->with('section_id', $section_id)
					->with('session_id', $session_id)
					->with('student_id', $student_id)
					->with('date_range', $date_range);
	}

	public function getTransactionListView()
	{
		AccessController::allowedOrNot('billing', 'can_view_transaction');
		return View::make($this->view.'transaction-list');
	}

	public function apiGetTransactionListData($date_range)
	{
		$dates = BillingHelperController::getDateRange($date_range);

		$transaction_table = BillingTransaction::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$data = DB::table($transaction_table)
								//->join($student_registration_table, $student_registration_table.'.id', '=', $transaction_table.'.related_user_id')
								->where('transaction_date', '>=', $dates[0])
								->where('transaction_date', '<=', $dates[1])
								//->where('related_user_id', $student_id)
								//->where('related_user_group', 'student')
								->select($transaction_table.'.*')
								->orderBy('transaction_date', 'ASC')
								->orderBy('transaction_no', 'ASC')
								->get();

		return $data;
	}

	public function apiGetTransactionListView()
	{
		$date_range = Input::get('date_range');
		$data = $this->apiGetTransactionListData($date_range);

		return View::make($this->view.'.partials.partial-transaction')
					->with('data', $data)
					/* Billing-v1-changed-made-here */
					->with('date_range', $date_range);
					/* Billing-v1-changed-made-here */
	}

	public function getInvoiceListView()
	{

		AccessController::allowedOrNot($this->module_name, 'can_view_invoice_list');
		
		$columns = array(
									array
									(
										'column_name' 	=> 'invoice_number',
										'alias'			=> 'Invoice Number'
									),
									array
									(
										'column_name' 	=> 'related_user_group',
										'alias'			=> 'Group'
									),
									///////// billing-cancel-v1-changes /////////
									['column_name'	=>	'invoice_group_id',
									 'alias'		=>	'Invoice Group'],
									///////// billing-cancel-v1-changes /////////
									//TODO HERE
									array
									(
										'column_name' 	=> 'invoice_details',
										'alias'			=> 'Invoice Against'
									),
									array
									(
										'column_name' 	=> 'invoice_balance',
										'alias'			=> 'Invoice Balance'
									),
									array
									(
										'column_name' 	=> 'received_amount',
										'alias'			=> 'Received Amount'
									),
									['column_name' => 'is_cleared',
									 'alias'	=>	'Is Paid'],

									['column_name'	=>	'created_at',
									 'alias'		=>	'Issued at'],



								 );
		$columnsToShow = $this->getSearchColumns($columns);
		$tableHeaders = $this->getTableHeader($columns);


		$model = new BillingInvoice;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'invoice-list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $columnsToShow)
					->with('tableHeaders', $tableHeaders)
					//->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					->with('role', $this->role);

	
	}

	public function ajaxGetEditFeeView($id)
	{
		return View::make($this->view.'partials.ajax-edit-fee')
					->with('id', $id);
	}

	public function getCreditNoteView($invoice_id)
	{
		AccessController::allowedOrNot('billing', 'can_create_credit_note');
		$data = BillingInvoice::where('id', $invoice_id)->first();
		$invoice_number = BillingHelperController::generateInvoiceNumber();
		return View::make($this->view.'credit-note.create')
					->with('data', $data)
					->with('invoice_number', $invoice_number)
					->with('invoice_id', $invoice_id);
	}

	////// Billing-v1-changed-made-here /////////
	public function postCreditNoteView($invoice_id)
	{
		AccessController::allowedOrNot('billing', 'can_create_credit_note');
		$input = Input::all();
	
		$invoice = BillingInvoice::where('id', $invoice_id)->first();
		$invoice_details = json_decode($invoice->invoice_details, true);
		
		$json_data = [];
		$json_data['personal_details'] = $invoice_details['personal_details'];
		if($input['tax_included'] == 'yes')
		{
			$tax = BillingHelperController::calculateTax($input['amount']);
		}
		else
		{
			$tax = 0;
		}


		$json_data['fees'][] = ['fee_title' => 'Credit Note', 'fee_amount' => $input['amount'], 'taxable' => $input['tax_included'], 'recipient' => $invoice_details['personal_details']['name']];
		
		
		$json_data['summary']['total'] = $input['amount'] + $tax;
		$json_data['summary']['tax'] = $tax;
		$json_data['summary']['sum_without_tax'] = $input['amount'];
		$json_data['summary']['taxable_amount'] = ($tax) ? $input['amount'] : 0;
		$json_data['summary']['untaxable_amount'] = ($tax) ? 0 : $input['amount'];

		$date = date('Y-m-d');
		$dates = BillingHelperController::getMonthAndYearInBsAndAd($date, $format = 'Y-m-d');
		$temp = [];
		$temp['invoice_details'] = json_encode($json_data);
		$temp['invoice_number'] = BillingHelperController::generateInvoiceNumber();
		$temp['related_user_id'] = $invoice->related_user_id;
		$temp['related_user_group'] = $invoice->related_user_group;
		$temp['invoice_type'] = 'credit_note';
		$temp['invoice_balance'] = $json_data['summary']['total'];
		$temp['related_invoice_id'] = $invoice_id;
		$temp['note'] = $input['description'];
		$temp['issued_date'] = $date;
		$temp['is_final'] = 'yes'; //change this
		$temp['is_active'] = 'yes';
		$temp['is_cleared'] = 'yes'; //change this
		$temp['received_amount'] = $json_data['summary']['total'];
		
		$temp['class_section'] = $invoice->related_user_group == 'organization' ? $invoice_details['personal_details']['name'] : $input['class'].' - '.$input['section'];

		$temp['invoice_group_id'] = $invoice->invoice_group_id;
		
		$temp['month_in_ad'] = $dates['month_in_ad'];
		$temp['month_in_bs'] = $dates['month_in_bs'];
		$temp['year_in_ad'] = $dates['year_in_ad'];
		$temp['year_in_bs'] = $dates['year_in_bs'];
		$temp['created_at'] = date('Y-m-d H:i:s');
		$temp['updated_at'] = $temp['created_at'];
		$temp['is_direct_invoice'] = 'no';
		$temp['is_opening_balance'] = 'no';		
		$temp['financial_year'] = BillingHelperController::getFiscalYear($date);

		$created_by_updated_by = $this->getCreatedByUpdatedBy($update = false);
		$temp = array_merge($temp, $created_by_updated_by);

		if($input['previous_credit_note_balance'] + $temp['invoice_balance'] > ($invoice->invoice_balance - $invoice->received_amount))
		{
			//// billing code added here ////
			Session::flash('error-msg', 'Invoice amount exceeded by credit note. Credit Note amount = '.$input['previous_credit_note_balance'].' and Invoice Balance = '.$input['invoice_total'].' and Received amount = '.$invoice->received_amount);
			return Redirect::back();
			//// billing code added here ////
		}
						
		try 
		{
			DB::connection()->getPdo()->beginTransaction();

			//// billing code added here ////
			$invoice->note .= "\n" . $input['description'];
			
			if($input['previous_credit_note_balance'] + $temp['invoice_balance'] == ($invoice->invoice_balance - $invoice->received_amount))
			{
				$invoice->is_cleared = 'yes';
			}

			$invoice->save();
			//// billing code added here ////

			$invoice_id = DB::table(BillingInvoice::getTableName())->insertGetId($temp);

			BillingTransaction::recordTransaction($temp['issued_date'], 'credit_note', $temp['invoice_balance'], $temp['related_user_id'], $temp['related_user_group'], $input['description'], $invoice_id);	

			DB::connection()->getPdo()->commit();

			Session::flash('success-msg', 'Successfully created');
											
		} 
		catch (Exception $e) 
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
		}								

		return Redirect::back();
	}
	////// Billing-v1-changed-made-here /////////



	//////////////////// These are for apis ///////////////////////////////////////////////////////
	
	public function apiPostCreateFee($data_to_store)
	{
				
			
		
	}



	////////////////////////////////////////////////////////////////////////////////////////////////
	/// These are helper controllers ///////////////////////////////

	public function ajaxGetClassList()
	{

		$session_id = Input::get('academic_session_id', '0');

		$classes = Classes::where('academic_session_id', $session_id)
							->orderBy('sort_order', 'ASC')
							->lists('class_name', 'id');

		$html = '';
		$extra = Input::get('extra', 'all');
		
		if($extra == 'all')
		{
			$extra = ['all' => 'All'];
		}
		elseif(strlen($extra))
		{
			$extra = [0 => $extra];
		}
		else
		{
			$extra = ['0' => '-- Select --'];
		}
		

		foreach($classes as $index => $c)
		{
			$extra[$index] = $c;
		}

		$html = HelperController::generateStaticSelectList($extra, 'class_id', 0, '');

		return $html;

	}

	public function ajaxGetSectionList()
	{
		$class_id = Input::get('class_id', 0);

		$section_table = Section::getTableName();
		$class_section_table = ClassSection::getTableName();
		

		$sections = DB::table($class_section_table) 
						->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
						->where($class_section_table.'.class_id', '=', $class_id)
						->select($section_table.'.section_code', $section_table.'.id')
						->lists('section_code', 'id');

		$extra = Input::get('extra', 'all');
				if($extra == 'all')
				{
					$extra = ['all' => 'All'];
				}
				elseif(strlen($extra))
				{
					$extra = ['0' => $extra];
				}
				else
				{
					$extra = ['0' => '-- Select --'];
				}
		

		$html = '';
		foreach($sections as $index => $c)
		{
			$extra[$index] = $c;
		}

		$html = HelperController::generateStaticSelectList($extra, 'section_id', 0, '');

		return $html;

	}

	public function ajaxGetStudentList()
	{
		$count = 1;
		$message = '';

		$students = $this->ajaxGetStudentListData();
		
		
		$count = count($students);
		if($count == 0)
		{
			$message = 'No students found';
		}
		

		return View::make($this->view.'partials.ajax-student-list')
					->with('students', $students)
					->with('count', $count)
					->with('message', $message)
					->with('class_id', Input::get('class_id', 0))
					->with('section_id', Input::get('section_id', 0));
	}

	///// Billing-v1-changed-made-here ///////
	public function ajaxGetStudentRemainingDueView()
	{
		$student_id = Input::get('student_id');
		$type = Input::get('type', 'student');

		$dues = BillingInvoice::where('is_final', 'yes')
								->where('is_active', 'yes')
								->where('is_cleared', '!=', 'yes')
								->where('related_user_id', $student_id)
								->where('related_user_group', $type)
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->select('id', 'invoice_number', 'year_in_ad', 'year_in_bs', 'month_in_ad', 'month_in_bs', 'invoice_balance', 'received_amount', 'financial_year');
		
		//// billing code added here ////
		$dues_ids = $dues->lists('id');
		$credit_notes = BillingInvoice::where('invoice_type', 'credit_note')
										->whereIn('related_invoice_id', $dues_ids)
										->select('invoice_balance', 'related_invoice_id as id')
										->get();
		$credit_note_data = [];
		foreach($credit_notes as $index => $credit_note)
		{
			if(isset($credit_note_data[$credit_note->id]))
			{
				$credit_note_data[$credit_note->id] += $credit_note->invoice_balance;
			}
			else
			{
				$credit_note_data[$credit_note->id] = $credit_note->invoice_balance;
			}
		}

		unset($credit_notes);
		unset($dues_ids);

		$dues = $dues->get();
		//// billing code added here ////

		return View::make($this->view.'partials.ajax-student-remaining-due-view')
						->with('dues', $dues)
						->with('credit_note_data', $credit_note_data)
						->with('student_id', $student_id)
						->with('type', $type);
	}
	////////// Billing-v1-changed-made-here ///////////

	public function ajaxGetStudentSelectList()
	{
		$students = $this->ajaxGetStudentListData();
		$list = '';

		if(count($students))
		{
			$student_list = ['0' => '-- select students --'];
			foreach($students as $student)
			{
				$student_list[$student->id] = '( '.$student->current_roll_number.' )'.$student->student_name.' '.$student->last_name.' ('.

$student->username.' )';

			}	

		
			$list = HelperController::generateStaticSelectList($student_list, 'student_id', 0, '');
		}

		return $list;
		
	}

	public function ajaxGetStudentListData($session_id = NULL, $class_id = NULL, $section_id = NULL)
	{
		$session_id = is_null($session_id) ? Input::get('academic_session_id', 0) : $session_id;
		$class_id = is_null($class_id) ? Input::get('class_id', 0) : $class_id;
		$section_id = is_null($section_id) ? Input::get('section_id', 0) : $section_id;

		$students_table = Student::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$users_table = Users::getTableName();

		$students = DB::table($students_table)
						->join($student_registration_table, $student_registration_table.'.id', '=', $students_table.'.student_id')
						//->join($users_table, $users_table.'.user_details_id', '=', $student_registration_table.'.id')
						->join($users_table, function ($join) use ($users_table, $student_registration_table){
				            $join->on($users_table.'.user_details_id', '=', $student_registration_table.'.id')
				                 ->where($users_table.'.role', '=', 'student');
				        })
				        ->where('current_session_id', $session_id)
						
						->where('current_section_code', HelperController::pluckFieldFromId('Section', 'section_code', $section_id))
						->where('current_class_id', $class_id)
						->select('username', $student_registration_table.'.id', $student_registration_table.'.student_name', 

'current_roll_number',$student_registration_table.'.last_name')

						->orderBy('current_roll_number', 'ASC')
						->get();

		return $students;
	}

	public function ajaxGetStudentFeeList($class_id, $section_id, $month)
	{
		//i am here
		$config = json_decode(File::get(app_path().'/modules/billing/config/config.json'));

		$student_fee_table = BillingFeeStudent::getTableName();
		$fee_table = BillingFee::getTableName();
		$section_code = Section::where('id', $section_id)->pluck('section_code');
		$discount_details_table = BillingDiscountDetails::getTableName();
		$discount_table = BillingDiscount::getTableName();
		$discount_organization_table = BillingDiscountOrganization::getTableName();

		$transportation_table = Transportation::getTableName();

		$fees = DB::table($student_fee_table)
				->join($fee_table, $fee_table.'.id', '=', $student_fee_table.'.fee_id')
				->select('fee_category', 'fee_amount', 'tax_applicable', $fee_table.'.id as fee_id')
				->where(function($query) use ($class_id)
				{
					$query->where('class_id', $class_id);
						
				})
				->where(function($query) use ($section_id)
				{
					$query->where('section_id', $section_id);	
						
				})
				->where(function($query) use ($month)
				{
					$query->where('fee_type', $month)	
						->orWhere('fee_type', 'recurring');
				})
				->get();

		$fees_amount = [];
		foreach($fees as $f)
		{
			$fees_amount[$f->fee_id] = $f->fee_amount;
		}


		$students_table = Student::getTableName();
		$student_registration_table = StudentRegistration::getTableName();

		$students = DB::table($students_table)
					->join($student_registration_table, function ($join) use ($student_registration_table, $class_id, $section_code, $students_table){
			            $join->on($student_registration_table.'.id', '=', $students_table.'.student_id')
			                 ->where($students_table.'.current_class_id', '=', $class_id)
			                 ->where($students_table.'.current_section_code', '=', $section_code);
			        })
			       ->select('student_name', $student_registration_table.'.id',

$student_registration_table.'.last_name', 'current_roll_number')
			        ->orderBy('current_roll_number', 'ASC')
			     	->get();



		$response_students = [];
		foreach($students as $index => $s)
		{
			$response_students[$s->id]['name'] = $s->student_name;
			$response_students[$s->id]['last_name'] = $s->last_name;
			$response_students[$s->id]['roll'] = $s->current_roll_number;
			unset($students[$index]);
		}

		$extra_fees = [];
		$extra_fees = BillingExtraFees::whereIn('student_id', array_keys($response_students))
									  ->select('student_id', 'fee_id', 'fee_amount')
									  ->get();

		$related_extra_fees_students = [];
		foreach($extra_fees as $index => $e)
		{
			$related_extra_fees_students[$e->student_id][$e->fee_id] = $e->fee_amount;
			unset($extra_fees[$index]);
		}
		unset($extra_fees);

		$transportation_fees = TransportationStudent::whereIn('student_id', 												array_keys($response_students										))
													->lists('fee_amount', 'student_id');

		
		$late_fee_date = Carbon::now()->subDays($config->late_fee_days)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
		
		$late_fees = DB::table(BillingInvoice::getTableName())
						->select(DB::raw("SUM(invoice_balance - received_amount) as remaining_balance, related_user_id"))
						->where('related_user_group', 'student')
						->where('is_cleared', '!=', 'yes')
						->where('invoice_type', '=', 'credit')
						->where('issued_date', '<=', $late_fee_date)
						->whereIn('related_user_id', array_keys($response_students))
						->groupBy('related_user_id') 
						->lists('remaining_balance', 'related_user_id');


		$discounts = DB::table($discount_details_table)
						->join($discount_table, $discount_table.'.id', '=', $discount_details_table.'.discount_id')
						->join($discount_organization_table, $discount_organization_table.'.id', '=', $discount_table.'.organization_id')
						->join($fee_table, $fee_table.'.id', '=', $discount_details_table.'.fee_id')
						->whereIn('student_id', array_keys($response_students))
						->whereIn($fee_table.'.id', array_keys($fees_amount))
						->where($discount_table.'.is_active', 'yes')
						->select($discount_organization_table.'.id as organization_id', 'organization_name','fee_category', 'discount_id', 'fee_id', 'student_id', 'discount_percent', 'discount_name', 'generate_invoice', 'tax_applicable')
						->get();


		$discount_fees = [];
		$discount_students = [];
		foreach($discounts as $index => $d)
		{
			$discount_fees[$d->organization_id]['organization_name'] = $d->organization_name;
			$discount_fees[$d->organization_id]['generate_invoice'] = $d->generate_invoice;
			$discount_fees[$d->organization_id]['discounts'][$d->discount_id]['fees'][$d->fee_id] = $d->fee_category;
			$discount_fees[$d->organization_id]['discounts'][$d->discount_id]['discount_name'] = $d->discount_name;
			$discount_fees[$d->organization_id]['discounts'][$d->discount_id][$d->fee_id]['tax_applicable'] = $d->tax_applicable;
			$discount_students[$d->student_id][$d->organization_id][$d->discount_id][$d->fee_id] = $d->discount_percent;
			unset($discounts[$index]);
		}



		
		return ['students' => $response_students, 'fees' => $fees, 'fees_amount' => $fees_amount, 'transportation_fees' => $transportation_fees, 'related_extra_fees_students' => $related_extra_fees_students, 'late_fees' => $late_fees, 'config' => $config, 'discounts' => ['discount_fees' => $discount_fees, 'discount_students' => $discount_students]];

	}
	public function getFeePrint()
	{
		AccessController::allowedOrNot('billing', 'can_print_fee');
		return View::make($this->view.'fee-print')->with('module_name', $this->module_name);

	}

	public function getFeePrintList()
	{	
		AccessController::allowedOrNot('billing', 'can_print_fee');
		$student_id = Input::get('student_id'); //student id prithbi= 38
		$class_id = Input::get('class_id');
		$section_id = Input::get('section_id');
		$session_id = Input::get('academic_session_id'); //shows id of 2073 = 4
		
		$return_data = $this->getFeePrintListData($session_id, $class_id, $section_id, $student_id);
		
		return View::make($this->view.'fee-print-ajax')
					->with('return_data', $return_data);
	}

	public function getFeePrintListData($session_id, $class_id, $section_id, $student_id)
	{

		$engMonth = date('Y-m-d');
		$nepMonth = new DateConverter();
		$nepaliMonth = $nepMonth->ad2bs($engMonth);
		$parts = explode('-',$nepaliMonth);
		$year = $parts[0];
		$month = $parts[1];

		$section_code = DB::table('sections')
							->select('id', 'section_code')
							->where('id', '=', $section_id)
							->first();
		if($student_id == 'all'){
		$student_details = [];
		
		$student_table = Student::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$student_detail = DB::table($student_registration_table)
							->join($student_table, function ($join) use ($student_table, $student_registration_table,$class_id, $section_id, $session_id, $section_code){
				            $join->on($student_table.'.student_id', '=', $student_registration_table.'.id')
				                 ->where($student_table.'.current_session_id', '=', $session_id)
				                 ->where($student_table.'.current_class_id','=', $class_id)
				                 ->where($student_table.'.current_section_code', '=', $section_code->section_code);
											        })
							->select($student_table.'.student_id',$student_registration_table.'.id',$student_table.'.current_session_id',$student_table.'.current_class_id',$student_table.'.current_section_code')
							->lists('id');
		
		$data['data'] = DB::table(StudentGuardianRelation::getTableName())
							  ->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.student_id')
							  ->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
							  ->join(Guardian::getTableName(),Guardian::getTableName().'.id', '=', 'guardian_id')
							  ->where(Users::getTableName().'.role', 'student')
						 	->join(BillingInvoice::getTableName(), BillingInvoice::getTableName().'.related_user_id', '=', StudentRegistration::getTableName().'.id')
						 	->select(Users::getTableName().'.username',Guardian::getTableName().'.guardian_name', BillingInvoice::getTableName().'.*', StudentRegistration::getTableName().'.current_address')
							->where(BillingInvoice::getTableName().'.is_cleared', '<>','yes')
							///////// billing-cancel-v1-changes /////////
							->where('is_cleared', '!=', 'cancel')
							///////// billing-cancel-v1-changes /////////
							->where(BillingInvoice::getTableName().'.related_user_group', 'student')
							->whereIn(BillingInvoice::getTableName().'.related_user_id',$student_detail)
							->where(BillingInvoice::getTableName().'.year_in_bs', $year)
							->where(BillingInvoice::getTableName().'.month_in_bs', '=', $month)
							->where('is_direct_invoice', 'no')
							->where('is_opening_balance', 'no')
							->orderBy(BillingInvoice::getTableName().'.id', 'DESC')
							->get();
		
					
		$previous_month['data'] = DB::table(BillingInvoice::getTableName())->where('is_cleared', '<>','yes')
								->where('related_user_group', 'student')
								->whereIn('related_user_id', $student_detail)
								->where(function($query) use ($month, $year)
						{
							$query->where(function($query) use ($month, $year)
							   {
								$query->where(BillingInvoice::getTableName().'.month_in_bs', '<', $month )
								->where(BillingInvoice::getTableName().'.year_in_bs','=', $year);
							   })
								->orWhere(BillingInvoice::getTableName().'.year_in_bs', '<', $year);
						})
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->orderBy('id', 'DESC')
								->get();

		
		}
		else{	
		$data['data'] = DB::table(StudentGuardianRelation::getTableName())
							  ->join(StudentRegistration::getTableName(), StudentRegistration::getTableName().'.id', '=', StudentGuardianRelation::getTableName().'.student_id')
							  ->join(Users::getTableName(), Users::getTableName().'.user_details_id', '=', StudentRegistration::getTableName().'.id')
							  ->join(Guardian::getTableName(),Guardian::getTableName().'.id', '=', 'guardian_id')
							  ->where(Users::getTableName().'.role', 'student')
						 	->join(BillingInvoice::getTableName(), BillingInvoice::getTableName().'.related_user_id', '=', StudentRegistration::getTableName().'.id')
						 	->select(Users::getTableName().'.username',Guardian::getTableName().'.guardian_name', BillingInvoice::getTableName().'.*', StudentRegistration::getTableName().'.current_address')
							->where(BillingInvoice::getTableName().'.is_cleared', '<>','yes')
							->where(BillingInvoice::getTableName().'.related_user_group', 'student')
							->where(BillingInvoice::getTableName().'.related_user_id',$student_id)
							->where(BillingInvoice::getTableName().'.year_in_bs', $year)
							->where(BillingInvoice::getTableName().'.month_in_bs', '=', $month)
							->where('is_direct_invoice', 'no')
							->where('is_opening_balance', 'no')
							///////// billing-cancel-v1-changes /////////
							->where('is_cleared', '!=', 'cancel')
							///////// billing-cancel-v1-changes /////////
							->orderBy(BillingInvoice::getTableName().'.id', 'DESC')
							->get();
		

		$previous_month['data'] = DB::table(BillingInvoice::getTableName())->where('is_cleared', '<>','yes')
								->where('related_user_group', 'student')
								->where('related_user_id', $student_id)
								->where(function($query) use ($month, $year)
						{
							$query->where(function($query) use ($month, $year)
							   {
								$query->where(BillingInvoice::getTableName().'.month_in_bs', '<', $month )
								->where(BillingInvoice::getTableName().'.year_in_bs','=', $year);
							   })
								->orWhere(BillingInvoice::getTableName().'.year_in_bs', '<', $year);
						})
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->orderBy('id', 'DESC')
								->get();
			
			}
		
		$return_data = [];
		foreach($data['data'] as $d)
		{	if(isset($return_data[$d->related_user_id]['current_month'][$d->id]))
			{
				$return_data[$d->related_user_id]['current_month'][$d->id]->guardian_name .= ' , '. $d->guardian_name  ; 
			}
			else
			{
				$return_data[$d->related_user_id]['current_month'][$d->id] = $d;
			}
		}

		foreach($previous_month['data'] as $d)
		{	
			$return_data[$d->related_user_id]['previous_month'][] = $d;
		}

		// echo '<pre>';
		// print_r($return_data);
		// die();
		return $return_data;
	}
	
	public function getListViewFeePrint()
	{
		AccessController::allowedOrNot($this->module_name, 'can_print_fee');
		return View::make($this->view.'list-view-fee-print')->with('module_name', $this->module_name);
	}
	
	public function getListViewFeePrintList()
	{
		AccessController::allowedOrNot('billing', 'can_print_fee');
		$selected_month = Input::get('nepDate', '0-0'); 
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$session_id = Input::get('academic_session_id', 0); //shows id of 2073 = 4
		$student_id = Input::get('student_id', 0);
		
		$class = Classes::where('id', $class_id)->first()->class_name;
		$section = Section::where('id', $section_id)->first()->section_code;
		$parts = explode('-',$selected_month);
		$year = $parts[0];
		$month = $parts[1];
		$return_data = $this->getListViewFeePrintListData($session_id, $class_id, $section_id, $selected_month, $student_id);
		
		return View::make($this->view.'list-view-fee-print-ajax')
					->with('block_data', $return_data)
					->with('class', $class)
					->with('section', $section)
					->with('year', $year)
					->with('month', HelperController::getNepaliMonth($month))
					->with('month_index', $month)
					->with('issued_date', $selected_month);
	}

	public function getListViewFeePrintListData($session_id, $class_id, $section_id, $selected_month, $student_id = 0){

		$parts = explode('-',$selected_month);
		$year = $parts[0];
		$month = $parts[1];
		$section_code = DB::table('sections')
							->select('id', 'section_code')
							->where('id', '=', $section_id)
							->first();

		$student_details = [];

		//$student_details

		$student_guardian_relation_table = StudentGuardianRelation::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$users_table = Users::getTableName();
		$guardian_table = Guardian::getTableName();
		$billing_invoice_table = BillingInvoice::getTableName();
		$student_table = Student::getTableName();


		$temp_student_details = DB::table($student_registration_table)
							->join($users_table, $users_table.'.user_details_id', '=', $student_registration_table.'.id')
							->join($student_table, $student_table.'.student_id', '=', $student_registration_table.'.id')
							->leftJoin($student_guardian_relation_table, $student_guardian_relation_table.'.student_id', '=', $student_registration_table.'.id')
							->leftJoin($guardian_table, $guardian_table.'.id', '=', $student_guardian_relation_table.'.guardian_id')
							->select($users_table.'.username',$guardian_table.'.guardian_name', $student_registration_table.'.current_address', $student_registration_table.'.student_name', $student_registration_table.'.last_name', $student_registration_table.'.id', 'current_roll_number')
							->where('role', 'student')
							->where('current_session_id', '=', $session_id)
							->where('current_class_id', '=',  $class_id)
							->where('current_section_code', '=',  $section_code->section_code);

		if($student_id)
		{
			$temp_student_details = $temp_student_details->where($student_registration_table.'.id', $student_id);
		}
		
		$temp_student_details = $temp_student_details->get();

		$student_details = [];
		foreach($temp_student_details as $index => $t)
		{
			if(!isset($student_details[$t->id]))
			{
				$student_details[$t->id] = $t;
			}	
			else
			{
				$student_details[$t->id]->guardian_name .= ','.$t->guardian_name;
			}
		}

		$student_ids = array_keys($student_details);

		unset($temp_student_details);
		
		
		$temp_current_month_bills = DB::table($billing_invoice_table)
							  ->where($billing_invoice_table.'.is_cleared', '<>','yes')
							->where($billing_invoice_table.'.related_user_group', 'student')
							->whereIn($billing_invoice_table.'.related_user_id',$student_ids)
							->where($billing_invoice_table.'.year_in_ad', $year)
							->where($billing_invoice_table.'.month_in_ad', '=', $month)
							///////// billing-cancel-v1-changes /////////
							->where('is_cleared', '!=', 'cancel')
							///////// billing-cancel-v1-changes /////////
							//->where('is_direct_invoice', 'yes')
							//->where('is_opening_balance', 'no')
							->orderBy($billing_invoice_table.'.id', 'DESC')
							->get();

		$current_month_bills = [];

		foreach($temp_current_month_bills as $t)
		{
			$credit_note_sum = BillingHelperController::calculateSumOfCreditNotes($t->id);
			$t->invoice_balance -= $credit_note_sum;
			$current_month_bills[$t->related_user_id][] = $t->invoice_balance;
		}

		$temp_previous_month_bills = DB::table($billing_invoice_table)
									    ->where($billing_invoice_table.'.is_cleared', '<>','yes')
									    ///////// billing-cancel-v1-changes /////////
										->where('is_cleared', '!=', 'cancel')
										///////// billing-cancel-v1-changes /////////
									    ->whereIn('related_user_id', $student_ids)
									    ->where($billing_invoice_table.'.related_user_group', 'student')
										->where(function($query) use ($month, $year, $billing_invoice_table)
										{
											$query->where(function($query) use ($month, $year, $billing_invoice_table)
											   {
												$query->where($billing_invoice_table.'.month_in_ad', '<', $month )
												->where($billing_invoice_table.'.year_in_ad','=', $year);
											   })
												->orWhere($billing_invoice_table.'.year_in_ad', '<', $year);
										})
										->orderBy('id', 'DESC')
										->get();

		$previous_month_bills = [];
		foreach($temp_previous_month_bills as $t)
		{
			$credit_note_sum = BillingHelperController::calculateSumOfCreditNotes($t->id);
			$t->invoice_balance -= $credit_note_sum;
			$previous_month_bills[$t->related_user_id][] = $t->invoice_balance;
		}

		$return_data = [];
		foreach($student_details as $student_id => $student_detail)
		{
			if((isset($current_month_bills[$student_id]) && count($current_month_bills[$student_id])) || (isset($previous_month_bills[$student_id]) && count($previous_month_bills[$student_id])))
			{
				$temp = [];
				$temp['student_detail'] = $student_detail;
				if(isset($current_month_bills[$student_id]))
				{
					$temp['current_month'] = $current_month_bills[$student_id];
				}
				else
				{
					$temp['current_month'] = [];	
				}

				if(isset($previous_month_bills[$student_id]))
				{
					$temp['previous_month'] = $previous_month_bills[$student_id];
				}
				else
				{
					$temp['previous_month'] = [];	
				}
				$return_data[] = $temp;	
			}
			
		}

		return $return_data;
	}
	
	
	
	public function ajaxGetStudentSelectListForFeePrint()
	{
		$students = $this->ajaxGetStudentListData();
		$list = '';

		if(count($students))
		{
			$student_list = ['0' => '-- select students --'];
			$student_list = ['all' => '-- all --'];

			foreach($students as $student)
			{
				$student_list[$student->id] = '( '.$student->current_roll_number.' )'.$student->student_name.' ('.$student->username.' )';
			}	

		
			$list = HelperController::generateStaticSelectList($student_list, 'student_id', 0, '');
		}

		return $list;
		
	}



	public function ajaxGetStudentFeeListView()
	{
		$nepali_months_list = [
							  	1 => "january",
                      			2 => "february",
                      			3 => "march",
                      			4 => "april",
                      			5 => "may",
                      			6 => "june",
                      			7 => "july",
                      			8 => "august",
                      			9 => "september",
                      			10=> "october",
                      			11=> "november",
                      			12=> "december"
                      		];

		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$academic_session_id = Input::get('academic_session_id', 0);
		$month = Input::get('month', 'january');
		$issued_date = Input::get('issued_date');

		$dates = BillingHelperController::getMonthAndYearInBsAndAd($issued_date, $format = 'Y-m-d');

		
		/*if($month != $nepali_months_list[$dates['month_in_bs']])
		{
			die('This is not '.$month);
		}*/
		
		$class_section =  Classes::where('id', $class_id)->pluck('class_name') .' - '. Section::where('id', $section_id)->pluck('section_code');



			
		$invoice_group_id = $dates['year_in_ad'].'-'.$month.'-'.$class_section;
		

		//check if invoice has been generated
		$is_created = (int) BillingInvoice::where('invoice_group_id', $invoice_group_id)
										->where('is_final', 'yes')
										->where('is_direct_invoice','!=','yes')
										->where('is_opening_balance','!=','yes')
										->count();


		if($is_created)
		{
			die('invoice already generated');
		}
		
		if(file_exists(GENERATE_FEE_LOCATION.'/'.$month.'-'.$academic_session_id.'-'.$class_id.'-'.$section_id.'.json'))
		{
			$json = json_decode(file_get_contents(GENERATE_FEE_LOCATION.'/'.$month.'-'.$academic_session_id.'-'.$class_id.'-'.$section_id.'.json'), true);
			$view = json_decode($json['json_data'], true);
			$save_data = $json['data'];

		}
		else
		{

			$data = $this->ajaxGetStudentFeeList($class_id, $section_id, $nepali_months_list[$dates['month_in_ad']]);
			

			$view = [];

			
			/*file_put_contents(GENERATE_FEE_LOCATION.'/'.$month.'-'.$academic_session_id.'-'.$class_id.'-'.$section_id.'.json', $view);*/
			$data = json_decode(json_encode($data), true);
			$view['class_id'] = $class_id;
			$view['section_id'] = $section_id;
			$view['academic_session_id'] = $academic_session_id;
			$view['month'] = $month;
			$view['fees'] = $data['fees'];
			$view['students'] = $data['students'];
			$view['discounts'] = $data['discounts'];
			$view['fees_amount'] = $data['fees_amount'];
			$view['late_fees'] = $data['late_fees'];
			$view['config'] = $data['config'];
			$view['related_extra_fees_students'] = $data['related_extra_fees_students'];
			
			

			$view['transportation_fees'] = $data['transportation_fees'];
			$save_data = [];

		}
		return View::make($this->view.'.partials.ajax-student-list-fee')
					->with('view', $view)
					->with('save_data', $save_data);
	}

	public function ajaxGetStudentFeeFromClassIdSectionIdStudentId()
	{
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$student_id = Input::get('student_id', 0);

		$fee_table = BillingFee::getTableName();
		$fee_student_table = BillingFeeStudent::getTableName();

		$fees = DB::table($fee_table)
					->where($fee_table.'.is_active', 'yes')
					->distinct()
					->get();

		return View::make($this->view.'partials.partial-direct-invoice-student')
					->with('fees', $fees);
	}

	public function ajaxCreateEditStudentFee()
	{
		$selected_session_id = Input::get('selected_session_id', 0);
		$selected_class_id = Input::get('selected_class_id', 0);
		$selected_section_id = Input::get('selected_section_id', 0);
		
		$amount = Input::get('fee_amount', 0);

		if($selected_section_id)
		{
			$classes = Classes::where('academic_session_id', $selected_session_id)
							->orderBy('sort_order', 'ASC')
							->lists('class_name', 'id');

			$class_html = '';
			$extra = Input::get('extra', 'all');
					if($extra == 'all')
					{
						$extra = ['all' => 'All'];
					}
					elseif(strlen($extra))
					{
						$extra = ['0' => $extra];
					}
					else
					{
						$extra = ['0' => '-- Select --'];
					}
			

			foreach($classes as $index => $c)
			{
				$extra[$index] = $c;
			}

			$class_html = HelperController::generateStaticSelectList($extra, 'class_id', $selected_class_id, '');
		}
		else
		{
			$class_html = '<select class="form-control class_id" name = "class_id[]"><option value="0">-- Select Session First --</option></select>';
		}

		if($selected_class_id)
		{
			$section_table = Section::getTableName();
			$class_section_table = ClassSection::getTableName();
			

			$sections = DB::table($class_section_table) 
							->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
							->where($class_section_table.'.class_id', '=', $selected_class_id)
							->select($section_table.'.section_code', $section_table.'.id')
							->lists('section_code', 'id');

			$extra = Input::get('extra', 'all');
					if($extra == 'all')
					{
						$extra = ['all' => 'All'];
					}
					elseif(strlen($extra))
					{
						$extra = ['0' => $extra];
					}
					else
					{
						$extra = ['0' => '-- Select --'];
					}
			

			$section_html = '';
			foreach($sections as $index => $c)
			{
				$extra[$index] = $c;
			}

			$section_html = HelperController::generateStaticSelectList($extra, 'section_id', $selected_section_id, '');
		}
		else
		{
			$section_html = '<select class="form-control section_id" name = "section_id[]"><option value="0">-- Select Class First --</option></select>';
		}

		$students_table = Student::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$users_table = Users::getTableName();

		$students = DB::table($students_table)
						->join($student_registration_table, $student_registration_table.'.id', '=', $students_table.'.student_id')
						//->join($users_table, $users_table.'.user_details_id', '=', $student_registration_table.'.id')
						->join($users_table, function ($join) use ($users_table, $student_registration_table){
				            $join->on($users_table.'.user_details_id', '=', $student_registration_table.'.id')
				                 ->where($users_table.'.role', '=', 'student');
				        })
				        ->where('current_session_id', $selected_session_id)
						
						->where('current_section_code', HelperController::pluckFieldFromId('Section', 'section_code', $selected_section_id))
						->where('current_class_id', $selected_class_id)
						->select('username', $student_registration_table.'.id', $student_registration_table.'.student_name', 'current_roll_number')
						->orderBy('current_roll_number', 'ASC')
						->get();

		
		$count = count($students);
		$message = '';
		if($count == 0)
		{
			$message = 'No students found';
		}

		return View::make($this->view.'partials.ajax-fee-create-edit')
					->with('selected_session_id', $selected_session_id)
					->with('selected_class_id', $selected_class_id)
					->with('selected_section_id', $selected_section_id)
					->with('section_html', $section_html)
					->with('class_html', $class_html)
					->with('amount', $amount)
					->with('students', $students)
					
					->with('message', $message)
					->with('count', $count);
	}

	public function ajaxCalculateTax()
	{
		$sum_without_tax = Input::get('sum_without_tax', 0);
		//return $sum_without_tax;
		return BillingHelperController::calculateTax($sum_without_tax);
	}

	public function getClassIdsFromSessionId()
	{
		$session_id = Input::get('session_id');
		$default_class_id = Input::get('default_class_id');

		$class_ids = Classes::where('academic_session_id', $session_id)
							->where('is_active', 'yes')
							->select('id', 'class_name')
							->orderBy('sort_order', 'ASC')
							->get();

		$html = '';
		foreach($class_ids as $c)
		{
			$sel = $c->id == $default_class_id ? 'selected' : '';
			$html .= '<option value = "'.$c->id.'" '.$sel.'>'.$c->class_name.'</option>';
		}

		return $html;
	}

	public function getClassIdsFromSessionIdAndClassId()
	{
		$session_id = Input::get('session_id');
		$class_id = Input::get('class_id');
		$default_section_id = Input::get('default_section_id');

		$class_table = Classes::getTableName();
		$section_table = Section::getTableName();
		$class_section_table = ClassSection::getTableName();

		$section_ids = DB::table($class_section_table)
							->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')
							->where('class_id', $class_id)
							->select($section_table.'.id', $class_section_table.'.section_code')
							->get();

		$html = '';
		foreach($section_ids as $c)
		{
			$sel = $c->id == $default_section_id ? 'selected' : '';
			$html .= '<option value = "'.$c->id.'" '.$sel.'>'.$c->section_code.'</option>';
		}

		return $html;
	}
	public function getFeePrintOrganizationList()
	{
		$date_type = 'AD';

		$month_type = $date_type == 'AD' ? 'month_in_ad' : 'month_in_bs';
		$year_type = $date_type == 'AD' ? 'year_in_ad' : 'year_in_ad';

		$session_id = Input::get('academic_session_id'); //shows id of 2073 = 4
		$organization_id = Input::get('organization_id');
		$issued_date = Input::get('issued_date');
		
		
		$engMonth = $issued_date;
		$nepMonth = new DateConverter();
		$nepaliMonth = $nepMonth->ad2bs($engMonth);
		$parts = explode('-',$nepaliMonth);
		$eng_parts = explode('-', $issued_date);


		$year = $date_type == 'AD' ? $eng_parts[0] : $parts[0];
		$month = $date_type == 'AD' ? $eng_parts[1] : $parts[1];
		
		
		$organization_table = BillingDiscountOrganization::getTableName();
		$discount_table = BillingDiscount::getTableName();
		$discount_details_table = BillingDiscountDetails::getTableName();
				
		
		$data['data'] =  DB::table(BillingInvoice::getTableName())
								->where('related_user_group', 'organization')
								->where('related_user_id', $organization_id)
								->where($year_type, $year)
								->where($month_type, '=', $month )
								->where('is_final', 'yes')
								->where('is_cleared', '!=', 'yes')
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->get();
		

		$previous_month['data'] = DB::table(BillingInvoice::getTableName())
								->where('related_user_group', 'organization')
								->where('related_user_id', $organization_id)
								->where(function($query) use ($month, $year, $month_type, $year_type)
								{
									$query->where(function($query) use ($month, $year, $month_type, $year_type)
									   {
										$query->where(BillingInvoice::getTableName().'.'.$month_type, '<', $month )
										->where(BillingInvoice::getTableName().'.'.$year_type,'=', $year);
									   })
										->orWhere(BillingInvoice::getTableName().'.'.$year_type, '<', $year);
								})
								->where('is_final', 'yes')
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'cancel')
								///////// billing-cancel-v1-changes /////////
								->where('is_cleared', '!=', 'yes')
								->get();

	
		
		$return_data = [];
		foreach($data['data'] as $d)
		{
			$return_data[$d->related_user_id]['current_month'][] = $d;
		}

		foreach($previous_month['data'] as $d)
		{
			$return_data[$d->related_user_id]['previous_month'][] = $d;
		}

		return View::make($this->view.'fee-print-organization-ajax')
					->with('return_data', $return_data);
	}

	public function getFeePrintOrganization()
	{
		AccessController::allowedOrNot('billing', 'can_print_fee');
		return View::make($this->view.'fee-print-organization');
	}


	//////////////// this is for api //////////////////////////////////
	public function apiGetListViewFeePrintList()
	{
		$selected_month = Input::get('nepDate', '0-0'); 
		$class_id = Input::get('class_id', 0);
		$section_id = Input::get('section_id', 0);
		$session_id = Input::get('academic_session_id', 0); //shows id of 2073 = 4
		$student_id = Input::get('student_id', 0);
		
		$class = Classes::where('id', $class_id)->first()->class_name;
		$section = Section::where('id', $section_id)->first()->section_code;
		$parts = explode('-',$selected_month);
		$year = $parts[0];
		$month = $parts[1];
		$return_data = $this->getListViewFeePrintListData($session_id, $class_id, $section_id, $selected_month, $student_id);
		
		return View::make($this->view.'list-view-fee-print-ajax')
					->with('block_data', $return_data)
					->with('class', $class)
					->with('section', $section)
					->with('year', $year)
					->with('month', HelperController::getNepaliMonth($month))
					->with('month_index', $month)
					->with('issued_date', $selected_month);
	}

	public function apiGetStudentStatement()
	{
		return View::make($this->view.'statement-student');
	}
}