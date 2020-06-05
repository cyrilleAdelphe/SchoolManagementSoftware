
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

@define $book_details = array_chunk($book_details, 5)
@define $url = Config::get('app.url');	
				
<table cellpadding="10">
<thead>
	<tr>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</thead>
<tbody>
@foreach($book_details as $index => $book_detail)
	<tr>
	@foreach($book_detail as $d)
		<td>
		@define $book_path = 'app/modules/books/all-books/'. $d->title . '-'.$d->books_id.'/'.$d->book_id.'.png';
		<img src="{{ $url.$book_path }}"  onerror="this.src= '{{ asset('barcode-not-generated.PNG')}}';">
		
		<br>
		{{ $d->title }}
		{{ $d->book_id }}
		</td>
	@endforeach
	</tr>
				
@endforeach 	
</tbody>
</table>
</body>
</html>