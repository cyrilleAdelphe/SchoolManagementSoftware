@section('custom-css')
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop
<div id="edit{{$d->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit</h4>
      </div>
      <div class="modal-body">
             <form method = "post" action = "{{URL::route($module_name.'-edit-post')}}">
                
                <table class = "table table-striped table-hover table-bordered">
                  <tbody>
                  <tr>
                    <th>Subject Name</th>
                    <td><div class = 'form-group @if($errors->has("subject_name")) {{"has-error"}} @endif'><input type = 'text' name = 'subject_name' value = '{{$d->subject_name}}' class = "form-control required"><span class = 'help-block'>@if($errors->has('subject_name')) {{$errors->first('subject_name')}} @endif</span></div></td>
                  </tr>

                  <tr>
                    <th>Subject Code</th>
                    <td><div class = 'form-group @if($errors->has("subject_code")) {{"has-error"}} @endif'><input type = 'text' name = 'subject_code' value = '{{$d->subject_code}}' class = "form-control required"><span class = 'help-block'>@if($errors->has('subject_code')) {{$errors->first('subject_code')}} @endif</span></div></td>
                  </tr>

                  <tr>
                    <th>Full Marks</th>
                    <td><div class = 'form-group @if($errors->has("full_marks")) {{"has-error"}} @endif'><input type = 'text' name = 'full_marks' value = '{{$d->full_marks}}' class = "form-control required"><span class = 'help-block'>@if($errors->has('full_marks')) {{$errors->first('full_marks')}} @endif</span></div></td>
                  </tr>

                  <tr>
                    <th>Pass Marks</th>
                    <td><div class = 'form-group @if($errors->has("pass_marks")) {{"has-error"}} @endif'><input type = 'text' name = 'pass_marks' value = '{{$d->pass_marks}}' class = "form-control required"><span class = 'help-block'>@if($errors->has('pass_marks')) {{$errors->first('pass_marks')}} @endif</span></div></td>
                  </tr>

                  <tr>
                    <th>Remarks</th>
                    <td><div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'><input type = 'text' name = 'remarks' value = '{{$d->remarks}}' class = "form-control required"><span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span></div></td>
                  </tr>

                  <tr>
                    <th>Sort Order</th>
                    <td><div class = 'form-group @if($errors->has("sort_order")) {{"has-error"}} @endif'><input type = 'text' name = 'sort_order' value = '{{$d->sort_order}}' class = "required"><span class = 'help-block'>@if($errors->has('sort_order')) {{$errors->first('sort_order')}} @endif</span></div></td>
                  </tr>
                  
                  <tr>
                    <th>Is Active &nbsp;&nbsp;&nbsp;</th>
                    <td><span><input type = 'radio' name = 'is_active' value = 'yes' @if($d->is_active == 'yes') {{'checked'}} @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;</span><span><input type = 'radio' name = 'is_active' value = 'no' @if($d->is_active == 'no') {{'checked'}} @endif>&nbsp;&nbsp;No</span>
                  </tr>                  

                  <tr>
                    <th>Is Graded &nbsp;&nbsp;&nbsp;</th>
                    <td><span><input type = 'radio' name = 'is_graded' value = 'yes' @if($d->is_graded == 'yes') {{'checked'}} @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;</span><span><input type = 'radio' name = 'is_graded' value = 'no' @if($d->is_graded == 'no') {{'checked'}} @endif>&nbsp;&nbsp;No</span>
                  </tr>
                  <tr>
                    <th>Include In Report Card</th>
                    <td><span><input type = 'radio' name = 'include_in_report_card' value = 'yes' @if($d->include_in_report_card == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'include_in_report_card' value = 'no' @if($d->include_in_report_card == 'no') {{'checked'}} @endif>No</span>
                  </tr>  
                </table>
                {{Form::token()}}<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
                <input type = "hidden" name = "class_id" value = "{{$d->class_id}}">
                <input type = "hidden" name = "section_id" value = "{{$d->section_id}}">
                <input type = "hidden" name = "id" value = "{{$d->id}}">
                </form>
      </div>
      <div class="modal-footer">
                    
      </div>
     </div>
  </div>
</div>
@section('custom-js')
<script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>

@stop