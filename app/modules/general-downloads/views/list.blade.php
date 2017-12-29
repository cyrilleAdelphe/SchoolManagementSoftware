<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
    <table class = 'table table-striped table-hover table-bordered scrollable'>
			{{$tableHeaders}}
			<tbody class = 'search-table'>
				<?php $i = 1; ?>
				{{$searchColumns}}
				@if($data['count'])
					@foreach($data['data'] as $d)
						<tr>
							<td>{{$i++}}</td>
							<td>{{$d->filename}}</td>
							<td>
								<a href="{{$d->download_link}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="Download" @if(!AccessController::checkPermission('student-document', 'can_view')) disabled @endif>
                	<i class="fa fa-fw fa-download"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('student-document', 'can_delete')) disabled @endif>
	                <i class="fa fa-fw fa-trash"></i>
	              </a>
	              @include('include.modal-delete')
              </td>
						</tr>
					@endforeach
				@else
					<div class="alert alert-warning alert-dismissable">
						<h4>
							<i class="icon fa fa-warning"></i>No Data Found
						</h4>
					</div>
				@endif
				</tbody>
			
		</table>
	</div>
</div> 

<div class = "container">
	<div class = 'paginate'>
		@if($data['count'])
			{{$data['data']->appends($queryString)->links()}}
		@endif
	</div>
</div>

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>