@extends('subject.views.tabs')

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
</div>

<table id="pageList" class="table table-bordered table-striped">
  @if($subjects==='')
  	<div class="alert alert-warning alert-dismissable">
  		<h4><i class="icon fa fa-warning"></i>SELECT CLASS AND SECTION FIRST</h4>
		
 	</div>
  @elseif(sizeof($subjects)==0)
	  	<div class="alert alert-warning alert-dismissable">
	  		<h4><i class="icon fa fa-warning"></i>NO DATA</h4>
			
	 	</div>
	@else
	<h4 class="text-red">
    Class {{Classes::find(Input::get('class_id',0))->class_name}} {{Section::find(Input::get('section_id',0))->section_name}} 
  </h4>
  <thead>
    <tr>
      <th>SN</th>
      <th>Name</th>
      <th>Full Marks</th>
      <th>Pass Marks</th>
      <th>Remarks</th>
      <th>Action</th>
    </tr>
  </thead>
  
  <tbody id="subject_list">
  	
	  	
		  	@define $i = 1 
		  	@foreach($subjects as $d)
		  		<tr>
		  			<td>{{$i++}}</td>
		  			<td>{{$d->subject_name}}</td>
		  			<td>{{$d->full_marks}}</td>
		  			<td>{{$d->pass_marks}}</td>
		  			<td>{{$d->remarks}}</td>
		  			<td>
		  				<a href="#" data-toggle="modal" data-target="#edit{{$d->id}}" data-toggle="tooltip" title="Edit" class="btn btn-success btn-flat" type="button" @if(!AccessController::checkPermission('subject', 'can_edit')) disabled @endif>
                <i class="fa fa-fw fa-edit"></i> Edit
               </a>
               @include('subject.views.edit-modal')

		  				<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('subject', 'can_delete')) disabled @endif>
                <i class="fa fa-fw fa-trash"></i> Delete
               </a>
               @include('subject.views.delete-modal')
            		</td>
		  		</tr>
		  	@endforeach
		  
		
  </tbody>
  @endif
</table>

<input type="hidden" id="class_ajax" value="{{URL::route('ajax-classes-get-classes')}}" />
<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />
<input type = "hidden" id = "ajax-get-section-ids-from-class-id" value = "{{URL::route('ajax-get-section-ids-from-class-id')}}">
<input type="hidden" id="default_section" value="{{Input::has('section_id')?Input::get('section_id'):''}}" />
<input type="hidden" name= "default_academic_session" id="default_academic_session" value="{{Input::has('academic_session_id')?Input::get('academic_session_id'):''}}" />

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

<script>
	
	/**
	 * Update the table showing subjects corresponding to the selected class and section
	**/
	function updateSubjectList()
	{
		var academic_session_id = $('#academic_session_id').val();
		var class_id = $('#class_id').val();
		var section_id = $('#section_id').val();
				
		if(academic_session_id!=0 && class_id!=0 && section_id!=0)
		{
			var current_url = $('#current_url').val();
      alert(current_url);
			current_url += '?class_id=' + class_id + '&section_id=' + section_id + '&academic_session_id=' + academic_session_id;
			window.location.replace(current_url);
		}
	}

	function updateSectionList(class_id, default_section) {
    if (typeof(class_id) == 'undefined')
      class_id = $('#class_id').val();

    if (class_id == 0) return;

    var url = $('#ajax-get-section-ids-from-class-id').val();
    
    $.ajax( {
	    "url": url,
	    //"contentType": "application/json",
	    "data": {"class_id" : class_id},
	    "method": "GET",
	    //"dataType": "json"
    } ).done(function(data) 
    {
      $('#section_id').html(data);
      if (typeof(default_section) != 'undefined') {
        $('#section_id').val(default_section);
      }
    });
      
  }


	$(function() {
		var default_class = "{{ Input::get('class_id', 0) }}";
		var default_section = "{{ Input::get('section_id', 0) }}";
		
		if($('#academic_session_id').val() != 0)
		{
			updateClassList(default_class);
			updateSectionList(default_class, default_section);
		}

		$(document).on('change', '#academic_session_id', updateClassList);
	
		$('#class_id').change(function()
    {
      var class_id = $(this).val();
      
        $.ajax( {
                      "url": "{{URL::route('exam-details-get-section-ids')}}",
                      "data": {"class_id" : class_id},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#section_id').html(data);
                });   
      });
		
	    $("#section_id").bind('change', updateSubjectList);

	});
</script>
@stop
