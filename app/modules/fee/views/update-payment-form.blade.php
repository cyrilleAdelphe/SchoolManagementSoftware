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
    <div class="col-sm-12">
      <div class="main-head">
        {{$student->student_name}} - <small>{{$student->class_name}} {{$student->current_section_code}}</small>
      </div>
      <div class="text-red">
        Last updated by : {{$payment->updated_by}} at 
        <span class="text-green">
          {{DateTime::createFromFormat('Y-m-d H:i:s', $payment->updated_at)->format('d F Y')}}
        </span>
      </div>
      <br/><br/>
    </div>
  </div>

  <form method="post" action="{{URL::route('fee-update-payment-post')}}">
    <div class="row">
      <div class="col-sm-6">
    		<div class="form-group">
    			<label>Monthly:</label>
          <input class="form-control" type="text" name="monthly_fee" value="{{$monthly_fee->amount}}" />
    			<input type="hidden" name="monthly_fee_id" value="{{$monthly_fee->id}}" />
    		</div>

        @if($examination_fee)
        <div class="form-group">
          <label>{{ $examination_fee->exam_name }}:</label>
          <input class="form-control" type="text" name="examination_fee" value="{{$examination_fee->amount}}">
          <input type="hidden" name="examination_fee_id" value="{{$examination_fee->id}}">
        </div>
        @endif

        @foreach($misc_class_fees as $misc_class_fee)
    		<div class="form-group">
    			<label>{{$misc_class_fee->title}}:</label>
          <input class="form-control"  type="text" name="misc_class_fee[]" value="{{$misc_class_fee->amount}}" />
          <input type="hidden" name="misc_class_fee_id[]" value="{{$misc_class_fee->id}}" />
          <input type="hidden" name="misc_class_fee_title[]" value="{{$misc_class_fee->title}}" />
    		</div>
        @endforeach

        @foreach($misc_student_fees as $misc_student_fee)
        <div class="form-group">
          <label>{{$misc_student_fee->title}}:</label>
          <input class="form-control"  type="text" name="misc_student_fee[]" value="{{$misc_student_fee->amount}}" />
          <input type="hidden" name="misc_student_fee_id[]" value="{{$misc_student_fee->id}}" />
          <input type="hidden" name="misc_student_fee_title[]" value="{{$misc_student_fee->title}}" />
        </div>
        @endforeach

        <div class="form-group">
          <label>Transportation Fee:</label>
          <input class="form-control"  type="text" name="transportation_fee" value="{{$transportation_fee->amount}}" />
          <input type="hidden" name="transportation_fee_id" value="{{$transportation_fee->id}}" />
        </div>

        <div class="form-group">
          <label>Hostel:</label>
          <input class="form-control"  type="text" name="hostel_fee" value="{{$hostel_fee->amount}}" />
          <input type="hidden" name="hostel_fee_id" value="{{$hostel_fee->id}}" />
        </div>

        @if(isset($scholarships['monthly']))
        <div class="form-group">
          <label>Monthly Scholarship:</label>
          <input class="form-control"  type="text" name="scholarship[]" value="{{ ScholarshipMonthly::find($scholarships['monthly'])->amount }}" />
          <input type="hidden" name="scholarship_id[]" value="{{ $scholarships['monthly'] }}" />
        </div>
        @endif

        @if(isset($scholarships['transportation']))
        <div class="form-group">
          <label>Transportation Scholarship:</label>
          <input class="form-control"  type="text" name="scholarship[]" value="{{ ScholarshipMonthly::find($scholarships['transportation'])->amount }}" />
          <input type="hidden" name="scholarship_id[]" value="{{ $scholarships['transportation'] }}" />
        </div>
        @endif

        @if(isset($scholarships['hostel']))
        <div class="form-group">
          <label>Hostel Scholarship:</label>
          <input  class="form-control"  type="text" name="scholarship[]" value="{{ ScholarshipMonthly::find($scholarships['hostel'])->amount }}" />
          <input type="hidden" name="scholarship_id[]" value="{{ $scholarships['hostel'] }}" />
        </div>
        @endif

        @foreach($taxes as $tax)
        <div class="form-group">
          <label>{{ ucfirst($tax->type) }} Tax:</label>
          <input class="form-control"  type="text" name="tax_amount[]" value="{{ $tax->amount }}" />
          <input type="hidden" name="tax_id[]" value="{{ $tax->id }}" />
        </div>
        @endforeach      		
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <h3>Total: <span class="text-red"> {{$payment->fee_amount}} </span></h3>            
        </div>
        <div class="form-group @if($errors->has('received_amount')) has-error @endif">
          <label>Received:</label>
          <input class="form-control"  type="text" name="received_amount" value="{{Input::old('received_amount')?Input::old('received_amount'):$payment->received_amount}}" />
          <span class = 'help-block'>@if($errors->has('received_amount')) {{$errors->first('received_amount')}} @endif</span>
        </div>
        <input type="hidden" name="payment_id" value="{{$payment->id}}" />

        <input type="hidden" name="student_id" value="{{$payment->student_id}}" />

        <input type="hidden" name="academic_session_id" value="{{$payment->academic_session_id}}" />

        <input type="hidden" name="month" value="{{$payment->month}}" />

        <input type="hidden" name="payment_amount" value="{{$payment->fee_amount}}" />

        <input type="hidden" name="is_paid" value="{{$payment->is_paid}}" />

        {{Form::token()}}
        <button class="btn btn-success btn-lg btn-flat" type="submit" @if(!AccessController::checkPermission('fee', 'can_edit,can_create')) disabled @endif>Update Payment</button>
        <a class="btn btn-primary btn-lg btn-flat" href="{{URL::route('fee-fee-individual-get').'?academic_session_id='.$payment->academic_session_id.'&student_id='.$student->student_id.'&month='.$payment->month}}">View Receipt</a>
      </div>
    </div><!-- row ends -->
  </form>

  <div class="row">
  </div>

  @if (count($previous_dues))
    <div class="row">
      <div class="col-sm-12">
        <div class="main-head">
          Previous Dues
        </div>
      </div>
    </div>

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
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @define $i=1
              @foreach($previous_dues as $payment)
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
  @endif
  <div class="row">
    <div class="col-sm-12">
      <div class="main-head">
        Total Dues: {{ $total_dues }}
      </div>
    </div>
  </div>
@else
	{{$msg}}
@endif
  
