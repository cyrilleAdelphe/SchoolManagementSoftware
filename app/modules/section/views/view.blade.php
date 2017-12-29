@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}
<div class = 'container'>
	@if($data)
		<table class = "table table-striped table-hover table-bordered">
			<tbody>
				<tr>
					<th>Section Name :</th>
					<td>{{$data->section_name}}</td>
				</tr>
				
				<tr>
					<th>Section Code :</th>
					<td>{{$data->section_code}}</td>
				</tr>
				
				<tr>
					<th>Is Actvie:</th>
					<td>{{$data->is_active}}</td>
				</tr>
			</body>
		</table>
	@else
		<h1>Record not found</h1>
	@endif
</div>

@stop

