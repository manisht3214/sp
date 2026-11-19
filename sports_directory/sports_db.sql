-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.11.16-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for sports_directory
CREATE DATABASE IF NOT EXISTS `sports_directory` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `sports_directory`;

-- Dumping structure for table sports_directory.achievements
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `competition` varchar(180) DEFAULT NULL,
  `achievement_year` year(4) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.achievements: ~0 rows (approximately)

-- Dumping structure for table sports_directory.career_history
CREATE TABLE IF NOT EXISTS `career_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `organization` varchar(180) DEFAULT NULL,
  `role` varchar(120) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `career_history_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.career_history: ~0 rows (approximately)

-- Dumping structure for table sports_directory.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `entity_type` enum('person','team','organization') DEFAULT 'person',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.categories: ~7 rows (approximately)
INSERT INTO `categories` (`id`, `name`, `slug`, `entity_type`) VALUES
	(1, 'Players / Athletes', 'players', 'person'),
	(2, 'Coaches', 'coaches', 'person'),
	(3, 'Referees / Umpires', 'referees-umpires', 'person'),
	(4, 'Judges / Officials', 'judges-officials', 'person'),
	(5, 'Other Staff', 'other-staff', 'person'),
	(6, 'Teams & Clubs', 'teams-clubs', 'team'),
	(7, 'Organizations', 'organizations', 'organization');

-- Dumping structure for table sports_directory.organizations
CREATE TABLE IF NOT EXISTS `organizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `organization_type` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.organizations: ~2 rows (approximately)
INSERT INTO `organizations` (`id`, `name`, `country`, `organization_type`, `logo`, `description`, `created_at`) VALUES
	(1, 'National Sports Association', 'Nepal', 'Sports Association', NULL, NULL, '2026-11-06 04:41:07'),
	(2, 'International Sports Federation', 'International', 'Federation', NULL, NULL, '2026-11-06 04:41:07');

-- Dumping structure for table sports_directory.people
CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(180) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `gender` varchar(30) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','retired') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.people: ~4 rows (approximately)
INSERT INTO `people` (`id`, `full_name`, `slug`, `gender`, `date_of_birth`, `nationality`, `photo`, `biography`, `email`, `website`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Alex Morgan', 'alex-morgan', 'Female', NULL, 'United States', NULL, 'Professional football player.', NULL, NULL, 'active', '2026-11-06 04:41:07', '2026-11-06 04:41:07'),
	(2, 'John Smith', 'john-smith', 'Male', NULL, 'England', NULL, 'Professional football coach.', NULL, NULL, 'active', '2026-11-06 04:41:07', '2026-11-06 04:41:07'),
	(3, 'David Wilson', 'david-wilson', 'Male', NULL, 'England', NULL, 'Experienced football referee.', NULL, NULL, 'active', '2026-11-06 04:41:07', '2026-11-06 04:41:07'),
	(4, 'Manish Tamang', 'manish-tamang', 'Male', '2001-01-06', 'Nepali', 'boxer.png', 'Instead of fighting in sanctioned matches, he infamously visited local commercial gyms, ambushed unsuspecting or non-professional gym-goers with cheap shots, and fled or cowered if they fought back. He then edited and uploaded the videos online to claim he was an undefeated champion', NULL, NULL, 'active', '2026-11-06 04:55:28', '2026-11-06 04:55:28');

-- Dumping structure for table sports_directory.person_sports
CREATE TABLE IF NOT EXISTS `person_sports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `sport_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `jersey_number` varchar(20) DEFAULT NULL,
  `qualification` varchar(180) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `statistics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`statistics`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_person_sport_role` (`person_id`,`sport_id`,`category_id`),
  KEY `sport_id` (`sport_id`),
  KEY `category_id` (`category_id`),
  KEY `organization_id` (`organization_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `person_sports_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `person_sports_ibfk_2` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `person_sports_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `person_sports_ibfk_4` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `person_sports_ibfk_5` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.person_sports: ~4 rows (approximately)
