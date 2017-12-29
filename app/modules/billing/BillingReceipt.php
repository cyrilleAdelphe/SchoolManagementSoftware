<?php

class BillingReceipt extends BaseModel
{
	protected $table = 'billing_receipt';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingReceipt';



	public $createRule = [
	];

	public $updateRule = [
	];

	public static function getLatestReceiptNumber($counter = 0)
	{
		if($counter)
		{
			return ++$counter;
		}
		else
		{	
			$financial_year = BillingHelperController::getFiscalYear($date = '', $format = 'Y-m-d');
			
			$counter = DB::table(BillingReceipt::getTableName())
						->where('financial_year', $financial_year)
						->max('receipt_no');
			
			return ++$counter;
		}	
	}

	public static function storeInReceiptPayment($invoice_no, $amount_to_be_paid, $paid_amount, $received_from, $received_id, $received_on, $received_name = NULL)
	{

		$data_to_store['receipt_no'] = BillingReceipt::getLatestReceiptNumber();
		$data_to_store['received_on'] = $received_on;
		$data_to_store['financial_year'] = BillingHelperController::getFiscalYear($received_on, $format = 'Y-m-d');
		$data_to_store['received_from'] = $received_from;
		$data_to_store['received_id'] = $received_id;
		$data_to_store['amount_to_be_paid'] = $amount_to_be_paid;
		$data_to_store['paid_amount'] = $paid_amount;
		$data_to_store['invoice_no'] = $invoice_no;
		$data_to_store['received_name'] = $received_name;

		$baseController = new BaseController;
		$created_by_updated_by = $baseController->getCreatedByUpdatedBy();
		$data_to_store['created_by'] = $created_by_updated_by['created_by'];
		$data_to_store['updated_by'] = $created_by_updated_by['updated_by'];

		$receipt_id = BillingReceipt::create($data_to_store)->id;

		return $receipt_id;
	}

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$student_table = StudentRegistration::getTableName();
		$organization_table = BillingDiscountOrganization::getTableName();
		$table = $model::getTableName();
		

		$result = DB::table($table)
					->leftJoin($student_table, function($query) use ($table, $student_table)
						{
							$query->on($student_table.'.id', '=', $table.'.received_id')
								->where($table.'.received_from', '=', 'student');
						})
					->leftJoin($organization_table, function($query) use ($table, $organization_table)
						{
							$query->on($organization_table.'.id', '=', $table.'.received_id')
								->where($table.'.received_from', '=', 'organization');
						});
		
		$result = $result->select(array($model::getTableName().'.*', $student_table.'.student_name', $student_table.'.last_name', $organization_table.'.organization_name'));

		//$result = $result->where($model::getTableName().'.is_active', $queryString['status']);

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
			}
			
		}
		
		if(isset($queryString['orderBy']))
		{
			$result = $result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result = $result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}
}