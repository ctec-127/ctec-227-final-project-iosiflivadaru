-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2019 at 07:24 AM
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
-- Table structure for table `badge`
--

CREATE TABLE `badge` (
  `id` int(11) NOT NULL,
  `badge` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `badge`
--

INSERT INTO `badge` (`id`, `badge`) VALUES
(1, 'fa-user-check text-primary'),
(2, 'fa-certificate text-warning'),
(3, 'fa-code'),
(4, 'fa-heart text-danger'),
(5, 'fa-check-circle text-success');

-- --------------------------------------------------------

--
-- Table structure for table `fav_tag`
--

CREATE TABLE `fav_tag` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fav_tag`
--

INSERT INTO `fav_tag` (`id`, `user_id`, `tag_id`) VALUES
(1, 3, 1);

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
(18, 1, 3),
(19, 2, 1),
(21, 2, 3),
(23, 3, 1),
(26, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `post_img` varchar(15) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `description`, `post_img`, `date`) VALUES
(1, 1, 'First Post', 'noImg', '2019-05-28 00:00:00'),
(2, 3, 'Jake\'s first post!!!', 'noImg', '2019-05-28 00:00:00'),
(3, 3, 'yup!', 'noImg', '2019-05-28 23:43:37'),
(4, 3, 'ORDER BY DATE!!!!', 'post_4.jpg', '2019-05-28 23:44:14'),
(5, 1, 'I know right?!!!', 'noImg', '2019-05-28 23:44:34'),
(6, 1, 'This post is from my phone!!', 'noImg', '2019-05-29 05:30:02'),
(7, 1, 'Post time ago', 'noImg', '2019-05-29 22:08:01');

-- --------------------------------------------------------

--
-- Table structure for table `post_tag`
--

CREATE TABLE `post_tag` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post_tag`
--

INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`) VALUES
(1, 6, 1),
(2, 6, 3);

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
(1, 'php'),
(2, 'PHP'),
(3, 'js'),
(4, 'contest');

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
  `contest_join` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `profile_img`, `bio`, `bio_limit`, `fav_tag_limit`, `contest_tokens`, `contest_join`) VALUES
(1, 'Iosif', 'Livadaru', 'iosiflivadaru@yahoo.com', '$2y$10$9Mh4QBI6yC1Qiy5hJ2VpHufSifP44NcK4r2mag3A9nAl/.INTf0mu', '1_Iosif_Livadaru.jpg', 'Hello there! Welcome to my profile! Make sure to follow me and like my posts! Have fun on the app!!!', 100, 1, 0, 0),
(2, 'Not', 'Iosif', 'ioiliv05@gmail.com', '$2y$10$ZCR5zyHlgxobqZk.FT2o/uH7Edy1Qg56dJZIovI45RYdI9hO9.8VO', '2_Not_Iosif.jpg', '', 100, 1, 0, 0),
(3, 'Jake', 'Tichenor', 'jake@gmail.com', '$2y$10$XLev/Yi.lUUjkZO5fwFl8eF/L95DidX9F/y9P14Xq66V7.a9Vvd4O', '3_Jacob_Tichenor.jpg', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quibusdam dignissimos tempora cum delectus', 100, 1, 0, 0),
(4, 'Beniamin', 'Livadaru', 'benyliv@gmail.com', '$2y$10$G3JGQCAPpl.DCVIiw6wK9ex21tj4mfR/c/U3p/klFBD98Mx.EmKQO', '4_Beniamin_Livadaru.jpeg', '', 100, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_badge`
--

CREATE TABLE `user_badge` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_badge`
--

INSERT INTO `user_badge` (`user_id`, `badge_id`) VALUES
(1, 1),
(1, 3),
(3, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badge`
--
ALTER TABLE `badge`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fav_tag`
--
ALTER TABLE `fav_tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
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
-- AUTO_INCREMENT for table `badge`
--
ALTER TABLE `badge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fav_tag`
--
ALTER TABLE `fav_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `post_tag`
--
ALTER TABLE `post_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
