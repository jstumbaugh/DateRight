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
        <meta name="description" content="DateRight- Plan the perfect dates to impress your special other or share your perfect date with all our users. ">
        <meta name="keywords" content="DateRight, Date, DatePlan, login, create"> 
        <link href="css/index.css" rel="stylesheet">
    </head>
<body>
<div class="right">
<div class="bubble"><h3>Date Idea</h3><p id="dateIdea" class="ellipsis"></p></div>
</div>
<img src="img/background.png" class="bg">
<p id="greeting"></p>

<!-- Search Bar HTML--> 
<div class="midSection">
<img src="img/Logo.png">
    <section id="searchBox">
        <form id="searchbar">
            <input type="text" name="datesearch" id="searchBoxText"  placeholder="Search..." />
            <input type="submit" value="Search" id="searchButton" class="searchButton">
        </form>
        <div id="searchResults"></div>
    </section>
</div>

<!-- The Login and create account buttons BEFORE they are pressed--> 
<div id="loginButtons" class="midSection">
    <a href="#" id="loginButton" class="button">Login </a>
    <a href="#" id="createAccountButton" class="button">Create Account!</a>
</div>
<!-- The overlay created when login button is pressed--> 
<section id="loginBox" class="overlayBox">
    <h2>Login</h2>
    <form method="post" class="minimal" id="login">
        <label for="email">
            Email:
            <input type="email" name="email" id="emailLogin" placeholder="Email Address" title="Valid email address required" required />
        </label>
        <label for="password">
            Password:
            <input type="password" name="password" id="passwordLogin" placeholder="Password" pattern=".{8,}" title="Password" required />
        </label>
        <button type="submit" class="btn-minimal" >Sign in</button> 
        <button type="button" class="btn-minimal" id = "ForgotPassword">Forgot password?</button>
        <p id="loginMessage"></p>
    </form>
</section>
<!-- Over lay created when Create Account is clicked--> 
<section id="createAccountBox" class="overlayBox">
    <h2>Create Account</h2>
    <form method="post" class="minimal" id="createAccountForm">
        <label for="userName">
            Username:
            <input type="text" name="userName" id="userName"  placeholder="Username" required />
        </label><br>
        <label for="fName">
            First:
            <input type="text" name="fNameAccount" id="fNameAccount" pattern="[a-zA-Z]+" title="Letters only" placeholder="First Name" required="required" />
        </label><br>
        <label for="lName">
            Last:
            <input type="text" name="lNameAccount" id="lNameAccount" pattern="[a-zA-Z]+" title="Letters only" placeholder="Last Name" required="required"/>
        </label><br>
        <label for = "email">
            Email:
            <input type = "text" id="emailAccount" email = "email" required="required" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"  placeholder = "YourEmail@email.com" required/>
        </label><br>
        <label for="password">
            Password:
            <input type="password" name="password" id="passwordAccount" placeholder="At least 8 characters." pattern="[a-zA-Z0-9!@#$%^*]{8,25}" required="required" />
        </label><br>
        <label for="security" id="securityQuest"> <p id="SecrueQuestP" style=" margin-top: -20px;  margin-bottom: -5px;  padding-right: 100px;"> Security Question: </p>   
            <select name="securityQuestion" id = "SecurityQuestion" placeholder= "Pick a Question!" required>
            <option value="1">Where was your first date?</option>
            <option value="2">What is the first name of the person you first kissed?</option>
            <option value="3">What is your mother's maiden name?</option>
            <option value="4">What is the name of your favorite pet?</option>
            <option value= "5"> What is the name of the street you grew up on? </option>
            </select>
        </label><br>
        <label for="securityAnsewer">
            <input type="text" name="SecurityAnswer" id="SecurityAnswer" pattern="[a-zA-Z0-9._%+-]+" placeholder="SecurityAnswer" required="required"/>
        </label><br>
        <button type="submit" class="btn-minimal" style = "margin-right: 105px;">Create Account!</button>
        <p id="resultMessage"></p>
    </form>
</section>

<!-- Recovery Box Div -->
<section id="ForgotPasswordBox" class="overlayBox">
    <h2>Enter Email to Start Reset Process</h2>
    <form method="post" class="minimal" id="forgotPassword">
        <label for="email">
            Email:
            <input type="email" name="email" id="emailforget" placeholder="Email Address" title="Valid email address required" required="required" />
        </label>
        <button type="submit" class="btn-minimal" id="ForgotPasswordButton">Get Question</button>
        <p id="forgotPasswordMessage"></p>
    </form>
</section>

<section id="PasswordRecoveryBox" class="overlayBox">
    <h2>Security Question</h2>
    <form method="post" class="minimal" id="forgotPassword">
         <p id="SecureQuestion"></p>
        <label for="securityAnsewer">
            Answer:
            <input type="text" name="securityAnswer" id="securityAnswer" pattern="[a-zA-Z0-9._%+-]+" placeholder="Security Ansewer" required="required"/>
        </label><br>
        <button type="submit" class="btn-minimal" id="PasswordRecoveryButton">Submit Answer</button>
        <p id="PasswordRecoveryMessage"></p>
    </form>
</section>

<!-- Reset Password Box Div -->
<section id="ResetPasswordBox" class="overlayBox">
    <h2>Reset Password</h2>
    <form method="post" class="minimal" id="forgotPassword">
        <label for="email">
            New Password
            <input type="password" name="newPassword" id="newPassword" placeholder="New Password" pattern=".{8,}" required="required"/>
        </label>
        <button type="submit" class="btn-minimal" id="ResetPasswordButton">Reset Password</button>
        <p id="ResetPasswordMessage"></p>
    </form>
</section>

<section id="SuccessBox" class="overlayBox">
    <h2>Password Reset Successful</h2>
</section>
<script src="js/index.js"></script>
<!-- <script src="js/test.js"></script> -->
</body>
</HTML>





