@extends('dormitory-room.views.tabs')

@section('tab-content')
<div class="row">
	<div class="col-sm-3">
    <div class="form-group">
      <label>Select Session</label>
      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
        $selected = 
          Input::has('academic_session_id') ?
          Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      <label>Select Dormitory</label>
      {{HelperController::generateSelectList('DormitoryRoom', 'dormitory_code', 'id', 'dormitory_id', 
	        $selected = 
	          Input::has('dormitory_id') ?
	          Input::get('dormitory_id') : '')}}
    </div>
  </div>
</div>

<div id="studentList">
</div>

<input type="hidden" id="_token" value="{{csrf_token()}}">

<input type="hidden" id="query_url" value="{{URL::route('dormitory-student-post-list')}}">

@stop

@section('custom-js')
<script>

function updateStudentList()
{

  if($('#academic_session_id').val()!=0 && $('#dormitory_id').val()!=0)
  {
    $('#studentList').html('<img src="{{asset('sms/assets/img/Loading_icon.gif')}}" />');
    $.post($('#query_url').val(),
            {
              'academic_session_id': $('#academic_session_id').val(),
              'dormitory_id': $('#dormitory_id').val(),
              '_token': $('#_token').val()
            },
            function(data, status) {
              if(status) {
                $('#studentList').html(data);
              }
            }
    );
  }
}

$(function() {
  $(document).on('change', '#academic_session_id', updateStudentList);

  $(document).on('change', '#dormitory_id', updateStudentList);

  updateStudentList();
});

</script>
@stop