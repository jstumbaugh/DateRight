var x, fadeBackground;
var para;

//The Growing Circle and the forms that appear
function LoginFunction() {
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
	$("#creatediv").addClass( "grownc", 1000);
	$("#createbut").addClass("shrinkc", 0);
	//$("#createbut").addClass( "grownc", 1000);

	if (para == null){
	//First Name
	/*var para = document.createElement("p");
	para.setAttribute("class", "createAccountText");
	var node = document.createTextNode("Enter your First Name Here");
	para.appendChild(node);
	var element = document.getElementById("creatediv");
	element.appendChild(para);
    var f = document.createElement("INPUT");
	f.setAttribute("type", "text");
	f.setAttribute("placeholder", "First Name"); 
	f.setAttribute("id", "fNameAccount");
	f.setAttribute("fName", "fName");    
	f.setAttribute("required", "required");
    document.getElementById("creatediv").appendChild(f);

    //Last Name
    var para = document.createElement("p");
	para.setAttribute("class", "createAccountText");
	var node = document.createTextNode("Enter your Last Name Here");
	para.appendChild(node);
	var element = document.getElementById("creatediv");
	element.appendChild(para);
    var l = document.createElement("INPUT");
	l.setAttribute("type", "text");
	l.setAttribute("placeholder", "Last Name"); 
	l.setAttribute("id", "lNameAccount");
	l.setAttribute("fName", "lName");    
	l.setAttribute("required", "required");
    document.getElementById("creatediv").appendChild(l);

	//Email Login Form
	var para = document.createElement("p");
	para.setAttribute("class", "createAccountText");
	var node = document.createTextNode("Enter your Email Here");
	para.appendChild(node);
	var element = document.getElementById("creatediv");
	element.appendChild(para);
    var y = document.createElement("INPUT");
	y.setAttribute("type", "text");
	y.setAttribute("placeholder", "Email"); 
	y.setAttribute("id", "emailAccount");
	y.setAttribute("email", "email");    
	y.setAttribute("required", "required");
    document.getElementById("creatediv").appendChild(y);

    //Password Form
    var para = document.createElement("p");
	para.setAttribute("class", "createAccountText");
	var node = document.createTextNode("Enter your Password Here");
	para.appendChild(node);
	var element = document.getElementById("creatediv");
	element.appendChild(para);
    var z = document.createElement("INPUT");
	z.setAttribute("type", "password");
	z.setAttribute("placeholder", "Password must be 8 characters"); 
	z.setAttribute("id", "passwordAccount");
	z.setAttribute("password", "password");    
	z.setAttribute("required", "required");
    document.getElementById("creatediv").appendChild(z);

    var para = document.createElement("p");
	para.setAttribute("class", "createAccountText");
	var node = document.createTextNode("Repeat Password");
	para.appendChild(node);
	var element = document.getElementById("creatediv");
	element.appendChild(para);
    var z = document.createElement("INPUT");
	z.setAttribute("type", "password");
	z.setAttribute("placeholder", "Password"); 
	z.setAttribute("id", "passwordAccount");
	z.setAttribute("password", "Repeat the same password");    
	z.setAttribute("required", "required");
    document.getElementById("creatediv").appendChild(z);

    //Create Submit Button
    var btn = document.createElement("INPUT");
    btn.setAttribute("type", "Submit")
    btn.setAttribute("class", "submitButton");
    btn.setAttribute("onclick", "accountSubmit()");
    document.getElementById("creatediv").appendChild(btn);
*/
	}
	else{
		console.log("Else statment!");
	}
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
