-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 01 avr. 2026 à 08:51
-- Version du serveur : 8.0.45-0ubuntu0.24.04.1
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sesomate`
--

-- --------------------------------------------------------

--
-- Structure de la table `Allowed`
--

CREATE TABLE `Allowed` (
  `ID_role` int NOT NULL,
  `ID_utility` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Allowed`
--

INSERT INTO `Allowed` (`ID_role`, `ID_utility`) VALUES
(2, 1),
(3, 1),
(1, 2),
(3, 2),
(2, 3),
(3, 3),
(3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `Apply`
--

CREATE TABLE `Apply` (
  `ID_user` int NOT NULL,
  `ID_offer` int NOT NULL,
  `CV` varchar(255) DEFAULT NULL,
  `ML` varchar(255) DEFAULT NULL,
  `Comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Apply`
--



-- --------------------------------------------------------

--
-- Structure de la table `Company`
--

CREATE TABLE `Company` (
  `ID_company` int NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Phone_Number` varchar(10) DEFAULT NULL,
  `Creation_date` varchar(50) DEFAULT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Company`
--

INSERT INTO `Company` (`ID_company`, `Name`, `Creation_date`, `Email`, `Phone_Number`, `Description`) VALUES
(1, 'TechCorp', '2020-01-01', 'contact@techcorp.com', '0102030405', 'Entreprise web'),
(2, 'DataSolutions', '2019-05-10', 'hr@data.com', '0203040506', 'Data & IA'),
(3, 'GreenEnergy', '2018-03-15', 'contact@green.com', '0304050607', 'Energie renouvelable');
-- --------------------------------------------------------

--
-- Structure de la table `Evaluate`
--

CREATE TABLE `Evaluate` (
  `ID_user` int NOT NULL,
  `ID_company` int NOT NULL,
  `Rate` int DEFAULT NULL,
  `Comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Evaluate`
--

INSERT INTO `Evaluate` (`ID_user`, `ID_company`, `Rate`, `Comment`) VALUES
(1, 1, 5, 'Super entreprise'),
(2, 2, 4, 'Bonne expérience');

-- --------------------------------------------------------

--
-- Structure de la table `Job_offer`
--

CREATE TABLE `Job_offer` (
  `ID_offer` int NOT NULL,
  `Title` text,
  `Description` text,
  `Category` varchar(50) DEFAULT NULL,
  `Salary` decimal(10,2) DEFAULT NULL,
  `Duration` int DEFAULT NULL,
  `Create_date` date DEFAULT NULL,
  `Start_date` date DEFAULT NULL,
  `ID_company` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Job_offer`
--

INSERT INTO `Job_offer` (`ID_offer`, `Title`, `Description`, `Salary`, `Duration`, `Category`, `Create_date`, `Start_date`, `ID_company`) VALUES
(1, 'Développeur Web', 'PHP / MySQL', 2000.00, 6, 'Informatique', '2026-03-01', '2026-04-01', 1),
(2, 'Front-End', 'React JS', 1800.00, 6, 'Informatique', '2026-03-01', '2026-04-01', 1),
(3, 'Back-End', 'Laravel API', 2100.00, 6, 'Informatique', '2026-02-01', '2026-03-01', 1),
(4, 'Full Stack', 'Web complet', 2200.00, 6, 'Informatique', '2026-01-01', '2026-02-01', 1),
(5, 'Dev Mobile', 'Flutter', 2000.00, 6, 'Informatique', '2026-02-01', '2026-03-01', 1),
(6, 'Data Analyst', 'Python SQL', 2200.00, 6, 'Data', '2026-02-01', '2026-03-01', 2),
(7, 'Data Scientist', 'Machine Learning', 2400.00, 6, 'Data', '2026-01-01', '2026-02-01', 2),
(8, 'BI Analyst', 'Power BI', 2100.00, 5, 'Data', '2026-03-01', '2026-04-01', 2),
(9, 'Data Engineer', 'Pipeline data', 2300.00, 6, 'Data', '2026-02-01', '2026-03-01', 2),
(10, 'AI Engineer', 'Deep Learning', 2500.00, 6, 'Data', '2026-01-01', '2026-02-01', 2),
(11, 'Ingénieur Energie', 'Optimisation', 2300.00, 6, 'Energie', '2026-01-01', '2026-02-01', 3),
(12, 'Technicien Energie', 'Maintenance', 1800.00, 6, 'Energie', '2026-02-01', '2026-03-01', 3),
(13, 'Projet Environnement', 'Gestion projet', 2000.00, 6, 'Energie', '2026-03-01', '2026-04-01', 3),
(14, 'Consultant RSE', 'Développement durable', 2100.00, 6, 'Energie', '2026-01-01', '2026-02-01', 3),
(15, 'Thermicien', 'Bâtiment', 2400.00, 6, 'Energie', '2026-02-01', '2026-03-01', 3),
(16, 'DevOps', 'Docker CI/CD', 2400.00, 6, 'Informatique', '2026-03-01', '2026-04-01', 1),
(17, 'Cybersecurity', 'Sécurité SI', 2500.00, 6, 'Informatique', '2026-02-01', '2026-03-01', 2),
(18, 'QA Tester', 'Tests logiciels', 1800.00, 4, 'Informatique', '2026-01-01', '2026-02-01', 1),
(19, 'Product Owner', 'Agile', 2200.00, 6, 'Management', '2026-03-01', '2026-04-01', 2),
(20, 'Scrum Master', 'Gestion agile', 2300.00, 6, 'Management', '2026-02-01', '2026-03-01', 1);
-- --------------------------------------------------------

--
-- Structure de la table `Profil`
--

CREATE TABLE `Profil` (
  `ID_profil` int NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Lastname` varchar(50) DEFAULT NULL,
  `Photo_path` varchar(255) DEFAULT NULL,
  `Phone_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Profil`
--

INSERT INTO `Profil` (`ID_profil`, `Name`, `Lastname`, `Photo_path`, `Phone_number`) VALUES
(1, 'Mathis', 'Boulenger', NULL, '0600000001'),
(2, 'Lou', 'Dutertre', NULL, '0600000002'),
(3, 'Nathan', 'Bocquet', NULL, '0600000003'),
(4, 'Ines', 'Lancelevee', NULL, '0600000004'),
(5, 'Seohyun', 'Nam', NULL, '0600000005'),
(6, 'Admin', 'Root', NULL, '0600000006');

-- --------------------------------------------------------
CREATE TABLE `Promotion`(
  `ID_promotion` int NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `ID_pilote` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `Promotion` (`ID_promotion`, `Name`, `ID_pilote`) VALUES
(1, 'CESI 2024', 4),
(2, 'CESI 2025', 5);
--
-- Structure de la table `Remember_tokens`
--

CREATE TABLE `Remember_tokens` (
  `ID_token` int NOT NULL,
  `Token` varchar(255) DEFAULT NULL,
  `Expires` varchar(50) DEFAULT NULL,
  `ID_user` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Remember_tokens`
--

INSERT INTO `Remember_tokens` (`ID_token`, `Token`, `Expires`, `ID_user`) VALUES
(1, 'token123', '2026-01-01', 1),
(2, 'token456', '2026-01-01', 2);

-- --------------------------------------------------------

--
-- Structure de la table `Role`
--

CREATE TABLE `Role` (
  `ID_role` int NOT NULL,
  `Status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Role`
--

INSERT INTO `Role` (`ID_role`, `Status`) VALUES
(1, 'Etudiant'),
(2, 'Pilote'),
(3, 'Admin');

-- --------------------------------------------------------
CREATE TABLE `Study`(
  `ID_Promotion` int NOT NULL,
  `ID_user` int NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
--
-- Structure de la table `User_`
--

CREATE TABLE `User_` (
  `ID_user` int NOT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Password` varchar(60) DEFAULT NULL,
  `ID_profil` int DEFAULT NULL,
  `ID_role` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `User_`
--

INSERT INTO `User_` (`ID_user`, `Email`, `Password`, `ID_profil`, `ID_role`) VALUES
(1, 'mathis@mail.com', '$2y$10$bUihwu/wK1pXLBX.zd/9z.ZVZH6zyz1scTBHVfEF46zULNd6m9RMe', 1, 1),
(2, 'lou@mail.com', '$2y$10$r0ldpp4H8WyLuQARr5YNCusYnJjen4xdd9Frsg4QUM4aq.HFut7mq', 2, 1),
(3, 'nathan@mail.com', '$2y$10$d4TljyZn7wyueltn3cZYD.1QZS6B6ERhbZX0GyCsCJ1zTUGfKIRW.', 3, 1),
(4, 'ines@mail.com', '$2y$10$tgTbvbhf0uKXEyPSe.t5YOYvMurByQf5JgRoYS1G5ou9KTkdIbwVK', 4, 2),
(5, 'seo@mail.com', '$2y$10$qn/hrIxwT6nDCF9IHl8pP.MHOXukGg7XKdseuloDmj6F8PBU3slei', 5, 2),
(6, 'admin@mail.com', '$2y$10$xzlurJpQ2vcJTAc5459Pc.l.j5uHuSIsbz0J3CUh/zUiSc8OWB4J6', 6, 3);

-- --------------------------------------------------------

--
-- Structure de la table `Utility`
--

CREATE TABLE `Utility` (
  `ID_utility` int NOT NULL,
  `Utility_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Utility`
--

INSERT INTO `Utility` (`ID_utility`, `Utility_name`) VALUES
(1, 'POST_JOB'),
(2, 'APPLY_JOB'),
(3, 'DELETE_JOB'),
(4, 'MANAGE_USERS');

-- --------------------------------------------------------

--
-- Structure de la table `Wishlist`
--

CREATE TABLE `Wishlist` (
  `ID_user` int NOT NULL,
  `ID_offer` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Wishlist`
--

INSERT INTO `Wishlist` (`ID_user`, `ID_offer`) VALUES
(1, 1),
(1, 2);

--
-- Index pour les tables déchargées
--
ALTER TABLE `Study`
  ADD PRIMARY KEY (`ID_user`,`ID_Promotion`),
  ADD KEY `ID_Promotion` (`ID_Promotion`);
--
-- Index pour la table `Allowed`
--
ALTER TABLE `Allowed`
  ADD PRIMARY KEY (`ID_role`,`ID_utility`),
  ADD KEY `ID_utility` (`ID_utility`);

--
-- Index pour la table `Apply`
--
ALTER TABLE `Apply`
  ADD PRIMARY KEY (`ID_user`,`ID_offer`),
  ADD KEY `ID_offer` (`ID_offer`);

--
-- Index pour la table `Company`
--
ALTER TABLE `Company`
  ADD PRIMARY KEY (`ID_company`);

--
-- Index pour la table `Evaluate`
--
ALTER TABLE `Evaluate`
  ADD PRIMARY KEY (`ID_user`,`ID_company`),
  ADD KEY `ID_company` (`ID_company`);

--
-- Index pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  ADD PRIMARY KEY (`ID_offer`),
  ADD KEY `ID_company` (`ID_company`);
--
-- Index pour la table `Profil`
--
ALTER TABLE `Profil`
  ADD PRIMARY KEY (`ID_profil`);

--
-- Index pour la table `Remember_tokens`
--
ALTER TABLE `Remember_tokens`
  ADD PRIMARY KEY (`ID_token`),
  ADD KEY `ID_user` (`ID_user`);

--
-- Index pour la table `Role`
--
ALTER TABLE `Role`
  ADD PRIMARY KEY (`ID_role`);

--
-- Index pour la table `User_`
--
ALTER TABLE `User_`
  ADD PRIMARY KEY (`ID_user`),
  ADD KEY `ID_profil` (`ID_profil`),
  ADD KEY `ID_role` (`ID_role`);

ALTER TABLE `Promotion`
  ADD PRIMARY KEY (`ID_promotion`),
  ADD KEY `ID_pilote` (`ID_pilote`);
--
-- Index pour la table `Utility`
--
ALTER TABLE `Utility`
  ADD PRIMARY KEY (`ID_utility`);

--
-- Index pour la table `Wishlist`
--
ALTER TABLE `Wishlist`
  ADD PRIMARY KEY (`ID_user`,`ID_offer`),
  ADD KEY `ID_offer` (`ID_offer`);

--
-- AUTO_INCREMENT pour les tables déchargées
--
ALTER TABLE `Promotion`
  MODIFY `ID_promotion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `Company`
--
ALTER TABLE `Company`
  MODIFY `ID_company` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  MODIFY `ID_offer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `Profil`
--
ALTER TABLE `Profil`
  MODIFY `ID_profil` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `Remember_tokens`
--
ALTER TABLE `Remember_tokens`
  MODIFY `ID_token` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `Role`
--
ALTER TABLE `Role`
  MODIFY `ID_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `User_`
--
ALTER TABLE `User_`
  MODIFY `ID_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `Utility`
--
ALTER TABLE `Utility`
  MODIFY `ID_utility` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `Study`
  ADD CONSTRAINT `Study_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Study_ibfk_2` FOREIGN KEY (`ID_Promotion`) REFERENCES `Promotion` (`ID_promotion`);
--
-- Contraintes pour les tables déchargées
--
ALTER TABLE `Promotion`
  ADD CONSTRAINT `Promotion_ibfk_1` FOREIGN KEY (`ID_pilote`) REFERENCES `User_` (`ID_user`);
--
-- Contraintes pour la table `Allowed`
--
ALTER TABLE `Allowed`
  ADD CONSTRAINT `Allowed_ibfk_1` FOREIGN KEY (`ID_role`) REFERENCES `Role` (`ID_role`),
  ADD CONSTRAINT `Allowed_ibfk_2` FOREIGN KEY (`ID_utility`) REFERENCES `Utility` (`ID_utility`);

--
-- Contraintes pour la table `Apply`
--
ALTER TABLE `Apply`
  ADD CONSTRAINT `Apply_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Apply_ibfk_2` FOREIGN KEY (`ID_offer`) REFERENCES `Job_offer` (`ID_offer`);

--
-- Contraintes pour la table `Evaluate`
--
ALTER TABLE `Evaluate`
  ADD CONSTRAINT `Evaluate_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Evaluate_ibfk_2` FOREIGN KEY (`ID_company`) REFERENCES `Company` (`ID_company`);

--
-- Contraintes pour la table `Remember_tokens`
--
ALTER TABLE `Remember_tokens`
  ADD CONSTRAINT `Remember_tokens_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`);

--
-- Contraintes pour la table `User_`
--
ALTER TABLE `User_`
  ADD CONSTRAINT `User__ibfk_1` FOREIGN KEY (`ID_profil`) REFERENCES `Profil` (`ID_profil`),
  ADD CONSTRAINT `User__ibfk_2` FOREIGN KEY (`ID_role`) REFERENCES `Role` (`ID_role`);


--
-- Contraintes pour la table `Wishlist`
--
ALTER TABLE `Wishlist`
  ADD CONSTRAINT `Wishlist_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Wishlist_ibfk_2` FOREIGN KEY (`ID_offer`) REFERENCES `Job_offer` (`ID_offer`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;