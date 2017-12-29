@extends('assignments.views.tabs')

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
      <label>Select class</label>
      <select name="class_id" id="class_id" class="form-control">
				<option value="0">--Select Session First--</option>
			</select>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      <label>Select section</label>
      <select name="section_id" id="section_id" class="form-control">
				<option value="0">--Select Class First--</option>
			</select>
    </div>
  </div>

</div><!-- row ends -->

<div class="row">
  <div class="col-sm-12">
    @if($class_id && $section_id && $last_updated_data)
      <h4 class="text-red">
        Class {{Classes::find($class_id)->class_name}} {{Section::find($section_id)->section_name}} 
        @if($last_updated_data)
        <small>
          Last updated by : {{$last_updated_data->updated_by}} at 
          <span class="text-green">{{DateTime::createFromFormat('Y-m-d H:i:s', $last_updated_data->updated_at)->format('d F Y, g:i A')}}</span>
        </small>
        @endif
      </h4>
    @endif

      
      @if($children_file_array==='')
        <h4 class="text-red">SELECT CLASS/SECTION/SESSION FIRST</h4>
      @elseif(sizeof($children_file_array)==0)
        <h4 class="text-red">NO ASSIGNMENT UPLOADED</h4>
      @else
      <table id="pageList" class="table table-bordered table-striped">
        <thead>
          <th>SN</th>
          <th>File name</th>
          <th>Subject</th>
          <th>Uploaded by</th>
          <th>Upload date</th>
          <th>Downloaded</th>
          <th>Action</th>
        </thead>
        <tbody>
        @define $i=1
        @foreach($children_file_array as $child)
          <tr>
            <td>{{$i++}}</td>
            <td>{{$child->filename}}</td>
            <td>{{$child->subject_name}}</td>
            <td>{{$child->created_by}}</td>
            <td>{{substr($child->created_at, 0, 10)}}</td>
            <td>{{$child->no_of_downloads}}</td>
            <td>
              <a href="{{$child->download_link}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="Download" >
                <i class="fa fa-fw fa-download"></i>
              </a>
            
              <a href="#" data-toggle="modal" data-target="#delete{{$child->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
                <i class="fa fa-fw fa-trash"></i>
              </a>
              @include('assignments.views.delete-modal')

              <a href="{{URL::route('assignments-push-notification', [$child->assignment_id, $class_id, $section_id])}}" class="btn bg-purple btn-flat" data-toggle="tooltip" title="Push Notification" >
                <i class="fa fa-fw fa-info"></i>
              </a>
            </td>

          </tr>
        @endforeach
      @endif
      </tbody>
    </table>
  </div>
</div>

<input type="hidden" id="class_ajax" value="{{URL::route('ajax-classes-get-classes')}}" />

<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />

<input type="hidden" name= "default_academic_session" id="default_academic_session" value="{{Input::has('academic_session_id')?Input::get('academic_session_id'):''}}" />

<input type="hidden" name= "default_class" id="default_class" value="{{Input::has('class_id')?Input::get('class_id'):''}}" />

<input type="hidden" name= "default_section" id="default_section" value="{{Input::has('section_id')?Input::get('section_id'):''}}" />

@stop

@section('custom-js')


<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script>

	$(document).on('change', '#academic_session_id', updateClassList);
	$(document).on('change', '#class_id', function() {
    updateSectionList();
    //updateFilesTable();// this will update the table even when the section (which came from previous class) is invalid for the current class
  });
  $(document).on('change', '#section_id', updateFilesTable);

  /**
   * Update the student table corresponding to the selected class, section and subject
  **/
  function updateFilesTable()
  {
    var academic_session_id = $('#academic_session_id').val();
    var class_id = $('#class_id').val();
    var section_id = $('#section_id').val();
    
    if(class_id!=0 && section_id!=0 && academic_session_id!=0)
    {
      var current_url = $('#current_url').val();
      current_url += '?class_id=' + class_id + '&section_id=' + section_id + '&academic_session_id=' + academic_session_id;
      window.location.replace(current_url);
    }
  }

	if($('#academic_session_id').val() != 0)
	{
		updateClassList($('#default_class').val());
		updateSectionList($('#default_section').val());
    updateFilesTable();
	}
</script>


@stop