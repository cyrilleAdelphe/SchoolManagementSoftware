
@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Statement</h1>
@stop

@section('custom-css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
              <div class = 'row'>
                <div class = "col-sm-3">
                  <div class="form-group">
                    <a href = "#" id = "toggle-button" class = "btn btn-primary btn-flat auto-off">Switch search type</a>
                  </div>  
                </div>  
              </div>

              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Date range</label>
                    <input id="date" type = 'text' name = 'daterange' class = 'form-control myDate required'>
                  </div>
                </div>
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
                <div class="col-sm-2 auto-off-block">
                  <div class="form-group">
                    <label>Class</label>
                    <select class="form-control class_id" name = "class_id" id="class_id">
                    <option value="0">-- Select Session First --</option>
                  </select>
                  </div>
                </div>
                <div class="col-sm-2 auto-off-block">
                  <div class="form-group">
                    <label>Section</label>
                    <select id="section_id"  name = "section_id" class="form-control academic_session_id" >
                      <option value="0">-- Select Class First --</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2 auto-off-block">
                  <div class="form-group">
                    <label>Student</label>
                    <select id="student_id" class="form-control" >
                      <option value="select">Select student</option>
                    </select>
                  </div>
                </div>  

                <div class = "col-sm-3 auto-on-block" style = "display:none;">
                  <div class="form-group">
                    <label >Student name</label>
                    <input type = "text" class = "auto form-control">
                    <input type = "hidden" class = "student_id" name = "student_id">
                  </div>
                </div>

                <div class="col-sm-1">
                  <div class="form-group">
                   <label style="color: #fff">Show</label>
                   <a href="#" class="btn btn-success btn-flat" id = "show-statement-button">Show</a>
                  </div>
                </div>

 
              </div> <!-- row ends -->
              
              <div id = "ajax-content">
              
              </div>

              <form method="post">  
                
              <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
              <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
              <input type = "hidden" id = 'billing-ajax-get-student-select-list' value = '{{URL::route('billing-ajax-get-student-select-list')}}'>
              </form>  
@stop

@section('custom-js')
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script type="text/javascript" src="{{asset('sms/assets/js/lity.min.js')}}"></script>
    <script type="text/javascript">
        $(function() {
         
          $('.myDate').daterangepicker(
          {
              
              "showDropdowns": true,
              "showCustomRangeLabel": false,
              "alwaysShowCalendars": true,
              locale: {
                format: 'YYYY/MM/DD'
              },
              startDate: '{{date('Y/m/d')}}',
          }, 
          function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY/MM/DD') + ' to ' + end.format('YYYY/MM/DD') + ' (predefined range: ' + label + ')");
          });
        });
    </script>
    <script>

      updateClassList();
      updateSectionList();
      updateStudentList();

      $(document).on('change', '#academic_session_id', function(e)
      {
        
        updateClassList();
        updateSectionList();
        updateStudentList();
        
        
      });

      $(document).on('change', '#class_id', function(e)
      {
        
        
        updateSectionList();
        updateStudentList();
        
      });

      $(document).on('change', '#section_id', function(e)
      {
        updateStudentList();
        
      });

      $(document).on('change', '#student_id', function(e)
      {
        $('.student_id').val($(this).val());
        
      });

      $('#show-statement-button').click(function()
      {
        var date_range = $('#date').val();
        var academic_session_id = $('#academic_session_id').val();
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var student_id = $('.student_id').val();

        $.ajax({
          'url' : '{{URL::route('billing-api-get-statement-list-view')}}',
          'method' : 'GET',
          'data' : {'session_id' : academic_session_id, 'date_range' : date_range, 'class_id' : class_id, 'section_id' : section_id, 'student_id' : student_id}
        }).done(function(data)
        {
          $('#ajax-content').html(data);
        });
      })

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
          'data' : {'class_id' : class_id, 'academic_session_id' : session_id, 'section_id' : section_id}
        }).done(function(data)
        {
          $('#student_id').html(data);
        });
      }
      


      function updateClassList()
      {
        var session_id = $('#academic_session_id');
        var class_id = $('#class_id');

        class_id.html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id.val(), 'extra' : ''}
        }).done(function(data)
        {
          class_id.html(data);
        });
      }

      function updateSectionList()
      {
        var class_id = $('#class_id');
        var section_id = $('#section_id');

        section_id.html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-section-list").val(),
            'method' : 'GET',
            'data' : {'class_id' : class_id.val(), 'extra' : ''}
        }).done(function(data)
        {
          section_id.html(data);
        });
      }

      $(document).on('click', '.auto-off', function(e)
      {
        console.log('off');
        e.preventDefault();
        $('.auto-off-block').css('display', 'none');
        $('.auto-on-block').css('display', 'block');
        $(this).removeClass('auto-off');
        $(this).addClass('auto-on');
       

      });

      $(document).on('click', '.auto-on', function(e)
      {
        console.log('on');
        e.preventDefault();
        $('.auto-off-block').css('display', 'block');
        $('.auto-on-block').css('display', 'none');
        $(this).removeClass('auto-on');
        $(this).addClass('auto-off');
       

      });

      $(document).on('keyup.autocomplete', '.auto', function()
      {
        $(this).autocomplete({select: function( event, ui ) 
                {
                 // console.log(ui);
                 $('#student_id').val(ui.item.id);
                 $('.student_id').val(ui.item.id);
                 //console.log(ui.item);
                  //console.log(event);
                },
        source: "{{URL::route('ajax-student-id-autocomplete')}}",
        minLength: 2
        
        });
      });
    </script>
@stop