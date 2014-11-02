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
**/

$(document).ready(function(){
    $.cookie.json = true;
    loadUser();
    //$("#updateSubmitButton").click(submitUpdateForm);
    $("#updateAccountForm").submit(submitUpdateForm);
    $("#backButton").click(function(e){
        e.preventDefault();
        window.location.replace("search.html");
    });
});

/* Loads User data onto page 
   This is main function for the viewProfile api call
   Now works with session
*/
function loadUser(){
    var profile = {};
    var user = new Object();
    
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

    $("#pageHeader").text(profile.FirstName + "'s Profile");
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
    inputPassword = $("#passwordInput").attr("value");

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
    user.currentPassword = inputPassword;
    user.newPassword = inputPassword;
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
