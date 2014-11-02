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

    inputName = $("#nameInput").attr("value");
    inputDescription = $("#descriptionInput").attr("value");
    inputRawCost = Number($("#costInput").attr("value"));
    cost = inputRawCost.toFixed(2);
    inputLocation = $("#locationInput").attr("value");
   
    //create activity object 
    newActivity = new Object();
    newActivity.Name = inputName;
    newActivity.Description = inputDescription;
    newActivity.Cost = cost;
    newActivity.Location = inputLocation;

    //create new activity
    $.ajax({
        type: 'POST',
        url: 'api/submitNewActivity',
        content: 'application/json',
        data: JSON.stringify(newActivity),
        success: function(data){
            console.log(data);
            if(data == '600'){
                alert('That activity already exists.');
            }
        }
    });
}