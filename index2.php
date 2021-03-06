<!DOCTYPE html>
<HTML lang = "en">
    <head>
        <title>
            DateRight Login
        </title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <script src="js/jquery.cookie.js"></script>
        <meta name="description" content="DateRight">
        <meta name="keywords" content="DateRight, Date, DatePlan, login, create"> 
        <link href="css/index.css" rel="stylesheet">
    </head>
<body>
<img src="img/background.png" class="bg">
<img src="img/Logo.png" class = "logo">
<p id="greeting"></p>
<button id="createbut" class="" onclick="createAccount()"><span>Create Account<span></button>
<button id="loginbut" onclick="LoginFunction()"><span>Login</span></button>
<div id="logindiv"><span>Login</span></div>
<div id="fade"></div>
<a id="make" href="orders.html">Make a Random DatePlan</a>
  

<dialog id = "createAccount">
    <form id = "createAccountForm" onsubmit="accountSubmit()">
        <button type="button" id="close" onclick="closeWindow()">X</button>
        <ol> 
            <li> <label for = "fName">First Name:</label><input type = "text" id="fNameAccount" fName = "fName" required="required"/> </li>
            <li> <label for = "lName">Last Name:</label><input type = "text" id="lNameAccount" password = "lName" required="required"/> </li>
            <li> <label for = "email">Email:</label><input type = "text" id="emailAccount" email = "email" required="required" pattern="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum|ninja)\b"/> </li>
            <li> <label for = "password">Password:</label><input type = "password" id="passwordAccount" password = "password" required="required"/> </li>
            <li> <label for = "repeatPassword">Repeat Password:</label><input type = "password" id="passwordAccount" repeatPassword = "repeatPassword" required="required"/> </li>
        </ol>
        <button type="submit" id="submitaccount" onclick="createAccount()">Submit</button>
    </form>
</dialog>

<dialog id = "login">
    <form id = "loginForm" onsubmit="loginSubmit()">
        <button type="button" id="close" onclick="closeWindow()">X</button>
        <ol>
            <li> <label for = "email">Email:</label><input type = "text" id="emailLogin" email = "email" required="required"/> </li>
            <li> <label for = "password">Password:</label><input type = "password" id="passwordLogin" password = "password" required="required"/> </li>
        </ol>
        <button type="submit" id="submitlogin">Submit</button>
    </form>
</dialog>

<script src="js/index.js"></script>

</body>
</HTML>





