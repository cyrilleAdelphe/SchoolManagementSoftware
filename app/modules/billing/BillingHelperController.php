<?php

use Carbon\Carbon;

class BillingHelperController
{
	public static function calculateTax($sum)
	{
		return $sum * 0.01;
	}

	public static function calculateDiscount($sum, $percent)
	{
		return $sum * $percent/100;
	}

	public static function getMonthAndYearInBsAndAd($date, $format = 'Y-m-d')
	{
		$return = [];
		$date = Carbon::createFromFormat($format, $date);

		$return['month_in_ad'] = $date->format('n');
		$return['year_in_ad'] = $date->format('Y');
		$bs_date = (new DateConverter)->ad2bs($date->format('Y-m-d'));

		$bs_date = explode('-', $bs_date);
		$return['month_in_bs'] = (int) $bs_date[1];
		$return['year_in_bs'] = (int) $bs_date[0];



		return $return;

	}

	public static function generateInvoiceNumber($counter = 0) //counter gives the highest invoice number in database
	{
			if($counter)
			{
				return ++$counter;
			}
			else
			{	
				$fiscal_year = BillingHelperController::getFiscalYear($date = '', $format = 'Y-m-d');
				
				$counter = DB::table(BillingInvoice::getTableName())
							->where('financial_year', $fiscal_year)
							->max('invoice_number');
				return ++$counter;
			}	
		
	}

	public static function getFiscalYear($date = '', $format = 'Y-m-d')
	{

		$date = strlen($date) ? $date : date($format);
		$date_converter = new DateConverter;
		$today_date_in_bs = $date_converter->ad2bs($date);
		$today_date_in_bs = explode('-', $today_date_in_bs);

		$config = json_decode(File::get(app_path().'/modules/billing/config/config.json'));
		$fiscal_year = '';

		if($today_date_in_bs[1] < $config->start_month)
		{
			$fiscal_year = ($today_date_in_bs[0]-1).'/'.($today_date_in_bs[0]);
		}
		elseif($today_date_in_bs[1] > $config->start_month)
		{
			$fiscal_year = $today_date_in_bs[0].'/'.($today_date_in_bs[0]+1);
		}
		else
		{
			if($today_date_in_bs[0] == $config->start_day)
			{
				$fiscal_year = $today_date_in_bs[0].'/'.($today_date_in_bs[0]+1);
			}	
			elseif($today_date_in_bs[0] > $config->start_day)
			{
				$fiscal_year = $today_date_in_bs[0].'/'.($today_date_in_bs[0]+1);
			}
			else
			{
				$fiscal_year = ($today_date_in_bs[0]-1).'/'.($today_date_in_bs[0]);
			}
		}

		return $fiscal_year;


	}

	public static function generateSelectList($fees)
	{
		
		$html = '';
		$html .= '<div class = "row">';
		$html .= '<select name = "fees[]" class = "form-control fees">';
		foreach($fees as $fee)
		{
			$html .= '<option value = "'.$fee->id.'">'.$fee->fee_category."</option>";
		}
		$html .= "</select>";
		return $html;
	}

	public static function getStudentListFromSessionClassAndSection($session_id, $class_id, $section_id)
	{

	}

	public static function getDateRange($dateRange, $seperator = '-', $format = 'Y/m/d', $add_secs = true)
	{
		
		$date = explode($seperator, $dateRange);

		$date[0] = Carbon::createFromFormat($format, trim($date[0]));
		$date[1] = Carbon::createFromFormat($format, trim($date[1]));
		if($add_secs)
		{
			$date[0] = $date[0]->second(0)->minute(0)->hour(0)->format('Y-m-d H:i:s');
			$date[1] = $date[1]->second(59)->minute(59)->hour(23)->format('Y-m-d H:i:s');
		}
		else
		{
			$date[0] = $date->format('Y-m-d');
			$date[1] = $date->format('Y-m-d');
		}

		return $date;
	}

	public static function getDueInvoices($date, $due_days)
	{
		
	}

