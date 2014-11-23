//Test function
//Get activity by id
function getActivityById(){
		$.ajax({
			type: 'GET',
			url: 'api/getActivityById/1',
			content: 'application/json',
			success: function(data){
				if($.isNumeric(data)){
					if(data==500){
						console.log("There are no results");
					}
				}else{
					console.log(data);
				}
			}
		});
}
//Test function
//Get tags from activity
function getTagsFromActivityID(){
		$.ajax({
			type: 'GET',
			url: 'api/getTagsFromActivityID/1',
			content: 'application/json',
			success: function(data){
				if($.isNumeric(data)){
					if(data==500){
						console.log("There are no results");
					}
				}else{
					console.log(data);
				}
			}
		});
}
//Test function
//Get dateplan by id
function getDatePlanById(){
		$.ajax({
			type: 'GET',
			url: 'api/getDateplanById/1',
			content: 'application/json',
			success: function(data){
				if($.isNumeric(data)){
					if(data==500){
						console.log("There are no results");
					}
				}else{
					console.log(data);
				}
			}
		});
}


//Test function
//Search by location, activity, or just general query
//Uses Myisam FULLTEXT indexing
function updateDatePlan(){
	var dateplan = new Object();
	dateplan.Name = "Testing Update Plan";
	dateplan.Public = 0;
	dateplan.ModID = 1;
	dateplan.Description = "Testing Update Plan Description";
	dateplan.Activites =[1,2,3];
	dateplan.DatePlanID =1;
	console.log("What I am sending to update date plan: "+JSON.stringify(dateplan));
		$.ajax({
			type: 'POST',
			url: 'api/index.php/updateDatePlan',
			content: 'application/json',
			data: JSON.stringify(dateplan),
			success: function(data){
				if($.isNumeric(data)){
					if(data==1){
						console.log("Success in updating date plan!");
					}else if(data==200){
						console.log("There was an error in updating your date plan.");
					}else
					{
						console.log("You must be logged in to update a date plan");
					}
				}else{
					console.log("Error...");
				}
			}
		});
}
//Test function
//Search by location, activity, or just general query
//Uses Myisam FULLTEXT indexing
//Returned results will be JSON Format that for each dateplan id there will be
//a field called "AssociatedActivities" that will be an array that holds the activities
//of that dateplan
//LOOK AT EXAMPLE BELOW TO SEE HOW TO USE
function searchDatabase(){
	var searchQuery = new Object();
	searchQuery.SearchQuery = $("#searchbar").val();8
	console.log("Activity results");
	//Searching Activities
	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchActivities',
		content: 'application/json',
		data: JSON.stringify(searchQuery),
		success: function(data){
			if($.isNumeric(data)){
                if(data==500){
                    console.log("No results found using the search query supplied.");
                }
            }else
            {
            	 var returnedSearch = jQuery.parseJSON(data);
            	 console.log("Activity Search Results: ");
            	 //Check to see if there are any activity results
            	 if(returnedSearch.hasOwnProperty("Activities")){
            	 	for(var i=0;i<returnedSearch.Activities.length;i++)
						console.log(returnedSearch.Activities[i]["Description"]);
            	 }
            }
		}
	});
	console.log("Dateplan results");
	//Searching Dateplans
	$.ajax({
		type: 'POST',
		url: 'api/index.php/searchDateplans',
		content: 'application/json',
		data: JSON.stringify(searchQuery),
		success: function(data){
			if($.isNumeric(data)){
                if(data==500){
                    console.log("No results found using the search query supplied.");
                }
            }else
            {
            	//console.log(data);
            	 var returnedSearch = jQuery.parseJSON(data);
            	 console.log("DatePlan Search Results: ");
            	 //check to see if there is any dateplan results
            	 if(returnedSearch.hasOwnProperty("DatePlans")){
            	 	for(var i=0;i<returnedSearch.DatePlans.length;i++){
						console.log(returnedSearch.DatePlans[i]["Description"]);
						console.log("Made of Activities: ");
						for(var j=0;j<returnedSearch.DatePlans[i].AssociatedActivities.length;j++)
							console.log(returnedSearch.DatePlans[i].AssociatedActivities[j].ActivityID);
            		}
            	 }
            }
		}
	});
}
	/*
	* Delete favorite
	* Call like this api/deleteFavorite/0/2
	* api/deleteFavorite/<this is either a 0 or a 1 to signify activitiy or dateplan deletion respectively>/id of dateplan or activity
	* op:0 = activity delete ; op:1 = dateplan delete
	*  
	*/
    $.ajax({
       type: 'DELETE',
       url: 'api/deleteFavorite/0/7',
		success: function(data){
			if($.isNumeric(data)){
                if(data==1){
                    console.log("Deleted success");
                }
                else{
                    console.log("Deleted failure");
                    }
            }else
            {
            	console.log("Error in deleteing favorite");
            }
		}
    });

	/*
	* Delete DateAcitivtyID by datePlanID & activityID
	* Call like this api/deleteDateActivity/datePlanID/activityID
	*/
    $.ajax({
       type: 'DELETE',
       url: 'api/deleteDateActivity/7/19',
		success: function(data){
			if($.isNumeric(data)){
                if(data==1){
                    console.log("Deleted success");
                }
                else{
                    console.log("Deleted failure");
                    }
            }else
            {
            	console.log("Error in deleteing favorite");
            }
		}
    });


