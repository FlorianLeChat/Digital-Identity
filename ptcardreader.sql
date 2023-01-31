-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 31 jan. 2023 à 11:09
-- Version du serveur : 8.0.27
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ptcardreader`
--

-- --------------------------------------------------------

--
-- Structure de la table `absence`
--

DROP TABLE IF EXISTS `absence`;
CREATE TABLE IF NOT EXISTS `absence` (
  `id` int NOT NULL AUTO_INCREMENT,
  `justification_statut` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `absence_cours`
--

DROP TABLE IF EXISTS `absence_cours`;
CREATE TABLE IF NOT EXISTS `absence_cours` (
  `absence_id` int NOT NULL,
  `cours_id` int NOT NULL,
  PRIMARY KEY (`absence_id`,`cours_id`),
  KEY `IDX_9D0D13872DFF238F` (`absence_id`),
  KEY `IDX_9D0D13877ECF78B0` (`cours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `absence_user`
--

DROP TABLE IF EXISTS `absence_user`;
CREATE TABLE IF NOT EXISTS `absence_user` (
  `absence_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`absence_id`,`user_id`),
  KEY `IDX_FA8218D62DFF238F` (`absence_id`),
  KEY `IDX_FA8218D6A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

DROP TABLE IF EXISTS `cours`;
CREATE TABLE IF NOT EXISTS `cours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terminé` tinyint(1) NOT NULL,
  `token` varchar(4096) COLLATE utf8mb4_unicode_ci NOT NULL,
  `groupe` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours_formation`
--

DROP TABLE IF EXISTS `cours_formation`;
CREATE TABLE IF NOT EXISTS `cours_formation` (
  `cours_id` int NOT NULL,
  `formation_id` int NOT NULL,
  PRIMARY KEY (`cours_id`,`formation_id`),
  KEY `IDX_B8E51B787ECF78B0` (`cours_id`),
  KEY `IDX_B8E51B785200282E` (`formation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours_matiere`
--

DROP TABLE IF EXISTS `cours_matiere`;
CREATE TABLE IF NOT EXISTS `cours_matiere` (
  `cours_id` int NOT NULL,
  `matiere_id` int NOT NULL,
  PRIMARY KEY (`cours_id`,`matiere_id`),
  KEY `IDX_D3123E317ECF78B0` (`cours_id`),
  KEY `IDX_D3123E31F46CD258` (`matiere_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours_user`
--

DROP TABLE IF EXISTS `cours_user`;
CREATE TABLE IF NOT EXISTS `cours_user` (
  `cours_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`cours_id`,`user_id`),
  KEY `IDX_5EE5E9A67ECF78B0` (`cours_id`),
  KEY `IDX_5EE5E9A6A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20221113104604', '2022-11-13 10:47:44', 590),
('DoctrineMigrations\\Version20221113110110', '2022-11-13 11:01:16', 55),
('DoctrineMigrations\\Version20221212105429', '2022-12-12 11:54:43', 551),
('DoctrineMigrations\\Version20230115175642', '2023-01-15 18:56:58', 671),
('DoctrineMigrations\\Version20230115182116', '2023-01-15 19:21:23', 1264),
('DoctrineMigrations\\Version20230115183838', '2023-01-15 19:38:42', 115),
('DoctrineMigrations\\Version20230115184430', '2023-01-15 19:44:33', 1830),
('DoctrineMigrations\\Version20230115184802', '2023-01-15 19:48:04', 218),
('DoctrineMigrations\\Version20230115185235', '2023-01-15 19:52:40', 2140),
('DoctrineMigrations\\Version20230115195746', '2023-01-15 19:58:14', 219),
('DoctrineMigrations\\Version20230115200349', '2023-01-15 20:03:52', 156),
('DoctrineMigrations\\Version20230115201103', '2023-01-15 20:11:07', 1929),
('DoctrineMigrations\\Version20230115201226', '2023-01-15 20:12:29', 1599),
('DoctrineMigrations\\Version20230115201834', '2023-01-15 20:18:38', 645),
('DoctrineMigrations\\Version20230118103052', '2023-01-18 10:31:06', 911),
('DoctrineMigrations\\Version20230118114405', '2023-01-18 11:44:08', 57),
('DoctrineMigrations\\Version20230119162207', '2023-01-19 20:39:39', 415),
('DoctrineMigrations\\Version20230119174328', '2023-01-19 20:39:40', 22),
('DoctrineMigrations\\Version20230119203917', '2023-01-23 14:06:07', 62),
('DoctrineMigrations\\Version20230122194802', '2023-01-23 14:06:07', 13),
('DoctrineMigrations\\Version20230123140540', '2023-01-23 14:06:07', 219),
('DoctrineMigrations\\Version20230123140859', '2023-01-23 14:09:02', 785),
('DoctrineMigrations\\Version20230123142537', '2023-01-23 14:25:44', 4143),
('DoctrineMigrations\\Version20230123163807', '2023-01-23 16:38:13', 362),
('DoctrineMigrations\\Version20230123165749', '2023-01-23 16:57:56', 1688),
('DoctrineMigrations\\Version20230125144109', '2023-01-25 14:41:15', 414),
('DoctrineMigrations\\Version20230125211012', '2023-01-27 12:40:36', 400),
('DoctrineMigrations\\Version20230125211339', '2023-01-27 12:40:36', 14),
('DoctrineMigrations\\Version20230126080856', '2023-01-27 12:40:36', 66),
('DoctrineMigrations\\Version20230127124024', '2023-01-27 12:40:36', 13),
('DoctrineMigrations\\Version20230130143220', '2023-01-30 14:32:35', 688),
('DoctrineMigrations\\Version20230131091451', '2023-01-31 09:54:28', 733),
('DoctrineMigrations\\Version20230131091737', '2023-01-31 09:54:28', 1989);

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE IF NOT EXISTS `formation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_formation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id`, `nom_formation`) VALUES
(1, 'M1MIAGE'),
(2, 'M2MIAGE'),
(3, 'L1 Info'),
(4, 'L3MIAGE');

-- --------------------------------------------------------

--
-- Structure de la table `matiere`
--

DROP TABLE IF EXISTS `matiere`;
CREATE TABLE IF NOT EXISTS `matiere` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_matiere` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matiere`
--

INSERT INTO `matiere` (`id`, `nome_matiere`) VALUES
(1, 'Base de données'),
(2, 'Technologie Web'),
(3, 'Intelligence Artificiel'),
(4, 'Maths'),
(5, 'Langage R');

-- --------------------------------------------------------

--
-- Structure de la table `matiere_formation`
--

DROP TABLE IF EXISTS `matiere_formation`;
CREATE TABLE IF NOT EXISTS `matiere_formation` (
  `matiere_id` int NOT NULL,
  `formation_id` int NOT NULL,
  PRIMARY KEY (`matiere_id`,`formation_id`),
  KEY `IDX_854E7299F46CD258` (`matiere_id`),
  KEY `IDX_854E72995200282E` (`formation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matiere_formation`
--

INSERT INTO `matiere_formation` (`matiere_id`, `formation_id`) VALUES
(1, 1),
(2, 2),
(5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `matiere_user`
--

DROP TABLE IF EXISTS `matiere_user`;
CREATE TABLE IF NOT EXISTS `matiere_user` (
  `matiere_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`matiere_id`,`user_id`),
  KEY `IDX_FE415017F46CD258` (`matiere_id`),
  KEY `IDX_FE415017A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matiere_user`
