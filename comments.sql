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
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment` text NOT NULL,
`commentId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `correct` tinyint(1) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment`, `commentId`, `questionId`, `correct`, `userid`) VALUES
('Please clarify which train.', 1, 7, 1, 1),
('21km/hr', 2, 7, 0, 2),
('very fast', 3, 6, 0, 3),
('PHPÂ is aÂ server-side scriptingÂ language designed forÂ web developmentÂ but also used as aÂ general-purpose programming language.', 4, 9, 0, 4),
('JavaScriptÂ  is aÂ dynamicÂ computerÂ programming language. It is most commonly used as part ofÂ Web browsers, whose implementations allowÂ client-side scriptsÂ toÂ interact with the user, control the browser, communicate asynchronously, and alter theÂ document contentÂ that is displayed.', 5, 10, 1, 5),
('HTML is the standardÂ markup languageÂ used to createÂ web pages.\n', 6, 11, 0, 6),
('Jquery is a JavaScript Library.Jquery greatly simplifies JavaScript programming.', 7, 6, 0, 7),
('this is a hypertext preprossesor.', 8, 9, 0, 8),
('java script', 9, 12, 0, 9),
('hbfuydggu', 10, 9, 0, 10),
('this is the last answerva', 11, 6, 0, 9),
('\n\nI''m also not familiar with ruby, but I have an idea for some workaround:\n\nPlease look at the following example about returning an array of network interfaces. Now to create domain_array fact use the following code:\n\nFacter.add(:domain_array) do\n  setcode do\n  domains = Facter.value(:domains)\n  domain_array = domains.split('','')\n  domain_array\n  end\nend\n\n', 12, 15, 1, 9),
('it is a language....!!', 13, 11, 0, 11),
('it finds the complexity', 14, 16, 0, 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
 ADD PRIMARY KEY (`commentId`), ADD UNIQUE KEY `commentId` (`commentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
