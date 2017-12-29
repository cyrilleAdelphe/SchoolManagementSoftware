@if(CALENDAR=='BS')
  @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
  @define $months = array('Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra');
@else
  @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
  @define $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December')
@endif
@if($status=='success')
<div class="row">
  <div class="col-sm-12">
      <table id="pageList" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>SN</th>
            <th>Year and Month</th>
            <th>Amount</th>
            <th>Received</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        	@define $i=1
        	@foreach($payments as $payment)
        	<tr>
            <td>{{$i++}}</td>
            <td>{{$payment->year_in_bs}} - {{$months[$payment->month_in_bs-1]}}</td>
            <td>{{$payment->invoice_balance}}</td>
            <td>{{$payment->received_amount}}</td>
            <td>
            	@if($payment->is_paid=='yes')
			        <span class="text-green">Paid</span>
			        @else
			        <span class="text-red">Unpaid</span>
			        @endif
            </td>
            <td>
              <a id="view_payment" data-toggle="tooltip" href="{{ URL::route('show-invoice-from-invoice-number', $payment->invoice_number)}}?financial_year={{$payment->financial_year}}" class="btn btn-info btn-flat" data-original-title="View detail" @if(!AccessController::checkPermission('billing', 'can_view')) disabled @endif>
            		<i class="fa fa-fw fa-eye"></i>
            	</a>
            </td>
          </tr>
          @endforeach
          
        </tbody>
      </table>
  </div>
</div><!-- row ends -->
@else
	{{$msg}}
@endif