@extends('backend.'.$role.'.main')

@section('custom-css')
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
@stop

@section('page-header')
  <h1>Billing - Receive Payment</h1>
@stop

@section('content')

  <form action = "{{URL::route('billing-recieve-payment-post')}}" method = "post" id = "backendForm">

              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Receipt From</label>
                    <select id="payType" class="form-control" name = "received_from">
                      <option value="student">Student</option>
                      <option value="organization">Organization</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Date</label>
                    <input type = 'text' name = 'daterange' value="Show current date" class = 'form-control myDate required'>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Receipt number</label>
                    <input class="form-control" value="{{$invoice_number}}" disabled />
                  </div>
                </div>
              </div> <!-- row ends -->
              <div class="studentAdditional" style=" display: block;">
                <h4 class="text-green">Choose student</h4>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label>Session</label>
                       {{ HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
                          $selected = 
                            Input::has('academic_session_id') ?
                            Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])
                        }}
                    <input type = "hidden" name = "student_id" id = "val_student_id">
                    </div>
                  </div>
                  <div class="col-sm-2 auto-off-block">
                    <div class="form-group">
                      <label>Class</label>
                      <select class="form-control" id="class_id" name = "class_id">
                      <option value="0">-- Select Session First --</option>
                    </select>
                    </div>
                  </div>
                  <div class="col-sm-2  auto-off-block">
                    <div class="form-group">
                      <label>Section</label>
                        <select class="form-control" id="section_id" name = "section_id" disabled>
                          <option value="">-- Select Class First --</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-sm-3  auto-off-block">
                    <div class="form-group">
                      <label>Student</label>
                      <select id="student_id" class="form-control" disabled >
                        <option value = "0">-- Select Section First --</option>
                      </select>
                    </div>
                  </div>

                  <div class = "col-sm-3 auto-on-block" style = "display:none;">
                    <div class="form-group">
                      <label>Type student name</label>
                      <input type = "text" class = "auto form-control">
                    </div>
                  </div>                  
                  <div class = "col-sm-2">
                    <div class="form-group">
                      <label style="color: #fff">Search type</label>
                      <a href = "#" id = "toggle-button" class = "btn btn-primary auto-off btn-flat">Switch search type</a>
                    </div>
                  </div>
                </div><!-- row ends -->
              </div><!-- studentAdditional ends --> 

              <div class = "organizationAdditional" style="display:none">
                @define $organizations = BillingDiscountOrganization::lists('organization_name', 'id')
                <div class = "row">
                  <div class = "col-md-3">
                    <div class = "form-group">
                      <select name = "organization_id" class = "form-control" id = "organization_id">
                        <option value = "-1">-- Select Organization --</option> 
                        @foreach($organizations as $organization_id => $organization_name)
                        <option value = "{{$organization_id}}">{{$organization_name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
                
              <div id = "display_unpaid_invoices">
              </div>

              <table  class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Receipt mode</th>
                    <th class="col-sm-2">Chq/Deposit Number</th>
                    <th class="col-sm-2">Amount</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <select id="receiptMode" class="form-control" name = "receipt_mode" >
                        <option value="cash">Cash</option>
                        <option value = "cheque">Cheque</option>
                        <option value = "bank_deposit" selected>Bank deposit</option>
                        <option value = "debit_note">Debit Note</option>
                      </select>
                    </td>
                    <td><input name="number" class="form-control" /></td>
                    <td><input type = "text" step = "0.01" name="amount" class="form-control" /></td>
                    <td><input name="description" class="form-control" /></td>
                  </tr>
                </tbody>
              </table>
              
              <span class = "error">*Note: if discount provided the selected invoices will be changed as paid.</span>
              <table class = "table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Flat Discount Amount</th>
                    <th>Flat Discount Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type = "number" step=0.01 name = "flat_discount_amount"></td>
                    <td><input type = "text" name = "flat_discount_description"></td>
                  </tr>
                </tbody>
              </table>
              
              <br/>
              <input type = "submit" name = "pay" class = "btn btn-danger btn-flat btn-lg submit-enable-disable" related-form = "backendForm" value = "Submit">
              <input type = "submit" name = "save_and_generate" class="btn btn-lg btn-flat btn-success submit-enable-disable" related-form = "backendForm" value = "Save and Generate Receipt">
  {{Form::token()}}
  </form>


              <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
              <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
              <input type = "hidden" id = 'billing-ajax-get-student-select-list' value = '{{URL::route('billing-ajax-get-student-select-list')}}'>
              <input type = "hidden" id = 'billing-ajax-get-student-remaining-due-view' value = '{{URL::route('billing-ajax-get-student-remaining-due-view')}}'>
              
@stop

@section('custom-js')
<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">

    $(function() {
          updateClassList();
          updateSectionList();
          updateStudentList();
          updateRemainingDueList(null);

      $('#academic_session_id').change(function(e)
      {
        updateClassList();
        updateSectionList();
        updateStudentList();
        updateRemainingDueList(null);
      });

      $('#class_id').change(function(e)
      {
        updateSectionList();
        updateStudentList();
        updateRemainingDueList(null);
      });

      $('#section_id').change(function(e)
      {
        updateStudentList();
        updateRemainingDueList(null);
      });

      $('#organization_id').change(function(e)
      {
        updateOrganizationDueList($(this).val());
      })

      $('#payType').change(function(e)
      {
        var val = $(this).val();

        if(val == 'student')
        {
          $('#display_unpaid_invoices').css('display', 'block');
        }
        else if(val == 'other')
        {
          $('#display_unpaid_invoices').css('display', 'none');
        }
        
      });

      $('#student_id').change(function(e)
      {
        updateRemainingDueList(null);
      });

      $(document).on('click', '.auto-off', function(e)
      {
        console.log('off');
        e.preventDefault();
        $('.auto-off-block').css('display', 'none');
        $('.auto-on-block').css('display', 'block');
        $(this).removeClass('auto-off');
        $(this).addClass('auto-on');
        updateRemainingDueList();

      });

      $(document).on('change', '#student_id', function(e)
      {
        e.preventDefault();
        $('#val_student_id').val($(this).val());
      });

      $(document).on('click', '.auto-on', function(e)
      {
        console.log('on');
        e.preventDefault();
        $('.auto-off-block').css('display', 'block');
        $('.auto-on-block').css('display', 'none');
        $(this).removeClass('auto-on');
        $(this).addClass('auto-off');
        updateRemainingDueList();

      });

      function updateClassList()
      {
        var session_id = $('#academic_session_id').val();
        $('#class_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id, 'extra' : ''}
        }).done(function(data)
        {
          $('#class_id').html(data);
        });
      }

      function updateSectionList()
      {
        var class_id = $('#class_id').val();
        $('#section_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-section-list").val(),
            'method' : 'GET',
            'data' : {'class_id' : class_id, 'extra' : ''}
        }).done(function(data)
        {
          $('#section_id').html(data);
        });
      }

      function updateStudentList()
      {
        var session_id = $('#academic_session_id').val();
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        $('#fee_student_list').html('loading...')
        $.ajax
        ({
          'url' : $("#billing-ajax-get-student-select-list").val(),
          'method' : 'GET',
          'data' : {'class_id' : class_id, 'academic_session_id' : session_id, 'section_id' : section_id, }
        }).done(function(data)
        {
          $('#student_id').html(data);
        });
      }

      function updateRemainingDueList(val)
      {
        if(val == null)
          var student_id = $('#student_id').val();
        else
          var student_id = val;


        $('#display_unpaid_invoices').html('loading.........');
        $.ajax
        ({
          'url' : $("#billing-ajax-get-student-remaining-due-view").val(),
          'method' : 'GET',
          'data' : {'student_id' : student_id}
        }).done(function(data)
        {
          
          $('#display_unpaid_invoices').html(data);
        });
      
      }

      function updateOrganizationDueList(organization_id)
      {

        $('#display_unpaid_invoices').html('loading.........');
        $.ajax
        ({
          'url' : $("#billing-ajax-get-student-remaining-due-view").val(),
          'method' : 'GET',
          'data' : {'student_id' : organization_id, 'type' : 'organization'}
        }).done(function(data)
        {
          
          $('#display_unpaid_invoices').html(data);
        });
      }

      $(document).on('keyup.autocomplete', '.auto', function()
      {
        $(this).autocomplete({select: function( event, ui ) 
                {
                 // console.log(ui);
                 $('#val_student_id').val(ui.item.id);
                 updateRemainingDueList(ui.item.id);
                  //console.log(event);
                },
        source: "{{URL::route('ajax-student-id-autocomplete')}}",
        minLength: 2
        
        });
      });
     
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

  var payType = jQuery('#payType');
  var select = this.value;
  payType.change(function () {
      var val = $(this).val();
      if (val == 'other') {
          $('.otherAdditional').show();
          $('.studentAdditional').hide();
          $('.organizationAdditional').hide();
      }
      else if(val == 'organization')
      { 
        $('.studentAdditional').hide();
        $('.otherAdditional').hide();
        $('.organizationAdditional').show();
      }
      else if(val == 'student')
      {
        $('.studentAdditional').show();
        $('.otherAdditional').hide();
        $('.organizationAdditional').hide();
      }
  });

  var val2 = document.getElementById("class_id");
  var val3 = document.getElementById("section_id");
  var val4 = document.getElementById("student_id");


  val2.onchange = function () {
    if (val2.value !== "select" && val2.value.length > 0) {
      val3.disabled = false;
    } else {
      val3.disabled = true;
    }
  };
  val3.onchange = function () {
    if (val3.value !== "select" && val3.value.length > 0) {
      val4.disabled = false;
    } else {
      val4.disabled = true;
    }
  };

  $('.check-all-invoice-ids').click(function(e)
  {

    if($(this).is(":checked"))
    {
      $('.check-invoice-id').each(function()
      {

        $(this).prop('checked', true);

      });
    }
    else
    {
      $('.check-invoice-id').each(function()
      {

        $(this).prop('checked', false);

      });
    }

  });
</script>
@stop