@extends('student.views.form-tabs')

@section('tab-content')
	<form method = "post" action = "{{URL::route($module_name.'-import-excel-post')}}" id = "backendForm" enctype = "multipart/form-data">
    
    <div id = "div_for_registered_class_id">
      <div class = 'form-group @if($errors->has("registered_class_id")) {{"has-error"}} @endif'>
        <label for = 'registered_class_id'  class = 'control-label'>Class:</label>
          <select id = "registered_class_id" name = "registered_class_id" class = "form-control">
            <option>Please select session first</option>
          </select>
        <span class = 'help-block'>@if($errors->has('registered_class_id')) {{$errors->first('registered_class_id')}} @endif</span>
      </div>
    </div>

    <div id = "div_for_registered_section_code">
      <div class = 'form-group @if($errors->has("registered_section_code")) {{"has-error"}} @endif'>
        <label for = 'registered_section_code'  class = 'control-label'>Section:</label>
          <select id = "registered_section_code" name = "registered_section_code" class = "form-control">
            <option>Please select class first</option>
          </select>
        <span class = 'help-block'>@if($errors->has('registered_section_code')) {{$errors->first('registered_section_code')}} @endif</span>
      </div>
    </div>

		<div class = 'form-group @if($errors->has("excel_file")) {{"has-error"}} @endif'>
        <label for = 'excel_file'  class = 'control-label'>Upload Excel File</label>
        <input type = 'file' name = 'excel_file'><span class = 'help-block'>@if($errors->has('excel_file')) {{$errors->first('excel_file')}} @endif</span>
    </div>

    <input type = "hidden" name = "registered_session_id" value = "{{ HelperController::getCurrentSession() }}" id = "registered_session_id" />

    <div class="form-group">
        <button class="btn btn-success btn-flat btn-lg" type="submit">Submit</button>
    </div>

    {{ Form::token() }}
	</form>
@stop

@section('custom-js')

  <script type = "text/javascript">
    function updateClass()
    {
      var registered_session_id = $('#registered_session_id').val();
      if(registered_session_id == 0)
      {
        return;
      }

      $.ajax({
                      "url": "{{URL::route('student-ajax-active-classes')}}",
                      "data": {"session_id" : registered_session_id},
                      "method": "GET"
                    }).done(function(data) {
                  
                  $('#registered_class_id').html(data);
          });
    }
    $(function()
    {
      var ajax_url = $('#ajax_url').val();
      
      updateClass();

      $('#registered_session_id').change(function()
      {
        updateClass();
      });
    
      $('#registered_class_id').change(function()
      {

        var registered_class_id = $(this).val();
        $('#registered_section_code').html('<option value="0">Loading...</option>');
        $.ajax( {
                      "url": "{{URL::route('student-ajax-active-sections')}}",
                      "data": {"class_id" : registered_class_id},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#registered_section_code').html(data);
                });   
      });

    });
  </script>

@stop