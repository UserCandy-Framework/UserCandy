-- phpMyAdmin SQL Dump
-- version 4.2.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2016 at 06:31 PM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

-- UserCandy v1.0.0

-- Instructions
-- Import this file to your mySQL database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `uc4`
--

-- --------------------------------------------------------

--
-- Table structure for table `uc_version`
--

CREATE TABLE IF NOT EXISTS `uc_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_users_groups`
-- Sets first user as Admin
--

INSERT INTO `uc_version` (`version`) VALUES
('1.0.0');

-- --------------------------------------------------------

--
-- Table structure for table `uc_activitylog`
--

CREATE TABLE IF NOT EXISTS `uc_activitylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `additionalinfo` varchar(500) NOT NULL DEFAULT 'none',
  `ip` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_attempts`
--

CREATE TABLE IF NOT EXISTS `uc_attempts` (
  `ip` varchar(39) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `expiredate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_groups`
--

CREATE TABLE IF NOT EXISTS `uc_groups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(150) DEFAULT NULL,
  `groupDescription` varchar(255) DEFAULT NULL,
  `groupFontColor` varchar(20) DEFAULT NULL,
  `groupFontWeight` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`groupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_sessions`
--

CREATE TABLE IF NOT EXISTS `uc_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `expiredate` datetime DEFAULT NULL,
  `ip` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_users`
--

CREATE TABLE IF NOT EXISTS `uc_users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `pass_change_timestamp` datetime DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `gender` varchar(8) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `aboutme` text DEFAULT NULL,
  `signature` text DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `activekey` varchar(15) NOT NULL DEFAULT '0',
  `resetkey` varchar(15) NOT NULL DEFAULT '0',
  `LastLogin` datetime DEFAULT NULL,
  `privacy_massemail` varchar(5) NOT NULL DEFAULT 'true',
  `privacy_pm` varchar(5) NOT NULL DEFAULT 'true',
  `privacy_profile` varchar(20) NOT NULL DEFAULT 'Public',
  `terms_view_date` TIMESTAMP NULL DEFAULT NULL,
  `privacy_view_date` TIMESTAMP NULL DEFAULT NULL,
  `SignUp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE INDEX index_username ON uc_users(username);

-- --------------------------------------------------------

--
-- Table structure for table `uc_users_groups`
--

CREATE TABLE IF NOT EXISTS `uc_users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `groupID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_users_online`
--

CREATE TABLE IF NOT EXISTS `uc_users_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `lastAccess` datetime DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lastAccess` (`lastAccess`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_sitelogs`
--

CREATE TABLE IF NOT EXISTS `uc_sitelogs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `membername` varchar(255) DEFAULT NULL,
  `refer` text,
  `useragent` text,
  `cfile` varchar(255) DEFAULT NULL,
  `uri` text,
  `ipaddy` varchar(255) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

CREATE INDEX index_timestamp ON uc_sitelogs(timestamp);

-- --------------------------------------------------------

--
-- Dumping data for table `uc_users_groups`
-- Sets first user as Admin
--

INSERT INTO `uc_users_groups` (`userID`, `groupID`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Dumping data for table `uc_groups`
--

INSERT INTO `uc_groups` (`groupID`, `groupName`, `groupDescription`, `groupFontColor`, `groupFontWeight`) VALUES
(1, 'New Member', 'Site Members that Recently Registered to the Web Site.', 'GREEN', 'Bold'),
(2, 'Member', 'Site Members That Have Been Here a While.', 'BLUE', 'BOLD'),
(3, 'Moderator', 'Site Members That Have a Little Extra Privilege on the Site.', 'ORANGE', 'BOLD'),
(4, 'Administrator', 'Site Members That Have Full Access To The Site.', 'RED', 'BOLD');

-- --------------------------------------------------------

--
-- Table structure for table `uc_settings`
--

CREATE TABLE IF NOT EXISTS `uc_settings` (
  `setting_id` int(10) NOT NULL AUTO_INCREMENT,
  `setting_title` varchar(255) DEFAULT NULL,
  `setting_data` text,
  `timestamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_settings`
--

INSERT INTO `uc_settings` (`setting_id`, `setting_title`, `setting_data`) VALUES
(1, 'site_title', 'My UserCandy Web Site'),
(2, 'site_description', 'Welcome to My UserCandy Web Site'),
(3, 'site_keywords', 'UC, UserCandy, PHP, Framework, Easy'),
(4, 'site_user_activation', 'false'),
(5, 'site_email_username', ''),
(6, 'site_email_password', ''),
(7, 'site_email_fromname', ''),
(8, 'site_email_host', ''),
(9, 'site_email_port', ''),
(10, 'site_email_smtp', ''),
(11, 'site_email_site', ''),
(12, 'site_recapcha_public', ''),
(13, 'site_recapcha_private', ''),
(14, 'site_user_invite_code', ''),
(15, 'site_theme', 'default'),
(16, 'max_attempts', '5'),
(17, 'security_duration', '5'),
(18, 'session_duration', '1'),
(19, 'session_duration_rm', '1'),
(20, 'min_username_length', '5'),
(21, 'max_username_length', '30'),
(22, 'min_password_length', '5'),
(23, 'max_password_length', '30'),
(24, 'min_email_length', '5'),
(25, 'max_email_length', '100'),
(26, 'random_key_length', '15'),
(27, 'default_timezone', 'America/Chicago'),
(28, 'users_pageinator_limit', '20'),
(29, 'friends_pageinator_limit', '20'),
(30, 'message_quota_limit', '50'),
(31, 'message_pageinator_limit', '10'),
(32, 'sweet_title_display', 'Sweets'),
(33, 'sweet_button_display', 'Sweet'),
(34, 'image_max_size', '800,600'),
(35, 'site_message', 'Welcome to your UserCandy Install.  Make sure to be the first to Register for this site to be Admin.  You can delete this message in the Admin Panel under Main Settings.  Savor the Sweetness!'),
(36, 'online_bubble', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `uc_links`
--

CREATE TABLE IF NOT EXISTS `uc_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `link_order` int(11) DEFAULT '0',
  `link_order_drop_down` int(11) DEFAULT '0',
  `drop_down` int(11) DEFAULT '0',
  `drop_down_for` int(11) DEFAULT '0',
  `require_plugin` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `permission` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_links`
--

INSERT INTO `uc_links` (`id`, `title`, `url`, `alt_text`, `location`, `link_order`, `link_order_drop_down`, `drop_down`, `drop_down_for`, `require_plugin`, `timestamp`) VALUES
(1, 'Home', 'Home', 'Home Page', 'header_main', 1, 0, 0, 0, NULL, NOW()),
(2, 'About', 'About', 'About Us', 'header_main', 2, 0, 1, 0, NULL, NOW()),
(3, 'Contact', 'Contact', 'Contact Us', 'header_main', 3, 0, 0, 0, '', NOW()),
(4, 'About', 'About', 'About', 'header_main', 2, 1, 0, 2, NULL, NOW()),
(5, 'Footer', 'Home', 'Footer', 'footer', 1, 0, 0, 0, NULL, NOW()),
(6, 'Contact Us', 'Contact', '', 'header_main', 2, 2, NULL, 2, '', NOW());

-- --------------------------------------------------------

--
-- Table structure for table `uc_users_images`
--

CREATE TABLE IF NOT EXISTS `uc_users_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `userImage` varchar(255) DEFAULT NULL,
  `defaultImage` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uc_pages`
--

CREATE TABLE IF NOT EXISTS `uc_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `pagefolder` varchar(255) DEFAULT NULL,
  `pagefile` varchar(255) DEFAULT NULL,
  `arguments` varchar(255) DEFAULT NULL,
  `headfoot` BOOLEAN NOT NULL DEFAULT TRUE,
  `template` varchar(255) NOT NULL DEFAULT 'Default',
  `enable` varchar(5) NOT NULL DEFAULT 'true',
  `sitemap` varchar(5) NOT NULL DEFAULT 'true',
  `stock` varchar(5) NOT NULL DEFAULT 'false',
  `edit_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_pages`
--

INSERT INTO `uc_pages` (`id`, `url`, `pagefolder`, `pagefile`, `arguments`, `sitemap`, `stock`, `template`) VALUES
(1, 'Home', 'Home', 'Home', NULL, 'true', 'true', 'Default'),
(2, 'Templates', 'Home', 'Templates', NULL, 'false', 'true', 'Default'),
(3, 'assets', 'Home', 'assets', NULL, 'false', 'true', 'Default'),
(4, 'Register', 'Auth', 'Register', NULL, 'false', 'true', 'Default'),
(5, 'Activate', 'Auth', 'Activate', NULL, 'false', 'true', 'Default'),
(6, 'Forgot-Password', 'Auth', 'Forgot-Password', NULL, 'false', 'true', 'Default'),
(7, 'Reset-Password', 'Auth', 'Reset-Password', NULL, 'false', 'true', 'Default'),
(8, 'Resend-Activation-Email', 'Auth', 'Resend-Activation-Email', NULL, 'false', 'true', 'Default'),
(9, 'Login', 'Auth', 'Login', NULL, 'false', 'true', 'Default'),
(10, 'Logout', 'Auth', 'Logout', NULL, 'false', 'true', 'Default'),
(11, 'Change-Email', 'Members', 'Change-Email', NULL, 'false', 'true', 'Default'),
(12, 'Change-Password', 'Members', 'Change-Password', NULL, 'false', 'true', 'Default'),
(13, 'Edit-Profile', 'Members', 'Edit-Profile', NULL, 'false', 'true', 'Default'),
(14, 'Edit-Profile-Images', 'Members', 'Edit-Profile-Images', NULL, 'false', 'true', 'Default'),
(15, 'Privacy-Settings', 'Members', 'Privacy-Settings', NULL, 'false', 'true', 'Default'),
(16, 'Account-Settings', 'Members', 'Account-Settings', NULL, 'false', 'true', 'Default'),
(17, 'LiveCheckEmail', 'Members', 'LiveCheckEmail', NULL, 'false', 'true', 'Default'),
(18, 'LiveCheckUserName', 'Members', 'LiveCheckUserName', NULL, 'false', 'true', 'Default'),
(19, 'Members', 'Members', 'Members', NULL, 'true', 'true', 'Default'),
(20, 'Online-Members', 'Members', 'Online-Members', NULL, 'true', 'true', 'Default'),
(21, 'Profile', 'Members', 'Profile', NULL, 'false', 'true', 'Default'),
(22, 'AdminPanel', 'AdminPanel', 'AdminPanel', NULL, 'false', 'true', 'AdminPanel'),
(23, 'AdminPanel-Settings', 'AdminPanel', 'AdminPanel-Settings', NULL, 'false', 'true', 'AdminPanel'),
(24, 'AdminPanel-AdvancedSettings', 'AdminPanel', 'AdminPanel-AdvancedSettings', NULL, 'false', 'true', 'AdminPanel'),
(25, 'AdminPanel-EmailSettings', 'AdminPanel', 'AdminPanel-EmailSettings', NULL, 'false', 'true', 'AdminPanel'),
(26, 'AdminPanel-Users', 'AdminPanel', 'AdminPanel-Users', NULL, 'false', 'true', 'AdminPanel'),
(27, 'AdminPanel-User', 'AdminPanel', 'AdminPanel-User', NULL, 'false', 'true', 'AdminPanel'),
(28, 'AdminPanel-Groups', 'AdminPanel', 'AdminPanel-Groups', NULL, 'false', 'true', 'AdminPanel'),
(29, 'AdminPanel-Group', 'AdminPanel', 'AdminPanel-Group', NULL, 'false', 'true', 'AdminPanel'),
(30, 'AdminPanel-MassEmail', 'AdminPanel', 'AdminPanel-MassEmail', NULL, 'false', 'true', 'AdminPanel'),
(31, 'AdminPanel-AuthLogs', 'AdminPanel', 'AdminPanel-AuthLogs', NULL, 'false', 'true', 'AdminPanel'),
(32, 'AdminPanel-SiteLinks', 'AdminPanel', 'AdminPanel-SiteLinks', NULL, 'false', 'true', 'AdminPanel'),
(33, 'AdminPanel-SiteLink', 'AdminPanel', 'AdminPanel-SiteLink', NULL, 'false', 'true', 'AdminPanel'),
(34, 'AdminPanel-Upgrade', 'AdminPanel', 'AdminPanel-Upgrade', NULL, 'false', 'true', 'AdminPanel'),
(35, 'AdminPanel-PagesPermissions', 'AdminPanel', 'AdminPanel-PagesPermissions', NULL, 'false', 'true', 'AdminPanel'),
(36, 'AdminPanel-PagePermissions', 'AdminPanel', 'AdminPanel-PagePermissions', NULL, 'false', 'true', 'AdminPanel'),
(37, 'AdminPanel-TermsPrivacy', 'AdminPanel', 'AdminPanel-TermsPrivacy', NULL, 'false', 'true', 'AdminPanel'),
(38, 'ChangeLang', 'Home', 'ChangeLang', NULL, 'false', 'true', 'Default'),
(39, 'About', 'Home', 'About', NULL, 'true', 'true', 'Default'),
(40, 'Contact', 'Home', 'Contact', NULL, 'true', 'true', 'Default'),
(41, 'sitemap', 'Home', 'sitemap', NULL, 'false', 'true', 'Default'),
(42, 'Terms', 'Home', 'Terms', NULL, 'false', 'true', 'Default'),
(43, 'Privacy', 'Home', 'Privacy', NULL, 'false', 'true', 'Default'),
(44, 'AdminPanel-Dispenser-Settings', 'AdminPanel', 'AdminPanel-Dispenser-Settings', NULL, 'false', 'true', 'AdminPanel'),
(45, 'AdminPanel-Dispenser-Widgets', 'AdminPanel', 'AdminPanel-Dispenser-Widgets', NULL, 'false', 'true', 'AdminPanel'),
(46, 'AdminPanel-Dispenser-Plugins', 'AdminPanel', 'AdminPanel-Dispenser-Plugins', NULL, 'false', 'true', 'AdminPanel'),
(47, 'AdminPanel-Dispenser-Themes', 'AdminPanel', 'AdminPanel-Dispenser-Themes', NULL, 'false', 'true', 'AdminPanel'),
(48, 'AdminPanel-Dispenser-Helpers', 'AdminPanel', 'AdminPanel-Dispenser-Helpers', NULL, 'false', 'true', 'AdminPanel'),
(49, 'AdminPanel-Dispenser-Widgets-Settings', 'AdminPanel', 'AdminPanel-Dispenser-Widgets-Settings', NULL, 'false', 'true', 'AdminPanel'),
(50, 'themes', 'Home', 'themes', NULL, 'false', 'true', 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `uc_pages_permissions`
--

CREATE TABLE IF NOT EXISTS `uc_pages_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_pages_permissions`
--

INSERT INTO `uc_pages_permissions` (`id`, `page_id`, `group_id`) VALUES
(1, 39, 0),(2, 16, 1),(3, 16, 2),(4, 16, 3),(5, 16, 4),(6, 5, 0),(7, 22, 4),(8, 24, 4),
(9, 31, 4),(10, 25, 4),(11, 29, 4),(12, 28, 4),(13, 30, 4),(14, 36, 4),(15, 35, 4),
(16, 23, 4),(17, 33, 4),(18, 32, 4),(19, 37, 4),(20, 34, 4),(21, 27, 4),(22, 26, 4),
(23, 3, 0),(24, 11, 1),(25, 11, 2),(26, 11, 3),(27, 11, 4),(28, 12, 1),(29, 12, 2),
(30, 12, 3),(31, 12, 4),(32, 38, 0),(33, 40, 0),(34, 13, 1),(35, 13, 2),(36, 13, 3),
(37, 13, 4),(38, 14, 1),(39, 14, 2),(40, 14, 3),(41, 14, 4),(42, 6, 0),(43, 1, 0),
(44, 17, 0),(45, 18, 0),(46, 9, 0),(47, 10, 1),(48, 10, 2),(49, 10, 3),(50, 10, 4),
(51, 19, 0),(52, 20, 0),(53, 43, 0),(54, 15, 1),(55, 15, 2),(56, 15, 3),(57, 15, 4),
(58, 21, 0),(59, 4, 0),(60, 8, 0),(61, 7, 0),(62, 41, 0),(63, 2, 0),(64, 42, 0),
(65, 44, 4),(66, 45, 4),(67, 46, 4),(68, 47, 4),(69, 48, 4),(70, 49, 4),(71, 50, 0);

-- --------------------------------------------------------

--
-- Table structure for table `uc_dispenser`
--

CREATE TABLE IF NOT EXISTS `uc_dispenser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `folder_location` varchar(255) DEFAULT NULL,
  `version` varchar(10) DEFAULT NULL,
  `enable` varchar(5) NOT NULL DEFAULT 'true',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_dispenser`
--

INSERT INTO `uc_dispenser` (`id`, `name`, `type`, `folder_location`, `version`, `enable`) VALUES
(1, 'MembersSidebar', 'widget', 'MembersSidebar', '1.0.0', 'true'),
(2, 'AccountSidebar', 'widget', 'AccountSidebar', '1.0.0', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `uc_dispenser_widgets`
--

CREATE TABLE IF NOT EXISTS `uc_dispenser_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_id` int(11) DEFAULT NULL,
  `display_type` varchar(255) DEFAULT NULL,
  `display_location` varchar(255) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Dumping data for table `uc_dispenser_widgets`
--

INSERT INTO `uc_dispenser_widgets` (`id`, `widget_id`, `display_type`, `display_location`, `page_id`) VALUES
(1, 1, 'sidebar', 'sidebar_right', 1),
(2, 1, 'sidebar', 'sidebar_left', 39),
(3, 1, 'sidebar', 'sidebar_right', 40),
(4, 2, 'sidebar', 'sidebar_left', 16),
(5, 2, 'sidebar', 'sidebar_left', 13),
(6, 2, 'sidebar', 'sidebar_left', 14),
(7, 2, 'sidebar', 'sidebar_left', 11),
(8, 2, 'sidebar', 'sidebar_left', 12),
(9, 2, 'sidebar', 'sidebar_left', 15),
(10, 1, 'sidebar', 'sidebar_right', 19),
(11, 1, 'sidebar', 'sidebar_right', 20);

-- --------------------------------------------------------
