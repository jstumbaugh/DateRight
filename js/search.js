jQuery(document).ready(function() {
	//Search Activities
	$('#searchform').submit(function (e) {
		e.preventDefault();
		var searchString = new Object();
		searchString.tagName = $("#searchbar").val();
		searchString = searchString.tagName;
		getActivitiesByTag(searchString);
	});
});

function getActivitiesByTag(searchString){
	$.ajax({
		type: 'GET',
	    url: 'api/index.php/getTaggedActivities?tagID=1',
	    success: function(data) {
	    	console.log('hello');
		}, 
		error: function(jqXHR, errorThrown){
			console.log('We didnt make it sir, sorry');
			console.log(jqXHR, errorThrown);
		}
	});
}