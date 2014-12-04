jQuery(document).ready(function() {
	var reviewButton;
	getUserID();
	datePlanActivity = new Object();
	$('#backToCreateBut').hide();

	console.log(user.UserID);

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
	//Search Activities
	$('#searchform').submit(function (e) {
		e.preventDefault();
		if ($("input[name=search]:checked").val() == "activityTag"){
			var searchString = new Object();
			searchString.tagName = $("#searchbar").val();
			searchString = JSON.stringify(searchString);
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
		} else if ($("input[name=search]:checked").val() == "datePlanTag"){
			$('.activity').remove();
			$('.dateplan').remove();
			searchDatePlanTags();
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

	$('#descriptionBut').click(function(e){
		$('#DescriptionBox').show();
	})

	$('#myPlansBut').click(function(){
		getUserDatePlans();
	})

	$('#backToCreateBut').click(function(){
		updateName();
		$('#descriptionBut').show();
		$('#myPlansBut').show();
		$('#datePlanName').show();
		$('#datePlanName').val("");
		$('#backToCreateBut').hide();
		$('#publishLabel').show();
		$('.userDatePlan').parent().empty();
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

	$('#publishCheckbox').change(function(){
		var check = 0;
		if (this.checked){
			check = 1;
		}
		publishDatePlan(check);
	})

	addSort();
});

//Searches for Activites by their tag.
function getActivitiesByTag(searchString){
	$.ajax({
		type: 'POST',
	    url: 'api/index.php/getTaggedActivities',
	    async: false,
	    content: 'application/json',
	    data: searchString,
	    success: function(data) {
	    	var actData = jQuery.parseJSON(data);
	    	var activitiesDiv = $("#searchResults");
	    	favData = new Object;
	    	if(user.UserID != undefined){
				$.ajax({
					type: 'POST',
					url: 'api/index.php/viewFavorites',
					async: false,
					content: 'application/json',
					data: JSON.stringify(user),
					success: function(data2){
						favData = jQuery.parseJSON(data2);
					}
				});
			}

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

	    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\n";
	    		if (!(tags === null)){
		    		for (var x = 0; x < tags.length; x++){
		    			elem = elem + tags[x].TagName + " ";
		    		}
	    		}
	    		elem = elem + "\"></li>";
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
	    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button></a> <input type='text' size='15' maxlength='20' name='tagField' class='tagText'> <button name='addTag' class='addTagC' title='Add a Tag!'>+</button>").appendTo(activityDiv);
	    	}
	    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')))
	    		addDrag();
		}, 
		error: function(jqXHR, errorThrown){
			console.log('We didnt make it sir, sorry');
			console.log(jqXHR, errorThrown);
		}
	});
}

//Get the mouse position. Used to access the review button and favorite star in the date plans and activities 
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
		console.log(reviewButton.parentNode.parentNode.value);
		$("#ReviewDatePlanBox").show();
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

	else if (favStar.classList.contains("addTag")){
		tagBut = favStar;
		addTag();
	}

	if ($("#datePlanName").val() != datePlanActivity.Name || $("#datePlanName").val() != ""){
		updateName();
	}
}

//Adds a tag to an activity
function addTag(){
	var tag = new Object();
    tag.tagName = tagBut.previousSibling.value;
    tag.activityID = tagBut.parentNode.value;
    $.ajax({
        type: 'POST',
        url: 'api/addTagToActivity',
        content: 'application/json',
        data: JSON.stringify(tag),
        success: function(data){
            console.log(data);
        }
    });    
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
	    		if (!(tags === null)){
		    		for (var x = 0; x < tags.length; x++){
		    			elem = elem + tags[x].TagName + " ";
		    		}
	    		}
	    		elem = elem + "\"></li>";

	    		var activityDiv = $(elem).appendTo(activitiesDiv);
	    		$("<h3></h3>").text(actData[i].Name).appendTo(activityDiv);
	    		activityDiv.append("<p class='starred'></p>");
	    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button></a> <input type='text' name='tagField' size='15' maxlength='20' class='tagText'> <button name='addTag' class='addTagC' title='Add a Tag!'>+</button>").appendTo(activityDiv);
	    	}
	    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')))
	    		addDrag();
		},
		error: function(){
			console.log('Unable to get favorite activities');
		}
	});
}

