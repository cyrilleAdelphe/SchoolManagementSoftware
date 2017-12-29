<div id="edit-remarks{{$d->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Remarks</h4>
      </div>
      <form method="post" action="{{URL::route('report-edit-remarks-post')}}">
        <div class="box-body">
          <div class="row">  
            <div class="col-sm-9">
              <div class="form-group @if($errors->has("remarks")) {{"has-error"}} @endif">
                <label for="catname">Remarks</label>
                <input id="catname" class="form-control" type="text" name= "remarks" value="{{$d->remarks}}"/>
                <span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span>
              </div>
            </div>                     
          </div><!-- row ends -->
          <input type="hidden" name="id" id="id" value="{{ $d->id }}" />
          {{Form::token()}}
          <button class="btn btn-success" type="submit"  @if(!AccessController::checkPermission('report', 'can_edit')) disabled @endif>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
