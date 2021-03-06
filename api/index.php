<?php
/** 
 * Designed by DDR Software.
 * This file contains the api for all our php functions.
 */

//EXAMPLE FOR API DOC:
 /**
  * A summary informing the user what the associated element does.
  *
  * A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
  * and to provide some background information or textual references.
  *
  * @param string $myArgument With a *description* of this argument, these may also
  *    span multiple lines.
  *
  * @return void
  */

//


 
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
	 ACTIVITY_EXISTS = 600,
	 USERNAME_EXISTS = 700,
	 EMAIL_EXISTS = 800,
	 DATEPLAN_DOESNT_EXIST = 900,
	 ACTIVITY_DOESNT_EXIST = 1000,
	 UPLOAD_ERROR = 1100,
	 PARAMETERS_NOT_SET = 1200,
	 ACTIVITY_ALREADY_TAGGED = 1300;

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
$app->delete('/deleteFavorite/:op/:id', 'deleteFavorite');
$app->post('/searchActivities', 'searchActivities');
$app->post('/searchDateplans', 'searchDateplans');
$app->post('/viewActivityReviews', 'viewActivityReviews');
$app->post('/viewDatePlanReviews', 'viewDatePlanReviews');
$app->get('/topTags', 'topTags');
$app->post('/getTaggedActivities', 'getTaggedActivities');
$app->post('/getTaggedDatePlans', 'getTaggedDatePlans');
$app->get('/getRandomIdea', 'getRandomIdea');
$app->get('/getActivityById/:id', 'getActivityById');
$app->get('/getDateplanById/:id', 'getDateplanById');
$app->get('/getFullDatePlanByID/:id', 'getFullDatePlanByID');
$app->post('/addFavorite', 'addFavorite');
$app->post('/updateAccount', 'updateAccount');
$app->post('/getSessionInfo', 'getSessionInfo');
$app->post('/shareDatePlan', 'shareDatePlan');
$app->post('/reviewDatePlan', 'reviewDatePlan');
$app->post('/updateDatePlan', 'updateDatePlan');
$app->post('/reviewActivity', 'reviewActivity');
$app->post('/recoveryQuestion', 'recoveryQuestion');
$app->post('/recoverPassword', 'recoverPassword');
$app->post('/resetPassword', 'resetPassword');
$app->post('/viewUserDatePlans', 'viewUserDatePlans');
$app->post('/addPhoto', 'addPhoto');
$app->get('/getPhoto/:userID', 'getPhoto');
$app->get('/getTagsFromActivityID/:activityID', 'getTagsFromActivityID');
$app->delete('/deleteDatePlan', 'deleteDatePlan');
$app->delete('/deleteDateActivity/:datePlanID/:activityID', 'deleteDateActivity');
$app->post('/createDatePlan', 'createDatePlan');
$app->post('/addActivity', 'addActivity');
$app->post('/updateDatePlanDescription', 'updateDatePlanDescription');
$app->post('/updateDatePlanName', 'updateDatePlanName');
$app->get('/getAssociatedActivities/:datePlanID/:standalone', 'getAssociatedActivities');
$app->post('/addTagToActivity', 'addTagToActivity');
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
	$returnedInfo;
	try {
		// Bind and run the SQL Statement of logging the user in
		if(isset($userInfo)) {
			//Get the database connection
			$db = getConnection();
			$stmt = $db->prepare($sql);
		    $stmt->bindParam("email", $userInfo->email);
		    $stmt->execute();
		    $returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		    if(empty($returnedInfo)){
		    	$foundEmail=false;
		    }else{
		    	$foundEmail=true;
		    }
		}
		else {
			echo ERROR::LOGIN_FAILURE;
		}
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	if($foundEmail){
	//Get the salt for the user
	$salt = $returnedInfo->PasswordSalt;
	$foundEmail = true;	
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
		    	echo ERROR::LOGIN_FAILURE;
		    }
		    else {
			    //Store user info into session
			    $_SESSION['UserName'] = $returnedInfo->UserName;
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

	}else{
		echo ERROR::LOGIN_FAILURE;
	}
}
/**
     * Deletes session for user upon clicking logout
     * @return true or false based upon if successful logout
     */
