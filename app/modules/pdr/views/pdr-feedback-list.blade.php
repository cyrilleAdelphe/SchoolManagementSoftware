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
                <td>{{$d->student_name}}</td>
                <td>{{$d->guardian_name}}</td>
                <td>{{$d->feedback}}</td>
                
                <td>
                  <a href = "{{URL::route('pdr-view-feedback', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail"  @if(!AccessController::checkPermission('pdr', 'can_view')) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

                </td>
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

