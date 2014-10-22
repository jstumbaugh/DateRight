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
$app->post('/submitNewActivity', 'submitNewActivity');
$app->post('/viewProfile', 'viewProfile');
$app->post('/viewFavorites', 'viewFavorites');
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
	$sql = "SELECT * FROM Users WHERE Email = :email";
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
		    $salt = $returnedInfo->PasswordSalt;
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
	$sql2 = "SELECT * FROM Users WHERE Email = :email AND Password = :password";
	try {
		// Bind and run the SQL Statement of logging the user in
		if(isset($userInfo)) {
			//Get the database connection
			$stmt2 = $db->prepare($sql2);
		    $stmt2->bindParam("email", $userInfo->email);
		    $stmt2->bindParam("password", $pw);
		    $stmt2->execute();
		    $returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);
		    if(empty($returnedInfo)) {
		    	echo '{"error":{"text": "Email not found in DB."}}';//ALSO DISPLAYS IF PW IS INCORRECT. PLEASE FIX.
		    }
		    else {
			    //Store user info into session
			    $_SESSION['Email'] = $returnedInfo->Email;
			    $_SESSION['FirstName'] = $returnedInfo->FirstName;
			    $_SESSION['LastName'] = $returnedInfo->LastName;
			    $_SESSION['UserType'] = $returnedInfo->UserType;
			    $_SESSION['Sex'] = $returnedInfo->Sex;
			    $_SESSION['UserID'] = $returnedInfo->UserID + 0;
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
		$checksql = "SELECT Email FROM Users WHERE Email = :email";
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
		$sql = "INSERT INTO Users (FirstName, LastName, Email, Password, PasswordSalt, UserType, Sex) Values(:fName, :lName, :email, :password, :salt, :userType,  :sex)";
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
			$stmt->bindParam("userType", $userInfo->userType);
			$stmt->bindParam("sex", $userInfo->sex);
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


// Submit New Activity code for DateRight

function submitNewActivity()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$activityInfo = json_decode($request->getBody());
	$activity_exists;

	// This will check to see if the activity is already in the database
	try {
		$checksql = "SELECT Name, Location FROM Activities WHERE Name = :name AND Location = :location";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("name",$activityInfo->name);
		$stmt->bindParam("location",$activityInfo->location);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo)){
			$activity_exists = false;
		}
		else {
			$activity_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

	// If the activity does not already exist, then we insert the activity into the Activities table
	if($activity_exists == 0)
	{
		$sql = "INSERT INTO Activities (Name, Description, Cost, Location) Values(:name, :description, :cost, :location)";
	try{
		if(isset($activityInfo)) {
			//Get database connection
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("name",$activityInfo->Name);
			$stmt->bindParam("description", $activityInfo->Description);
			$stmt->bindParam("cost", $activityInfo->Cost);
			$stmt->bindParam("location", $activityInfo->Location);
			$stmt->execute();
			echo '{"success":{"text": "Activity successfully created."}}';
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
		echo '{"error":{"text": "Activity already exists."}}';
	}			
}


// View Profile for DateRight
function viewProfile(){
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$profile_exists;

	// This will check to see if the user has an account in the database
	try {
		$checksql = "SELECT UserID FROM Users WHERE UserID = :userID";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("userID",$userInfo->UserID);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo)){
			$profile_exists = false;
			echo json_encode($userInfo->UserID);
		}
		else {
			$profile_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	
	if ($profile_exists) // pull info from database
	{
		$sql = "SELECT FirstName, LastName, Email FROM Users WHERE UserID = :userID";
		$db = getConnection();
		$stmt1 = $db->prepare($sql);
		$stmt1->bindParam("userID",$userInfo->UserID);	
		$stmt1->execute();
		$returnedInfo1 = $stmt1->fetch(PDO::FETCH_OBJ);
		echo json_encode($returnedInfo1);
	} else 
	{
		//echo $userInfo->UserID;
		echo '{"error":{"text": "User does not have a profile."}}';
	}
} // end of function

//View Favorites
function viewFavorites(){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	$userInfo = json_decode($request->getBody());
	$userID = $userInfo->UserID; 
	$sql = "SELECT ActivityID, DatePlanID FROM Favorites WHERE UserID = :userID";
	$db = getConnection();
	$stmt = $db->prepare($sql);
	$stmt ->bindParam("userID", $userID);
	$stmt ->execute();
	$returnedInfo1 = $stmt->fetch(PDO::FETCH_ASSOC);
	$activity = $returnedInfo1['ActivityID'];
	$dateplan = $returnedInfo1['DatePlanID'];
	$sql2 = "SELECT * FROM Activities, DatePlans WHERE Activities.ActivityID = :activityID AND DatePlans.DatePlanID = :dateplanid";
	$stmt2 = $db ->prepare($sql2);
	$stmt2 -> bindParam("activityID", $activity);
	$stmt2 -> bindParam("dateplanid", $dateplan);

	$stmt2->execute();
	$rI2 = $stmt2->fetch(PDO::FETCH_OBJ);
	echo json_encode($rI2);
	
	}
function addFavorites (){
	
}






?>