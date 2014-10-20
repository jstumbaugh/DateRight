<?php
/** 
 * Designed by DDR Software.
 * This file contains the api for all our php functions.
 */

//Includes
require 'config.php';

\Slim\Slim::registerAutoloader();
$menu = new stdClass();
$app = new \Slim\Slim();
// This will use the Slim Framework to implement Sessions
$app->add(new \Slim\Middleware\SessionCookie(array('secret' => 'date')));
$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
            $app->flash('error', 'Login required');
            $app->redirect('/login');
        }
    };
};

$app->hook('slim.before.dispatch', function() use ($app) { 
   $user = null;
   if (isset($_SESSION['user'])) {
      $user = $_SESSION['user'];
   }
   $app->view()->setData('user', $user);
});

//This will create all the posts for each function
$app->post('/login', 'login');
$app->post('/logout', 'logout');
$app->post('/createAccount', 'createAccount');
$app->run();

/**
     * A function to verify that a user is entering the right information when
     * logging in. This will login the user and change the session from guest to user. By retrieving information from a query to the database. It 
     * also saves the information into a session cookie (first and last name, email, credit card info). 
     * @return result in logging user in through echo statement or failure in doing so
     */
function login() {
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$raw_password = $userInfo->password;
	//Get the user from the email address
	$sql = "SELECT * FROM Users WHERE email = :email";
	$salt = null;
	try {
		// Bind and run the SQL Statement of logging the user in
		if(isset($userInfo)) {
			//Get the database connection
			$db = getConnection();
			$stmt = $db->prepare($sql);
		    $stmt->bindParam("email", $userInfo->email);
		    $stmt->execute();
		    $returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		    //Get the salt for the user
		    $salt = $returnedInfo->salt;
		}
		else {
			echo '{"error":{"text": "JSON was not properly set."}}'; 
		}
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	//Get password from user inputted password and salt saved in database
	$pw = md5($raw_password.$salt);
	$sql = "SELECT userID, fName, lName, email,creditProvider,creditCardNumber FROM Users WHERE email = :email AND password = :password";
	try {
		// Bind and run the SQL Statement of logging the user in
		if(isset($userInfo)) {
			//Get the database connection
			$db = getConnection();
			$stmt = $db->prepare($sql);
		    $stmt->bindParam("email", $userInfo->email);
		    $stmt->bindParam("password", $pw);
		    $stmt->execute();
		    $returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		    if(empty($returnedInfo)) {
		    	echo '{"error":{"text": "Email not found in DB."}}';
		    }
		    else {
			    //Store user info into session
			    $_SESSION['email'] = $returnedInfo->email;
			    $_SESSION['fName'] = $returnedInfo->fName;
			    $_SESSION['lName'] = $returnedInfo->lName;
			    $_SESSION['userID'] = $returnedInfo->userID + 0;
			    $_SESSION['creditProvider'] = $returnedInfo->creditProvider;
			    $_SESSION['creditCardNumber'] = $returnedInfo->creditCardNumber;
			    echo json_encode($_SESSION);
			}
		}
		else {
			echo '{"error":{"text": "JSON was not properly set."}}'; 
		}
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}


}
//Deletes session for user upon clicking logout
function logout() {
	$app = \Slim\Slim::getInstance();
	//clear session cookie
	unset($_SESSION['user']);
    $app->view()->setData('user', null);
}

/**
     * Function to allow a user to create an account. If the user supplies the correct information
     * then they will be inserted into the database as a new user. Get's the user's information from
     * a JSON response. First checks to see if the account already exists in the database
     * @return result in creating account in echo statement or failure in doing so
     */
function createAccount(){
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$account_exists;

	// This will check to see if the email is already in the database
	try {
		$checksql = "SELECT email FROM Users WHERE email = :email";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("email",$userInfo->email);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo)){
			$account_exists = false;
		}
		else {
			$account_exists = true;

		}
		$db = null;
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	// If the account does not already exist, then we insert the account into the Users table
	if($account_exists == 0)
	{
		$sql = "INSERT INTO Users (fname, lname, email, password, salt, creditCardNumber, creditProvider) Values(:fName, :lName, :email, :password, :salt, :creditCardNumber,  :creditProvider)";
	try{
		if(isset($userInfo)) {
			// Salt and hash the password.
        	$salt = sha1(md5($userInfo->password));
       		$pw = md5($userInfo->password.$salt);
			//Get database connection
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("fName",$userInfo->fName);
			$stmt->bindParam("lName", $userInfo->lName);
			$stmt->bindParam("email", $userInfo->email);
			$stmt->bindParam("password", $pw);
			$stmt->bindParam("salt", $salt);
			$stmt->bindParam("creditCardNumber", $userInfo->creditCardNumber);
			$stmt->bindParam("creditProvider", $userInfo->creditProvider);
			$stmt->execute();
			echo '{"success":{"text": "Account successfully created."}}';
		}
		else {
			echo '{"error":{"text": "JSON was not properly set."}}'; 
		}
	}		    
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}	
	}
	else{
		echo '{"error":{"text": "Account exists!!"}}';
	}		
	
}


?>