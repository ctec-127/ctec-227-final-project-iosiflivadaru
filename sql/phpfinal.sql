-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2019 at 10:26 AM
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

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `user_id`, `post_id`, `comment`, `date`) VALUES
(1, 3, 10, 'What a strange, yet cool image. Good job, bro.', '2019-06-12 23:06:18'),
(2, 3, 10, 'Can I just spam you with comments?', '2019-06-12 23:06:28'),
(3, 3, 10, 'Don\'t you like me, you never respond.', '2019-06-12 23:06:36'),
(4, 3, 10, 'Can I program a bot to do this for me?', '2019-06-12 23:06:47'),
(5, 3, 10, 'I wonder what the limit is on the comment form. I\'m going to just type a bunch even though the width of the input field tells me I should be brief. But I\'m not a brief person. I\'m long-winded. Did I mention I\'m stubborn. What if I hav a tipo. Can I go back and edit my comment? Or am I stuck in my typing skills. Okay, so far there is no limit. Wow. I\'m typing a lot. Iosif, you still there? hahaha', '2019-06-12 23:08:11'),
(6, 3, 9, 'This image looks like Iosif\'s. Are you stealing his work?', '2019-06-12 23:09:09'),
(7, 1, 15, 'haha', '2019-06-13 08:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `contest`
--

CREATE TABLE `contest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contest`
--

INSERT INTO `contest` (`id`, `user_id`, `post_id`) VALUES
(1, 2, 9),
(2, 1, 10),
(3, 1, 9),
(4, 3, 10),
(5, 1, 14),
(6, 1, 19),
(7, 5, 14),
(8, 5, 9),
(9, 5, 19);

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
(2, 3, 1),
(4, 6, 2),
(5, 6, 3),
(6, 4, 2),
(8, 5, 3),
(9, 5, 4),
(10, 5, 1),
(12, 1, 2),
(13, 1, 6),
(14, 2, 5),
(15, 2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE `like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `like`
--

INSERT INTO `like` (`id`, `user_id`, `post_id`) VALUES
(1, 1, 9);

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
  `contest` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `description`, `post_img`, `date`, `contest`, `active`) VALUES
(9, 2, 'Contest Post', 'post_9.jpg', '2019-06-12 22:39:38', 1, 1),
(11, 3, 'How am I supposed to LivIt? Why should I add tags? What is this for? Existential post for Living It. Are these global tags? Why should I care about someone else\'s tags? Do I have a limit to how much I', 'noImg', '2019-06-12 23:01:10', 0, 0),
(12, 3, 'I have discovered the limit! Varchar! :-)', 'noImg', '2019-06-12 23:01:39', 0, 0),
(14, 3, '', 'post_14.jpg', '2019-06-12 23:04:55', 1, 1),
(15, 3, 'The million dollar question: How do I delete my account?', 'noImg', '2019-06-12 23:14:16', 0, 0),
(16, 1, 'hello', 'noImg', '2019-06-13 08:51:45', 0, 0),
(19, 1, 'Contest time!!!', 'post_19.jpg', '2019-06-13 09:09:11', 1, 1),
(20, 5, 'My first post here wow!!!', 'noImg', '2019-06-13 10:01:42', 0, 0);

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
(3, 2, 1),
(4, 3, 1),
(5, 11, 3),
(6, 11, 2),
(7, 14, 2),
(8, 10, 1),
(9, 16, 1),
(10, 16, 2),
(11, 17, 1),
(12, 17, 4);

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
(1, 'php2'),
(2, 'random'),
(3, 'ux'),
(4, 'nodeJS'),
(5, 'contest'),
(6, 'verified'),
(7, 'welcome');

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
(1, 'Iosif', 'Livadaru', 'iosiflivadaru@yahoo.com', '$2y$10$P0/2FHq31P7HxpU58WtB5.oMs5FykGcHEYT1S709KZ4Hiq9QZW3Ta', '1_Iosif_Livadaru.jpg', 'PHP is never boring!!!', 140, 1, 1000, 1, '2019-06-17'),
(2, 'Jacob', 'Tichenor', 'jake@gmail.com', '$2y$10$dG6a3cv8s0.WJNbR1jP.NOlcuolpombXPhP4GtwtnBI2DjlBGAOzy', '2_Jacob_Tichenor.jpg', '', 100, 1, 0, 1, '0000-00-00'),
(3, 'Chris', 'Martin', 'cmartin@clark.edu', '$2y$10$wmAFGpP9K0/DMJdZdFG2euZRS8Q/unF0kYifUz8r39nBe6pNgl16i', '3_Chris_Martin.jpg', 'So I just got 20 extra characters?', 120, 1, 310, 1, '0000-00-00'),
(4, 'Maybe', 'Iosif', 'ioiliv05@gmail.com', '$2y$10$HX2Q8bty4Mx470zZ8LMBHe6iArr6QAy2Fk88omDxERkliV6gX1.12', '4_Maybe_Iosif.jpg', '', 100, 1, 0, 0, '0000-00-00'),
(5, 'Someone', 'Else', 'someone@else.com', '$2y$10$Hh39k/ch7bEnChqqDAz0neUwisWgfV9NPp1zHKnPN6R4uIWgkMGZi', '5_Someone_Else.jpg', '', 100, 1, 0, 0, '0000-00-00'),
(6, 'Test', 'Test', 'test@test.com', '$2y$10$l.MytTTOfHbBvVa3pzZbp.tsbyPPWc2HJK2kaKBryKTzsFvlA3ANi', '6_Test_Test.jpg', '', 100, 1, 0, 0, '0000-00-00'),
(7, 'Not', 'Iosif', 'not@iosif.com', '$2y$10$YgOaDqz1C2rN27bYs24AY.1FnTHcZaGTIXSeC3Up5c7JCqOJjuFTa', '7_Not_Iosif.jpg', '', 100, 1, 0, 0, '0000-00-00');

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
(3, 1),
(3, 3),
(3, 2),
(3, 4),
(3, 5);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contest`
--
ALTER TABLE `contest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `like`
--
ALTER TABLE `like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `market`
--
ALTER TABLE `market`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `post_tag`
--
ALTER TABLE `post_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `reset contest` ON SCHEDULE EVERY 7 DAY STARTS '2019-06-10 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE post SET active = 0 WHERE active = 1$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
