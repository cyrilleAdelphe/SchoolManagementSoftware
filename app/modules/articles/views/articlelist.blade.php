<div class = "table-responsive">
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>SN</th>
      <th>Article Title</th>
      <th>Alise</th>
      <th>Meta Tag</th>
      <th>Meta Description</th>
      <th>Category</th>
      <th>Creation detail</th>
      <th>Featured</th>
      <th>Action</th>
    </tr>
  </thead>

  @define $i =1
  @foreach($articles as $article)
    <tbody>
      <tr>
        <td>{{ $i++ }} </td>
        <td>{{ $article['title'] }}</td>
        <td>{{ $article['alias'] }}</td>
        <td>{{ $article['meta_tag'] }}</td>
        <td>{{ $article['meta_description'] }}</td>
        <td>{{ $category_info[$article['category_id']]['title'] }}</td>
        <td>{{ $article['created_at'] }}</td>
        <td>{{ $article['is_featured'] }}</td>
        <td>
        	<a class="btn btn-success btn-flat" href="{{URL::route('articles-edit-get',[$article['id'],$article['alias']])}}" data-toggle="tooltip" title="Edit">
          <i class="fa fa-fw fa-edit"></i>
          </a>
          <a class="btn btn-danger btn-flat" href="{{URL::route('articles-delete-get',[$article['id'],$article['alias']])}}" data-toggle="tooltip" title="Delete">
          <i class="fa fa-fw fa-trash"></i>
          </a>
        </td>
      </tr>
    </tbody>
  @endforeach
</table>
</div>