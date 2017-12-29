<div id="find-id" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ID Finder</h4>
      </div>
      <div class="modal-body">
        <div class="row">
         <div class="col-sm-4">
            <div class="form-group ">
              <label>Position</label>
              <!-- <input type="text" name="find_class_id" value="1" > -->
              {{ HelperController::generateSelectList('Group', 'group_name', 'id', 'group_id') }}
            </div>
          </div>
          
        </div> <!-- row ends -->
        <div id = "find_result">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// for the find id modal
$('#group_id').change(function()
{
  $('#find_result').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
  $.ajax({
            "url" : "{{URL::route('ajax-get-modal-employee-search-list')}}",
            "data" : {'group_id' : $(this).val()},
            "method" : "GET"
        } ).done(function(data) {
          $('#find_result').html(data);
  });
});

// for the find id modal
$('#find_class_id').change(function()
{

  var class_id = $(this).val();
  $('#find_section_id').html('<option value="0"> loading... </option>');
  $.ajax( {
                    "url": "{{URL::route('ajax-get-section-ids-from-class-id')}}",
                    "data": {"class_id" : class_id},
                    //"dataType" : "json",
                    "method": "GET",
                    } ).done(function(data) {
                      $('#find_section_id').html(data);
              });

});
</script>