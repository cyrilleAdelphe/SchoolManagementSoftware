<div class="row">
    <div class="col-sm-12" style="max-height:250px; overflow:auto">
      <table id="pageList" class="table table-bordered table-striped">
        <thead>
          <tr>
            @if($checkbox == 'yes')
            <th><input type = "checkbox" id = "checkall"></th>
            @endif
            <th>SN</th>
            <th>Name</th>
            <th>ID</th>
            <th>Username</th>
            <th>Select</th>
            <th>
              {{-- <button type="button" class = "btn btn-default" data-dismiss="modal" onclick="findIdSelect('{{ $all_usernames }}', this)">
                <b>Select All</b>
              </button> --}}
            </th>
          </tr>
        </thead>
        <tbody>
        	@if(count($data))
  	      	@define $i = 1
  	      	
  	      	@foreach($data as $d)
  	      		<tr>
                @if($checkbox == 'yes')
                  <td><input type = "checkbox" class = "checkall"><input type = "hidden" name = "user_id[]" value = "{{$d->id}}"></td>
                @endif
  	      			<td>{{$i++}}</td>
  	      			<td>
                  {{$d->employee_name}}
  	      			</td>
  	      			<td>{{$d->id}}</td>
                <td>{{$d->username}}</td>
                <td><input type="checkbox" name="select_staff" id="select_staff" value="{{ $d->username}}"></td>
                <td>
                  <button type="button" class = "btn btn-default" data-dismiss="modal" onclick='findIdSelect("{{ $d->username }}", {{json_encode

($d)}} , this)'>
                    Select
                  </button>
                </td>
  	      		</tr>
  			    @endforeach
            
          @else
          	<tr>
          		<td>
          			<div class="alert alert-warning alert-dismissable">
                    <h4><i class="icon fa fa-warning"></i>No employee in the position</h4>
                </div>
          		</td>
          	</tr>
          @endif
        </tbody>
      </table>
    </div>
    <br><br>
  <p align="center"><button type="button" class = "btn btn-success" data-dismiss="modal" id="staff_checkbox" > Submit</button></p>

  </div><!-- row ends --> 