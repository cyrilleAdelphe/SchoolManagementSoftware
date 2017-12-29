<?php 
 
class BillingDiscountController extends BaseController
{

	protected $view = 'billing.views.discount.';

	protected $model_name = 'BillingDiscount';

	protected $module_name = 'billing-discount';

	protected $role;

	public $current_user;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'discount_name',
										'alias'			=> 'Discount Name'
									)
								 );

	public function getCreateFlatDiscountsView()
	{
		AccessController::allowedOrNot('billing', 'can_create_discount');
		return View::make($this->view.'flat-discounts');
	}

	public function postCreateFlatDiscountsView()
	{
		AccessController::allowedOrNot('billing', 'can_create_discount');
		$input = Input::all();
		//do validation for unique discount name

		$check = BillingDiscount::where('discount_name', $input['discount_name'])
								->count();

		if($check)
		{
			Session::flash('error-msg', 'Discount name already present. Please choose another name');
			return Redirect::back();
		}

		try
		{

			DB::connection()->getPdo()->beginTransaction();
				$discount_id = $this->storeInDatabase(['organization_id' => $input['organization_id'], 'discount_name' => $input['discount_name'], 'is_active' => 'yes'], 'BillingDiscount');
				
				//check if fee_id is transportation id
				$fee_name = BillingFee::where('id', $input['fee_id'])->pluck('fee_category');
				if($fee_name == 'Transportation Fee')
				{
					$student_ids = TransportationStudent::lists('student_id');
				}
				else
				{
					//else give discounts to all students of applicable class
					$billing_fee_student_table = BillingFeeStudent::getTableName();
					$student_table = Student::getTableName();
					//$section_table = Section::getTableName();

					$current_session = HelperController::getCurrentSession();

					$student_ids = DB::table($billing_fee_student_table)
										//->join($section_table, $section_table.'.id', '=', $billing_fee_student_table.'.section_id')
										->join($student_table, $student_table.'.current_class_id', '=', $billing_fee_student_table.'.class_id')
										->where('current_session_id', $current_session)
										->lists('student_id');

					$student_ids_from_billing_extra_fees_table = BillingExtraFees::where('fee_id', $input['fee_id'])
					->lists('student_id');

					$student_ids = array_unique(array_merge($student_ids, $student_ids_from_billing_extra_fees_table));

					unset($student_ids_from_billing_extra_fees_table);

				}

				foreach($student_ids as $student_id)
				{
					$this->storeInDatabase(['is_active' => 'yes', 'discount_id' => $discount_id, 'fee_id' => $input['fee_id'], 'student_id' => $student_id, 'discount_percent' => $input['discount_percent']], 'BillingDiscountDetails');
				}

			Session::flash('success-msg', 'Flat Discount successfully created. Please dont forget to delete this discount once the bill is generated else this discount will continue!');
			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
		}
		catch(Exception $e)
		{
			
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back();
	}

	public function getOrganizationCreateView()
	{
		AccessController::allowedOrNot('billing', 'can_create_organization');
		return View::make($this->view.'create-organization');
	}

	public function postOrganizationCreateView()
	{
		AccessController::allowedOrNot('billing', 'can_create_organization');
		$data = Input::all();

		try
		{
			$this->storeInDatabase($data, 'BillingDiscountOrganization');
			Session::flash('success-msg', 'Organization successfully created');
			return Redirect::route('billing-discount-organization-list');
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back()
							->withInput();
		}

	}

	public function getOrganizationEditView($id)
	{
		AccessController::allowedOrNot('billing', 'can_edit_organization');
		$data = (new BillingDiscountOrganization)->getEditViewData($id);
		
		return View::make($this->view.'edit-organization')
					->with('data', $data)
					->with('id', $id);
	}

	public function getOrganizationListView()
	{
		AccessController::allowedOrNot('billing', 'can_view_organization');
		$data = (new BillingDiscountOrganization)->getListViewData([]);

		return View::make($this->view.'list-organization')
					->with('data', $data);
	}

	public function postOrganizationEditView($id)
	{
		AccessController::allowedOrNot('billing', 'can_edit_organization');
		$data = Input::all();
		$data['id'] = $id;

		try
		{
			$this->updateInDatabase($data, [], 'BillingDiscountOrganization');
			Session::flash('success-msg', 'Organization successfully created');
			return Redirect::route('billing-discount-organization-list');
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back()
							->withInput();
		}
	}

	public function getCreateView()
	{
		AccessController::allowedOrNot('billing', 'can_create_discount');
		$temp = BillingFee::where('is_active', 'yes')->lists('fee_category', 'id');
		//$fees = ['0' => '-- Select Fee --'];
		foreach($temp as $index => $t)
		{
			$fees[$index] = $t;
		}
		unset($temp);
		$session = AcademicSession::where('is_active', 'yes')->select('session_name', 'is_current', 'id')->get();
		return View::make($this->view.'create')
					->with('fees', $fees)
					->with('session', $session);
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot('billing', 'can_create_discount');
		$input = Input::all();


		try
		{
			DB::connection()->getPdo()->beginTransaction();
			$data_to_store_in_discount = [];
			$data_to_store_in_discount['discount_name'] = $input['discount_name'];
			$data_to_store_in_discount['organization_id'] = $input['organization_id'];
			$data_to_store_in_discount['is_active'] = 'yes';

			$discount_id = $this->storeInDatabase($data_to_store_in_discount, 'BillingDiscount');

			
			foreach($input['fee_id'] as $index => $fee_id)
			{
				//// BillingDiscount-v1-changes-made-here ////
				if($input['student_id'][$index])
				{
					if($fee_id == 'all')
					{
						//get all fee ids
						$fee_ids = BillingFee::lists('id');
						$fee_ids = array_diff($fee_ids, $input['fee_id']);

						foreach($fee_ids as $_fee_id)
						{
							$data_to_store_in_discount_details = ['discount_id' => $discount_id, 'student_id' => $input['student_id'][$index], 'fee_id' => $_fee_id, 'discount_percent' => $input['discount_percent'][$index], 'is_active' => 'yes'];
							$this->storeInDatabase($data_to_store_in_discount_details, 'BillingDiscountDetails');			
						}
					}
					else
					{
						$data_to_store_in_discount_details = ['discount_id' => $discount_id, 'student_id' => $input['student_id'][$index], 'fee_id' => $fee_id, 'discount_percent' => $input['discount_percent'][$index], 'is_active' => 'yes'];
						$this->storeInDatabase($data_to_store_in_discount_details, 'BillingDiscountDetails');	
					}	
				}
				//// BillingDiscount-v1-changes-made-here ////
			}	
			DB::connection()->getPdo()->commit();
			Session::flash('success-msg', 'Discount successfully created');
			return Redirect::route('billing-discount-list');
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back()
							->withInput();
		}
		
	}

	public function getEditView($id)
	{
		AccessController::allowedOrNot('billing', 'can_edit_discount');
		$student_names = [];
		$student_ids = [];
		$data = BillingDiscount::where('id', $id)->with('discountDetails')->first();
		$temp = BillingFee::where('is_active', 'yes')->lists('fee_category', 'id');
		//$fees = ['0' => '-- Select Fee --'];
		foreach($temp as $index => $t)
		{
			$fees[$index] = $t;
		}
		unset($temp);
		
		if($data)
		{
			foreach($data->discountDetails as $d)
			{
				$student_ids[] = $d->student_id;
			}
			$students_table = Student::getTableName();
			$class_table = Classes::getTableName();
			$student_registration_table = StudentRegistration::getTableName();
			$section_table = Section::getTableName();
			$current_session = AcademicSession::where('is_current', 'yes')->pluck('id');

			$temp_student_names = DB::table($students_table)
							->join($student_registration_table, $student_registration_table.'.id', '=', $students_table.'.student_id')
							->join($class_table, $class_table.'.id', '=', $students_table.'.current_class_id')
							->join($section_table, $section_table.'.section_code', '=', $students_table.'.current_section_code')
							///// Billing-v1-lastname-changes-made-here ////
							->select($student_registration_table.'.student_name','last_name', $class_table.'.class_name', $section_table.'.section_code', $student_registration_table.'.id')
							///// Billing-v1-lastname-changes-made-here ////
							->where('current_session_id', $current_session)
							->whereIn('student_id', $student_ids)
							->get();

			foreach($temp_student_names as $index => $t)
			{
				$student_names[$t->id] = $t->student_name.' '.$t->last_name.' ('.$t->class_name.' - '.$t->section_code.')';
				unset($temp_student_names[$index]);	
			}
			
		}

		unset($student_ids);
		return View::make($this->view.'edit')
					->with('data', $data)
					->with('id', $id)
					->with('fees', $fees)
					->with('student_names', $student_names);
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot('billing', 'can_edit_discount');
		$input = Input::all();

		try
		{
			DB::connection()->getPdo()->beginTransaction();
			$data_to_store_in_discount = [];
			$data_to_store_in_discount['discount_name'] = $input['discount_name'];
			$data_to_store_in_discount['is_active'] = 'yes';
			$data_to_store_in_discount['id'] = $id;
			
			/* BillingDiscount-v1-changes-made-here */
			$data_to_store_in_discount['organization_id'] = $input['organization_id'];
			/* BillingDiscount-v1-changes-made-here */

			$this->updateInDatabase($data_to_store_in_discount, [], 'BillingDiscount');

			BillingDiscountDetails::where('discount_id', $id)->delete();
			
			foreach($input['fee_id'] as $index => $fee_id)
			{
				if($fee_id == 'all')
				{
					//get all fee ids
					$fee_ids = BillingFee::lists('id');

					foreach($fee_ids as $_fee_id)
					{
						$data_to_store_in_discount_details = ['discount_id' => $id, 'student_id' => $input['student_id'][$index], 'fee_id' => $_fee_id, 'discount_percent' => $input['discount_percent'][$index], 'is_active' => 'yes'];
						$this->storeInDatabase($data_to_store_in_discount_details, 'BillingDiscountDetails');			
					}
				}
				else
				{
					$data_to_store_in_discount_details = ['discount_id' => $id, 'student_id' => $input['student_id'][$index], 'fee_id' => $fee_id, 'discount_percent' => $input['discount_percent'][$index], 'is_active' => 'yes'];
					$this->storeInDatabase($data_to_store_in_discount_details, 'BillingDiscountDetails');	
				}
				
			}	
			DB::connection()->getPdo()->commit();
			Session::flash('success-msg', 'Discount successfully created');
			return Redirect::route('billing-discount-list');
		}
		catch(Exception $e)
		{
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back()
							->withInput();
		}
	}

	public function postDeleteDiscountView($id)
	{
		AccessController::allowedOrNot('billing', 'can_delete_discount');
		try
		{
			BillingDiscount::where('id', $id)->delete();	
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}


		Session::flash('success-msg', 'Successfully deleted');
		return Redirect::back();
	}

	public function postDeleteOrganizationView($id)
	{
		AccessController::allowedOrNot('billing', 'can_delete_organization');
		try
		{
			BillingDiscountOrganization::where('id', $id)->delete();	
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}


		Session::flash('success-msg', 'Successfully deleted');
		return Redirect::back();
	}

	//////////////////////////////////////////////////////////////////////////////
	public function ajaxBillingDiscountGetFeeData($fee_id)
	{
		$details = DB::table(BillingFeeStudent::getTableName())
						->where('fee_id', $fee_id)
						->select('fee_id', 'class_id', 'section_id', 'fee_amount', 'excluded_student_id')
						->get();

		return $details;

	}

	public function ajaxStudentIdAutoComplete()
	{

		//get current_session
		$term = Input::get('term', NULL);
		$current_session = AcademicSession::where('is_current', 'yes')->pluck('id');

		$students_table = Student::getTableName();
		$class_table = Classes::getTableName();
		$student_registration_table = StudentRegistration::getTableName();
		$section_table = Section::getTableName();

		$students = DB::table($students_table)
						->join($student_registration_table, $student_registration_table.'.id', '=', $students_table.'.student_id')
						->join($class_table, $class_table.'.id', '=', $students_table.'.current_class_id')
						->join($section_table, $section_table.'.section_code', '=', $students_table.'.current_section_code')
						->select($student_registration_table.'.student_name',$student_registration_table.'.last_name', $class_table.'.class_name', 

$section_table.'.section_code', $student_registration_table.'.id', 'current_roll_number')
						->where('current_session_id', $current_session)
						->where(DB::raw('CONCAT_WS(" ", student_name, last_name)'), 'LIKE', '%'.$term.'%')
						->orderBy('student_name', 'ASC')
						->get();

		$return = [];
		foreach($students as $index => $s)
		{
			$return[] = ['id' => $s->id, 'label' => '( '.$s->current_roll_number.' ) '.$s->student_name.' '.$s->last_name.' ('.$s->class_name.' - '. $s->section_code.' )'];

			unset($students[$index]);
		}
		if(count($return) == 0)
		{
			$return = [['id' => 0, 'label' => 'No student found']];
		}

		//return [Input::get('term')];

		return $return;
	}

	public function ajaxBillingDiscountGetFeeView($detais)
	{
		return View::make($this->view.'discount.ajax.ajax-billing-discount-fee-details');
	}

}