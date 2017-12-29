@extends('backend.superadmin.main')

@section('page-header')
  <h1>Fee Print Organization</h1>
@stop

@section('custom-css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet" type="text/css" />

<!-- <link href="{{asset('sms/assets/css/nepali.datepicker.v2.2.min.css')}}" rel="stylesheet" type="text/css" /> -->
<link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />

@stop

@section('content')
            
            <form method="GET" action="{{URL::route('billing-fee-print-organization-list')}}">
              <div class="row">
                
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Session</label>
                    @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();

                  <select class = "academic_session_id form-control" id = "academic_session_id" name = "academic_session_id">
                  @foreach($sessions as $s)
                    <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                  @endforeach
                  </select>
                  </div>
                </div>  

                <div class="col-sm-2">
                  <div class="form-group">
                  <label>Date</label>
                    @define $date = date('Y-m-d')
                    <input type="text" id="englishDate" class="form-control myDate" value="" name="issued_date"/>
                  </div>
                </div>              
                
                <div class="col-sm-2">
                <div class = 'form-group @if($errors->has("organization_id")) {{"has-error"}} @endif'>
                    <label for = 'organization_id'>Organization</label>
                    <?php $organizations = BillingDiscountOrganization::where('is_active', 'yes')
                                                            ->select('id', 'organization_name')
                                                            ->lists('organization_name', 'id'); ?>

                    <select class="form-control" name = "organization_id" id="organization_id">
                    @foreach($organizations as $id => $o)
                    <option value = "{{$id}}">{{$o}}</option>
                    @endforeach
                    </select>
                    <span class = 'help-block'>@if($errors->has('organization_id')) {{$errors->first('organization_id')}} @endif</span>
                </div>
                </div>

                 <div class="col-sm-1">
                  <div class="form-group">
                  <label style="color: #fff">Show</label>
                   <button type="submit" class="btn btn-success btn-flat" id = "show-statement-button">Show</button>
                  </div>
                </div>

 
              </div> <!-- row ends -->
              </form>
              
              
  
@stop

@section('custom-js')

<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script type="text/javascript" src="{{asset('sms/assets/js/lity.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<!-- this is for english <script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}" type="text/javascript"></script> -->
<!--this is for nepali <script src="{{asset('sms/assets/js/nepali.datepicker.v2.2.min.js')}}" type="text/javascript"></script> -->
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}" type="text/javascript"></script>

<script>
$(document).ready(function()
      {

        /* this is for english billing $('.myDate').daterangepicker({
            locale: {
              format: 'YYYY-MM-DD'
            },
            autoUpdateInput: true,
            singleDatePicker: true,
            showDropdowns: true,

        }, 
        function(start, end, label) {
            var years = moment().diff(start, 'years');
        });

        $('.myDate').on('apply.daterangepicker', function(ev, picker){

          $(this).val(picker.startDate.format('YYYY-MM-DD'));
         
        });

        
        $('#englishDate').change(function()
        {
          getMonthFromEnglishDate($('#englishDate').val());
        });
        */

        /* This is for nepali billing 
        $('.hidNep').val(AD2BS($('#englishDate').val()));
        $('#month').val(getMonthFromNepaliDate($('#nepaliDate').val()));
        $('#nepaliDate').nepaliDatePicker({
        ndpEnglishInput: 'englishDate',
        onChange: function()
        {
          
        }
        });
      */

      $('.myDate').daterangepicker({
            locale: {
              format: 'YYYY-MM-DD'
            },
            autoUpdateInput: true,
            singleDatePicker: true,
            showDropdowns: true,

        }, 
        function(start, end, label) {
            var years = moment().diff(start, 'years');
        });

        $('.myDate').on('apply.daterangepicker', function(ev, picker){

          $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    });
</script>

@stop