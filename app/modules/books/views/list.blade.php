@extends('books-assign.views.template.books-log')

@section('tab-content')

<div class="tab-pane " id="tab_2">
	
	{{-- $actionButtons --}}
	<div class='row'>
		<a class = 'btn btn-app' id = 'PraCreate' href = "{{URL::route($module_name.'-create-get')}}">
			<i class = 'fa fa-save'></i>Create
		</a>
	</div>

	<div class="col-md-6">
		<a href ="{{ URL::route('download-book-barcodes',0)}}" type="button" class="btn btn-success">Download All Barcodes</a>
			
		
		</div>
		</div>

		<div class="col-md-6">
		<form action="{{ URL::route('upload-books-from-excel')}}" enctype="multipart/form-data" method="POST">
			<label>Upload Books Excel</label>
			<input type="file" name="excel_file" class="form-control">
			<input type="submit" value="Upload Books" class="btn btn-success">
			<div style="color:red;">{{ $errors->first('excel_file')}}</div>
		</form>
		</div>
		</div>
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
							@define $assigned_books = count(BooksAssigned::where('books_id', $d->id)->whereNull('returned_date')->get())
							<tr>
								{{-- <td><input type = 'checkbox' class = 'checkbox_id minimal' name = "rid[]" value = '{{$d->id}}'></td> --}}
								<td>{{$i++}}</td>
								<td>{{$d->title}}</td>

								<td>{{$d->author}}</td>
								<td>{{$d->no_of_copies - $assigned_books}}</td>
								<td>{{$assigned_books}}</td>
								<td>{{ DB::table('book_categories')->where('id', $d->category_id)->pluck('rack_number')}}</td>
								<td> {{ DB::table('book_categories')->where('id', $d->category_id)->pluck('title')}} </td>

								<td>{{$d->max_holding_days}} Days</td>
								
								<td>

									<a href = "{{ URL::route('download-book-barcodes', $d->id) }}"><button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="Download BarCode"><i class="fa fa-download" aria-hidden="true"></i>
									</button></a>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail"><i class="fa fa-fw fa-eye"></i></button></a>
									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit"><i class="fa fa-fw fa-edit"></i></button></a>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button"> <i class="fa fa-fw fa-trash"></i> </a>
						  		@include('include.modal-delete')
								</td>
							</tr>
						@endforeach
				
				</tbody>
				{{Form::token()}}
			{{-- </form> --}}
		@else
							<div class="alert alert-warning alert-dismissable">
      <h4><i class="icon fa fa-warning"></i>{{$data['message']}}</h4>
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

@stop
