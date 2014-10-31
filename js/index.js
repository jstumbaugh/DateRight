/*DDR Software */
function init() {
$("#loginBox").hide();
$("#createAccountBox").hide();
$("#loginButton").click(function(){
	$("#createAccountBox").hide();
	$("#loginBox").show();
        
}); 
$("#createAccountButton").click(function(){
        
    $("#createAccountBox").show();
    $("#loginBox").hide();
        
}); 
 function getRandomIdea() {

    var randomIdea="";
    //Get random idea from Dateplans table
    $.ajax({
        type: 'GET',
        url: 'api/index.php/getRandomIdea',
        success: function(data){
            var dataArray = jQuery.parseJSON(data);
            if(dataArray.length>0)
                $("#dateIdea").text(dataArray[0]["Description"]);
                
        }
    });
}


$('#createAccount').submit(function (event) {
    $("#loginBox").hide();
    $("#createAccountBox").show();
    event.preventDefault(); 
    var user = new Object();
    user.userName = $("#userName").val();
    user.fName = $("#fNameAccount").val();
    user.lName = $("#lNameAccount").val();
    user.email = $("#emailAccount").val();
    user.password = $("#passwordAccount").val();
    user.userType = 0;
    user.sex = 0;
    //console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/createAccount',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            if($.isNumeric(data)){
                if(data==100){
                     $("#resultMessage").text("Account already exists...");    
                }
                else{
                    $("#resultMessage").text("There was an error in your transaction. Try typing your info again");
                    }
                }
            else if(!jQuery.isEmptyObject(data)){
                var obj = JSON.parse(data);
                if(obj.Email.length>0){
                    $.cookie.json = true;
                    $.cookie("data", data); 
                    //redirect user
                    window.location.replace("search.html");
                }
            }
        }
    });
}); 

$('#searchbar').submit(function (e) {
e.preventDefault();
//Redirect user
window.location.replace("search.html?"+$("#searchbar").serialize());

/* CODE TO SEARCH ACTIVITIES */
// $.ajax({
//     type: "POST",
//     url: './api/index.php/searchActivities',
//     data: $(this).serialize(),
//     success: function(response) {
//         console.log(response);
//         var obj = $.parseJSON(response);
//         var resultDiv = "<div >"+"Search Results:"+"</div>";
//         $(resultDiv).appendTo("#searchResults");
//         if(response==500)
//             $("<div >"+"No results found"+"</div>").appendTo("#searchResults");
//         else{
//             for ( var i = 0; i < obj['results'].length; i++) {
//                 var a = obj['results'][i];
//                 var div_data = "<div >"+"Name: "+a['Name']+"------- Description: "+a['Description']+"</div>";
//                 $(div_data).appendTo("#searchResults");
                
//                 }
//             }
//         }
//     });


}); 

$("#login").submit(function(event) {
    event.preventDefault(); 
    var user = new Object();
    user.email = $("#emailLogin").val();
    user.password = $("#passwordLogin").val();
    console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/login',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            //Status code
            if($.isNumeric(data)){
                if(data==400){
                    $("#loginMessage").text("Inncorrect login information..Try typing your password again");
                }
                else{
                    $("#loginMessage").text("Error in transaction");
                    }
                
            }
            else if(!jQuery.isEmptyObject(data)){
                var obj = JSON.parse(data);
                if(obj.Email.length>0){
                    $.cookie.json = true;
                    $.cookie("data", data); 
                    //redirect user
                    window.location.replace("search.html");
                }
            }
        }
    });

    // var info = $.cookie("data");
    // if (info.indexOf("email") >= 0)
    // {
    //     //HERE"S WHERE WE LINK TO ANOTHER PAGE
    //     self.location='search.html'
    //     document.getElementById("createbut").setAttribute("class", "inv");
    //     document.getElementById("loginbut").setAttribute("id", "logout");
    //     document.getElementById("logout").innerHTML = "Logout";
    //     document.getElementById("logout").setAttribute("onclick", "");
    //     document.getElementById("greeting").innerHTML = "Hello, " + JSON.parse(info).fName;
    // }

    });
getRandomIdea();
}
window.onload = init;