function logout() {
	//clear session cookie
	if(isset($_SESSION['UserName']) || isset($_SESSION['Email']) || 
            isset($_SESSION['UserID']) || isset($_SESSION['FirstName']))
        {
        	unset($_SESSION['UserName']);
            $_SESSION = array();
            session_destroy();
            echo true;
        }
        else echo false;
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
		//Check if the email exists
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
		$stmt1->bindParam("username",$userInfo->userName); // should be $userInfo->UserName
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
	if(!$email_exists && !$username_exists)
	{
		$sql = "INSERT INTO Users (UserName,FirstName, LastName, Email, Password, PasswordSalt, UserType, Sex, SecurityQuestion, SecurityAnswer, SecuritySalt) Values(:userName,:fName, :lName, :email, :password, :salt, :userType,  :sex, :secQuestion, :secAnswer, :secSalt)";
		try
		{
			if(isset($userInfo)) 
			{
				// Salt and hash the password.
	        	$salt = sha1(md5($userInfo->password));
	       		$pw = md5(($userInfo->password).$salt);

	       		// Salt and hash the security answer
	       		$secSalt = sha1(md5($userInfo->secAnswer));
	       		$secAnswer = md5(($userInfo->secAnswer).$secSalt);
				
				//Get database connection and insert user to database
				$db = getConnection();
				$stmt = $db->prepare($sql);
				$stmt->bindParam("userName",$userInfo->userName);
				$stmt->bindParam("fName",$userInfo->fName);
				$stmt->bindParam("lName", $userInfo->lName);
				$stmt->bindParam("email", $userInfo->email);
				$stmt->bindParam("password", $pw);
				$stmt->bindParam("salt", $salt);
				$stmt->bindParam("userType", $userInfo->userType);
				$stmt->bindParam("sex", $userInfo->sex);
				$stmt->bindParam("secQuestion", $userInfo->securityQuestion);
				$stmt->bindParam("secAnswer", $secAnswer);
				$stmt->bindParam("secSalt", $secSalt);
				$stmt->execute();
				//Log user in
			    logUser($userInfo);
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
/**
* A function to log user in and create session
* @param Object $userInfo The object that contains the user info
* @return JSON object of the user info from session
*/		
function logUser($userInfo){
	 //Log user in
	$salt = sha1(md5($userInfo->password));
	$pw = md5($userInfo->password.$salt);
	$sql2 = "SELECT * FROM Users WHERE Email = :email AND Password = :password";
		try {
			// Bind and run the SQL Statement of logging the user in
			if(isset($userInfo)) {
				//Get the database connection
				$db = getConnection();
				$stmt2 = $db->prepare($sql2);
				$stmt2->bindParam("email", $userInfo->email);
				$stmt2->bindParam("password", $pw);
				$stmt2->execute();
				$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);				
				if(empty($returnedInfo)) {
					 echo ERROR::LOGIN_FAILURE;
				}
				else {
					//Store user info into session
					$_SESSION['UserName'] = $returnedInfo->UserName;
					$_SESSION['Email'] = $returnedInfo->Email;
					$_SESSION['FirstName'] = $returnedInfo->FirstName;
					$_SESSION['LastName'] = $returnedInfo->LastName;
					$_SESSION['UserType'] = $returnedInfo->UserType;
					$_SESSION['Sex'] = $returnedInfo->Sex;
					$_SESSION['UserID'] = $returnedInfo->UserID + 0;
					//Echo back session information
					echo json_encode($_SESSION);
					}
				}
				else {
						echo ERROR::JSON_ERROR;
					}
			}
			catch(PDOException $e) {
				return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
/**
* Submit New Activity code for DateRight
* @param JSON object containing activitity information
* @return SUCCESS OR NOT
*/
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

/**
* View Profile for DateRight
* @param JSON object containing user information
* @return JSON object containing user's profile information
*/
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
		$sql = "SELECT FirstName, LastName, Email, UserName FROM Users WHERE UserID = :userID";
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

/**
* View Favorites
* @param JSON object containing user information
* @return JSON object containing user's favorites
*/
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
			$sql2 =" SELECT DatePlans.DatePlanID, DatePlans.Name, DatePlans.CreatorID, DatePlans.Description, AVG(DatePlanReviews.Rating) AS Rating, COUNT(DatePlanReviews.Rating) AS RatingCount  FROM DatePlans LEFT OUTER JOIN DatePlanReviews ON DatePlans.DatePlanID = DatePlanReviews.DatePlanID WHERE DatePlans.DatePlanID = :dateplanid GROUP BY DatePlanID";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("dateplanid", $dateplan);
			$stmt2->execute();
			$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			array_push($favorites, $rI2);
			$size = count($favorites);
			for($i=0;$i<$size;$i++){
				$cID=$favorites[$i]['DatePlanID'];
				$associatedActs=getAssociatedActivities($cID, 0);
				$favorites[$i]['AssociatedActivities']=$associatedActs;
			}
		}
		else if (empty($dateplan))
		{
			$sql2 =" SELECT Activities.ActivityID,Activities.Name, Activities.Description, Activities.Cost, Activities.Location, AVG(ActivityReviews.Rating) AS Rating, COUNT(ActivityReviews.Rating) AS RatingCount FROM Activities LEFT OUTER JOIN ActivityReviews ON Activities.ActivityID = ActivityReviews.ActivityID WHERE Activities.ActivityID = :activityID";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("activityID", $activity);
			$stmt2->execute();
			$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			array_push($favorites, $rI2);
		}
		else{
		$sql2 = "SELECT Activities.ActivityID,Activities.Name, Activities.Description, Activities.Cost, Activities.Location, AVG(ActivityReviews.Rating) AS Rating, COUNT(ActivityReviews.Rating) AS RatingCount, DatePlans.Name, DatePlans.CreatorID, DatePlans.Description FROM Activities LEFT OUTER JOIN ActivityReviews ON Activities.ActivityID = ActivityReviews.ActivityID, DatePlans WHERE Activities.ActivityID = :activityID AND DatePlans.DatePlanID = :dateplanid GROUP BY Activities.ActivityID";
		$stmt2 = $db ->prepare($sql2);
		$stmt2 -> bindParam("activityID", $activity);
		$stmt2 -> bindParam("dateplanid", $dateplan);
		$stmt2->execute();
		$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
	 	//$favorites['activityID'] = $rI2['ActivityID'];
		// $favorites['dateplanid'] = $rI2['DatePlanID'];
		array_push($favorites, $rI2);
		$size = count($favorites);
		for($i=0;$i<$size;$i++){
			if ($favorites[$i]['DatePlanID'] != null){
				$cID=$favorites[$i]['DatePlanID'];
				$associatedActs=getAssociatedActivities($cID, 0);
				$favorites[$i]['AssociatedActivities']=$associatedActs;
			}
		}
	}
}


	//Type-casting integers before returning them
	// for($i = 0; $i < sizeof($favorites); $i = $i + 1) {
	// 	$favorites[$i]['Cost'] = $favorites[$i]['Cost'] + 0;
	// }
	
	echo json_encode($favorites);

	//echo json_encode($rI2);
	}
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	
	}
/**
* Function to add Favorite to the database
* @param JSON object containing user information
* @return SUCCESS OR NOT
*/
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
/**
* Function to delete a favorite by id from the favorites table. Called with a DELETE request
* @param int $id the favorite id
* @return success or not in deleting favorite
*/
function deleteFavorite($op,$id) {
	//Determine operation wanted, activity or date plan deletion
	if($op==0)
    	$deleteQuery = "DELETE FROM Favorites WHERE ActivityID =:ID and UserID=:userID";
    else 
    	$deleteQuery = "DELETE FROM Favorites WHERE DatePlanID =:ID and UserID=:userID";
    if (isset($_SESSION['UserID'])) {
    	$uID=$_SESSION['UserID'];
    try {
        $db = getConnection();
        $stmt = $db->prepare($deleteQuery);
        $stmt->bindParam("ID", $id);
        $stmt->bindParam("userID", $uID);
        $stmt->execute();
        if($stmt->rowCount()>0){
        echo ERROR::SUCCESS;
    	}
    	else{
    		echo ERROR::NO_RESULTS;
    	}
        $db = null;
    } catch(PDOException $e) {
    	echo ERROR::NO_RESULTS;
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
	}
}
/**
* Get Activity By ID
* @param int id that represents the activity id
* @return JSON object containing activity information for that id
*/
function getActivityById($id) {
    $sql = "SELECT Activities.*, AVG(ActivityReviews.Rating) AS Rating, COUNT(ActivityReviews.Rating) AS RatingCount FROM Activities LEFT OUTER JOIN ActivityReviews ON Activities.ActivityID = ActivityReviews.ActivityID WHERE Activities.ActivityID=:id GROUP BY ActivityID";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        $size = count($results);
		if($size>0){
			echo json_encode($results);
		}else{
			echo ERROR::NO_RESULTS;
		}
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
  * This function will return the dateplan given the associated DatePlanID
  *
  * @param the ID of the dateplan
  * @return the DatePlan
  */
function getDateplanById($id) {
    $sql = "SELECT DatePlans.*, AVG(DatePlanReviews.Rating) AS Rating, COUNT(DatePlanReviews.Rating) AS RatingCount FROM DatePlans LEFT OUTER JOIN DatePlanReviews ON DatePlans.DatePlanID = DatePlanReviews.DatePlanID  WHERE DatePlans.DatePlanID=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        $size = count($results);
        exit(json_encode($results));
		if($size>0){
			echo json_encode($results);
		}else{
			echo ERROR::NO_RESULTS;
		}
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
  * This function returns an array of TagIDs from a given activityid
  *
  * @param activityID
  *
  * @return JSON array of tag ids for that activity id
  */
function getTagsFromActivityID($activityID) {
    $sql = "SELECT TagID, TagName FROM TaggedActivities NATURAL JOIN Tags WHERE ActivityID=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $activityID);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        $size = count($results);
		if($size>0){
			echo json_encode($results);
		}else{
			echo ERROR::NO_RESULTS;
		}
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/**
  * This function searchs by activity name, works with multiple word query as well 
  *
  * @param DatePlanID
  *
  * @return echo response with result JSON
  */
function getFullDatePlanByID ($id)
{
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	//User search query
	$json = json_decode($request->getBody());

	//First check if they sent any query
	if (!empty($id)) 
	{
		try 
		{
			$db = getConnection();
			$getDatePlans="SELECT DatePlans.*, AVG(DatePlanReviews.Rating) AS Rating, COUNT(DatePlanReviews.Rating) AS RatingCount FROM DatePlans LEFT OUTER JOIN DatePlanReviews ON DatePlans.DatePlanID = DatePlanReviews.DatePlanID WHERE DatePlans.Public=1 AND DatePlans.DatePlanID = :dateID GROUP BY DatePlans.DatePlanID";
			$stmt = $db->prepare($getDatePlans);
			$stmt->bindParam("dateID", $id);
			$stmt ->execute();
			$datePlanResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$size = count($datePlanResults);
			if($size>0)
			{
				//Find the associated activities for each dateplan id
				for($i=0;$i<$size;$i++)
				{
					$cID=$datePlanResults[$i]['DatePlanID'];
					$associatedActs=getAssociatedActivities($cID, 0);
					$datePlanResults[$i]['AssociatedActivities']=$associatedActs;
				}
			}
			echo '{"DatePlans": ' . json_encode($datePlanResults) . '}';
		}	
		catch(PDOException $e) 
		{
    		echo ERROR::NO_RESULTS;
        	echo '{"error":{"text":'. $e->getMessage() .'}}';
    	}
	}
	else
	{
		//No activities found w/ that query
		echo ERROR::NO_RESULTS;
	}
}

/**
  * Helper function to return the associated activities for each dateplanid supplied 
  *
  * @param int representing dateplan id
  *
  * @return array containing the associatd activities
  */
function getAssociatedActivities($datePlanID, $standalone){
	$sql = "SELECT ActivityID FROM DateActivities WHERE DatePlanID = :dateID";
	try{
	$db = getConnection();
	$stmt = $db->prepare($sql);
	$stmt ->bindParam("dateID", $datePlanID);
	$stmt ->execute();
	$dateActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if($standalone==1)
		echo json_encode($dateActivities);
	else
		return $dateActivities;

	}catch(PDOException $e) {
    	return ERROR::NO_RESULTS;
    }
}

/**
  * Search by date plan name, works with multiple word query as well 
  *
  * @param string to search for dateplans
  *
  * @return echo response with result JSON
  */
function searchDateplans (){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	//User search query
	$json = json_decode($request->getBody());
	$result = $json->SearchQuery;

	//First check if they sent any query
	if (!empty($result)) {
		try {
		$db = getConnection();
		//accept plural version e.g. movie(s)
		$result.="*";
		$getDatePlans="SELECT DatePlans.*, AVG(DatePlanReviews.Rating) AS Rating, COUNT(DatePlanReviews.Rating) AS RatingCount FROM DatePlans LEFT OUTER JOIN DatePlanReviews ON DatePlans.DatePlanID = DatePlanReviews.DatePlanID WHERE DatePlans.Public=1 AND MATCH(DatePlans.Name,DatePlans.Description) AGAINST(:searchQuery IN BOOLEAN MODE) GROUP BY DatePlans.DatePlanID";
		$stmt = $db->prepare($getDatePlans);
		$stmt->bindParam("searchQuery", $result);
		$stmt ->execute();
		$datePlanResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$size = count($datePlanResults);
		if($size>0){
			//Find the associated activities for each dateplan id
			for($i=0;$i<$size;$i++){
				$cID=$datePlanResults[$i]['DatePlanID'];
				$associatedActs=getAssociatedActivities($cID, 0);
				$datePlanResults[$i]['AssociatedActivities']=$associatedActs;
			}
		}
		echo '{"DatePlans": ' . json_encode($datePlanResults) . '}';
		
		}	
		catch(PDOException $e) {
    	echo ERROR::NO_RESULTS;
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
	}else{
		//No activities found w/ that query
		echo ERROR::NO_RESULTS;
		}
}

/**
  * Search by activity name, works with multiple word query as well 
  *
  * @param string of a search query
  *
  * @return echo response with result JSON
  */
function searchActivities (){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	//User search query
	$json = json_decode($request->getBody());
	$result = $json->SearchQuery;

	//First check if they sent any query
	if (!empty($result)) {
		try {
		$db = getConnection();
		$getActivities="SELECT Activities.*, AVG(ActivityReviews.Rating) AS Rating, COUNT(ActivityReviews.Rating) AS RatingCount FROM Activities LEFT OUTER JOIN ActivityReviews ON Activities.ActivityID = ActivityReviews.ActivityID WHERE MATCH(Activities.Name,Activities.Description,Activities.Location) AGAINST(:searchQuery IN BOOLEAN MODE) GROUP BY Activities.ActivityID";
		$stmt = $db->prepare($getActivities);
		//accept plural version e.g. movie(s)
		$result.="*";
		$stmt->bindParam("searchQuery", $result);
		$stmt ->execute();
		$activityResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$json=json_encode($activityResults);
		if($json!=FALSE)
			echo '{"Activities": ' . $json .'}';
		else 
			echo ERROR::NO_RESULTS;
		
		}	
		catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	}else{
		//No activities found w/ that query
		echo ERROR::NO_RESULTS;
		}
}

/**
  * This function returns the dateplan and all associated activities
  *
  * @param the userID
  *
  * @return the dateplan with all associated activities
  */
function viewUserDatePlans(){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	$sql = "SELECT * FROM DatePlans WHERE CreatorID = :userID";
	$userInfo = json_decode($request->getBody());
	$userID = $userInfo->UserID; 
	$db = getConnection();
	$allArray = array();
	$dateplans = array();

	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("userID", $userID);
		$stmt ->execute();
		$count = 0;
		while($returnedInfo1 = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			//var_dump($returnedInfo1);
			$dateplan = $returnedInfo1['DatePlanID'];
			$sql2 = "SELECT DatePlans.DatePlanID, DatePlans.CreatorID, DatePlans.ModID, DatePlans.Name, DatePlans.Description, DatePlans.Timestamp, AVG(DatePlanReviews.Rating) AS Rating, COUNT(DatePlanReviews.Rating) AS RatingCount FROM DatePlans LEFT OUTER JOIN DatePlanReviews ON DatePlans.DatePlanID = DatePlanReviews.DatePlanID WHERE DatePlans.DatePlanID = :dateplanid GROUP BY DatePlanID";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("dateplanid", $dateplan);
			$stmt2->execute();
			$rI2 = $stmt2 ->fetch(PDO::FETCH_OBJ);
			$creatorID = $rI2->CreatorID;
			$modID = $rI2->ModID;
			//var_dump($rI2);
			$quicksql1 = "SELECT Users.UserName FROM Users WHERE Users.UserID = :creatorID";
			$quickstmt1 = $db->prepare($quicksql1);
			$quickstmt1->bindParam("creatorID", $creatorID);
			$quickstmt1->execute();
			$creatorIDRI = $quickstmt1 -> fetch(PDO::FETCH_OBJ);

			$quicksql2 = "SELECT Users.UserName FROM Users WHERE Users.UserID = :modID";
			$quickstmt2 = $db->prepare($quicksql2);
			$quickstmt2->bindParam("modID", $modID);
			$quickstmt2->execute();
			$modIDRI = $quickstmt2 -> fetch(PDO::FETCH_OBJ);
			$rI2->ModName= $modIDRI->UserName;
			$rI2->CreatorName = $creatorIDRI->UserName;
			$dateplans[$count] = array();
			array_push($dateplans[$count], $rI2);

			$sql3 = "SELECT ActivityID FROM DateActivities WHERE DateActivities.DatePlanID = :dateplanid ";
			$stmt3 = $db ->prepare($sql3);
			$stmt3->bindParam("dateplanid", $dateplan);
			$stmt3 -> execute();
			$innerCount = 0;
			while($rI3 = $stmt3->fetch(PDO::FETCH_ASSOC))
			{
				//echo "\n$dateplans0";
				//echo json_encode($dateplans[1]);
				$activityID = $rI3['ActivityID'];
				//echo json_encode($dateplans[0]);
				$sql4 = "SELECT * FROM Activities WHERE ActivityID = :activityID";
				$stmt4 = $db->prepare($sql4);
				$stmt4->bindParam("activityID", $activityID);
				$stmt4->execute();
				$rI4 = $stmt4->fetch(PDO::FETCH_OBJ);
				$innerCount = $innerCount + 1;
			}
			$count = $count + 1;
		}
		echo json_encode($dateplans);
	}
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

}


/**
  * This function returns the top tags
  *
  * @param number of tags to return
  *
  * @return the tags and their quantity
  */
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

/**
  * This function returns the activity based on the tag passed in
  *
  * @param either the TagID or the TagName
  *
  * @return the activity
  */
function getTaggedActivities() {
	$app= \Slim\Slim::getInstance();
	$request =$app->request;

	$tagInfo = json_decode($request->getBody());

	//Declaring strings which may be inserted into the SQL statement before sending
	$option1 = "WHERE TagID = ";
	$option2 = "WHERE TagName = ";

	$sqlInsert1 = "";//Set to $option1 or $option2 depending on if tagID or tagName is provided
	$sqlInsert2 = "";//Only set if num is provided

	if(!empty($tagInfo->num)) {
		$num = $tagInfo->num;
		$sqlInsert2 = "
		LIMIT $num";
	}

	//Check to see if either tagID or tagName was specified
	if(!empty($tagInfo->tagID)) {
		$sqlInsert1 = $option1 . $tagInfo->tagID;
	}
	else if(!empty($tagInfo->tagName)) {
		$sqlInsert1 = $option2 . "'" . $tagInfo->tagName . "'";
	}
	else {
		echo ERROR::PARAMETERS_NOT_SET;
		exit(ERROR::PARAMETERS_NOT_SET);
	}
	
	$sql = "SELECT Activities.ActivityID, Activities.Name, Activities.Description, Activities.Cost, Activities.Location, AVG(ActivityReviews.Rating) AS Rating, COUNT(ActivityReviews.Rating) AS RatingCount
			FROM TaggedActivities NATURAL JOIN Activities NATURAL JOIN Tags
			LEFT OUTER JOIN ActivityReviews ON Activities.ActivityID = ActivityReviews.ActivityID
			$sqlInsert1
			GROUP BY Activities.ActivityID
			$sqlInsert2";


	try {
		$db = getConnection();

		$query = "SET SESSION group_concat_max_len = 10000";
		$db->query($query);
		
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
			$returnedInfo[$i]['Rating'] = $returnedInfo[$i]['Rating'] + 0;
		}

		echo json_encode($returnedInfo);
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

/**
  * This function returns the dateplan based on the tag passed in
  *
  * @param either the TagID or the TagName
  *
  * @return the dateplan
  */
function getTaggedDatePlans() {
	$app= \Slim\Slim::getInstance();
	$request =$app->request;

	$tagInfo = json_decode($request->getBody());

	//Declaring strings which may be inserted into the SQL statement before sending
	$option1 = "AND TagID = ";
	$option2 = "AND TagName = ";

	$sqlInsert1 = "";//Set to $option1 or $option2 depending on if tagID or tagName is provided
	$sqlInsert2 = "";//Only set if num is provided

	if(!empty($tagInfo->num)) {
		$num = $tagInfo->num;
		$sqlInsert2 = "
		LIMIT $num";
	}

	//Check to see if either tagID or tagName was specified
	if(!empty($tagInfo->tagID)) {
		$sqlInsert1 = $option1 . $tagInfo->tagID;
	}
	else if(!empty($tagInfo->tagName)) {
		$sqlInsert1 = $option2 . "'" . $tagInfo->tagName . "'";
	}
	else {
		echo ERROR::PARAMETERS_NOT_SET;
		exit(ERROR::PARAMETERS_NOT_SET);
	}
	
	$sql = "SELECT DatePlans.*, AVG(DatePlanReviews.Rating) AS Rating, COUNT(DatePlanReviews.Rating) AS RatingCount
			FROM TaggedActivities NATURAL JOIN Activities NATURAL JOIN DateActivities NATURAL JOIN Tags
			LEFT OUTER JOIN DatePlans ON DateActivities.DatePlanID = DatePlans.DatePlanID
			LEFT OUTER JOIN DatePlanReviews ON DatePlans.DatePlanID = DatePlanReviews.DatePlanID
			WHERE DatePlans.Public = 1
			$sqlInsert1
			GROUP BY DatePlans.DatePlanID
			$sqlInsert2";


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
			// $returnedInfo[$i]['Cost'] = $returnedInfo[$i]['Cost'] + 0.00;
			$returnedInfo[$i]['DatePlanID'] = $returnedInfo[$i]['DatePlanID'] + 0;
			$returnedInfo[$i]['Public'] = $returnedInfo[$i]['Public'] + 0;
			$returnedInfo[$i]['CreatorID'] = $returnedInfo[$i]['CreatorID'] + 0;
			$returnedInfo[$i]['ModID'] = $returnedInfo[$i]['ModID'] + 0;
			$returnedInfo[$i]['Public'] = $returnedInfo[$i]['Public'] + 0;
			$associatedActs = getAssociatedActivities($returnedInfo[$i]['DatePlanID'], 0);
			$returnedInfo[$i]['AssociatedActivities'] = $associatedActs;

		}

		echo json_encode($returnedInfo);
	}
	catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

}

/**
  * This function returns the activity reviews that a user has submitted
  *
  * @param the userID
  *
  * @return all of the activity reviews that a user has submitted
  */
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
		$sql = "SELECT ActivityReviews.*, Activities.Name FROM ActivityReviews LEFT OUTER JOIN Activities ON ActivityReviews.ActivityID = Activities.ActivityID WHERE ActivityReviews.UserID = :userID";
		$db = getConnection();
		$stmt1 = $db->prepare($sql);
		$stmt1->bindParam("userID",$userInfo->UserID);	
		$stmt1->execute();
		$returnedInfo1 = $stmt1->fetchAll(PDO::FETCH_OBJ);
		
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



/**
  * This function returns the dateplan reviews that a user has submitted
  *
  * @param the userID
  *
  * @return all of the dateplan reviews that a user has submitted
  */
function viewDatePlanReviews() {
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$userInfo = json_decode($request->getBody());
	$user_exists;

	// This will check to see if the user has an account in the database
	try 
	{
		$checksql = "SELECT UserID FROM Users WHERE UserID = :userID";
		$db = getConnection();
		$stmt = $db->prepare($checksql);
		$stmt->bindParam("userID",$userInfo->UserID);	
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo))
		{
			$user_exists = false;
			exit(ERROR::ACCOUNT_DOESNT_EXIST);
		}
		else 
		{
			$user_exists = true;
		}
		$db = null;
	}
	catch(PDOException $e) 
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	
	if ($user_exists) // pull info from database
	{
		// For DatePlan Reviews
		$sql1 = "SELECT DatePlanReviews.*, DatePlans.Name FROM DatePlanReviews LEFT OUTER JOIN DatePlans ON DatePlanReviews.DatePlanID = DatePlans.DatePlanID WHERE UserID = :userID";
		$db = getConnection();
		$stmt2 = $db->prepare($sql1);
		$stmt2->bindParam("userID",$userInfo->UserID);	
		$stmt2->execute();
		$returnedInfo2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
		if(empty($returnedInfo2))
		{
			echo ERROR::NO_RESULTS;
		} 
		else
		{
			echo json_encode($returnedInfo2);
			$db = null;
		}

	} 
	else 
	{
		//echo $userInfo->UserID;
		echo '{"error":{"text": "User does not have a profile."}}';
	}
} // end of function



/**
  * This function returns random date idea as JSON object
  *
  * @param void
  *
  * @return JSON object containing random date idea from Dateplans
  */
function getRandomIdea(){
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	//Generate random date idea from dateplans table
	$sql = "SELECT * FROM DatePlans WHERE Public=1 AND DatePlanID >= (SELECT FLOOR( MAX(DatePlanID+1) * RAND()) FROM DatePlans) ORDER BY DatePlanID LIMIT 1";
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



/**
  * This function updates a user's account in the database
  *
  * @param all of the information about a user's account
  *
  * @return nothing if successful, errors if there are any
  */
function updateAccount(){
	$app = \Slim\Slim::getInstance();
	$request = $app->request();
	$info = json_decode($request->getBody());
	// echo json_encode($info);
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
		// echo "\nPASSWORD: ";
		// echo json_encode($returnedInfoPassword->Password);
		// echo "\nSALT: ";
		// echo json_encode($returnedInfoPassword->PasswordSalt);

		$salt = $returnedInfoPassword->PasswordSalt;

		$raw_password = $info->currentPassword;
		// echo "\nCURRENT PASSWORD: " . $raw_password;
		$pw = md5($raw_password.$salt);
		// echo "\nCurrent password salted: " . json_encode($pw);
		if ($pw == $returnedInfoPassword->Password)
		{
			//echo "\nmatch found\n";
			$password_exists = true;
		}
		else{
			//echo "\nmatch not found\n";
			echo ERROR::NO_RESULTS;
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

		$username_unique;
		$checkUserNameSql = "SELECT Users.UserID FROM Users WHERE Users.UserName = :username";
		$stmtUserName = $db->prepare($checkUserNameSql);
		$stmtUserName->bindParam("username", $username);
		$stmtUserName->execute();
		$returnedInfoCheckUName = $stmtUserName->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfoCheckUName))
		{
			$username_unique = true;
		}
		else{
			if($returnedInfoCheckUName->UserID == $userID)
			{
				$username_unique = true;
			}
			else
			{
				$username_unique = false;
				echo ERROR::USERNAME_EXISTS;
				exit();
			}
		}

		$email_unique;
		$checkEmailSql = "SELECT Users.UserID FROM Users WHERE Users.Email = :email";
		$stmtEmail = $db->prepare($checkEmailSql);
		$stmtEmail->bindParam("email", $email);
		$stmtEmail->execute();
		$returnedInfoCheckEmail = $stmtEmail->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfoCheckEmail))
		{
			$email_unique = true;
		}
		else
		{
			if($returnedInfoCheckEmail->UserID == $userID)
			{
				$email_unique = true;
			}
			else{
				$email_unique = false;
				echo ERROR::EMAIL_EXISTS;
				exit();
			}
			
		}
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

		$sessionsql = "SELECT * FROM Users WHERE UserID = $userID";
		$stmt2 = $db->query($sessionsql);
		$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);
		    if(empty($returnedInfo)) {
		    	exit(ERROR::NO_RESULTS);
		    }
		    else {
			    //update session info
			    $_SESSION['UserName'] = $returnedInfo->UserName;
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
			exit(ERROR::LOGIN_FAILURE);

		}
	}
	else
	{
		exit(ERROR::ACCOUNT_DOESNT_EXIST);
	}
}

