-- ----------------------------
-- Table structure for `paypal_donation_login_attempts`
-- ----------------------------
DROP TABLE IF EXISTS `paypal_donation_login_attempts`;
CREATE TABLE `paypal_donation_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(64) NOT NULL DEFAULT '',
  `usermail` enum('1','0') NOT NULL DEFAULT '0',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;