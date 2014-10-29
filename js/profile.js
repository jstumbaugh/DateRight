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
    //$("#updateAccountButton").click();

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
            profile = JSON.parse(response);
            //profile = response;
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

}

