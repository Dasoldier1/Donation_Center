-- ----------------------------
-- Table structure for `paypal_donation_captcha_codes`
-- ----------------------------
DROP TABLE IF EXISTS `paypal_donation_captcha_codes`;
CREATE TABLE `paypal_donation_captcha_codes` (
  `id` varchar(40) NOT NULL,
  `namespace` varchar(32) NOT NULL,
  `code` varchar(32) NOT NULL,
  `code_display` varchar(32) NOT NULL,
  `created` int(11) NOT NULL,
  `audio_data` mediumblob,
  PRIMARY KEY (`id`,`namespace`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;