$(document).on("ready", function()
{
	$(".sidenav-icon").on("click", function(e)
	{
		var el = $(this);
		
		var target = $("#" + el.data("target"));
		
		if (target != null && !target.hasClass("collapsing"))
		{
			target.collapse("toggle");
			
			el.toggleClass("glyphicon-chevron-up glyphicon-chevron-down");
		}
		
		e.preventDefault();
		
		return false;
	});
});
