<?php

use Carbon\Carbon;

class AdvanceBillingPaymentController extends BaseController
{
	protected $view = 'billing.views.';

	protected $model_name = 'Billing';

	protected $module_name = 'billing';

	protected $role;

	public $current_user;

	public function getAdvanceBillingView()
	{
		AccessController::allowedOrNot('billing', 'can_receive_payment');
		////// Billing-v1-changed-made-here ///////
		$invoice_number = BillingReceipt::getLatestReceiptNumber();
		////// Billing-v1-changed-made-here ///////
		
		return View::make('billing.views.advance-payment')
					->with('invoice_number', $invoice_number);
	}

	public function postAdvanceBillingView()
	{
		AccessController::allowedOrNot('billing', 'can_receive_payment');

		$input = Input::all();

		//store in receipt table

		//store in transaction table
		if((int) strlen(trim($input['student_id'])) == 0)
		{
			Session::flash('error-msg', 'Student not selected');
			return Redirect::back();
		}

		if((int) $input['fee_amount'] == 0)
		{
			Session::flash('error-msg', 'Fee amount cannot be 0');
			return Redirect::back();
		}

		$remaining_dues = BillingInvoice::where('related_user_id', $input['student_id'])
										->where('related_user_group', 'student')
										->where('is_cleared', '!=', 'yes')
										->first();

		if($remaining_dues)
		{
			Session::flash('error-msg', 'Student has remaining due. Cannot take advance. Please clear the due first');
			return Redirect::back();
		}

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$data_to_store_in_receipt_table = [];
			$data_to_store_in_receipt_table['receipt_no'] = BillingReceipt::getLatestReceiptNumber();
			$data_to_store_in_receipt_table['financial_year'] = BillingHelperController::getFiscalYear($input['issued_date']);
			$data_to_store_in_receipt_table['invoice_no'] = '';
			$data_to_store_in_receipt_table['paid_amount'] = $input['fee_amount'];
			$data_to_store_in_receipt_table['amount_to_be_paid'] = 0.00;
			$data_to_store_in_receipt_table['received_from'] = 'student';
			$data_to_store_in_receipt_table['received_id'] = $input['student_id'];
			$data_to_store_in_receipt_table['received_on'] = $input['issued_date'];
			$data_to_store_in_receipt_table['receipt_status'] = 'paid';
			$data_to_store_in_receipt_table['receipt_description'] = 'Advance Payment '.$input['note'];

			$this->storeInDatabase($data_to_store_in_receipt_table, 'BillingReceipt');

			BillingTransaction::recordTransaction($input['issued_date'], 'advance', $input['fee_amount'], $input['student_id'], 'student', $data_to_store_in_receipt_table['receipt_description'], NULL);

			Session::flash('success-msg', 'Advance successfully collected')	;

			DB::connection()->getPdo()->commit();
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
		}

		return Redirect::back();
	}
}