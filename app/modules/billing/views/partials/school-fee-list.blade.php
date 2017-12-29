
    <label class="text-red" style="font-size:16px">School fee list</label>
    
    <br/>
    <table id="pageList" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>SN</th>
          <th>Fee title</th>
          <th>Type</th>
          <th>Applied to</th>
          </tr>
      </thead>
      <tbody>
      	@if(count($fees))
      		@define $i = 1
      		@foreach($fees as $fee)
	        <tr>
	          <td>{{$i++}}</td>
	          <td>{{$fee->fee_category}}</td>
	          <td>{{$fee->fee_type}}</td>
	          <td>
	            
              <button title="Edit" data-toggle="modal" data-target="#edit" class="btn btn-success btn-flat btn-sm module-edit-fee" type="button">
                <i class="fa fa-fw fa-edit"></i>
              </button>

	            <a href="#" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat btn-sm" type="button">
	              <i class="fa fa-fw fa-trash"></i>
	            </a>
              <input type = "hidden" class = "fee_id" value = "{{$fee->id}}">
              <input type = "hidden" class = "module-edit-fee-url" value = "{{URL::route('billing-ajax-get-edit-view', $fee->id)}}">
	          </td>
	        </tr>
	        @endforeach
        @else
        <tr>
        	<td>No Fees Created</td>
        </tr>
        @endif
      </tbody>
    </table>

    <div id="edit" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit</h4>
          </div>
          
          <div class="modal-body" id = "edit-body">                          


          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>

    </div>

    <script>
    $(function()
    {

      $('.module-edit-fee').click(function(e)
      {
        var currentElement = $(this);
        $('#edit-body').html('loading.....');
        $.ajax
        ({
          url: currentElement.parent().find('.module-edit-fee-url').val(),
          method: 'GET'
        }).done(function(data)
        {
          $('#edit-body').html(data);
        });

      });

    });
    </script>