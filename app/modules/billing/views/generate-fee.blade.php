@extends('backend.'.$role.'.main')

@section('custom-css')
  <!-- this is for nepali billing <link href="{{asset('sms/assets/css/nepali.datepicker.v2.2.min.css')}}" rel="stylesheet" type="text/css" /> -->

  <!-- this is for english billing <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" /> -->
  <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
  <h1>Generate fee</h1>
@stop

@section('content')

            <form method="post" action = "{{URL::route('billing-generate-fee-post')}}" id = "backendForm">  
              <div class="row">
                <div class="col-sm-2">
                {{
                  HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
                  $selected = 
                    Input::has('academic_session_id') ?
                    Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])
                }}
                </div>
                
                <div class="col-sm-2">
                  <div class="form-group">
                    <select class="form-control" id="class_id" name = "class_id">
                      <option value="0">-- Select Session First --</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <select class="form-control" id="section_id" name = "section_id" disabled>
                      <option value="">-- Select Section First --</option>
                    </select>
                  </div>
                </div>
                <!-- this is for nepali billing 
                <div class="col-sm-2">
                  <div class="form-group">
                    <input type="text" id="nepaliDate" class="nepali-calendar hidNep form-control" value=""/>
                  </div>
                </div>
            
                <input type = "hidden" name = "month" id = "month" class = "month"> -->
                
                <!-- this is for english billing <div class="col-sm-2">
                  <div class="form-group">
                    @define $date = date('Y-m-d')
                    <input type="text" id="englishDate" class="form-control myDate" value="" name="issued_date"/>
                  </div>
                </div>
            
                <input type = "hidden" name = "month" id = "month" class = "month"> -->

               <div class="col-sm-2">
                  <div class="form-group">
                    @define $date = date('Y-m-d')
                    <input type="text" id="englishDate" class="form-control myDate" value="" name="issued_date"/>
                  </div>
                </div>
            
                <input type = "hidden" name = "month" id = "month" class = "month">

                <div class="col-sm-2">
                  <div class="form-group">
                    <!-- this is for nepali billing
                    @define $date = date('Y-m-d')
                    <input type="hidden" id="englishDate" class="form-control" name="issued_date" value = "{{$date}}"/> -->
                    
                  </div>
                </div>

              </div> <!-- row ends -->
              <div id = "fee_student_list"  >
                
              </div>
                <!-- View Modal -->
                       
              <div class="row">
                <div class="col-sm-12">
                  
                    <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
                    <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
                    <input type = "hidden" id = "billing-ajax-get-student-fee-list-view" value = '{{URL::route('billing-ajax-get-student-fee-list-view')}}'>
                    <input type = "hidden" id = 'billing-ajax-calculate-tax' value = '{{URL::route('billing-ajax-calculate-tax')}}'>
                    
                    <input type="submit" name="generate_invoice" class="btn btn-danger btn-flat btn-submit btn-lg submit-enable-disable" related-form = "backendForm" value="Generate Invoice" disabled/>
                 
                </div>
              </div><!-- row ends -->
              {{Form::token()}}
            </form>
@stop


