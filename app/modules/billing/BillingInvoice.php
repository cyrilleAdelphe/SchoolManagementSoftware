<?php

class BillingInvoice extends BaseModel
{
	protected $table = 'billing_invoice';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingInvoice';

	public $defaultOrder = ['orderBy' => 'issued_date', 'orderOrder' => 'DESC'];



	public $createRule = [
	];

	public $updateRule = [
	];

	public static function getInvoiceDetails($id)
	{
		$data = BillingInvoice::where('id', $id)
								->first();

		return $data;
	}

	public function getListViewData($queryString)
	{
		
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->select(array($model::getTableName().'.*'));
		//$result = $result->where($model::getTableName().'.is_active', $queryString['status']);

		$result = $result->whereIn('invoice_type', SsmConstants::$const_billing_types['credit']);
						//->where('is_cleared', '!=', 'yes');
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
		//}

		if(isset($queryString['orderBy']))
		{
			$result = $result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result = $result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->orderBy('id', 'DESC');

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

}