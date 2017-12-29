@extends('backend.'.$role.'.main')

@section('content')
             
<div class="tab-pane active" id="tab_1">
    {{$actionButtons}}
    
        <div class = 'form-group'>
			<label for = 'title'  class = 'control-label'>Title :</label>
			<p>{{$data->title}}</p>
		</div>

		<div class = 'form-group'>
			<label for = 'venue'  class = 'control-label'>Venue :</label>
			<p>{{$data->venue}}</p>
		</div>

		<div class = 'form-group'>
			<label for = 'from_ad'  class = 'control-label'>Start Date :</label>
			<p>{{$data->from_ad}}</p>
		</div>
		<div class = 'form-group'>
			<label for = 'to_ad'  class = 'control-label'>End Date :</label>
			<p>{{$data->to_ad}}</p>
		</div>
		
		<div class = 'form-group'>
			<label for = 'for_students'  class = 'control-label'>For Students :</label>
			<p>{{$data->for_students}}</p>
		</div>
		<div class = 'form-group'>
			<label for = 'for_teachers'  class = 'control-label'>For Teachers :</label>
			<p>{{$data->for_teachers}}</p>
		</div>
		<div class = 'form-group'>
			<label for = 'for_management_staff'  class = 'control-label'>For Management Staff :</label>
			<p>{{$data->for_management_staff}}</p>
		</div>
		<div class = 'form-group'>
			<label for = 'for_parents'  class = 'control-label'>For Parents :</label>
			<p>{{$data->for_parents}}</p>
		</div>

		<div class = 'form-group'>
			<label for = 'description'  class = 'control-label'>Description :</label>
			<p>{{$data->description}}</p>
		</div>

		

		<div class = "form-group">
			<label for = "is_active">Is Active:</label>
			<p>{{$data->is_active}}</p>
		</div>                         
</div>
     
@stop