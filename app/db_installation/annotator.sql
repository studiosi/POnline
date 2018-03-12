-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2018 at 06:17 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `annotator`
--
CREATE DATABASE IF NOT EXISTS `annotator` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `annotator`;

-- --------------------------------------------------------

--
-- Table structure for table `clicks`
--

DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
  `id` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `id_player` int(11) DEFAULT NULL,
  `human_generated` tinyint(1) NOT NULL,
  `distance` float NOT NULL,
  `click_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `clicks_per_photo`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `clicks_per_photo`;
CREATE TABLE `clicks_per_photo` (
`id_photo` int(11)
,`n_clicks` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `clicks_per_player`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `clicks_per_player`;
CREATE TABLE `clicks_per_player` (
`id` int(11)
,`username` text
,`status` text
,`id_player` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `filename`) VALUES
(1, 'Img_001_L_1.jpg'),
(2, 'Img_001_L_2.jpg'),
(3, 'Img_001_L_3.jpg'),
(4, 'Img_001_L_4.jpg'),
(5, 'Img_001_L_5.jpg'),
(6, 'Img_001_R_1.jpg'),
(7, 'Img_001_R_2.jpg'),
(8, 'Img_001_R_3.jpg'),
(9, 'Img_001_R_4.jpg'),
(10, 'Img_001_R_5.jpg'),
(11, 'Img_002_L_2.jpg'),
(12, 'Img_002_L_3.jpg'),
(13, 'Img_002_L_4.jpg'),
(14, 'Img_002_L_5.jpg'),
(15, 'Img_002_R_1.jpg'),
(16, 'Img_002_R_2.jpg'),
(17, 'Img_002_R_3.jpg'),
(18, 'Img_002_R_4.jpg'),
(19, 'Img_002_R_5.jpg'),
(20, 'Img_002_R_6.jpg'),
(21, 'Img_003_L_1.jpg'),
(22, 'Img_003_L_2.jpg'),
(23, 'Img_003_L_3.jpg'),
(24, 'Img_003_L_4.jpg'),
(25, 'Img_003_L_5.jpg'),
(26, 'Img_003_R_1.jpg'),
(27, 'Img_003_R_2.jpg'),
(28, 'Img_003_R_3.jpg'),
(29, 'Img_003_R_4.jpg'),
(30, 'Img_003_R_5.jpg'),
(31, 'Img_004_L_1.jpg'),
(32, 'Img_004_L_2.jpg'),
(33, 'Img_004_L_3.jpg'),
(34, 'Img_004_L_4.jpg'),
(35, 'Img_004_L_5.jpg'),
(36, 'Img_004_R_1.jpg'),
(37, 'Img_004_R_2.jpg'),
(38, 'Img_004_R_3.jpg'),
(39, 'Img_004_R_4.jpg'),
(40, 'Img_004_R_5.jpg'),
(41, 'Img_005_L_1.jpg'),
(42, 'Img_005_L_2.jpg'),
(43, 'Img_005_L_3.jpg'),
(44, 'Img_005_L_4.jpg'),
(45, 'Img_005_L_5.jpg'),
(46, 'Img_005_R_1.jpg'),
(47, 'Img_005_R_2.jpg'),
(48, 'Img_005_R_3.jpg'),
(49, 'Img_005_R_4.jpg'),
(50, 'Img_005_R_5.jpg'),
(51, 'Img_006_L_1.jpg'),
(52, 'Img_006_L_2.jpg'),
(53, 'Img_006_L_3.jpg'),
(54, 'Img_006_L_4.jpg'),
(55, 'Img_006_L_5.jpg'),
(56, 'Img_006_L_6.jpg'),
(57, 'Img_006_R_1.jpg'),
(58, 'Img_006_R_3.jpg'),
(59, 'Img_006_R_4.jpg'),
(60, 'Img_006_R_5.jpg'),
(61, 'Img_007_L_1.jpg'),
(62, 'Img_007_L_2.jpg'),
(63, 'Img_007_L_3.jpg'),
(64, 'Img_007_L_4.jpg'),
(65, 'Img_007_L_5.jpg'),
(66, 'Img_007_R_1.jpg'),
(67, 'Img_007_R_2.jpg'),
(68, 'Img_007_R_3.jpg'),
(69, 'Img_007_R_4.jpg'),
(70, 'Img_007_R_5.jpg'),
(71, 'Img_008_L_1.jpg'),
(72, 'Img_008_L_2.jpg'),
(73, 'Img_008_L_3.jpg'),
(74, 'Img_008_L_4.jpg'),
(75, 'Img_008_R_1.jpg'),
(76, 'Img_008_R_2.jpg'),
(77, 'Img_008_R_3.jpg'),
(78, 'Img_008_R_4.jpg'),
(79, 'Img_008_R_5.jpg'),
(80, 'Img_008_R_6.jpg'),
(81, 'Img_009_L_1.jpg'),
(82, 'Img_009_L_2.jpg'),
(83, 'Img_009_L_3.jpg'),
(84, 'Img_009_L_4.jpg'),
(85, 'Img_009_L_5.jpg'),
(86, 'Img_009_R_1.jpg'),
(87, 'Img_009_R_2.jpg'),
(88, 'Img_009_R_3.jpg'),
(89, 'Img_009_R_4.jpg'),
(90, 'Img_009_R_5.jpg'),
(91, 'Img_010_L_1.jpg'),
(92, 'Img_010_L_2.jpg'),
(93, 'Img_010_L_3.jpg'),
(94, 'Img_010_L_4.jpg'),
(95, 'Img_010_L_5.jpg'),
(96, 'Img_010_R_1.jpg'),
(97, 'Img_010_R_2.jpg'),
(98, 'Img_010_R_3.jpg'),
(99, 'Img_010_R_4.jpg'),
(100, 'Img_010_R_5.jpg'),
(101, 'Img_011_L_1.jpg'),
(102, 'Img_011_L_2.jpg'),
(103, 'Img_011_L_3.jpg'),
(104, 'Img_011_L_4.jpg'),
(105, 'Img_011_L_5.jpg'),
(106, 'Img_011_R_1.jpg'),
(107, 'Img_011_R_2.jpg'),
(108, 'Img_011_R_3.jpg'),
(109, 'Img_011_R_4.jpg'),
(110, 'Img_011_R_5.jpg'),
(111, 'Img_012_L_1.jpg'),
(112, 'Img_012_L_2.jpg'),
(113, 'Img_012_L_3.jpg'),
(114, 'Img_012_L_4.jpg'),
(115, 'Img_012_L_5.jpg'),
(116, 'Img_012_R_1.jpg'),
(117, 'Img_012_R_2.jpg'),
(118, 'Img_012_R_3.jpg'),
(119, 'Img_012_R_4.jpg'),
(120, 'Img_012_R_5.jpg'),
(121, 'Img_013_L_1.jpg'),
(122, 'Img_013_L_2.jpg'),
(123, 'Img_013_L_3.jpg'),
(124, 'Img_013_L_4.jpg'),
(125, 'Img_013_L_5.jpg'),
(126, 'Img_013_R_1.jpg'),
(127, 'Img_013_R_2.jpg'),
(128, 'Img_013_R_3.jpg'),
(129, 'Img_013_R_4.jpg'),
(130, 'Img_013_R_5.jpg'),
(131, 'Img_014_L_1.jpg'),
(132, 'Img_014_L_2.jpg'),
(133, 'Img_014_L_3.jpg'),
(134, 'Img_014_L_4.jpg'),
(135, 'Img_014_L_5.jpg'),
(136, 'Img_014_R_1.jpg'),
(137, 'Img_014_R_2.jpg'),
(138, 'Img_014_R_3.jpg'),
(139, 'Img_014_R_4.jpg'),
(140, 'Img_014_R_5.jpg'),
(141, 'Img_015_L_1.jpg'),
(142, 'Img_015_L_2.jpg'),
(143, 'Img_015_L_3.jpg'),
(144, 'Img_015_L_4.jpg'),
(145, 'Img_015_L_5.jpg'),
(146, 'Img_015_R_1.jpg'),
(147, 'Img_015_R_2.jpg'),
(148, 'Img_015_R_3.jpg'),
(149, 'Img_015_R_4.jpg'),
(150, 'Img_015_R_5.jpg'),
(151, 'Img_016_L_1.jpg'),
(152, 'Img_016_L_2.jpg'),
(153, 'Img_016_L_3.jpg'),
(154, 'Img_016_L_4.jpg'),
(155, 'Img_016_L_5.jpg'),
(156, 'Img_016_R_1.jpg'),
(157, 'Img_016_R_2.jpg'),
(158, 'Img_016_R_3.jpg'),
(159, 'Img_016_R_4.jpg'),
(160, 'Img_016_R_5.jpg'),
(161, 'Img_017_L_1.jpg'),
(162, 'Img_017_L_2.jpg'),
(163, 'Img_017_L_3.jpg'),
(164, 'Img_017_L_4.jpg'),
(165, 'Img_017_L_5.jpg'),
(166, 'Img_017_R_1.jpg'),
(167, 'Img_017_R_2.jpg'),
(168, 'Img_017_R_3.jpg'),
(169, 'Img_017_R_4.jpg'),
(170, 'Img_017_R_5.jpg'),
(171, 'Img_018_L_1.jpg'),
(172, 'Img_018_L_2.jpg'),
(173, 'Img_018_L_3.jpg'),
(174, 'Img_018_L_4.jpg'),
(175, 'Img_018_L_5.jpg'),
(176, 'Img_018_R_1.jpg'),
(177, 'Img_018_R_2.jpg'),
(178, 'Img_018_R_3.jpg'),
(179, 'Img_018_R_4.jpg'),
(180, 'Img_018_R_5.jpg'),
(181, 'Img_019_L_1.jpg'),
(182, 'Img_019_L_2.jpg'),
(183, 'Img_019_L_3.jpg'),
(184, 'Img_019_L_4.jpg'),
(185, 'Img_019_L_5.jpg'),
(186, 'Img_019_R_1.jpg'),
(187, 'Img_019_R_2.jpg'),
(188, 'Img_019_R_3.jpg'),
(189, 'Img_019_R_4.jpg'),
(190, 'Img_019_R_5.jpg'),
(191, 'Img_020_L_1.jpg'),
(192, 'Img_020_L_2.jpg'),
(193, 'Img_020_L_3.jpg'),
(194, 'Img_020_L_4.jpg'),
(195, 'Img_020_L_5.jpg'),
(196, 'Img_020_R_1.jpg'),
(197, 'Img_020_R_2.jpg'),
(198, 'Img_020_R_3.jpg'),
(199, 'Img_020_R_4.jpg'),
(200, 'Img_020_R_5.jpg'),
(201, 'Img_021_L_1.jpg'),
(202, 'Img_021_L_2.jpg'),
(203, 'Img_021_L_3.jpg'),
(204, 'Img_021_L_4.jpg'),
(205, 'Img_021_L_5.jpg'),
(206, 'Img_021_R_1.jpg'),
(207, 'Img_021_R_2.jpg'),
(208, 'Img_021_R_3.jpg'),
(209, 'Img_021_R_4.jpg'),
(210, 'Img_021_R_5.jpg'),
(211, 'Img_022_L_1.jpg'),
(212, 'Img_022_L_2.jpg'),
(213, 'Img_022_L_3.jpg'),
(214, 'Img_022_L_4.jpg'),
(215, 'Img_022_L_5.jpg'),
(216, 'Img_022_R_1.jpg'),
(217, 'Img_022_R_2.jpg'),
(218, 'Img_022_R_3.jpg'),
(219, 'Img_022_R_4.jpg'),
(220, 'Img_022_R_5.jpg'),
(221, 'Img_023_L_1.jpg'),
(222, 'Img_023_L_2.jpg'),
(223, 'Img_023_L_3.jpg'),
(224, 'Img_023_L_4.jpg'),
(225, 'Img_023_L_5.jpg'),
(226, 'Img_023_R_1.jpg'),
(227, 'Img_023_R_2.jpg'),
(228, 'Img_023_R_3.jpg'),
(229, 'Img_023_R_4.jpg'),
(230, 'Img_023_R_5.jpg'),
(231, 'Img_024_L_1.jpg'),
(232, 'Img_024_L_2.jpg'),
(233, 'Img_024_L_3.jpg'),
(234, 'Img_024_L_4.jpg'),
(235, 'Img_024_L_5.jpg'),
(236, 'Img_024_R_1.jpg'),
(237, 'Img_024_R_2.jpg'),
(238, 'Img_024_R_3.jpg'),
(239, 'Img_024_R_4.jpg'),
(240, 'Img_024_R_5.jpg'),
(241, 'Img_025_L_1.jpg'),
(242, 'Img_025_L_2.jpg'),
(243, 'Img_025_L_3.jpg'),
(244, 'Img_025_L_4.jpg'),
(245, 'Img_025_L_5.jpg'),
(246, 'Img_025_R_1.jpg'),
(247, 'Img_025_R_2.jpg'),
(248, 'Img_025_R_3.jpg'),
(249, 'Img_025_R_4.jpg'),
(250, 'Img_026_L_1.jpg'),
(251, 'Img_026_L_2.jpg'),
(252, 'Img_026_L_3.jpg'),
(253, 'Img_026_L_4.jpg'),
(254, 'Img_026_L_5.jpg'),
(255, 'Img_026_R_1.jpg'),
(256, 'Img_026_R_2.jpg'),
(257, 'Img_026_R_3.jpg'),
(258, 'Img_026_R_4.jpg'),
(259, 'Img_026_R_5.jpg'),
(260, 'Img_027_L_1.jpg'),
(261, 'Img_027_L_2.jpg'),
(262, 'Img_027_L_3.jpg'),
(263, 'Img_027_L_4.jpg'),
(264, 'Img_027_L_5.jpg'),
(265, 'Img_027_R_1.jpg'),
(266, 'Img_027_R_2.jpg'),
(267, 'Img_027_R_3.jpg'),
(268, 'Img_027_R_4.jpg'),
(269, 'Img_027_R_5.jpg'),
(270, 'Img_028_L_1.jpg'),
(271, 'Img_028_L_2.jpg'),
(272, 'Img_028_L_3.jpg'),
(273, 'Img_028_L_4.jpg'),
(274, 'Img_028_L_5.jpg'),
(275, 'Img_028_R_1.jpg'),
(276, 'Img_028_R_2.jpg'),
(277, 'Img_028_R_4.jpg'),
(278, 'Img_028_R_5.jpg'),
(279, 'Img_029_L_1.jpg'),
(280, 'Img_029_L_2.jpg'),
(281, 'Img_029_L_3.jpg'),
(282, 'Img_029_L_4.jpg'),
(283, 'Img_029_L_5.jpg'),
(284, 'Img_029_R_1.jpg'),
(285, 'Img_029_R_2.jpg'),
(286, 'Img_029_R_3.jpg'),
(287, 'Img_029_R_4.jpg'),
(288, 'Img_029_R_5.jpg'),
(289, 'Img_030_L_1.jpg'),
(290, 'Img_030_L_2.jpg'),
(291, 'Img_030_L_3.jpg'),
(292, 'Img_030_L_4.jpg'),
(293, 'Img_030_L_5.jpg'),
(294, 'Img_030_R_1.jpg'),
(295, 'Img_030_R_2.jpg'),
(296, 'Img_030_R_3.jpg'),
(297, 'Img_030_R_4.jpg'),
(298, 'Img_030_R_5.jpg'),
(299, 'Img_031_L_1.jpg'),
(300, 'Img_031_L_2.jpg'),
(301, 'Img_031_L_3.jpg'),
(302, 'Img_031_L_4.jpg'),
(303, 'Img_031_L_5.jpg'),
(304, 'Img_031_R_1.jpg'),
(305, 'Img_031_R_2.jpg'),
(306, 'Img_031_R_3.jpg'),
(307, 'Img_031_R_4.jpg'),
(308, 'Img_031_R_5.jpg'),
(309, 'Img_032_L_1.jpg'),
(310, 'Img_032_L_2.jpg'),
(311, 'Img_032_L_3.jpg'),
(312, 'Img_032_L_4.jpg'),
(313, 'Img_032_L_5.jpg'),
(314, 'Img_032_R_1.jpg'),
(315, 'Img_032_R_2.jpg'),
(316, 'Img_032_R_3.jpg'),
(317, 'Img_032_R_4.jpg'),
(318, 'Img_032_R_5.jpg'),
(319, 'Img_033_L_1.jpg'),
(320, 'Img_033_L_2.jpg'),
(321, 'Img_033_L_3.jpg'),
(322, 'Img_033_L_4.jpg'),
(323, 'Img_033_L_5.jpg'),
(324, 'Img_033_R_1.jpg'),
(325, 'Img_033_R_2.jpg'),
(326, 'Img_033_R_3.jpg'),
(327, 'Img_033_R_4.jpg'),
(328, 'Img_033_R_5.jpg'),
(329, 'Img_034_L_1.jpg'),
(330, 'Img_034_L_2.jpg'),
(331, 'Img_034_L_3.jpg'),
(332, 'Img_034_L_4.jpg'),
(333, 'Img_034_L_5.jpg'),
(334, 'Img_034_R_1.jpg'),
(335, 'Img_034_R_2.jpg'),
(336, 'Img_034_R_3.jpg'),
(337, 'Img_034_R_4.jpg'),
(338, 'Img_034_R_5.jpg'),
(339, 'Img_035_L_1.jpg'),
(340, 'Img_035_L_2.jpg'),
(341, 'Img_035_L_3.jpg'),
(342, 'Img_035_L_4.jpg'),
(343, 'Img_035_L_5.jpg'),
(344, 'Img_035_R_1.jpg'),
(345, 'Img_035_R_2.jpg'),
(346, 'Img_035_R_3.jpg'),
(347, 'Img_035_R_4.jpg'),
(348, 'Img_036_L_1.jpg'),
(349, 'Img_036_L_2.jpg'),
(350, 'Img_036_L_3.jpg'),
(351, 'Img_036_L_4.jpg'),
(352, 'Img_036_L_5.jpg'),
(353, 'Img_036_R_1.jpg'),
(354, 'Img_036_R_2.jpg'),
(355, 'Img_036_R_3.jpg'),
(356, 'Img_036_R_4.jpg'),
(357, 'Img_036_R_5.jpg'),
(358, 'Img_037_L_1.jpg'),
(359, 'Img_037_L_2.jpg'),
(360, 'Img_037_L_3.jpg'),
(361, 'Img_037_L_4.jpg'),
(362, 'Img_037_L_5.jpg'),
(363, 'Img_037_R_1.jpg'),
(364, 'Img_037_R_2.jpg'),
(365, 'Img_037_R_3.jpg'),
(366, 'Img_037_R_4.jpg'),
(367, 'Img_037_R_5.jpg'),
(368, 'Img_038_L_1.jpg'),
(369, 'Img_038_L_2.jpg'),
(370, 'Img_038_L_3.jpg'),
(371, 'Img_038_L_4.jpg'),
(372, 'Img_038_L_5.jpg'),
(373, 'Img_038_R_1.jpg'),
(374, 'Img_038_R_2.jpg'),
(375, 'Img_038_R_3.jpg'),
(376, 'Img_038_R_4.jpg'),
(377, 'Img_038_R_5.jpg'),
(378, 'Img_039_L_1.jpg'),
(379, 'Img_039_L_2.jpg'),
(380, 'Img_039_L_3.jpg'),
(381, 'Img_039_L_4.jpg'),
(382, 'Img_039_L_5.jpg'),
(383, 'Img_039_R_1.jpg'),
(384, 'Img_039_R_2.jpg'),
(385, 'Img_039_R_3.jpg'),
(386, 'Img_039_R_4.jpg'),
(387, 'Img_039_R_5.jpg'),
(388, 'Img_040_L_1.jpg'),
(389, 'Img_040_L_2.jpg'),
(390, 'Img_040_L_3.jpg'),
(391, 'Img_040_L_4.jpg'),
(392, 'Img_040_L_5.jpg'),
(393, 'Img_040_R_1.jpg'),
(394, 'Img_040_R_2.jpg'),
(395, 'Img_040_R_3.jpg'),
(396, 'Img_040_R_4.jpg'),
(397, 'Img_040_R_5.jpg'),
(398, 'Img_041_L_1.jpg'),
(399, 'Img_041_L_2.jpg'),
(400, 'Img_041_L_3.jpg'),
(401, 'Img_041_L_4.jpg'),
(402, 'Img_041_L_5.jpg'),
(403, 'Img_041_R_1.jpg'),
(404, 'Img_041_R_2.jpg'),
(405, 'Img_041_R_3.jpg'),
(406, 'Img_041_R_4.jpg'),
(407, 'Img_041_R_5.jpg'),
(408, 'Img_042_L_1.jpg'),
(409, 'Img_042_L_2.jpg'),
(410, 'Img_042_L_3.jpg'),
(411, 'Img_042_L_4.jpg'),
(412, 'Img_042_L_5.jpg'),
(413, 'Img_042_R_1.jpg'),
(414, 'Img_042_R_2.jpg'),
(415, 'Img_042_R_3.jpg'),
(416, 'Img_042_R_4.jpg'),
(417, 'Img_042_R_5.jpg'),
(418, 'Img_043_L_1.jpg'),
(419, 'Img_043_L_2.jpg'),
(420, 'Img_043_L_3.jpg'),
(421, 'Img_043_L_4.jpg'),
(422, 'Img_043_L_5.jpg'),
(423, 'Img_043_R_1.jpg'),
(424, 'Img_043_R_2.jpg'),
(425, 'Img_043_R_3.jpg'),
(426, 'Img_043_R_4.jpg'),
(427, 'Img_043_R_5.jpg'),
(428, 'Img_044_L_1.jpg'),
(429, 'Img_044_L_2.jpg'),
(430, 'Img_044_L_3.jpg'),
(431, 'Img_044_L_4.jpg'),
(432, 'Img_044_L_5.jpg'),
(433, 'Img_044_R_1.jpg'),
(434, 'Img_044_R_2.jpg'),
(435, 'Img_044_R_3.jpg'),
(436, 'Img_044_R_4.jpg'),
(437, 'Img_045_L_1.jpg'),
(438, 'Img_045_L_2.jpg'),
(439, 'Img_045_L_3.jpg'),
(440, 'Img_045_L_4.jpg'),
(441, 'Img_045_L_5.jpg'),
(442, 'Img_045_R_1.jpg'),
(443, 'Img_045_R_2.jpg'),
(444, 'Img_045_R_3.jpg'),
(445, 'Img_045_R_4.jpg'),
(446, 'Img_045_R_5.jpg'),
(447, 'Img_046_L_1.jpg'),
(448, 'Img_046_L_2.jpg'),
(449, 'Img_046_L_3.jpg'),
(450, 'Img_046_L_4.jpg'),
(451, 'Img_046_L_5.jpg'),
(452, 'Img_046_R_1.jpg'),
(453, 'Img_046_R_2.jpg'),
(454, 'Img_046_R_3.jpg'),
(455, 'Img_046_R_4.jpg'),
(456, 'Img_046_R_5.jpg'),
(457, 'Img_047_L_1.jpg'),
(458, 'Img_047_L_2.jpg'),
(459, 'Img_047_L_3.jpg'),
(460, 'Img_047_L_4.jpg'),
(461, 'Img_047_L_5.jpg'),
(462, 'Img_047_R_1.jpg'),
(463, 'Img_047_R_2.jpg'),
(464, 'Img_047_R_3.jpg'),
(465, 'Img_047_R_4.jpg'),
(466, 'Img_047_R_5.jpg'),
(467, 'Img_048_L_1.jpg'),
(468, 'Img_048_L_2.jpg'),
(469, 'Img_048_L_3.jpg'),
(470, 'Img_048_L_4.jpg'),
(471, 'Img_048_L_5.jpg'),
(472, 'Img_048_R_1.jpg'),
(473, 'Img_048_R_2.jpg'),
(474, 'Img_048_R_3.jpg'),
(475, 'Img_048_R_4.jpg'),
(476, 'Img_048_R_5.jpg'),
(477, 'Img_049_L_1.jpg'),
(478, 'Img_049_L_2.jpg'),
(479, 'Img_049_L_3.jpg'),
(480, 'Img_049_L_4.jpg'),
(481, 'Img_049_L_5.jpg'),
(482, 'Img_049_R_1.jpg'),
(483, 'Img_049_R_2.jpg'),
(484, 'Img_049_R_3.jpg'),
(485, 'Img_049_R_4.jpg'),
(486, 'Img_049_R_5.jpg'),
(487, 'Img_050_L_1.jpg'),
(488, 'Img_050_L_2.jpg'),
(489, 'Img_050_L_3.jpg'),
(490, 'Img_050_L_4.jpg'),
(491, 'Img_050_L_5.jpg'),
(492, 'Img_050_R_1.jpg'),
(493, 'Img_050_R_2.jpg'),
(494, 'Img_050_R_3.jpg'),
(495, 'Img_050_R_4.jpg'),
(496, 'Img_050_R_5.jpg'),
(497, 'Img_051_L_1.jpg'),
(498, 'Img_051_L_2.jpg'),
(499, 'Img_051_L_3.jpg'),
(500, 'Img_051_L_4.jpg'),
(501, 'Img_051_L_5.jpg'),
(502, 'Img_051_R_1.jpg'),
(503, 'Img_051_R_3.jpg'),
(504, 'Img_051_R_4.jpg'),
(505, 'Img_051_R_5.jpg'),
(506, 'Img_052_L_1.jpg'),
(507, 'Img_052_L_2.jpg'),
(508, 'Img_052_L_3.jpg'),
(509, 'Img_052_L_4.jpg'),
(510, 'Img_052_L_5.jpg'),
(511, 'Img_052_R_1.jpg'),
(512, 'Img_052_R_3.jpg'),
(513, 'Img_052_R_4.jpg'),
(514, 'Img_052_R_5.jpg'),
(515, 'Img_053_L_1.jpg'),
(516, 'Img_053_L_2.jpg'),
(517, 'Img_053_L_3.jpg'),
(518, 'Img_053_L_4.jpg'),
(519, 'Img_053_L_5.jpg'),
(520, 'Img_053_R_1.jpg'),
(521, 'Img_053_R_2.jpg'),
(522, 'Img_053_R_3.jpg'),
(523, 'Img_053_R_4.jpg'),
(524, 'Img_053_R_5.jpg'),
(525, 'Img_054_L_1.jpg'),
(526, 'Img_054_L_2.jpg'),
(527, 'Img_054_L_3.jpg'),
(528, 'Img_054_L_4.jpg'),
(529, 'Img_054_L_5.jpg'),
(530, 'Img_054_R_1.jpg'),
(531, 'Img_054_R_2.jpg'),
(532, 'Img_054_R_3.jpg'),
(533, 'Img_054_R_4.jpg'),
(534, 'Img_054_R_5.jpg'),
(535, 'Img_055_L_1.jpg'),
(536, 'Img_055_L_2.jpg'),
(537, 'Img_055_L_3.jpg'),
(538, 'Img_055_L_4.jpg'),
(539, 'Img_055_L_5.jpg'),
(540, 'Img_055_R_1.jpg'),
(541, 'Img_055_R_2.jpg'),
(542, 'Img_055_R_3.jpg'),
(543, 'Img_055_R_4.jpg'),
(544, 'Img_055_R_5.jpg'),
(545, 'Img_056_L_1.jpg'),
(546, 'Img_056_L_2.jpg'),
(547, 'Img_056_L_3.jpg'),
(548, 'Img_056_L_4.jpg'),
(549, 'Img_056_L_5.jpg'),
(550, 'Img_056_R_1.jpg'),
(551, 'Img_056_R_2.jpg'),
(552, 'Img_056_R_3.jpg'),
(553, 'Img_056_R_4.jpg'),
(554, 'Img_056_R_5.jpg'),
(555, 'Img_057_L_1.jpg'),
(556, 'Img_057_L_2.jpg'),
(557, 'Img_057_L_3.jpg'),
(558, 'Img_057_L_4.jpg'),
(559, 'Img_057_L_5.jpg'),
(560, 'Img_057_R_1.jpg'),
(561, 'Img_057_R_2.jpg'),
(562, 'Img_057_R_3.jpg'),
(563, 'Img_057_R_4.jpg'),
(564, 'Img_057_R_5.jpg'),
(565, 'Img_058_L_1.jpg'),
(566, 'Img_058_L_2.jpg'),
(567, 'Img_058_L_3.jpg'),
(568, 'Img_058_L_4.jpg'),
(569, 'Img_058_L_5.jpg'),
(570, 'Img_058_R_1.jpg'),
(571, 'Img_058_R_2.jpg'),
(572, 'Img_058_R_3.jpg'),
(573, 'Img_058_R_4.jpg'),
(574, 'Img_058_R_5.jpg'),
(575, 'Img_059_L_1.jpg'),
(576, 'Img_059_L_2.jpg'),
(577, 'Img_059_L_3.jpg'),
(578, 'Img_059_L_4.jpg'),
(579, 'Img_059_L_5.jpg'),
(580, 'Img_059_R_1.jpg'),
(581, 'Img_059_R_2.jpg'),
(582, 'Img_059_R_3.jpg'),
(583, 'Img_059_R_4.jpg'),
(584, 'Img_059_R_5.jpg'),
(585, 'Img_060_L_1.jpg'),
(586, 'Img_060_L_2.jpg'),
(587, 'Img_060_L_3.jpg'),
(588, 'Img_060_L_4.jpg'),
(589, 'Img_060_L_5.jpg'),
(590, 'Img_060_R_1.jpg'),
(591, 'Img_060_R_2.jpg'),
(592, 'Img_060_R_3.jpg'),
(593, 'Img_060_R_4.jpg'),
(594, 'Img_060_R_5.jpg'),
(595, 'Img_061_L_1.jpg'),
(596, 'Img_061_L_10.jpg'),
(597, 'Img_061_L_2.jpg'),
(598, 'Img_061_L_3.jpg'),
(599, 'Img_061_L_4.jpg'),
(600, 'Img_061_L_5.jpg'),
(601, 'Img_061_L_6.jpg'),
(602, 'Img_061_L_7.jpg'),
(603, 'Img_061_L_8.jpg'),
(604, 'Img_061_L_9.jpg'),
(605, 'Img_061_R_1.jpg'),
(606, 'Img_061_R_10.jpg'),
(607, 'Img_061_R_2.jpg'),
(608, 'Img_061_R_3.jpg'),
(609, 'Img_061_R_4.jpg'),
(610, 'Img_061_R_5.jpg'),
(611, 'Img_061_R_6.jpg'),
(612, 'Img_061_R_7.jpg'),
(613, 'Img_061_R_8.jpg'),
(614, 'Img_061_R_9.jpg'),
(615, 'Img_062_L_1.jpg'),
(616, 'Img_062_L_2.jpg'),
(617, 'Img_062_L_3.jpg'),
(618, 'Img_062_L_4.jpg'),
(619, 'Img_062_L_5.jpg'),
(620, 'Img_062_R_1.jpg'),
(621, 'Img_062_R_2.jpg'),
(622, 'Img_062_R_3.jpg'),
(623, 'Img_062_R_4.jpg'),
(624, 'Img_062_R_5.jpg'),
(625, 'Img_063_L_1.jpg'),
(626, 'Img_063_L_2.jpg'),
(627, 'Img_063_L_3.jpg'),
(628, 'Img_063_L_4.jpg'),
(629, 'Img_063_L_5.jpg'),
(630, 'Img_063_R_1.jpg'),
(631, 'Img_063_R_2.jpg'),
(632, 'Img_063_R_3.jpg'),
(633, 'Img_063_R_4.jpg'),
(634, 'Img_063_R_5.jpg'),
(635, 'Img_064_L_1.jpg'),
(636, 'Img_064_L_2.jpg'),
(637, 'Img_064_L_3.jpg'),
(638, 'Img_064_L_4.jpg'),
(639, 'Img_064_L_5.jpg'),
(640, 'Img_064_R_1.jpg'),
(641, 'Img_064_R_2.jpg'),
(642, 'Img_064_R_3.jpg'),
(643, 'Img_064_R_4.jpg'),
(644, 'Img_064_R_5.jpg'),
(645, 'Img_065_L_1.jpg'),
(646, 'Img_065_L_2.jpg'),
(647, 'Img_065_L_3.jpg'),
(648, 'Img_065_L_4.jpg'),
(649, 'Img_065_L_5.jpg'),
(650, 'Img_065_R_1.jpg'),
(651, 'Img_065_R_2.jpg'),
(652, 'Img_065_R_3.jpg'),
(653, 'Img_065_R_4.jpg'),
(654, 'Img_065_R_5.jpg'),
(655, 'Img_066_L_1.jpg'),
(656, 'Img_066_L_2.jpg'),
(657, 'Img_066_L_3.jpg'),
(658, 'Img_066_L_4.jpg'),
(659, 'Img_066_L_5.jpg'),
(660, 'Img_066_R_1.jpg'),
(661, 'Img_066_R_2.jpg'),
(662, 'Img_066_R_3.jpg'),
(663, 'Img_066_R_4.jpg'),
(664, 'Img_066_R_5.jpg'),
(665, 'Img_067_L_1.jpg'),
(666, 'Img_067_L_2.jpg'),
(667, 'Img_067_L_3.jpg'),
(668, 'Img_067_L_4.jpg'),
(669, 'Img_067_L_5.jpg'),
(670, 'Img_067_R_1.jpg'),
(671, 'Img_067_R_2.jpg'),
(672, 'Img_067_R_3.jpg'),
(673, 'Img_067_R_4.jpg'),
(674, 'Img_067_R_5.jpg'),
(675, 'Img_068_L_1.jpg'),
(676, 'Img_068_L_2.jpg'),
(677, 'Img_068_L_3.jpg'),
(678, 'Img_068_L_4.jpg'),
(679, 'Img_068_L_5.jpg'),
(680, 'Img_068_R_1.jpg'),
(681, 'Img_068_R_2.jpg'),
(682, 'Img_068_R_3.jpg'),
(683, 'Img_068_R_4.jpg'),
(684, 'Img_068_R_5.jpg'),
(685, 'Img_069_L_1.jpg'),
(686, 'Img_069_L_2.jpg'),
(687, 'Img_069_L_3.jpg'),
(688, 'Img_069_L_4.jpg'),
(689, 'Img_069_L_5.jpg'),
(690, 'Img_069_R_1.jpg'),
(691, 'Img_069_R_2.jpg'),
(692, 'Img_069_R_3.jpg'),
(693, 'Img_069_R_4.jpg'),
(694, 'Img_069_R_5.jpg'),
(695, 'Img_070_L_1.jpg'),
(696, 'Img_070_L_2.jpg'),
(697, 'Img_070_L_3.jpg'),
(698, 'Img_070_L_4.jpg'),
(699, 'Img_070_L_5.jpg'),
(700, 'Img_070_R_1.jpg'),
(701, 'Img_070_R_2.jpg'),
(702, 'Img_070_R_3.jpg'),
(703, 'Img_070_R_4.jpg'),
(704, 'Img_070_R_5.jpg'),
(705, 'Img_071_L_1.jpg'),
(706, 'Img_071_L_2.jpg'),
(707, 'Img_071_L_4.jpg'),
(708, 'Img_071_L_5.jpg'),
(709, 'Img_071_R_1.jpg'),
(710, 'Img_071_R_3.jpg'),
(711, 'Img_071_R_4.jpg'),
(712, 'Img_071_R_5.jpg'),
(713, 'Img_072_L_1.jpg'),
(714, 'Img_072_L_2.jpg'),
(715, 'Img_072_L_3.jpg'),
(716, 'Img_072_L_4.jpg'),
(717, 'Img_072_L_5.jpg'),
(718, 'Img_072_R_1.jpg'),
(719, 'Img_072_R_2.jpg'),
(720, 'Img_072_R_3.jpg'),
(721, 'Img_072_R_4.jpg'),
(722, 'Img_072_R_5.jpg'),
(723, 'Img_073_L_1.jpg'),
(724, 'Img_073_L_2.jpg'),
(725, 'Img_073_L_3.jpg'),
(726, 'Img_073_L_4.jpg'),
(727, 'Img_073_L_5.jpg'),
(728, 'Img_073_R_1.jpg'),
(729, 'Img_073_R_2.jpg'),
(730, 'Img_073_R_3.jpg'),
(731, 'Img_073_R_4.jpg'),
(732, 'Img_073_R_5.jpg'),
(733, 'Img_074_L_1.jpg'),
(734, 'Img_074_L_2.jpg'),
(735, 'Img_074_L_3.jpg'),
(736, 'Img_074_L_4.jpg'),
(737, 'Img_074_L_5.jpg'),
(738, 'Img_074_R_1.jpg'),
(739, 'Img_074_R_2.jpg'),
(740, 'Img_074_R_3.jpg'),
(741, 'Img_074_R_4.jpg'),
(742, 'Img_074_R_5.jpg'),
(743, 'Img_075_L_1.jpg'),
(744, 'Img_075_L_2.jpg'),
(745, 'Img_075_L_3.jpg'),
(746, 'Img_075_L_4.jpg'),
(747, 'Img_075_L_5.jpg'),
(748, 'Img_075_L_6.jpg'),
(749, 'Img_075_R_1.jpg'),
(750, 'Img_075_R_2.jpg'),
(751, 'Img_075_R_3.jpg'),
(752, 'Img_075_R_4.jpg'),
(753, 'Img_076_L_1.jpg'),
(754, 'Img_076_L_2.jpg'),
(755, 'Img_076_L_3.jpg'),
(756, 'Img_076_L_4.jpg'),
(757, 'Img_076_L_5.jpg'),
(758, 'Img_076_R_1.jpg'),
(759, 'Img_076_R_2.jpg'),
(760, 'Img_076_R_3.jpg'),
(761, 'Img_076_R_4.jpg'),
(762, 'Img_076_R_5.jpg'),
(763, 'Img_077_L_1.jpg'),
(764, 'Img_077_L_2.jpg'),
(765, 'Img_077_L_3.jpg'),
(766, 'Img_077_L_4.jpg'),
(767, 'Img_077_L_5.jpg'),
(768, 'Img_077_R_1.jpg'),
(769, 'Img_077_R_2.jpg'),
(770, 'Img_077_R_3.jpg'),
(771, 'Img_077_R_4.jpg'),
(772, 'Img_077_R_5.jpg'),
(773, 'Img_078_L_1.jpg'),
(774, 'Img_078_L_2.jpg'),
(775, 'Img_078_L_3.jpg'),
(776, 'Img_078_L_4.jpg'),
(777, 'Img_078_L_5.jpg'),
(778, 'Img_078_R_1.jpg'),
(779, 'Img_078_R_2.jpg'),
(780, 'Img_078_R_3.jpg'),
(781, 'Img_078_R_4.jpg'),
(782, 'Img_078_R_5.jpg'),
(783, 'Img_079_L_1.jpg'),
(784, 'Img_079_L_2.jpg'),
(785, 'Img_079_L_3.jpg'),
(786, 'Img_079_L_4.jpg'),
(787, 'Img_079_L_5.jpg'),
(788, 'Img_079_R_1.jpg'),
(789, 'Img_079_R_2.jpg'),
(790, 'Img_079_R_3.jpg'),
(791, 'Img_079_R_4.jpg'),
(792, 'Img_079_R_5.jpg');

-- --------------------------------------------------------

--
-- Stand-in structure for view `images_count`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `images_count`;
CREATE TABLE `images_count` (
`id` int(11)
,`filename` varchar(255)
,`n_clicks` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `images_with_clicks`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `images_with_clicks`;
CREATE TABLE `images_with_clicks` (
`id` int(11)
,`filename` varchar(255)
,`id_photo` int(11)
,`n_clicks` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `leaderboard`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `leaderboard`;
CREATE TABLE `leaderboard` (
`N` bigint(21)
,`username` text
,`status` text
,`id_player` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `salt` text NOT NULL,
  `status` text NOT NULL,
  `email` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `total_clicks`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `total_clicks`;
CREATE TABLE `total_clicks` (
`total` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `salt` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`) VALUES
(2, 'david', 'PYzvdbqoxGDsvEx2rhaVDSpPns9fUio6PrQFPW5fzcSDc3NlOWPczt/chahfpUY8qFeyyyNnetT/YtGDfJLC/Q==', 'rM9UPyYPaanb8K0IgMzOYVy7G5kcFQmpGowTQicz94hbCgisJdK6gWML4uSMSJ7C'),
(3, 'matti', 'H4Xr89921iobWgglvdpsN2mVSezegBLEQKfiQYDJdRGOKt+c7Yd5cADqvaaFPfpP5+ug7hJm6aUp2y+vid7lRQ==', 'UD16ojtcezMk4YC7lkdjtoV7zwmw7JsAFI+op3duC8A9yLW97KvxMF46PlKC5BHp'),
(4, 'annotator', 'mT6pCl/L9177Rbiw2n7kPCiwHJyFhHRWI4ZAF8gGbj6O8Im0+kDalVIRETJgblPU1ntnSbB6Y5FcvNctSUU48g==', 'ENCZvF9uWmorBAc1c7It7D2sg0keKUPtUpKSW+7jyQATFJjBXosr54s6+Qjc3/vJ');

