/**
    Author: Konersmann
    Created: October 20, 2014
    Maintenance Log:
        10/27/2014 - still testing with input to recieve the correct data 
                        in viewProfile call.
        10/28/2014 - added jquery instead of just bare naked scripts. Created
                        the loadUser function.
        11/1/2014 - both load user and submit update check session for UserID
        11/2/2014 - Added error checking to the ajax call in the updateAccount 
                    function. Also added functionality for a back button to direct
                    a user to the search page.
        11/17/2014 - User now have buttons to view Date Plans and Activities from.
                    - update Account info takes multiple fields, can modify any 
                        part of account, but needs the old password to work.
**/

$(document).ready(function(){
    $.cookie.json = true;
    loadUser();
    //$("#updateSubmitButton").click(submitUpdateForm);
    $("#updateAccountForm").submit(submitUpdateForm);
    $("#homeButton").click(function(e){
        e.preventDefault();
        $(location).attr('href', "search.html");
    });
    $("#datePlanButton").click(showUserDatePlans);
    $("#reviewsButton").click(showUserReviews);

    /**
    $('#logoutbut').click(function(e){
        e.preventDefault();
        logout();
    });
    */
});

function showUserDatePlans(){
    // ajac call on click??
    /**
        Expand and Retract info
    */
    if($(this).children().length === 0){
            title = $("<p></p>").text("Date Plan Title");
            share = $("<p></p>").text("Share?");
            creator = $("<p></p>").text("Creator: You OR someone else");
            modify = $("<p></p>").text("Modified by: You OR someone else... Modify button?");
            timeStamp = $("<p></p>").text("Date and time created/modified: October 1st, 2014 at 12:21");
            description = $("<p></p>").text("Description: A bunch of stuff about this date plan.");
            $(this).append(title, share, creator, modify, timeStamp, description);
    }
    else {
        $(this).empty();
        $(this).text("View Your Date Plans");
    }
}

function showUserReviews(){
    if($(this).children().length === 0){
        //check if data was empty
        if(datePlanReviews[0] === undefined && activityReviews[0] === undefined) {
            //no reviews at all
            $(this).append($("<p></p>").text("You have not reviewed anything on DateRight yet."));
        } else if(datePlanReviews[0] === undefined) {
            $(this).append($("<p></p>").text("You have not reviewed any date plans yet."));
            //add activityReview stuff
            addActivityReviews(this);
        } else if(activityReviews[0] === undefined) {
            $(this).append($("<p></p>").text("You have not reviewed any activities yet."));
            //add datePlanReview stuff
            addDatePlanReviews(this);
        } else {
            //add everything
            addDatePlanReviews(this);
            addActivityReviews(this);
        }
    }
    else {
        $(this).empty();
        $(this).text("View Your Reviews");
    }
}

function addDatePlanReviews(tag) {

    for(x in datePlanReviews){

        if(datePlanReviews[x].Attended === 1) {
            att = "Yes";
        } else {
            att = "No";
        }
        title = $("<p></p>").text("Review Title");
        type = $("<p></p>").text("Date Plan Review");
        rating = $("<p></p>").text("Rating: " + datePlanReviews[x].Rating);
        attended = $("<p></p>").text("Attended? " + att);
        timeStamp = $("<p></p>").text("Date and time created: " + datePlanReviews[x].ReviewTime);
        description = $("<p></p>").text("Description: " + datePlanReviews[x].Description);
        $(tag).append(title, type, rating, attended, timeStamp, description);
    }
}

function addActivityReviews(tag) {

    for(x in activityReviews){
        //initialize activity reviews
        if(activityReviews[x].Attended === 1) {
            att = "Yes";
        } else {
            att = "No";
        }
        title = $("<p></p>").text("Review Title");
        type = $("<p></p>").text("Activity Review");
        rating = $("<p></p>").text("Rating: " + activityReviews[x].Rating);
        attended = $("<p></p>").text("Attended? " + att);
        timeStamp = $("<p></p>").text("Date and time created: " + activityReviews[x].ReviewTime);
        description = $("<p></p>").text("Description: " + activityReviews[x].Description);
        //add activity reviews to the page
        $(tag).append(title, type, rating, attended, timeStamp, description);
    }
}

