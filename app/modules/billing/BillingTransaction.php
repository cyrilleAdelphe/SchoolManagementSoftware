<?php

class BillingTransaction extends BaseModel
{
	protected $table = 'billing_transactions';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $model_name = 'BillingTransaction';

	/*SELECT SUM(m1.balance_amount)
FROM per_billing_transactions m1 LEFT JOIN per_billing_transactions m2
 ON (m1.related_user_id = m2.related_user_id AND m1.id < m2.id)
WHERE m2.id IS NULL;*/


	public $createRule = [
	];

	public $updateRule = [
	];

	public static function recordTransaction($transaction_date, $transaction_type, $transaction_amount, $related_user_id, $related_user_group, $description = '', $related_invoice_id)
	{
		$data_to_store = [];

		$data_to_store['transaction_date'] = $transaction_date;
		$data_to_store['transaction_amount'] = $transaction_amount;
		$data_to_store['related_user_id'] = $related_user_id;
		$data_to_store['related_user_group'] = $related_user_group;
		$data_to_store['transaction_type'] = $transaction_type;
		$data_to_store['description'] = $description;
		$data_to_store['transaction_no'] = BillingTransaction::generateTransactionNo();
		$data_to_store['balance_amount'] = BillingTransaction::getBalanceAmount($transaction_type, $transaction_amount, $related_user_id, $related_user_group, $related_invoice_id);
		$data_to_store['related_invoice_id'] = $related_invoice_id;
		//$data_to_store['crea']
		$baseController = new BaseController;
		$created_by_updated_by = $baseController->getCreatedByUpdatedBy();
		$data_to_store['created_by'] = $created_by_updated_by['created_by'];
		$data_to_store['updated_by'] = $created_by_updated_by['updated_by'];

		$id = BillingTransaction::create($data_to_store)->id;
		return $id;
	}

	///////// billing-cancel-v1-changes /////////
	public static function setReverseStatus($transaction_id)
	{
		$record = BillingTransaction::where('id', $transaction_id)->first();

		if(in_array($record->transaction_type, SsmConstants::$const_billing_types['credit']))
		{
			$status = 'cancel_credit';
		}
		elseif(in_array($record->transaction_type, SsmConstants::$const_billing_types['debit']))
		{
			$status = 'cancel_debit';
		}

		$amount = -1 * $record->transaction_amount;

		BillingTransaction::recordTransaction(date('Y-m-d'), $status, $amount, $record->related_user_id, $record->related_user_group, $description = 'This transaction was cancelled', $record->related_invoice_id);

	}
	///////// billing-cancel-v1-changes /////////

	public static function generateTransactionNo($counter = 0)
	{
		if($counter)
		{
			return ++$counter;
		}
		else
		{
			$counter = (int) DB::table(BillingTransaction::getTableName())->max('transaction_no');
			
			return ++$counter;
		}	
	}

	public static function getBalanceAmount($transaction_type, $transaction_amount, $related_user_id, $related_user_group, $related_invoice_id)
	{

		if($related_user_id == 0)
			return 0;
		//get latest balance amount;
		$prev_amount = (float) DB::table(BillingTransaction::getTableName())
							->where('related_user_id', $related_user_id)
							->where('related_user_group', $related_user_group)
							->orderBy('id', 'DESC')
							->take(1)
							->pluck('balance_amount');


		if(in_array($transaction_type, SsmConstants::$const_billing_types['credit']))
		{
			if($prev_amount < 0)
			{
				$extra_cash = -1 * $prev_amount;

				$invoice = BillingInvoice::where('id', $related_invoice_id)
												->first();


				if($extra_cash >= $transaction_amount)
				{


					$invoice->is_cleared = 'yes';
					$invoice->received_amount = $invoice->invoice_balance;
					$invoice->save();
				}
				else
				{

					$invoice->is_cleared = 'partial';
					//dd($invoice);
					$invoice->received_amount += $extra_cash;
					
					$invoice->save();

				}

			}

			$prev_amount += $transaction_amount;
			
		}
		elseif(in_array($transaction_type, SsmConstants::$const_billing_types['debit']))
		{
			$prev_amount -= $transaction_amount;
		}
		else
		{
			die('invalid transaction type');
		}

		return $prev_amount;
	}
	
}