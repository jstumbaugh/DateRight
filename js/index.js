/*DDR Software */
function init() {
$("#loginBox").hide();
$("#createAccountBox").hide();
$("#ForgotPasswordBox").hide();
$("#PasswordRecoveryBox").hide();
$("#ResetPasswordBox").hide();
$("#loginButton").click(function(){
	$("#createAccountBox").hide();
	$("#loginBox").show();
   $("#ForgotPasswordBox").hide();
    $("#PasswordRecover").hide();
        
}); 
$("#createAccountButton").click(function(){
        
    $("#createAccountBox").show();
    $("#loginBox").hide();
     $("#ForgotPasswordBox").hide();
    $("#PasswordRecover").hide();    
}); 
$("#ForgotPassword").click(function(){
        
    $("#ForgotPasswordBox").show();
    $("#loginBox").hide();
        
}); 

$("#ForgotPasswordButton").click(forgotPassword);

$("#PasswordRecoveryButton").click(SecurityAnsewer);


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
    user.securityQuestion = $("#SecurityQuestion").val();
    user.securityAnsewer = $("#SecurityAnswer").val();
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
function forgotPassword(){
    event.preventDefault(); 

    var user = new Object();
    user.email = $("#emailforget").val();
    console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/index.php/recoveryQuestion',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            console.log(data);
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
                //if(obj.email.length>0){
                    //create div 
                $("#PasswordRecoveryBox").show();
                $("#ForgotPasswordBox").hide();
            securityQuestion = parseInt(obj.SecurityQuestion);
            console.log(securityQuestion);
            switch(securityQuestion){
                case 1:
                     $("#SecureQuestion").text("Where was your first date?");
                     break;
                case 2:
                     $("#SecureQuestion").text("What is the first name of the person you first kissed?");
                     break;
                case 3:
                     $("#SecureQuestion").text("What is your mother's maiden name?");
                     break;
                 case 4:
                     $("#SecureQuestion").text("What is the name of your favorite pet?");
                     break;
                 case 5:
                     $("#SecureQuestion").text("What is the name of the street you grew up on?");
                     break;
                 default:
                     $("#SecureQuestion").text("Something went wrong!");
                 }
              // }
            }
        }
    });
}
//Security Question Answering 
function SecurityAnsewer(){

    event.preventDefault(); 
    var user = new Object();
    user.email = $("#emailforget").val();
    user.securityAnsewer = $("#securityAnswer").val();
    console.log(JSON.stringify(user));
    $.ajax({
       type: "POST",
        url: 'api/index.php/recoverPassword',
        content: 'application/json',
        data: JSON.stringify(user),
        success: function(data){
            //Error Checking
            if($.isNumeric(data)){
                if(data==400){
                    $("#loginMessage").text("Inncorrect ansewer..Try again");
                }
                else{
                    $("#loginMessage").text("Error in transaction");
                    }
                
            }
            else if(!jQuery.isEmptyObject(data)){
                var obj = JSON.parse(data);
                    //create div 
                  $("#PasswordRecoveryBox").hide();
                  $("#ResetPasswordBox").show();
                
            }
        }
    });
}
//Password Reseting

getRandomIdea();
}
window.onload = init;
