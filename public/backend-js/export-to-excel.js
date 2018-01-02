$(function()
{
  /* Billing-v1-changed-made-here */
  $(document).on('click', '#export-to-excel-button', function(e)
  /* Billing-v1-changed-made-here */
  {
    e.preventDefault();
    var file_name = $('#export-to-excel-file_name').text();
    //var url = $('#export-to-excel-form').attr('action');
    var main_data_row = [];
    var row = $('.export-to-excel-row');
    $(row).each(function()
    {
      var data_row = [];
      var column = $(this).find('.export-to-excel-data');
      $(column).each(function()
      {
        data_row.push($(this).text());
      });

      main_data_row.push(data_row);

    });

    main_data_row = JSON.stringify(main_data_row);

    //url += '?file_name=' + file_name + '&json=' + main_data_row;
    $('#export-to-excel-form').append("<input type = 'hidden' name = 'json' value = '"+ main_data_row +"'><input type = 'hidden' name = 'file_name' value = '"+file_name+"'>");
    //console.log($('#export-to-excel-form').html());
    $('#export-to-excel-form').submit();
    //window.location.href = url;
  });
});  