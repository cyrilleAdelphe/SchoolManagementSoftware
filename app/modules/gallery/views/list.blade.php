@extends('gallery.views.tabs')

@section('tab-content')

<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
	  <table class = 'table table-striped table-hover table-bordered scrollable'>
			<tbody class = 'search-table'>
					{{$tableHeaders}}
					{{$searchColumns}}
					@if($data['count'])
						@define $i=1
						@foreach($data['data'] as $d)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$d->title}}</td>
								<td>{{$d->description}}</td>
								<td>{{GalleryCategory::find($d->category_id)->title}}</td>
								<td>
									<img class="dynamicImage" width="200px" height="auto" src="{{Config::get('app.url').'/app/modules/gallery/assets/images/thumbnails/'.$d->id}}" alt="{{$d->title}}">
								</td>
								<td>
									<a href="{{URL::route($module_name.'-edit-get', $d->id)}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="EDIT" >
		                <i class="fa fa-fw fa-edit btn-flat"></i>
		              </a>

		              <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
		                <i class="fa fa-fw fa-trash"></i>
		              </a>
              		@include($module_name.'.views.delete-modal')
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td>{{$data['message']}}</td>
						</tr>
					@endif
				</tbody>
				{{Form::token()}}
			
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

<script src="{{Config::get('app.url').'/app/modules/gallery/assets/js/dynamicImages.js'}}"></script>

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>


@stop