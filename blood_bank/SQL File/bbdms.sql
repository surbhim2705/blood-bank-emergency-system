-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2024 at 09:50 AM
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
-- Database: `bbdms`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Admin', 'admin', 8979555558, 'admin@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-01-01 04:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `tblblooddonars`
--

CREATE TABLE `tblblooddonars` (
  `id` int(11) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `MobileNumber` char(11) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `Gender` varchar(20) DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `BloodGroup` varchar(20) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Message` mediumtext DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(1) DEFAULT NULL,
  `Password` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblblooddonars`
--

INSERT INTO `tblblooddonars` (`id`, `FullName`, `MobileNumber`, `EmailId`, `Gender`, `Age`, `BloodGroup`, `Address`, `Message`, `PostingDate`, `status`, `Password`) VALUES
(13, 'Techy Guy', '567890', 'techyguy@gmail.com', 'Male', 20, 'AB+', 'Lokanthali-1, Bhaktapur, Nepal', ' Contact me anytime if AB+ blood is required.', '2024-07-13 04:09:09', 1, 'adfd3406be09283907df6c2e8afffa6c');

-- --------------------------------------------------------

--
-- Table structure for table `tblbloodgroup`
--

CREATE TABLE `tblbloodgroup` (
  `id` int(11) NOT NULL,
  `BloodGroup` varchar(20) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbloodgroup`
--

INSERT INTO `tblbloodgroup` (`id`, `BloodGroup`, `PostingDate`) VALUES
(1, 'A-', '2024-01-01 20:33:50'),
(2, 'AB-', '2024-01-01 20:33:50'),
(3, 'O-', '2024-01-01 20:33:50'),
(4, 'A-', '2024-01-01 20:33:50'),
(5, 'A+', '2024-01-01 20:33:50'),
(6, 'AB+', '2024-01-01 20:33:50'),
(8, 'B+', '2024-07-13 04:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `tblbloodrequirer`
--

CREATE TABLE `tblbloodrequirer` (
  `ID` int(10) NOT NULL,
  `BloodDonarID` int(10) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `EmailId` varchar(250) DEFAULT NULL,
  `ContactNumber` bigint(10) DEFAULT NULL,
  `BloodRequirefor` varchar(250) DEFAULT NULL,
  `Message` mediumtext DEFAULT NULL,
  `ApplyDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbloodrequirer`
--

INSERT INTO `tblbloodrequirer` (`ID`, `BloodDonarID`, `name`, `EmailId`, `ContactNumber`, `BloodRequirefor`, `Message`, `ApplyDate`) VALUES
(1, 6, 'CC', 'rak@gmail.com', 7894561236, 'Father', 'Please help', '2024-01-06 11:57:24'),
(2, 3, 'Mukesh', 'muk@gmail.com', 5896231478, 'Others', 'Please help', '2024-01-06 11:58:48'),
(3, 6, 'Hitesh', 'hit@gmail.com', 1236547896, 'Brother', 'do the needful', '2024-01-07 12:02:12'),
(4, 10, 'Rahul Singh', 'rahk@gmail.com', 2536251425, 'Mother', 'Please help me', '2024-01-08 01:51:52'),
(5, 11, 'Anuj Kumar', 'ak@gmail.com', 8525232102, 'Others', 'Need blood on urgent basis', '2024-01-08 01:24:18'),
(6, 13, 'harsh', 'harsh@gmail.com', 876669870980, '', 'I need this urgently, please help me', '2024-07-13 04:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusinfo`
--

CREATE TABLE `tblcontactusinfo` (
  `id` int(11) NOT NULL,
  `Address` tinytext DEFAULT NULL,
  `EmailId` varchar(255) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusinfo`
--

INSERT INTO `tblcontactusinfo` (`id`, `Address`, `EmailId`, `ContactNo`) VALUES
(1, 'Bhaktapur, Nepal																							', 'techyguy@gmail.com', '977 1234567');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusquery`
--

CREATE TABLE `tblcontactusquery` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `ContactNumber` char(11) DEFAULT NULL,
  `Message` longtext DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpages`
--

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL,
  `PageName` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpages`
--

INSERT INTO `tblpages` (`id`, `PageName`, `type`, `detail`) VALUES
(2, 'Why Become Donor', 'donor', '<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),
(3, 'About Us ', 'aboutus', '<div style=\"text-align: justify;\"><span style=\"font-size: 1em; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif;\">Welcome to the blood bank donor management system.</span></div>');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblblooddonars`
--
ALTER TABLE `tblblooddonars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bgroup` (`BloodGroup`);

--
-- Indexes for table `tblbloodgroup`
--
ALTER TABLE `tblbloodgroup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `BloodGroup` (`BloodGroup`),
  ADD KEY `BloodGroup_2` (`BloodGroup`);

--
-- Indexes for table `tblbloodrequirer`
--
ALTER TABLE `tblbloodrequirer`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `donorid` (`BloodDonarID`);

--
-- Indexes for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpages`
--
ALTER TABLE `tblpages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblblooddonars`
--
ALTER TABLE `tblblooddonars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tblbloodgroup`
--
ALTER TABLE `tblbloodgroup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tblbloodrequirer`
--
ALTER TABLE `tblbloodrequirer`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblpages`
--
ALTER TABLE `tblpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
