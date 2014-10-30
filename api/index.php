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

//ERROR CODES
class ERROR{
	const SUCCESS = 001,
	 ACCOUNT_EXISTS = 100,
	 JSON_ERROR = 200,
	 ACCOUNT_DOESNT_EXIST = 300,
	 LOGIN_FAILURE = 400,
	 NO_RESULTS = 500,
	 ACTIVITY_EXISTS = 600;
	// PASSWORD_IS_INCORRECT = 700;
}

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
$app->post('/searchActivities', 'searchActivities');
$app->post('/viewActivityReviews', 'viewActivityReviews');
$app->post('/viewDatePlanReviews', 'viewDatePlanReviews');
$app->get('/topTags', 'topTags');
$app->get('/getTaggedActivities', 'getTaggedActivities');
$app->get('/getRandomIdea', 'getRandomIdea');
$app->post('/addFavorite', 'addFavorite');
$app->post('/updateAccount', 'updateAccount');
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
	$foundEmail = false;
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
		    $foundEmail = true;
		}
		else {
			echo ERROR::JSON_ERROR;
		}
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	if($foundEmail){
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
		    	echo ERROR::LOGIN_FAILURE;//ALSO DISPLAYS IF PW IS INCORRECT. PLEASE FIX.
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
			echo ERROR::JSON_ERROR;
		}
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}

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

function createAccount()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$email_exists;
	$username_exists;

	// This will check to see if the email is already in the database
	try 
	{
		$checksql = "SELECT Email FROM Users WHERE Email = :email";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("email",$userInfo->email);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo))
		{
			$email_exists = false;
		}
		else 
		{
			$email_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) 
	{
			exit('{"error":{"text":'. $e->getMessage() .'}}');

	}

// This will check to see if the username is already in the database
	try 
	{
		$checksql1 = "SELECT UserName FROM Users WHERE UserName = :username";
		$db = getConnection();
		$stmt1 = $db->prepare($checksql1);
		$stmt1->bindParam("username","asdfasdf"); // should be $userInfo->UserName
		$stmt1->execute();
		$returnedInfo1 = $stmt1->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo1))
		{
			$username_exists = false;
		}
		else 
		{
			$username_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) 
	{
			exit('{"error":{"text":'. $e->getMessage() .'}}'); 
	}


	// If the username and email do not already exist, then we insert the account into the Users table
	if($username_exists == 0 && $email_exists == 0)
	{
		$sql = "INSERT INTO Users (FirstName, LastName, Email, Password, PasswordSalt, UserType, Sex) Values(:fName, :lName, :email, :password, :salt, :userType,  :sex)";
		try
		{
			if(isset($userInfo)) 
			{
				// Salt and hash the password.
	        	$salt = sha1(md5($userInfo->password));
	       		$pw = md5($userInfo->password.$salt);
				
				//Get database connection and insert user to database
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

				// now want to login user and store user info into session
			    $_SESSION['Email'] = $returnedInfo->Email;
			    $_SESSION['FirstName'] = $returnedInfo->FirstName;
			    $_SESSION['LastName'] = $returnedInfo->LastName;
			    $_SESSION['UserType'] = $returnedInfo->UserType;
			    $_SESSION['Sex'] = $returnedInfo->Sex;
			    $_SESSION['UserID'] = $returnedInfo->UserID + 0;
			    echo json_encode($_SESSION);
			}
			else 
			{
				exit(ERROR::JSON_ERROR);
			}
		}		    
		catch(PDOException $e) 
		{
				exit('{"error":{"text":'. $e->getMessage() .'}}'); 
		}	
	}
	else
	{
		echo ERROR::ACCOUNT_EXISTS;
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
		$stmt->bindParam("name",$activityInfo->Name);
		$stmt->bindParam("location",$activityInfo->Location);	
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
			echo ERROR::SUCCESS;
		}
		else {
			echo ERROR::JSON_ERROR;
		}
	}		    
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}	
	}
	else{
		echo ERROR::ACTIVITY_EXISTS;
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
		echo ERROR::ACCOUNT_DOESNT_EXIST;
	}
} // end of function