/**
  * This function returns the session info
  *
  * @param void
  *
  * @return JSON object containing all of the information stored in the session
  */
function getSessionInfo() // this will return the info stored in the session
{
	echo json_encode($_SESSION);
}

/**
  * This function updates a dateplan to be public. 
  * Only the creator of the dateplan can make it public
  *
  * @param UserID and DatePlanID
  *
  * @return JSON object containing random date idea from Dateplans
  */
function shareDatePlan() 
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$dateInfo = json_decode($request->getBody());
	
	$userID = $dateInfo->userID;
	$datePlanID = $dateInfo->datePlanID;

	try 
	{
		$sql = "UPDATE DatePlans SET Public = 1 WHERE CreatorID = $userID && DatePlanID = $datePlanID";
		$db = getConnection();
		$stmt = $db->query($sql);
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
	}
	catch(PDOException $e) 
	{
			exit('{"error":{"text":'. $e->getMessage() .'}}');
	}

}

/**
  * This function inserts a dateplan review into the database
  *
  * @param dateplan Rating, if they Attended, the Description, the DatePlanID, and the UserID
  *
  * @return success or an error
  */
function reviewDatePlan()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$datePlanInfo = json_decode($request->getBody());
	$datePlanExists;

	// this will check to see if the datePlan exists
	try
	{
		$checkDatePlanExists = "SELECT * FROM DatePlans WHERE DatePlanID = :datePlanID";
		if(isset($datePlanInfo))
		{
			// get database connection
			$db = getConnection();
			$stmt1 = $db->prepare($checkDatePlanExists);
			// bind params
			$stmt1->bindParam("datePlanID", $datePlanInfo->DatePlanID);
			$stmt1->execute();
			$returnedInfo = $stmt1->fetch(PDO::FETCH_OBJ);
			if(empty($returnedInfo))
			{
				$datePlanExists = false;
				echo ERROR::DATEPLAN_DOESNT_EXIST;
			}
			else
			{
				$datePlanExists = true;
			}
			$db = null;
		}
		else
		{
			echo ERROR::JSON_ERROR;
		}
	}
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

	// if the datePlan exists, insert the review into the table
	if($datePlanExists)
	{
		// insert the review into the table
		$sql = "INSERT INTO DatePlanReviews (Rating, Attended, Description, DatePlanID, UserID, ReviewTime) VALUES(:rating, :attended, :description, :dateplanID, :userID, NOW())";
		try
		{
			if(isset($datePlanInfo)) 
			{
				// Get database connection
				$db = getConnection();
				$stmt = $db->prepare($sql);
				// bind params
				$stmt->bindParam("rating",$datePlanInfo->Rating);
				$stmt->bindParam("attended", $datePlanInfo->Attended);
				$stmt->bindParam("description", $datePlanInfo->Description);
				$stmt->bindParam("dateplanID", $datePlanInfo->DatePlanID);
				$stmt->bindParam("userID", $datePlanInfo->UserID);
				$stmt->execute();
				echo ERROR::SUCCESS;
			}
			else 
			{
				echo ERROR::JSON_ERROR;
			}
		}		    
		catch(PDOException $e) 
		{
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	}		
} // end of function
	