-- --------------------------------------------------------

--
-- Stand-in structure for view `valid_clicks`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `valid_clicks`;
CREATE TABLE `valid_clicks` (
`id` int(11)
,`id_photo` int(11)
,`x` int(11)
,`y` int(11)
,`id_player` int(11)
);

-- --------------------------------------------------------

--
-- Structure for view `clicks_per_photo`
--
DROP TABLE IF EXISTS `clicks_per_photo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `clicks_per_photo`  AS  select `clicks`.`id_photo` AS `id_photo`,count(`clicks`.`id_photo`) AS `n_clicks` from `valid_clicks` `clicks` group by `clicks`.`id_photo` ;

-- --------------------------------------------------------

--
-- Structure for view `clicks_per_player`
--
DROP TABLE IF EXISTS `clicks_per_player`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `clicks_per_player`  AS  select `c`.`id` AS `id`,`p`.`username` AS `username`,`p`.`status` AS `status`,`p`.`id` AS `id_player` from (`players` `p` left join `clicks` `c` on((`c`.`id_player` = `p`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `images_count`
--
DROP TABLE IF EXISTS `images_count`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `images_count`  AS  select `images_with_clicks`.`id` AS `id`,`images_with_clicks`.`filename` AS `filename`,coalesce(`images_with_clicks`.`n_clicks`,0) AS `n_clicks` from `images_with_clicks` ;

-- --------------------------------------------------------

--
-- Structure for view `images_with_clicks`
--
DROP TABLE IF EXISTS `images_with_clicks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `images_with_clicks`  AS  select `images`.`id` AS `id`,`images`.`filename` AS `filename`,`c`.`id_photo` AS `id_photo`,`c`.`n_clicks` AS `n_clicks` from (`images` left join `clicks_per_photo` `c` on((`images`.`id` = `c`.`id_photo`))) ;

