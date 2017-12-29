<!-- modal for student detail starts -->
  <div id="student_{{$i}}" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content"> 
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{-- $d->student_name --}}</h4>                                          
          </div>               
          <div class="modal-body">
            <div class="row">
              <div class="col-md-3 col-sm-3 col-xs-4">
                Class
              </div>
              <div class="col-md-9 col-sm-9 col-xs-8">
                <span class='text-green'>
                  {{-- Classes::find($d->current_class_id)->class_name}} {{$d->current_section_code --}} 
                </span>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-sm-3 col-xs-4">
                Roll No.
              </div>
              <div class="col-md-9 col-sm-9 col-xs-8">
                {{-- $d->current_roll_number --}}
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-sm-3 col-xs-4">
                Contact No.
              </div>
              <div class="col-md-9 col-sm-9 col-xs-8">
                {{-- $d->student_email --}}
              </div>
            </div>
          </div>
          <div class="modal-footer">                                         
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    </div>
  </div>
<!-- modal for student detail ends --> 