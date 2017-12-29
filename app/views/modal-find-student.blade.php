<div id="find-id" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ID Finder</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="find_group" value="student" />
        <div class="row">
         <div class="col-sm-6">
            <div class="form-group ">
              <label>Class</label>
              <!-- <input type="text" name="find_class_id" value="1" > -->
              {{ HelperController::generateStaticSelectList(HelperController::getCurrentSessionClassList(), 'find_class_id') }}
            </div>
          </div>
          <div class="col-sm-6">
            <label>Section</label>
            <!-- <input type="text" name="find_section_id" value="2" > -->
            <select class="form-control" id = "find_section_id">
              <option>-- Select Class First --</option>
            </select>
          </div>          
        </div> <!-- row ends -->
        <div class = "row">
          <div class="col-sm-12">
            <button type = "button" class = "btn btn-info btn-flat" id = "find_search">
              <i class="fa fa-fw fa-search"></i> Search
            </button>
          </div>
        </div>
        <div id = "find_result">
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// for the find id modal
$('#find_search').click(function()
{
  var class_id = $('#find_class_id').val();
  var section_id = $('#find_section_id').val();
  var group = $('#find_group').val();
  $('#find_result').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
  $.ajax({
            "url" : "{{URL::route('ajax-get-dashboard-modal-search-list')}}",
            "data" : {'class_id' : class_id, 'section_id' : section_id, 'group' : group},
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