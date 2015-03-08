-- phpMyAdmin SQL Dump
-- version 4.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2015 at 10:35 AM
-- Server version: 5.6.17
-- PHP Version: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `OOP2DB_chapters`
--

DROP TABLE IF EXISTS `OOP2DB_chapters`;
CREATE TABLE IF NOT EXISTS `OOP2DB_chapters` (
  `_key` varchar(255) COLLATE utf8_bin NOT NULL,
  `_value` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `OOP2DB_game_data`
--

DROP TABLE IF EXISTS `OOP2DB_game_data`;
CREATE TABLE IF NOT EXISTS `OOP2DB_game_data` (
  `_key` varchar(255) COLLATE utf8_bin NOT NULL,
  `_value` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `OOP2DB_players`
--

DROP TABLE IF EXISTS `OOP2DB_players`;
CREATE TABLE IF NOT EXISTS `OOP2DB_players` (
  `_key` varchar(255) COLLATE utf8_bin NOT NULL,
  `_value` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `OOP2DB_chapters`
--
ALTER TABLE `OOP2DB_chapters`
 ADD PRIMARY KEY (`_key`);

--
-- Indexes for table `OOP2DB_game_data`
--
ALTER TABLE `OOP2DB_game_data`
 ADD PRIMARY KEY (`_key`);

--
-- Indexes for table `OOP2DB_players`
--
ALTER TABLE `OOP2DB_players`
 ADD PRIMARY KEY (`_key`);
