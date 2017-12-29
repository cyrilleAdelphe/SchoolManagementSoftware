$(function()
{
	$('.treeview').each(function() {
		var links = $(this).find('a');
				
		links.each(function()
		{
			if ($(this).attr('href') == window.location.href.split('?')[0]) {
				$(this).parent('.treeview-item').addClass('active');
				$(this).parent('.treeview-item').parent('.treeview-menu').parent('.treeview').addClass('active');
				$(this).parent('.treeview').addClass('active');

				$(this).parent('.treeview-item').parent('.treeview-menu').parent('.treeview').parent('.treeview-menu').parent('.treeview').addClass('active');
				$(this).parent('.treeview-item').addClass('active');
			}	
		});
		
	});

});
