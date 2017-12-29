<div id="delete_transportation_staff{{$d->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation</h4>
      </div>
      <div class="modal-body">
            <h4 class="text-red">Are you sure you want to delete?</h4>
      </div>
      <div class="modal-footer">
          <form action="{{URL::route('transportation-staff-delete-single-post',$d->id)}}" method="post">
              <button name="delete{{$d->id}}" value="delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat btn-lg pull-left">
                <i class="fa fa-fw fa-trash"></i>
              </button>
              {{ Form::token() }}
          </form>
          <button type="button" class="btn btn-default btn-lg pull-right btn-flat" data-dismiss="modal">Close</button>
          
      </div>
     </div>
  </div>
</div>