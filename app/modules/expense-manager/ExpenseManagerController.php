<?php

class ExpenseManagerController extends BaseController {

	protected $view = 'expense-manager.views.';

	protected $model_name = 'ExpenseManager';

	protected $module_name = 'expense-manager';

	public $current_user;


	public function searchExpense() {

		

		$data = Input::all();
		$search = $data['searchExpense'];
		if(Request::ajax()){
	
		$expense_list = DB::table('account')
						->join('expense','expense.account_id','=','account.id')
						->select('account.account_name','expense.*')
						->where('transaction_id', 'LIKE', '%'.$search.'%' )
						->get();
			

			return View::make($this->view.'expense-load')
						->with('expense_list', $expense_list);
		}
	}

	public function searchCash() {

		


		$data = Input::all();
		$search = $data['searchCash'];

		if(Request::ajax()) {

		$cash_list = DB::table('account')
						->join('cash_in_hand','cash_in_hand.account_id','=','account.id')
						->select('account.account_name', 'cash_in_hand.*')
						->where('transaction_id','LIKE','%'.$search.'%')
						->get();
					

		return View::make($this->view.'cash-load')
						->with('cash_list', $cash_list);

		}
	}

	public function searchDateExpense() {

		

		$data = Input::all();
		$startD = $data['start_date'];
		$endD = $data['end_date'];
		$startDate = date("Y-m-d", strtotime($startD));
		$endDate = date("Y-m-d", strtotime($endD));

		if(Request::ajax()) {
			$expense_list = DB::table('account')
								->join('expense', 'expense.account_id','=','account.id')
								->select('account.account_name', 'expense.*')
								->whereBetween('expense.created_at',[$startDate, $endDate])
								->get();


			return View::make($this->view.'expense-load')
							->with('expense_list', $expense_list);

		}		

	}

	public function searchDateCash() 
	{
		 
		 	 
		 $data = Input::all();
		 $startD = $data['start_date'];
		 $endD = $data['end_date'];
		 $startDate =date("Y-m-d", strtotime($startD));
	     $endDate = date("Y-m-d", strtotime($endD));

	       
	     if(Request::ajax()){   
	        $cash_list = DB::table('account')
							->join('cash_in_hand','cash_in_hand.account_id','=','account.id')
							->select('account.account_name','cash_in_hand.*')
	                        ->whereBetween('cash_in_hand.created_at',[$startDate, $endDate])
	                        ->get();

			return View::make($this->view.'cash-load')
							->with('cash_list', $cash_list);
			}
		}

	public function getExpenseList() {

		AccessController::allowedOrNot($this->module_name, 'can_view');

		$expense_list = DB::table('account')
							->join('expense','expense.account_id','=','account.id')
							->select('account.account_name','expense.*')
							->orderBy('id', 'DESC')
							->get();

		$string = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 1, 1);		
		$number= rand(10000,99999);
		$transaction_id = $string.$number;
		$account_type = DB::table('account')->lists('account_name', 'id');
/*
		if (Request::ajax()) {

			return View::make($this->view.'expense-load')
						->with('expense_list', $expense_list);
		}*/
			
