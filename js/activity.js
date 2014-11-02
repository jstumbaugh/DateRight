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

    inputName = $("#nameInput").attr("value");
    inputDescription = $("#descriptionInput").attr("value");
    inputRawCost = Number($("#costInput").attr("value"));
    cost = inputRawCost.toFixed(2);
    inputLocation = $("#locationInput").attr("value");
    console.log(inputName);
    console.log(inputDescription);
    console.log(cost);
    console.log(inputLocation);
}