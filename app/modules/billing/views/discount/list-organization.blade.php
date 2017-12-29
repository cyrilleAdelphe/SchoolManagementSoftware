@extends('backend.'.$role.'.main')

@section('content')

	<div class = "content">
		<div class="bill-title">Organization Manager</div>
		
		<table class = 'table table-striped table-hover table-bordered'>
			<thead>
				<tr>
					<th>SN</th>
					<th>Organization Name</th>
					<th>Generate Invoice</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@define $i = 1;
				@foreach($data as $d)
					<Tr>
						<td>{{$i++}}</td>
						<td>{{$d->organization_name}}</td>
						<td>{{$d->generate_invoice}}</td>
						<td>
							<a href = "{{URL::route('billing-discount-organization-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit"><i class="fa fa-fw fa-edit"></i></button></a>
							<a href="#" data-toggle="modal" data-target="#organization-delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
	                            <i class="fa fa-fw fa-trash"></i>
	                        </a>
	                        
                        	@include('billing.views.modal.organization-delete')
						</td>
					</Tr>
				@endforeach
			</tbody>
		</table>
		<a href = "{{URL::route('billing-discount-organization-create-get')}}" class = "btn btn-primary btn-flat"> <i class="fa fa-fw fa-plus"></i> Create organization</a>
	</div>

@stop