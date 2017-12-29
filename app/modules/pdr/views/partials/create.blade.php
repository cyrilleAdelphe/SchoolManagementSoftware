	@define $status = false
		@if(strlen($message) == 0)
			<table  class = 'table table-striped table-hover table-bordered'>
				<thead>
					<tr>
						<th>Subject</th>
						<th>Chapter</th>
						<th>Class activity</th>
						<th>Learning achievement</th>
						<th>Homework</th>
						<th>Comment</th>
					</tr>
				</thead>
				<tbody>
				@foreach($data as $index => $d)
					@define $status = true
						<tr>
							<td>
								<label class = "control-label">{{$d->subject}}</label>
								<input type = "hidden" name = "subject_name[]" value = "{{$d->subject}}">
							</td>
							<td>
								<input type = "text" class = "form-control" name = "chapter[]">
							</td>
							<td>
								<input class = "form-control" name = "class_activity[]">
							</td>
							<td>
								<input class = "form-control" name = "learning_achievement[]">
							</td>
							<td>
								<input class = "form-control" name = "homework[]">
							</td>
							<td>
								<input class = "form-control" name = "comment[]">
							</td>
						</tr>
				@endforeach
				</tbody>
			</table>

			@if($status)
				<div class = 'form-row'>
					<div module='col-xs-offset-2 col-xs-10'>
					{{Form::token()}}
					<input type = 'hidden' name = 'is_active' value = 'yes'>
					<input type = "submit" value = "Create" class = "btn btn-success btn-flat btn-lg">
					</div>
				</div>
			@endif
		
			@if(!$status)
				<h1>Please Creat Daily Routine First</h1>
			@endif
		@else
			<h1>{{$message}}</h1>
		@endif

