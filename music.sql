-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2025 at 07:19 PM
-- Server version: 8.0.41-cll-lve
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `music`
--

-- --------------------------------------------------------

--
-- Table structure for table `Songs`
--

CREATE TABLE `Songs` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `audio_url` varchar(500) NOT NULL,
  `album` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Songs`
--

INSERT INTO `Songs` (`id`, `title`, `artist`, `image_url`, `audio_url`, `album`, `genre`) VALUES
(4, 'نامه ای به مادر', 'ریمیکس', 'https://tajmusics.com/wp-content/uploads/2024/01/Shayea-&-Putak-&-Gandom-Madar-(Dj-Sajjad-Remix-R)-(Remix).webp', 'https://musicviral.musitraf.com/Music/04-03/Mrzhak%20x%20Shayea%20x%20Mj%20x%20Hichkas%20x%20Maslak%20-%20NAMEY%20BE%20MADAR.mp3', '', 'ریمیکس'),
(5, 'نامه ای به پدر', 'ریمیکس', 'https://musicviral.ir/wp-content/uploads/2025/02/Yas-Hichkas-Khalse-Masin-Owj-Putak---Nameiy-Be-Pedar.webp', 'https://musicviral.musitraf.com/Music/03-12/Yas%20Hichkas%20Khalse%20Masin%20Owj%20Putak%20-%20Nameiy%20Be%20Pedar.mp3', '', 'ریمیکس'),
(6, 'زده بالا 2', 'ریمیکس', 'https://musicchi.net/wp-content/uploads/2024/10/Iman-Haji-Nezhad-&-Mahdyar-Zad-Bala-2-2024-10-08-00-12.jpg', 'https://dl.musicchi.net/1403/07/17/Iman%20Haji%20Nezhad%20&%20Mahdyar%20-%20Zad%20Bala%202.mp3', '', 'ریمیکس'),
(7, 'منم دلم میخواست', 'ریمیکس', 'https://musicchi.net/wp-content/uploads/2024/05/Sorena-&-Bahram-&-Shayea-&-Pishro-MANAM-DELAM-MIKHAST-(Zero-Remix)-2024-05-17-00-39.jpg', 'https://dl.musicchi.net/1403/02/28/Sorena%20&%20Bahram%20&%20Shayea%20&%20Pishro%20-%20MANAM%20DELAM%20MIKHAST%20(Zero%20Remix).mp3', '', 'ریمیکس'),
(8, 'مرافه', 'ریمیکس', 'https://tajmusics.com/wp-content/uploads/2024/12/Hamidreza-Babaei-x-Ramin-Tajangi-x-Vinak-x-Shayea-x-Tataloo-Morafe-(Remix).webp', 'https://musictaj.musitraf.com/song403/zmstn/Hamidreza%20Babaei%20x%20Ramin%20Tajangi%20x%20Vinak%20x%20Shayea%20x%20Tataloo%20-%20Morafe%20(Remix).mp3', '', 'ریمیکس'),
(9, 'مرافه 2', 'ریمیکس', 'https://melonmusic.ir/wp-content/uploads/2025/05/aryanerg-morafe-2.jpg', 'https://cdns.musicsmelon.com/Music/Aryanerg%20-%20Morafe%202%20(Remix-320).mp3', '', 'ریمیکس'),
(10, 'غمین', 'ریمیکس', 'https://shahremix.ir/wp-content/uploads/2025/05/4231-SowloGhamin-shahremix.ir.jpg', 'https://dl6.topsongs.ir/music/s/sowlo-&-jimi/Sowlo-&-JimiGhamin-320.mp3', '', 'ریمیکس'),
(11, 'علی لال درد', 'ریمیکس', 'https://shahremix.ir/wp-content/uploads/2025/04/7397-Iman-HajinezhadLale-Dard-shahremix.ir.jpg', 'https://dl6.shahremix.ir/music/i/iman-hajinezhad/Iman-HajinezhadLale-Dard-320.mp3', '', 'ریمیکس'),
(12, 'از آن شبی که برنگشتی', 'ریمیکس', 'https://tajmusics.com/wp-content/uploads/2025/01/Iraj-Bastami-x-Pishro-x-Shayea-x-Hiphopologist-x-Maslak-x-Vinak-x-Naaji-x-Poori-x-Daniyal-Az-An-Shabi-Ke-Barnagashti-(Remix).webp', 'https://musictaj.musitraf.com/song403/zmstn/Iraj%20Bastami%20x%20Pishro%20x%20Shayea%20x%20Hiphopologist%20x%20Maslak%20x%20Vinak%20x%20Naaji%20x%20Poori%20x%20Daniyal%20-%20Az%20An%20Shabi%20Ke%20Barnagashti%20(Remix).mp3', '', 'ریمیکس'),
(13, 'ثانیه ها', 'ریمیکس', 'https://music-mehr.com/wp-content/uploads/2025/04/%D8%B1%DB%8C%D9%85%DB%8C%DA%A9%D8%B3-%D8%AB%D8%A7%D9%86%DB%8C%D9%87-%D9%87%D8%A7.jpg', 'https://mehrdl.musitraf.com/Music/A/S/New/Ali%20Sorena%20x%20Bahram%20x%20Aiso%20Beatz%20x%20Komeyl%20Remix%20-%20Sanie%20Ha.mp3', '', 'ریمیکس'),
(14, 'نوکرتم ننه', 'ریمیکس', 'https://musicchi.net/wp-content/uploads/2024/09/Mehrab-&-Ali-Sorena-Nokaretam-Nane-Jahan-Che-Dandoone-Nane-2024-09-22-23-24.jpg', 'https://dl.musicchi.net/1403/07/01/Mehrab%20&%20Ali%20Sorena%20-%20Nokaretam%20Nane%20Jahan%20Che%20Dandoone%20Nane.mp3', '', 'ریمیکس'),
(15, 'yener 2', 'ترک', 'https://ahlemusic.eu/wp-content/uploads/2025/04/Afra-Wayb-Yener-2.0-300x300.jpg', 'https://dl.ahlemusic.eu/music403/Zemeston/Afra%20&%20Wayb%20-%20Yener%202.0.mp3', '', 'ترک'),
(16, 'اون شب', 'ریمیکس', 'https://shahremix.ir/wp-content/uploads/2025/04/3453-HwmidrezwOn-Shab-shahremix.ir.jpg', 'https://dl6.shahremix.ir/music/h/hwmidrezw/HwmidrezwOn-Shab-320.mp3', '', 'ریمیکس'),
(17, 'nofel', 'ترک', 'https://beys-music.ir/wp-content/uploads/2021/11/tural-ali-nofel.jpg', 'https://dl.beysmusic.ir/music/T/Tural..Ali/Nofel/Tural..Ali.Nofel.320.BeysMusic.ir.mp3', '', 'ترک'),
(18, 'با صدای بی صدا', 'ریمیکس', 'https://musicchi.net/wp-content/uploads/2025/02/Farhad-x-Bahram-x-Ali-Sorena-x-Pishro-x-Yas-Bi-Seda-2025-02-15-23-07.jpg', 'https://dl.musicchi.net/1403/11/27/Farhad%20x%20Bahram%20x%20Ali%20Sorena%20x%20Pishro%20x%20Yas%20-%20Bi%20Seda.mp3', '', 'ریمیکس'),
(19, 'سکوت', 'ریمیکس', 'https://delahang.com/wp-content/uploads/2025/04/%D8%B1%DB%8C%D9%85%DB%8C%DA%A9%D8%B3-%D8%B3%DA%A9%D9%88%D8%AA.jpg', 'https://dldel.musitraf.com/2025/4/New/Aryanerg%20-%20Sokoot%20(Remix-320).mp3', '', 'ریمیکس');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone_number`, `created_at`) VALUES
(1, '', '2025-06-05 19:15:51'),
(2, '', '2025-06-05 19:16:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Songs`
--
ALTER TABLE `Songs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Songs`
--
ALTER TABLE `Songs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