/**
  * A function to let the user update a dateplan.
  *
  * @param DatePlanName, isPublic, updater ID, DatePlanID, and associated activities
  *
  * @return result of success or not in updating date plan
  */
function updateDatePlan()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$datePlanInfo = json_decode($request->getBody());

	if (isset($_SESSION['UserID'])) {
    	$uID=$_SESSION['UserID'];
    
	try
	{
		if(isset($datePlanInfo)) 
		{
			// Get database connection
			$db = getConnection();
			// UPDATE SQL Statement
			$sql = "UPDATE DatePlans SET Name = :name, 
            Public = :isPublic, ModID = :modID, Timestamp = NOW() WHERE DatePlanID = :datePlanID";
			$stmt = $db->prepare($sql);
			// bind params
			$stmt->bindParam("name",$datePlanInfo->Name);
			$stmt->bindParam("isPublic", $datePlanInfo->Public);
			//Assign the new modified id to the currently logged in user
			$stmt->bindParam("modID", $uID);
			$stmt->bindParam("datePlanID", $datePlanInfo->DatePlanID);
			$stmt->execute();

			//Updated dateplan info now we need to update the date activities table
			//DELETE SQL Statement
			$deleteQuery="DELETE FROM DateActivities WHERE DatePlanID=:datePlanID";
			$stmt = $db->prepare($deleteQuery);
			// bind params
			$stmt->bindParam("datePlanID", $datePlanInfo->DatePlanID);
			$stmt->execute();

			$insertDateActivities = "INSERT INTO DateActivities (DatePlanID,ActivityID) Values(:datePlanID,:activityID)";
			$stmt = $db->prepare($insertDateActivities);
			//Loop through the user's selected activities array
			foreach ($datePlanInfo->Activites as $activity) {
				//bind params
				$stmt->bindParam("datePlanID", $datePlanInfo->DatePlanID);
				$stmt->bindParam("activityID", $activity);
				$stmt->execute();
			}
			//Return success if updated date plan
			echo ERROR::SUCCESS;
		}
		else 
		{
			echo ERROR::JSON_ERROR;
		}
	}		    
	catch(PDOException $e) 
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}	
}else
	//Not logged in so return false
	echo 0;
} // end of function