--

INSERT INTO `matiere_user` (`matiere_id`, `user_id`) VALUES
(1, 4),
(2, 2),
(3, 2),
(4, 3),
(5, 3);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `presence`
--

DROP TABLE IF EXISTS `presence`;
CREATE TABLE IF NOT EXISTS `presence` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `presence_cours`
--

DROP TABLE IF EXISTS `presence_cours`;
CREATE TABLE IF NOT EXISTS `presence_cours` (
  `presence_id` int NOT NULL,
  `cours_id` int NOT NULL,
  PRIMARY KEY (`presence_id`,`cours_id`),
  KEY `IDX_D4F8BC1CF328FFC4` (`presence_id`),
  KEY `IDX_D4F8BC1C7ECF78B0` (`cours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `presence_user`
--

DROP TABLE IF EXISTS `presence_user`;
CREATE TABLE IF NOT EXISTS `presence_user` (
  `presence_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`presence_id`,`user_id`),
  KEY `IDX_666ACE30F328FFC4` (`presence_id`),
  KEY `IDX_666ACE30A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `firsname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_badge` int NOT NULL,
  `td` int DEFAULT NULL,
  `tp` int DEFAULT NULL,
  `year` int DEFAULT NULL,
  `formation_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  KEY `IDX_8D93D6495200282E` (`formation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `firsname`, `lastname`, `code_badge`, `td`, `tp`, `year`, `formation_id`) VALUES
(1, 'brahim.lamjarad@etu.univ-cotedazur.fr', '[\"ROLE_STUDENT\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Brahim', 'LAMJARAD', 1234, 3, NULL, 20222023, 1),
(2, 'michel.buffa@univ-cotedazur.fr', '[\"ROLE_TEACHER\"]', '$2y$13$ELchctDA.F6M5M1HDJbKDelnD7skA.NmpkQf6Kv/U0Vn.NkJffOh.', 'Michel', 'Buffa', 2334, NULL, NULL, NULL, NULL),
(3, 'leo.donati@univ-cotedazur.fr', '[\"ROLE_TEACHER\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Leo', 'DONATI', 123, NULL, NULL, NULL, NULL),
(4, 'emmanuelle.baret@univ-cotedazur.fr', '[\"ROLE_ADMIN\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Emmanuelle', 'BARET', 1234, NULL, NULL, NULL, NULL),
(5, 'ons.hamdi@etu.univ-cotedazur.fr', '[\"ROLE_STUDENT\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Ons', 'HAMDI', 1234, 2, NULL, 20222023, 1),
(6, 'toto.tata@etu.univ-cotedazur.fr', '[\"ROLE_STUDENT\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Toto', 'TATA', 1234, 1, 2, 20222023, 3),
(7, 'emre.orsay@etu.univ-cotedazur.fr', '[\"ROLE_STUDENT\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Emre', 'ORSAY', 123, 3, NULL, 20222023, 1),
(8, 'kenzo.daben@etu.univ-cotedazur.fr', '[\"ROLE_STUDENT\"]', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Kenzo', 'DABEN', 1242, 2, NULL, 20222023, 1),
(9, 'yanis.allouche@etu.univ-cotedazur.fr', '{\"3\":\"[\\\"ROLE_STUDENT\\\"]\"}', '$2y$13$ij5t6QqjqIZreiOvBuVdWOadLfnmOkAr2CAiSxl5eJ7TueNkPjO5y', 'Yanis', 'ALLOUCHE', 123, 3, NULL, 20222020, 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absence_cours`
--
ALTER TABLE `absence_cours`
  ADD CONSTRAINT `FK_9D0D13872DFF238F` FOREIGN KEY (`absence_id`) REFERENCES `absence` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_9D0D13877ECF78B0` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `absence_user`
--
ALTER TABLE `absence_user`
  ADD CONSTRAINT `FK_FA8218D62DFF238F` FOREIGN KEY (`absence_id`) REFERENCES `absence` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FA8218D6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cours_formation`
--
ALTER TABLE `cours_formation`
  ADD CONSTRAINT `FK_B8E51B785200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B8E51B787ECF78B0` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cours_matiere`
--
ALTER TABLE `cours_matiere`
  ADD CONSTRAINT `FK_D3123E317ECF78B0` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_D3123E31F46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `matiere` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cours_user`
--
ALTER TABLE `cours_user`
  ADD CONSTRAINT `FK_5EE5E9A67ECF78B0` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_5EE5E9A6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matiere_formation`
--
ALTER TABLE `matiere_formation`
  ADD CONSTRAINT `FK_854E72995200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_854E7299F46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `matiere` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matiere_user`
--
ALTER TABLE `matiere_user`
  ADD CONSTRAINT `FK_FE415017A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FE415017F46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `matiere` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `presence_cours`
--
ALTER TABLE `presence_cours`
  ADD CONSTRAINT `FK_D4F8BC1C7ECF78B0` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_D4F8BC1CF328FFC4` FOREIGN KEY (`presence_id`) REFERENCES `presence` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `presence_user`
--
ALTER TABLE `presence_user`
  ADD CONSTRAINT `FK_666ACE30A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_666ACE30F328FFC4` FOREIGN KEY (`presence_id`) REFERENCES `presence` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6495200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
