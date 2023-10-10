-- phpMyAdmin SQL Dump
-- version 4.9.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 30, 2021 at 05:05 PM
-- Server version: 10.3.21-MariaDB
-- PHP Version: 7.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techutks_gusto`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `alert_id` int(11) NOT NULL,
  `user_anchors` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`alert_id`, `user_anchors`, `type`, `message`, `date_created`, `status`) VALUES
(1, '2,1', 'General', 'The thing happened', '2020-03-22T11:18:53-10:00', 0),
(2, '2,1,6', 'Warning', 'The thing is almost due.', '2020-04-22T11:18:53-10:00', 0),
(3, '2', 'Error', 'The thing is past due!', '2020-04-21T11:18:53-10:00', 0),
(4, '1', 'Error', 'The thing is super past due!', '2019-04-21T11:18:53-10:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE `calendar` (
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `calendar`
--

INSERT INTO `calendar` (`event_id`, `title`, `description`, `start_time`, `end_time`, `color`) VALUES
(44, 'Meyer Cabling', 'Running cables and that one gaylords place', '2020-10-16T10:00:00-10:00', '2020-10-16T10:00:00-10:00', '#87b2e9'),
(43, 'Wake up early', 'Doing this for no reason', '2020-10-09T06:00:00-10:00', '2020-10-09T06:00:00-10:00', '#b9dae2'),
(40, 'Save the trees', 'Plant weed in the community garden breh', '2020-09-29T10:00:00-10:00', '2020-10-04T14:00:00-10:00', '#89cf97'),
(41, 'Do milk', 'Open cloth show vagana', '2020-10-08T14:00:00-10:00', '2020-10-09T17:00:00-10:00', '#ffe279'),
(42, 'Exercise', 'Get fit for the litty titty', '2020-10-09T08:00:00-10:00', '2020-10-12T10:05:00-10:00', '#ec8181'),
(45, 'Doin Stuff', 'Got stuff to do', '2020-10-09T10:00:00-10:00', '2020-10-09T15:00:00-10:00', '#ffc0cb'),
(46, 'Abandon Family', 'Go out for cigarettes and never return', '2020-10-09T10:00:00-10:00', '2020-10-09T17:15:00-10:00', '#89cf97'),
(47, 'Kill the chiropractor', 'gonna shove my hand up his ass and rip out his goddamn spine', '2020-10-09T10:00:00-10:00', '2020-10-09T14:00:00-10:00', '#87b2e9'),
(48, 'Buy Robitussin', 'Broke my leg need mo tussin', '2020-10-09T10:00:00-10:00', '2020-10-09T10:30:00-10:00', '#b9dae2'),
(49, 'Another one', 'Assassinate dj khaled', '2020-10-09T10:00:00-10:00', '2020-10-09T23:00:00-10:00', '#db9adb'),
(50, 'Batman', 'Im batman', '2020-10-09T10:00:00-10:00', '2020-10-09T16:00:00-10:00', '#db9adb');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `countries_id` int(11) NOT NULL,
  `code` varchar(2) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`countries_id`, `code`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AX', 'Aland Islands'),
(3, 'AL', 'Albania'),
(4, 'DZ', 'Algeria'),
(5, 'AS', 'American Samoa'),
(6, 'AD', 'Andorra'),
(7, 'AO', 'Angola'),
(8, 'AI', 'Anguilla'),
(9, 'AQ', 'Antarctica'),
(10, 'AG', 'Antigua And Barbuda'),
(11, 'AR', 'Argentina'),
(12, 'AM', 'Armenia'),
(13, 'AW', 'Aruba'),
(14, 'AU', 'Australia'),
(15, 'AT', 'Austria'),
(16, 'AZ', 'Azerbaijan'),
(17, 'BS', 'Bahamas'),
(18, 'BH', 'Bahrain'),
(19, 'BD', 'Bangladesh'),
(20, 'BB', 'Barbados'),
(21, 'BY', 'Belarus'),
(22, 'BE', 'Belgium'),
(23, 'BZ', 'Belize'),
(24, 'BJ', 'Benin'),
(25, 'BM', 'Bermuda'),
(26, 'BT', 'Bhutan'),
(27, 'BO', 'Bolivia'),
(28, 'BA', 'Bosnia And Herzegovina'),
(29, 'BW', 'Botswana'),
(30, 'BV', 'Bouvet Island'),
(31, 'BR', 'Brazil'),
(32, 'IO', 'British Indian Ocean Territory'),
(33, 'BN', 'Brunei Darussalam'),
(34, 'BG', 'Bulgaria'),
(35, 'BF', 'Burkina Faso'),
(36, 'BI', 'Burundi'),
(37, 'KH', 'Cambodia'),
(38, 'CM', 'Cameroon'),
(39, 'CA', 'Canada'),
(40, 'CV', 'Cape Verde'),
(41, 'KY', 'Cayman Islands'),
(42, 'CF', 'Central African Republic'),
(43, 'TD', 'Chad'),
(44, 'CL', 'Chile'),
(45, 'CN', 'China'),
(46, 'CX', 'Christmas Island'),
(47, 'CC', 'Cocos (Keeling) Islands'),
(48, 'CO', 'Colombia'),
(49, 'KM', 'Comoros'),
(50, 'CG', 'Congo'),
(51, 'CD', 'Congo, Democratic Republic'),
(52, 'CK', 'Cook Islands'),
(53, 'CR', 'Costa Rica'),
(54, 'CI', 'Cote D\'Ivoire'),
(55, 'HR', 'Croatia'),
(56, 'CU', 'Cuba'),
(57, 'CY', 'Cyprus'),
(58, 'CZ', 'Czech Republic'),
(59, 'DK', 'Denmark'),
(60, 'DJ', 'Djibouti'),
(61, 'DM', 'Dominica'),
(62, 'DO', 'Dominican Republic'),
(63, 'EC', 'Ecuador'),
(64, 'EG', 'Egypt'),
(65, 'SV', 'El Salvador'),
(66, 'GQ', 'Equatorial Guinea'),
(67, 'ER', 'Eritrea'),
(68, 'EE', 'Estonia'),
(69, 'ET', 'Ethiopia'),
(70, 'FK', 'Falkland Islands (Malvinas)'),
(71, 'FO', 'Faroe Islands'),
(72, 'FJ', 'Fiji'),
(73, 'FI', 'Finland'),
(74, 'FR', 'France'),
(75, 'GF', 'French Guiana'),
(76, 'PF', 'French Polynesia'),
(77, 'TF', 'French Southern Territories'),
(78, 'GA', 'Gabon'),
(79, 'GM', 'Gambia'),
(80, 'GE', 'Georgia'),
(81, 'DE', 'Germany'),
(82, 'GH', 'Ghana'),
(83, 'GI', 'Gibraltar'),
(84, 'GR', 'Greece'),
(85, 'GL', 'Greenland'),
(86, 'GD', 'Grenada'),
(87, 'GP', 'Guadeloupe'),
(88, 'GU', 'Guam'),
(89, 'GT', 'Guatemala'),
(90, 'GG', 'Guernsey'),
(91, 'GN', 'Guinea'),
(92, 'GW', 'Guinea-Bissau'),
(93, 'GY', 'Guyana'),
(94, 'HT', 'Haiti'),
(95, 'HM', 'Heard Island & Mcdonald Islands'),
(96, 'VA', 'Holy See (Vatican City State)'),
(97, 'HN', 'Honduras'),
(98, 'HK', 'Hong Kong'),
(99, 'HU', 'Hungary'),
(100, 'IS', 'Iceland'),
(101, 'IN', 'India'),
(102, 'ID', 'Indonesia'),
(103, 'IR', 'Iran, Islamic Republic Of'),
(104, 'IQ', 'Iraq'),
(105, 'IE', 'Ireland'),
(106, 'IM', 'Isle Of Man'),
(107, 'IL', 'Israel'),
(108, 'IT', 'Italy'),
(109, 'JM', 'Jamaica'),
(110, 'JP', 'Japan'),
(111, 'JE', 'Jersey'),
(112, 'JO', 'Jordan'),
(113, 'KZ', 'Kazakhstan'),
(114, 'KE', 'Kenya'),
(115, 'KI', 'Kiribati'),
(116, 'KR', 'Korea'),
(117, 'KW', 'Kuwait'),
(118, 'KG', 'Kyrgyzstan'),
(119, 'LA', 'Lao People\'s Democratic Republic'),
(120, 'LV', 'Latvia'),
(121, 'LB', 'Lebanon'),
(122, 'LS', 'Lesotho'),
(123, 'LR', 'Liberia'),
(124, 'LY', 'Libyan Arab Jamahiriya'),
(125, 'LI', 'Liechtenstein'),
(126, 'LT', 'Lithuania'),
(127, 'LU', 'Luxembourg'),
(128, 'MO', 'Macao'),
(129, 'MK', 'Macedonia'),
(130, 'MG', 'Madagascar'),
(131, 'MW', 'Malawi'),
(132, 'MY', 'Malaysia'),
(133, 'MV', 'Maldives'),
(134, 'ML', 'Mali'),
(135, 'MT', 'Malta'),
(136, 'MH', 'Marshall Islands'),
(137, 'MQ', 'Martinique'),
(138, 'MR', 'Mauritania'),
(139, 'MU', 'Mauritius'),
(140, 'YT', 'Mayotte'),
(141, 'MX', 'Mexico'),
(142, 'FM', 'Micronesia, Federated States Of'),
(143, 'MD', 'Moldova'),
(144, 'MC', 'Monaco'),
(145, 'MN', 'Mongolia'),
(146, 'ME', 'Montenegro'),
(147, 'MS', 'Montserrat'),
(148, 'MA', 'Morocco'),
(149, 'MZ', 'Mozambique'),
(150, 'MM', 'Myanmar'),
(151, 'NA', 'Namibia'),
(152, 'NR', 'Nauru'),
(153, 'NP', 'Nepal'),
(154, 'NL', 'Netherlands'),
(155, 'AN', 'Netherlands Antilles'),
(156, 'NC', 'New Caledonia'),
(157, 'NZ', 'New Zealand'),
(158, 'NI', 'Nicaragua'),
(159, 'NE', 'Niger'),
(160, 'NG', 'Nigeria'),
(161, 'NU', 'Niue'),
(162, 'NF', 'Norfolk Island'),
(163, 'MP', 'Northern Mariana Islands'),
(164, 'NO', 'Norway'),
(165, 'OM', 'Oman'),
(166, 'PK', 'Pakistan'),
(167, 'PW', 'Palau'),
(168, 'PS', 'Palestinian Territory, Occupied'),
(169, 'PA', 'Panama'),
(170, 'PG', 'Papua New Guinea'),
(171, 'PY', 'Paraguay'),
(172, 'PE', 'Peru'),
(173, 'PH', 'Philippines'),
(174, 'PN', 'Pitcairn'),
(175, 'PL', 'Poland'),
(176, 'PT', 'Portugal'),
(177, 'PR', 'Puerto Rico'),
(178, 'QA', 'Qatar'),
(179, 'RE', 'Reunion'),
(180, 'RO', 'Romania'),
(181, 'RU', 'Russian Federation'),
(182, 'RW', 'Rwanda'),
(183, 'BL', 'Saint Barthelemy'),
(184, 'SH', 'Saint Helena'),
(185, 'KN', 'Saint Kitts And Nevis'),
(186, 'LC', 'Saint Lucia'),
(187, 'MF', 'Saint Martin'),
(188, 'PM', 'Saint Pierre And Miquelon'),
(189, 'VC', 'Saint Vincent And Grenadines'),
(190, 'WS', 'Samoa'),
(191, 'SM', 'San Marino'),
(192, 'ST', 'Sao Tome And Principe'),
(193, 'SA', 'Saudi Arabia'),
(194, 'SN', 'Senegal'),
(195, 'RS', 'Serbia'),
(196, 'SC', 'Seychelles'),
(197, 'SL', 'Sierra Leone'),
(198, 'SG', 'Singapore'),
(199, 'SK', 'Slovakia'),
(200, 'SI', 'Slovenia'),
(201, 'SB', 'Solomon Islands'),
(202, 'SO', 'Somalia'),
(203, 'ZA', 'South Africa'),
(204, 'GS', 'South Georgia And Sandwich Isl.'),
(205, 'ES', 'Spain'),
(206, 'LK', 'Sri Lanka'),
(207, 'SD', 'Sudan'),
(208, 'SR', 'Suriname'),
(209, 'SJ', 'Svalbard And Jan Mayen'),
(210, 'SZ', 'Swaziland'),
(211, 'SE', 'Sweden'),
(212, 'CH', 'Switzerland'),
(213, 'SY', 'Syrian Arab Republic'),
(214, 'TW', 'Taiwan'),
(215, 'TJ', 'Tajikistan'),
(216, 'TZ', 'Tanzania'),
(217, 'TH', 'Thailand'),
(218, 'TL', 'Timor-Leste'),
(219, 'TG', 'Togo'),
(220, 'TK', 'Tokelau'),
(221, 'TO', 'Tonga'),
(222, 'TT', 'Trinidad And Tobago'),
(223, 'TN', 'Tunisia'),
(224, 'TR', 'Turkey'),
(225, 'TM', 'Turkmenistan'),
(226, 'TC', 'Turks And Caicos Islands'),
(227, 'TV', 'Tuvalu'),
(228, 'UG', 'Uganda'),
(229, 'UA', 'Ukraine'),
(230, 'AE', 'United Arab Emirates'),
(231, 'GB', 'United Kingdom'),
(232, 'US', 'United States'),
(233, 'UM', 'United States Outlying Islands'),
(234, 'UY', 'Uruguay'),
(235, 'UZ', 'Uzbekistan'),
(236, 'VU', 'Vanuatu'),
(237, 'VE', 'Venezuela'),
(238, 'VN', 'Viet Nam'),
(239, 'VG', 'Virgin Islands, British'),
(240, 'VI', 'Virgin Islands, U.S.'),
(241, 'WF', 'Wallis And Futuna'),
(242, 'EH', 'Western Sahara'),
(243, 'YE', 'Yemen'),
(244, 'ZM', 'Zambia'),
(245, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE `errors` (
  `error_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `event` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `language_id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`language_id`, `code`, `language`) VALUES
(1, 'en', 'English'),
(2, 'ja', 'Japanese');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_id` int(11) NOT NULL,
  `time` varchar(255) DEFAULT NULL,
  `event` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`log_id`, `time`, `event`) VALUES
