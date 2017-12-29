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

@if($status=='success')
<div class="row">
  <div class="col-sm-6" style="margin-bottom:15px">
    <a class="btn btn-default btn-flat" target="_blank" onclick="printDiv('printableArea')" href="#">
        <i class="fa fa-print"></i>
        Print
    </a>
  </div>
  <div class="col-sm-6 backBtn " style="margin-bottom:15px">
	    <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
	  </div>
</div><!-- print row ends -->
<div id="printableArea">
	<div class="row">
	  <div class="col-sm-6">
	    <h4 class="text-red">{{$student->student_name}} - {{$student->class_name}} {{$student->current_section_code}} <small class="text-green">{{$months[$payment->month-1]}} of {{AcademicSession::where('id', $payment->academic_session_id)->first()->session_name}}</small></h4> 
	  </div>
	</div> <!-- row ends -->

	<div class="row">
	  <div class="col-sm-12">
	      <table id="pageList" class="table table-bordered table-striped">
	        <thead>
	          <tr>
	            <th>SN</th>
	            <th>Title</th>
	            <th>Amount</th>
	          </tr>
	        </thead>
	        <tbody>
	        	@define $i=1
	          <tr>
	            <td>{{$i++}}</td>
	            <td>Monthly fee</td>
	            <td>{{$monthly_fee->amount}}</td>
	          </tr>

	          @if($examination_fee && $examination_fee->amount)
			      <tr>
	          	<td>{{ $i++ }}</td>
	          	<td>{{ $examination_fee->exam_name }}</td>
				  		<td>{{ $examination_fee->amount }}</td>
			  		</tr>
			  		@endif

	          @foreach($misc_class_fees as $misc_class_fee)
	          <tr>
	          	<td>{{$i++}}</td>
	          	<td>{{$misc_class_fee->title}}</td>
				  		<td>{{$misc_class_fee->amount}}</td>
			  		</tr>
			      @endforeach

			      @foreach($misc_student_fees as $misc_student_fee)
	          <tr>
	          	<td>{{$i++}}</td>
	          	<td>{{$misc_student_fee->title}}</td>
				  		<td>{{$misc_student_fee->amount}}</td>
			  		</tr>
			      @endforeach

			      @if($transportation_fee->amount)
			      <tr>
	          	<td>{{$i++}}</td>
	          	<td>Transportation Fee</td>
				  		<td>{{$transportation_fee->amount}}</td>
			  		</tr>
			  		@endif

			  		@if($hostel_fee->amount)
			      <tr>
	          	<td>{{$i++}}</td>
	          	<td>Hostel Fee</td>
				  		<td>{{$hostel_fee->amount}}</td>
			  		</tr>
			  		@endif

			  		@foreach($scholarships as $scholarship)
			  		<tr>
			  			<td>{{$i++}}</td>
	          	<td>{{ucfirst($scholarship->type)}} Scholarship</td>
				  		<td>{{$scholarship->amount}}</td>
			  		</tr>
			  		@endforeach

			  		@foreach($taxes as $tax)
	          <tr>
	          	<td>{{ $i++ }}</td>
	          	<td>{{ ucfirst($tax->type) }} Tax</td>
				  		<td>{{ $tax->amount }}</td>
				  	</tr>
	          @endforeach 
	          
	        </tbody>
	      </table>
	  </div>
	</div><!-- row ends -->

	<div class="row">
	  <div class="col-sm-4 col-sm-offset-7">
	    <table class="table">
	      <tr>
	        <td><strong>Total :</strong></td>
	        <td>{{$payment->fee_amount}}</td>
	      </tr>
	      <tr>
	        <td><strong>Received :</strong></td>
	        <td>{{$payment->received_amount}}</td>
	      </tr>
	      <tr>
	      	<td><strong>Last Updated :</strong></td>
	      	<td>
	      		<?php
	      		if (CALENDAR == 'BS')
	      		{
	      			echo HelperController::formatNepaliDate(
	      				(new DateConverter)->ad2bs(
	      					substr($payment->updated_at, 0, 10)
	      				)
	      			) . ' ' .
	      			DateTime::createFromFormat('Y-m-d H:i:s', $payment->updated_at)->format('g:i A');
	      		}
	      		else
	      		{
	      			echo DateTime::createFromFormat('Y-m-d H:i:s', $payment->updated_at)->format('d F Y g:i A');
	      		}
	      		?>
	      	</td>
	      </tr>
	      <tr>
	        <td><strong>Current month dues :</strong></td>
	        <td>{{$payment->fee_amount - $payment->received_amount}}</td>
	      </tr>
	      <tr>
	      	<td><strong>Total dues:</strong></td>
	      	<td>{{ $total_dues }}</td>
	      </tr>
	      <tr>
	        <td><strong>Status :</strong></td>
	        @if ($payment->is_paid == 'yes')
	        <td><span class="text-green">Paid</span></td>
	        @elseif ($payment->received_amount != 0)
	        <td><span class="text-yellow">Partially Paid</span></td>
	        @else
	        <td><span class="text-red">Unpaid</span></td>
	        @endif
	      </tr>
	    </table>
	  </div>
	</div><!-- row ends -->
</div><!-- printable div ends -->
@else
	{{$msg}}
@endif




<script>
  function printDiv(divName) {
		var printContents = document.getElementById(divName).innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
	}
</script>