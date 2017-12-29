@extends('books-assign.views.template.books-log')

@section('tab-content')

<div class="tab-pane " id="tab_2">
  
  {{-- $actionButtons --}}
  <div class='row'>
    <a class = 'btn btn-app' id = 'PraCreate' href = "{{URL::route($module_name.'-create-get')}}">
      <i class = 'fa fa-save'></i>Create
    </a>
  </div>

  <section class="row">
        
    {{-- <div class="col-md-2">
        <div class="form-group">
          <select class="form-control" id = "list_status">
            <option value = "yes" @if(isset($queryString['status']) && $queryString['status'] == 'yes') selected @endif>Live Data</option>
            <option value = "no" @if(isset($queryString['status']) && $queryString['status'] == 'no') selected @endif>Deleted Data</option>
          </select>
        </div>
    </div> --}}

    {{-- $paginateBar --}}

    {{-- <div class="col-md-2">
      <a  href = '{{URL::current()}}'><button class="btn btn-block btn-danger">Cancel Query</button></a>
    </div> --}}
  </section>
      
  <div class = "table-responsive">
    <table class = 'table table-striped table-hover table-bordered scrollable'>
      @if($data['count'])
      {{$tableHeaders}}
      {{-- <form id = "backendListForm" method = "post" action = "{{$queries}}"> --}}
        <tbody class = 'search-table'>
        
          <?php $i = 1; ?>
          {{$searchColumns}}

            @foreach($data['data'] as $d)
              <tr>
                {{-- <td><input type = 'checkbox' class = 'checkbox_id minimal' name = "rid[]" value = '{{$d->id}}'></td> --}}
                <td>{{$i++}}</td>
                <td>{{$d->book_title}} <br/> <span class="text-green">ID: {{$d->book_copy_id}}</span></td>
                <td>
                  <a href="#" data-toggle="modal" data-target="#student_{{$i}}" >
                    {{-- $d->username --}}
                    @if($d->related_group == 'student')
                    
                      @define $issued_person = StudentRegistration::where('id', $d->student_id)->first();

                      @if($issued_person)
                        {{ $issued_person->student_name }} {{ $issued_person->last_name }}
                        {{ Users::where('user_details_id', $d->student_id)->where('role', 'student')->pluck('username') }}
                      @endif
                    
                    @elseif($d->related_group == 'admin')
                    
                      @define $issued_person = Employee::where('id', $d->student_id)->first();

                      @if($issued_person)
                        {{ $issued_person->employee_name }}
                        {{ Admin::where('admin_details_id', $d->student_id)->pluck('username') }}
                      @endif
                    
                    @else
                    
                      @define $issued_person = Superadmin::where('id', $d->student_id)->first();

                      @if($issued_person)
                        {{ $issued_person->name }}
                        {{ $issued_person->username }}
                      @endif
                    
                    @endif
                  </a>
                  {{-- @include('books-assign.views.student-detail-modal') --}}
                </td>
                <td>
                  @if(CALENDAR == 'BS')
                    {{HelperController::formatNepaliDate((new DateConverter)->ad2bs($d->assigned_date))}}
                  @else
                    {{DateTime::createFromFormat('Y-m-d', $d->assigned_date)->format('d F Y')}}
                  @endif
                </td>
                
                <td>
                  @if($d->returned_date)
                    <span class='text-green'>Returned {{ $d->returned_date }}</span>
                  @else
                    <span class='text-danger'>Not Returned</span>
                  @endif
                </td>

                <td>{{BooksAssignHelper::getDueDays($d->id)}}</td>
                                    
                <td>
                  @if($d->returned_date)
                    <a href="#" data-toggle="modal" data-target="#remarks{{$d->id}}" title="Remark" class="btn btn-success btn-flat" type="button">
                     <i class="fa fa-fw fa-bars"></i>
                    </a>
                    <!-- modal for remark starts -->
                    <div id="remarks{{$d->id}}" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                          <div class="modal-content">
                            <div class="modal-header">
                              <h4>Remark</h4>
                            </div>
                            <div class="modal-body">                              
                              <p class="text-green" >{{$d->remarks}}</p>                                          
                            </div>
                            <div class="modal-footer">
                              <button data-dismiss="modal" class="btn btn-default" type="submit">Close</button>
                            </div>
                          </div>
                      </div>
                    </div>
                    <!-- modal for remark ends -->
                    <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
                      <i class="fa fa-fw fa-trash btn-flat"></i>
                    </a>
                    @include('include.modal-delete')
                  @else
                    <a href="{{URL::route($module_name . '-send-notification', $d->id)}}" title="Remind" data-toggle="tooltip" class="btn bg-purple btn-flat" type="button">
                      <i class="fa fa-fw fa-info"></i>
                    </a>
                    
                    <form method = "post" action = "{{ URL::route('books-assign-return-post', $d->id) }}">
                      <input type = "submit" class = "btn btn-success" value = "Return">
                      {{Form::token()}}
                    </form>
                    
                   
                  @endif
                </td>
              </tr>
            @endforeach
        
        </tbody>
        {{Form::token()}}
      {{-- </form> --}}
     @else
              <div class="alert alert-warning alert-dismissable">
      <h4><i class="icon fa fa-warning"></i>{{$data['message']}}</h4></div>
        @endif
    </table>
  </div>
</div> 

<div class = "container">
  <div class = 'paginate'>
    @if($data['count'])
      {{$data['data']->appends($queryString)->links()}}
    @endif
  </div>
</div>


@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>
<script>
  $('#2').attr('disabled','disabled');
</script>
@stop