/**
  * This function inserts an activity review into the database
  *
  * @param the activity Rating, the Cost, if they Attended, the Description, the ActivityID, and the UserID
  *
  * @return success or an error
  */
function reviewActivity()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$activityInfo = json_decode($request->getBody());
	$activityExists;

	// this will check to see if the activity exists
	$checkActivityExists = "SELECT * FROM Activities WHERE ActivityID = :activityID";
	try
	{
		if(isset($activityInfo))
		{
			// get database connection
			$db = getConnection();
			$stmt1 = $db->prepare($checkActivityExists);
			// bind params
			$stmt1->bindParam("activityID", $activityInfo->ActivityID);
			$stmt1->execute();
			$returnedInfo = $stmt1->fetch(PDO::FETCH_OBJ);
			if(empty($returnedInfo))
			{
				$activityExists = false;
				echo ERROR::ACTIVITY_DOESNT_EXIST;
			}
			else
			{
				$activityExists = true;
			}
			$db = null;
		}
		else
		{
			echo ERROR::JSON_ERROR;
		}
	}
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}


	if($activityExists) // insert the review into the table
	{
		// make sql statement
		$sql = "INSERT INTO ActivityReviews (Rating, UserID, ActivityID, Description, Cost, Attended, ReviewTime) VALUES(:rating, :userID, :activityID, :description, :cost, :attended, NOW())";
		try
		{
			if(isset($activityInfo)) 
			{
				// Get database connection
				$db = getConnection();
				$stmt = $db->prepare($sql);
				// bind params
				$stmt->bindParam("rating",$activityInfo->Rating);
				$stmt->bindParam("userID", $activityInfo->UserID);
				$stmt->bindParam("activityID", $activityInfo->ActivityID);
				$stmt->bindParam("description", $activityInfo->Description);
				$stmt->bindParam("cost", $activityInfo->Cost);
				$stmt->bindParam("attended", $activityInfo->Attended);
				$stmt->execute();
				echo ERROR::SUCCESS;
			}
			else 
			{
				echo ERROR::JSON_ERROR;
			}
		}		    
		catch(PDOException $e) 
		{
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	}
} // end of function