INSERT INTO `person_sports` (`id`, `person_id`, `sport_id`, `category_id`, `organization_id`, `team_id`, `designation`, `position`, `jersey_number`, `qualification`, `start_date`, `end_date`, `statistics`) VALUES
	(1, 1, 1, 1, NULL, 1, 'Player', 'Forward', NULL, NULL, NULL, NULL, NULL),
	(2, 2, 1, 2, NULL, 1, 'Head Coach', NULL, NULL, NULL, NULL, NULL, NULL),
	(3, 3, 1, 3, NULL, NULL, 'Referee', NULL, NULL, NULL, NULL, NULL, NULL),
	(4, 4, 9, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL);

-- Dumping structure for table sports_directory.sports
CREATE TABLE IF NOT EXISTS `sports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.sports: ~10 rows (approximately)
INSERT INTO `sports` (`id`, `name`, `slug`, `description`, `image`, `status`, `created_at`) VALUES
	(1, 'Football', 'football', 'Association football', NULL, 1, '2026-11-06 04:41:07'),
	(2, 'Cricket', 'cricket', 'International and domestic cricket', NULL, 1, '2026-11-06 04:41:07'),
	(3, 'Basketball', 'basketball', 'Basketball', NULL, 1, '2026-11-06 04:41:07'),
	(4, 'Tennis', 'tennis', 'Tennis', NULL, 1, '2026-11-06 04:41:07'),
	(5, 'Badminton', 'badminton', 'Badminton', NULL, 1, '2026-11-06 04:41:07'),
	(6, 'Volleyball', 'volleyball', 'Volleyball', NULL, 1, '2026-11-06 04:41:07'),
	(7, 'Swimming', 'swimming', 'Competitive swimming', NULL, 1, '2026-11-06 04:41:07'),
	(8, 'Athletics', 'athletics', 'Track and field athletics', NULL, 1, '2026-11-06 04:41:07'),
	(9, 'Boxing', 'boxing', 'Boxing', NULL, 1, '2026-11-06 04:41:07'),
	(10, 'Rugby', 'rugby', 'Rugby', NULL, 1, '2026-11-06 04:41:07');

-- Dumping structure for table sports_directory.sport_categories
CREATE TABLE IF NOT EXISTS `sport_categories` (
  `sport_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`sport_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `sport_categories_ibfk_1` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sport_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.sport_categories: ~70 rows (approximately)
INSERT INTO `sport_categories` (`sport_id`, `category_id`) VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(1, 4),
	(1, 5),
	(1, 6),
	(1, 7),
	(2, 1),
	(2, 2),
	(2, 3),
	(2, 4),
	(2, 5),
	(2, 6),
	(2, 7),
	(3, 1),
	(3, 2),
	(3, 3),
	(3, 4),
	(3, 5),
	(3, 6),
	(3, 7),
	(4, 1),
	(4, 2),
	(4, 3),
	(4, 4),
	(4, 5),
	(4, 6),
	(4, 7),
	(5, 1),
	(5, 2),
	(5, 3),
	(5, 4),
	(5, 5),
	(5, 6),
	(5, 7),
	(6, 1),
	(6, 2),
	(6, 3),
	(6, 4),
	(6, 5),
	(6, 6),
	(6, 7),
	(7, 1),
	(7, 2),
	(7, 3),
	(7, 4),
	(7, 5),
	(7, 6),
	(7, 7),
	(8, 1),
	(8, 2),
	(8, 3),
	(8, 4),
	(8, 5),
	(8, 6),
	(8, 7),
	(9, 1),
	(9, 2),
	(9, 3),
	(9, 4),
	(9, 5),
	(9, 6),
	(9, 7),
	(10, 1),
	(10, 2),
	(10, 3),
	(10, 4),
	(10, 5),
	(10, 6),
	(10, 7);

-- Dumping structure for table sports_directory.teams
CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sport_id` int(11) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `name` varchar(180) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sport_id` (`sport_id`),
  KEY `organization_id` (`organization_id`),
  CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`),
  CONSTRAINT `teams_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sports_directory.teams: ~2 rows (approximately)
INSERT INTO `teams` (`id`, `sport_id`, `organization_id`, `name`, `country`, `logo`, `description`) VALUES
	(1, 1, 1, 'Kathmandu United', 'Nepal', NULL, NULL),
	(2, 2, 1, 'Kathmandu Cricket Club', 'Nepal', NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
