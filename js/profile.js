/**
    Author: Konersmann
    Created: October 20, 2014
    Maintenance Log:
        10/27/2014 - still testing with input to recieve the correct data in viewProfile call

**/
$.cookie.json = true;

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
        profile = response;
    }
});

console.log(profile);