$(document).ready(function()
{
	$(".sidenav-toggle").click(function(e)
	{
		var parent = $(this).parent();
		
		parent.children(".opened").toggle();
		
		parent.children(".closed").toggle();
		
		parent.parent().children("ul").toggle();
		
		parent.parent().toggleClass("active-parent");
		
		e.preventDefault();
		
		return false;
	});
});
