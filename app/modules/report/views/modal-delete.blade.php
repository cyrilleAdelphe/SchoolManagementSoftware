<div id="deleteClassSection" class="modal fade" role="dialog">
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
          <form action="{{URL::route('report-delete-class-section-post')}}" method="post">
              <input type="hidden" name="session_id" value="{{ Input::get('session_id_list') }}">
              <input type="hidden" name="exam_id" value="{{ Input::get('exam_id_list') }}">
              <input type="hidden" name="class_id" value="{{ Input::get('class_id_list') }}">
              <input type="hidden" name="section_id" value="{{ $index }}">
                            
              <button name="delete-day" value="delete-day" data-toggle="tooltip" title="Delete" class="btn btn-danger">
                <i class="fa fa-fw fa-trash"></i>
              </button>
              {{ Form::token() }}
          </form>
      </div>
     </div>
  </div>
</div>