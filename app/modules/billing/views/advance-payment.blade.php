@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Advance Payment</h1>
@stop

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab_1" data-toggle="tab">To Students</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab_1"> 
        <div class="bill-title">Invoice Number: <span class="text-red">{{$invoice_number}} </span></div>
        <form action = "{{URL::route('advance-billing-create-post')}}" method = "post" id = "backendForm">
        <div class="row">
          
          <div class = "col-md-3">
            <div class="form-group"> 
              @define $session = AcademicSession::where('is_active', 'yes')->select('id', 'session_name', 'is_current')->get();
              <label>Session</label>
              <select class="form-control" name = "academic_session_id" id = "academic_session_id">
                @foreach($session as $s)
                <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class = "col-md-3">
            <div class="form-group"> 
              <label>Student name</label>
              <input type = "text" class = "auto form-control">
              <input type = "hidden" name = "student_id" class = "auto-student-id" id ="student_id">
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Date</label>
              <input type = 'text' name = 'issued_date' value="Show current date" class = 'form-control myDate required'>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <input type = "hidden" name = "invoice_number" value = "{{$invoice_number}}">
            </div>
          </div>
        </div><!-- row ends -->

        <div class = "row">
          <p>* All Fees are tax exclusive </p>
        </div>
        <div id = "fee_row">
          <div class="row">
            
            <div class="col-sm-5">
              <div class="form-group">
                <label>Description</label>
                <input type = "text" class = "form-control note" name="note" >
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label>Amount</label>
                <input type = "number" class = "form-control fee_amount" name = "fee_amount" step=0.01 required>
              </div>
            </div>

            
          </div>
        </div>
        
        <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
        <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
        <input type = "hidden" id = 'billing-ajax-get-student-select-list' value = '{{URL::route('billing-ajax-get-student-select-list')}}'>
        <input type = "hidden" id = 'billing-ajax-get-student-fee-from-class-id-section-id-student-id' value = '{{URL::route('billing-ajax-get-student-fee-from-class-id-section-id-student-id')}}'>
        
        {{Form::token()}}
        <input type = "submit" class="btn btn-success btn-lg btn-flat submit-enable-disable" value = "Generate Receipt" related-form = "backendForm">
        </form>

      </div><!-- tab 1 ends -->
    </div><!-- tab content ends -->
</div><!-- nav-tab ends -->

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
    
     <script type="text/javascript">
        $(document).on('keyup.autocomplete', '.auto', function()
        {
          $(this).autocomplete({select: function( event, ui ) 
                  {
                   // console.log(ui);
                   $(this).parent().find('.auto-student-id').val(ui.item.id);
                    //console.log(event);
                  },
          source: "{{URL::route('ajax-student-id-autocomplete')}}",
          minLength: 3
          
          });
        });  


        $(function() {
         
          $('.myDate').daterangepicker(
          {
              "singleDatePicker": true,
              locale: {
                format: 'YYYY-MM-DD'
              },
              startDate: '{{date('Y-m-d')}}',
          }, 
          function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
          });
        });
    </script>
    <script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
    <script>
    $(document).ready(function(){
      $('input').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue',
        increaseArea: '20%' // optional
      });
    });
    </script>
@stop