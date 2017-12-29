@extends('backend.'.$role.'.main')

@section('content')

<form action = "{{URL::route('billing-create-extra-fee-post')}}" method = "post">


<div class = "row">
  <div class = "col-md-4">
  @define $fees = BillingFee::lists('fee_category', 'id')
  	<select name = "fee_id" id = "fee_id" class = "form-control">
  	@foreach($fees as $fee_id => $fee_category)
  		<option value = "{{$fee_id}}">{{$fee_category}}</option>
  	@endforeach
  	</select>
  </div>
</div>
<br/>


	
<div class="row">
    <div class="col-sm-2">
    {{
      HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
      $selected = 
        Input::has('academic_session_id') ?
        Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])
    }}
    </div>
    
    <div class="col-sm-2">
      <div class="form-group">
        <select class="form-control" id="class_id" name = "class_id">
          <option value="0">-- Select Session First --</option>
        </select>
      </div>
    </div>
    <div class="col-sm-2">
      <div class="form-group">
        <select class="form-control" id="section_id" name = "section_id">
          <option value="">-- Select Section First --</option>
        </select>
      </div>
    </div>
</div> <!-- row ends -->


<div id = "ajax-content">

	<div class = "row">

	</div>

</div>
{{Form::token()}}
</form>

<input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
<input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>


@stop

@section('custom-js')
<script>	
	updateClassList();
    updateSectionList();

      $('#academic_session_id').change(function(e)
      {
        updateClassList();
        
      });

      $('#class_id').change(function(e)
      {
        updateSectionList();
        
      });

      $('#section_id').change(function(e)
      {
        updateStudentList();
      });

      $('#fee_id').change(function(e)
      {
        updateStudentList();
      });

      function updateClassList()
      {
        var session_id = $('#academic_session_id').val();
        $('#class_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id, 'extra':''}
        }).done(function(data)
        {
          $('#class_id').html(data);
          updateSectionList();
        });
      }

      function updateSectionList()
      {
        var class_id = $('#class_id').val();
        $('#section_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-section-list").val(),
            'method' : 'GET',
            'data' : {'class_id' : class_id, 'extra':''}
        }).done(function(data)
        {
          $('#section_id').html(data);
          updateStudentList();
        });
      }

      function updateStudentList()
      {
      	var session_id = $('#academic_session_id').val();
      	var class_id = $('#class_id').val();
      	var section_id = $('#section_id').val();
        var fee_id = $('#fee_id').val();

        $('#ajax-content').html('loading...')
        $.ajax({
          'url' : "{{URL::route('api-billing-extra-fee-student-list')}}",
          'method' : 'GET',
          'data' : {'academic_session_id' : session_id, 'class_id' : class_id, 'section_id' : section_id, 'fee_id' : fee_id}
        }).done(function(data)
        {
          $('#ajax-content').html(data);
        });
      }
</script>
@stop