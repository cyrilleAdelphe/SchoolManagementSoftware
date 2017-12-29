@extends('pdr.views.tabs')

@section('tab-content')
  <div class="table-responsive">
    <table class = 'table table-striped table-hover table-bordered'>
      {{$tableHeaders}}
     
        <tbody class = 'search-table'>
        @if($data['count'])
          <?php $i = 1; ?>
          {{$searchColumns}}

            @foreach($data['data'] as $d)
              <tr>
                <td>{{$i++}}</td>
                <td>{{$d->session_name}}</td>
                <td>{{$d->class_name}}</td>
                <td>{{$d->section_code}}</td>
                <td>{{$d->pdr_date}}</td>
                <td>
                  <a href = "{{URL::route($module_name.'-view', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail"  @if(!AccessController::checkPermission('pdr', 'can_view')) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

                  <a href = "{{URL::route('pdr-feedback-list', $d->id)}}" data-toggle="tooltip" title="" class="btn bg-purple btn-flat" type="button" data-original-title="View Feedback"  @if(!AccessController::checkPermission('pdr', 'can_view')) disabled @endif><i class="fa fa-fw fa-commenting-o"></i></a>

                  <a href = "{{URL::route($module_name.'-edit-get', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit"  @if(!AccessController::checkPermission('pdr', 'can_edit')) disabled @endif><i class="fa fa-fw fa-edit"></i></a>
                  <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('student', 'can_delete')) disabled @endif>
                    <i class="fa fa-fw fa-trash"></i>
                  </a>
                  @include('include.modal-delete')</td>
              </tr>
            @endforeach
        @else
              <tr>
                <td>{{$data['message']}}</td>
              </tr>
        @endif
        </tbody>
       
    </table>

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


@stop

