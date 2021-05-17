CREATE DATABASE IF NOT EXISTS `main_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `main_db`;

CREATE TABLE IF NOT EXISTS `accounts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  `imgStatus_1` int(11) NOT NULL DEFAULT '1',
  `imgStatus_2` int(11) NOT NULL DEFAULT '1',
  `role` enum('Miembro', 'Admin', 'Ejecutivo', 'Supervisor') NOT NULL DEFAULT 'Miembro',
  `group_name` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `birthday` varchar(50) NOT NULL,
  `address` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `state` varchar(128) NOT NULL,
  `ine` varchar(128) NOT NULL,
  `phone` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `hire_date` varchar(50) NOT NULL,
  `guar_imgStatus_1` int(11) NOT NULL DEFAULT '1',
  `guar_imgStatus_2` int(11) NOT NULL DEFAULT '1',
  `guar_name` varchar(50) NOT NULL,
  `guar_lastname` varchar(50) NOT NULL,
  `guar_phone` varchar(50) NOT NULL,
  `guar_address` varchar(128) NOT NULL,
  `guar_city` varchar(128) NOT NULL,
  `guar_state` varchar(128) NOT NULL,
  `guar_ine` varchar(128) NOT NULL,
	`activation_code` varchar(50) NOT NULL DEFAULT '',
  `rememberme` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1003 DEFAULT CHARSET=utf8;

INSERT INTO `accounts` (`id`, `name`, `password`, `email`, `role`) VALUES
(1001, 'admin', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'admin@example.com', 'Admin'),
(1002, 'member', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'member@example.com', 'Miembro');

CREATE TABLE IF NOT EXISTS `customers` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  `customerStatus` int(11) NOT NULL DEFAULT '0',
  `imgStatus_1` int(11) NOT NULL DEFAULT '1',
  `imgStatus_2` int(11) NOT NULL DEFAULT '1',
  `group_name` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `birthday` varchar(50) NOT NULL,
  `address` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `state` varchar(128) NOT NULL,
  `ine` varchar(128) NOT NULL,
  `phone` varchar(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `registration_date` varchar(50) NOT NULL,
  `guar_imgStatus_1` int(11) NOT NULL DEFAULT '1',
  `guar_imgStatus_2` int(11) NOT NULL DEFAULT '1',
  `guar_name` varchar(50) NOT NULL,
  `guar_lastname` varchar(50) NOT NULL,
  `guar_phone` varchar(128) NOT NULL,
  `guar_address` varchar(128) NOT NULL,
  `guar_city` varchar(128) NOT NULL,
  `guar_state` varchar(128) NOT NULL,
  `guar_ine` varchar(128) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `loans` (
	`loanId` int(11) NOT NULL AUTO_INCREMENT,
  `customerID` int(11) NOT NULL,
  `loanAmount` int(11) NOT NULL,
  `loanInterest` int(11) NOT NULL,
  `loanPlusInterest` int(11) NOT NULL,
  `loanDate` varchar(50) NOT NULL,
  `executive` varchar(128) NOT NULL,
    PRIMARY KEY (`loanId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `payments` (
	`paymentId` int(20) NOT NULL AUTO_INCREMENT,
  `loanId` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `paymentAmount` int(11) NOT NULL,
  `paymentDate` varchar(11) NOT NULL,
  `latePayment` int(11) NOT NULL,
  `executive` varchar(128) NOT NULL,
    PRIMARY KEY (`paymentId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;