		return View::make($this->view.'expense-list')
						->with('current_user', $this->current_user)
						->with('transaction_id', $transaction_id)
						->with('account_type', $account_type)
						->with('expense_list', $expense_list);
	}


	public function postCreateExpense() {

		AccessController::allowedOrNot($this->module_name, 'can_create');
		
		$model = new ExpenseManager;

		$validator = Validator::make(Input::all(), $model->rules);

		if($validator->fails())
		{
			return Redirect::back()
							->withErrors($validator)
							->withInput();	
		}

		
		try
		{	

			if(Input::get('account_id') == 0) 
			{
				$total_cash = DB::table('account')->select('balance')->where('id', 0)->first();

				if($total_cash)
				{
					foreach ($total_cash as $key => $value) {
						$cash_in_hand = $value;
					}

					if($cash_in_hand < Input::get('amount'))
					{
						Session::flash('error-msg', 'Insufficent cash in hand');
						return Redirect::back();
					}
					else
					{	
						$expense = new ExpenseManager;
						$expense->title 			= Input::get('title');
						$expense->transaction_id 	= Input::get('transaction_id');
						$expense->account_id		= Input::get('account_id'); 
						$expense->paid_to		    = Input::get('paid_to');
						$expense->amount			= Input::get('amount');
						$expense->payment_type 		= Input::get('payment_type');
						$expense->reference 		= Input::get('reference');
						$expense->notes 			= Input::get('notes');
						$expense->payment_date	    = Input::get('payment_date');
						$expense->pic_name			= Input::get('pic_name');

						if(Input::hasFile('pic'))
						{

						$file = Input::file('pic');
						$file = Image::make($file);

						$image = Input::file('pic');
						$name = Input::file('pic')->getClientOriginalName();
						

						$original_width = $file->width();
						$original_height = $file->height();

						$max_width = 920;
						$max_height = 1000;
						$path = public_path('expense-photos/'.$name);
						
							if($original_width > $max_width && $original_height > $max_height)
							{
								Image::make($image->getRealPath())->resize($max_width, $max_height)->save($path);
								
							}
							elseif($original_width > $max_width && $original_height <= $max_height)
							{
								Image::make($image->getRealPath())->resize($max_width, $original_height)->save($path);
								
							}
							elseif($original_width <= $max_width && $original_height > $max_height)
							{
								Image::make($image->getRealPath())->resize($original_width, $max_height)->save($path);
								
							}
							elseif($original_width == $max_width && $original_height == $max_height )
							{
								Image::make($image->getRealPath())->resize($original_width, $original_height)->save($path);
								
							}
							elseif($original_width < $max_width && $original_height < $max_width)
							{
								Image::make($image->getRealPath())->resize($original_width, $original_height)->save($path);
								
							}

						$expense->pic = $name;
						}

						$expense->save();


						$final_cash_in_hand = $cash_in_hand - Input::get('amount');
									
					
						AccountManager::where('id', 0)->update(['balance' => $final_cash_in_hand]);
						Session::flash('success-msg', 'Expense Created Successfully');
						return Redirect::back();
					}
				}
				else
				{
					Session::flash('error-msg', 'Cash in hand is not set');
					return Redirect::back();
				}

			}
			else
			{	

				$id = Input::get('account_id');
				$bal = DB::table('account')->where('id', $id)->select('balance')->first();

				foreach ($bal as $key => $value) {
						$balance = $value;
					}
			
				if($balance)
				{
					$expense = new ExpenseManager;
					$expense->title 			= Input::get('title');
					$expense->transaction_id 	= Input::get('transaction_id');
					$expense->account_id		= Input::get('account_id'); 
					$expense->paid_to		    = Input::get('paid_to');
					$expense->amount			= Input::get('amount');
					$expense->payment_type 		= Input::get('payment_type');
					$expense->reference 		= Input::get('reference');
					$expense->notes 			= Input::get('notes');
					$expense->payment_date	    = Input::get('payment_date');
					$expense->pic_name			= Input::get('pic_name');

						if(Input::hasFile('pic'))
						{

						$file = Input::file('pic');
						$file = Image::make($file);

						$image = Input::file('pic');
						$name = Input::file('pic')->getClientOriginalName();
						

						$original_width = $file->width();
						$original_height = $file->height();

						$max_width = 920;
						$max_height = 1000;
						$path = public_path('expense-photos/'.$name);
						
							if($original_width > $max_width && $original_height > $max_height)
							{
								Image::make($image->getRealPath())->resize($max_width, $max_height)->save($path);
								
							}
							elseif($original_width > $max_width && $original_height <= $max_height)
							{
								Image::make($image->getRealPath())->resize($max_width, $original_height)->save($path);
								
							}
							elseif($original_width <= $max_width && $original_height > $max_height)
							{
								Image::make($image->getRealPath())->resize($original_width, $max_height)->save($path);
								
							}
							elseif($original_width == $max_width && $original_height == $max_height )
							{
								Image::make($image->getRealPath())->resize($original_width, $original_height)->save($path);
								
							}
							elseif($original_width < $max_width && $original_height < $max_width)
							{
								Image::make($image->getRealPath())->resize($original_width, $original_height)->save($path);
								
							}

						$expense->pic = $name;
						}

					
					$amount_to_decrease = Input::get('amount');
							
					if($balance < $amount_to_decrease) 
					{
						Session::flash('error-msg','Insufficent Balance');
						return Redirect::back();
					}
					else
					{

					$final_balance =  $balance - $amount_to_decrease;

						if(AccountManager::where('id', $id)) 
						{
							AccountManager::where('id',$id)->update(['balance' => $final_balance]);	
							$expense->save();	
							Session::flash('success-msg', 'Expense created successfully');
							return Redirect::back();
						}
						else
						{
							Session::flash('error-msg','No Account Found');
							return Redirect::back();
						}
					
					}
				}
				else
				{
					Session::flash('error-msg', 'Balance is not set');
					return Redirect::back();
				}

			}	

		}	
		catch(\Exception $e)
		{	
			
			return Redirect::back()->with('error-msg',$e->getMessage());
		}
			
	}


	public function getAccountList() {
		AccessController::allowedOrNot($this->module_name, 'can_view');

		$account_list = AccountManager::all()->sortByDesc('id');
		
		return View::make($this->view.'account-list')
						->with('current_user', $this->current_user)
						->with('account_list', $account_list);
						
	}


	public function postCreateAccount() {
		AccessController::allowedOrNot($this->module_name, 'can_create');

		$validator = Validator::make(Input::all(), array(	

			'account_name'	=> 'required',
			'balance'		=> 'required|numeric|between:0,99999999.99',

			));

		if($validator->fails()) {
			return Redirect::back()->withErrors($validator)
								   ->withInput();
		}

		$account = new AccountManager;
		$account->account_name = Input::get('account_name');
		$account->balance      = Input::get('balance');
		$account->description  = Input::get('description');
		$account->save();
		Session::flash('success-msg', 'Account Created Successfully');
		return Redirect::back();
 
	}

	public function getCashList() {
		AccessController::allowedOrNot($this->module_name, 'can_view');

		$total_cash = DB::table('account')->select('balance')->where('id', 0)->first();
		
		$final_cash_in_hand = '';

		if($total_cash) {

		foreach ($total_cash as $key => $value) {
			$final_cash_in_hand = $value;
		}

		}

		$cash_list = DB::table('cash_in_hand')
							  ->join('account', 'account.id','=','cash_in_hand.account_id')
							  ->select('account.account_name','cash_in_hand.*')
							  ->orderBy('id', 'DESC')
							  ->get();

		return View::make($this->view.'cash-list')
						->with('current_user', $this->current_user)
						->with('cash_list', $cash_list)
						->with('final_cash_in_hand',$final_cash_in_hand);
						
	}

	public function getExpenseNote($id) {
		AccessController::allowedOrNot($this->module_name, 'can_view');

		$expense_detail = ExpenseManager::find($id);
		return View::make($this->view.'expense-note')
						->with('current_user', $this->current_user)
						->with('expense_detail', $expense_detail);
	}

	public function getExpenseEdit($id) {
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$expense = ExpenseManager::find($id);
		$account_type = AccountManager::lists('account_name', 'id');


		return View::make($this->view.'expense-edit')
						->with('current_user', $this->current_user)
						->with('expense', $expense)
						->with('account_type', $account_type);
	}

	public function postExpenseEdit($id) {

		AccessController::allowedOrNot($this->module_name, 'can_edit');

		/*$model = new ExpenseManager;

		$validator = Validator::make(Input::all(), $model->rules);

		if($validator->fails())
		{
			return Redirect::back()
							->withErrors($validator)
							->withInput();	
		}
*/

		$expense = ExpenseManager::find($id);
		$expense->title 			= Input::get('title'); 	
		$expense->account_id  		= Input::get('account_id'); 
		$expense->paid_to		    = Input::get('paid_to');
		$expense->amount			= Input::get('amount');
		$expense->payment_type 		= Input::get('payment_type');
		$expense->reference 		= Input::get('reference');
		$expense->notes 			= Input::get('notes');
		$expense->payment_date	    = Input::get('payment_date');
		$expense->pic_name			= Input::get('pic_name');

			
		$file_path = public_path("expense-photos/{$expense->pic}");

		if(Input::hasFile('pic'))
		{
			if(File::exists($file_path))
			{
				File::delete($file_path);
			}

		$file = Input::file('pic');
		$file = Image::make($file);

		$image = Input::file('pic');
		$name = Input::file('pic')->getClientOriginalName();
		

		$original_width = $file->width();
		$original_height = $file->height();

		$max_width = 920;
		$max_height = 1000;
		$path = public_path('expense-photos/'.$name);
		
			if($original_width > $max_width && $original_height > $max_height)
			{
				Image::make($image->getRealPath())->resize($max_width, $max_height)->save($path);
				
			}
			elseif($original_width > $max_width && $original_height <= $max_height)
			{
				Image::make($image->getRealPath())->resize($max_width, $original_height)->save($path);
				
			}
			elseif($original_width <= $max_width && $original_height > $max_height)
			{
				Image::make($image->getRealPath())->resize($original_width, $max_height)->save($path);
				
			}
			elseif($original_width == $max_width && $original_height == $max_height )
			{
				Image::make($image->getRealPath())->resize($original_width, $original_height)->save($path);
				
			}
			elseif($original_width < $max_width && $original_height < $max_width)
			{
				Image::make($image->getRealPath())->resize($original_width, $original_height)->save($path);
				
			}

		$expense->pic = $name;
		}
		
		$expense->save();

		Session::flash('success-msg', 'Expense edited successfully');
		return Redirect::route('expense-list');

	}

	public function getAccountEdit($id) {

		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$account = AccountManager::find($id);
		return View::make($this->view.'account-edit')
						->with('current_user', $this->current_user)
						->with('account', $account);
	}

	public function postAccountEdit($id) {
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		/*$redirect = Input::get('redirect_route');

		$validator = Validator::make(Input::all(), array(
			'account_name' => 'required',
			'balance'	   => 'required'
			));
		
		if($validator->fails()) {
			return Redirect::route($redirect)
							->withErrors($validator)
							->withInput();
		}
*/
		$account = AccountManager::find($id);
		$account->account_name = Input::get('account_name');
		$account->balance      = Input::get('balance');
		$account->description  = Input::get('description');
		$account->save();

		
		Session::flash('success-msg', 'Account Edited Successfully');
		return Redirect::route('accounts-list');

	}

	public function getAccountDelete($id) {

		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$account = AccountManager::find($id);
		$account->delete();
		Session::flash('success-msg', 'Account Deleted Successfully');
		return Redirect::back();

	}

	public function getIncomeType() {
		
		AccessController::allowedOrNot($this->module_name, 'can_view');

		$data = $this->getIncomeReport();

		return View::make($this->view.'income-type')
						->with('current_user', $this->current_user)
						->with('data', $data);			
		
	}


	public function getRemainingDues() {
	AccessController::allowedOrNot($this->module_name, 'can_view');
		$data = $this->getDuesReport();

		return View::make($this->view.'remaining-dues')
						->with('current_user', $this->current_user)
						->with('data', $data);			
	}

	public function getDuesReport() {
	AccessController::allowedOrNot($this->module_name, 'can_view');
		$data = BillingInvoice::select(array('invoice_balance', 'received_amount', 'id', 'related_user_group', 'class_section'))					->where('is_final','yes')
								->where('is_cleared', '!=', 'yes')
								->orderBy('related_user_group', 'ASC')
								->orderBy('class_section', 'DESC')
								->get();

		$return_data = [];
		foreach($data as $d)
		{
			if(isset($return_data[$d->related_user_group][$d->class_section]))
				$return_data[$d->related_user_group][$d->class_section] += ($d->invoice_balance - $d->received_amount);
			else
				$return_data[$d->related_user_group][$d->class_section] = ($d->invoice_balance - $d->received_amount);
		}

		return $return_data;

	}


	public function getIncomeReport() {
	AccessController::allowedOrNot($this->module_name, 'can_view');
		$billing_invoice_table = BillingInvoice::getTableName();

		$data = DB::table($billing_invoice_table)
					->where($billing_invoice_table.'.is_active', 'yes')
					->where('is_final', 'yes')
					->whereIn('invoice_type', SsmConstants::$const_billing_types['credit'])
					->select($billing_invoice_table.'.*')
					->orderBy('class_section', 'ASC')
					->get();

		$credit_notes_data = DB::table($billing_invoice_table)
							->where($billing_invoice_table.'.is_active', 'yes')
							->where('is_final', 'yes')
							->whereIn('invoice_type', ['credit_note'])
							->select($billing_invoice_table.'.*')
							->orderBy('class_section','ASC')
							->get();

		$return_data = [];
		$fee_titles = [];
		$unpaid_amount = [];
		$unpaid_amount_total = 0;
		foreach($data as $index => $d)
		{
			$json = json_decode($d->invoice_details, true);

			$unpaid_amount_total += ($d->invoice_balance - $d->received_amount);
			if(isset($unpaid_amount[$d->class_section]))
			{
				$unpaid_amount[$d->class_section] = $unpaid_amount[$d->class_section] + ($d->invoice_balance - $d->received_amount);
				
			}
			else
			{
				$unpaid_amount[$d->class_section] = ($d->invoice_balance - $d->received_amount);
			}	

			foreach($json['fees'] as $index => $fee)
			{
				$return_data[$d->class_section]['fees'][$fee['fee_title']] = isset($return_data[$d->class_section]['fees'][$fee['fee_title']]) ? $return_data[$d->class_section]['fees'][$fee['fee_title']] + $fee['fee_amount'] : $fee['fee_amount'];



				$return_data[$d->class_section]['total'] = isset($return_data[$d->class_section]['total']) ? $return_data[$d->class_section]['total'] + $fee['fee_amount'] : $fee['fee_amount'];



				$fee_titles[$fee['fee_title']] = isset($fee_titles[$fee['fee_title']]) ? $fee_titles[$fee['fee_title']] + $fee['fee_amount'] : $fee['fee_amont'];
					
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

		$credit_note = [];
		$credit_note['total'] = 0;
		foreach($credit_notes_data as $index => $d)
		{
			$credit_note[$d->class_section]['total'] = isset($credit_note[$d->class_section]['total']) ? ($credit_note[$d->class_section]['total'] + $d->invoice_balance) : $d->invoice_balance;
			$credit_note['total'] +=  $d->invoice_balance;
			
			unset($credit_notes_data[$index]);
		}
		
		return ['data' => $return_data, 'fee_titles' => $fee_titles, 'unpaid_amount' => $unpaid_amount, 'unpaid_amount_total' => $unpaid_amount_total, 'credit_note' => $credit_note];

		
	}

	public function getTransfer() {
        AccessController::allowedOrNot($this->module_name, 'can_view');


		$string = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 1, 1);		
		$number= rand(10000,99999);
		$account_type = AccountManager::lists('account_name', 'id');
		$transaction_id = $string.$number;
		return View::make($this->view.'transfer')
						->with('current_user', $this->current_user)
						->with('transaction_id', $transaction_id)
						->with('account_type', $account_type);
	}

	public function postCashTransfer() {

		AccessController::allowedOrNot($this->module_name, 'can_create');

		/*$model = new CashManager;

		$validator = Validator::make(Input::all(), $model->rules);

		if($validator->fails()) {
			return Redirect::back()
							->withInput()
							->withErrors($validator);
		}*/

		try
		{
			$cash = new CashManager;
			$cash->transaction_id 	= Input::get('transaction_id');
			$cash->account_id		= Input::get('account_id');
			$cash->date 			= Input::get('date');
			$cash->amount			= Input::get('amount');
			$cash->reference 		= Input::get('reference');
			$cash->notes 			= Input::get('notes');
			$cash->save();
				
				$bal = DB::table('account')->select('balance')->where('id',0)->first();
				$account_balance = AccountManager::where('id',Input::get('account_id'))->pluck('balance');
				

				if(!$bal) 
				{
					Session::flash('error-msg', 'Cash in Hand not found');
					return Redirect::back();
				}

				foreach($bal as $key=>$value) {
					$balance = $value;
				}
					
				if($account_balance < Input::get('amount'))
				{
					Session::flash('error-msg', 'Could not transfer amount because you donot have sufficient balance in your account. Please Check again');
					return Redirect::route('cash-list');	
				}
				else
				{
					$updated_account_balance = $account_balance - Input::get('amount');
					AccountManager::where('id',Input::get('account_id'))->update(['balance' => $updated_account_balance]);
					
				}
				$final_cash_in_hand = $balance + Input::get('amount');	
				AccountManager::where('id', 0)->update(['balance' => $final_cash_in_hand]);
				Session::flash('success-msg', 'Cash Transferred Successfully');
				return Redirect::route('cash-list');
			
		}
		catch(\Exception $e)
		{
			return Redirect::route('cash-list')->with('error-msg', $e->getMessage());
		}
	}

	public function getTransferInfo($id) {
	AccessController::allowedOrNot($this->module_name, 'can_view');
		$transfer_info = CashManager::find($id);
		return View::make($this->view.'transfer-info')
						->with('current_user', $this->current_user)
						->with('transfer_info', $transfer_info);
	}

	public function getTransferEdit($id) {

		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$cash = CashManager::find($id);
		$account_type = AccountManager::lists('account_name','id');
		return View::make($this->view.'transfer-edit')
						->with('current_user', $this->current_user)
						->with('cash', $cash)
						->with('account_type', $account_type);	
	}

	public function postTransferEdit($id) {

		AccessController::allowedOrNot($this->module_name, 'can_edit');
		/*$model = new CashManager;

		$validator = Validator::make(Input::all(), $model->rules);

		if($validator->fails()) {
			return Redirect::back()
							->withInput()
							->withErrors($validator);
		}*/

		$cash = CashManager::find($id);
		$cash->account_id = Input::get('account_id');
		$cash->date 	  = Input::get('date');
		$cash->amount 	  = Input::get('amount');
		$cash->reference  = Input::get('reference');
		$cash->notes 	  = Input::get('notes');
		$cash->save();

		Session::flash('success-msg', 'Cash updated successfully');
		return Redirect::route('cash-list');

	}

	public function getExpenseDelete($id) {

		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$expense = ExpenseManager::find($id);

		$file_path = public_path("expense-photos/{$expense->pic}");

		if(File::exists($file_path))
		{
			File::delete($file_path);
		}

		$expense->delete();
		Session::flash('success-msg','Expense deleted successfully');
		return Redirect::back();

	}

	public function getCashDelete($id) {

		AccessController::allowedOrNot($this->module_name, 'can_delete');

		$cash = CashManager::find($id);
		$cash->delete();
		Session::flash('success-msg', 'Cash deleted successfully');
		return Redirect::back();
	}
}