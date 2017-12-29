@extends('users.views.tabs')



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
                <td>{{$d->username}}</td>
                <td>{{$d->name}}</td>
                <td>{{$d->role}}</td>
                <td>{{$d->is_active}}</td>
                <td>
                <a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info" type="button" data-original-title="Edit"><i class="fa fa-fw fa-edit"></i></button></a>
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