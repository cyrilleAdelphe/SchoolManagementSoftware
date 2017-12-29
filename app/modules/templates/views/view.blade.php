@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}
<div class = 'container'>
	@if($data)
		<table class = "table table-striped table-hover table-bordered">
			<tbody>
				<tr>
					<th>Template Name :</th>
					<td>{{$data->template_name}}</td>
				</tr>
				<tr>
					<th>Template Alias :</th>
					<td>{{$data->template_alias}}</td>
				</tr>

				<tr>
					<th>Position :</th>
					<td>{{$data->position_name}}</td>
				</tr>

				<tr>
					<th>Order :</th>
					<td>{{$data->sort_order}}</td>
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

