<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
    <!-- FontAwesome 4.3.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="{{asset('sms/assets/css/ionicons.min.css')}}" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- jQuery 2.1.4 -->
    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
    <link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet" type="text/css" />
    </head>

<body>
              <div class = 'row'>
                <div class = "col-sm-3">
                  <div class="form-group">
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
                
                <input type = "hidden" name = "academic_session_id" id = "academic_session_id" value = "{{ Input::get('academic_session_id', 0) }}">
                <input type = "hidden" name = "section_id" id = "section_id" value = "{{ Input::get('section_id', 0) }}">
                <input type = "hidden" name = "class_id" id = "class_id" value = "{{ Input::get('class_id', 0) }}">
                <input type = "hidden" name = "student_id" class = "student_id" value = "{{ Input::get('student_id', 0) }}">

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js" type="text/javascript"></script> 
    <script src="{{asset('sms/assets/js/app.min.js')}}" type="text/javascript"></script>    
    
    <script src="{{asset('backend-js/remove-global.js')}}" type="text/javascript"></script>  

    <script src="{{asset('backend-js/sidebarActivate.js')}}" type="text/javascript"></script>  
    <script>
      setTimeout(function() {
        $('.alert-success').fadeOut('slow');
        }, 2000);
    </script>
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

      
      $('#show-statement-button').click(function()
      {
        var date_range = $('#date').val();
        var academic_session_id = $('#academic_session_id').val();
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var student_id = $('.student_id').val();

        var encoded_api_token = "{{ preg_replace( "/\r|\n/", "", Input::get('apiToken', base64_encode('0:0')) ) }}";

        $.ajax({
          'url' : '{{URL::route('billing-api-get-statement-list-view')}}',
          'method' : 'GET',
          'data' : { 'session_id' : academic_session_id, 'date_range' : date_range, 'class_id' : class_id, 'section_id' : section_id, 'student_id' : student_id, 'apiToken' : encoded_api_token }
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
</body>
</html>