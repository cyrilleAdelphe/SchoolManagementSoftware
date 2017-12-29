

@if(CALENDAR=='BS')
  @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
  @define $months = HelperController::getNepaliMonths();
@else
  @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
  <?php 
  $months = array_map(function($i) {
    return DateTime::createFromFormat('m', $i)->format('F');
  }, range(1, 12));
  ?>
@endif
<div class="row">
  <div class="col-sm-6" style="margin-bottom:15px">
    <a class="btn btn-default btn-flat" target="_blank" onclick="printDiv('printableArea')" href="#">
        <i class="fa fa-print"></i>
        Print
    </a>
    <a href = "{{URL::route('fee-mass-print', array(Input::get('class_id'), Input::get('section_id'), Input::get('month')))}}">Mass Print</a>
  </div>
   <div class="col-sm-6 backBtn" style="margin-bottom:15px">
	    <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
	  </div>
</div><!-- print row ends -->

<div id="printableArea">
	<div class="row">
	  <div class="col-sm-6">
	    <h4 class="text-red">
	    	{{Classes::find($class_id)->class_name}} {{Section::find($section_id)->section_name}} 
	    	<small class="text-green">
	    		{{$months[$month-1]}} of {{AcademicSession::where('id', $academic_session_id)->first()->session_name}}
	    	</small>
	    </h4> 
	    <h4>
	    	<small class="text-red">
	    		Defaulters: 
	    		{{ count(array_filter($payments, function ($payment) {
	    				return $payment->is_paid == 'no';
	    			}))}} / {{ count($payments) }}
	    	</small>
	    </h4>
	  </div>
	</div> <!-- row ends -->
	<div class="row">
	  <div class="col-sm-12">
	      <table id="pageList" class="table table-bordered table-striped last-hide">
	        <thead>
	          <tr>
	            <th>SN</th>
	            <th>Name</th>
	            <th>Total Fee</th>
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
	            <td>{{$payment->student_name}}</td>
	            <td>{{$payment->fee_amount}}</td>
	            <td>{{$payment->received_amount}}</td>
	            <td>
		          	@if($payment->is_paid=='yes')
	                <span class="text-green">Paid</span>
	              @else
	                @if ($payment->received_amount)
	                  <span class="text-yellow">Partially Paid</span>
	                @else
	                  <span class="text-red">Unpaid</span>
	                @endif
	              @endif
				      </td>
				      <td>
				      	<a href="{{URL::route('fee-update-payment-get').'?student_id='.$payment->student_id.'&month='.$payment->month}}" data-toggle="tooltip" title="View detail" href="fee-individual.php" class="btn btn-info btn-flat">
                  <i class="fa fa-fw fa-eye"></i>
                </a>
                @if($payment->is_paid=='no')
                <a href="{{URL::route('fee-defaulter-notification').'?student_id='.$payment->student_id.'&month='.$payment->month.'&_token='.csrf_token()}}" data-toggle="tooltip" title="Send Notification" href="fee-individual.php" class="btn btn-info btn-flat bg-purple">
                  <i class="fa fa-fw fa-info"></i>
                </a>
                @endif
              </td>
				    </tr>
				    @endforeach
	          
	        </tbody>
	      </table>
	  </div>
	</div><!-- row ends -->
	
</div><!-- printable div ends -->

<script>
  function printDiv(divName) {
		var printContents = document.getElementById(divName).innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
	}
</script>