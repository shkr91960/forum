-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2015 at 08:24 PM
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
-- Table structure for table `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `question` text NOT NULL,
`qid` int(11) NOT NULL,
  `title` text NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`question`, `qid`, `title`, `userid`) VALUES
('can somebody tell me what is the basic difference in between cv and resume', 6, 'difference between cv and resume', 9),
('What is the speed of train?', 7, 'train speed', 9),
('What is PHP??', 9, 'PHP', 1),
('What is java Script??', 10, 'Java Script', 10),
('What is HTML??', 11, 'HTML', 2),
('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 12, 'Jquery', 3),
('The ultimate goal is to implement a mobile Orientation tracker. The following paragraph is from a published paper.Assume we want to estimate the gravity vector at time t given a window of historical accelerometer data a(tâˆ’T:t) of size T. We first rotate acceleration signals a(tâˆ’T:tâˆ’1) to the same orientation as a(t), and then estimate the gravity at time t as the mean of the acceleration readings after the rotation. Specifically, the gravity at time t is estimated asg(t) = R(t) sum_k[ (R(k) * a(k))/T ]where k goes from t-T to twhere a(k) is the acceleration vector in the coordinate frame of the device, and R(k) is the rotation matrix describing the rotation from the earth coordinate system to the coordinate system of the device at time k, as obtained from the Kalman Filter.Now the paper does not say how R(k) is obtained.How can I go about solving this problem', 14, 'Estimate Gravity using gyro and accelerometer', 9),
('data analysis and algorithm', 16, 'DAA', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `question`
--
ALTER TABLE `question`
 ADD PRIMARY KEY (`qid`), ADD UNIQUE KEY `qid` (`qid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
MODIFY `qid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
