@extends('events.views.backend')

@section('custom-css')
    <!-- iCheck for checkboxes and radio inputs -->
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />

    <!-- Date Picker -->
    <link href="{{asset('sms/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
    
    <!-- Daterange picker -->
    <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />

    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />



@stop

@section('page-header')
  <h1>Manage/View Events</h1>
@stop

@section('tab-content')
  {{$actionButtons}}
  <form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm">
      <div class="form-group @if($errors->has('title')) {{'has-error'}} @endif">
          <label for="title" class = 'control-label'>Title</label>
          <input id="title" name="title" class="form-control required" type="text" placeholder="Enter event title"
              value= "{{ (Input::old('title')) ? (Input::old('title')) : '' }}">
          <span class = 'help-block'>
            @if($errors->has('title')) {{$errors->first('title')}} @endif
          </span>
      </div>                                
      
      <div class="form-group  @if($errors->has('venue')) {{'has-error'}} @endif">
          <label for="venue" class = 'control-label'>Venue</label>
          <input name="venue" id="venue" class="form-control required" type="text" placeholder="Enter venue"
                value= "{{ (Input::old('venue')) ? (Input::old('venue')) : '' }}">
          <span class = 'help-block'>
            @if($errors->has('venue')) {{$errors->first('venue')}} @endif
          </span>
      </div>

      <div class="form-group  @if($errors->has('event_type')) {{'has-error'}} @endif">
          <label for="event_type" class = 'control-label'>Event Type:</label>

          @define $event_types = ['holiday'=>'Holiday', 'exam'=>'Examination', 'school_function'=>'School Function']

          @foreach($event_types as $event_type => $alias)
            @if(Input::old('event_type'))
              @if(Input::old('event_type')==$event_type)
                @define $is_checked = true
              @else
                @define $is_checked = false
              @endif
            @else
              @if($event_type=='holiday')
                @define $is_checked = true
              @else
                @define $is_checked = false
              @endif
            @endif
            
            <input name="event_type" type="radio" value="{{$event_type}}" @if($is_checked) checked @endif>{{$alias}}
          @endforeach
          
          <span class = 'help-block'>
            @if($errors->has('event_type')) {{$errors->first('event_type')}} @endif
          </span>
      </div>

      <div class="form-group @if($errors->has('date')) {{'has-error'}} @endif">
        <label class="control-label">Date</label>
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
          </div>
          <input name="date" type="text" class="form-control pull-right" id="reservationtime"
                  value= "{{ (Input::old('date')) ? (Input::old('date')) : '' }}"/>
        </div><!-- /.input group -->
        <span class = 'help-block'>
            @if($errors->has('date')) {{$errors->first('date')}} @endif
          </span>
      </div><!-- /.form group -->

      <div class="form-group @if($errors->has('for')) {{'has-error'}} @endif">
        <label for="for" class="control-label">For</label><br/>
        <label>
          <input type="hidden" name="for_students" value="no">
          <input type="checkbox" name="for_students" value="yes" class="flat-red" {{ (Input::old('for_students')) ? (Input::old('for_students'))==='yes'?'checked':'' : 'checked' }}/>
          Students
        </label>&nbsp;&nbsp;&nbsp;
        <label>
          <input type="hidden" name="for_teachers" value="no">
          <input type="checkbox" name="for_teachers" value="yes" class="flat-red" {{ Input::old('for_teachers')==='yes'?'checked':'' }}/>
          Teachers
        </label>&nbsp;&nbsp;&nbsp;
        <label>
          <input type="hidden" name="for_management_staff" value="no">
          <input type="checkbox" name="for_management_staff" value="yes" class="flat-red" {{ Input::old('for_management_staff')==='yes'?'checked':'' }}/>
          Management Staff
        </label>&nbsp;&nbsp;&nbsp;
        <label>
          <input type="hidden" name="for_parents" value="no">
          <input type="checkbox" name="for_parents" value="yes" class="flat-red" {{ Input::old('for_parents')==='yes'?'checked':'' }}/>
          Parents
        </label>&nbsp;&nbsp;&nbsp;
        <label>
          <input type="hidden" name="for_all" value="no">
          <input type="checkbox" name="for_all" value="yes" class="flat-red" {{ Input::old('for_all')==='yes'?'checked':'' }}/>
         All
        </label>
        <span class = 'help-block'>
          @if($errors->has('for')) {{$errors->first('for')}} @endif
        </span>
      </div>
      <div class="form-group @if($errors->has('description')) {{'has-error'}} @endif">
          <label for="desciption" class="control-label">Event Description</label>
          <textarea class="textarea" name="description" placeholder="Describe your event here" 
                style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('description')) ? (Input::old('description')) : '' }}</textarea>
          <span class = 'help-block'>
            @if($errors->has('description')) {{$errors->first('description')}} @endif
          </span>
      </div>
      {{-- <div class="form-group">
          <button class="btn btn-primary" type="submit">Submit</button>
      </div> --}}
      <div class='row'>
        <a class = 'btn btn-app' href = '#' id = 'PraSave'>
          <i class = 'fa fa-save'></i>Save
        </a>

        <a class = 'btn btn-app' href = '#'  id = 'PraSaveAndSendNotification'>
          <i class='fa  fa-check-square-o'></i>Save & send notification
        </a>
      </div>
      <input type="hidden" name="is_active" value="yes"/>
      {{Form::token()}}
    </form>
