1/**
    Author: Konersmann
    Created: October 20, 2014
    Maintenance Log:
        10/27/2014 - still testing with input to recieve the correct data in viewProfile call

**/
$.cookie.json = true;

var profile = {};
var profileData = {"userID":"1"};

$.ajax({
    url: 'api/viewProfile',
    async: false, 
    dataType: 'json',
    data: JSON.stringify(profileData),
    success: function(response) {
        console.log(response);
        profile = response;
    }
});

console.log(profile);