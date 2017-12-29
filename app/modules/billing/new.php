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

		$prev_amount = BillingTransaction::getBalanceAmount($transaction_type, $transaction_amount, $related_user_id, $related_user_group, $related_invoice_id);

		if($prev_amount < 0)
		{
			$extra_cash = -1 * $prev_amount;
			$invoice = BillingInvoice::where('id', $related_invoice_id)->first();

			$amount_to_pay = $invoice->invoice_balance - $invoice->$received_amount;

			if($extra_cash < $amount_to_pay)
			{
				//store as partially paid
				$invoice->received_amount += $extra_cash;
				$invoice->is_cleared = 'partial';
				//$extra_cash = 0;
				$transaction_amount -= $extra_cash;
			}
			elseif($extra_cas == $amount_to_pay)
			{
				$invoice->received_amount += $extra_cash;
				$invoice->is_cleared = 'yes';
				$extra_cash = 0;
				$transaction_amount -= $extra_cash;
			}
			else
			{
				$invoice->received_amount += $extra_cash;
				$invoice->is_cleared = 'yes';
				$extra_cash -= $invoice->received_amount;
				$data_to_store['balance_amount'] = 
			}

			$data_to_store['transaction_type'] = 'prev_balance';
			$data_to_store['transaction_amount'] = $transaction_amount;
		}
		else
		{
			$data_to_store['transaction_date'] = $transaction_date;
			$data_to_store['transaction_amount'] = $transaction_amount;
			$data_to_store['related_user_id'] = $related_user_id;
			$data_to_store['related_user_group'] = $related_user_group;
			$data_to_store['transaction_type'] = $transaction_type;
			$data_to_store['description'] = $description;
			$data_to_store['transaction_no'] = BillingTransaction::generateTransactionNo();
			$data_to_store['balance_amount'] = 


			
			$data_to_store['related_invoice_id'] = $related_invoice_id;
			//$data_to_store['crea']
			$baseController = new BaseController;
			$created_by_updated_by = $baseController->getCreatedByUpdatedBy();
			$data_to_store['created_by'] = $created_by_updated_by['created_by'];
			$data_to_store['updated_by'] = $created_by_updated_by['updated_by'];

			$id = BillingTransaction::create($data_to_store)->id;
		}
		
		return $id;

		/*
			if(in_array($transaction_type, SsmConstants::$const_billing_types['credit']))
			{
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
		*/
	}

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

		return $prev_amount;
	}

	/*public static function checkIfExtraMoneyAlreadyPaid($group, $user_id, $amount_to_be_paid)
	{
		//if any money already given then get the remaining amount
				$prev_balance = 0;
				$prev_balance = DB::table($transaction_table)
									->where('related_user_id', $input['received_from'])
									->where('related_user_id', $input['student_id'])
									->orderBy('id', 'DESC')
									->take(1)
									->get();

				if(count($prev_balance))
				{
					$prev_balance = $prev_balance[0]->balance_amount < 0 ? (-1* $prev_balance[0]->balance_amount) : 0;
				}
				else
				{
					$prev_balance = 0;
				}
	}*/
	
}