//View Favorites
function viewFavorites(){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	$sql = "SELECT * FROM Favorites WHERE UserID = :userID";
	$userInfo = json_decode($request->getBody());
	$userID = $userInfo->UserID; 
	$db = getConnection();
	$favorites = array();
	
	try{
	$stmt = $db->prepare($sql);
	$stmt ->bindParam("userID", $userID);
	$stmt ->execute();
	while($returnedInfo1 = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$activity = $returnedInfo1['ActivityID'];
		$dateplan = $returnedInfo1['DatePlanID'];
		if (empty($activity))
		{
			$sql2 =" SELECT DatePlans.Name, DatePlans.CreatorID, DatePlans.Description  FROM Dateplans WHERE Dateplans.DatePlanID = :dateplanid";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("dateplanid", $dateplan);
			$stmt2->execute();
			$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			array_push($favorites, $rI2);
		}
		else if (empty($dateplan))
		{
			$sql2 =" SELECT Activities.Name, Activities.Description, Activities.Cost, Activities.Rating, Activities.Location FROM Activities WHERE Activities.ActivityID = :activityID";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("activityID", $activity);
			$stmt2->execute();
			$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			array_push($favorites, $rI2);
		}
		else{
		$sql2 = "SELECT Activities.Name, Activities.Description, Activities.Cost, Activities.Rating, Activities.Location, DatePlans.Name, DatePlans.CreatorID, DatePlans.Description FROM Activities, DatePlans WHERE Activities.ActivityID = :activityID AND DatePlans.DatePlanID = :dateplanid";
		$stmt2 = $db ->prepare($sql2);
		$stmt2 -> bindParam("activityID", $activity);
		$stmt2 -> bindParam("dateplanid", $dateplan);
		$stmt2->execute();
		$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
	// $favorites['activityID'] = $rI2['ActivityID'];
	// $favorites['dateplanid'] = $rI2['DatePlanID'];
		array_push($favorites, $rI2);
	}
}

	//Type-casting integers before returning them
	// for($i = 0; $i < sizeof($rI2); $i = $i + 1) {
		// $rI2[$i]['ActivityID'] = $rI2[$i]['ActivityID'] + 0;
		// $rI2[$i]['Cost'] = $rI2[$i]['Cost'] + 0.00;
		// $rI2[$i]['DatePlanID'] = $rI2[$i]['DatePlanID'] + 0;
		// $rI2[$i]['Public'] = $rI2[$i]['Public'] + 0;
		// $rI2[$i]['CreatorID'] = $rI2[$i]['CreatorID'] + 0;
		// $rI2[$i]['ModID'] = $rI2[$i]['ModID'] + 0;
	// }
	echo json_encode($favorites);

	//echo json_encode($rI2);
	}
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	
	}
function addFavorite (){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	$info = json_decode($request->getBody());

	$sqlInsert = '';
	$sqlInsert2 = '';
	try{
		if (!empty($info->ActivityID))
		{
			$sqlInsert = "ActivityID";
			$sqlInsert2 = $info->ActivityID;

		}
		if (!empty($info->DatePlanID))
		{
			$sqlInsert = "DatePlanID";
			$sqlInsert2 = $info->DatePlanID;
		}
		$sql = "INSERT INTO Favorites (UserID, $sqlInsert)
				VALUES (:UserID, $sqlInsert2);";
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt -> bindParam("UserID", $info->UserID);
		$stmt -> execute();
		echo ERROR::SUCCESS;


	}
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

	
}
//Search by activity name, works with multiple word query as well 
//@return echo response with result JSON
function searchActivities (){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	//User search query
	$result = $app->request()->post('datesearch');
	
	//First check if they sent any query
	if (!empty($result)) {
		$words = explode(" ",trim($result));
		$sql = "SELECT * FROM Activities WHERE name LIKE '%$result%' OR description LIKE '%$result%'";
		//Process over all entered keywords
		foreach ($words as $term) {
			$sql.=" OR name LIKE '%$term%' OR description LIKE '%$term%' ";
		}
	//Order results by the user rating
	$sql.=" ORDER BY Rating LIMIT 50";
	$db = getConnection();
	$stmt = $db->prepare($sql);
	$stmt ->execute();
	$searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo '{"results": ' . json_encode($searchResults) . '}';
	}else{
	//No activities found w/ that query
	echo ERROR::NO_RESULTS;
		}
}