/* Loads User data onto page 
   This is main function for the viewProfile api call
   Now works with session
*/
function loadUser(){
    var profile = {};
    var user = new Object();
    
    //get userID
    var sessionData = {};
    $.ajax({
        type: 'POST',
        async: false,
        url: 'api/index.php/getSessionInfo',
        success: function(data){
            //console.log(data);
            sessionData = JSON.parse(data);
        }
    });
    // viewProfile uses 'UserID', session uses 'UserID'
    user.UserID = sessionData.UserID;
    //user.UserID = 1;
    //console.log(user);

    $.ajax({
        type: 'POST',
        url: 'api/index.php/viewProfile',
        async: false, 
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(response) {
            //error checking
            try {
                profile = JSON.parse(response);
            }
            catch (e) {
                console.log(e);
                profile.FirstName = "NULL";
                profile.LastName = "NULL";
                profile.Email = "NULL";
                profile.UserName = "NULL";
            }
        }
    });

    console.log(profile);

    //Change static page view variables

    $("#pageHeader").text(profile.UserName + "'s Profile");
    $("#username").text("Username: " + profile.UserName);
    $("#firstName").text("First Name: " + profile.FirstName);
    $("#lastName").text("Last Name: " + profile.LastName);
    $("#email").text("Email: " + profile.Email);

    //Change Modal Dialog variables

    $("#usernameInput").attr("value", profile.UserName);
    $("#firstNameInput").attr("value", profile.FirstName);
    $("#lastNameInput").attr("value", profile.LastName);
    $("#emailInput").attr("value", profile.Email);
    $("#passwordInput").attr("placeholder", "Verify Password");

    /**
        Get Date Plans and Review information
    */

    datePlanReviews = new Object();
    $.ajax({
        type: 'POST',
        url: 'api/index.php/viewDatePlanReviews',
        async: false, 
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(response) {
            //error checking
            if(response === "500"){
                console.log("No Results for Date Plan Reviews");
            }
            else {
                try {
                    datePlanReviews = JSON.parse(response);
                }
                catch (e) {
                    console.log(e);
                }
                console.log(datePlanReviews);
            }
        }
    });

    activityReviews = new Object();
    $.ajax({
        type: 'POST',
        url: 'api/index.php/viewActivityReviews',
        async: false, 
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(response) {
            //error checking
            if(response === "500"){
                console.log("No Results for Activity Reviews");
            }
            else {
                try {
                    activityReviews = JSON.parse(response);
                }
                catch (e) {
                    console.log(e);
                }
                console.log(activityReviews);
            }
        }
    });

}

/*  Update User Account Info 
    main funciton which uses updateAccount api call
*/
function submitUpdateForm (event) {
    event.preventDefault();

    //event.target.setCustomValidity("Stuff");

    inputUsername = $("#usernameInput").attr("value");
    inputFirstName = $("#firstNameInput").attr("value");
    inputLastName = $("#lastNameInput").attr("value");
    inputEmail = $("#emailInput").attr("value");
    inputOldPassword = $("#oldPasswordInput").attr("value");
    inputNewPassword = $("#newPasswordInput").attr("value");

    user = new Object();

    //get userID
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
    // updateAccount uses 'userID', session uses 'UserID'
    user.userID = sessionData.UserID;

    user.username = inputUsername;
    user.fName = inputFirstName;
    user.lName = inputLastName;
    user.email = inputEmail;
    user.currentPassword = inputOldPassword;
    user.newPassword = inputNewPassword;
    //console.log(user);

    //api call
    $.ajax({
        type: 'POST',
        url: 'api/index.php/updateAccount',
        async: false,
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            console.log(data);
            //700 is the UserName already exists/is taken error
            if(data == 700) {
                alert("That username is already taken. Try again.");
            }
            //800 is the email already exists/is taken error
            else if(data == 800) {
                alert("That email is already taken. Try again.");
            }
            else {
                $("#openModal div a.close button").click();
                loadUser();
            }
        }
    });

}

/**
function logout(){
    $.ajax({
        type: "POST",
        url: 'api/index.php/logout',
        content: 'application/jsonajax',
        success: function(data){
            if(data){
                //redirect user to homepage after successful logout
                window.location.replace("index.php");
            }
        },
        error: function(){
            console.log('Unable to logout');
        }
    });
}
*/