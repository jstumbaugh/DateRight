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
	 ACTIVITY_EXISTS = 600,
	 USERNAME_EXISTS = 700,
	 EMAIL_EXISTS = 800;
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
$app->delete('/deleteFavorite/:id', 'deleteFavorite');
$app->post('/searchActivities', 'searchActivities');
$app->post('/viewActivityReviews', 'viewActivityReviews');
$app->post('/viewDatePlanReviews', 'viewDatePlanReviews');
$app->get('/topTags', 'topTags');
$app->get('/getTaggedActivities', 'getTaggedActivities');
$app->get('/getRandomIdea', 'getRandomIdea');
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

//Deletes session for user upon clicking logout
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
	       		$secSalt = sha1(md5($userInfo->securityAnswer));
	       		$secAnswer = md5(($userInfo->securityAnswer).$secSalt);
				
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
			    //logUser($userInfo);
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
			$sql2 =" SELECT Activities.ActivityID,Activities.Name, Activities.Description, Activities.Cost, Activities.Rating, Activities.Location FROM Activities WHERE Activities.ActivityID = :activityID";
			$stmt2 = $db->prepare($sql2);
			$stmt2->bindParam("activityID", $activity);
			$stmt2->execute();
			$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
			array_push($favorites, $rI2);
		}
		else{
		$sql2 = "SELECT Activities.ActivityID,Activities.Name, Activities.Description, Activities.Cost, Activities.Rating, Activities.Location, DatePlans.Name, DatePlans.CreatorID, DatePlans.Description FROM Activities, DatePlans WHERE Activities.ActivityID = :activityID AND DatePlans.DatePlanID = :dateplanid";
		$stmt2 = $db ->prepare($sql2);
		$stmt2 -> bindParam("activityID", $activity);
		$stmt2 -> bindParam("dateplanid", $dateplan);
		$stmt2->execute();
		$rI2 = $stmt2->fetch(PDO::FETCH_ASSOC);
	 	//$favorites['activityID'] = $rI2['ActivityID'];
		// $favorites['dateplanid'] = $rI2['DatePlanID'];
		array_push($favorites, $rI2);
	}
}

	//Type-casting integers before returning them
	for($i = 0; $i < sizeof($favorites); $i = $i + 1) {
		$favorites[$i]['Cost'] = $favorites[$i]['Cost'] + 0;
	}
	
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
/**
     * Function to delete a favorite by id from the favorites table. Called with a DELETE request
     * @param int $id the favorite id
     * @return success or not in deleting favorite
     */
