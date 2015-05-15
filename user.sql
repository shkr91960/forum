-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2015 at 08:25 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `name` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
`userid` int(11) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`name`, `username`, `password`, `userid`, `points`) VALUES
('situmaurya', 'situ123@gmail.com', 'GGsbn3PMTZCJZ/7MISPEH/7lVnu8CuQ+:dETlo7bkUBWkhBhVb', 1, 0),
('nehamaurya', 'neha123@gmail.com', 'CAtOoRLbOOF99KCWOZFffksnZjll+iVg:Cmg4zfKGAKoGTrFtf', 2, 0),
('anshumaurya', 'anshu12@gmail.com', 'UWprjtEQRVhc0N36Ce5UTzGFNlKu0vtz:uYOrGnaElhf6smpvw', 3, 0),
('anshumaurya', 'anshu10@gmail.com', 'ZnAH0QHoPbqQ5wJMbuxl4kK1m7MXxkMr:vbIT/rmgGh0Wg7D0k', 4, 0),
('anshumaurya', 'anshu16@gmail.com', 'keS/I4QVpTcJAYdR2bHA3p2i5mjreLsL:cbd4EGHf9tM8P3Qja', 5, 0),
('preetimaurya', 'shkr91960@gmail.com', 'tOlbrJ3p68PghZ50225pcmih6iIKN/10:Y1rvHyVfLNv58YgIu', 6, 0),
('Pratibhasoni', 'psoni6@gmail.com', 'TR4zD6NsvVQotXsyBJa1HWLQmGiBC6v7:J0QUiaaUrmPYdnn0m', 7, 0),
('Pratibhasoni', 'psoni06@gmail.com', 'VFWCuws3634iVE1ISaAzluesfaKUNFo0:ao5/yIEUQYPzNGsc+', 8, 0),
('anshu maurya', 'anshu18@gmail.com', 'L0+c9FLdNzSMk3DOUNaShiagdzmiAV4i:DYOW458nrjMPmcNgQ', 9, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`userid`), ADD UNIQUE KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
