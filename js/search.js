jQuery(document).ready(function() {
	var reviewButton;
	getUserID();
	currentPlanName = null;
	check = 0;
	//performSearchFromHompage();
	datePlanActivity = new Object();
	$('#backToCreateBut').hide();

	//Checks if user is  logged in. If not  hide the buttons and date plan items
	if(user.UserID == undefined ){
		$("#currentplan").hide();
		$("#myPlansBut").hide();
		$("#profilebut").hide();
		$("#logoutbut").hide();
		$("#favDatePlansBut").hide();
		$("#favactivitiesbut").hide();
		$("#backtoHomePage").show();
	}
	else{
		$("#backtoHomePage").hide();
	}

//Go back to index page
	$("#homeButton").click(function(e){
        e.preventDefault();
        $(location).attr('href', "index.php");
    });

	performSearchFromHompage();

		
	//Search Activities
	$('#searchform').submit(function (e) {
		var searchString = newSearch();
		var searchType = $("input[name=search]:checked").val();
		var searchArea = $('#searchResults');
		e.preventDefault();

		if (searchType == "activityTag"){
			searchArea.css("-webkit-column-width", "");
			displayActivitiesByTag(searchString);
		} else if (searchType == "activitySearch"){
			searchArea.css("-webkit-column-width", "");
			displayActivitiesByName(searchString);
		} else if (searchType == "datePlanSearch"){
			searchArea.css("-webkit-column-width", "315px");
			searchDatabase(searchString);
		} else if (searchType == "datePlanTag"){
			searchArea.css("-webkit-column-width", "315px");
			hideActsAndPlans();
			searchDatePlanTags();
		}
	});

	document.captureEvents(Event.CLICK);
	document.onclick = getMousePosition;

	$.cookie.json = true;
	getUserID();

	$('#favactivitiesbut').click(function(e){
		e.preventDefault();
		var searchArea = $('#searchResults');
		searchArea.css("-webkit-column-width", "");
		$('.activity').remove();
		$('.dateplan').remove();
		displayFavoriteActivities();
	});

	$('#favDatePlansBut').click(function(e){
		e.preventDefault();
		var searchArea = $('#searchResults');
		searchArea.css("-webkit-column-width", "315px");
		$('.activity').remove();
		$('.dateplan').remove();
		displayFavoriteDatePlans();
	})

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

	$('#descriptionBut').click(function(e){
		$('#DescriptionBox').show();
	})

	$('#myPlansBut').click(function(){
		$('#publishCheckbox').hide();
		getUserDatePlans();
	})

	$('#backToCreateBut').click(function(){
		$('#descriptionBut').show();
		$('#myPlansBut').show();
		$('#datePlanName').show();
		$('#publishCheckbox').show();
		$('#publishLabel').show();
		$('#datePlanName').val("");
		$('#backToCreateBut').hide();
		$('.userDatePlan').parent().empty();
	})

	$('#dpNameSaveBut').click(function(){
		updateName();
	})

	$('#reviewActivity').submit(function(e){
		e.preventDefault();
		reviewActivity();
	})

	$('#reviewDatePlan').submit(function(e){
		e.preventDefault();
		reviewDatePlan();
	})

	$('#addDescription').submit(function(e){
		e.preventDefault();
		editDescription();
	})

	$('#addTagToActivity').submit(function(e){
		e.preventDefault();
		var tag = new Object();
		tag.tagName = $('#tagForActivity').val();
		tag.activityID = activityFromClick;
		addTag(tag);
	})

	$('#publishCheckbox').change(function(){
		if (this.checked){
			check = 1;
		}
		publishDatePlan(check);
		$('#publishCheckbox').hide();
		$('#publishLabel').hide();
	})

    $(document).tooltip({
		position: { 
			my: "left+15 center", 
			at: "right center",
			using: function(position, feedback){
				$(this).css(position);
				$("<div>").addClass("arrow").addClass("left").appendTo(this);
				$('.ui-helper-hidden-accessible').remove();
			}
		}
    })

	addSort();
});

