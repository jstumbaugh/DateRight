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

	$('#favactivitiesbut').click(function(e){
		e.preventDefault();
		$('.activity').remove();
		getFavoriteActivities();
	});
});

function getActivitiesByTag(searchString){
	$.ajax({
		type: 'GET',
	    url: searchString,
	    success: function(data) {
	    	var actData = jQuery.parseJSON(data);
	    	var activitiesDiv = $("#activities");
	    	console.log(user);
			$.ajax({
				type: 'POST',
				url: 'api/index.php/viewFavorites',
				content: 'application/json',
				data: JSON.stringify(user),
				success: function(data2){
					var favData = jQuery.parseJSON(data2);
					console.log(favData.length);
					for (i = 0; i < actData.length; i++){
			    		var elem = "<div class='activity' id=" + actData[i].ActivityID + "></div>"
			    		var activityDiv = $(elem).appendTo(activitiesDiv);
			    		var starunstar;
			    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
			    		for (j = 0; j < favData.length; j++){
			    			if (favData[j].Name == actData[i].Name){
			    				starunstar = 'starred';
			    				break;
			    			}
			    			else
			    				starunstar = 'unstarred';
			    		}
			    		var starString = "<p class='" + starunstar +"'></p>";
			    		console.log(starString);
			    		$(starString).appendTo(activityDiv);
			    	}
				}
			});
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

function getFavoriteActivities(){
	$.ajax({
		type: 'POST',
		url: 'api/index.php/viewFavorites',
		content: 'application/json',
		data: JSON.stringify(user),
		success: function(data){
			var actData = jQuery.parseJSON(data);
	    	var activitiesDiv = $("#activities");
	    	for (i = 0; i < actData.length; i++)
	    	{
	    		var elem = "<div class='activity' id=" + actData[i].ActivityID + "></div>"
	    		var activityDiv = $(elem).appendTo(activitiesDiv);
	    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
	    		activityDiv.append("<p class='starred'></p>");
	    	}
		},
		error: function(){
			console.log('Unable to get favorite activities');
		}
	});
}