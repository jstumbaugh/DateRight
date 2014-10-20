/**
    Author: Konersmann
    Created: October 20, 2014
    Maintenance Log:

**/
$.cookie.json = true;

var profile = {};
var profileData = {"userID":"0"};

$.ajax({
    url: 'api/getProfile',
    async: false, 
    dataType: 'json',
    data: JSON.stringify(profileData),
    success: function(response) {
        profile = response;
    }
});