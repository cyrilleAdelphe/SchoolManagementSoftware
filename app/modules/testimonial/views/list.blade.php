@extends('testimonial.views.tabs')



@section('tab-content')

   <table class = 'table table-striped table-hover table-bordered'>
      {{$tableHeaders}}
      <!--<form id = "backendListForm" method = "post" action = "{{$queries}}"> -->
        <tbody class = 'search-table'>
        @if($data['count'])
          <?php $i = 1; ?>
          {{$searchColumns}}

            @foreach($data['data'] as $d)
              <tr>
                <td><input type = 'checkbox' class = 'checkbox_id minimal' name = "rid[]" value = '{{$d->id}}'>
                {{$i++}}</td>
                <td>{{$d->content}}</td>
                <td>{{$d->show_in_module}}</td>
                <td>{{$d->sort_order}}</td>
                
                <td><a href = "{{URL::route($module_name.'-view', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success" type="button" data-original-title="View Detail"><i class="fa fa-fw fa-list-ul"></i></button></a>
                <a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info" type="button" data-original-title="Edit"><i class="fa fa-fw fa-edit"></i></button></a>
                <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
                <i class="fa fa-fw fa-trash"></i></a>
                @include('include.modal-delete')</td>
              </tr>
            @endforeach
        @else
              <tr>
                <td>{{$data['message']}}</td>
              </tr>
        @endif
        </tbody>
        {{Form::token()}}
      <!-- </form> -->
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