@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script src="{{asset('backend-js/submit-enable-disable.js')}}" type="text/javascript"></script>
<!-- this is for english <script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}" type="text/javascript"></script> -->
<!--this is for nepali <script src="{{asset('sms/assets/js/nepali.datepicker.v2.2.min.js')}}" type="text/javascript"></script> -->
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}" type="text/javascript"></script>
 <script type="text/javascript">
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
          $('#month').val(picker.startDate.format('MMM'));
          updateStudentList();

        });

        $('#month').val(getMonthFromEnglishDate($('#englishDate').val()));

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
          $('#month').val(getMonthFromNepaliDate($('#nepaliDate').val()));
          updateStudentList();
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
          $('#month').val(picker.startDate.format('MMM'));
          updateStudentList();

        });

        $('#month').val(getMonthFromEnglishDate($('#englishDate').val()));

        $('#englishDate').change(function()
        {
          getMonthFromEnglishDate($('#englishDate').val());
        });

      var val1 = document.getElementById("academic_session_id");
      var val2 = document.getElementById("class_id");
      var val3 = document.getElementById("section_id");
    
      var val5 = document.getElementById("month");

      val1.onchange = function () {
        if (val1.value !== "select" && val1.value.length > 0) {
            val2.disabled = false;
        } else {
          val2.disabled = true;
        }
      };
      val2.onchange = function () {
        if (val2.value !== "select" && val2.value.length > 0) {
          val3.disabled = false;
        } else {
          val3.disabled = true;
        }
      };
      val3.onchange = function () {
        if (val3.value !== "select" && val3.value.length > 0) {
          
        } else {
          
        }
      };
       
      updateClassList();
      updateSectionList();

      $('#academic_session_id').change(function(e)
      {
        updateClassList();
        updateSectionList();
        updateStudentList();
      });

      $('#class_id').change(function(e)
      {
        updateSectionList();
        updateStudentList();
      });

      $('#section_id').change(function(e)
      {
        updateStudentList();
      });

      function updateClassList()
      {
        var session_id = $('#academic_session_id').val();
        $('#class_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id, 'extra':''}
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
            'data' : {'class_id' : class_id, 'extra':''}
        }).done(function(data)
        {
          $('#section_id').html(data);
        });
      }

      /* this is for english billing function getMonthFromEnglishDate(date) //in Y-m-d format
      {
        var date_elements = date.split('-');
        var month = parseInt(date_elements[1]);
        var month_array = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return month_array[month-1];
      }
      */

      /* this is for nepali billing function getMonthFromNepaliDate(nepali_date) //in Y-m-d format
      {
        console.log('here');
        var date_elements = nepali_date.split('-');
        var month = parseInt(date_elements[1]);
        var month_array = ['baishak', 'jestha', 'ashad', 'shrawan', 'bhadra', 'ashwin', 'kartik', 'mangsir', 'poush', 'magh', 'falgun', 'chaitra'];
        return month_array[month-1];
      }*/

      function getMonthFromEnglishDate(date) //in Y-m-d format
      {
        var date_elements = date.split('-');
        var month = parseInt(date_elements[1]);
        var month_array = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return month_array[month-1];
      }

      function updateStudentList()
      {
        var session_id = $('#academic_session_id').val();
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var month = $('#month').val();
        $('#fee_student_list').html('loading...')
        $.ajax
        ({
          'url' : $("#billing-ajax-get-student-fee-list-view").val(),
          'method' : 'GET',
          'data' : {'class_id' : class_id, 'academic_session_id' : session_id, 'section_id' : section_id, 'month' : month, 'issued_date' : $('#englishDate').val()}
        }).done(function(data)
        {
          $('#fee_student_list').html(data);
          checkIfDataBeingEdited();
        });
      }

      $('#fee_student_list').on('click', '.master_billing_editable', function(e)
      {

        var rows = $('#fee_student_list').find('table').find('tbody').find('tr');
        $(rows).each(function()
        {
          var row = $(this);
          var data_to_edit = row.find('.editable_data');

          $(data_to_edit).each(function(index, value) {
            var edited_data = $(this).parent().find('.billing_editable').val();

            $(this).html('<input type = "number" class = "edited_data" value = "' + edited_data + '">');
          });

          var button = row.find('.billing_editable_row');
          button.find('i').removeClass('fa-edit');
          button.find('i').addClass('fa-save');
          button.removeClass('billing_editable_row');
          button.addClass('billing_savable_row');
          checkIfDataBeingEdited();
        });
        
      });

      $('#fee_student_list').on('click', '.billing_editable_row', function(e) {
        e.preventDefault();
          var row = $(this).parent().parent();
          var data_to_edit = row.find('.editable_data');

          $.each(data_to_edit, function(index, value) {
            var edited_data = $(this).parent().find('.billing_editable').val();

            $(this).html('<input type = "number" class = "edited_data" value = "' + edited_data + '">');
          });

          $(this).find('i').removeClass('fa-edit');
          $(this).find('i').addClass('fa-save');
          $(this).removeClass('billing_editable_row');
          $(this).addClass('billing_savable_row');
          checkIfDataBeingEdited();
      });

       $('#fee_student_list').on('change', '.edited_data', function(e) {
          var edited_data = $(this).val();
          $(this).parent().parent().find('.billing_editable').val(edited_data);
       });

       $('#fee_student_list').on('click', '.billing_savable_row', function(e) {
          e.preventDefault();

          var currentElement = $(this);
          
          var row = $(this).parent().parent();
          
          var data_to_save = row.find('.editable_data');

          
          var taxable = 0;
          var untaxable = 0;
          $.each(data_to_save, function(index, value) {
            var edited_data = $(this).find('.edited_data').val();
            
            if($(this).parent().find('.billing_editable').hasClass('taxable'))
            {
              taxable = taxable + parseFloat(edited_data);
            }
            else if($(this).parent().find('.billing_editable').hasClass('taxable-minus-yes'))
            {
              
              taxable = taxable - parseFloat(edited_data);
            }
            else if($(this).parent().find('.billing_editable').hasClass('taxable-minus-no'))
            {
              
              untaxable = untaxable - parseFloat(edited_data);
            }
            else
            {
              untaxable = untaxable + parseFloat(edited_data);
            }

            $(this).html(edited_data);
          });

          row.find('.sum-without-tax').html(taxable + untaxable);
          row.find('.sum-without-tax-data').val(taxable + untaxable);

          $.ajax
          ({
            url : $('#billing-ajax-calculate-tax').val(),
            method : 'GET',
            data : {'sum_without_tax' : taxable}
          }).done(function(data)
          {
            row.find('.tax').html(data);
            row.find('.tax-data').val(data);
            row.find('.taxable-sum').html(taxable);
            row.find('.taxable-sum-data').val(taxable);
            row.find('.untaxable-sum').html(untaxable);
            row.find('.untaxable-sum-data').val(untaxable);
            row.find('.sum').html(parseFloat(data) + parseFloat(taxable) + parseFloat(untaxable));
            row.find('.sum-data').val(parseFloat(data) + parseFloat(taxable) + parseFloat(untaxable));
            currentElement.find('i').removeClass('fa-save');
            currentElement.find('i').addClass('fa-edit');
            currentElement.removeClass('billing_savable_row');
            currentElement.addClass('billing_editable_row');
            checkIfDataBeingEdited();
          });

       });

       $('#fee_student_list').on('change', '.taxable', function(e) {

          var taxable_data = $(this).parent().parent().parent();
          var taxable_sum = 0;
          $.each(taxable_data, function(index, value)
          {
            taxable_sum = taxable_sum + $(this).val();
          });

          var discount_amount = $(this).parent().parent().parent().find('.taxable-minus').val();
          taxable_sum = taxable_sum - discount_amount;
       });

       function checkIfDataBeingEdited()
       {
          var elements = $('#fee_student_list').find('.billing_savable_row');
          var buttons = $(document).find('.btn-submit');

          var status = false;

          $(elements).each(function()
          {
            status = true;
          });

          if(status)
          {
            $(buttons).each(function()
            {
              $(this).prop('disabled', true);
              
            });
          }
          else
          {
            $(buttons).each(function()
            {
              $(this).prop('disabled', false);
            });
          }
       }
      });
      
    </script>
@stop