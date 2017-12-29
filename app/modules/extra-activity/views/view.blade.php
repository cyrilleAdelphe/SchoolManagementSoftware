@extends('backend.'.$current_user->role.'.main')

@section('content')
             
	<div class="tab-pane active" id="tab_1">

	    @if($data)
		    {{$actionButtons}}

		    <div class = 'form-group'>
					<label for = 'name'  class = 'control-label'>Event title:</label>
					<p>{{$data->title}}</p>
				</div>

				<div class = 'form-group'>
					<label for = 'name'  class = 'control-label'>Event code:</label>
					<p>{{$data->event_code}}</p>
				</div>

				<div class = 'form-group'>
					<label for = 'name'  class = 'control-label'>Description:</label>
					<p>{{$data->description}}</p>
				</div>

				<div class = 'form-group'>
					<label for = 'name'  class = 'control-label'>Highlighted students:</label>
					@foreach($data->student_list as $student)
						<p>{{$student->student_name}}: {{$student->remarks}}</p>
					@endforeach
				</div>

				
		@else
			<h1>Record Not Found</h1>
		@endif
	</div>
@stop