<div class = "table-responsive">
<table  class="table table-bordered table-striped">
	<thead>
	  <tr>
	    <th>SN</th>
	    <th>Category Title</th>
	    <th>Frontend Publish</th>
	    <th>Viewing URL</th>
	    <th>Creation detail</th>
	    <th>Action</th>
	  </tr>
	</thead>

	@define $i = 1
	@foreach($categories as $category)
	<tbody>
	  <tr>
	    <td>{{ $i++ }} </td>
	    <td>{{ $category['title'] }}</td>
	    <td>{{ $category['frontend_publishable'] ? 'Yes' : 'No' }}</td>
	    @define $url = URL::route('articles-view-category-get',$category['id'])
	    <td><a href="{{$url}}">{{$url}}</a></td>
	    <td>{{ $category['created_at'] }}</td>
	    <td>
	    	<a class="btn btn-success btn-flat" href="{{URL::route('articles-edit-category-get',[$category['id'],$category['title']])}}" data-toggle="tooltip" title="Edit">
				<i class="fa fa-fw fa-edit"></i>
			</a>
			<a class="btn btn-danger btn-flat" href="{{URL::route('articles-delete-category-get',[$category['id'],$category['title']])}}" data-toggle="tooltip" title="Delete">
				<i class="fa fa-fw fa-trash"></i>
			</a>
		</td>
	  </tr>
	</tbody>
	@endforeach
</table>
</div>