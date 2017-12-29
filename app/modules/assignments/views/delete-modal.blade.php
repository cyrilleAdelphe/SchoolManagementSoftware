<div id="delete{{$child->id}}" class="modal fade" role="dialog">
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
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <form action="{{URL::route('assignments-remove-file-post')}}" method="post">
              <input type="hidden" name="file_id" value="{{$child->id}}">
              <input type="hidden" name="google_file_id" value="{{$child->google_file_id}}">
              <input type="hidden" name="class_id" value="{{Input::get('class_id')}}">
              <input type="hidden" name="section_id" value="{{Input::get('section_id')}}">
              <input type="hidden" name="academic_session_id" value="{{Input::get('academic_session_id')}}">
              
              <button name="delete{{$child->id}}" value="delete{{$child->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger">
                <i class="fa fa-fw fa-trash"></i>
              </button>
              {{ Form::token() }}
          </form>
      </div>
     </div>
  </div>
</div>