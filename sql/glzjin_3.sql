ALTER TABLE `ss_node` ADD `node_ip` TEXT NULL AFTER `node_ping`;
ALTER TABLE `ss_node` ADD `bandwidthlimit_resetday` INT NOT NULL DEFAULT '0' AFTER `node_bandwidth_limit`; 
ALTER TABLE `user` ADD `class` INT NOT NULL AFTER `sendDailyMail`, ADD `class_expire` DATETIME NOT NULL AFTER `class`;
ALTER TABLE `user` CHANGE `class` `class` INT(11) NOT NULL DEFAULT '0', CHANGE `class_expire` `class_expire` DATETIME NOT NULL DEFAULT '1989-06-04 00:05:00';

-- phpMyAdmin SQL Dump
-- version 4.4.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-03-26 17:41:17
-- 服务器版本： 5.5.42
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `glzjin_ss2`
--

-- --------------------------------------------------------

--
-- 表的结构 `radius_ban`
--

CREATE TABLE IF NOT EXISTS `radius_ban` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `radius_ban`
--
ALTER TABLE `radius_ban`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `radius_ban`
--
ALTER TABLE `radius_ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


ALTER TABLE `user` ADD `expire_in` DATETIME NOT NULL DEFAULT '2099:06:04 00:05:00' AFTER `class_expire`;

UPDATE `user` SET `expire_in` = '2099-06-04 00:05:00' ;


UPDATE `user` SET `class_expire` = '1989-06-04 02:00:00' ;



-- --------------------------------------------------------

--
-- 表的结构 `code`
--

CREATE TABLE IF NOT EXISTS `code` (
  `id` bigint(20) NOT NULL,
  `code` text NOT NULL,
  `type` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `isused` int(11) NOT NULL DEFAULT '0',
  `userid` bigint(20) NOT NULL,
  `usedatetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `code`
--
ALTER TABLE `code`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `code`
--
ALTER TABLE `code`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
