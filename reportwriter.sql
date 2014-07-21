-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 18, 2014 at 06:27 PM
-- Server version: 5.1.69
-- PHP Version: 5.3.2-1ubuntu4.24

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `reportwriter`
--

-- --------------------------------------------------------

--
-- Table structure for table `Member`
--

CREATE TABLE IF NOT EXISTS `Member` (
  `pkMemberID` int(10) NOT NULL AUTO_INCREMENT,
  `MemberName` varchar(255) NOT NULL,
  PRIMARY KEY (`pkMemberID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Member`
--

INSERT INTO `Member` (`pkMemberID`, `MemberName`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C');

-- --------------------------------------------------------

--
-- Table structure for table `MemberFriend`
--

CREATE TABLE IF NOT EXISTS `MemberFriend` (
  `pkMemberFriendID` int(11) NOT NULL AUTO_INCREMENT,
  `fkMemberID` int(11) NOT NULL,
  `FriendID` int(11) NOT NULL,
  PRIMARY KEY (`pkMemberFriendID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `MemberFriend`
--

INSERT INTO `MemberFriend` (`pkMemberFriendID`, `fkMemberID`, `FriendID`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 2, 1),
(4, 2, 3),
(5, 3, 1),
(6, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ReportHistory`
--

CREATE TABLE IF NOT EXISTS `ReportHistory` (
  `pkHistoryID` int(100) NOT NULL AUTO_INCREMENT,
  `ReportName` varchar(255) NOT NULL,
  `SqlQuery` text NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pkHistoryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ReportHistory`
--

INSERT INTO `ReportHistory` (`pkHistoryID`, `ReportName`, `SqlQuery`, `DateCreated`) VALUES
(1, 'Member Report', 'select * from Member where pkMemberID=''1''', '2014-04-18 11:45:20');