	public static function generateInvoiceDetails($related_user_id, $related_user_group, $session_id, $fees = [], $discounts = [], $summary= [], $issued_date, $invoice_type, $invoice_balance, $is_final, $is_cleared, $received_amount, $note, $name = '', $is_direct_invoice = 'no', $is_opening_balance = 'no')
	//$fees in the form [{"fee_title":"Monthly Fee","fee_amount":"1000.00","taxable":"yes","recipient":"self"}]
	{
		$student_registration_table = StudentRegistration::getTableName();
		$student_table = Student::getTableName();
		$organization_table = BillingDiscountOrganization::getTableName();
		$class_table = Classes::getTableName();

		$dates = BillingHelperController::getMonthAndYearInBsAndAd($issued_date, $format = 'Y-m-d');

		$data_to_store = [];
		$invoice_details = [];

		if($related_user_group == 'student')
		{
			$personal_details = DB::table($student_registration_table)
									->join($student_table, $student_table.'.student_id', '=', $student_registration_table.'.id')
									->join($class_table, $class_table.'.id', '=', 'current_class_id')
									->where($student_registration_table.'.id', '=', $related_user_id)
									->where('current_session_id', $session_id)
									->select('student_name as name', 'current_roll_number as roll_number', 'class_name', 'current_section_code', $student_registration_table.'.id')
									->get();

			$invoice_details['personal_details']['id'] = $personal_details[0]->id;
			$invoice_details['personal_details']['name'] = $personal_details[0]->name;
			$invoice_details['personal_details']['roll_number'] = $personal_details[0]->roll_number;
			$invoice_details['personal_details']['group'] = $related_user_group;
			$invoice_details['personal_details']['class'] = $personal_details[0]->class_name;
			$invoice_details['personal_details']['section'] = $personal_details[0]->current_section_code;


		}
		elseif($related_user_group == 'organanization')
		{
			$personal_details = DB::table($organization_table)
									->where('id', $related_user_id)
									->select('organization_name as name', 'id')
									->get();

			$invoice_details['personal_details']['id'] = $personal_details[0]->id;
			$invoice_details['personal_details']['name'] = $personal_details[0]->name;
			$invoice_details['personal_details']['roll_number'] = $personal_details[0]->roll_number;
			$invoice_details['personal_details']['group'] = $related_user_group;
			$invoice_details['personal_details']['class'] = 'None';
			$invoice_details['personal_details']['section'] = 'None';


		}
		else
		{
			$invoice_details['personal_details']['id'] = 0;
			$invoice_details['personal_details']['name'] = $name;
			$invoice_details['personal_details']['roll_number'] = 0;
			$invoice_details['personal_details']['group'] = 'other';
			$invoice_details['personal_details']['class'] = 'None';
			$invoice_details['personal_details']['section'] = 'None';
		}

		$invoice_details['fees'] = [];
		foreach($fees as $f)
		{
			$invoice_details['fees'][] = ['fee_title' => $f['fee_title'], 'fee_amount' => $f['fee_amount'], 'taxable' => $f['taxable'], 'recipient' => $f['recipient']];
		}

		$invoice_details['discount'] = [];
		foreach($discounts as $f)
		{
			$invoice_details['discount'][] = ['organization_name' => $f['organization_name'], 'discount_title' => $f['discount_title'], 'fee_title' => $f['fee_title'], 'discount_amount' => $f['discount_amount']];
		}

		$invoice_details['summary'] = [];
		$invoice_details['summary'] = ['total' => $summary['total'], 'tax' => $summary['tax'], 'taxable_amount' => $summary['taxable_amount'], 'untaxable_amount' => $summary['untaxable_amount'], 'sum_without_tax' => ($summary['taxable_amount'] + $summary['untaxable_amount'])];

		$data_to_store['invoice_details'] = json_encode($invoice_details);

		$data_to_store['invoice_number'] = BillingHelperController::generateInvoiceNumber();
		$data_to_store['related_user_id'] = $related_user_id;
		$data_to_store['related_user_group'] = $related_user_group;
		$data_to_store['invoice_type'] = 'invoice';
		$data_to_store['invoice_balance'] = $invoice_balance;
		$data_to_store['issued_date'] = $issued_date;
		$data_to_store['is_final'] = $is_final; //change this
		$data_to_store['is_active'] = 'yes';
		$data_to_store['class_section'] = $invoice_details['personal_details']['class'] .' - '. $invoice_details['personal_details']['section'];
		$data_to_store['is_cleared'] = $is_cleared;
		$data_to_store['received_amount'] = $received_amount;
		$data_to_store['note'] = $note;
		$data_to_store['month_in_ad'] = $dates['month_in_ad'];
		$data_to_store['month_in_bs'] = $dates['month_in_bs'];
		$data_to_store['year_in_ad'] = $dates['year_in_ad'];
		$data_to_store['year_in_bs'] = $dates['year_in_bs'];
		$data_to_store['is_direct_invoice'] = $is_direct_invoice;
		$data_to_store['is_opening_balance'] = $is_opening_balance;
		$data_to_store['financial_year'] = BillingHelperController::getFiscalYear($issued_date);
		$base_controller = new BaseController;
		$createdByUpdatedBy = $base_controller->getCreatedByUpdatedBy();
		foreach($createdByUpdatedBy as $index => $val)
		{
			$data_to_store[$index] = $val;
		}

		$data_to_store['created_at'] = $data_to_store['updated_at'] = date('Y-m-d H:i:s');

		return $data_to_store;

	}

	public static function removeQuotesAndUnderScore($text)
	{
		$text = str_replace("'", "", $text);
		if(strpos($text, "_"))
		{
			$str = explode('_', $text);
			$text = '';
			foreach($str as $s)
			{
				$text .= ucfirst($s).' ';
			}
		}

		return $text;
	}

	//// billing code added here ////
	public static function calculateSumOfCreditNotes($invoice_id)
	{
		$sum = BillingInvoice::where('related_invoice_id', $invoice_id)
								->where('invoice_type', 'credit_note')
								->sum('invoice_balance');

		return $sum;
	}
	//// billing code added here ////

}