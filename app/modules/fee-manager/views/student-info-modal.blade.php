<div id="student{{$d->student_id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Student Info</h4>
      </div>
      <div class="modal-body">
        <div class="row-fluid">
          <div class="col-md-9">
             Student Name: {{$d->student_name}}<br />
             Student ID: {{$d->student_id}}
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
     </div>
  </div>
</div>