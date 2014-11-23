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
        11/21/2014 - Can view content such as Date Plans, Date Plan Reviews, or
                        Activity Reviews. No actual Date Plan functionality... yet.
        11/22/2014 - Can change profile picture. Also, hovering over profile pic 
                        prompts user to change it. Good stuff.
**/

$(document).ready(function(){
    $.cookie.json = true;
    loadUser();

    //call function to add Date Plans to the page
    showUserDatePlans();
    //call function to add the date plan reviews to the page
    addDatePlanReviews();
    //call function to add the activity reviews to the page
    addActivityReviews();

    //$("#updateSubmitButton").click(submitUpdateForm);
    $("#updateAccountForm").submit(submitUpdateForm);

    /**
    *    upload profile pic
    */
    $("#updateProfilePic").submit(function(e){
        $.ajax({
            url: 'api/index.php/addPhoto',
            type: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false
        });
        e.preventDefault();
        loadProfilePic();
        $("#openModalTwo div a.close button").click();
    });
    
    $("#homeButton").click(function(e){
        e.preventDefault();
        $(location).attr('href', "search.html");
    });
    $("#selectionMenuBar #datePlanA").click(menuSelection);
    $("#selectionMenuBar #datePlanReviewsA").click(menuSelection);
    $("#selectionMenuBar #activityReviewsA").click(menuSelection);

    /**
    $('#logoutbut').click(function(e){
        e.preventDefault();
        logout();
    });
    */
});

function menuSelection(){

    if($(this).parent().attr("class") === "selected"){
        deselectMenuAndContent();
    } else {
        deselectMenuAndContent();
        $(this).parent().attr("class", "selected");
        if(this === $("#datePlanA")[0]) {
            //show content for date plans
            $("#datePlans").attr("class", "contentWrapperDiv");
        } else if(this === $("#datePlanReviewsA")[0]) {
            //show content for date plan reviews
            $("#datePlanReviews").attr("class", "contentWrapperDiv");
        } else if(this === $("#activityReviewsA")[0]) {
            //show content for activty reviews
            $("#activityReviews").attr("class", "contentWrapperDiv");
        } else {
            console.log("wut");
        }
    }
}

function deselectMenuAndContent() {
    for (i = 0; i < 3; i++) {
            //unselect Menu
            $($("#selectionMenuBar").children().children()[i]).attr("class", "");
        }
        for(i = 0; i < 3; i++) {
            //make all content disappear 
            $($("#displaySection").children()[i]).attr("class", "hideThis");
        }
}

/**
*   Add Date Plans to the page
*/
function showUserDatePlans(){
    if(userDatePlans[0] === undefined) {
        $("#displaySection #datePlans").append($("<p></p>").text("You have created any Date Plans yet."));
    } else {

        for(x in userDatePlans) {

            title = $("<p></p>").text("Date Plan Title: " + userDatePlans[x].Name);
            share = $("<button></button").text("Share?");
            share.attr("class", "shareButton");
            share.click(function(){
                $(this).attr("class", "hideThis");
            });
            creator = $("<p></p>").text("Creator: You OR someone else");
            modify = $("<p></p>").text("Modified by: You OR someone else... Modify button?");
            timeStamp = $("<p></p>").text("Date and time created/modified: " + userDatePlans[x].Timestamp);
            description = $("<p></p>").text("Description: " + userDatePlans[x].Description);
            $("#displaySection #datePlans").append(title, share, creator, modify, timeStamp, description);
            //keep it hidden
            $("#displaySection #datePlans").attr("class", "hideThis");
        }
    }
    //sharing
}

/**
*   Add date plan reviews to the page
*/
function addDatePlanReviews() {
    if(datePlanReviews[0] === undefined) {
        $("#displaySection #datePlanReviews").append($("<p></p>").text("You have not reviewed any Date Plans yet."));
    } else {

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
            $("#displaySection #datePlanReviews").append(title, type, rating, attended, timeStamp, description);
        }
    }
    //keep it hidden
    $("#displaySection #datePlanReviews").attr("class", "hideThis");
}

/**
*   Add activity reviews to the page
*/
function addActivityReviews() {
    if(activityReviews[0] === undefined) {
        $("#displaySection #activityReviews").append($("<p></p>").text("You have not reviewed any activities yet."));
    } else {

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
            $("#displaySection #activityReviews").append(title, type, rating, attended, timeStamp, description);
        }
    }
    //keep it hidden
    $("#displaySection #activityReviews").attr("class", "hideThis");
}

/* Loads User data onto page 
   This is main function for the viewProfile api call
   Now works with session
*/
function loadUser(){
    var profile = {};
    user = new Object();
    
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
                //console.log("No Results for Date Plan Reviews");
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
                //console.log("No Results for Activity Reviews");
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

    userDatePlans = [];
    $.ajax({
        type: 'POST',
        url: 'api/index.php/viewUserDatePlans',
        async: false, 
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(response) {
            //error checking
            if(response === "500"){
                console.log("No Results for Date Plans or incorrect json formatting");
            }
            else {
                try {
                    userDatePlans = JSON.parse(response)[0];
                }
                catch (e) {
                    console.log(e);
                }
                console.log(userDatePlans);
            }
        }
    });

    loadProfilePic();
}

/**
*   Loads Profile Picture
*/
function loadProfilePic(){
    profilePic = "";
    $.ajax({
        type: 'GET',
        url: 'api/index.php/getPhoto/'+user.UserID,
        async: false, 
        success: function(response) {
            console.log(response);
            //error checking
            if(response === "1100"){
                console.log("Profile pic upload error");
            }
            else if(response === "500"){
                //do stuff
            } else {
                //grab file name
                profilePic = response;
                //console.log(profilePic);
                $("#profilePic").attr("src", "img/user/"+profilePic+"?"+ new Date().getTime());
            }
        }
    });

    //set profile pic update hidden input
    $("#superSecretProfilePicValue").attr("value", user.UserID);
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