/**
  * This function returns the recovery question for a specific user
  *
  * @param user's email
  *
  * @return the security question
  */
function recoveryQuestion() {
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$recoveryInfo = json_decode($request->getBody());
	
	$email = $recoveryInfo->email;

	$sql = "SELECT SecurityQuestion FROM Users WHERE Email = :email";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("email", $recoveryInfo->email);
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(!empty($returnedInfo)) {	
			echo json_encode($returnedInfo);
		}
		else {
			echo ERROR::NO_RESULTS;
		}
	}
	catch(PDOException $e) 
	{
		exit('{"error":{"text":'. $e->getMessage() .'}}');
	}

}

/**
  * This function recovers a user's password
  *
  * @param user's email, user's recovery question
  *
  * @return success or errors
  */
function recoverPassword() 
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$recoveryInfo = json_decode($request->getBody());
	
	$email = $recoveryInfo->email;
	$securityQuestion = $recoveryInfo->securityQuestion;

	$securitySalt = sha1(md5($recoveryInfo->securityAnswer));
	$securityAnswer = md5($recoveryInfo->securityAnswer.$securitySalt);

	$sql = "SELECT UserID FROM Users WHERE Email = :email AND SecurityAnswer = :securityAnswer";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("email", $email);
		$stmt->bindParam("securityAnswer", $securityAnswer);
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);	
		if(!empty($returnedInfo)) {
			echo ERROR::SUCCESS;
		}
		else {
			echo ERROR::NO_RESULTS;
		}
	}
	catch(PDOException $e) 
	{
		exit('{"error":{"text":'. $e->getMessage() .'}}');
	}

}


/**
  * This function resets a user's password
  *
  * @param user's email, security answer, and the new password
  *
  * @return success or errors
  */
function resetPassword() 
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$recoveryInfo = json_decode($request->getBody());

	if(!empty($recoveryInfo->newPassword)) {
		
		$email = $recoveryInfo->email;

		$securitySalt = sha1(md5($recoveryInfo->securityAnswer));
		$securityAnswer = md5($recoveryInfo->securityAnswer.$securitySalt);

		$passwordSalt = sha1(md5($recoveryInfo->newPassword));
		$newPassword = md5($recoveryInfo->newPassword.$passwordSalt);

		$sql = "UPDATE Users SET Password = :newPassword, PasswordSalt = :passwordSalt WHERE Email = :email AND SecurityAnswer = :securityAnswer";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("email", $email);
			$stmt->bindParam("securityAnswer", $securityAnswer);
			$stmt->bindParam("newPassword", $newPassword);
			$stmt->bindParam("passwordSalt", $passwordSalt);
			$stmt->execute();

			$sql2 = "SELECT UserID FROM Users WHERE Email = :email AND Password = :newPassword";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("email", $email);
			$stmt2->bindParam("newPassword", $newPassword);
			$stmt2->execute();
			$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);	
			if(!empty($returnedInfo)) {
				echo ERROR::SUCCESS;
			}
			else {
				echo ERROR::LOGIN_FAILURE;
			}
		}
		catch(PDOException $e) 
		{
			exit('{"error":{"text":'. $e->getMessage() .'}}');
		}
	}
	else {
		echo ERROR::PARAMETERS_NOT_SET;
	}

}


/**
  * This function adds a photo to a user's profile
  *
  * @param the filepath to the photo to upload
  *
  * @return success or errors
  */
function addPhoto() {
	$target_dir = "../img/user/";
	$_FILES["fileToUpload"]["name"] = $_POST["userID"] . '.' . pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	// Check if image file is a actual image
	if(isset($_POST["submit"])) {
	    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    if($check !== false) {
	        $uploadOk = 1;
	    } else {
	        $uploadOk = 0;
	    }
	}

	//Check if file already exists
	if (glob($target_dir . $_POST["userID"] . ".*")) {
	    $deleteOld = 1;
	}
	else {
		$deleteOld = 0;
	}

	if((strcasecmp($imageFileType, "jpg") != 0) && (strcasecmp($imageFileType, "jpeg") != 0) && (strcasecmp($imageFileType, "png") != 0) && (strcasecmp($imageFileType, "gif") != 0) && (strcasecmp($imageFileType, "bmp") != 0)) {
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo ERROR::UPLOAD_ERROR;
	} 
	else {
		//if old file exists, remove it
		if($deleteOld == 1) {
			unlink(glob($target_dir . $_POST["userID"] . ".*")[0]);
		}
		//add file to folder
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        echo basename($_FILES["fileToUpload"]["name"]);
	    } 
	    else {
	        echo ERROR::UPLOAD_ERROR;
	    }
	}

}


