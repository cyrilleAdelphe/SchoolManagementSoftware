@extends('backend.'.$role.'.main')

@section('content')

<div class = 'container'>
	<div class="row">
                  <div class="col-sm-3" style="margin-bottom:15px">
                    <a  href="#" onclick="history.go(-1);" class="btn btn-danger"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
                </div><!-- row ends -->
            </div>

	@if($data)

		<table class = "table table-striped table-hover table-bordered">
			<tbody>
				<tr>
					<th>Class Name :</th>
					<td>{{$data->class_name}}</td>
				</tr>
				<tr>
					<th>Class Code :</th>
					<td>{{$data->class_code}}</td>
				</tr>
				<tr>
					<th>Display Order :</th>
					<td>{{$data->sort_order}}</td>
				</tr>
				
				<tr>
					<th>Actvie:</th>
					<td>{{$data->is_active}}</td>
				</tr>
			</body>
		</table>
	@else
		<h1>Record not found</h1>
	@endif
</div>

@stop

