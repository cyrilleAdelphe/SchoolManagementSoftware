@extends('message.views.tabs')

@section('tab-content')
@if(count($data))
                    <table id="pageList" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>SN.</th>
                          <th>Category</th>
                          <th>Sent Date</th>
                          <th>Message</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      @define $i = 1
                      @foreach($data as $d)
                        <tr>
                          <td>{{$i++}}</td>
                          <td>{{substr($d->message, 0, strpos($d->message, '#'))}}</td>
                          <td>
                            {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $d->created_at)->format('l jS F Y h:i:s A')}}
                          </td>
                          <td>
                            {{substr($d->message, strpos($d->message, '#') + 1)}}
                          </td>
                          <td>
                          <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
                          <i class="fa fa-fw fa-trash"></i>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>

<div id="delete{{$d->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation</h4>
      </div>
      <div class="modal-body">
        <div class="row-fluid">
          <div class="col-md-9">
             Are you sure you want to delete?
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <form action="{{URL::route('delete-notification')}}" method="post">
              <input type="hidden" name="id" value="{{$d->id}}">
                            
              <button name="delete{{$d->id}}" value="delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger">
                <i class="fa fa-fw fa-trash"></i>
              </button>
              {{ Form::token() }}
          </form>
      </div>
     </div>
  </div>
</div>

                    <div class = "container">
  			<div class = 'paginate'>
  				@if($data)
  					{{$data->appends(Input::query())->links()}}
  				@endif
  			</div>
  		</div>
  	@else
  	       <div class="alert alert-warning alert-dismissable">
  						<h4>
  							<i class="icon fa fa-warning"></i>No Data Found
  						</h4>
  					</div>
  	@endif
  @stop