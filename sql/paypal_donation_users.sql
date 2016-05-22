-- ----------------------------
-- Table structure for table `paypal_donation_users`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `paypal_donation_users` (
	`userID` int(11) NOT NULL AUTO_INCREMENT,
	`userName` varchar(100) NOT NULL,
	`userEmail` varchar(100) NOT NULL,
	`userPass` varchar(100) NOT NULL,
	`userRole` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
	`characterName` varchar(100) NOT NULL DEFAULT '',
	`userStatus` enum('Y','N') NOT NULL DEFAULT 'N',
	`tokenCode` varchar(100) NOT NULL,
	`joining_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`userID`),
	UNIQUE KEY `userEmail` (`userEmail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;