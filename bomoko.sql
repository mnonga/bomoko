-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 14 mars 2019 à 07:24
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `bomoko`
--

-- --------------------------------------------------------

--
-- Structure de la table `activation_code`
--

DROP TABLE IF EXISTS `activation_code`;
CREATE TABLE IF NOT EXISTS `activation_code` (
  `userid` int(11) NOT NULL COMMENT 'La clé primaire de l''user, chaque user aura au plus un code',
  `code` varchar(32) COLLATE utf8_bin NOT NULL,
  `dateheure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `activation_code`
--

INSERT INTO `activation_code` (`userid`, `code`, `dateheure`) VALUES
(17, 'a7add084410411e99f531458d0cc516b', '2019-03-07 18:12:49');

--
-- Déclencheurs `activation_code`
--
DROP TRIGGER IF EXISTS `generate_activation_code`;
DELIMITER $$
CREATE TRIGGER `generate_activation_code` BEFORE INSERT ON `activation_code` FOR EACH ROW BEGIN
	SET NEW.code=REPLACE(UUID(),'-','');
    SET NEW.dateheure=CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `generate_activation_code2`;
DELIMITER $$
CREATE TRIGGER `generate_activation_code2` BEFORE UPDATE ON `activation_code` FOR EACH ROW BEGIN
	SET NEW.code=REPLACE(UUID(),'-','');
    SET NEW.dateheure=CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`) VALUES
(1, 'Talent'),
(2, 'Service'),
(3, 'Produit');

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateheure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contenu` varchar(255) COLLATE utf8_bin NOT NULL,
  `user` int(11) NOT NULL COMMENT 'Celui qui a commenté',
  `pub` int(11) NOT NULL COMMENT 'La pub concernée',
  `ref_comment` int(11) DEFAULT NULL COMMENT 'Le commentaire dont celui ci est la reponse',
  PRIMARY KEY (`id`),
  KEY `ref_comment` (`ref_comment`),
  KEY `user` (`user`),
  KEY `pub` (`pub`),
  KEY `dateheure` (`dateheure`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `dateheure`, `contenu`, `user`, `pub`, `ref_comment`) VALUES
(1, '2019-01-16 11:20:04', 'Salut', 12, 5, NULL),
(2, '2019-01-16 11:23:39', 'bien, cool', 12, 5, NULL),
(3, '2019-01-16 11:25:03', 'bien, cool', 12, 5, NULL),
(4, '2019-01-16 11:26:10', 'J\'arrrive mon frere, contact moi au num 0812964848', 12, 5, NULL),
(7, '2019-01-16 12:36:04', 'Nada', 12, 5, NULL),
(8, '2019-03-07 14:05:20', 'J\'adore votre travail', 12, 10, NULL);

--
-- Déclencheurs `comment`
--
DROP TRIGGER IF EXISTS `update_nombre_comment_moins`;
DELIMITER $$
CREATE TRIGGER `update_nombre_comment_moins` AFTER DELETE ON `comment` FOR EACH ROW BEGIN
	UPDATE pub SET pub.nb_comment=pub.nb_comment-1 WHERE OLD.pub=pub.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_nombre_comment_plus`;
DELIMITER $$
CREATE TRIGGER `update_nombre_comment_plus` AFTER INSERT ON `comment` FOR EACH ROW BEGIN
	UPDATE pub SET pub.nb_comment=pub.nb_comment+1 WHERE pub.id=NEW.pub;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `file`
--

