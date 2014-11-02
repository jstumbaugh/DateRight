jQuery(document).ready(function() {
	//Search Activities
	$('#searchform').submit(function (e) {
		e.preventDefault();
		var searchString = new Object();
		searchString.tagName = $("#searchbar").val();
		searchString = 'api/index.php/getTaggedActivities?tagName=' + searchString.tagName;
		$('.activity').remove();
		getActivitiesByTag(searchString);
	});

	document.captureEvents(Event.CLICK);
	document.onclick = getMousePosition;

	$.cookie.json = true;
	getUserID();
});

function getActivitiesByTag(searchString){
	$.ajax({
		type: 'GET',
	    url: searchString,
	    success: function(data) {
	    	var actData = jQuery.parseJSON(data);
	    	var activitiesDiv = $("#activities");
	    	for (i = 0; i < actData.length; i++)
	    	{
	    		var elem = "<div class='activity' id=" + actData[i].ActivityID + "></div>"
	    		var activityDiv = $(elem).appendTo(activitiesDiv);
	    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
	    		activityDiv.append("<p class='unstarred'></p>");
	    	}
		}, 
		error: function(jqXHR, errorThrown){
			console.log('We didnt make it sir, sorry');
			console.log(jqXHR, errorThrown);
		}
	});
}

function getMousePosition(e){
	favStar = document.elementFromPoint(event.clientX, event.clientY);
	console.log(favStar);
	if (favStar.classList.contains("starred"))
	{
		console.log('Currently cannor unfavorite things, sorry.');
	}
	else if (favStar.classList.contains("unstarred"))
	{
		var tempUser = new Object();
		tempUser.UserID = user.UserID;
		tempUser.ActivityID = favStar.parentNode.id;
		$.ajax({
			type: 'POST',
			url: 'api/index.php/addFavorite',
			content: 'application/json',
			data: JSON.stringify(tempUser),
			success: function(data){
				console.log(data);
				favStar.setAttribute("class", "starred");
			},
			error: function(){
				console.log('For some reason, this activity was not added to favorites.');
				console.log(jqXHR, errorThrown);
			}
		});
	}
}

function getUserID(){
	user = new Object();
	sessionData = {};
    $.ajax({
        type: 'POST',
        async: false,
        url: 'api/index.php/getSessionInfo',
        success: function(data){
            //console.log(data);
            sessionData = JSON.parse(data);
        }
    });
    user.UserID = sessionData.UserID;
}