(1, '2021-06-04 12:08:40', 'User <b><i>Bad Syntax</i></b> logged in.'),
(2, '2021-06-04 01:22:21', 'User <b><i>Bad Syntax</i></b> logged out.'),
(3, '2021-06-09 02:03:06', 'User <b><i>Bad Syntax</i></b> logged in.'),
(4, '2021-06-09 02:25:19', 'User <b><i>Bad Syntax</i></b> logged in.'),
(5, '2021-06-09 02:26:26', 'User <b><i>Bad Syntax</i></b> logged out.'),
(6, '2021-06-29 07:48:26', 'User <b><i>Bad Syntax</i></b> logged in.'),
(7, '2021-06-29 08:50:41', 'User <b><i>Bad Syntax</i></b> logged in.');

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `login_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `lock_time` varchar(255) NOT NULL,
  `attempts` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `mail_id` int(11) NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `port` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mail`
--

INSERT INTO `mail` (`mail_id`, `host`, `port`, `username`, `password`) VALUES
(1, 'smtp.office365.com', '587', 'chase@yourtechconnection.com', 'p@ssw0rd');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL,
  `menu_anchor` int(11) NOT NULL,
  `main_menu` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu_anchor`, `main_menu`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 0),
(4, 4, 0),
(5, 5, 0),
(6, 6, 0),
(7, 7, 0),
(8, 8, 0),
(9, 9, 0),
(10, 10, 0),
(11, 11, 0),
(12, 12, 0),
(13, 13, 0),
(14, 14, 0),
(15, 15, 0),
(16, 16, 0),
(17, 17, 0),
(18, 18, 0),
(19, 19, 0),
(20, 20, 0),
(21, 21, 0),
(22, 22, 0),
(23, 23, 0),
(24, 24, 0),
(25, 25, 0),
(26, 26, 0),
(27, 27, 0),
(28, 28, 0),
(29, 29, 0),
(30, 30, 0),
(31, 31, 0),
(32, 32, 0),
(33, 33, 0),
(34, 34, 0),
(35, 35, 0),
(36, 36, 0),
(37, 37, 0),
(38, 38, 0),
(39, 39, 0),
(40, 40, 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `name`, `value`) VALUES
(1, 'owners_email', 'chase@techsourcehawaii.com'),
(2, 'strong_pw', NULL),
(3, 'inactivity_limit', '0'),
(4, 'language', 'en'),
(5, 'maintenance_mode', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `group` int(11) NOT NULL DEFAULT 0,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `privacy` tinyint(1) NOT NULL DEFAULT 0,
  `avatar` varchar(255) NOT NULL DEFAULT 'default_avatar.png',
  `bio` text DEFAULT NULL,
  `signup_date` varchar(255) NOT NULL,
  `last_active` varchar(255) DEFAULT NULL,
  `ip` varchar(255) NOT NULL,
  `verify_logout` tinyint(1) NOT NULL DEFAULT 1,
  `theme` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `key`, `group`, `firstname`, `lastname`, `username`, `email`, `phone`, `password`, `birthday`, `country`, `gender`, `privacy`, `avatar`, `bio`, `signup_date`, `last_active`, `ip`, `verify_logout`, `theme`) VALUES
(1, '18e19b6d19a6b7807ddf5f6ba40f80b8', 4, 'Bad', 'Syntax', 'badsyntax', 'badsyntaxx@gmail.com', '808 738', '$2y$12$dR8SfOBAyoduEzCevs0rQuVhxAjcTZxTvAjKyLMnRsROh9SsCqi1K', NULL, 'Select', NULL, 0, 'default_avatar.png', '', '2017-11-05 21:20:00', '2021-06-29T20:51:05-10:00', '::1', 1, 0),
(2, 'b4888d79272d7107ac316814eb28790a', 4, 'Chase', 'Asahina', 'chaser', 'chase@techsourcehawaii.com', '808', '$2y$12$Av2SZ2r8qCNN5SaQCAJxmOt2PqXJOsPF/sh.NQfJjodOXWbUCbTOu', '1987-07-16T00:00:00-10:00', 'US, United States', 'Male', 0, 'default_avatar.png', 'I am the cool', '2016-11-09 21:34:23', '2020-09-25T11:40:17-10:00', '::1', 1, 0),
(3, '8a998aaf477faa5bd7aae497a2d0de55', 0, 'Fing', 'Fan Foom', 'fing', 'fing@fing.com', NULL, '$2y$12$5JLdLKl5qiG5IIH1zTrI4.qcG/xLaXV7Qvl6gUu/ptIUrKA/xtXYm', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:45:38', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(4, '3bbd4490baaf02a12e91cf6f3c808eb0', 0, 'fang', 'Fan Foom', 'fang', 'fang@fang.com', NULL, '$2y$12$W1YqmQ1wdMqOi2f9hpocQeer7eKVYeOY1M2DjLVy3CkJSn4oRF.ju', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:46:08', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(5, 'fc48b7028493e90e0de9b021ccd77d4c', 0, 'foom', 'zoom', 'foom', 'foom@foom.com', NULL, '$2y$12$qDvjUgjcrYtpk838Hmih.OwdUDUw0crvqHj2EJWevZPBbBjFxvZDq', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:46:35', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(6, '2980d95d75a680d22497bec94ed5e147', 4, 'Tony', 'Stark', 'tony', 'tony@tony.com', NULL, '$2y$12$Up6fKWGku7Oo56DSPVOb1ug4qhtYJx4xsQ1D1YVxBzHL88/73gvrC', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:46:58', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(7, 'ea267af33517933c224901c8e31ef29b', 4, 'Steve', 'Rogers', 'steve', 'steve@steve.com', NULL, '$2y$12$C9.BFDtl9PiL6RHbR7TaPes4gX76tPFgHIqqncc1kZBay/z7FcCUe', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:47:46', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(8, 'd20d32dbdde5ed28e56267911fbac6b2', 4, 'Peter', 'Parker', 'peter', 'peter@peter.com', NULL, '$2y$12$nEdusq/ILQ4Tvt4FP8rfwOpRTRs23.3sqZGkv5rL148Ma3edTteoi', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:48:09', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(9, '9a2443a946b1f85951e65bad4249d21a', 2, 'Natasha', 'Romanov', 'natasha', 'natasha@natasha.com', NULL, '$2y$12$jgZe6sb7wBcfxPfuRtG51.Sec10rj0/lWJK3TIoz73O52uwjv6U9u', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:50:00', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(10, 'c35c8e3cd4ae9252f694ac7b09968e35', 2, 'James', 'Logan', 'logan', 'logan@logan.com', NULL, '$2y$12$GqnHPuBAHkZx3M/A6erHDOGiDrHd54mgaMJntWak5QnG3X3Uy/v2m', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:50:33', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(11, 'fc86b521095049f2bbaa7ab3a50fe769', 3, 'Scott', 'Summers', 'scott', 'scott@scott.com', NULL, '$2y$12$JbX17SGvl.1gtSwlZwqOQuQgsiT59IVpewCl21DhWLCToH6XrLiqm', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:51:09', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(12, '481467f1404185890adc1e4d8d1ab070', 1, 'Vision', 'Ultron', 'vision', 'vision@vision.com', NULL, '$2y$12$GD3PCMoEb6FQzxNGDAt3SeYfmG4EvhqccpX83mZx/g/OQSpJ9cZdi', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:51:47', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(13, 'e76d9b36a1381c2abb09cd6e889e2e1a', 2, 'Bruce', 'Banner', 'bruce', 'bruce@bruce.com', NULL, '$2y$12$qXJ8bCSK/7u/nRkblWY9g.Z0MpN5INdcOOrtGsrWBb9mVPychadGi', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:52:08', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(14, '8f189bce5bda66a6a871b881f12805a3', 2, 'Sam', 'Wilson', 'sam', 'sam@sam.com', NULL, '$2y$12$berMPJZpdPaM0ueXol5SS.2yhi89ACE7xyILgK5EnB3eITQpu81Fi', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:53:10', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(15, 'fd3d060e8e0e6804a41a0b7054d924ea', 2, 'Tchalla', 'Yomamma', 'tchalla', 'tchalla@tchalla.com', NULL, '$2y$12$P/8Zt0PNJCYzBTfJMqLsf.CJNhuY/ffEq4tms2ZhPDdO0/68k0MLC', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:54:09', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(16, 'cbf4ea5577e12b63efef95505a016e2b', 2, 'Thor', 'Bhors', 'thor', 'thor@thor.com', NULL, '$2y$12$KhjI0VfJNwReqOHPcgnjv.htbme6ZY8Ed3ShA4ch6WhXi59S2WKFW', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:54:41', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(17, '9ce2130fbc7b6137832e7c4b49c057fe', 2, 'Pietro', 'Maximoff', 'pietro', 'pietro@pietro.com', NULL, '$2y$12$2EaOuoSMBYrwYcrzvOvBmOFABdkfv2IQvlt3dO3iOZwCHjRPM6w9e', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:56:04', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(18, '7a1362046a6ac3907749da74e4012930', 2, 'Wanda', 'Maximoff', 'wanda', 'wanda@wanda.com', NULL, '$2y$12$7he8tJr5N0H01iePXZZgQ.Prsg3pCi.UMQIaD2dYaO7KlzU.fPSjW', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:56:34', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(19, 'fcd69499e94a39d9396c740f84d2919d', 2, 'Clint', 'Barton', 'clint', 'clint@clint.com', NULL, '$2y$12$vygpC4kWN8yFC4qM94rhlO3Q/zftaYzQ3KBiZTKkhKvNwRpN21.MO', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:58:38', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(20, 'a91bc15e4cbd8dbeb4e487423309ea82', 2, 'Bruce', 'Wayne', 'batman', 'batman@batman.com', NULL, '$2y$12$VC2gj54j2c9IvAO1BSuPWOuxvuuEK5oPhdJiBNU0iz7Lix0FS8JNa', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 21:59:42', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(21, '1f6f92e23f69b849496e321a76bed719', 2, 'Clark', 'Kent', 'superman', 'superman@superman.com', NULL, '$2y$12$jdGO25K0bGyCoGcsud/Q9uAopsa294SfO.VEahSLU2XWEDspuoKHe', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 22:00:05', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(22, 'f24efe944e15c0dd373f68cff1273f45', 2, 'Arthur', 'Curry', 'aquaman', 'aquaman@aquaman.com', NULL, '$2y$12$vfZ6rBvjy.vKQUo9/NM5EuvIRbUoxbsGBNFStjq.G13Oi19pT5Yp2', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 22:00:30', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(23, 'dca91be5b1b2631b678df1c24c5dc694', 2, 'Barry', 'Allen', 'flash', 'flash@flash.com', NULL, '$2y$12$7NIYJG8g.dPFFFc1MKOlduQg9yxSjrwlIoA2R9xHEzfjNU7x7wSqS', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2017-12-14 22:01:01', '2020-04-09T12:20:48-10:00', '::1', 1, 0),
(24, '59f0c1cc2657355d693fc1403b38bb6f', 2, 'Diana', 'Prince', '', 'diana@gusto.com', NULL, '$2y$12$c.0QqbQCnboo51ptYFJ2uOqoiSYmdNX7zwg2TG.lNIjZPvrt7Zmti', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-04-10T20:04:56-10:00', NULL, '::1', 1, 0),
(25, '1d7323e582b45c8bfe76a071314ebffe', 2, 'Aurora', 'Monroe', '', 'aurora@gusto.com', NULL, '$2y$12$U98B0ORgZMMFEOXQSix16usgLDNcc.j0Dd.aSeAkLOCOE2TN2F7Iy', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-04-10T20:08:45-10:00', NULL, '::1', 1, 0),
(26, 'fae7783c15b21571e1a5d86557c113d6', 2, 'Talia', 'Alghul', '', 'talia@gusto.com', NULL, '$2y$12$Ird1s/WPCG9Hq9Bx9HatauFoL3fStBxOQxQX3o4YT3c0wvx/kVHKW', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-04-10T20:09:36-10:00', NULL, '::1', 1, 0),
(27, '85de11f721df6529cdab9dacf391bb40', 2, 'Nissa', 'Alghul', '', 'nissa@gusto.com', NULL, '$2y$12$tSEJLArwNjnULoKa6ik5/O/oLyI9avjIG5k5skB1rm1adFcVJj7y6', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-04-10T20:11:47-10:00', NULL, '::1', 1, 0),
(28, '2d1e7618ea13ab5b8a7c25ec6341b325', 2, 'Ras', 'Alghul', '', 'ras@gusto.com', NULL, '$2y$12$l0/miN4iodlkrd6Jdiq7wumCxgRiRygb9OC8kB0ES.4SS5JSsiPE6', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-04-10T20:50:30-10:00', NULL, '::1', 1, 0),
(38, 'e41b7acd014ac832802218fb105c0845', 1, 'Bil', 'Bo', '', 'bilbo@baggins.com', NULL, '$2y$12$nAe4TTmI/bX8BTqWT4g4P.lolMsw3lbVTGj6M5CqsaZGWWjahUlAG', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-07-07T09:39:49-10:00', NULL, '::1', 1, 0),
(40, '9b351b328fc804e7eda4d75dcf2720d3', 1, 'Frodo', 'Baggins', '', 'frodo@baggins.com', NULL, '$2y$12$pdagVOSS0jHumMzBO5B52uZ08IrEvMe49BPtA5vtiTigkBMNEnyMC', NULL, NULL, NULL, 0, 'default_avatar.png', NULL, '2020-08-04T15:20:53-10:00', NULL, '::1', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`alert_id`);

--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`countries_id`);

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`error_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`language_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD PRIMARY KEY (`mail_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `countries_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
