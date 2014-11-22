jQuery(document).ready(function() {
	var reviewButton;

	//Search Activities
	$('#searchform').submit(function (e) {
		e.preventDefault();
		if ($("input[name=search]:checked").val() == "activityTag"){
			var searchString = new Object();
			searchString.tagName = $("#searchbar").val();
			searchString = 'api/index.php/getTaggedActivities?tagName=' + searchString.tagName;
			$('.activity').remove();
			$('.dateplan').remove();
			getActivitiesByTag(searchString);
		} else if ($("input[name=search]:checked").val() == "activitySearch"){
			$('.activity').remove();
			$('.dateplan').remove();
			getActivitiesByName();
		} else if ($("input[name=search]:checked").val() == "datePlanSearch"){
			$('.activity').remove();
			$('.dateplan').remove();
			searchDatabase();
		}
	});

	document.captureEvents(Event.CLICK);
	document.onclick = getMousePosition;

	$.cookie.json = true;
	getUserID();

	$('#favactivitiesbut').click(function(e){
		e.preventDefault();
		$('.activity').remove();
		$('.dateplan').remove();
		getFavoriteActivities();
	});

	$('#profilebut').click(function(e){
		e.preventDefault();
		$('.activity').remove();
		$('.dateplan').remove();
		$(location).attr('href', "profile.html");
	})

	$('#logoutbut').click(function(e){
		e.preventDefault();
		logout();
	})

	$('#reviewActivity').submit(function(e){
		e.preventDefault();
		reviewActivity();
	})

	$('#reviewDatePlan').submit(function(e){
		e.preventDefault();
		reviewDatePlan();
	})
	
	addSort();
});