//Gets the users favorite dateplans and displays them on the screen
function displayFavoriteDatePlans(){
	var favData = getFavoriteActivities();
	var tags = new Object();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/viewFavorites',
		content: 'application/json',
		data: JSON.stringify(user),
		success: function(data){
			console.log(data);
			var planData = jQuery.parseJSON(data);
	    	var datePlansDiv = $("#searchResults");
	    	var count = -1;

	    	if (!jQuery.isEmptyObject(planData)){

		    	for (var k = 0; k < planData.length; k++) {

	    			if(planData[k].DatePlanID != null) {
	    				count++;
			    		var searchedDatePlan = "<ul class='dateplan' value=" + planData[k].DatePlanID + " title=\"" + planData[k].Description + "\"></ul>";
			    		$(searchedDatePlan).appendTo(datePlansDiv);
			    		var datePlanDiv = $('.dateplan')[count];

			    		var starElement = $("<p class='starred' id=" + planData[k].DatePlanID + "></p>");
			    		starElement.appendTo(datePlanDiv);
			    		
			    		var dateTitle = $("<h2></h2>").text(planData[k].Name)//.appendTo(datePlanDiv);
			    		dateTitle.appendTo(datePlanDiv);

			    		for (var l = 0; l < planData[k].AssociatedActivities.length; l++){
			    			$.ajax({
								type: 'GET',
							    url: 'api/index.php/getActivityById/' + planData[k].AssociatedActivities[l].ActivityID,
							    async: false,
							    success: function(data2) {

							    	if(!jQuery.isEmptyObject(data2)){

								    	var actData = jQuery.parseJSON(data2);
									    for (i = 0; i < actData.length; i++){

											$.ajax({
												type: 'GET',
												url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
												async: false,
												content: 'application/json',
												success: function(data3){
													tags = jQuery.parseJSON(data3);
												}
											});

								    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description;
								    		var hTags = "<h5 class='tags'> ";
								    		if (!(tags === null)){
									    		for (var x = 0; x < tags.length; x++){
									    			hTags = hTags + tags[x].TagName + "&nbsp&nbsp&nbsp";
									    		}
									    		hTags = hTags + "</h5>";
								    		}
								    		elem = elem + "\"></li>";
								    		var activityDiv = $(elem).appendTo(datePlanDiv);
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

								    		if (actData[i].Rating != null)
												ratingDisplayed = (Math.round(actData[i].Rating * 10 ) / 10).toFixed(1);
											else
												ratingDisplayed = "Not Rated";

								    		$("<br>" + "<p class='ratingDisplay'>Rating: " + ratingDisplayed + "</p>" + hTags + " <a href='#addTagBox'><button name='addTag' class='addTagC' title='Add a Tag!'>+</button></a>").appendTo(activityDiv);
								    	}		
								    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
										    addDrag();
									}
								}, 
								error: function(jqXHR, errorThrown){
									console.log('We didnt make it sir, sorry');
									console.log(jqXHR, errorThrown);
								}						
							});
			    		}
			    		if(user.UserID != null){
			    			if (planData[k].Rating != null)
								ratingDisplayed = (Math.round(planData[k].Rating * 10 ) / 10).toFixed(1);
							else
								ratingDisplayed = "Not Rated";
			    			$("<a id='DescriptionBoxAnchor' href='#ReviewDatePlanBox'> <button href='#ReviewDatePlanBox' name='Review' class='reviewButton' id='review" + planData[k].DatePlanID + "'>Review</button> </a>" + "<p class='ratingDisplay dateRating'>Rating: " + ratingDisplayed + "</p>").appendTo(datePlanDiv);
			    		}
	    			}
	    		}	
	    	}    	
		}
	});
}

//Grabs search query from home page search bard and performs dateplan and activities search
function performSearchFromHompage(){
	var queryString = new Array();
	//Logic below credits go to aspdotnetcodebook-blogspot-com from StackOverflow
	if (queryString.length == 0) {
            if (window.location.search.split('?').length > 1) {
                var params = window.location.search.split('?')[1].split('&');
                for (var i = 0; i < params.length; i++) {
                    var key = params[i].split('=')[0];
                    var value = decodeURIComponent(params[i].split('=')[1]);
                    queryString[key] = value;
                }
            }
        }
        if (queryString["datesearch"] != null) {
            var data = "";
            data += queryString["datesearch"];
            searchDatabase(data);
            displayActivitiesByName(data);
        }else if(queryString["selectedDateplan"] != null){
        	var data = "";
            data += queryString["selectedDateplan"];
           	getFullDatePlanByID(data);
        }
        else if(queryString["selectedActivity"] != null){
        	var data = "";
            data += queryString["selectedActivity"];
           	getActivityById(data);
        }
}
//Used when someone clicks the random date idea form homepage
function getFullDatePlanByID(data){
	$.ajax({
		type: 'GET',
		url: 'api/index.php/getFullDatePlanByID/'+data,
		content: 'application/json',
		success: function(data){
	    	var datePlansDiv = $("#searchResults");
	    	var planData = $.parseJSON(data).DatePlans;
	    	for (var k = 0; k < planData.length; k++){
	    		var searchedDatePlan = "<ul class='dateplan' value=" + planData[k].DatePlanID + " title=\"" + planData[k].Description + "\"></ul>";
	    		$(searchedDatePlan).appendTo(datePlansDiv);
	    		var datePlanDiv = $('.dateplan')[k];
	    		$("<h2></h2>").text(planData[k].Name).appendTo(datePlanDiv);
	    		for (var l = 0; l < planData[k].AssociatedActivities.length; l++){

	    			$.ajax({
						type: 'GET',
					    url: 'api/index.php/getActivityById/' + planData[k].AssociatedActivities[l].ActivityID,
					    async: false,
					    success: function(data2) {
					    	var actData = jQuery.parseJSON(data2);
						    favData = getFavoriteActivities()

						    for (i = 0; i < actData.length; i++){

								$.ajax({
									type: 'GET',
									url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
									async: false,
									content: 'application/json',
									success: function(data3){
										if (data3 != 500)
											tags = jQuery.parseJSON(data3);
										else
											tags = null;
									}
								});

					    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description;
					    		var hTags = "<h5 class='tags'> ";
					    		if (!(tags === null)){
						    		for (var x = 0; x < tags.length; x++){
						    			hTags = hTags + tags[x].TagName + "&nbsp&nbsp&nbsp";
						    		}
						    		hTags = hTags + "</h5>";
					    		}
					    		elem = elem + "\"></li>";
					    		var activityDiv = $(elem).appendTo(datePlanDiv);
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
					    		$("<br>" + hTags + " <button name='addTag' class='addTagC' title='Add a Tag!'>+</button>").appendTo(activityDiv);
					    	}		
					    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
							    addDrag();
						}, 
						error: function(jqXHR, errorThrown){
							console.log('We didnt make it sir, sorry');
							console.log(jqXHR, errorThrown);
						}						
					});
	    		}
	    		$("<a id='DescriptionBoxAnchor' href='#addDescription'> <button href='#addDescription' name='Review' class='reviewButton' id='review" + planData[k].DatePlanID + "'>Review</button> </a>").appendTo(datePlanDiv);
	    	}	    	
		}
	});
}

