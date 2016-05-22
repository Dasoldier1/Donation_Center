-- ----------------------------
-- Table structure for `paypal_donation_log`
-- ----------------------------
DROP TABLE IF EXISTS `paypal_donation_log`;
CREATE TABLE `paypal_donation_log` (
  `transaction_id` varchar(64) NOT NULL DEFAULT '',
  `donation` varchar(255) NOT NULL DEFAULT '',
  `amount` double NOT NULL DEFAULT '0',
  `amountminfee` double NOT NULL DEFAULT '0',
  `character_name` text NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;