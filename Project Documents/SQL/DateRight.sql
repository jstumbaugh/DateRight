--
-- Database: `DateRight`
--
DROP DATABASE IF EXISTS DateRight;
CREATE DATABASE IF NOT EXISTS DateRight;
USE DateRight;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `UserName` varchar(50) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(64) DEFAULT NULL,
  `PasswordSalt` varchar(50) DEFAULT NULL,
  `UserType` int NOT NULL,
  `Sex` int NOT NULL,
  `SecurityQuestion` int NOT NULL,
  `SecurityAnswer` varchar(50) NOT NULL,
  `SecuritySalt` varchar(50) NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Table structure for table `Activities`
--

CREATE TABLE IF NOT EXISTS `Activities` (
  `ActivityID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Cost` double DEFAULT NULL,
  `Rating` double DEFAULT NULL,
  `Location` varchar(200) NOT NULL,
  PRIMARY KEY (`ActivityID`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `ActivitiesAttendees`
--

CREATE TABLE IF NOT EXISTS `ActivitiesAttendees` (
  `ActivitiesAttendeesID` int NOT NULL AUTO_INCREMENT,
  `ActivityID` int NOT NULL,
  `UserID` int NOT NULL,
  CONSTRAINT FOREIGN KEY (`ActivityID`) REFERENCES `Activities` (`ActivityID`),
  CONSTRAINT FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  PRIMARY KEY (`ActivitiesAttendeesID`)
) ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Table structure for table `ActivityReviews`
--

CREATE TABLE IF NOT EXISTS `ActivityReviews` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `Rating` int NOT NULL,
  `UserID` int NOT NULL,
  `ActivityID` int NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Cost` double DEFAULT NULL,
  `Location` varchar(200) NOT NULL,
  `Attended` BOOLEAN NOT NULL,
  `ReviewTime` datetime NOT NULL,
  CONSTRAINT FOREIGN KEY (`ActivityID`) REFERENCES `Activities` (`ActivityID`),
  CONSTRAINT FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  PRIMARY KEY (`ReviewID`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE IF NOT EXISTS `Tags` (
  `TagID` int NOT NULL AUTO_INCREMENT,
  `TagName` varchar(50) NOT NULL,
  PRIMARY KEY (`TagID`)
) ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Table structure for table `Tagged Activities`
--

CREATE TABLE IF NOT EXISTS `TaggedActivities` (
  `TagID` int NOT NULL AUTO_INCREMENT,
  `ActivityID` int NOT NULL,
  CONSTRAINT FOREIGN KEY (`TagID`) REFERENCES `Tags` (`TagID`),
  CONSTRAINT FOREIGN KEY (`ActivityID`) REFERENCES `Activities` (`ActivityID`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `DatePlans`
--

CREATE TABLE IF NOT EXISTS `DatePlans` (
  `DatePlanID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Public` BOOLEAN NOT NULL DEFAULT 0,
  `CreatorID` int NOT NULL,
  `ModID` int DEFAULT NULL,
  `Timestamp` datetime NOT NULL,
  `Description` varchar(255) NOT NULL,
  CONSTRAINT FOREIGN KEY (`CreatorID`) REFERENCES `Users` (`UserID`),
  CONSTRAINT FOREIGN KEY (`ModID`) REFERENCES `Users` (`UserID`),
  PRIMARY KEY (`DatePlanID`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `Date Plan Reviews`
-- 
-- 

CREATE TABLE IF NOT EXISTS `DatePlanReviews` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `Rating` int NOT NULL,
  `Attended` BOOLEAN NOT NULL,
  `Description` varchar(200) NOT NULL,
  `DatePlanID` int NOT NULL ,
  `UserID` int NOT NULL,
  `ReviewTime` datetime NOT NULL,
  CONSTRAINT FOREIGN KEY (`TagID`) REFERENCES `Tags` (`TagID`),
  CONSTRAINT FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
   CONSTRAINT FOREIGN KEY (`DatePlanID`) REFERENCES `DatePlans` (`DatePlanID`),
  PRIMARY KEY (`ReviewID`)
) ENGINE=InnoDB;


-- --------------------------------------------------------
--
-- Table structure for table `Date Plan Attendees`
-- 
-- 

CREATE TABLE IF NOT EXISTS `DatePlanAttendees` (
  `DatePlanAttendeesID` int NOT NULL AUTO_INCREMENT,
  `DatePlanID` int NOT NULL,
  `UserID` int NOT NULL,
  CONSTRAINT FOREIGN KEY (`DatePlanID`) REFERENCES `DatePlans` (`DatePlanID`),
  CONSTRAINT FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  PRIMARY KEY (`DatePlanAttendeesID`)
) ENGINE=InnoDB;



-- --------------------------------------------------------
--
-- Table structure for table `Date Activities`
-- 
-- 

CREATE TABLE IF NOT EXISTS `DateActivities` (
  `DateActivitiesID` int NOT NULL AUTO_INCREMENT,
  `DatePlanID` int NOT NULL,
  `ActivityID` int NOT NULL,
  CONSTRAINT FOREIGN KEY (`DatePlanID`) REFERENCES `DatePlans` (`DatePlanID`),
  CONSTRAINT FOREIGN KEY (`ActivityID`) REFERENCES `Activities` (`ActivityID`),
  PRIMARY KEY (`DateActivitiesID`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `Date Plan Activity Reviews`
-- 
-- 

CREATE TABLE IF NOT EXISTS `DatePlanActivityReviews` (
  `DatePlanActivityReviewID` int NOT NULL AUTO_INCREMENT,
  `ActivityReviewID` int NOT NULL,
  `DatePlanReviewsID` int NOT NULL,
  CONSTRAINT FOREIGN KEY (`ActivityReviewID`) REFERENCES `ActivityReviews` (`ReviewID`),
   CONSTRAINT FOREIGN KEY (`DatePlanReviewsID`) REFERENCES `DatePlanReviews` (`ReviewID`),
  PRIMARY KEY (`DatePlanActivityReviewID`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `Favorites`
--

CREATE TABLE IF NOT EXISTS `Favorites` (
  `FavoriteID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `ActivityID` int DEFAULT NULL,
  `DatePlanID` int DEFAULT NULL,
  PRIMARY KEY (`FavoriteID`),
  CONSTRAINT FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`DatePlanID`) REFERENCES `DatePlans` (`DatePlanID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`ActivityID`) REFERENCES `Activities` (`ActivityID`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `Reports`
--

CREATE TABLE IF NOT EXISTS `Reports` (
  `ReportID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Type` int NOT NULL,
  CONSTRAINT FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  PRIMARY KEY (`ReportID`)
) ENGINE=InnoDB;