//Searches for activites by their name
function getActivityById(data){
	$.ajax({
			type: 'GET',
			url: 'api/index.php/getActivityById/' + data,
			async: false,
			success: function(data2){
				var actData = jQuery.parseJSON(data2);

				var favoriteActivities = getFavoriteActivities();
				var activitiesDiv = $("#searchResults");
				var starunstar = 'unstarred';
				var starString = "<p class='" + starunstar +"'></p>";

				for (i = 0; i < actData.length; i++){
					var tags = getTagsByActID(actData[i].ActivityID);
					var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\"></li>";

					var hTags = "<h5 class='tags'> ";
					for (var x = 0; x < tags.length; x++){
						hTags += tags[x].TagName + "&nbsp&nbsp&nbsp";
					}
					hTags += "</h5>";

					activityDiv = $(elem).appendTo(activitiesDiv);
					$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
					for (j = 0; j < favoriteActivities.length; j++){
						if (favoriteActivities[j].Name == actData[i].Name){
							starunstar = 'starred';
							break;
						}
					}
					
					$(starString).appendTo(activityDiv);
					$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button></a> " + hTags + " <button name='addTag' class='addTagC' title='Add a Tag!'>+</button>").appendTo(activityDiv);
				}
				if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
					addDrag();
						}
			});
}

