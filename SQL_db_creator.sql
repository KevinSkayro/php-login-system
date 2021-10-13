-- Host: localhost
-- Generation Time: Oct 12, 2021 at 01:33 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `main_db`
--
CREATE DATABASE IF NOT EXISTS `main_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `main_db`;
-- --------------------------------------------------------


--
-- Table structure for table `accounts`
--
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL,
  `imgStatus_1` int(11) DEFAULT 1,
  `imgStatus_2` int(11) DEFAULT 1,
  `role` enum('Miembro','Admin','Ejecutivo','Supervisor') NOT NULL DEFAULT 'Miembro',
  `plaza_name` varchar(255) NOT NULL,
  `name` varchar(128) NOT NULL,
  `lastname` varchar(128) NOT NULL,
  `birthday` varchar(50) NOT NULL,
  `address` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `state` varchar(128) NOT NULL,
  `ine` varchar(128) NOT NULL,
  `phone` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `hire_date` varchar(50) NOT NULL DEFAULT current_timestamp(),
  `guar_imgStatus_1` int(11) DEFAULT 1,
  `guar_imgStatus_2` int(11) DEFAULT 1,
  `guar_name` varchar(128) NOT NULL,
  `guar_lastname` varchar(128) NOT NULL,
  `guar_phone` varchar(128) NOT NULL,
  `guar_address` varchar(128) NOT NULL,
  `guar_city` varchar(128) NOT NULL,
  `guar_state` varchar(128) NOT NULL,
  `guar_ine` varchar(128) NOT NULL,
  `activation_code` varchar(50) NOT NULL DEFAULT '',
  `rememberme` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `accounts` (`id`, `name`, `password`, `email`, `role`) VALUES
(1001, 'admin', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'admin@example.com', 'Admin'),
(1002, 'member', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'member@example.com', 'Miembro');


--
-- Table structure for table `customers`
--
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL,
  `customerStatus` int(11) NOT NULL DEFAULT 0,
  `imgStatus_1` int(11) NOT NULL DEFAULT 1,
  `imgStatus_2` int(11) NOT NULL DEFAULT 1,
  `group_name` varchar(255) NOT NULL,
  `maxLoan` int(11) NOT NULL,
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
  `guar_imgStatus_1` int(11) NOT NULL DEFAULT 1,
  `guar_imgStatus_2` int(11) NOT NULL DEFAULT 1,
  `guar_name` varchar(50) NOT NULL,
  `guar_lastname` varchar(50) NOT NULL,
  `guar_phone` varchar(128) NOT NULL,
  `guar_address` varchar(128) NOT NULL,
  `guar_city` varchar(128) NOT NULL,
  `guar_state` varchar(128) NOT NULL,
  `guar_ine` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `groups`
--
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `plazaID` int(11) NOT NULL,
  `asesor` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `loans`
--
CREATE TABLE IF NOT EXISTS `loans` (
  `loanID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `folio` varchar(128) NOT NULL,
  `loanStatus` int(11) NOT NULL DEFAULT 1,
  `loanAmount` int(11) NOT NULL,
  `loanInterest` int(11) NOT NULL,
  `loanPlusInterest` int(11) NOT NULL,
  `amountOwed` decimal(11,2) NOT NULL,
  `pastDue` decimal(11,2) NOT NULL,
  `weekThirteen` int(11) DEFAULT 0,
  `loanDate` varchar(50) NOT NULL,
  `executive` varchar(128) NOT NULL,
  `groupID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `payments`
--
CREATE TABLE IF NOT EXISTS `payments` (
  `paymentID` int(20) NOT NULL,
  `loanID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `paymentAmount` decimal(11,2) NOT NULL,
  `paymentDate` varchar(50) NOT NULL,
  `latePayment` int(11) NOT NULL,
  `executive` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `plazas`
--
CREATE TABLE IF NOT EXISTS `plazas` (
  `id` int(11) NOT NULL,
  `plaza_name` varchar(255) NOT NULL,
  `executive` varchar(128) NOT NULL,
  `supervisor` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loanID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `plazas`
--
ALTER TABLE `plazas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `paymentID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `plazas`
--
ALTER TABLE `plazas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;