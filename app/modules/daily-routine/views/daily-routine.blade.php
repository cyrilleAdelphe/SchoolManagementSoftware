@extends('backend.'.$role.'.main')

@section('custom-css')
    <!-- Theme style -->    
  <link href="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet"/> 
@stop

@section('page-header')
  <h1>
   Daily routine
  </h1>
@stop

@section('content')
  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-fw fa-bars"></i> Routine</a></li>
      {{-- <li><a href="#tab_2" data-toggle="tab"><i class="fa fa-fw fa-edit"></i> Create routine</a></li> --}}
    </ul>
    <div class="tab-content">
      
      <div class="tab-pane active" id="tab_1">                      
        
        <div class="row">
              
              <div class="col-sm-3">
                <div class="form-group  @if($errors->has('session_id')) {{'has-error'}} @endif">
                  <label>Choose Session</label>
                  {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id_list', Input::get('session_id', HelperController::getCurrentSession()))}}
                  
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group  @if($errors->has('class_id')) {{'has-error'}} @endif ">
                  <label>Choose Class</label>
                  <select class="form-control" id = "class_id_list" name = "class_id">
                    <option value="0">-- Please Select Session First --</option>
                  </select>
                  
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group  @if($errors->has('section_id')) {{'has-error'}} @endif  ">
                  <label>Section</label>
                  <select class="form-control" id = "section_id_list" name = "section_id">
                    <option value="0">-- Please Select Class First --</option>
                  </select>
                  <span class = 'help-block'>@if($errors->has('section_id')) {{$errors->first('section_id')}} @endif</span>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group  @if($errors->has('day')) {{'has-error'}} @endif  ">
                  <label>Day</label>
                  
                    {{HelperController::generateStaticSelectList(array('Sunday' => 'Sunday', 'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'), 'day_list', Input::get('day', 'Sunday'))}}
                  
                </div>
              </div>

              {{-- <div class="col-sm-2">
                <div class="form-group">
                  <label>Search</label>
                  <div class = "form-control">
                    <button type = "button" class = "btn btn-success" id = "search_list">Search</button>
                  </div>
                </div>
              </div> --}}

          </div><!-- row ends -->
        @if(Input::has('class_id') && Input::has('section_id'))
        <div class = "row">
          <h4 class="text-red">Class {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0))}} {{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0))}}</h4> 
        </div>
        @endif

        <div class="row">
          <div class="col-sm-3 col-sm-offset-9" style="margin-bottom:15px">
            <a href="{{URL::route('daily-routine-edit-post')}}?session_id={{Input::get('session_id', 0)}}&class_id={{Input::get('class_id', 0)}}&section_id={{Input::get('section_id', 0)}}&day={{Input::get('day', 'Sunday')}}" class="btn btn-success pull-right"  @if(AccessController::checkPermission('daily-routine', 'can_edit') == false) disabled @endif>
              <i class="fa fa-fw fa-edit"></i> @if(count($data)) Edit @else Create @endif routine
            </a>
            @if(count($data))
              <a href="#" data-toggle="modal" data-target="#deleteDay" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('daily-routine', 'can_delete') == false) disabled @endif>
                <i class="fa fa-fw fa-trash"></i> Delete
              </a>
              @include('daily-routine.views.modal-delete-day')
            @endif
          </div>
          <div class="col-sm-12">
            <table id="pageList" class="table table-bordered table-striped">
              @if(count($data))
              <thead>
                <tr>
                  <th>SN</th>
                  <th>Subject</th>
                  <th>Teacher</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody>
                
                  @define $i = 1;
                  @foreach($data as $d)
                  <tr>
                    <td>{{$i++}}</td>
                    <td>{{$d->subject}}</td>
                    <td>{{$d->teacher}}</td>
                    <td>{{DateTime::createFromFormat('H:i:s', $d->start_time)->format('g:i A')}} - {{DateTime::createFromFormat('H:i:s', $d->end_time)->format('g:i A')}}</td>
                    
                  </tr>
                  
              </tbody>
              @endforeach
               @else
              <tr><td><div class="alert alert-warning alert-dismissable">
      <h4><i class="icon fa fa-warning"></i>No Data Found</h4>
      </div></td></tr>
        @endif
            </table>
          </div>
        </div>
      </div><!-- tab 1 ends -->

      <div class="tab-pane" id="tab_2">
        <form method = "post" action = "{{URL::route('daily-routine-create-post')}}">
          <div class="row">
              
              <div class="col-sm-3">
                <div class="form-group  @if($errors->has('session_id')) {{'has-error'}} @endif">
                  <label>Choose Session</label>
                  {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', Input::old('session_id', HelperController::getCurrentSession()) )}}
                  <span class = 'help-block'>@if($errors->has('session_id')) {{$errors->first('session_id')}} @endif</span>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group  @if($errors->has('class_id')) {{'has-error'}} @endif ">
                  <label>Choose class</label>
                  <select class="form-control" id = "class_id" name = "class_id">
                    <option>-- Please Select Session First --</option>
                  </select>
                  <span class = 'help-block'>@if($errors->has('class_id')) {{$errors->first('class_id')}} @endif</span>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group  @if($errors->has('section_id')) {{'has-error'}} @endif  ">
                  <label>Section</label>
                  <select class="form-control" id = "section_id" name = "section_id">
                    <option>-- Please Select Class First --</option>
                  </select>
                  <span class = 'help-block'>@if($errors->has('section_id')) {{$errors->first('section_id')}} @endif</span>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group  @if($errors->has('day')) {{'has-error'}} @endif  ">
                  <label>Day</label>
                  
                    {{HelperController::generateStaticSelectList(array('Sunday' => 'Sunday', 'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thurdsay' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'), 'day', Input::old('day'))}}
                  <span class = 'help-block'>@if($errors->has('day')) {{$errors->first('day')}} @endif</span>
                </div>
              </div>
          </div><!-- row ends -->
          
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group  @if($errors->has('subject')) {{'has-error'}} @endif  ">
                <label>Subject</label>
                <input id="subject" name = "subject" class="form-control" type="text" value = "{{Input::old('subject')}}" />
                <span class = 'help-block'>@if($errors->has('subject')) {{$errors->first('subject')}} @endif</span> 
              </div>
            </div>
             <div class="col-sm-4">
              <div class="form-group  @if($errors->has('teacher')) {{'has-error'}} @endif  ">
                <label>Teacher</label>
                <input id="teacher" name = "teacher"  class="form-control" type="text" value = "{{Input::old('teacher')}}" /> 
                <span class = 'help-block'>@if($errors->has('teacher')) {{$errors->first('teacher')}} @endif</span>
              </div>
            </div>
          </div><!-- row ends -->

          <div class="row">
            <div class="col-sm-4">
              <div class="bootstrap-timepicker">
                <div class="form-group  @if($errors->has('start_time')) {{'has-error'}} @endif ">
                  <label>Start time</label>
                  <div class="input-group">
                    <input type="text" name = "start_time" value = "{{Input::old('start_time')}}" class="form-control timepicker" />
                    <div class="input-group-addon">
                      <i class="fa fa-clock-o"></i>
                    </div>
                  </div><!-- /.input group -->
                  <span class = 'help-block'>@if($errors->has('start_time')) {{$errors->first('start_time')}} @endif</span>
                </div><!-- /.form group -->
              </div>
            </div>
            <div class="col-sm-4">
              <div class="bootstrap-timepicker">
                <div class="form-group  @if($errors->has('end_time')) {{'has-error'}} @endif ">
                  <label>End time</label>
                  <div class="input-group">
                    <input type="text" name = "end_time" value = "{{Input::old('end_time')}}"  class="form-control timepicker" />
                    <div class="input-group-addon">
                      <i class="fa fa-clock-o"></i>
                    </div>
                    <span class = 'help-block'>@if($errors->has('end_time')) {{$errors->first('end_time')}} @endif</span>
                  </div><!-- /.input group -->
                </div><!-- /.form group -->
              </div>
            </div>
          </div><!-- row ends -->

          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
              <input type = "hidden" name = "is_active" value = "yes">
                {{Form::token()}}
                <button type="submit" class="btn btn-success"  @if(AccessController::checkPermission('contact-us', 'can_create') == false) disabled @endif>Save</button>
              </div>
            </div>
          </div>
        </form>
        <input type = "hidden" id = "ajax_get_class_ids_from_session_id" value = "{{URL::route('ajax-get-class-ids-from-session-id')}}">
        <input type = "hidden" id = "ajax-get-section-ids-from-class-id" value = "{{URL::route('ajax-get-section-ids-from-class-id')}}">
        
      </div><!-- tab 2 ends --> 
      
    </div>
  </div>
@stop

@section('custom-js') 
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>

    <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
      $(function () {

        //Timepicker
        $(".timepicker").timepicker({
          minuteStep: 5,
          showInputs: false
        });      
      });
    
    function updateClassList(default_class) {
      var session_id = $('#session_id_list').val();
      var url = $('#ajax_get_class_ids_from_session_id').val();
      
      if (session_id == 0) return;
      $('#class_id_list').html('<option value="0">Loading...</option>');
      $.ajax( {
                    "url": url,
                    //"contentType": "application/json",
                    "data": {"session_id" : session_id},
                    "method": "GET",
                    //"dataType": "json"
                    } ).done(function(data) 
                    {
                      $('#class_id_list').html(data);
                      if (typeof(default_class) != 'undefined') {
                        $('#class_id_list').val(default_class);
                      }
                    });
        
    }

    function updateSectionList(class_id, default_section) {
      if (typeof(class_id) == 'undefined')
        class_id = $('#class_id_list').val();

      if (class_id == 0) return;

      var url = $('#ajax-get-section-ids-from-class-id').val();
      $('#section_id_list').html('<option value="0">Loading...</option>');
      $.ajax( {
                    "url": url,
                    //"contentType": "application/json",
                    "data": {"class_id" : class_id},
                    "method": "GET",
                    //"dataType": "json"
                    } ).done(function(data) 
                    {
                      $('#section_id_list').html(data);
                      if (typeof(default_section) != 'undefined') {
                        $('#section_id_list').val(default_section);
                      }
                    });
        
    }

    function updateClass() {
      var session_id = $('#session_id').val();
      var url = $('#ajax_get_class_ids_from_session_id').val();
      
      if (session_id == 0) return;
      $('#class_id').html('<option value="0">Loading...</option>');
      $.ajax( {
                "url": url,
                //"contentType": "application/json",
                "data": {"session_id" : session_id},
                "method": "GET",
                //"dataType": "json"
                } ).done(function(data) 
                {
                  $('#class_id').html(data);
                });
    }

    function searchRoutine() {
      var url = $('#current_url').val();
      var session_id = $('#session_id_list').val();
      var class_id = $('#class_id_list').val();
      var section_id = $('#section_id_list').val();
      var day = $('#day_list').val();

      if(session_id!=0 && class_id!=0 && section_id!=0 && day) {
        url += '?session_id=' + session_id + '&class_id=' + class_id + '&section_id=' + section_id + '&day=' + day;
        window.location.replace(url);
      }
    }

    $(function()
    {
      var default_class = "{{ Input::get('class_id', 0) }}";
      var default_section = "{{ Input::get('section_id', 0) }}";
      //var default_class = /class_id=([^&]+)/.exec(location.search);
      //default_class = default_class ? default_class[1] : undefined;

      updateClassList(default_class);
      updateSectionList(default_class, default_section);

      updateClass();

      $('#session_id').change(function()
      {
        updateClass();
      });

      $('#class_id').change(function()
      {
        var class_id = $(this).val();
        var url = $('#ajax-get-section-ids-from-class-id').val();
        $('#section_id').html('<option value="0">Loading...</option>');
         $.ajax( {
                      "url": url,
                      //"contentType": "application/json",
                      "data": {"class_id" : class_id},
                      "method": "GET",
                      //"dataType": "json"
                      } ).done(function(data) 
                      {
                        $('#section_id').html(data);
                      });
        //
      });

      $('#session_id_list').change(function()
      {
        updateClassList();
        searchRoutine();
      });

      $('#class_id_list').change(function()
      {
        updateSectionList();
        searchRoutine();
      });

      $('#section_id_list').change(function()
      {
        searchRoutine();
      });

      $('#day_list').change(function()
      {
        searchRoutine();
      });

      $('#search_list').click(function()
      {
        searchRoutine();   
      });

    });

  </script>
@stop