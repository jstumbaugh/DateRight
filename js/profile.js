/**
    Author: Konersmann
    Created: October 20, 2014
    Maintenance Log:
        10/27/2014 - still testing with input to recieve the correct data 
                        in viewProfile call.
        10/28/2014 - added jquery instead of just bare naked scripts. Created
                        the loadUser function.
**/

$(document).ready(function(){
    $.cookie.json = true;
    loadUser();
    //$("#updateSubmitButton").click(submitUpdateForm);
    $("#updateAccountForm").submit(submitUpdateForm);

});

function loadUser(){
    var profile = {};
    var user = new Object();
    user.UserID = 1;

    $.ajax({
        type: 'POST',
        url: 'api/viewProfile',
        async: false, 
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(response) {
            //error checking
            try {
                profile = JSON.parse(response)
            }
            catch (e) {
                console.log(e);
                profile.FirstName = "NULL";
                profile.LastName = "NULL";
                profile.Email = "NULL";
            }
        }
    });

    console.log(profile);

    //Change static page view variables

    $("#pageHeader").text(profile.FirstName + "'s Profile");
    $("#username").text("Username: none? where ma username at");
    $("#firstName").text("First Name: " + profile.FirstName);
    $("#lastName").text("Last Name: " + profile.LastName);
    $("#email").text("Email: " + profile.Email);

    //Change Modal Dialog variables

    $("#usernameInput").attr("value", "Where ma username at?");
    $("#firstNameInput").attr("value", profile.FirstName);
    $("#lastNameInput").attr("value", profile.LastName);
    $("#emailInput").attr("value", profile.Email);
    $("#passwordInput").attr("placeholder", "Verify Password");

}

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
    user.userID = 1;

    user.username = inputUsername;
    user.fName = inputFirstName;
    user.lName = inputLastName;
    user.email = inputEmail;
    user.currentPassword = inputPassword;
    user.newPassword = inputPassword;
    console.log(user);

    //api call
    $.ajax({
        type: 'POST',
        url: 'api/updateAccount',
        async: false,
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(returnMessage){
            console.log(returnMessage);
        }
    });

    $("#openModal div a.close button").click();
    loadUser();

}