//Get the mouse position. Used to access the review button and favorite star in the date plans and activities 
function getMousePosition(e){
	favStar = document.elementFromPoint(e.clientX, e.clientY);
	if (favStar.classList.contains("starred"))
	{
		var type = 0;
		var id = favStar.parentNode.value;
		if (favStar.parentNode.classList.contains("dateplan")){
			type = 1;
			id = favStar.id;
		}
		$.ajax({
	        type: 'DELETE',
	        async: 'false',
	      	url: "api/index.php/deleteFavorite/" + type + "/" + id,
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
		if (favStar.parentNode.classList.contains("activity"))
			tempUser.ActivityID = favStar.parentNode.value;
		else if (favStar.parentNode.classList.contains("dateplan"))
			tempUser.DatePlanID = favStar.id;
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
					console.log(data);
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
		//clear the form after they close
		$('#reviewActivity').each (function(){
  			this.reset();
		});
	}

	else if (favStar.classList.contains("reviewButton")){
		reviewButton = favStar;
		$("#ReviewDatePlanBox").show();
		//clear the form after they close
		$('#reviewDatePlan').each (function(){
  			this.reset();
	});
	}

	else if (favStar.classList.contains("userDatePlanX")){
		exitBut = favStar;
		deleteUserPlan();
	}

	else if (favStar.classList.contains("userDatePlan")){
		userPlan = favStar;
		openDatePlan();
	}

	else if (favStar.classList.contains("userActivityX")){
		actExitBut = favStar;
		deleteActivity();
	}

	else if (favStar.classList.contains("addTagC")){
		activityFromClick = favStar.parentNode.parentNode.value;
	}
}

//Adds a tag to an activity
function addTag(tagObject){
    $.ajax({
        type: 'POST',
        url: 'api/index.php/addTagToActivity',
        content: 'application/json',
        data: JSON.stringify(tagObject),
        success: function(data){
        }
    });    
    $("#closeButton").click();
}

//Gets the user's ID for use in other functions
function getUserID(){
	user = new Object();
	sessionData = {};
    $.ajax({
        type: 'POST',
        async: false,
        url: 'api/index.php/getSessionInfo',
        success: function(data){
            sessionData = JSON.parse(data);
        }
    });
    user.UserID = sessionData.UserID;
}

//Gets the users favorite activites and displays them in a lit star on the searched activities
function displayFavoriteActivities(){
	var tags = new Object();
	var ratingDisplay;
	$.ajax({
		type: 'POST',
		url: 'api/index.php/viewFavorites',
		content: 'application/json',
		data: JSON.stringify(user),
		success: function(data){
			console.log(data);
			var actData = jQuery.parseJSON(data);
	    	var activitiesDiv = $("#searchResults");
	    	for (i = 0; i < actData.length; i++)
	    	{
	    		if(actData[i].ActivityID != null){
		    		$.ajax({
						type: 'GET',
						url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
						content: 'application/json',
						success: function(data2){
							tags = jQuery.parseJSON(data2);
							if (tags.length <= 0)
								tags = null;
						}
					});

		    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\n";
		    		var hTags = "<h5 class='tags'> ";
		    		if (!(tags === null)){
			    		for (var x = 0; x < tags.length; x++){
			    			hTags = hTags + tags[x].TagName + "&nbsp&nbsp&nbsp";
			    		}
			    		hTags = hTags + "</h5>";
		    		}
		    		elem = elem + "\"></li>";

		    		var activityDiv = $(elem).appendTo(activitiesDiv);
		    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
		    		activityDiv.append("<p class='starred'></p>");

		    		if (actData[i].Rating != null)
						ratingDisplayed = (Math.round(actData[i].Rating * 10 ) / 10).toFixed(1);
					else
						ratingDisplayed = "Not Rated";

		    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button></a> " + "<p class='ratingDisplay'>Rating: " + ratingDisplayed + "</p>" + hTags + " <a href='#addTagBox'><button name='addTag' class='addTagC' title='Add a Tag!'>+</button></a>").appendTo(activityDiv);
	    		}
	    	}
	    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
	    		addDrag();
		},
		error: function(){
			console.log('Unable to get favorite activities');
		}
	});
}


//Searches for activites by their name
function displayActivitiesByName(searchString){
	var searchQuery = new Object();
	var activities, tags, searchList, favActivities, activityListItem, tagsDisplayed, star, starElement, activityTitle, len, index, ratingDisplayed;

	searchQuery.SearchQuery = searchString;
	searchQuery.tagName = searchString;

	actData = getActsByTags(searchQuery);
	activities = getActsByName(searchQuery);

	if (!jQuery.isEmptyObject(actData) && !jQuery.isEmptyObject(activities)){
		for (var a = 0; a < activities.length; a++){
			for (var b = 0; b < actData.length; b++){
				if (activities[a].ActivityID == actData[b].ActivityID){
					actData.splice(b, 1);
				}
			}
		}
		activities = activities.concat(actData);
	}
	searchList = $("#searchResults");
	favActivities = getFavoriteActivities();

	for (i = 0; i < activities.length; i++){
		tags = getTagsByActID(activities[i].ActivityID);
		activityListItem = "<li class='activity' value=" + activities[i].ActivityID + " title=\"" + activities[i].Description + "\"></li>";
		activityListItem = $(activityListItem).appendTo(searchList);

		activityTitle = "<h3>" + activities[i].Name + "</h3>";

		star = 'unstarred';
		for (j = 0; j < favActivities.length; j++){
			if (favActivities[j].Name == activities[i].Name){
				star = 'starred';
				break;
			}
		}
		starElement = "<p class='" + star +"'></p> ";

		tagsDisplayed = "<h5 class='tags'> ";
		if (tags.length < 4)
			len = tags.length;
		else
			len = 4;
		for (var x = 0; x < len; x++){
			tagsDisplayed += tags[x].TagName + "&nbsp&nbsp&nbsp";
		}
		tagsDisplayed += "</h5>";

		if (activities[i].Rating != null)
			ratingDisplayed = (Math.round(activities[i].Rating * 10 ) / 10).toFixed(1);
		else
			ratingDisplayed = "Not Rated";

		$(activityTitle + starElement + "<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + activities[i].ActivityID + "'>Review</button></a> " + "<p class='ratingDisplay'>Rating: " + ratingDisplayed + "</p>" + tagsDisplayed + " <a href='#addTagBox'><button name='addTag' class='addTagC' title='Add a Tag!'>+</button></a>").appendTo(activityListItem);
	}
	if (checkToAddDrag())
    	addDrag();
}

//Allows the user to logout of their account
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
//Lets the user drag over activites to their date plan
function addDrag(){
	var v;
	$( ".activity" ).draggable({
		connectToSortable: "#currentDatePlan",
		helper: "clone",
		revert: "invalid",
		stop: function(event, ui){
			if ($('.activityDatePlan').length == 0)
				createDatePlan();
			var $elems = $('#currentDatePlan .activity[value=' + ui.helper.context.value + ']');
			$elems.switchClass("activity", "activityDatePlan");
			$elems.removeAttr("style");
			$('#currentDatePlan .activity[value=' + ui.helper.context.value + '] button').remove(".reviewBut");
			$('#currentDatePlan .activity[value=' + ui.helper.context.value + '] button').remove(".addTagC");
			$('#currentDatePlan .activity[value=' + ui.helper.context.value + '] input').remove(".tagText");
			$('#currentDatePlan .activity[value=' + ui.helper.context.value + '] p').remove(".ratingDisplay");
			$("<button class='userActivityX' value=" + ui.helper.context.value + "> X </button>").insertAfter($('#currentDatePlan .activity[value=' + ui.helper.context.value + '] .unstarred'));
			$("<button class='userActivityX' value=" + ui.helper.context.value + "> X </button>").insertAfter($('#currentDatePlan .activity[value=' + ui.helper.context.value + '] .starred'));

			setTimeout(addActivityToDatePlan, 1000);

		}
    });
}

function addButton(v){
	var datePlanBut = $('.activityDatePlan')[$('.activityDatePlan').length-1];
	$("<button class='userActivityX' value=" + v + "> X </button>").appendTo(datePlanBut);
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

//Lets a user review an activity in the review activity modal
function reviewActivity(){
	inputActivityID = reviewBut.parentNode.parentNode.value;
	inputUserID = user.UserID;
    inputRating = $("input[name=rating]:checked").val();
    inputDescription = $("#descriptionReview").val();
    inputCostReview = $("#costReview").val();
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
    $("#closeButton").click();
	}
	else{
		 $("#resultMessageActivityReview").text("You must attend an Activity to rate it!");
	}
}
//Lets the user review Date Plans in the review date plans modal
function reviewDatePlan(){
	//grab dateplanid 
	inputDatePlanID = reviewButton.id.substring(6, reviewButton.id.length);
	inputUserID = user.UserID;
    inputRating = $("input[name=rating]:checked").val();
    inputDescription = $("#descriptionReviewDatePlan").val();
    inputattended = $("input[name=attended]:checked").val();
     console.log(inputattended);
    //create activity object 
    if(inputattended == 1){
    newReview = new Object();
    newReview.Rating = inputRating;
    newReview.DatePlanID = inputDatePlanID;
    newReview.UserID = inputUserID;
    newReview.Description = inputDescription;
    newReview.Attended = inputattended;
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
    //clear input from form on close
    $("#reviewDatePlan").trigger( "reset" );
	}
	else{
		 $("#resultMessageDatePlan").text("You must attend a Date Plan to rate it!");
	}
}
//Searches date plans in our database
//Pass null to perform normal search with the search.html's search bar
//otherwise it will perform a search using the passed in searchString
function searchDatabase(searchString){
	var starElement;
	var searchQuery = new Object();
	searchQuery.SearchQuery = searchString;
	var star, len, ratingDisplayed;
	var tags = new Object();
	var favData = getFavoriteActivities();

	var datePlansDiv = $("#searchResults");
	searchQuery.tagName = $("#searchbar").val();
	var planData = getDatePlansByName(searchQuery);
	var planData2 = getDatePlansByTag(searchQuery);
	if(!jQuery.isEmptyObject(planData) && !jQuery.isEmptyObject(planData2)){
		for (var a = 0; a < planData.length; a++){
			for (var b = 0; b < planData2.length; b++){
				if (planData[a].DataPlanID == planData2[b].DatePlanID){
					planData2.splice(b, 1);
				}
			}
		}
		planData = planData.concat(planData2);
	}
	else if(jQuery.isEmptyObject(planData) && !jQuery.isEmptyObject(planData)){
		planData = planData2;
	}

	for (var k = 0; k < planData.length; k++){
		var searchedDatePlan = "<ul class='dateplan' value=" + planData[k].DatePlanID + " title=\"" + planData[k].Description + "\"></ul>";
		$(searchedDatePlan).appendTo(datePlansDiv);
		var datePlanDiv = $('.dateplan')[k];

		star = 'unstarred';
		for (j = 0; j < favData.length; j++){
			if (favData[j].Name == planData[k].Name){
				star = 'starred';
				break;
			}
		}
		var starElement = $("<p class='" + star + "' id=" + planData[k].DatePlanID + "></p>");
		starElement.appendTo(datePlanDiv);
		
		var dateTitle = $("<h2></h2>").text(planData[k].Name)//.appendTo(datePlanDiv);
		dateTitle.appendTo(datePlanDiv);

		for (var l = 0; l < planData[k].AssociatedActivities.length; l++){
			$.ajax({
				type: 'GET',
			    url: 'api/index.php/getActivityById/' + planData[k].AssociatedActivities[l].ActivityID,
			    async: false,
			    success: function(data2) {
			    	if(!jQuery.isEmptyObject(data2)){
				    	var actData = jQuery.parseJSON(data2);

					    for (i = 0; i < actData.length; i++){

							$.ajax({
								type: 'GET',
								url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
								async: false,
								content: 'application/json',
								success: function(data3){
									tags = jQuery.parseJSON(data3);
								}
							});

				    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description;
				    		var hTags = "<h5 class='tags'> ";
				    		if (!(tags === null)){
				    			if (tags.length < 4)
									len = tags.length;
								else
									len = 4;
					    		for (var x = 0; x < len; x++){
					    			hTags = hTags + tags[x].TagName + "&nbsp&nbsp&nbsp";
					    		}
					    		hTags = hTags + "</h5>";
				    		}
				    		elem = elem + "\"></li>";
				    		var activityDiv = $(elem).appendTo(datePlanDiv);
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

				    		if (actData[i].Rating != null)
								ratingDisplayed = (Math.round(actData[i].Rating * 10 ) / 10).toFixed(1);
							else
								ratingDisplayed = "Not Rated";

				    		$("<br>" + "<p class='ratingDisplay'>Rating: " + ratingDisplayed + "</p>" + hTags + " <a href='#addTagBox'><button name='addTag' class='addTagC' title='Add a Tag!'>+</button></a>").appendTo(activityDiv);
				    	}		
				    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
						    addDrag();
					}
				}, 
				error: function(jqXHR, errorThrown){
					console.log('We didnt make it sir, sorry');
					console.log(jqXHR, errorThrown);
				}						
			});
		}
		if(user.UserID != null){
			if (planData[k].Rating != null)
				ratingDisplayed = (Math.round(planData[k].Rating * 10 ) / 10).toFixed(1);
			else
				ratingDisplayed = "Not Rated";
			$("<a id='DescriptionBoxAnchor' href='#ReviewDatePlanBox'> <button href='#ReviewDatePlanBox' name='Review' class='reviewButton' id='review" + planData[k].DatePlanID + "'>Review</button> </a>" + "<p class='ratingDisplay dateRating'>Rating: " + ratingDisplayed + "</p>").appendTo(datePlanDiv);
		}
	}	
}

//Lets the user create a date plan
function createDatePlan(userDatePlanInfo){
	userDatePlanInfo = new Object();
	userDatePlanInfo.Name = $("#datePlanName").val();
	datePlanActivity.Name = userDatePlanInfo.Name;
	userDatePlanInfo.UserID = user.UserID;
	$.ajax({
		type: 'POST',
		url: 'api/index.php/createDatePlan',
		content: 'application/json',
		data: JSON.stringify(userDatePlanInfo),
		success: function(data){
			console.log($('.activityDatePlan').length);
			data = data.substring(10);
			datePlanActivity.DatePlanID = jQuery.parseJSON(data).DatePlanID;
			console.log("New date plan created");
		}
	});
}

//Lets the user add activities to the date plan
function addActivityToDatePlan(){
	var numActivities = $('.activityDatePlan').length;
	datePlanActivity.ActivityID = $('.activityDatePlan')[numActivities-1].value;
	$.ajax({
		type: 'POST',
		url: 'api/index.php/addActivity',
		content: 'application/json',
		data: JSON.stringify(datePlanActivity),
		success: function(data){
			console.log("Activity added to date plan");
		}
	});
}

//Edit the description of a date plan 
function editDescription(){
	var sentData = new Object();
	sentData.Description = $("#descriptionDatePlan").val();
	sentData.DatePlanID = datePlanActivity.DatePlanID;
	sentData.UserID = user.UserID;
	console.log(sentData);
	$.ajax({
		type: 'POST',
		url: 'api/index.php/updateDatePlanDescription',
		content: 'application/json',
		data: JSON.stringify(sentData),
		success: function(data){
			console.log(data);
			if(data == 1) {
				$('#DescriptionBox').hide();
			}
			else {
				console.log("Description Failed to update");
			}

		}
	});
}

//Searches date plans by their tags
function searchDatePlanTags(){
	var searchQuery = new Object();
	searchQuery.tagName = $("#searchbar").val();
	var star, len;
	var favData = getFavoriteActivities();

	    	var datePlansDiv = $("#searchResults");
	    	var planData = getDatePlansByTag(searchQuery);
	    	console.log(planData);

	    	if(!jQuery.isEmptyObject(planData)){

	    	for (var k = 0; k < planData.length; k++){
	    		var searchedDatePlan = "<ul class='dateplan' value=" + planData[k].DatePlanID + " title=\"" + planData[k].Description + "\"></ul>";
	    		$(searchedDatePlan).appendTo(datePlansDiv);
	    		var datePlanDiv = $('.dateplan')[k];

	    		star = 'unstarred';
	    		for (j = 0; j < favData.length; j++){
					if (favData[j].Name == planData[k].Name){
						star = 'starred';
						break;
					}
				}
	    		var starElement = $("<p class='" + star + "' id=" + planData[k].DatePlanID + "></p>");
	    		starElement.appendTo(datePlanDiv);

	    		$("<h2></h2>").text(planData[k].Name).appendTo(datePlanDiv);

	    		for (var l = 0; l < planData[k].AssociatedActivities.length; l++){
	    			$.ajax({
						type: 'GET',
					    url: 'api/index.php/getActivityById/' + planData[k].AssociatedActivities[l].ActivityID,
					    async: false,
					    success: function(data2) {
					    	if(!jQuery.isEmptyObject(data2)){
					    	var actData = jQuery.parseJSON(data2);

						    for (i = 0; i < actData.length; i++){

								$.ajax({
									type: 'GET',
									url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
									async: false,
									content: 'application/json',
									success: function(data3){
										if (data3 != 500)
											tags = jQuery.parseJSON(data3);
										else
											tags = null;
									}
								});

					    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description;
					    		var hTags = "<h5 class='tags'> ";
					    		if (!(tags === null)){
					    			if (tags.length < 4)
										len = tags.length;
									else
										len = 4;
						    		for (var x = 0; x < len; x++){
						    			hTags = hTags + tags[x].TagName + "&nbsp&nbsp&nbsp";
						    		}
						    		hTags = hTags + "</h5>";
					    		}
					    		elem = elem + "\"></li>";
					    		var activityDiv = $(elem).appendTo(datePlanDiv);
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
					    		$("<br>" + hTags + " <a href='#addTagBox'><button name='addTag' class='addTagC' title='Add a Tag!'>+</button></a>").appendTo(activityDiv);
					    	}		
					    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
							    addDrag();
						}
						}, 
						error: function(jqXHR, errorThrown){
							console.log('We didnt make it sir, sorry');
							console.log(jqXHR, errorThrown);
						}						
					});
	    		}
	    		if(user.UserID != null)
	    		$("<a id='DescriptionBoxAnchor' href='#ReviewDatePlanBox'> <button href='#ReviewDatePlanBox' name='Review' class='reviewButton' id='review" + planData[k].DatePlanID + "'>Review</button> </a>").appendTo(datePlanDiv);
	    	}	
	    	}  
}

//Gets the user date plans and displays it on our clipboard
function getUserDatePlans(){
	$('#descriptionBut').hide();
	$('#myPlansBut').hide();
	$('#datePlanName').hide();
	$('#backToCreateBut').show();
	$('#publishLabel').hide();
	$('.activityDatePlan').remove();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/viewUserDatePlans',
		content: 'application/json',
		data: JSON.stringify(user),
		success: function(data){
			if(data.length>2){
			var dateData = jQuery.parseJSON(data);
			var dateHolder = $('#currentDatePlan');

			for (var x = 0; x < dateData.length; x++) {
		    	var elem = "<li class='userDatePlan' value=" + dateData[x][0].DatePlanID + " title=\"" + dateData[x][0].Description + "\"></li><br/>";
		    	var datePlanItem = $(elem).appendTo(dateHolder);
		    	$("<button class='userDatePlanX' value=" + dateData[x][0].DatePlanID + "> X </button>").appendTo(datePlanItem);
			    $("<h3>" + dateData[x][0].Name + "</h3><br/>").appendTo(datePlanItem);
			    $("<a id='reviewDatePlanBoxAnchor' href='#ReviewDatePlanBox'> <button href='#ReviewDatePlanBox' name='Review' class='reviewButton' id='review" + dateData[x][0].DatePlanID + "'>Review</button> </a>").appendTo(datePlanItem);
			}
		}
		}
	});
}

//Deletes a user dateplan
function deleteUserPlan(){
	var datePlanUser = new Object();
	datePlanUser.DatePlanID = exitBut.value;
	datePlanUser.UserID = user.UserID;

	$.ajax({
        type: 'DELETE',
      	url: 'api/index.php/deleteDatePlan',
      	content: 'application/json',
      	data: JSON.stringify(datePlanUser),
		success: function(data){
            console.log(exitBut.parentNode.previousSibling);
            exitBut.parentNode.previousSibling.parentNode.removeChild(exitBut.parentNode.previousSibling);
            exitBut.parentNode.parentNode.removeChild(exitBut.parentNode);
		}
    });
}

//Deletes an activity
function deleteActivity(){
	$.ajax({
		type: 'DELETE',
		url: 'api/index.php/deleteDateActivity/' + datePlanActivity.DatePlanID + '/' + actExitBut.value,
		success: function(data){
			console.log("Activity deleted");
            actExitBut.parentNode.parentNode.removeChild(actExitBut.parentNode);
		},
		error: function(){
			console.log("Unable to delete function for some reason");
		}
	});
}

//Opens a user DatePlan
function openDatePlan(){
	var tags = new Object();
	$.ajax({
		type: 'GET',
		url: 'api/index.php/getDateplanById/' + userPlan.value,
		content: 'application/json',
		success: function(data){
			var datePlanFromData = $.parseJSON(data)[0];
			console.log(datePlanFromData);
			check = datePlanFromData.Public;
			var clipBoard = $('#currentDatePlan');
			datePlanActivity.DatePlanID = datePlanFromData.DatePlanID;
			$('#descriptionBut').show();
			$('#myPlansBut').show();
			$('#datePlanName').show();
			$('#datePlanName').val("");
			$('#backToCreateBut').hide();
			if (check == 0){
				$('#publishCheckbox').show()
				$('#publishLabel').show();
			}
			$('.userDatePlan').parent().empty();
			$('#datePlanName').val(datePlanFromData.Name);
			$.ajax({
				type: 'GET',
				url: 'api/index.php/getAssociatedActivities/' + datePlanActivity.DatePlanID + '/1',
				success: function(data2){
					var associatedActivities = jQuery.parseJSON(data2);
					for (var x = 0; x < associatedActivities.length; x++){
						$.ajax({
							type: 'GET',
							async: false,
							url: 'api/index.php/getActivityById/' + associatedActivities[x].ActivityID,
							content: 'application/json',
							success: function(data3){
								var actData = jQuery.parseJSON(data3);

								for (i = 0; i < actData.length; i++){
								$.ajax({
									type: 'GET',
									url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
									async: false,
									content: 'application/json',
									success: function(data3){
										tags = jQuery.parseJSON(data3);
									}
								});

								var elem = "<li class='activityDatePlan' value=" + actData[0].ActivityID + " title=\"" + actData[0].Description + "\"></li>";
								var hTags = "<h5 class='tags'> ";
					    		if (!(tags === null)){
					    			if (tags.length < 4)
										len = tags.length;
									else
										len = 4;
						    		for (var x = 0; x < len; x++){
						    			hTags = hTags + tags[x].TagName + "&nbsp&nbsp&nbsp";
						    		}
						    		hTags = hTags + "</h5>";
					    		}
						    	var activityDiv = $(elem).appendTo(clipBoard);
							    var starunstar = 'unstarred';
							    $("<h3>" + actData[0].Name + "</h3>").appendTo(activityDiv);

						    	$.ajax({
						    		type: 'POST',
						    		url: 'api/index.php/viewFavorites',
						    		content: 'application/json',
						    		async: false,
						    		data: JSON.stringify(user),
						    		success: function(data4){
							    		var favData = jQuery.parseJSON(data4);						  
							    		for (var x = 0; x < favData.length; x++) {
							    			if (favData[x].Name == actData[0].Name){
							    				starunstar = 'starred';
							    				break;
							    			}
							    		}
						    		}
					    		});
					    		var starString = "<p class='" + starunstar + "'></p>";
							    $(starString).appendTo(activityDiv);
								$("<button class='userActivityX' value=" + actData[0].ActivityID + "> X </button>" + hTags).appendTo(activityDiv);
							}
							    if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
							    	addDrag();
							}
						})
					}
				}
			});
		}
	}); //getDatePlanById ajax call
}

//Publishes a user date plan for everyone to search for
function publishDatePlan(check){
	var datePlanUser = new Object();
	datePlanUser.datePlanID = datePlanActivity.DatePlanID;
	datePlanUser.userID = user.UserID;

	$.ajax({
		type: 'POST', 
		url: 'api/index.php/shareDatePlan',
		content: 'application/json',
		data: JSON.stringify(datePlanUser),
		success: function(data){
			console.log("Date Plan is now public");
		}
	});
}

//Updates the name of a date plan
function updateName(){
	var datePlanName = new Object();
	datePlanName.DatePlanID = datePlanActivity.DatePlanID;
	datePlanName.Name = $("#datePlanName").val();
	datePlanActivity.Name = datePlanName.Name;
	datePlanName.UserID = user.UserID;

	if (user.UserID != null)
	{
		$.ajax({
			type: 'POST',
			url: 'api/index.php/updateDatePlanName',
			content: 'application/json',
			data: JSON.stringify(datePlanName),
			success: function(data){
			}
		});
	}
}

//Searches for Activites by their tag.
function displayActivitiesByTag(searchString){
	var searchQuery = new Object();
	var favoriteActivities, actData, activitiesDiv, elem, hTags, activityDiv, starunstar, tags, len;

	searchQuery.tagName = searchString;

	favoriteActivities = getFavoriteActivities();
	actData = getActsByTags(searchQuery);
	activitiesDiv = $("#searchResults");

	for (i = 0; i < actData.length; i++){
		tags = getTagsByActID(actData[i].ActivityID);
		elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\"></li>";

		if (tags.length < 4)
			len = tags.length;
		else
			len = 4;
		hTags = "<h5 class='tags'> ";
		for (var x = 0; x < len; x++){
			hTags += tags[x].TagName + "&nbsp&nbsp&nbsp";
		}
		hTags += "</h5>";

		activityDiv = $(elem).appendTo(activitiesDiv);
		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);

		starunstar = 'unstarred';
		for (j = 0; j < favoriteActivities.length; j++){
			if (favoriteActivities[j].Name == actData[i].Name){
				starunstar = 'starred';
				break;
			}
		}
		var starString = "<p class='" + starunstar +"'></p> ";
		
		$(starString).appendTo(activityDiv);
		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button></a> " + hTags + " <a href='#addTagBox'><button name='addTag' class='addTagC' title='Add a Tag!'>+</button></a>").appendTo(activityDiv);
	}
	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
		addDrag();
}

