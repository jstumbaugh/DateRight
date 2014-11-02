/**
    Author: Konersmann
    Created: 11/1/2014
*/

$(document).ready(function(){
    $.ajax({
        url: 'html/activity.html',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("body").prepend(data);
        }
    });
    $("div #createActivityForm").submit(createNewActivity);
})

function createNewActivity(event){
    event.preventDefault();
    console.log("stuff");

    name = $("#nameInput").attr("value");
    description = $("#descriptionInput").attr("value");
    rawCost = $("#costInput").attr("value");
    //cost = rawCost.toFixed(2);
    location = $("#locationInput").attr("value");
    console.log(name);
    console.log(description);
    console.log(cost);
    console.log(location);
}