-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2024 at 10:02 PM
-- Server version: 10.6.19-MariaDB-cll-lve
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vitovisu_joyfent`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` text NOT NULL,
  `description` text DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `status` enum('open','closed','canceled') DEFAULT 'open',
  `image_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `date`, `time`, `location`, `description`, `max_participants`, `status`, `image_url`) VALUES
(4, 'Raiden', '2024-11-09', '09:00:00', 'ICE BSD', 'wibu', 50, 'open', '671950113d19a-raidenEiBg.jpg'),
(9, 'Chise', '2024-11-01', '11:00:00', 'Iyyh', 'Iyyh', 22, 'open', '67195fe4bebe7-chise.gif'),
(11, 'Elaa', '2024-11-20', '14:00:00', 'Holoid', 'Elaa', 21, 'open', '67196b3f257df-ela.gif'),
(12, 'Mirai', '2024-11-21', '21:00:00', 'My Heart', 'Istri 1', 21, 'open', '67196b8ac5374-mirai.gif'),
(44, 'Owl Baron', '2024-11-12', '21:00:00', 'Geffen', 'Baron', 44, 'open', '671ac814c8fac-Nitro_Wallpaper_01_3840x2400.jpg'),
(45, 'Orc Lord', '2024-11-19', '22:30:00', 'Orc Forest', 'Orc Lord', 77, 'open', '671ac83c7b533-Nitro_Wallpaper_02_3840x2400.jpg'),
(46, 'Alatreon', '2024-12-04', '11:30:00', 'Secluded Valley', 'Kill Alatreon', 4, 'open', '671ac860ea00d-Nitro_Wallpaper_03_3840x2400.jpg'),
(47, 'Fatalis', '2024-11-22', '23:00:00', 'Old World', 'Kill Fatalis', 1, 'open', '671ac8840c7d8-Nitro_Wallpaper_04_3840x2400.jpg'),
(48, 'Saffi Jiva', '2024-12-03', '13:00:00', 'Secluded Valley', 'Kill Saffi Jiva', 12, 'open', '671ac8a4ef787-Nitro_Wallpaper_05_3840x2400.jpg'),
(49, 'Velkhana', '2024-11-13', '20:30:00', 'Hoara', 'Kill Velkhana', 61, 'open', '671ac8cd70bfc-Nitro_Wallpaper_06_3840x2400.jpg'),
(50, 'Namielle', '2024-12-05', '16:00:00', 'Coral Island', 'Kill Namielle', 89, 'open', '671ac8ec58983-Nitro_Wallpaper_07_3840x2400.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_temp`
--

CREATE TABLE `password_reset_temp` (
  `email` varchar(250) NOT NULL,
  `key` varchar(250) NOT NULL,
  `expDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `user_id`, `event_id`, `registered_at`) VALUES
(57, 11, 44, '2024-10-25 12:35:45'),
(58, 11, 49, '2024-10-25 12:35:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default-avatar.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `reset_token`, `reset_expiry`, `profile_image`) VALUES
(1, 'admin', 'jerdom17@gmail.com', '$2y$10$y2zzjWijPbJlJ0PuZatB3.LnUWSZZFeSSYGzgcswFWxOGf4NxRPBO', 'admin', NULL, '2024-10-25 09:44:06', 'default-avatar.png'),
(4, 'reza', 'reza@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', NULL, NULL, 'default-avatar.png'),
(5, 'jeremy', 'adadcaf@afuojb', '65a54865de989d0a6a60a8ad5b07e071', 'user', NULL, NULL, 'default-avatar.png'),
(6, 'baru', 'dad@ada', '202cb962ac59075b964b07152d234b70', 'user', NULL, NULL, 'default-avatar.png'),
(7, 'FORGOT', 'zoomjeremy17@gmail.com', '$2y$10$Pp6jkD1oWmdTYBKr7Y.EtOhlTi1z11jMhLIXQy7FDmRf5qSykXl26', 'user', NULL, '2024-10-25 14:17:28', 'default-avatar.png'),
(8, 'user', 'user@gmail.com', '$2y$10$B5WwoZg1OvwHgBvSTNObLuuGg6QnmUfveB.YjukG7Z/UIrA08SXsu', 'user', NULL, NULL, 'default-avatar.png'),
(9, 'asepais', 'denitosamosir@gmail.com', '$2y$10$GePmrTW4YiL9s2coBk6W2.jsvdHb/42iwNUI.YkBxRBKO/L99/1jW', 'user', NULL, '2024-10-25 15:50:08', 'default-avatar.png'),
(10, 'denito', 'denito@gmail.com', '$2y$10$YGKvqZYTzKxuui/n0sSr8O2kCWNO4bs3z25Ybu4eQ1r/NeQWC9MSm', 'user', NULL, NULL, 'default-avatar.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