//Searches for activites by their name
function getActivitiesByName(){
	var searchQuery = new Object();
	searchQuery.SearchQuery = $("#searchbar").val();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchActivities',
		async: false,
		content: 'application/json',
		data: JSON.stringify(searchQuery),
		success: function(data) {
	    	var activitiesDiv = $("#searchResults");
	    	var actData = $.parseJSON(data).Activities;
	    	favData = new Object;
	    	if(user.UserID != undefined){
				$.ajax({
					type: 'POST',
					url: 'api/index.php/viewFavorites',
					async: false,
					content: 'application/json',
					data: JSON.stringify(user),
					success: function(data2){
						favData = jQuery.parseJSON(data2);
					}
				});
			}

			for (i = 0; i < actData.length; i++){

	    		$.ajax({
					type: 'GET',
					url: 'api/index.php/getTagsFromActivityID/' + actData[i].ActivityID,
					async: false,
					content: 'application/json',
					success: function(data2){
						console.log(data2);
						tags = new Object();
						tags = jQuery.parseJSON(data2);
					}
				});

	    		var elem = "<li class='activity' value=" + actData[i].ActivityID + " title=\"" + actData[i].Description + "\n";
	    		for (var x = 0; x < tags.length; x++){
	    			elem = elem + tags[x].TagName + " ";
	    		}
	    		elem = elem + "\"></li>";
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
	    		$("<a id='reviewActivityBoxAnchor' href='#ReviewActivityBox'> <button href='#ReviewActivityBox' name='Review' class='reviewBut' id='review" + actData[i].ActivityID + "'>Review</button></a> <input type='text' name='tagField' size='15' maxlength='20' class='tagText'> <button name='addTag' class='addTagC' title='Add a Tag!'>+</button>").appendTo(activityDiv);
	    	}
	    	if (!$.contains($('#currentDatePlan'), $('#userDatePlan')))
		    	addDrag();
		}
	});
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
	console.log("Adding Drag");
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
	console.log("DATE PLAN ID");
	inputDatePlanID = reviewButton.parentNode.parentNode.value;
	inputUserID = user.UserID;
    inputRating = $("input[name=rating]:checked").val();
    inputDescription = $("#descriptionReviewDatePlan").attr("value");
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
//Searches date plans in our database
function searchDatabase(){
	var searchQuery = new Object();
	searchQuery.SearchQuery = $("#searchbar").val();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchDateplans',
		content: 'application/json',
		data: JSON.stringify(searchQuery),
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
					    	var elem = "<li class='activity' value=" + actData[0].ActivityID + " title=\"" + actData[0].Description + "\"></li><br>";
					    	var activityDiv = $(elem).appendTo(datePlanDiv);
						    var starunstar = 'unstarred';
						    $("<h3>" + actData[0].Name + "</h3>").appendTo(activityDiv);

					    	$.ajax({
					    		type: 'POST',
					    		url: 'api/index.php/viewFavorites',
					    		content: 'application/json',
					    		async: false,
					    		data: JSON.stringify(user),
					    		success: function(data3){
						    		var favData = jQuery.parseJSON(data3);							    
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
	var datePlanDescription = $("#descriptionDatePlan").val();
	$.ajax({
		type: 'POST',
		url: 'api/index.php/updateDatePlanDescription',
		content: 'application/json',
		data: JSON.stringify(datePlanDescription),
		success: function(data){
			console.log("Successfully added description.");
		}
	});
}

//Searches date plans by their tags
function searchDatePlanTags(){
	var searchQuery = new Object();
	searchQuery.tagName = $("#searchbar").val();

	$.ajax({
		type: 'POST',
		url: 'api/index.php/getTaggedDatePlans',
		content: 'application/json',
		data: JSON.stringify(searchQuery),
		success: function(data){
			console.log(data);
	    	var datePlansDiv = $("#searchResults");
	    	var planData = data[0].DatePlans;
	    	console.log(planData);
	    	for (var k = 0; k < planData.length; k++){
	    		var searchedDatePlan = "<ul class='dateplan' value=" + planData[k].DatePlanID + " title=\"" + planData[k].Description + "\"></ul>";
	    		var datePlanDiv = $('.dateplan')[k];
	    		$("<h2></h2>").text(planData[k].Name).appendTo(datePlanDiv);
	    		for (var l = 0; l < planData[k].AssociatedActivities.length; l++){
	    			$.ajax({
						type: 'GET',
					    url: 'api/index.php/getActivityById/' + planData[k].AssociatedActivities[l].ActivityID,
					    async: false,
					    success: function(data2) {
					    	var actData = jQuery.parseJSON(data2);
					    	var elem = "<li class='activity' value=" + actData[0].ActivityID + " title=\"" + actData[0].Description + "\"></li><br>";
					    	var activityDiv = $(elem).appendTo(datePlanDiv);
						    var starunstar = 'unstarred';
						    $("<h3>" + actData[0].Name + "</h3>").appendTo(activityDiv);

					    	$.ajax({
					    		type: 'POST',
					    		url: 'api/index.php/viewFavorites',
					    		content: 'application/json',
					    		async: false,
					    		data: JSON.stringify(user),
					    		success: function(data3){
						    		var favData = jQuery.parseJSON(data3);							    
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
						    if (!$.contains($('#currentDatePlan'), $('#userDatePlan')))
							    addDrag();
						}, 
						error: function(jqXHR, errorThrown){
							console.log('We didnt make it sir, sorry');
							console.log(jqXHR, errorThrown);
						}						
					});
	    		}
	    		$("<a id='DescriptionBoxAnchor' href='#addDescription'> <button href='#RaddDescription' name='Review' class='reviewButton' id='review" + planData[k].DatePlanID + "'>Review</button> </a>").appendTo(datePlanDiv);
	    	}	    	
		}
	});
}

//Gets the user date plans and displays it on our clipboard
function getUserDatePlans(){
	updateName()
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
			console.log(data);
			if(data.length>2){

			console.log(jQuery.parseJSON(data)[0][0]);
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
	$.ajax({
		type: 'GET',
		url: 'api/index.php/getDateplanById/' + userPlan.value,
		content: 'application/json',
		success: function(data){
			var datePlanFromData = $.parseJSON(data)[0];
			var clipBoard = $('#currentDatePlan');
			datePlanActivity.DatePlanID = datePlanFromData.DatePlanID;
			$('#descriptionBut').show();
			$('#myPlansBut').show();
			$('#datePlanName').show();
			$('#datePlanName').val("");
			$('#backToCreateBut').hide();
			$('#publishLabel').show();
			$('.userDatePlan').parent().empty();
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
								var elem = "<li class='activityDatePlan' value=" + actData[0].ActivityID + " title=\"" + actData[0].Description + "\"></li>";
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
								$("<button class='userActivityX' value=" + actData[0].ActivityID + "> X </button>").appendTo(activityDiv);
							    if (!$.contains($('#currentDatePlan'), $('#userDatePlan')))
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

	$.ajax({
		type: 'POST',
		url: 'api/index.php/updateDatePlanName',
		content: 'application/json',
		data: JSON.stringify(datePlanName),
		success: function(data){
			console.log("Date Plan name changed")
		}
	});
}