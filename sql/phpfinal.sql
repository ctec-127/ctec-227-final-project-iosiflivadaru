-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2019 at 09:47 AM
-- Server version: 10.1.39-MariaDB
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phpfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment` varchar(400) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contest`
--

CREATE TABLE `contest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contest`
--

INSERT INTO `contest` (`id`, `user_id`, `post_id`, `active`) VALUES
(1, 2, 1, 1),
(2, 4, 2, 1),
(12, 1, 1, 1),
(13, 1, 3, 1),
(14, 1, 2, 1),
(15, 4, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE `follow` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `following` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `follow`
--

INSERT INTO `follow` (`id`, `user_id`, `following`) VALUES
(1, 2, 1),
(2, 4, 2),
(3, 1, 4),
(4, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE `like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `market`
--

CREATE TABLE `market` (
  `id` int(11) NOT NULL,
  `item` varchar(30) NOT NULL,
  `tag` varchar(10) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `market`
--

INSERT INTO `market` (`id`, `item`, `tag`, `price`) VALUES
(1, 'fa-user-check text-primary', 'badge', 200),
(2, 'fa-award text-warning', 'badge', 200),
(3, 'fa-code', 'badge', 50),
(4, 'fa-heart text-danger', 'badge', 70),
(5, 'fa-check-circle text-success', 'badge', 70),
(6, 'bio', 'upgrade', 50);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `post_img` varchar(15) NOT NULL,
  `date` datetime NOT NULL,
  `contest` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `description`, `post_img`, `date`, `contest`) VALUES
(1, 2, '', 'post_1.jpg', '2019-06-10 07:27:14', 1),
(2, 4, '', 'post_2.jpg', '2019-06-10 07:27:35', 1),
(3, 1, 'contest post', 'post_3.jpg', '2019-06-10 09:45:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `post_tag`
--

CREATE TABLE `post_tag` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `id` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tag`) VALUES
(1, 'php2');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_img` varchar(60) NOT NULL,
  `bio` varchar(200) NOT NULL,
  `bio_limit` int(100) NOT NULL,
  `fav_tag_limit` int(11) NOT NULL,
  `contest_tokens` int(11) NOT NULL,
  `contest_join` tinyint(1) NOT NULL,
  `contest_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `profile_img`, `bio`, `bio_limit`, `fav_tag_limit`, `contest_tokens`, `contest_join`, `contest_date`) VALUES
(1, 'Iosif', 'Livadaru', 'iosiflivadaru@yahoo.com', '$2y$10$DSZevnIA1o4I/SdJXc2Eu.Wljqj2nRaH5alAwiEABLl9S8KtFYHq6', '1_Iosif_Livadaru.jpg', '', 100, 1, 0, 1, '2019-06-17'),
(2, 'Not', 'Iosif', 'ioiliv05@gmail.com', '$2y$10$16/IlsmtqKmkkDOaXMaLNu2hhvZgU0YQwM1rq9K3vwlUobBg83gQy', '2_Not_Iosif.jpg', '', 100, 1, 0, 1, '2019-06-18'),
(3, 'Test', 'Test', 'test@test.com', '$2y$10$1hiG2w9FBcWaye8otvbqBectacsv1OEuJMe82JPB7NSiSUZPvUswm', '3_Test_Test.jpg', '', 100, 1, 0, 0, '2019-06-18'),
(4, 'Jacob', 'Tichenor', 'jake@gmail.com', '$2y$10$w20CbkLr.pLgXdFLnAgON.fODOdsbOixfl5jcVdZhgP7QhpH78yLq', '4_Jacob_Tichenor.jpg', '', 100, 1, 0, 1, '2019-06-18');

-- --------------------------------------------------------

--
-- Table structure for table `user_badge`
--

CREATE TABLE `user_badge` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contest`
--
ALTER TABLE `contest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `like`
--
ALTER TABLE `like`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market`
--
ALTER TABLE `market`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_tag`
--
ALTER TABLE `post_tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contest`
--
ALTER TABLE `contest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `like`
--
ALTER TABLE `like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `market`
--
ALTER TABLE `market`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `post_tag`
--
ALTER TABLE `post_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `reset contest` ON SCHEDULE EVERY 7 DAY STARTS '2019-06-10 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE contest SET active = 0 WHERE active = 1$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
