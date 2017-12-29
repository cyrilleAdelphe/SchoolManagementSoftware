@if(CALENDAR=='BS')
  @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
  @define $months = $months = HelperController::getNepaliMonths();
@else
  @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
  @d<?php 
  $months = array_map(function($i) {
    return DateTime::createFromFormat('m', $i)->format('F');
  }, range(1, 12));
  ?>
@endif

<div class="row">
  <div class="col-sm-6">
    <h4 class="text-red">{{$student->student_name}} - {{$student->class_name}} {{$student->current_section_code}} <small class="text-green">{{AcademicSession::where('id', $academic_session_id)->first()->session_name}}</small></h4> 
  </div>
  <div class="col-sm-6 backBtn" style="margin-bottom:15px">
    <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
  </div>
</div> <!-- row ends -->
<div class="row">
  <div class="col-sm-12">
      <table id="pageList" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>SN</th>
            <th>Month</th>
            <th>Amount</th>
            <th>Received</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        	@define $i=1
        	@foreach($payments as $payment)
        	<tr>
            <td>{{$i++}}</td>
            <td>{{$months[$payment->month-1]}}</td>
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
          </tr>
          @endforeach
          
        </tbody>
      </table>
  </div>
</div><!-- row ends -->