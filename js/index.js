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

//Gets Random Idea for the Speech Button on the right
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

//Creates Account for user
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
    user.secruityQuestion = $("#SecurityQuestion").val();
    user.secruityAnsewer = $("#SecurityAnswer").val();
    user.userType = 0;
    user.sex = 0;
    //console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/index.php/createAccount',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            //Error Checking
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
//Search Button activites
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

//Login Function
$("#login").submit(function(event) {
    event.preventDefault(); 
    var user = new Object();
    user.email = $("#emailLogin").val();
    user.password = $("#passwordLogin").val();
    console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/index.php/login',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            //Error Checking
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

//Calls the random idea function and makes one for the user.
});

//Recovery Question function
/*
 $("#login").Button(function(event) {
    event.preventDefault(); 
    var user = new Object();
    user.email = $("#emailLogin").val();
    console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/index.php/recoveryQuestion',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            //Error Checking
            if($.isNumeric(data)){
                if(data==400){
                    $("#loginMessage").text("Inncorrect login information..Try typing your email again");
                }
                else{
                    $("#loginMessage").text("Error in transaction");
                    }
                
            }
            else if(!jQuery.isEmptyObject(data)){
                var obj = JSON.parse(data);
                if(obj.Email.length>0){
                    //create div 
                    var para = document.createElement("p");
                    para.setAttribute("class", "loginBox");
                    para.appendChild(node);
                    var element = document.getElementById("loginBox");
                    element.appendChild(para);
                    var y = document.createElement("INPUT");
                    y.setAttribute("type", "text");
                    y.setAttribute("placeholder", "Answer"); 
                    y.setAttribute("id", "SecurityAnswer");
                    y.setAttribute("SecurityAnswer", "SecurityAnswer");    
                    y.setAttribute("required", "required");
                    document.getElementById("logindiv").appendChild(y);
                    
                     var btn = document.createElement("INPUT");
                     btn.setAttribute("type", "Submit")
                     btn.setAttribute("class", "submitButton");
                     btn.setAttribute("onclick", "loginSubmit()");
                    document.getElementById("logindiv").appendChild(btn);
                }
            }
        }
    });
});
*/
getRandomIdea();
}
window.onload = init;