DROP TABLE IF EXISTS `file`;
CREATE TABLE IF NOT EXISTS `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` enum('IMAGE','VIDEO') COLLATE utf8_bin NOT NULL,
  `pub` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pub` (`pub`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `file`
--

INSERT INTO `file` (`id`, `path`, `type`, `pub`) VALUES
(4, 'bfc05375f68163e2d98dcde4f821a59c.jpeg', 'IMAGE', 4),
(6, '92917732bfdf85e772a305d7e8e78075.png', 'IMAGE', 6),
(7, 'de9bf504337c7bbd0c603bdab22fe0a9.png', 'IMAGE', 7),
(8, 'f892d5771d76c50fcf99a8bce615b96c.png', 'IMAGE', 8),
(9, '2a6d910bb8e4ddc2bb049a39b617d631.png', 'IMAGE', 9),
(10, '0b959b465b189ab917bc37d61accc85f.jpeg', 'IMAGE', 10),
(11, 'da389b4d02e72242bd1c7a28c6aa373a.jpeg', 'IMAGE', 10),
(12, 'd8c44ffcf07363de63d99bf21528c775.png', 'IMAGE', 10),
(17, '2d0fa12366afd49f7795a9ddb75c30f9.png', 'IMAGE', 5),
(18, 'b304a08d60c15b4d6e4525817ab45d23.jpeg', 'IMAGE', 5),
(20, '4d67f93e693d35f8c29b213e3c6bb0d6.jpeg', 'IMAGE', 5),
(21, 'a20a899b9166de07bea6d0fde4d8ad5d.jpeg', 'IMAGE', 11),
(22, '2b0848c8fbe449cf3f0543639d9daafe.jpeg', 'IMAGE', 11),
(23, 'ce1eee1f8baeb5c67c91b4dc9a40f6ed.jpeg', 'IMAGE', 12),
(24, '68c3c95cf034280102f16b6bdd678f96.jpeg', 'IMAGE', 13),
(25, '240b610364dcdcfbf1b692318fcf04ba.jpeg', 'IMAGE', 13),
(26, 'c62858292ce538759c24c2080b035f24.jpeg', 'IMAGE', 14),
(27, '40778770d66fc43125ad5658df37e29a.jpeg', 'IMAGE', 14),
(28, '5d11207fa6ff2c25960232de044d9e19.jpeg', 'IMAGE', 14);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contenu` text COLLATE utf8_bin NOT NULL,
  `dateheure` datetime NOT NULL,
  `vu` tinyint(1) DEFAULT NULL,
  `user` int(11) NOT NULL COMMENT 'L''expediteur',
  `dest_user` int(11) NOT NULL COMMENT 'Le destinataire',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `dest_user` (`dest_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `pub`
--

DROP TABLE IF EXISTS `pub`;
CREATE TABLE IF NOT EXISTS `pub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `titre` varchar(255) COLLATE utf8_bin NOT NULL,
  `contenu` text COLLATE utf8_bin NOT NULL,
  `dateheure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nb_like` int(11) NOT NULL COMMENT 'nombre de like',
  `nb_dislike` int(11) NOT NULL COMMENT 'nombre de dislike',
  `categorie` int(11) NOT NULL,
  `nb_comment` int(11) NOT NULL DEFAULT '0' COMMENT 'nombre de commentaire',
  PRIMARY KEY (`id`),
  KEY `dateheure` (`dateheure`),
  KEY `user` (`user`),
  KEY `categorie` (`categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `pub`
--

INSERT INTO `pub` (`id`, `user`, `titre`, `contenu`, `dateheure`, `nb_like`, `nb_dislike`, `categorie`, `nb_comment`) VALUES
(4, 12, 'Femme à vendre, pas cher', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2018-12-11 21:31:51', 0, 0, 3, 0),
(5, 12, 'Episode Naruto derniere saison', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2018-12-11 21:32:52', 0, 1, 1, 5),
(6, 12, 'Maison à vendre', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2018-12-11 21:34:12', 0, 0, 3, 0),
(7, 12, 'Elle cherche ses parents', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2018-12-11 21:34:55', 0, 0, 2, 0),
(8, 12, 'Decouvrez notre logiciel de vente de vehicule', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2018-12-11 21:35:58', 0, 0, 2, 0),
(9, 12, 'Malewa', 'kjfhgfkjgfnhghuhguhudfjhfdhhdfjhjhfihufuhdhduhuheruhuhuhuhuh', '2018-12-23 14:13:42', 0, 0, 2, 0),
(10, 12, 'Vente Tshombo', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2019-01-15 22:39:28', 1, 0, 2, 1),
(11, 12, 'Lorem ipsum dolor', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2019-03-08 10:09:27', 0, 0, 1, 0),
(12, 16, 'Jc NONGA', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2019-03-08 17:42:12', 0, 0, 1, 0),
(13, 12, 'Coiffure', 'Coiffure homme 1000Fc, chez salon FRANCK, venez vous en direz des nouvels', '2019-03-11 19:54:23', 0, 0, 1, 0),
(14, 12, 'Transport', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2019-03-11 19:56:30', 0, 0, 2, 0);

-- --------------------------------------------------------

--
-- Structure de la table `pub_like`
--

DROP TABLE IF EXISTS `pub_like`;
CREATE TABLE IF NOT EXISTS `pub_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pub` int(11) NOT NULL,
  `liker` int(11) NOT NULL COMMENT 'The user pk who liked',
  `is_like` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_pub_liker` (`pub`,`liker`),
  KEY `pub_like_ibfk_1` (`liker`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `pub_like`
--

INSERT INTO `pub_like` (`id`, `pub`, `liker`, `is_like`) VALUES
(9, 10, 12, 1),
(10, 5, 12, 0);

--
-- Déclencheurs `pub_like`
--
DROP TRIGGER IF EXISTS `update_nombre_like_moins`;
DELIMITER $$
CREATE TRIGGER `update_nombre_like_moins` AFTER DELETE ON `pub_like` FOR EACH ROW BEGIN
	IF OLD.is_like THEN
    	UPDATE pub SET pub.nb_like=pub.nb_like-1 WHERE pub.id=OLD.pub;
    ELSEIF OLD.is_like=0 THEN
    	UPDATE pub SET pub.nb_dislike=pub.nb_dislike-1 WHERE pub.id=OLD.pub;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_nombre_like_plus`;
DELIMITER $$
CREATE TRIGGER `update_nombre_like_plus` AFTER INSERT ON `pub_like` FOR EACH ROW BEGIN
	IF NEW.is_like THEN
    	UPDATE pub SET pub.nb_like=pub.nb_like+1 WHERE pub.id=NEW.pub;
    ELSEIF NEW.is_like=0 THEN
    	UPDATE pub SET pub.nb_dislike=pub.nb_dislike+1 WHERE pub.id=NEW.pub;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_nombre_like_plus_ou_moins`;
DELIMITER $$
CREATE TRIGGER `update_nombre_like_plus_ou_moins` AFTER UPDATE ON `pub_like` FOR EACH ROW BEGIN
	IF NEW.is_like THEN
    	UPDATE pub SET pub.nb_like=pub.nb_like+1, pub.nb_dislike=pub.nb_dislike-1  WHERE pub.id=NEW.pub;
    ELSEIF NEW.is_like=0 THEN
    	UPDATE pub SET pub.nb_dislike=pub.nb_dislike+1, pub.nb_like=pub.nb_like-1  WHERE pub.id=NEW.pub;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `datenaissance` date DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sexe` enum('M','F') COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `photo` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'la photo prendra l''userid comme nom',
  `email` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'sera le login',
  `etat` enum('NOT_ACTIVATED','NORMAL','BLOCKED','RESET_PASSWORD') COLLATE utf8_bin NOT NULL DEFAULT 'NOT_ACTIVATED',
  `salt` varchar(255) COLLATE utf8_bin NOT NULL,
  `roles` longtext COLLATE utf8_bin NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `telephone` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`userid`, `name`, `datenaissance`, `adresse`, `sexe`, `password`, `photo`, `email`, `etat`, `salt`, `roles`) VALUES
(12, 'Jc NONGA', '1997-01-03', 'Kinshasa', 'M', '$2y$13$9O9GGphYV5Tb3/GEjvEkPuOUb7P3jpx8h1JSbXmzZcDberCRmeCQm', '12.jpeg', 'jc@gmail.com', 'NORMAL', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}'),
(13, 'Deborah BONDO NTUMBA', '1997-05-07', 'KINSHASA', 'F', '$2y$13$K9Xh9xzLQ/HvGZ7V9GZ2tudu.lVjamNuFbXyjGptCeNTzxWgKqPYm', '13.jpeg', 'deborah@gmail.com', 'NOT_ACTIVATED', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}'),
(14, 'Ben Kalu', '1996-01-01', 'Kinshasa/Limete', 'M', '$2y$13$1SKMf/Eg7sA2ibw4AhPd1OT7VVIl5MLBIEcmamPbXn1R6wxq2XyLq', NULL, 'ben@gmail.com', 'NOT_ACTIVATED', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}'),
(16, 'Alerte TSHALA BOTA', '1996-12-26', 'République démocratique du Congo, Ville de KINSHASA, Commune de LIMETE, quartier KINGABWA/SANS FILAvenue KOKO N°508 BIS', 'F', '$2y$13$6J4Rwr.k5ejW8oLzpsIxwOjj57m81AW4cJk46Ypu5ObkpaCeeV2aq', '16.jpeg', 'alerte@gmail.com', 'NORMAL', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}'),
(17, 'Serge NONGA', '2004-07-04', 'Kinshasa/Limete', 'M', '$2y$13$/92hp5C5ckujhkAfnrJrzuyKPPdj9DYABLg0ikIVCvwvIoz7EmXpe', NULL, 'serge@gmail.com', 'NOT_ACTIVATED', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}'),
(18, 'Carole NONGA', '2000-01-14', 'Kinshasa/Limete', 'F', '$2y$13$ELRO3Zev.w5orcOuMjv6Ru1sTYcmWw9vkI4wYinXHTF2A42q6TVka', NULL, 'carole@gmail.com', 'NORMAL', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}'),
(19, 'Michee NONGA', '1918-01-01', 'Kinshasa/Limete', 'M', '$2y$13$RmC2TCSlRytYx7iMqtEpyOp1puX/axhDTMJjFjDnl3NjO./8dtume', NULL, 'nongamichee@gmail.com', 'NORMAL', 'MTIzNDU2Nzg=', 'a:1:{i:0;s:16:\"ROLE_BUSINESSMAN\";}');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `pub`
--
ALTER TABLE `pub` ADD FULLTEXT KEY `titre` (`titre`,`contenu`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activation_code`
--
ALTER TABLE `activation_code`
  ADD CONSTRAINT `activation_code_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`pub`) REFERENCES `pub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`ref_comment`) REFERENCES `comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`pub`) REFERENCES `pub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`dest_user`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pub`
--
ALTER TABLE `pub`
  ADD CONSTRAINT `pub_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`userid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pub_ibfk_3` FOREIGN KEY (`categorie`) REFERENCES `categorie` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `pub_like`
--
ALTER TABLE `pub_like`
  ADD CONSTRAINT `pub_like_ibfk_1` FOREIGN KEY (`liker`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pub_like_ibfk_2` FOREIGN KEY (`pub`) REFERENCES `pub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
