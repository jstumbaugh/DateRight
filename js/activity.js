/**
    Author: Konersmann
    Created: 11/1/2014
    Maintenance Log:
        11/1/2014 - completed activity dialog and ajax call.
                    Can create a new activity now.
        11/3/2014 - modal dialog now closes on submit.
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
    
    inputName = $("#nameInput").val();
    inputDescription = $("#descriptionInput").val();
    inputRawCost = $("#costInput").val();
    inputLocation = $("#locationInput").val();
   
    //create activity object 
    newActivity = new Object();
    newActivity.Name = inputName;
    newActivity.Description = inputDescription;
    newActivity.Cost = inputRawCost;
    newActivity.Location = inputLocation;
    console.log(newActivity);

    //create new activity
    $.ajax({
        type: 'POST',
        url: 'api/index.php/submitNewActivity',
        content: 'application/json',
        data: JSON.stringify(newActivity),
        success: function(data){
            console.log(data);
            if(data == '600'){
                alert('That activity already exists.');
            }
        }
    });
    $("#openModal div a.close button").click();
}