<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
	  <table class = 'table table-striped table-hover table-bordered scrollable'>
			<tbody class = 'search-table'>
					<tr>
						<th>S.No</th>
						<th>Student ID</th>
						<th>Student Name</th>
						<th>Remarks</th>
						<th>Action</th>
					</tr>
					@if($data['count'])
						@define $i=1
						@foreach($data['data'] as $d)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$d->student_id}}</td>
								<td><a href="{{URL::route('student-view',$d->student_id)}}"  @if(AccessController::checkPermission('students', 'can_view') == false) disabled @endif>{{$d->student_name}}</a></td>
								<td>{{$d->remarks}}</td>
								<td>
									<a href="{{URL::route($module_name.'-edit-get', $d->id)}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="EDIT"  @if(AccessController::checkPermission('dormitory-student', 'can_edit') == false) disabled @endif>
		                <i class="fa fa-fw fa-edit"></i>
		              </a>

		              <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger" type="button"  @if(AccessController::checkPermission('dormitory-student', 'can_delete') == false) disabled @endif>
		                <i class="fa fa-fw fa-trash"></i>
		              </a>
              		@include($module_name.'.views.delete-modal')
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td>{{$data['message']}}</td>
						</tr>
					@endif
				</tbody>
				{{Form::token()}}
			
		</table>
	</div>
</div>


