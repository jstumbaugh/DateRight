var x, fadeBackground;
var para;
//The Dialog boxes
function loginFunction() {
    x = document.getElementById("login");
    x.open = true;
    fadeBackground = document.getElementById("fade");
	fadeBackground.classList.add("fadeIn");
}

function testLoginFunction() {
	$("#logindiv").addClass( "grownl", 1000);
	$("#loginbut").addClass("shrinkl", 0);
	if (para == null){

	//Email Login Form
	var para = document.createElement("p");
	para.setAttribute("class", "loginText");
	var node = document.createTextNode("Enter your Email Here");
	para.appendChild(node);
	var element = document.getElementById("logindiv");
	element.appendChild(para);
    var y = document.createElement("INPUT");
	y.setAttribute("type", "text");
	y.setAttribute("placeholder", "Email"); 
	y.setAttribute("id", "emailLogin");
	y.setAttribute("email", "email");    
	y.setAttribute("required", "required");
    document.getElementById("logindiv").appendChild(y);

    //Password Form
    var para = document.createElement("p");
	para.setAttribute("class", "loginText");
	var node = document.createTextNode("Enter your Password Here");
	para.appendChild(node);
	var element = document.getElementById("logindiv");
	element.appendChild(para);
    var z = document.createElement("INPUT");
	z.setAttribute("type", "password");
	z.setAttribute("placeholder", "Password"); 
	z.setAttribute("id", "passwordLogin");
	z.setAttribute("password", "password");    
	z.setAttribute("required", "required");
    document.getElementById("logindiv").appendChild(z);

    //Create Submit Button
    var btn = document.createElement("INPUT");
    btn.setAttribute("type", "Submit")
    btn.setAttribute("class", "submitButton");
    btn.setAttribute("onclick", "loginSubmit()");
    document.getElementById("logindiv").appendChild(btn);

	}
	else{
		console.log("Else statment!");
	}
}

function createAccount(){
	$("#createbut").addClass( "grownc", 1000);
}

//Close the Dialog Boxes
function closeWindow() { 
	x.open = false;
	fadeBackground.className = "";
} 

//How to login to our website
function loginSubmit() {
	event.preventDefault(); 
	var user = new Object();
	user.email = $("#emailLogin").val()
	user.password = $("#passwordLogin").val()
	console.log(user);

	$.ajax({
		type: 'POST',
		url: 'api/login',
		async: false,
		content: 'application/json',
		data: JSON.stringify(user),
		success: function(data){
			console.log(data);
			var obj = JSON.parse(data);
			if(obj.error == true) {
				$("#loginModal").css({"border":"2px solid red"});
				$(".errorMessage").text("silly, your login information is not correct");
			}
			else {
				console.log("loading cookie");
				/* Store user information in cookie */ 
				$.cookie.json = true;
				$.cookie("data", data);	
				console.log($.cookie("data"))
			}
		}
	});

	document.getElementById("loginForm").reset();
	//closeWindow(); 

	var info = $.cookie("data");
	if (info.indexOf("email") >= 0)
	{
		//HERE"S WHERE WE LINK TO ANOTHER PAGE
		location.assign='search.html'
		//self.location='search.html'
	}
}

//How to create an account on our website
function accountSubmit() {
	event.preventDefault();		
	console.log("It Worked!!!");
	var user = new Object();
	user.fName = $("#fNameAccount").val();
	user.lName= $("#lNameAccount").val();
	user.email = $("#emailAccount").val(); 
	user.password = $("#passwordAccount").val();
    user.userType =0;
    user.sex = 0;

	$.ajax({
		type: 'POST',
		url: 'api/index.php/createAccount',
		content: 'application/jsonajax',
		data: JSON.stringify(user),
		success: function(data){
			//Account already exists
			if(data===100){
				//CREATE DIV HERE TO DISPLAY MESSAGE
				console.log("Account already exists");
			}else if(data===200){
				//CREATE DIV HERE TO DISPLAY MESSAGE
				console.log("JSON error");
			}else{
				//CREATE DIV HERE TO DISPLAY MESSAGE
				console.log(data);
			}
		}
	});

	document.getElementById("createAccountForm").reset();
	closeWindow(); 
}

$(document).on("click", "#logout", function(){
		event.preventDefault();		
		console.log("Logging out!");

		$.ajax({
			type: 'POST',
			url: 'api/index.php/logout',
			content: 'application/jsonajax',
			success: function(){
				console.log("You've been logged out!");
			}
		});

		document.getElementById("createbut").setAttribute("class", "accountb");
		document.getElementById("logout").innerHTML = "Login";
		document.getElementById("logout").setAttribute("onclick", "loginfunction()");
		document.getElementById("logout").setAttribute("id", "loginbut");
		document.getElementById("loginbut").setAttribute("class", "accountb");
		document.getElementById("greeting").innerHTML = "";
});


	var user = new Object();
	user.UserID = 1;
	console.log(user);
		$.ajax({
			type: 'POST',
			url: 'api/viewProfile',
			content: 'application/json',
			data: JSON.stringify(user),
			success: function(data){
				console.log(data);
			}
		});

//POST REQUEST TEST CODE
	// var user = new Object();
	// user.email = "test@test.edu";
	// user.password = "password";
	// user.fName = "Testy";
	// user.lName = "Testerson";
	// user.creditCardNumber = "1234123412341234";
	// user.creditProvider = "Visa";
	// 	$.ajax({
	// 		type: 'POST',
	// 		url: 'api/createAccount',
	// 		content: 'application/json',
	// 		data: JSON.stringify(user),
	// 		success: function(data){
	// 			console.log(data);
	// 		}
	// 	});
