-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2024 at 08:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vet_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appid` int(11) NOT NULL,
  `petownerid` int(11) DEFAULT NULL,
  `symptom` varchar(50) DEFAULT NULL,
  `appstatus` varchar(50) DEFAULT NULL,
  `vetid` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `petname` varchar(100) DEFAULT NULL,
  `pettype` text NOT NULL,
  `relatedtreatment` text NOT NULL,
  `relatedpetcare` text NOT NULL,
  `relatedsymptoms` text NOT NULL,
  `department` text NOT NULL,
  `appname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `appointment_temp`
--

CREATE TABLE `appointment_temp` (
  `appid` int(11) NOT NULL,
  `petownerid` int(11) DEFAULT NULL,
  `symptom` varchar(50) DEFAULT NULL,
  `appstatus` varchar(50) DEFAULT NULL,
  `vetid` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `petname` varchar(100) DEFAULT NULL,
  `pettype` text NOT NULL,
  `relatedtreatment` text NOT NULL,
  `relatedpetcare` text NOT NULL,
  `relatedsymptoms` text NOT NULL,
  `department` text NOT NULL,
  `appname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `chatid` int(11) NOT NULL,
  `chatsentdate` date DEFAULT NULL,
  `chatreceivedate` date DEFAULT NULL,
  `chatsenttime` time DEFAULT NULL,
  `chatreceivetime` time DEFAULT NULL,
  `chatsentcontent` text DEFAULT NULL,
  `chatreceivecontent` text DEFAULT NULL,
  `topic` varchar(100) DEFAULT NULL,
  `petownerid` int(11) DEFAULT NULL,
  `nurseid` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `medication`
--

CREATE TABLE `medication` (
  `medid` int(11) NOT NULL,
  `medname` varchar(100) DEFAULT NULL,
  `petownerid` int(11) DEFAULT NULL,
  `petname` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `nurse`
--

CREATE TABLE `nurse` (
  `nurseid` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `fullname` varchar(201) GENERATED ALWAYS AS (concat(`lastname`,' ',`firstname`)) VIRTUAL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phonenum` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `petowner`
--

CREATE TABLE `petowner` (
  `petownerid` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `fullname` varchar(201) GENERATED ALWAYS AS (concat(`lastname`,' ',`firstname`)) VIRTUAL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phonenum` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `appid` int(11) NOT NULL,
  `appname` varchar(100) DEFAULT NULL,
  `vetid` int(11) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `scheduledstatus` varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `relatedtreatment` text NOT NULL,
  `relatedpetcare` text NOT NULL,
  `relatedsymptoms` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `symptomid` int(11) NOT NULL,
  `symptomname` varchar(100) DEFAULT NULL,
  `symptomstatus` varchar(50) DEFAULT NULL,
  `petownerid` int(11) DEFAULT NULL,
  `pettype` varchar(50) DEFAULT NULL,
  `vetid` int(11) DEFAULT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `vet`
--

CREATE TABLE `vet` (
  `vetid` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `fullname` varchar(201) GENERATED ALWAYS AS (concat(`lastname`,' ',`firstname`)) VIRTUAL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phonenum` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appid`),
  ADD KEY `petownerid` (`petownerid`),
  ADD KEY `vetid` (`vetid`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`chatid`),
  ADD KEY `petownerid` (`petownerid`),
  ADD KEY `nurseid` (`nurseid`);

--
-- Indexes for table `medication`
--
ALTER TABLE `medication`
  ADD PRIMARY KEY (`medid`),
  ADD KEY `petownerid` (`petownerid`);

--
-- Indexes for table `nurse`
--
ALTER TABLE `nurse`
  ADD PRIMARY KEY (`nurseid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `petowner`
--
ALTER TABLE `petowner`
  ADD PRIMARY KEY (`petownerid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`appid`),
  ADD KEY `vetid` (`vetid`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`symptomid`),
  ADD KEY `petownerid` (`petownerid`),
  ADD KEY `vetid` (`vetid`);

--
-- Indexes for table `vet`
--
ALTER TABLE `vet`
  ADD PRIMARY KEY (`vetid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `chatid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`petownerid`) REFERENCES `petowner` (`petownerid`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`vetid`) REFERENCES `vet` (`vetid`);

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`petownerid`) REFERENCES `petowner` (`petownerid`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`nurseid`) REFERENCES `nurse` (`nurseid`);

--
-- Constraints for table `medication`
--
ALTER TABLE `medication`
  ADD CONSTRAINT `medication_ibfk_1` FOREIGN KEY (`petownerid`) REFERENCES `petowner` (`petownerid`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`vetid`) REFERENCES `vet` (`vetid`);

--
-- Constraints for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD CONSTRAINT `symptoms_ibfk_1` FOREIGN KEY (`petownerid`) REFERENCES `petowner` (`petownerid`),
  ADD CONSTRAINT `symptoms_ibfk_2` FOREIGN KEY (`vetid`) REFERENCES `vet` (`vetid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
