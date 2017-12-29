@extends('books-assign.views.template.books-log')

@section('tab-content')
  <form method = "post" action = "{{URL::route($module_name.'-return-post')}}" id = "backendForm">
    {{-- <div class="form-group @if($errors->has('title1')) {{'has-error'}} @endif">
      <label for="title1">Book Title</label>
      <input id="title1" name="title1" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title1')?Input::old('title1'):''}}">
      <span class = 'help-block'>@if($errors->has('title1')) {{$errors->first('title1')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('title2')) {{'has-error'}} @endif">
      <input id="title2" name="title2" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title2')?Input::old('title2'):''}}">
      <span class = 'help-block'>@if($errors->has('title2')) {{$errors->first('title2')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('title3')) {{'has-error'}} @endif">
      <input id="title3" name="title3" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title3')?Input::old('title3'):''}}">
      <span class = 'help-block'>@if($errors->has('title3')) {{$errors->first('title3')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('title4')) {{'has-error'}} @endif">
      <input id="title4" name="title4" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title4')?Input::old('title4'):''}}">
      <span class = 'help-block'>@if($errors->has('title4')) {{$errors->first('title4')}} @endif</span>
    </div> --}}

    <div class="form-group @if($errors->has('book_ids')) {{'has-error'}} @endif">
      <label for="book_ids">Book ID</label>
      <input data-toggle="tooltip" name="book_ids" title="Enter book's id number" id="book_ids" class="form-control" type="text" placeholder="Enter book's id number" value="{{Input::old('book_ids')?Input::old('book_ids'):''}}">
      <small class='text-danger'>You can enter multiple books ID by seperating with , (comma) sign.</small>
      <span class = 'help-block'>@if($errors->has('book_ids')) {{$errors->first('book_ids')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('student_id')) {{'has-error'}} @endif">
      <label for="student_id">Student's Username</label>
      <input data-toggle="tooltip" name="student_id" title="Enter student's id number" id="studentUsername" class="form-control" type="text" placeholder="Enter student's id number" value="{{Input::old('student_id')?Input::old('student_id'):''}}" required>
      <span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
      <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
      @include('include.modal-find-student')
    </div>
    <div class="form-group @if($errors->has('returned_date')) {{'has-error'}} @endif">
      <label for="returned_date">Return date</label>
      <div class="input-group">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" name="returned_date" class="form-control" data-toggle="tooltip" title='Enter date in english format' placeholder='Enter date in english format' data-inputmask="'alias': 'dd/mm/yyyy'" value="{{Input::old('returned_date')?Input::old('returned_date'):(isset($date) ? $date : '')}}" data-mask required/>
        <span class = 'help-block'>@if($errors->has('returned_date')) {{$errors->first('returned_date')}} @endif</span>
      </div>
    </div>

    <div class="form-group @if($errors->has('remarks')) {{'has-error'}} @endif">
      <label for="content">Remark</label>
      <textarea name="remarks" class="remark" placeholder="Enter comments here." style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{Input::old('remarks')?Input::old('remarks'):''}}</textarea>
      <span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span>
    </div>

    <input type="hidden" name="is_active" value="yes"/>
    <button class="btn btn-success" type="submit">Save</button>
    <!-- <button class="btn btn-primary" type="submit">Save and New</button> -->
    {{Form::token()}}
  </form>
@stop

@section('custom-js')
  <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>

  <script type="text/javascript">
    // event trigger for select button after searching student
    function findIdSelect(username) {
      $('#studentUsername').val(username);
    }
  </script>

  @if(CALENDAR == 'BS')
  <script type="text/javascript">
    $(function () {

      $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
      $("[data-mask]").inputmask();
      $(".textarea").wysihtml5();

    });
  </script>
  @endif
@stop