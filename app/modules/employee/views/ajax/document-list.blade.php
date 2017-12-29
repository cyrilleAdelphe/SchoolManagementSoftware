<div class="main-head" style="margin-top:15px" >
	General Documents
</div>

<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
    <table class = 'table table-striped table-hover table-bordered scrollable'>
			<tr>
				<th>S.No.</th>
				<th>Filename</th>
				<th>Action</th>
			</tr>
			<tbody class = 'search-table'>
				<?php $i = 1; ?>
				@if(count($documents))
					@foreach($documents as $d)
						<tr>
							<td>{{$i++}}</td>
							<td>{{$d->filename}}</td>
							<td>
								<a href="{{$d->download_link}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="Download" >
                	<i class="fa fa-fw fa-download"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
	                <i class="fa fa-fw fa-trash"></i>
	              </a>
	              @include('include.modal-delete', array('module_name' => 'student-document'))
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

