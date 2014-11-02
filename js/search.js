jQuery(document).ready(function() {
	//Search Activities
	$('#searchform').submit(function (e) {
		e.preventDefault();
		var searchString = new Object();
		searchString.tagName = $("#searchbar").val();
		searchString = 'api/index.php/getTaggedActivities?tagName=' + searchString.tagName;
		getActivitiesByTag(searchString);
	});
});

function getActivitiesByTag(searchString){
	$.ajax({
		type: 'GET',
	    url: searchString,
	    success: function(data) {
	    	var actData = jQuery.parseJSON(data);
	    	for (i = 0; i < actData.length; i++)
	    	{
	    		
	    	}
	    	console.log(actData.length);
		}, 
		error: function(jqXHR, errorThrown){
			console.log('We didnt make it sir, sorry');
			console.log(jqXHR, errorThrown);
		}
	});
}