@stop

@section('custom-js')
 
  <!-- Page script -->
  <script src="{{asset('sms/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
  
  <!-- InputMask -->
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js') }}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js') }}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.extensions.js') }}" type="text/javascript"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js') }}" type="text/javascript"></script>
  
  
  <!-- DATA TABES SCRIPT -->
  <script src="{{asset('sms/plugins/datatables/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/datatables/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>

  <script>
    $(function() {
      // save and save and send notification
      $(document).on('click', "#PraSave", function(e){
        e.preventDefault();
        $('#backendForm').append('<input type = "hidden" name = "pushNotification" value = "no">');
        $('#backendForm').submit();
      });

      $(document).on('click', "#PraSaveAndSendNotification", function(e){
        e.preventDefault();
        $('#backendForm').append('<input type = "hidden" name = "pushNotification" value = "yes">');
        $('#backendForm').submit();
      });
    });
  </script>

  <script type="text/javascript">
    $(function () {
      $("#pageList").dataTable();
      
    });
  </script>
  
  <!-- Editor SCRIPT -->
  <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>
  <script type="text/javascript">
    $(function () {
      //bootstrap WYSIHTML5 - text editor
      $(".textarea").wysihtml5();
    });
  </script>

  <script type="text/javascript">
    $( document ).ready(function(){
      //iCheck for checkbox and radio inputs
      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
      });
      //Red color scheme for iCheck
      $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
      });
      //Flat red color scheme for iCheck
      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
      });
    });
  </script>
  
  <script type="text/javascript">
    $( document ).ready(function() {
      //Datemask dd/mm/yyyy
      $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
      //Datemask2 mm/dd/yyyy
      $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
      //Money Euro
      $("[data-mask]").inputmask();

      //Date range picker
      $('#reservation').daterangepicker();
      //Date range picker with time picker
      $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
      //Date range as a button
      $('#daterange-btn').daterangepicker(
              {
                ranges: {
                  'Today': [moment(), moment()],
                  'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                  'Last 7 Days': [moment().subtract('days', 6), moment()],
                  'Last 30 Days': [moment().subtract('days', 29), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                startDate: moment().subtract('days', 29),
                endDate: moment()
              },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }
      );

      
      //Colorpicker
      $(".my-colorpicker1").colorpicker();
      //color picker with addon
      $(".my-colorpicker2").colorpicker();

      //Timepicker
      $(".timepicker").timepicker({
        showInputs: false
      });
    });
  </script>

@stop