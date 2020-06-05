@extends('books-assign.views.template.books-log')

@section('tab-content')
<h4>Select Books </h4>


<div class="row">
    <div class="col-md-12">
     <form action="{{ URL::route('generate-bar-code-post') }}" method="POST" enctype='multipart/form-data'>   
    <select class="form-control" name="book_id">
        
        <option value="all"> All</option>
        @foreach($books as $book)
        <option value="{{$book->id}}">{{ $book->title }}</option>
        @endforeach
    </select>
    <br>
    <input type="submit" value="Generate Bar Codes" class="btn btn-success">

    </form>
    </div>
</div>
@stop