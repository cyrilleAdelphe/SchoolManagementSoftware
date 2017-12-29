<div id="fee-delete{{$f->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation</h4>
      </div>
      <div class="modal-body">
        <div class="row-fluid">
          <div class="col-md-9">
             Are you sure you want to delete?
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="modal-footer">
          
          <form action="{{URL::route('billing-delete-fee-post', [$f->id])}}" method="post">
          
              <input type="hidden" name="id" value="{{$f->id}}">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>             
              <button name="delete{{$f->id}}" value="delete{{$f->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat">
                <i class="fa fa-fw fa-trash"></i>
              </button>
              {{ Form::token() }}
          </form>
      </div>
     </div>
  </div>
</div>