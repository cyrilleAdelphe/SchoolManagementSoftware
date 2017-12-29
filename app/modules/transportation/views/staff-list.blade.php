<table id="pageList" class="table table-bordered table-striped">
  @if(count($assigned_staffs))
  <thead>
    <tr>
      <th>SN</th>
      <th>Bus</th>
      <th>Staff / Employee ID</th>
      <th>Employee Name</th>
      
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i = 1
    @foreach($assigned_staffs as $d)
    <tr>
      <td>{{$i++}}</td>
      <td>{{$d->bus_code}}</td>
      <td>{{$d->employee_id}}</td>
      <td>{{$d->employee_name}}</td>
      
      <td>
      <a data-toggle="tooltip" title="Locate" href="{{URL::route('transportation-view-staff', $d->unique_transportation_id)}}" class="btn btn-info btn-flat"> <i class="fa fa-fw fa-map-marker"></i></a>
      <a data-toggle="tooltip" title="Edit" href="{{URL::route('transportation-edit-assign-staffs-get', $d->id)}}" class="btn btn-success btn-flat"> <i class="fa fa-fw fa-edit"></i></a>
      <a href="#" data-toggle="modal" data-target="#delete_transportation_staff{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('transportation', 'can_delete')) disabled @endif>
                <i class="fa fa-fw fa-trash"></i>
               </a>

               
               @include('transportation.views.transportation-staff-delete-modal')
      </td>

    </tr>
    @endforeach
  </tbody>
  @else
  <tr><td><div class="alert alert-warning alert-dismissable">
  <h4><i class="icon fa fa-warning"></i>No Data Found</h4>
  </div></td></tr>
  @endif
</table>