function deleteFavorite($id) {
    $deleteQuery = "DELETE FROM Favorites WHERE ActivityID =:actID and UserID=:userID";
    if (isset($_SESSION['UserID'])) {
    	$uID=$_SESSION['UserID'];
    try {
        $db = getConnection();
        $stmt = $db->prepare($deleteQuery);
        $stmt->bindParam("actID", $id);
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
//Search by activity name, works with multiple word query as well 
//@return echo response with result JSON
function searchActivities (){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	//User search query
	//$result = $app->request()->post('datesearch');
	$json = json_decode($request->getBody());
	$result = $json->SearchQuery;
	//First check if they sent any query
	if (!empty($result)) {
		// $words = explode("+",trim($result));
		// $sql = "SELECT * FROM Activities WHERE name LIKE '%$result%' OR description LIKE '%$result%'";
		// //Process over all entered keywords
		// foreach ($words as $term) {
		// 	$sql.=" OR name LIKE '%$term%' OR description LIKE '%$term%' ";
		// }
	$sql="SELECT * FROM Activities WHERE MATCH(Name,Description,Location) AGAINST(:searchQuery IN BOOLEAN MODE)";
	//Order results by the user rating
	$sql.=" ORDER BY Rating LIMIT 50";
	$db = getConnection();
	$stmt = $db->prepare($sql);
	//accept plural version e.g. movie(s)
	$result.="*";
	echo "RESL $result";
	$stmt->bindParam("searchQuery", $result);
	$stmt ->execute();
	$searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo '{"results": ' . json_encode($searchResults) . '}';
	}else{
	//No activities found w/ that query
	echo ERROR::NO_RESULTS;
		}
}
function viewUserDatePlans(){
	$app= \Slim\Slim::getInstance();
	$request =$app->request;
	$sql = "SELECT * FROM DatePlans WHERE CreatorID = :userID";
	$userInfo = json_decode($request->getBody());
	$userID = $userInfo->UserID; 
	$db = getConnection();
	$dateplans = array();

	try{
	$stmt = $db->prepare($sql);
	$stmt->bindParam("userID", $userID);
	$stmt ->execute();

	if(empty($dateplans))
	{
		echo '{"error":{"text":' . $e->getMessage() . '}}';
		exit(1);
	}
	while($returnedInfo1 = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		
	$dateplan = $returnedInfo1['DatePlanID'];
	$sql2 = "SELECT Users.UserName, DatePlans.Name, DatePlans.Description FROM DatePlans, Users WHERE DatePlans.DatePlanID = :dateplanid ";
	$stmt2 = $db->prepare($sql2);
	$stmt2->bindParam("dateplanid", $userInfo->DatePlanID);
	$stmt2->execute();
	$rI2 = $stmt2 ->fetch(PDO::FETCH_ASSOC);
	array_push($dateplans, $rI2);

	}

	echo json_encode($dateplans);
	}
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
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
	$sql = "SELECT * FROM DatePlans WHERE dateplanid >= (SELECT FLOOR( MAX(dateplanid) * RAND()) FROM DatePlans ) ORDER BY dateplanid LIMIT 1";
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


function getSessionInfo() // this will return the info stored in the session
{
	echo json_encode($_SESSION);
}

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

function createDatePlan()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request();
	$info = json_decode($request->getBody());
	$activity_exists;
	$user_exists;
	try{
		$name = $info->Name;
		$public = $info->Public;
		//$public = 0;
		$creatorID= $info->UserID;
		$description = $info->Description;
		$time = $info->Timestamp;




	}
	catch (PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	

}

// insert the Review into the database
function reviewDatePlan()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$datePlanInfo = json_decode($request->getBody());

	// make sql statement
	$sql = "INSERT INTO DatePlanReviews (Rating, Attended, Description, DatePlanID, UserID, ReviewTime) Values(:rating, :attended, :description, :dateplanID, :userID, NOW())";
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
} // end of function
	
/**
     * A function to let the user update a dateplan.  
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

// insert the Review into the database
function reviewActivity()
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$activityInfo = json_decode($request->getBody());

	// make sql statement
	$sql = "INSERT INTO ActivityReviews (Rating, UserID, ActivityID, Description, Cost, Location, Attended, ReviewTime) 
	Values(:rating, :userID, :activityID, :description, :cost, :location, :attended, NOW())";
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
			$stmt->bindParam("location", $activityInfo->Location);
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
} // end of function

function recoveryQuestion() {
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$recoveryInfo = json_decode($request->getBody());
	
	$email = $recoveryInfo->email;
	//require more than just email to get question?

	$sql2 = "SELECT SecurityQuestion FROM Users WHERE Email = :email";
	try {
		$db = getConnection();
		$stmt2 = $db->prepare($sql2);
		$stmt2->bindParam("email", $userInfo->email);
		$stmt2->execute();
		$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);	
		echo $returnedInfo;
	}
	catch(PDOException $e) 
	{
		exit('{"error":{"text":'. $e->getMessage() .'}}');
	}

}


function recoverPassword() 
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$recoveryInfo = json_decode($request->getBody());
	
	$email = $recoveryInfo->email;
	$securityQuestion = $recoveryInfo->securityQuestion;

	$securitySalt = sha1(md5($recoveryInfo->securityAnswer));
	$securityAnswer = md5($recoveryInfo->securityAnswer.$securitySalt);

	$sql2 = "SELECT * FROM Users WHERE Email = :email AND SecurityAnswer = :securityAnswer";
	try {
		$db = getConnection();
		$stmt2 = $db->prepare($sql2);
		$stmt2->bindParam("email", $userInfo->email);
		$stmt2->bindParam("securityAnswer", $pw);
		$stmt2->execute();
		$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);	
	}
	catch(PDOException $e) 
	{
		exit('{"error":{"text":'. $e->getMessage() .'}}');
	}

}

function resetPassword() 
{
	$app = \Slim\Slim::getInstance();
	$request = $app->request;
	$recoveryInfo = json_decode($request->getBody());
	
	$email = $recoveryInfo->email;
	$securityQuestion = $recoveryInfo->securityQuestion;

	$securitySalt = sha1(md5($recoveryInfo->securityAnswer));
	$securityAnswer = md5($recoveryInfo->securityAnswer.$securitySalt);

	$passwordSalt = sha1(md5($recoveryInfo->newPassword));
	$newPassword = md5($recoveryInfo->newPassword.$passwordSalt);

	$sql2 = "SELECT * FROM Users WHERE Email = :email AND SecurityAnswer = :securityAnswer";
	try {
		$db = getConnection();
		$stmt2 = $db->prepare($sql2);
		$stmt2->bindParam("email", $userInfo->email);
		$stmt2->bindParam("securityAnswer", $pw);
		$stmt2->execute();
		$returnedInfo = $stmt2->fetch(PDO::FETCH_OBJ);
		//TO DO: actually update password	
	}
	catch(PDOException $e) 
	{
		exit('{"error":{"text":'. $e->getMessage() .'}}');
	}

}


?>