/**
  * This function returns the user's photo for their profile
  *
  * @param userID
  *
  * @return the filename of the photo
  */
function getPhoto($userID) {
	$app = \Slim\Slim::getInstance();
	$target_dir = "../img/user/";

	//if the user has uploaded a custom photo, return the file name of that photo
	if (glob($target_dir . $userID . ".*")) {
	    echo basename(glob($target_dir . $userID . ".*")[0]);
	}
	else {
		echo ERROR::NO_RESULTS;
	}
}


/** This function deletes a dateplan from the database
  *
  * @param the userID and the dateplanID
  *
  * @return success or an error
  */
function deleteDatePlan() 
{
	// GUI will send the userID and the DatePlanID
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$datePlanInfo = json_decode($request->getBody());
	
    if (isset($datePlanInfo))
    {
	    try
	    {
	        // only the creator of the dateplan will be able to delete it
			$deleteQuery = "DELETE FROM DatePlans WHERE DatePlanID =:dateplanID AND CreatorID=:userID";
	        $db = getConnection();
	        $stmt = $db->prepare($deleteQuery);
	        $stmt->bindParam("dateplanID", $datePlanInfo->DatePlanID);
	        $stmt->bindParam("userID", $datePlanInfo->UserID);
	        $stmt->execute();
	        
	        if($stmt->rowCount()>0) // if more than 0 rows are effected, success reached
	        {
	        	echo ERROR::SUCCESS;
	    	}
	    	else
	    	{
	    		// no rows were deleted
	    		echo ERROR::NO_RESULTS;
	    	}
	        $db = null;
	    }
	    catch(PDOException $e) 
	    {
	        echo '{"error":{"text":'. $e->getMessage() .'}}';
	    }
	}
	else
	{
		echo ERROR::JSON_ERROR;
	}
} // end of function


/**
  * This function returns deletes an activity from a dateplan
  *
  * @param datePlanID, ActivityID
  *
  * @return success or errors
  */
function deleteDateActivity($datePlanID, $activityID) 
{
    $deleteQuery = "DELETE FROM DateActivities WHERE DatePlanID = :datePlanID AND ActivityID = :activityID";
  
    try {
        $db = getConnection();
        $stmt = $db->prepare($deleteQuery);
        $stmt->bindParam("datePlanID", $datePlanID); 
        $stmt->bindParam("activityID", $activityID);
        $stmt->execute();
        if($stmt->rowCount()>0){
        echo ERROR::SUCCESS;
    	}
    	else{
    		echo ERROR::NO_RESULTS;
    	}
        $db = null;
    } catch(PDOException $e) {
    	echo ERROR::NO_RESULTS;
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
} // end of function


/**
  * This function creates a dateplan and inserts it into the database
  *
  * @param all of the infor about the dateplan
  *
  * @return success or errors
  */
function createDatePlan()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request();
	$info = json_decode($request->getBody());
	$activity_exists;
	$user_exists;
	//$dateplexists = false;an_
	try{
		$userID = $info->UserID;
		//$dateplanName = $info ->Name;
		$db=getConnection();
		$checkUser = "SELECT * FROM Users WHERE UserID = :userID";
		$stmtcheckdp =  $db->prepare($checkUser);
		$stmtcheckdp ->bindParam("userID", $userID);
		$stmtcheckdp->execute();
		$RICHECKDP = $stmtcheckdp->fetch(PDO::FETCH_OBJ);
		//echo json_encode($RICHECKDP);
		if(empty($RICHECKDP))
		{
			$user_exists = false;
		}
		else
		{
			$user_exists = true;
		}
		$db= null;
	}
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

	if($user_exists == true)
	{
		$userID = $info->UserID;
		$dateplanName = $info->Name;
		$datePlanSQL = "INSERT INTO DatePlans(Name, CreatorID, ModID, Timestamp ) VALUES (:name, :userID1, :userID2, NOW())";
		$db=getConnection();
		$stmt = $db->prepare($datePlanSQL);
		//$stmt->bindParam("dateplanID", $dateplanID);
		$stmt ->bindParam("name", $dateplanName);
		$stmt->bindParam("userID1", $userID);
		$stmt->bindParam("userID2", $userID);

		$stmt->execute();
		echo "EXECUTED\n";
		echo ERROR::SUCCESS;
		$db = null;
		$db = getConnection();
		$sqlDPID = "SELECT DatePlanID FROM DatePlans WHERE DatePlans.Name= :name AND CreatorID = :userID1 AND ModID = :userID2";
		$stmtDPID = $db->prepare($sqlDPID);
		$stmtDPID ->bindParam("name", $dateplanName);
		$stmtDPID->bindParam("userID1", $userID);
		$stmtDPID->bindParam("userID2", $userID);
		$stmtDPID->execute();
		$returnedInfoDPID = $stmtDPID->fetch(PDO::FETCH_OBJ);
		echo json_encode($returnedInfoDPID);
	}
	else{
		echo "User Does Not Exist\n";
		echo ERROR::ACCOUNT_DOESNT_EXIST;
	}
}


/**
  * This function adds an activity to a dateplan
  *
  * @param dateplanID, activityID
  *
  * @return success or errors
  */
