@extends('backend.'.$role.'.main')

@section('custom-css')

@stop

@section('page-header')
	<h1>Assign CAS Marks</h1>
@stop

@section('content')
	<a href = "{{URL::route('teacher-cas-subtopics-list')}}" class = "btn btn-info">Go Back To Cas Topics</a>
	<div class = "note">
	CAS marks are in percent. Full marks is 100
	</div>
	<div class = "content">
		<form method = "post" action = "{{URL::route('teacher-cas-subtopics-assign-post', $subject_id)}}" id='assignMarksForm'>
			<div class = "row">
				<div class = "col-md-6">
					<div class = "form-group">
						<label>Select Subtopic</label>
						<select id = "sub_topic_id" class = "form-control" name = "sub_topic_id">
							<option value = "0">-- Select Sub Topic --</option>
							@foreach($data['sub_topic_list'] as $sub_topic_id => $sub_topic_name)
								<option value = "{{$sub_topic_id}}" @if($sub_topic_id == $data['current_sub_topic_id']) selected @endif>{{$sub_topic_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class = "col-md-6">
					<div class = "form-group">
						<input type = "hidden" id = "current_exam_id" value = "{{$data['current_exam_id']}}">
						<label>Select Exam</label>
						<select id = "exam_id" class = "form-control" name = "exam_id">
						</select>
					</div>
				</div>
			</div>
				<div class = "row" id = "ajax-content">
				</div>
		</form>
	</div>


@stop

@section('custom-js')
<script src="{{ asset('backend-js/submit-enable-disable.js') }}" type="text/javascript"></script>
	<script>
		$(function()
		{
			function getExamList(sub_topic_id, default_exam_id)
			{
				$('#exam_id').html('loading...');
				$.ajax(
				{
					url : "{{URL::route('cas-ajax-get-cas-assign-sub-topic-assign-mark-exam-list')}}",
					method : "get",
					data : {'sub_topic_id' : sub_topic_id, 'default_exam_id' : default_exam_id}
				}).done(function(data)
				{
					$('#exam_id').html(data);
					getStudentMarkList();
				});
			}

			function getStudentMarkList()
			{
				$('#ajax-content').html('loading....');
				$.ajax(
				{
					url : "{{URL::route('cas-ajax-get-cas-assign-sub-topic-assign-mark-student-list')}}",
					method : "get",
					data : {'sub_topic_id' : $('#sub_topic_id').val(), 'exam_id' : $('#exam_id').val()}
				}).done(function(data)
				{
					$('#ajax-content').html(data);
				});	
			}

			getExamList($('#sub_topic_id').val(), $('#current_exam_id').val());

			$('#sub_topic_id').change(function(e)
			{
				console.log($('#sub_topic_id').val());
				console.log($('#current_exam_id').val());
				getExamList($('#sub_topic_id').val(), $('#current_exam_id').val());
			});

			$('#exam_id').change(function(e)
			{
				getStudentMarkList();
			});
		});

		$('#ajax-content').on('change', '.exam_marks', function()
		{

			if($(this).val() > 100)
			{
				alert('Cas marks are in terms of percentage. Full marks is always 100%');
				$(this).val(100);
			}

		});
	</script>



@stop