function topTags() {
	$app= \Slim\Slim::getInstance();
	$request =$app->request;

	$numTags = 10;//Default number of tags to return

	//Check to see if number of tags to return was specified in the URL of the GET REQUEST

	$num = $app->request()->params('num');
	if(!empty($num))
		$numTags = $num;
	
	$sql = "SELECT TagID, TagName, count(activityID) AS quantity
			FROM Tags NATURAL JOIN TaggedActivities
			GROUP BY tagID
			ORDER BY count(activityID) DESC
			LIMIT $numTags;";

	try {
		$db = getConnection();
		$stmt = $db->query($sql);
		$returnedInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//Type-casting integers before returning them
		for($i = 0; $i < sizeof($returnedInfo); $i = $i + 1) {
			$returnedInfo[$i]['quantity'] = $returnedInfo[$i]['quantity'] + 0;
			$returnedInfo[$i]['TagID'] = $returnedInfo[$i]['TagID'] + 0;
		}

		echo json_encode($returnedInfo);
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}



"SELECT ActivityID, Name, Description, Cost, Rating, Location
FROM TaggedActivities NATURAL JOIN Activities NATURAL JOIN Tags
WHERE TagName = 'Italian'";


function getTaggedActivities() {
	$app= \Slim\Slim::getInstance();
	$request =$app->request;

	$numTags;
	$tagID;
	$tagName;

	//Declaring strings which may be inserted into the SQL statement before sending
	$option1 = "WHERE TagID = ";
	$option2 = "NATURAL JOIN Tags
			WHERE TagName = '";

	$sqlInsert1 = "";//Set to $option1 or $option2 depending on if tagID or tagName is provided
	$sqlInsert2 = "";//Only set if num is provided

	$num = $app->request()->params('num');
	//Check to see if number of tags to return was specified in the URL of the GET REQUEST
	if(!empty($num)) {
		$numTags = $app->request()->params('num');
		$sqlInsert2 = "
		LIMIT $numTags";
	}

	$tagID = $app->request()->params('tagID');
	$tagName = $app->request()->params('tagName');

	//Check to see if either tagID or tagName was specified
	if(!empty($tagID)) {
		$sqlInsert = $option1 . $tagID . $sqlInsert2;
		$sqlSet = TRUE;
	}
	else if(!empty($tagName)) {
		$sqlInsert = $option2 . $tagName . "'" . $sqlInsert2;
		$sqlSet = TRUE;
	}
	else {
		exit('Neither search parameter (TagID || TagName) set!');//ERROR
	}
	
	$sql = "SELECT ActivityID, Name, Description, Cost, Rating, Location
			FROM TaggedActivities NATURAL JOIN Activities $sqlInsert";

	try {
		$db = getConnection();
		$stmt = $db->query($sql);
		$returnedInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//Checking if the query returned any results
		if(sizeof($returnedInfo) == 0) {
			exit(ERROR::JSON_ERROR);
		}

		//Type-casting integers before returning them
		for($i = 0; $i < sizeof($returnedInfo); $i = $i + 1) {
			$returnedInfo[$i]['Cost'] = $returnedInfo[$i]['Cost'] + 0.00;
			$returnedInfo[$i]['ActivityID'] = $returnedInfo[$i]['ActivityID'] + 0;
		}

		echo json_encode($returnedInfo);
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}



// View User Activity Reviews for DateRight
function viewActivityReviews() {
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$user_exists;

	// This will check to see if the user has an account in the database
	try {
		$checksql = "SELECT UserID FROM Users WHERE UserID = :userID";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("userID",$userInfo->UserID);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo)){
			$user_exists = false;
			echo json_encode($userInfo->UserID);
		}
		else {
			$user_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	
	if ($user_exists) // pull info from database
	{
		// For Activity Reviews
		$sql = "SELECT * FROM ActivityReviews WHERE UserID = :userID";
		$db = getConnection();
		$stmt1 = $db->prepare($sql);
		$stmt1->bindParam("userID",$userInfo->UserID);	
		$stmt1->execute();
		$returnedInfo1 = $stmt1->fetch(PDO::FETCH_OBJ);
		
		if(empty($returnedInfo1)){
			echo ERROR::NO_RESULTS;
		} else {
			echo json_encode($returnedInfo1);
			$db = null;
		}

	}
	else {
		echo ERROR::ACCOUNT_DOESNT_EXIST;
	}
} // end of function



// View User DatePlan Reviews for DateRight
function viewDatePlanReviews() {
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$user_exists;

	// This will check to see if the user has an account in the database
	try {
		$checksql = "SELECT UserID FROM Users WHERE UserID = :userID";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("userID",$userInfo->UserID);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo)){
			$user_exists = false;
			exit(ERROR::ACCOUNT_DOESNT_EXIST);
		}
		else {
			$user_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	
	if ($user_exists) // pull info from database
	{
		// For DatePlan Reviews
		$sql1 = "SELECT * FROM DatePlanReviews WHERE UserID = :userID";
		$db = getConnection();
		$stmt2 = $db->prepare($sql1);
		$stmt2->bindParam("userID",$userInfo->UserID);	
		$stmt2->execute();
		$returnedInfo2 = $stmt2->fetch(PDO::FETCH_OBJ);
		
		if(empty($returnedInfo1)){
			echo ERROR::NO_RESULTS;
		} else {
			echo json_encode($returnedInfo1);
			$db = null;
		}

	} else 
	{
		//echo $userInfo->UserID;
		echo '{"error":{"text": "User does not have a profile."}}';
	}
} // end of function

//Returns random date idea as JSON object
//@return JSON object containing random date idea from Dateplans
function getRandomIdea(){
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	//Generate random date idea from dateplans table
	$sql = "SELECT * FROM dateplans WHERE dateplanid >= (SELECT FLOOR( MAX(dateplanid) * RAND()) FROM Dateplans ) ORDER BY dateplanid LIMIT 1";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);
		$returnedInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($returnedInfo);
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function updateAccount(){
	$app = \Slim\Slim::getInstance();
	$request = $app->request();
	$info = json_decode($request->getBody());
	echo json_encode($info);
	$user_exists;
	$password_exists;
	try{
		$userID = json_encode($info->userID);
		//echo $userID;

		// $sql = "SELECT UserID FROM Users WHERE Users.UserID = :userID";
		// $db = getConnection();
		// $stmt = $db->prepare($sql);
		// $stmt->bindParam("userID", $info->userID);
		// $stmt->execute();
		// $returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		// echo json_encode($returnedInfo);
		if (empty($userID))
		{
			$user_exists = false;
		}
		else {
			$user_exists = true;
		}
		//$db = null;
	
	} 
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	if($user_exists)
	{
		$db = getConnection();
		$checkpasswordsql = "SELECT Password, PasswordSalt FROM Users WHERE UserID = :userID";
		$passwordStmt = $db->prepare($checkpasswordsql);
		$passwordStmt ->bindParam("userID", $userID);
		$passwordStmt->execute();
		$returnedInfoPassword = $passwordStmt->fetch(PDO::FETCH_OBJ);
		//echo json_encode($returnedInfoPassword);
		echo "\nPASSWORD: ";
		echo json_encode($returnedInfoPassword->Password);
		echo "\nSALT: ";
		echo json_encode($returnedInfoPassword->PasswordSalt);

		$salt = $returnedInfoPassword->PasswordSalt;

		$raw_password = $info->currentPassword;
		echo "\nCURRENT PASSWORD: " . $raw_password;
		$pw = md5($raw_password.$salt);
		echo "\nCurrent password salted: " . json_encode($pw);
		if ($pw == $returnedInfoPassword->Password)
		{
			echo "\nmatch found\n";
			$password_exists = true;
		}
		else{
			echo "\nmatch not found\n";
			$password_exists = false;
		}

		if ($password_exists)
		{
		$fName = $info->fName;
		$lName = $info->lName;
		$email = $info->email;
		$username = $info->username;
		$raw_newPassword = $info->newPassword;
		$saltNewPassword = sha1(md5($raw_newPassword));
       	$newPassword = md5($raw_newPassword.$saltNewPassword);
		$db = getConnection();
		$updatesql1 = "UPDATE Users SET FirstName = :fName, LastName = :lName, UserName = :username, Email = :email, Password = :password, PasswordSalt = :passwordsalt WHERE Users.UserID = :userID";
		$stmt1 = $db->prepare($updatesql1);
		$stmt1 ->bindParam("userID", $userID);
		$stmt1->bindParam("fName", $fName);
		$stmt1->bindParam("lName", $lName);
		$stmt1->bindParam("email", $email);
		$stmt1->bindParam("username", $username);
		$stmt1->bindParam("password", $newPassword);
		$stmt1->bindParam("passwordsalt", $saltNewPassword);
		$stmt1->execute();
		}
		else {
			echo ERROR::LOGIN_FAILURE;

		}
	}
	else
	{
		echo ERROR::ACCOUNT_DOESNT_EXIST;
	}
}



?>