//LOGIN
	var user = new Object();
	user.email = "test@test.edu";
	user.password = "password";
		$.ajax({
			type: 'POST',
			url: 'api/login',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});


//CREATE ACCOUNT
    var user = new Object();
    user.email = "test@test.edu";
    user.userName = "dont_test_me_bro";
    user.password = "password";
    user.fName = "Testy";
    user.lName = "Testerson";
    user.userType = 1;
    user.sex = 1;
    user.securityQuestion = 1;
    user.securityAnswer = "Fries";
        $.ajax({
            type: 'POST',
            url: 'api/createAccount',
            content: 'application/json',
            data: JSON.stringify(user),
            success: function(data){
                console.log(data);
            }
        });


//SUBMIT NEW ACTIVITY
	var activity = new Object();
	activity.Name = "Denny's";
	activity.Description = "Lovely pancakes and friendly staff!";
	activity.Cost = 15.00;
	activity.Location = "1234 Pancake Court";
	
		$.ajax({
			type: 'POST',
			url: 'api/submitNewActivity',
			content: 'application/json',
			data: JSON.stringify(activity),
			success: function(data){
				console.log(data);
			}
		});

//VIEW PROFILE

	var user = new Object();
	user.UserID = 1;
	console.log(user);
		$.ajax({
			type: 'POST',
			url: 'api/viewProfile',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});


//ADD FAVORITE


	var user = new Object();
	user.UserID = 1;
	user.ActivityID = 2;
	//user.DatePlanID = 2;
	console.log(user);
		$.ajax({
			type: 'POST',
			url: 'api/addFavorite',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});

//VIEW FAVORITES


	var user = new Object();
	user.UserID = 1;
	console.log(user);
		$.ajax({
			type: 'POST',
			url: 'api/index.php/viewFavorites',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});

//UPDATE ACCOUNT INFO

	var user = new Object();
	user.userID = 1;
	user.username = "newuser";
	user.fName = "NewName";
	user.lName = "NewNamerson";
	user.email = "newmail@mail.com";
	user.currentPassword = "oldpassword";//only required if email or newPassword is not NULL
	user.newPassword = "newpassword";
	console.log(user);
		$.ajax({
			type: 'POST',
			url: 'api/updateAccount',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});


//GET TOP TAGS

	//URL: http://localhost/dateright/api/topTags?num=5

	//num: the # of tags to return. Default = 10 (optional)

	$.ajax({
		type: 'GET',
		url: 'api/topTags?num=5',
		success: function(data){
			console.log(data);
		}
	});

//GET TAGGED ACTIVITIES

	var tag = new Object();

	// SUPPLY tagName XOR tagID
	tag.tagName = "TV";
	//tag.tagID = 1;

	//OPTIONALLY, SUPPLY num TO LIMIT RESULTS
	//tag.num=1;


	$.ajax({
		type: 'POST',
		url: 'api/getTaggedActivities',
		content: 'application/json',
		data: JSON.stringify(tag),
		success: function(data){
			console.log(data);
		}
	});

//GET TAGGED DATEPLANS

	var tag = new Object();

	// SUPPLY tagName XOR tagID
	//tag.tagName = "TV";
	tag.tagID = 1;

	//OPTIONALLY, SUPPLY num TO LIMIT RESULTS
	tag.num=1;


	$.ajax({
		type: 'POST',
		url: 'api/getTaggedDatePlans',
		content: 'application/json',
		data: JSON.stringify(tag),
		success: function(data){
			console.log(data);
		}
	});	

//Get random idea from Dateplans table
	$.ajax({
		type: 'GET',
		url: 'api/getRandomIdea',
		success: function(data){
			console.log(data);
		}
	});

//GET SESSION INFO

$.ajax({
		type: 'POST',
		url: 'api/getSessionInfo',
		success: function(data){
			console.log(data);
		}
	});

//SHARE DATE PLAN

	var date = new Object();
	date.userID = 1;
	date.datePlanID = 1;
	$.ajax({
		type: 'POST',
		url: 'api/shareDatePlan',
		content: 'application/json',
		data: JSON.stringify(date),
		success: function(data){
			console.log(data);
		}
	});


//RECOVERY QUESTION

    var user = new Object();
    user.email = "jdoe@gmail.com";
    $.ajax({
        type: 'POST',
        url: 'api/recoveryQuestion',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            console.log(data);
            JSON.stringify(data);
            var securityQuestion = jQuery.parseJSON(data).SecurityQuestion;
            console.log(securityQuestion);
        }
    });



//RECOVER PASSWORD

    var user = new Object();
    user.email = "jdoe@gmail.com";
    user.securityQuestion = 1;
    user.securityAnswer = "password";
    $.ajax({
        type: 'POST',
        url: 'api/recoverPassword',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            console.log(data);
        }
    });


//RESET PASSWORD
	
    var user = new Object();
    user.email = "jdoe@gmail.com";
    user.securityAnswer = "password";
    user.newPassword = "newPassword";
    $.ajax({
        type: 'POST',
        url: 'api/resetPassword',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            console.log(data);
        }
    });


//GET ASSOCIATED ACTIVITIES
    
    $.ajax({
        type: 'GET',
        url: 'api/index.php/getAssociatedActivities/2/1',
        //NOTE: 1st parameter: datePlanID, 2nd parameter: Always send 1 when making a GET request
        success: function(data){
            console.log(data);
        }
    });