-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 02, 2024 at 04:29 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$H8hi1Oe8UH8gCizTtcAdVe914A/6EE2oJkw7xK0COwLhhDShEHWsa');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'Jawharlal Nehru', 0),
(2, 1, 'Narendra Modi', 1),
(3, 1, 'Sardar Vallabai Patel', 0),
(4, 1, 'None of these', 0),
(5, 2, 'Tiger', 1),
(6, 2, 'Peacock', 0),
(7, 2, 'Lion', 0),
(8, 2, 'Whale', 0),
(9, 3, 'Lokesh Nara', 0),
(10, 3, 'Jagan Mohan Reddy', 0),
(11, 3, 'Chandra Babu Naidu', 1),
(12, 3, 'Narendra Modi', 0),
(20, 8, 'simple past', 1),
(21, 8, 'past perfect', 0),
(22, 8, 'future tense', 0),
(23, 8, 'future perfect', 0),
(24, 9, 'Past Tense', 1),
(25, 9, 'Future Tense', 0),
(26, 9, 'Past Participle', 0),
(27, 9, 'Future Percet', 0),
(28, 10, '4', 0),
(29, 10, '7', 0),
(30, 10, '5', 1),
(31, 10, '6', 0),
(32, 11, 'Noun', 0),
(33, 11, 'Verb', 0),
(34, 11, 'Adverb', 0),
(35, 11, 'Pronoun', 1),
(36, 12, 'CDGV', 0),
(37, 12, 'DCBA', 0),
(38, 12, 'DCBA', 1),
(39, 12, 'ADCB', 0),
(40, 13, 'NDA', 1),
(41, 13, 'Congress', 0),
(42, 13, 'Both', 0),
(43, 13, 'None', 0),
(44, 14, 'India', 1),
(45, 14, 'Pakistan', 0),
(46, 14, 'Europe', 0),
(47, 14, 'Italy', 0),
(48, 15, 'Chandra Bose', 0),
(49, 15, 'Narenda Modi', 1),
(50, 15, 'Mahesh', 0),
(51, 15, 'Chandra Babu Naidu', 0),
(52, 16, 'YS Jagan', 0),
(53, 16, 'Nara Lokesh', 0),
(54, 16, 'Chandra Babu Naidu', 1),
(55, 16, 'None', 0),
(56, 17, 'Assam', 0),
(57, 17, 'Dispur', 0),
(58, 17, 'Nagaland', 0),
(59, 17, 'Megahalaya', 1),
(60, 18, 'Aug 1998', 0),
(61, 18, 'Sep 1997', 0),
(62, 18, 'Aug 1947', 1),
(63, 18, 'All of these', 0),
(64, 19, 'Tiger', 0),
(65, 19, 'Girafee', 0),
(66, 19, 'Swift', 1),
(67, 19, 'Lion', 0),
(68, 20, 'S C Railway', 0),
(69, 20, 'South Central Railway', 1),
(70, 20, 'Suraj City Railway', 0),
(71, 20, 'Subash Chandrabose Railway', 0),
(72, 21, 'Niramala Sitaraman', 1),
(73, 21, 'Savithri', 0),
(74, 21, 'Keethi Suresh', 0),
(75, 21, 'Namrata', 0);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `test_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `test_id`, `question_text`) VALUES
(1, 1, 'What is the name of man,who created NDA Government and who is from BJP Party and his age is greater than 80?'),
(2, 1, 'What is our national animal?'),
(3, 1, 'Who is Andhra Pradesh CM?'),
(8, 10, 'What is Past Tense'),
(9, 10, 'She is not explaing?'),
(10, 10, 'How many tenses are there?'),
(11, 10, 'Parts of speech of Person?'),
(12, 11, 'HSAQ is coded as QASQ then ABCD is coded as ?'),
(13, 1, 'What is Name of Our Government?\r\n'),
(14, 12, 'What is name of our nation?'),
(15, 12, 'Who is our Prime Minister?'),
(16, 12, 'Who is CM of Andhra Pradesh'),
(17, 12, 'Most raining place in India?'),
(18, 12, 'When we got independence?'),
(19, 12, 'What is the fastest Animal?'),
(20, 12, 'In Railway, SCR means?'),
(21, 12, 'Who is current Finance Minister?');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `title`, `start_time`, `end_time`) VALUES
(1, 'General Knowledge', '2024-07-25 14:00:00', '2024-08-20 15:08:00'),
(12, 'Current Affairs', '2024-08-14 15:30:00', '2024-09-30 15:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `completed_tests` varchar(2000) DEFAULT '[]',
  `points` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `username`, `name`, `password`, `completed_tests`, `points`) VALUES
(1, 'vamsi_nakka', 'Vamsi Praveen', '$2y$10$H8hi1Oe8UH8gCizTtcAdVe914A/6EE2oJkw7xK0COwLhhDShEHWsa', '[\"1\"]', 5),
(6, '21A91A05G5', '﻿Nakka Vamsi Praveen', '$2y$10$EMGz/5yrEyxp.VBRJOQdvOcqQmSX2Mh5W/Lj46/tOuJs4QHrVmCbO', '[\"12\"]', 5),
(7, '21A91A05I1', 'Rayudu Ramya Sri', '$2y$10$pJo7S7aCsG97VByv5ixm2eNlk8e4BbJiwkd43.lNTYRpd8C71Y0Ya', '[\"12\"]', 10),
(8, '21A91A05C3', 'T J S S Ganesh', '$2y$10$TINOrztRmez9CjC5Yv1z7.HFCkyecY57XWDQBw7nlgFnVsz83cQwO', '[\"12\"]', 5);

-- --------------------------------------------------------

--
-- Table structure for table `user_responses`
--

CREATE TABLE `user_responses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `selected_option_id` int(11) DEFAULT NULL,
  `is_correct` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_responses`
--

INSERT INTO `user_responses` (`id`, `user_id`, `test_id`, `question_id`, `selected_option_id`, `is_correct`) VALUES
(1, 1, 1, 1, 3, 0),
(2, 1, 1, 2, 7, 0),
(3, 1, 1, 3, 11, 1),
(4, 1, 1, 13, 43, 0),
(5, 1, 12, 14, 44, 1),
(6, 1, 12, 15, 49, 1),
(7, 1, 12, 16, 54, 1),
(8, 1, 12, 17, 59, 1),
(9, 1, 12, 18, 62, 1),
(10, 1, 12, 19, 66, 1),
(11, 1, 12, 20, 69, 1),
(12, 1, 12, 21, 75, 0),
(13, 6, 12, 14, 44, 1),
(14, 6, 12, 15, 49, 1),
(15, 6, 12, 16, 53, 0),
(16, 6, 12, 17, 59, 1),
(17, 6, 12, 18, 62, 1),
(18, 6, 12, 19, 66, 1),
(19, 6, 12, 20, 69, 1),
(20, 6, 12, 21, 72, 1),
(21, 7, 12, 14, 44, 1),
(22, 7, 12, 15, 49, 1),
(23, 7, 12, 16, 54, 1),
(24, 7, 12, 17, 59, 1),
(25, 7, 12, 18, 62, 1),
(26, 7, 12, 19, 64, 0),
(27, 7, 12, 20, 69, 1),
(28, 7, 12, 21, 72, 1),
(29, 8, 12, 14, 47, 0),
(30, 8, 12, 15, 50, 0),
(31, 8, 12, 16, 54, 1),
(32, 8, 12, 17, 58, 0),
(33, 8, 12, 18, 62, 1),
(34, 8, 12, 19, 66, 1),
(35, 8, 12, 20, 70, 0),
(36, 8, 12, 21, 74, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user_responses`
--
ALTER TABLE `user_responses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_responses`
--
ALTER TABLE `user_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
