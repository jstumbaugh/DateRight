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
        <link href="css/index2.css" rel="stylesheet">
    </head>
<body>
<div class="right">
<div class="bubble"><h3>Date Idea</h3><p id="dateIdea" class="ellipsis"></p></div>
</div>
<img src="img/background.png" class="bg">
<p id="greeting"></p>

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

<div id="loginButtons" class="midSection">
    <a href="#" id="loginButton" class="button">Login </a>
    <a href="#" id="createAccountButton" class="button">Create Account!</a>
</div>
<section id="loginBox" class="overlayBox">
    <h2>Login</h2>
    <form method="post" class="minimal" id="login">
        <label for="email">
            Username:
            <input type="email" name="email" id="emailLogin" placeholder="Email address" title="Valid email address required" required />
        </label>
        <label for="password">
            Password:
            <input type="password" name="password" id="passwordLogin" placeholder="Password" pattern=".{8,}" title="At least 8 characters" required />
        </label>
        <button type="submit" class="btn-minimal">Sign in</button>
    </form>
</section>
<section id="createAccountBox" class="overlayBox">
    <h2>Create Account</h2>
    <form method="post" class="minimal" id="createAccount">
        <label for="fName">
            First:
            <input type="text" name="fNameAccount" id="fNameAccount" pattern="[a-zA-Z]+" title="Letters only" placeholder="First Name" required />
        </label>
        <label for="lName">
            Last:
            <input type="text" name="lNameAccount" id="lNameAccount" pattern="[a-zA-Z]+" title="Letters only" placeholder="Last Name" required />
        </label>
        <label for = "email">
            Email:
        </label>
        <input type = "text" id="emailAccount" email = "email" required="required" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" />
        <label for="password">
            Password:
            <input type="password" name="password" id="passwordAccount" placeholder="Password must be at least 8 characters" pattern=".{8,}" required />
        </label>
        <button type="submit" class="btn-minimal">Create Account!</button>
    </form>
</section>
<script src="js/index2.js"></script>
</body>
</HTML>





