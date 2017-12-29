@extends('backend.'.$role.'.main')

@section('custom-css')
	<link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet"/>
@stop

@section('page-header')
  <h1>Subject Sub Topics</h1>
@stop


@section('content')
		<div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li><a href="{{URL::route('cas-grade-settings-list')}}">Grading</a></li>
                  <li class="active"><a href="#">Sub-topics</a></li>
                  <li><a href="{{URL::route('remark-setting-list')}}">Remarks setting</a></li>
                </ul> 
            <div class="tab-content">

                <div class="tab-pane active" id="tab_2">
                    <div class="row">
                      <div class="col-sm-2">
                        <div class="form-group">
                          <label>Session</label>
                          @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();
                          <select id = "session" name = "session" class = "form-control">
                              @foreach($sessions as $s)
                              @if($s->is_current == 'yes')
                                @define $current_session = $s->id
                              @endif
                              <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                              @endforeach
                           </select>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                           <label>Class</label>
                           <select id = "class_id" name = "class_id" class = "form-control">
			                     </select>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                           <label>Section</label>
                          <select id = "section_id" class = "form-control" name = "section_id">
			                <option value = "0"> --Select Class First-- </option>
			              </select>
                        </div>
                      </div>
                    </div><!-- row ends -->

                    <div id = "ajax-content">
	                    <!-- ends -->
	                </div>
                </div><!-- tab 2 ends -->
            </div>
        </div>
        
@stop

@section('custom-js')
<script src="{{asset('sms/assets/js/lity.min.js')}}"></script>
<script>

	
	$(function()
    {
    	
        updateClassList('session', 'class_id', '{{(int) Input::old("class_id")}}');
        

        $('#session').change(function()
        {
          updateClassList('session', 'class_id', '{{(int) Input::old("class_id")}}');
        });

        $('#class_id').change(function()
        {
          updateSectionList('session', 'class_id', 'section_id', '{{(int) Input::old("section_id")}}');
        });

        $('#section_id').change(function()
        {
          updateSubjectList();
        });

        function updateClassList(session_id, class_id, default_class_id)
        {
          var class_obj = $('#' + class_id);
          var session_obj = $('#' + session_id);

          class_obj.html('loading....')

          $.ajax({
            ///// CAS-v1-changes-made-here ////
            url : '{{URL::route("ajax-get-related-classes")}}',
            ///// CAS-v1-changes-made-here ////
            data: {'session_id' : session_obj.val(), 'default_class_id' : default_class_id},
            method: 'get'
          }).done(function (data)
          {

              class_obj.html(data);
              updateSectionList('session', 'class_id', 'section_id', '{{(int) Input::old("section_id")}}');
          });
          
        }

        function updateSectionList(session_id, class_id, section_id, default_section_id)
        {
        	
          var class_obj = $('#' + class_id);
          var session_obj = $('#' + session_id);
          var section_obj = $('#' + section_id);

          section_obj.html('loading....');
          $.ajax({
            ///// CAS-v1-changes-made-here ////
            url : '{{URL::route("ajax-get-related-sections")}}',
            ///// CAS-v1-changes-made-here ////
            data: {'session_id' : session_obj.val(), 'class_id' : class_obj.val(), 'default_section_id' : default_section_id},
            method: 'get'
          }).done(function (data)
          {
              section_obj.html(data);
              updateSubjectList();
          }); 
        }

        function updateSubjectList()
        {
          $('#ajax-content').html('loading...');
          $.ajax({
            url : '{{URL::route("cas-ajax-get-subject-list-from-session-id-class-id-and-section-id")}}',
            data: {'session_id' : $('#session').val(), 'class_id' : $('#class_id').val(), 'section_id' : $('#section_id').val()},
            method: 'get'
          }).done(function (data)
          {
              $('#ajax-content').html(data);
          }); 
        }
    });
</script>
@stop