<table id="pageList" class="table table-bordered table-striped">
	<thead>
		<tr>
      <th>SN</th>
    	<th>Book</th>
    	<th>Book ID</th>
    	<th>Assigned</th>
    	<th>Returned</th>
    	<th>Due</th>
    	<th>Status</th>
    	<th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i = 1
  	@foreach($books as $d)
  		<tr>
  			<td>{{ $i++ }}</td>
  			<td>{{ $d->book_title }}</td>
  			<td>{{ $d->book_copy_id }}
  			<td>
		      @if(CALENDAR == 'BS')
		        {{HelperController::formatNepaliDate((new DateConverter)->ad2bs($d->assigned_date))}}
		      @else
		        {{DateTime::createFromFormat('Y-m-d', $d->assigned_date)->format('d F Y')}}
		      @endif
		    </td>
				<td>
		      @if(HelperController::validateDate(substr($d->returned_date, 0,10), 'Y-m-d'))
		        @if(CALENDAR == 'BS')
		          {{HelperController::formatNepaliDate((new DateConverter)->ad2bs($d->returned_date))}}
		        @else
		          {{DateTime::createFromFormat('Y-m-d', $d->returned_date)->format('d F Y')}}
		        @endif
		      @else
		        N/A
		      @endif
		    </td>
				<td>{{BooksAssignHelper::getDueDays($d->id)}}</td>
				<td>
					@if($d->returned_date)
						<span class='text-green'>Returned</span>
					@else
						<span class='text-danger'>Not Returned</span>
					@endif
				</td>
				
				<td>
					@if($d->returned_date)
						<a href="#" data-toggle="modal" data-target="#remarks{{$d->id}}" title="Remark" class="btn btn-success" type="button">
		          Remark
		        </a>
		        <!-- modal for remark starts -->
		        <div id="remarks{{$d->id}}" class="modal fade" role="dialog">
		          <div class="modal-dialog">
		            <!-- Modal content-->
		              <div class="modal-content">
		                <div class="modal-body">
		                  <p >{{$d->remarks}}</p>                                          
		                </div>
		                <div class="modal-footer">
		                  <button data-dismiss="modal" class="btn btn-default" type="submit">Close</button>
		                </div>
		              </div>
		          </div>
		        </div>
		        <!-- modal for remark ends -->
		      @else
          	<a href="{{URL::route('books-assign-send-notification', $d->id)}}" title="Remind" class="btn btn-info" type="button" @if(!AccessController::checkPermission('books-assign', 'can_edit')) disabled @endif>
              Remind
            </a>
		      @endif
		    </td>
  		</tr>
  	@endforeach
  </tbody>
</td>