-- --------------------------------------------------------

--
-- Structure for view `leaderboard`
--
DROP TABLE IF EXISTS `leaderboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `leaderboard`  AS  select count(`clicks_per_player`.`id`) AS `N`,`clicks_per_player`.`username` AS `username`,`clicks_per_player`.`status` AS `status`,`clicks_per_player`.`id_player` AS `id_player` from `clicks_per_player` group by `clicks_per_player`.`id_player` order by count(`clicks_per_player`.`id`) desc ;

-- --------------------------------------------------------

--
-- Structure for view `total_clicks`
--
DROP TABLE IF EXISTS `total_clicks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `total_clicks`  AS  select count(0) AS `total` from `valid_clicks` ;

-- --------------------------------------------------------

--
-- Structure for view `valid_clicks`
--
DROP TABLE IF EXISTS `valid_clicks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`annotator`@`%` SQL SECURITY DEFINER VIEW `valid_clicks`  AS  select `clicks`.`id` AS `id`,`clicks`.`id_photo` AS `id_photo`,`clicks`.`x` AS `x`,`clicks`.`y` AS `y`,`clicks`.`id_player` AS `id_player` from (`clicks` join `players` on((`clicks`.`id_player` = `players`.`id`))) where (`players`.`status` = 'OPE') ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clicks`
--
ALTER TABLE `clicks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_photo` (`id_photo`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clicks`
--
ALTER TABLE `clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=793;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `clicks`
--
ALTER TABLE `clicks`
  ADD CONSTRAINT `click_to_photo` FOREIGN KEY (`id_photo`) REFERENCES `images` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
