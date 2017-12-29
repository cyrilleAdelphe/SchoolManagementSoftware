@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Direct Invoice</h1>
@stop

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<div class="nav-tabs-custom">
    <div class="tab-content">
      <div class="tab-pane active" id="tab_1"> 
        <div class="bill-title">Invoice Number: <span class="text-red">{{$invoice_number}} </span></div>
        <form action = "{{URL::route('billing-direct-invoice-organization-post')}}" method = "post" id = "backendForm">
        <div class="row">
          
          <div class = "col-md-3">
            <div class="form-group"> 
              <label>Organization name</label>
              <select class = "form-control" name = "organization_id">
                <?php
                  $organizations = BillingDiscountOrganization::where('generate_invoice', 'yes')
                                                              ->orderBy('organization_name', 'ASC')
                                                              ->select('id', 'organization_name')
                                                              ->get();

                ?>
                @foreach($organizations as $o)
                  <option value = "{{ $o->id }}">{{ $o->organization_name }}</option>
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
            <div class="col-sm-3">
              <div class="form-group">
                <label>Fee type</label>                
                <select class = "form-control fee_type" name = "fee_type[]">
                @define $fees = BillingFee::where('is_active', 'yes')->select('fee_category', 'tax_applicable')->get();
                  <option value = "select">-- Select -- </option>
                @foreach($fees as $fee)      
                  <option value = "{{$fee->fee_category}}">{{$fee->fee_category}}</option>
                @endforeach
                </select>
                <input type = "hidden" name = "tax_applicable[]" class="tax_applicable" value = "no">
              </div>
            </div>

            <div class="col-sm-5">
              <div class="form-group">
                <label>Description</label>
                <input type = "text" class = "form-control note" name="note[]" >
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label>Amount</label>
                <input type = "number" class = "form-control fee_amount" name = "fee_amount[]" step=0.01>
              </div>
              <span class = "tax_applicable_or_not">
                
                  tax not applicable
                
              </span>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label style="color: #eee; display: block">Add more</label>
                <a href="#" class="btn btn-primary btn-flat add_field_button">Add more </a>
              </div>
            </div>

          </div>
        </div>

        <div class = "row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>
                <input type = "checkbox" name = "is_paid" value = "yes">&nbsp;&nbsp;&nbsp; Paid
              </label>
            </div>
          </div>
        </div>
        
        
        <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
        <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
        <input type = "hidden" id = 'billing-ajax-get-student-select-list' value = '{{URL::route('billing-ajax-get-student-select-list')}}'>
        <input type = "hidden" id = 'billing-ajax-get-student-fee-from-class-id-section-id-student-id' value = '{{URL::route('billing-ajax-get-student-fee-from-class-id-section-id-student-id')}}'>
        
        {{Form::token()}}
        <input type = "submit" class="btn btn-success btn-lg btn-flat submit-enable-disable" value = "Generate Invoice" related-form = "backendForm">
        </form>

      </div><!-- tab 1 ends -->


      <div class="tab-pane" id="tab_2">
        <form action = "{{URL::route('billing-direct-invoice-post')}}" method = "post" id = "backendForm2">
        <div class="bill-title">Invoice Number: <span class="text-red">{{$invoice_number}} </span></div>
        <div class="row">                        
          <div class="col-sm-6 ">
            <div class="form-group">
              <label>Name</label>
              <input type = "text" name = "student_id" class = "form-control">
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Date</label>
              <input type = 'text' name = 'issued_date' value="Show current date" class = 'form-control myDate required'>
            </div>
          </div>
        </div><!-- row ends -->

        <div id = "other_fee_row">

          <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label>Fee type</label>                
                <select class = "form-control fee_type" name = "fee_type[]">
                @define $fees = BillingFee::where('is_active', 'yes')->select('fee_category', 'tax_applicable')->get();
                  <option value = "select">-- Select --</option>
                @foreach($fees as $fee)      
                  <option value = "{{$fee->fee_category}}">{{$fee->fee_category}}</option>
                @endforeach
                </select>
                <input type = "hidden" name = "tax_applicable[]" class = "tax_applicable" value = "no">
              </div>
            </div>

            <div class="col-sm-5">
              <div class="form-group">
                <label>Description</label>
                <input type = "text" class = "form-control note" name="note[]" >
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label>Amount</label>
                <input type = "text" class = "form-control fee_amount" name = "fee_amount[]" >
              </div>
              <span class = "tax_applicable_or_not">
                not applicable
              </span>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label style="color: #eee; display: block">Add more</label>
                <a href="#" class="btn btn-primary btn-flat add_other_field_button">Add more </a>
              </div>
            </div>

          </div>

        </div>

        <div class = "row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>
                <input type = "checkbox" name = "is_paid" value = "yes"> &nbsp;&nbsp;&nbsp;Paid
              </label>
            </div>
          </div>
        </div>
        
        <input type = "submit" class="btn btn-success btn-lg btn-flat submit-enable-disable" value = "Generate Invoice" related-form = "backendForm2">
          {{Form::token()}}
        </form>

      </div><!-- tab 2 ends -->
    </div><!-- tab content ends -->
</div><!-- nav-tab ends -->

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript">
      var fee_type = '{{json_encode($fees)}}';
      fee_type = jQuery.parseJSON(fee_type);

      $(document).on('change', '.fee_type', function(e)
      {
        var currentElement = $(this);
        var val = $(this).val();
        console.log(fee_type);
        $.each( fee_type, function( key, value ) 
        {
          if($(this)[0].fee_category == val)
          {
            currentElement.parent().find('.tax_applicable').val($(this)[0].tax_applicable);
            
            if($(this)[0].tax_applicable == 'yes')
            {
              currentElement.parent().parent().parent().find('.tax_applicable_or_not').html('tax applicable')
            }
            else
            {
              currentElement.parent().parent().parent().find('.tax_applicable_or_not').html('not tax applicable')
            }

          }
        });


      });
      //tax_applicable
      $(document).on('click', '.add_field_button', function(e) {
         
              e.preventDefault();
              
                  var html = $(this).parent().parent().parent().parent().html();
                  $('#fee_row').append('<div>' + html + '</div>');
                  $(this).removeClass('add_field_button btn-primary');
                  $(this).addClass('remove_field_button btn-danger');
                  $(this).text('Remove');
          
          });

      $(document).on('click', '.add_other_field_button', function(e) {
         
              e.preventDefault();
              
                  var html = $(this).parent().parent().parent().html();
                  $('#other_fee_row').append('<div class="row">' + html + '</div>');
                  $(this).removeClass('add_other_field_button btn-primary');
                  $(this).addClass('remove_field_button btn-danger');
                  $(this).text('Remove');
          
          });

      $(document).on('click', '.remove_field_button', function(e) {
         
              e.preventDefault();
              
                  var html = $(this).parent().parent().parent().remove();
          
          });
          
    </script>

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