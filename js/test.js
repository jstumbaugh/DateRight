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
	user.creditCardNumber = "1234123412341234";
	user.creditProvider = "Visa";
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

//VIEW FAVORITES


	var user = new Object();
	user.UserID = 1;
	console.log(user);
		$.ajax({
			type: 'POST',
			url: 'api/viewFavorites',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});

//UPDATE ACCOUNT INFO

	var user = new Object();
	user.userID = 1;
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