function addActivity()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$info = json_decode($request->getBody());
	$dateplan_exists;
	try
	{
		$datePlanID = $info->DatePlanID;
		$checkDatePlanExists ="SELECT * FROM DatePlans WHERE DatePlanID = :datePlanID";
		$db = getConnection();
		$stmt  = $db->prepare($checkDatePlanExists);
		$stmt->bindParam("datePlanID", $datePlanID);
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if(empty($returnedInfo))
			{
				$dateplan_exists = false;
				echo ERROR::DATEPLAN_DOESNT_EXIST;
				exit(1);
			}
			else
			{
				$dateplan_exists = true;
			}
		$db = null;
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	if ($dateplan_exists == true)
	{
		$activityID = $info->ActivityID;
		try{
			$sql = "SELECT * FROM Activities WHERE ActivityID = :activityID";
			$db = getConnection();
			$stmt2 = $db->prepare($sql);
			$stmt2->bindParam("activityID", $activityID);
			$stmt2->execute();
			$returnedActivityInfo = $stmt2->fetch(PDO::FETCH_OBJ);	
			echo json_encode($returnedActivityInfo);
			//CHECK IF ACTIVTY ALREADY EXISTS IN THE DATEBASE
			$activityExists;
			if(empty($returnedActivityInfo))
			{
				$activityExists = false;
				echo "ACTIVTY DOES NOT EXIST IN DB\n";
			}
			else {
				$activityExists = true;
			}

		}
		catch(PDOException $e){
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	else{
		echo ERROR::DATEPLAN_DOESNT_EXIST;
		exit(1);
	}
	if($activityExists == true)
	{
		$insertDateActivities = "INSERT INTO DateActivities(DatePlanID, ActivityID) VALUES (:dateplanID, :activityID) ";
		$stmtInsertDA = $db->prepare($insertDateActivities);
		$stmtInsertDA->bindParam("dateplanID", $datePlanID);
		$stmtInsertDA->bindParam("activityID", $activityID);
		$stmtInsertDA->execute();
	}
	else{
		echo "activity doesn't exist";
	}	
}


/**
  * This function updates a dateplan's description
  *
  * @param userID, description, datePlanID
  *
  * @return success or errors
  */
function updateDatePlanDescription()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$info = json_decode($request->getBody());
	$dateplan_exists;
	try
	{	
		$dateplanID = $info->DatePlanID;
		$checkDPExists = "SELECT * FROM DatePlans WHERE DatePlanID = :dateplanID";
		$db= getConnection();
		$stmt = $db->prepare($checkDPExists);
		$stmt->bindParam("dateplanID", $dateplanID);
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if (empty($returnedInfo))
		{
			echo ERROR::DATEPLAN_DOESNT_EXIST;
			$dateplan_exists = false;
		}
		else
		{
			$dateplan_exists = true;
		}
		$db = null;

	}
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	if($dateplan_exists == false)
	{
		exit(1);
	}
	else
	{
		$updatesql = "UPDATE DatePlans SET DatePlans.Description = :description WHERE DatePlans.CreatorID = :userID AND DatePlans.DatePlanID = :dateplanid";
		$db = getConnection();
		$stmtUpdateDesc = $db->prepare($updatesql);
		$stmtUpdateDesc->bindParam("description", $info->Description);
		$stmtUpdateDesc->bindParam("dateplanid",$dateplanID );
		$stmtUpdateDesc->bindParam("userID", $info->UserID);
		$stmtUpdateDesc->execute();
		$db=null;
		$db = getConnection();
		$checkSQL = "SELECT * FROM DatePlans WHERE DatePlans.Description = :description AND DatePlans.DatePlanID = :dateplanid AND DatePlans.CreatorID = :userID";
		$stmtCheckSQL = $db->prepare($checkSQL);
		$stmtCheckSQL->bindParam("description", $info->Description);
		$stmtCheckSQL->bindParam("dateplanid",$dateplanID );
		$stmtCheckSQL->bindParam("userID", $info->UserID);
		$stmtCheckSQL->execute();
		$rICheck = $stmtCheckSQL->fetch(PDO::FETCH_OBJ);
		if(empty($rICheck))
		{
			echo ERROR::JSON_ERROR;
		}
		else
		{
			echo ERROR::SUCCESS;
		}
	}
}


/**
  * This function updates a dateplan's name
  *
  * @param userID, datePlanName, datePlanID
  *
  * @return success or errors
  */
function updateDatePlanName()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$info = json_decode($request->getBody());
	$dateplan_exists;
	try
	{	
		$dateplanID = $info->DatePlanID;
		$checkDPExists = "SELECT * FROM DatePlans WHERE DatePlanID = :dateplanID";
		$db= getConnection();
		$stmt = $db->prepare($checkDPExists);
		$stmt->bindParam("dateplanID", $dateplanID);
		$stmt->execute();
		$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
		if (empty($returnedInfo))
		{
			echo ERROR::DATEPLAN_DOESNT_EXIST;
			$dateplan_exists = false;
		}
		else
		{
			$dateplan_exists = true;
		}
		$db = null;

	}
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	if($dateplan_exists == false)
	{
		exit(1);
	}
	else
	{
		$updatesql = "UPDATE DatePlans SET DatePlans.Name = :Name WHERE DatePlans.CreatorID = :userID AND DatePlans.DatePlanID = :dateplanid";
		$db = getConnection();
		$stmtUpdateDesc = $db->prepare($updatesql);
		$stmtUpdateDesc->bindParam("Name", $info->Name);
		$stmtUpdateDesc->bindParam("dateplanid",$dateplanID );
		$stmtUpdateDesc->bindParam("userID", $info->UserID);
		$stmtUpdateDesc->execute();
		$db=null;
		$db = getConnection();
		$checkSQL = "SELECT * FROM DatePlans WHERE DatePlans.Name = :Name AND DatePlans.DatePlanID = :dateplanid AND DatePlans.CreatorID = :userID";
		$stmtCheckSQL = $db->prepare($checkSQL);
		$stmtCheckSQL->bindParam("Name", $info->Name);
		$stmtCheckSQL->bindParam("dateplanid",$dateplanID );
		$stmtCheckSQL->bindParam("userID", $info->UserID);
		$stmtCheckSQL->execute();
		$rICheck = $stmtCheckSQL->fetch(PDO::FETCH_OBJ);
		echo json_encode($rICheck);
		if(empty($rICheck))
		{
			echo "DID NOT UPDATE";
		}
		else
		{
			echo ERROR::SUCCESS;
		}
	}
}


/**
  * This function adds a tag to an activity
  *
  * @param TagID or TagName, activityID
  *
  * @return success or errors
  */
function addTagToActivity() 
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$info = json_decode($request->getBody());
	
	$ActivityIsAlreadyTagged = false;
	$isName = false;

	if(!empty($info->TagID)) 
	{
		$tagID = $info->TagID;
	}
	else if(!empty($info->TagName)) 
	{
		$tagName = $info->TagName;
		$isName = true;
	}
	$activityID = $info->ActivityID;

	try 
	{
		if($isName) 
		{
			$checknamesql = "SELECT TagID FROM Tags WHERE TagName = '$tagName'";
			$db = getConnection();
			$stmtname = $db->query($checknamesql);
			$tagInfo = $stmtname->fetch(PDO::FETCH_OBJ);
			if(!empty($tagInfo)) 
			{
				$tagID = $tagInfo->TagID;
			}
			else // if the tag does not exist, create it in the DB
			{
				$tagsql = "INSERT INTO Tags (TagName) VALUES ('$tagName')";
				$db = getConnection();
				$stmttag = $db->query($tagsql);

				$checknamesql2 = "SELECT TagID FROM Tags WHERE TagName = '$tagName'";
				$stmttagid = $db->query($checknamesql2);
				$tagInfo = $stmttagid->fetch(PDO::FETCH_OBJ);
				if(!empty($tagInfo))
				{
					$tagID = $tagInfo->TagID;
				}
				else 
				{
					echo ERROR::JSON_ERROR;
					exit(ERROR::JSON_ERROR);
				}
			}
		}

		// This will check to see if the Activity is already tagged with the tag
		try
		{
			$testSQL = "SELECT * FROM TaggedActivities WHERE ActivityID = :activityID AND TagID = :tagID";
			$db = getConnection();
			$stmt = $db->prepare($testSQL);
			$stmt->bindParam("activityID", $activityID);
			$stmt->bindParam("tagID", $tagID);
			$stmt->execute();
			$returnedInfo = $stmt->fetch(PDO::FETCH_OBJ);
			if (!empty($returnedInfo)) // if the activity is already tagged with the tag
			{
				$ActivityIsAlreadyTagged = true;
			}
		}
		catch(PDOException $e) 
		{
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 		
		}

		if (!$ActivityIsAlreadyTagged) // if the activity is not already tagged, insert it into the DB
		{
			$sqlfinal = "INSERT INTO TaggedActivities (TagID, ActivityID) VALUES ($tagID, $activityID)";
			$db = getConnection();
			$stmtfinal = $db->query($sqlfinal);

			$checksql = "SELECT * FROM TaggedActivities WHERE TagID = $tagID AND ActivityID = $activityID";
			$stmt2 = $db->query($checksql);
			$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);
			if(!empty($returnedInfo))
			{
				echo ERROR::SUCCESS;
			}
			else
			{
				echo ERROR::NO_RESULTS;	
			}	
		}
		else
		{
			echo ERROR::ACTIVITY_ALREADY_TAGGED;
		}				
	}
	catch(PDOException $e) 
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}// end of function




?>