function getActivitiesByTag(searchString){
	$.ajax({
		type: 'GET',
	    url: searchString,
	    success: function(data) {
	    	console.log(data);
	    	var actData = jQuery.parseJSON(data);
	    	console.log(actData);
	    	var activitiesDiv = $("#searchResults");
			$.ajax({
				type: 'POST',
				url: 'api/index.php/viewFavorites',
				content: 'application/json',
				data: JSON.stringify(user),
				success: function(data2){
					var favData = jQuery.parseJSON(data2);
					for (i = 0; i < actData.length; i++){
			    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\"></li>"
			    		var activityDiv = $(elem).appendTo(activitiesDiv);
			    		var starunstar = 'unstarred';
			    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
			    		for (j = 0; j < favData.length; j++){
			    			if (favData[j].Name == actData[i].Name){
			    				starunstar = 'starred';
			    				break;
			    			}
			    		}
			    		var starString = "<p class='" + starunstar +"'></p>";
			    		$(starString).appendTo(activityDiv);
			    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button> </a>").appendTo(activityDiv);
			    	}
			    	addDrag();
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
	favStar = document.elementFromPoint(e.clientX, e.clientY);
	if (favStar.classList.contains("starred"))
	{
		$.ajax({
	        type: 'DELETE',
	      	url: "api/index.php/deleteFavorite/0/" + favStar.parentNode.value,
			success: function(data){
				if($.isNumeric(data)){
	                if(data==1){
	                    console.log("Successfully unfavorited");
	                    favStar.setAttribute("class", "unstarred");
	                }
	                else{
	                	console.log(data);
	                    console.log("Unable to delete");
	                    }
	            }else
	            {
	            	console.log("Error in deleteing favorite");
	            }
			}
	    });
	}

	else if (favStar.classList.contains("unstarred"))
	{
		var tempUser = new Object();
		tempUser.UserID = user.UserID;
		tempUser.ActivityID = favStar.parentNode.value;
		if(typeof tempUser.UserID === 'undefined'){
			console.log('Not logged in');
		} else {
			$.ajax({
				type: 'POST',
				url: 'api/index.php/addFavorite',
				content: 'application/json',
				data: JSON.stringify(tempUser),
				success: function(data){
					favStar.setAttribute("class", "starred");
				},
				error: function(){
					console.log('For some reason, this activity was not added to favorites.');
					console.log(jqXHR, errorThrown);
				}
			});
		}
	}

	else if (favStar.classList.contains("reviewBut")){
		reviewBut = favStar;
		$("#ReviewActivityBox").show();
	}

	else if (favStar.classList.contains("reviewButton")){
		reviewButton = favStar;
		$("#ReviewDatePlanBox").show();
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
            sessionData = JSON.parse(data);
            console.log(data);
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
	    	var activitiesDiv = $("#searchResults");
	    	for (i = 0; i < actData.length; i++)
	    	{
	    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\"></li>"
	    		var activityDiv = $(elem).appendTo(activitiesDiv);
	    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
	    		activityDiv.append("<p class='starred'></p>");
	    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button> </a>").appendTo(activityDiv);
	    	}
	    	addDrag();
		},
		error: function(){
			console.log('Unable to get favorite activities');
		}
	});
}

function getActivitiesByName(){
	var searchQuery = new Object();
	searchQuery.SearchQuery = $("#searchbar").val();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchActivities',
		content: 'application/json',
		data: JSON.stringify(searchQuery),
		success: function(data) {
			console.log(data);
	    	var activitiesDiv = $("#searchResults");
	    	var actData = $.parseJSON(data).Activities;
	    	console.log(actData);
			$.ajax({
				type: 'POST',
				url: 'api/index.php/viewFavorites',
				content: 'application/json',
				data: JSON.stringify(user),
				success: function(data2){
					var favData = jQuery.parseJSON(data2);
					for (i = 0; i < actData.length; i++){
			    		var elem = "<li class='activity' value=" + actData[i].ActivityID + "></li>"
			    		var activityDiv = $(elem).appendTo(activitiesDiv);
			    		var starunstar = 'unstarred';
			    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
			    		for (j = 0; j < favData.length; j++){
			    			if (favData[j].Name == actData[i].Name){
			    				starunstar = 'starred';
			    				break;
			    			}
			    		}
			    		var starString = "<p class='" + starunstar +"'></p>";
			    		$(starString).appendTo(activityDiv);
			    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button> </a>").appendTo(activityDiv);
			    	}
			    	addDrag();
				}
			});
		}
	});
}

function logout(){
	$.ajax({
		type: "POST",
		url: 'api/index.php/logout',
		content: 'application/jsonajax',
		success: function(data){
			if(data){
				//redirect user to homepage after successful logout
				$(location).attr('href', "index.php");
			}
		},
		error: function(){
			console.log('Unable to logout');
		}
	});
}

function addSort(){
	$( "#currentDatePlan" ).sortable({
    	revert: true
    });
}

function addDrag(){
	console.log("Adding Drag");
	$( ".activity" ).draggable({
		connectToSortable: "#currentDatePlan",
		helper: "clone",
		revert: "invalid",
		stop: function(event, ui){
			var $elems = $('#currentDatePlan .activity[value=' + ui.helper.context.value + ']');
			$elems.switchClass("activity", "activityDatePlan");
			$elems.removeAttr("style");
		}
    });
}

function clearText(a){
	if (a.defaultValue == a.value){
		a.value = "";
	}
	else {
		if(a.value == ""){
			a.value = a.defaultValue;
		}
	}
};

function reviewActivity(){
	inputActivityID = reviewBut.parentNode.parentNode.value;
	inputUserID = user.UserID;
    inputRating = $("input[name=rating]:checked").val();
    inputDescription = $("#descriptionReview").attr("value");
    inputCostReview = ($("#costReview").attr("value"));
    inputattended = $("input[name=attended]:checked").val();
     console.log(inputattended);
    //create activity object 
    if(inputattended == 1){
    newReview = new Object();
    newReview.Rating = inputRating;
    newReview.ActivityID = inputActivityID
    newReview.UserID = inputUserID
    newReview.Description = inputDescription;
    newReview.Cost = inputCostReview;
    newReview.Attended = inputattended;
	console.log(newReview);
    //create new activity
    $.ajax({
        type: 'POST',
        url: 'api/index.php/reviewActivity',
        content: 'application/json',
        data: JSON.stringify(newReview),
        success: function(data){
            console.log(data);
            if(data == '1000'){
                alert('That activity does not exsist');
            }
            else{
            	console.log("Success");
            }
        }
    });
    $("#openModal div a.close button").click();
	}
	else{
		 $("#resultMessageActivityReview").text("You must attend an Activity to rate it!");
	}
}

function reviewDatePlan(){
	inputDatePlanID = reviewButton.parentNode.parentNode.value;
	inputUserID = user.UserID;
    inputRating = $("input[name=ratingDatePlan]:checked").val();
    inputDescription = $("#descriptionReviewDatePlan").attr("value");
    inputattended = $("input[name=attendedDatePlan]:checked").val();
     console.log(inputattended);
    //create activity object 
    if(inputattended == 1){
    newReview = new Object();
    newReview.Rating = inputRating;
    newReview.DatePlanID = inputDatePlanID
    newReview.UserID = inputUserID
    newReview.Description = inputDescription;
    newReview.Attended = inputattended;
	console.log(newReview);
    //create new activity
    $.ajax({
        type: 'POST',
        url: 'api/index.php/reviewDatePlan',
        content: 'application/json',
        data: JSON.stringify(newReview),
        success: function(data){
            console.log(data);
            if(data == '1000'){
                alert('That Date Plan does not exsist');
            }
            else{
            	console.log("Success");
            }
        }
    });
    $("#openModal div a.close button").click();
	}
	else{
		 $("#resultMessageActivityReview").text("You must attend a Date Plan to rate it!");
	}
}

function searchDatabase(){
	var searchQuery = new Object();
	searchQuery.SearchQuery = $("#searchbar").val();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchActivities',
		content: 'application/json',
		data: JSON.stringify(searchQuery),
		success: function(data){
	    	var datePlansDiv = $("#searchResults");
	    	var planData = $.parseJSON(data).DatePlans;
	    	console.log(planData);
	    	for (var k = 0; k < planData.length; k++){
	    		var searchedDatePlan = "<ul class='dateplan' value=" + planData[k].DatePlanID + " title=\"" + planData[k].Description + "\"></ul>";
	    		var datePlanDiv = $(searchedDatePlan).appendTo(datePlansDiv);
	    		$("<h2></h2>").text(planData[k].Name).appendTo(datePlanDiv);
	    		for (var l = 0; l < planData[k].AssociatedActivities.length; l++){
	    // 			$.ajax({
					// 	type: 'GET',
					//     url: 'api/index.php/getTaggedActivities?tagID=' + planData[k].AssociatedActivities[l].ActivityID,
					//     success: function(data2) {
					//     	console.log(data2);
					//     	var actData = jQuery.parseJSON(data);
				 //    		var elem = "<li class='activity' value=" + actData.ActivityID + " title=\"" + actData.Description + "\"></li>";
				 //    		var activityDiv = $(elem).appendTo(searchedDatePlan);
				 //    		$("<h3></h3>").text(actData.Name).appendTo(activityDiv);
				 //    		addDrag();
					// 	}, 
					// 	error: function(jqXHR, errorThrown){
					// 		console.log('We didnt make it sir, sorry');
					// 		console.log(jqXHR, errorThrown);
					// 	}
					// });
	    		}
	    		$("<a id='reviewDatePlanBoxAnchor' href='#ReviewDatePlanBox'> <button href='#ReviewDatePlanBox' name='Review' class='reviewButton' id='review" + planData[k].DatePlanID + "'>Review</button> </a>").appendTo(datePlanDiv);
	    	}
		}
	});
}