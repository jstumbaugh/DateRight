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
	user.password = "password";
	user.fName = "Testy";
	user.lName = "Testerson";
	user.userType = 1;
	user.sex = 1;
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

	//URL example1: http://localhost/dateright/api/getTaggedActivities?tagID=1
	//URL example2: http://localhost/dateright/api/getTaggedActivities?tagName=Movies
	//URL example3: http://localhost/dateright/api/getTaggedActivities?tagID=1&num=3

	//Either tagID OR tagName must be specified for SQL query to execute

	//num:  the # of activites to return. Default returns all tagged activities (optional)


	$.ajax({
		type: 'GET',
		url: 'api/getTaggedActivities?tagID=1',
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




