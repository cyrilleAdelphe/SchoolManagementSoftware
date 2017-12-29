@extends('academic-session.views.form-tabs')



@section('tab-content')

<div class = 'content'>
	<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
	@if($data)
		<table class = "table table-striped table-hover table-bordered">
			<tbody>
				<tr>
					<th>Session Start (AD) :</th>
					<td>{{$data->session_start_date_in_ad}}</td>
				</tr>
				<tr>
					<th>Session Start (BS) :</th>
					<td>{{$data->session_start_date_in_bs}}</td>
				</tr>
				<tr>
					<th>Session End (AD) :</th>
					<td>{{$data->session_end_date_in_ad}}</td>
				</tr>
				<tr>
					<th>Session End (BS) :</th>
					<td>{{$data->session_end_date_in_bs}}</td>
				</tr>
				<tr>
					<th>Is Actvie:</th>
					<td>{{$data->is_active}}</td>
				</tr>
			</body>
		</table>
	@else
		<h4 class="text-red">Record not found</h4>
	@endif
</div>

@stop

