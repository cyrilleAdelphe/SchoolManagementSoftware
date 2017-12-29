@extends('backend.'.$role.'.main')

@section('custom-css')
    <link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
@stop

@section('content')
            <form action = "{{URL::route('billing-opening-balance-post')}}" method = "post">

              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Select session</label>
                    @define $session = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get()
                    <select id="session" class="form-control" name = "session">
                      @foreach($session as $s)
                      <option value="{{$s->id}}" <?php 
                      if((int) Input::old('session') && $s->id == Input::old('session')) 
                        { echo 'selected'; } 
                      elseif( $s->is_current == 'yes') 
                        { echo 'selected'; } ?> >{{$s->session_name}}</option>
                      @endforeach
                      </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                     <label>Select class</label>
                    <select id="class" class="form-control" name = "class">
                      <option value="0">Select class</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Section</label>
                    <select id="section" class="form-control" name = "section">
                      <option value="0">Select section</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Balance as on</label>
                    <input id="date" type = 'text' name = 'daterange' value="Show current date" class = 'form-control myDate required'>
                  </div>
                </div>
              </div><!-- row ends -->
              
              <div class="row">
                <div class="col-sm-12">
                  <div class="primeBtn pull-right" style="margin-bottom: 15px">
                    <input type = "submit" value = "save" name = "save" class="btn btn-flat btn-success">
                    <input type = "submit" value = "Confirm Save" name = "save" class="btn btn-flat btn-primary">
                  </div>
                </div>
              </div>

              <div class = "row">
                <div class = "col-md-6">Total</div>
                <div class = "col-md-6" id = "total_opening_balance_first"></div>
              </div>

              <div id = "ajax-content">
              </div>

              <div class = "row">
                <div class = "col-md-6">Total</div>
                <div class = "col-md-6" id = "total_opening_balance"></div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <div class="primeBtn pull-right" style="margin-bottom: 15px">
                    <input type = "submit" value = "save" name = "save" class="btn btn-flat btn-success edtBtn">
                    <input type = "submit" value = "Confirm Save" name = "save" class="btn btn-flat btn-primary svBtn">
                  </div>
                </div>
              </div>
              {{Form::token()}}
            </form>
@stop

@section('custom-js')
    
    <script>
      $(function()
      {

        updateClassList('session', 'class', '{{(int) Input::old("class")}}');

        $('#session').change(function()
        {
          updateClassList('session', 'class', '{{(int) Input::old("class")}}');
        });

        $('#class').change(function()
        {
          updateSectionList('session', 'class', 'section', '{{(int) Input::old("section")}}');
        });

        $('#section').change(function()
        {
          updateStudentList();
        });

        

        function updateClassList(session_id, class_id, default_class_id)
        {
          var class_obj = $('#' + class_id);
          var session_obj = $('#' + session_id);

          class_obj.html('loading....')

          $.ajax({
            url : '{{URL::route("billing-ajax-get-class-ids-from-session-id")}}',
            data: {'session_id' : session_obj.val(), 'default_class_id' : default_class_id},
            method: 'get'
          }).done(function (data)
          {

              class_obj.html(data);
              updateSectionList('session', 'class', 'section', '{{(int) Input::old("section")}}');
          });
          
        }

        function updateSectionList(session_id, class_id, section_id, default_section_id)
        {
          var class_obj = $('#' + class_id);
          var session_obj = $('#' + session_id);
          var section_obj = $('#' + section_id);

          section_obj.html('loading....');
          $.ajax({
            url : '{{URL::route("billing-ajax-get-section-ids-from-session-id-and-class-id")}}',
            data: {'session_id' : session_obj.val(), 'class_id' : class_obj.val(), 'default_section_id' : default_section_id},
            method: 'get'
          }).done(function (data)
          {
              section_obj.html(data);
              updateStudentList();
          }); 
        }

        function updateStudentList()
        {
          $('#ajax-content').html('loading...');
          $.ajax({
            url : '{{URL::route("billing-ajax-get-opening-balance-student-list")}}',
            data: {'session_id' : $('#session').val(), 'class_id' : $('#class').val(), 'section_id' : $('#section').val(), 'date' : $('#date').val()},
            method: 'get'
          }).done(function (data)
          {
              $('#ajax-content').html(data);
              calculateTotalBalance();
          }); 
        }
      });

      function calculateTotalBalance()
      {
        var balance = $('.opening_balance');

        var total_amount = 0;
        $(balance).each(function()
        {
          var amount = $(this).val().trim();
          if(amount.length > 0)
          {
            var amount = parseFloat(amount);
            total_amount += amount;  
          }
          
        });

        $('#total_opening_balance').html(total_amount);
        $('#total_opening_balance_first').html(total_amount);
      }

      $('#ajax-content').on('change', '.opening_balance', calculateTotalBalance);
      
    </script>

    <script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript">
      $('.myDate').daterangepicker({
          "singleDatePicker": true,
          "locale": {
              "format": "YYYY-MM-DD",
              "separator": " - ",           
          },
          "startDate": "{{date('Y-m-d')}}"
        }, function(start, end, label) {
          console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
      });
    </script>
@stop