function getActsByTags(searchString){
	var activities = new Object();
	$.ajax({
		type: 'POST',
	    url: 'api/index.php/getTaggedActivities',
	    async: false,
	    content: 'application/json',
	    data: JSON.stringify(searchString),
	    success: function(data){
	    	if (data)
		    	activities = jQuery.parseJSON(data);
	    }
	});
	return activities;
}

//gets a users favorite activities from the database
function getFavoriteActivities(){
	var favoriteActivities = new Object();
	if(user.UserID != undefined){
		$.ajax({
			type: 'POST',
			url: 'api/index.php/viewFavorites',
			async: false,
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				favoriteActivities = jQuery.parseJSON(data);
			}
		});
	}
	return favoriteActivities;
}

function newSearch(){
	hideActsAndPlans();
	return $("#searchbar").val();
}

function hideActsAndPlans(){
	$('.activity').remove();
	$('.dateplan').remove();
}

function getTagsByActID(activityID){
	var tags = new Object();
	$.ajax({
		type: 'GET',
		url: 'api/index.php/getTagsFromActivityID/' + activityID,
		async: false,
		success: function(data){
			tags = jQuery.parseJSON(data);
		}
	});
	return tags;
}

function getActsByName(searchString){
	var activities = new Object();
	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchActivities',
		async: false,
		content: 'application/json',
		data: JSON.stringify(searchString),
		success: function(data) {
			if (data)
				activities = jQuery.parseJSON(data).Activities;
		}
	});
	return activities;
}

function checkToAddDrag(){
	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')) && user.UserID != null)
		return true;
	else
		return false;
}

function getDatePlansByName(searchQuery){
	var planData = new Object();
	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchDateplans',
		content: 'application/json',
		async: false,
		data: JSON.stringify(searchQuery),
		success: function(data){
			if (data)
				planData = $.parseJSON(data).DatePlans;
		}
	});
	return planData;
}

function getDatePlansByTag(searchQuery){
	var planData = new Object();
	$.ajax({
		type: 'POST',
		url: 'api/index.php/getTaggedDatePlans',
		content: 'application/json',
		async: false,
		data: JSON.stringify(searchQuery),
		success: function(data){
			if (data)
				planData = jQuery.parseJSON(data);
		}
	});
	return planData;
}