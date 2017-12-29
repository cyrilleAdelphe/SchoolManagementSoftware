<div id="edit{{$d->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit</h4>
      </div>
      <div class="modal-body">
        <div class="row-fluid">
          <div class="col-md-9">
             <form method = "post" action = "{{URL::route($module_name.'-edit-post', $d->id)}}">
                <div class = 'form-group @if($errors->has("subject_name")) {{"has-error"}} @endif'>
                  <label for = 'exam_name'  class = 'control-label'>Exam Name</label>
                    
                  <input id="exam_name" name="exam_name" class="form-control required" type="text" placeholder="Enter exam name"
                            value= "{{$d->exam_name}}">
                        <span class = 'help-block'>
                          @if($errors->has('exam_name')) {{$errors->first('exam_name')}} @endif
                        </span>
                </div>

                <div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
                  <label for = 'session_id'  class = 'control-label'>Code</label>
                    
                  @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();
                  <select class = "form-control" class = "session_id" name = "session_id">
                    @foreach($sessions as $s)
                    @if($s->id == $d->session_id)
                      @define $current_session = $s->id
                    @endif
                    <option value = "{{$s->id}}" @if($s->id == $d->session_id) selected @endif>{{$s->session_name}}</option>
                    @endforeach
                  </select>
                </div>

                <div class = 'form-group @if($errors->has("parent_exam_id")) {{"has-error"}} @endif'>
                  <label for = 'parent_exam_id'  class = 'control-label'>Parent Exam</label>
                    
                  <select id = 'parent_exam_id' class = "form-control" name = "parent_exam_id">
                  @define $exams = ExamConfiguration::where('session_id', $current_session)->lists('exam_name', 'id');

                  <option value = "0">Root</option>
                  @foreach($exams as $exam_id => $exam_name)
                  <option value = "{{$exam_id}}" @if($exam_id == $d->parent_exam_id) selected @endif>{{$exam_name}}</option>
                  @endforeach
                  </select>
                </div>

                <div class="form-group">
                        <label>Start date:</label>
                  <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name = "exam_start_date_in_ad" data-mask="" data-inputmask="'alias': 'yyyy/mm/dd'" class="form-control" placeholder="Enter Joining date" value = "{{$d->exam_start_date_in_ad}}">
                    </div>
                </div>

                <div class="form-group">
                      <label>End date:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name = "exam_end_date_in_ad"  data-mask="" data-inputmask="'alias': 'yyyy/mm/dd'" class="form-control" placeholder="Enter Joining date"  value = "{{$d->exam_end_date_in_ad}}">
                      </div>
                    </div>

                <div class="form-group">
                      <label>Result Publish Date:</label>
                      <div class="input-group">
                        <input type="text" name = "result_publish_date" class="form-control" placeholder="Enter Joining date"  value = "{{$d->result_publish_date}}">
                      </div>
                    </div>

                <div class="form-group ">
                      <label>Weightage % for final</label>
                       <input id="weightage" name="weightage" class="form-control required" type="text" placeholder="Enter weightage"
            value= "{{$d->weightage}}">
                        <span class = 'help-block'>
                          @if($errors->has('weightage')) {{$errors->first('weightage')}} @endif
                        </span>
                    </div>

                <div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
                  <label for = 'remarks'  class = 'control-label'>Remarks</label>
                    
                  <textarea class="form-control" name = "remarks" placeholder="Enter ..." rows="2">{{$d->remarks}}</textarea>
                </div>

                <span>Is Active:  <input type = 'radio' name = 'is_active' value = 'yes' @if($d->is_active == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'is_active' value = 'no' @if($d->is_active == 'no') {{'checked'}} @endif>No</span>
                <br><br>
                
                <div class = 'form-row'>
                  <div subject='col-xs-offset-2 col-xs-10'>
                  {{Form::token()}}
                  </div>
                </div>
                <input type = "hidden" name = "id" value = "{{$d->id}}">
                <div class="form-group">
                  <button type = "submit" class = "btn btn-info" value = "edit">Edit</button>
                  <a href = "{{URL::route($module_name.'-list')}}" class = "btn btn-default">Cancel</a>

                </div>
            </form>
       </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="modal-footer">
                    
      </div>
     </div>
  </div>
</div>