-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.11-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table quiz_os.tb_answer
CREATE TABLE IF NOT EXISTS `tb_answer` (
  `id_answer` int(5) NOT NULL AUTO_INCREMENT,
  `id_chat` varchar(50) DEFAULT NULL,
  `no_question` int(11) DEFAULT NULL,
  `id_question` int(11) DEFAULT NULL,
  `answer` varchar(2) DEFAULT NULL,
  `correct_answer` varchar(2) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_answer`)
) ENGINE=InnoDB AUTO_INCREMENT=862 DEFAULT CHARSET=utf8mb4;


-- Dumping structure for table quiz_os.tb_log
CREATE TABLE IF NOT EXISTS `tb_log` (
  `id_chat` varchar(50) DEFAULT NULL,
  `command_log` varchar(250) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40000 ALTER TABLE `tb_log` ENABLE KEYS */;

-- Dumping structure for table quiz_os.tb_question
CREATE TABLE IF NOT EXISTS `tb_question` (
  `id_question` int(3) NOT NULL AUTO_INCREMENT,
  `question` text DEFAULT NULL,
  `answer_a` text DEFAULT NULL,
  `answer_b` text DEFAULT NULL,
  `answer_c` text DEFAULT NULL,
  `answer_d` text DEFAULT NULL,
  `correct_answer` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id_question`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table quiz_os.tb_question: ~60 rows (approximately)
/*!40000 ALTER TABLE `tb_question` DISABLE KEYS */;
INSERT INTO `tb_question` (`id_question`, `question`, `answer_a`, `answer_b`, `answer_c`, `answer_d`, `correct_answer`) VALUES
	(1, 'PAM Stands for?', '​Pluggable Authentication Module', '​Plug-in Authentication Module', '​Preferred Authentication Module', '​Pluggable Authentication Media', 'A'),
	(2, 'What is the function of PAM?', '​PAM provide dynamic authentication support that sits between Linux Kernel and the Linux native authentication system', '​PAM provide dynamic authentication between Linux and Windows', '​PAM provide dynamic authentication support that sits between Linux applications and the Linux native authentication system', '​PAM provide dynamic logging between Linux applications and the Linux native authentication system', 'C'),
	(3, 'The main purpose of PAM is', '​to allow system administrators to integrate services or programs with different authentication mechanism without changing the code for the service', '​to allow system developers to integrate services or programs with different authentication mechanism without changing the code for the service', '​to allow system administrators and developers to integrate users with hardware', '​A and B', 'D'),
	(4, 'How to reject all incoming traffic from 192.168.0.25?', '​firewall-cmd --add-rich-rule=\'rule family="ipv4" source address="192.168.0.25" drop\'', '​firewall-cmd --add-rich-rule=\'rule family="ipv4" source address="192.168.0.25" reject\'', '​firewall-cmd --add-high-rule=\'rule family="ipv4" source address="192.168.0.25" reject\'', 'none above', 'B');
/*!40000 ALTER TABLE `tb_question` ENABLE KEYS */;

-- Dumping structure for table quiz_os.tb_status
CREATE TABLE IF NOT EXISTS `tb_status` (
  `id_chat` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `status_option` varchar(50) DEFAULT NULL,
  `status_option2` varchar(50) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping structure for table quiz_os.tb_user
CREATE TABLE IF NOT EXISTS `tb_user` (
  `id_chat` varchar(50) DEFAULT NULL,
  `nama` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
