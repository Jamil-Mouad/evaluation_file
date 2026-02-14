-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 08 août 2024 à 12:01
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `f_evaluation`
--

-- --------------------------------------------------------

--
-- Structure de la table `annee_scolaire`
--

CREATE TABLE `annee_scolaire` (
  `ID_annee` int(11) NOT NULL,
  `Description_annee` varchar(50) DEFAULT NULL,
  `ID_filiere` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annee_scolaire`
--

INSERT INTO `annee_scolaire` (`ID_annee`, `Description_annee`, `ID_filiere`) VALUES
(1, '1ère année DUT GI', 1),
(2, '2ème année DUT GI', 1),
(3, 'Licence professionnelle BIG DATA  GI', 1),
(4, '1ère année DUT GC', 2),
(5, '2ème année DUT GC', 2),
(6, 'Licence professionnelle GC', 2),
(7, '1ère année DUT IDS', 3),
(8, '2ème année DUT IDS', 3),
(9, 'Licence professionnelle IDS', 3),
(10, '1ère année DUT IA', 4),
(11, '2ème année DUT IA', 4),
(12, '1ère année licence professionnelle IA', 4),
(13, ' 2ème année licence professionnelle IA', 4);

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `Numero_D_apogee` int(11) NOT NULL,
  `Nom_etudiant` varchar(20) DEFAULT NULL,
  `Prenom_etudiant` varchar(20) DEFAULT NULL,
  `Date_Naissance` date DEFAULT NULL,
  `Email_etudiant` varchar(30) DEFAULT NULL,
  `Telephone_etudiant` varchar(15) DEFAULT NULL,
  `ID_filiere` int(11) DEFAULT NULL,
  `ID_annee` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`Numero_D_apogee`, `Nom_etudiant`, `Prenom_etudiant`, `Date_Naissance`, `Email_etudiant`, `Telephone_etudiant`, `ID_filiere`, `ID_annee`) VALUES
(1, 'Bennani', 'Amine', '2003-01-15', 'amine.bennani@gmail.com', '+212612345678', 1, 1),
(2, 'Elhajji', 'Sara', '2002-04-10', 'sara.elhajji@gmail.com', '+212612345679', 1, 1),
(3, 'Naciri', 'Mohamed', '2003-06-25', 'mohamed.naciri@gmail.com', '+212612345680', 1, 1),
(4, 'Fassi', 'Aya', '2004-03-30', 'aya.fassi@gmail.com', '+212612345681', 1, 1),
(5, 'Kabbaj', 'Youssef', '2003-02-15', 'youssef.kabbaj@gmail.com', '+212612345682', 1, 2),
(6, 'Rhani', 'Laila', '2002-05-18', 'laila.rhani@gmail.com', '+212612345683', 1, 2),
(7, 'Bennis', 'Omar', '2003-07-20', 'omar.bennis@gmail.com', '+212612345684', 1, 2),
(8, 'Farah', 'Nour', '2004-04-12', 'nour.farah@gmail.com', '+212612345685', 1, 2),
(9, 'Amrani', 'Yassine', '2003-03-25', 'yassine.amrani@gmail.com', '+212612345686', 1, 3),
(10, 'Bouzaid', 'Imane', '2002-06-15', 'imane.bouzaid@gmail.com', '+212612345687', 1, 3),
(11, 'Maarouf', 'Saad', '2003-08-22', 'saad.maarouf@gmail.com', '+212612345688', 1, 3),
(12, 'Chafik', 'Hajar', '2004-05-18', 'hajar.chafik@gmail.com', '+212612345689', 1, 3),
(13, 'El Idrissi', 'Anas', '2003-01-15', 'anas.elidrissi@gmail.com', '+212612345690', 2, 4),
(14, 'Benabid', 'Malak', '2002-04-10', 'malak.benabid@gmail.com', '+212612345691', 2, 4),
(15, 'Haddi', 'Ismail', '2003-06-25', 'ismail.haddi@gmail.com', '+212612345692', 2, 4),
(16, 'Lamrani', 'Asma', '2004-03-30', 'asma.lamrani@gmail.com', '+212612345693', 2, 4),
(17, 'Kamal', 'Reda', '2003-02-15', 'reda.kamal@gmail.com', '+212612345694', 2, 5),
(18, 'El Haouzi', 'Nada', '2002-05-18', 'nada.elhaouzi@gmail.com', '+212612345695', 2, 5),
(19, 'Lahlou', 'Adil', '2003-07-20', 'adil.lahlou@gmail.com', '+212612345696', 2, 5),
(20, 'Zahidi', 'Siham', '2004-04-12', 'siham.zahidi@gmail.com', '+212612345697', 2, 5),
(21, 'Ouali', 'Amine', '2003-03-25', 'amine.ouali@gmail.com', '+212612345698', 2, 6),
(22, 'El Fassi', 'Fatima', '2002-06-15', 'fatima.elfassi@gmail.com', '+212612345699', 2, 6),
(23, 'Loukili', 'Rachid', '2003-08-22', 'rachid.loukili@gmail.com', '+212612345700', 2, 6),
(24, 'Ibrahim', 'Yasmine', '2004-05-18', 'yasmine.ibrahim@gmail.com', '+212612345701', 2, 6),
(25, 'El Kadi', 'Younes', '2003-01-15', 'younes.elkadi@gmail.com', '+212612345702', 3, 7),
(26, 'Benjelloun', 'Hanae', '2002-04-10', 'hanae.benjelloun@gmail.com', '+212612345703', 3, 7),
(27, 'Maroufi', 'Hamza', '2003-06-25', 'hamza.maroufi@gmail.com', '+212612345704', 3, 7),
(28, 'Zoubir', 'Imane', '2004-03-30', 'imane.zoubir@gmail.com', '+212612345705', 3, 7),
(29, 'Kharbouch', 'Karim', '2003-02-15', 'karim.kharbouch@gmail.com', '+212612345706', 3, 8),
(30, 'Abbadi', 'Zineb', '2002-05-18', 'zineb.abbadi@gmail.com', '+212612345707', 3, 8),
(31, 'Zarrouk', 'Oussama', '2003-07-20', 'oussama.zarrouk@gmail.com', '+212612345708', 3, 8),
(32, 'El Hamdi', 'Leila', '2004-04-12', 'leila.elhamdi@gmail.com', '+212612345709', 3, 8),
(33, 'Fikri', 'Mehdi', '2003-03-25', 'mehdi.fikri@gmail.com', '+212612345710', 3, 9),
(34, 'Benaissa', 'Hajar', '2002-06-15', 'hajar.benaissa@gmail.com', '+212612345711', 3, 9),
(35, 'El Yacoubi', 'Nabil', '2003-08-22', 'nabil.elyacoubi@gmail.com', '+212612345712', 3, 9),
(36, 'Tazi', 'Nadia', '2004-05-18', 'nadia.tazi@gmail.com', '+212612345713', 3, 9),
(37, 'Lahlou', 'Samir', '2003-01-15', 'samir.lahlou@gmail.com', '+212612345714', 4, 10),
(38, 'Bouziane', 'Sanaa', '2002-04-10', 'sanaa.bouziane@gmail.com', '+212612345715', 4, 10),
(39, 'Sekkat', 'Imad', '2003-06-25', 'imad.sekkat@gmail.com', '+212612345716', 4, 10),
(40, 'Karkach', 'Fatima', '2004-03-30', 'fatima.karkach@gmail.com', '+212612345717', 4, 10),
(41, 'Ouazzani', 'Omar', '2003-02-15', 'omar.ouazzani@gmail.com', '+212612345718', 4, 11),
(42, 'Gharbi', 'Salma', '2002-05-18', 'salma.gharbi@gmail.com', '+212612345719', 4, 11),
(43, 'Tamek', 'Rachid', '2003-07-20', 'rachid.tamek@gmail.com', '+212612345720', 4, 11),
(44, 'Idrissi', 'Amina', '2004-04-12', 'amina.idrissi@gmail.com', '+212612345721', 4, 11),
(45, 'Mernissi', 'Yassine', '2003-03-25', 'yassine.mernissi@gmail.com', '+212612345722', 4, 12),
(46, 'Bekkali', 'Meryem', '2002-06-15', 'meryem.bekkali@gmail.com', '+212612345723', 4, 12),
(47, 'Raji', 'Anwar', '2003-08-22', 'anwar.raji@gmail.com', '+212612345724', 4, 12),
(48, 'Ait', 'Kenza', '2004-05-18', 'kenza.ait@gmail.com', '+212612345725', 4, 12),
(49, 'Bennani', 'Ahmed', '2002-05-20', 'ahmed.bennani@gmail.com', '+212612345678', 4, 13),
(50, 'El Idrissi', 'Fatima', '2002-08-15', 'fatima.elidrissi@gmail.com', '+212623456789', 4, 13),
(51, 'Alaoui', 'Mohamed', '2002-01-12', 'mohamed.alaoui@gmail.com', '+212634567890', 4, 13),
(52, 'Fassi', 'Zineb', '2002-10-05', 'zineb.fassi@gmail.com', '+212645678901', 4, 13);

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

CREATE TABLE `filiere` (
  `ID_filiere` int(11) NOT NULL,
  `Nom_filiere` varchar(50) DEFAULT NULL,
  `Abbreviation_filiere` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`ID_filiere`, `Nom_filiere`, `Abbreviation_filiere`) VALUES
(1, 'Génie Informatique', 'GI'),
(2, 'Génie Civil', 'GC'),
(3, 'Informatique Décisionnelle et Statistique', 'IDS'),
(4, 'Industriel Agroalimentaire', 'IA');

-- --------------------------------------------------------

--
-- Structure de la table `jury_soutenance`
--

CREATE TABLE `jury_soutenance` (
  `ID_jury` int(11) NOT NULL,
  `ID_soutenance` int(11) NOT NULL,
  `role_du_jury` varchar(75) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jury_soutenance`
--

INSERT INTO `jury_soutenance` (`ID_jury`, `ID_soutenance`, `role_du_jury`) VALUES
(8, 77, 'visiteur'),
(9, 77, 'president'),
(10, 77, 'noteur'),
(11, 77, 'ok');

-- --------------------------------------------------------

--
-- Structure de la table `membre_jury`
--

CREATE TABLE `membre_jury` (
  `ID_prof` int(11) NOT NULL,
  `CIN_prof` int(11) DEFAULT NULL,
  `Nom_prof` varchar(50) DEFAULT NULL,
  `Prenom_prof` varchar(50) DEFAULT NULL,
  `Email_prof` varchar(50) DEFAULT NULL,
  `Telephone_prof` varchar(15) DEFAULT NULL,
  `ID_filiere` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membre_jury`
--

INSERT INTO `membre_jury` (`ID_prof`, `CIN_prof`, `Nom_prof`, `Prenom_prof`, `Email_prof`, `Telephone_prof`, `ID_filiere`) VALUES
(1, 123456, 'El Azzouzi', 'Mohamed', 'elazzouzi.mohamed@gmail.com', '+212612345678', 1),
(2, 234567, 'Bennani', 'Fatima', 'bennani.fatima@gmail.com', '+212623456789', 1),
(3, 345678, 'El Idrissi', 'Kamal', 'elidrissi.kamal@gmail.com', '+212634567890', 1),
(4, 456789, 'Fassi', 'Yasmine', 'fassi.yasmine@gmail.com', '+212645678901', 1),
(5, 567890, 'Mouline', 'Reda', 'mouline.reda@gmail.com', '+212656789012', 1),
(6, 678901, 'Tazi', 'Omar', 'tazi.omar@gmail.com', '+212667890123', 1),
(7, 789012, 'Alami', 'Nadia', 'alami.nadia@gmail.com', '+212678901234', 2),
(8, 890123, 'Bouazza', 'Said', 'bouazza.said@gmail.com', '+212689012345', 2),
(9, 901234, 'Chafik', 'Hanan', 'chafik.hanan@gmail.com', '+212690123456', 2),
(10, 123457, 'El Khalfi', 'Youssef', 'elkhalfi.youssef@gmail.com', '+212601234567', 2),
(11, 234568, 'Jabri', 'Salma', 'jabri.salma@gmail.com', '+212612345678', 2),
(12, 345679, 'Kabbaj', 'Hamza', 'kabbaj.hamza@gmail.com', '+212623456789', 2),
(13, 456790, 'Lahlou', 'Karim', 'lahlou.karim@gmail.com', '+212634567890', 3),
(14, 567801, 'Mouhoub', 'Amina', 'mouhoub.amina@gmail.com', '+212645678901', 3),
(15, 678912, 'Naim', 'Rachid', 'naim.rachid@gmail.com', '+212656789012', 3),
(16, 789023, 'Ouazzani', 'Sara', 'ouazzani.sara@gmail.com', '+212667890123', 3),
(17, 890134, 'Rifai', 'Hicham', 'rifai.hicham@gmail.com', '+212678901234', 3),
(18, 901245, 'Sahli', 'Leila', 'sahli.leila@gmail.com', '+212689012345', 3),
(19, 123458, 'Tazi', 'Ahmed', 'tazi.ahmed@gmail.com', '+212690123456', 4),
(20, 234569, 'Yousfi', 'Nour', 'yousfi.nour@gmail.com', '+212601234567', 4),
(21, 345680, 'Zahidi', 'Samir', 'zahidi.samir@gmail.com', '+212612345678', 4),
(22, 456791, 'Amrani', 'Hiba', 'amrani.hiba@gmail.com', '+212623456789', 4),
(23, 567802, 'Bakkali', 'Meryem', 'bakkali.meryem@gmail.com', '+212634567890', 4),
(24, 678913, 'Chraibi', 'Anouar', 'chraibi.anouar@gmail.com', '+212645678901', 4);

-- --------------------------------------------------------

--
-- Structure de la table `pourcentage`
--

CREATE TABLE `pourcentage` (
  `ID_filiere` int(11) NOT NULL,
  `Pourcentage_rapport` float DEFAULT NULL,
  `Pourcentage_presentation_orale` float DEFAULT NULL,
  `Pourcentage_encadrant` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pourcentage`
--

INSERT INTO `pourcentage` (`ID_filiere`, `Pourcentage_rapport`, `Pourcentage_presentation_orale`, `Pourcentage_encadrant`) VALUES
(1, 0.4, 0.3, 0.3),
(2, 0.35, 0.35, 0.3),
(3, 0.4, 0.4, 0.2),
(4, 0.4, 0.35, 0.25);

-- --------------------------------------------------------

--
-- Structure de la table `rapport`
--

CREATE TABLE `rapport` (
  `ID_Rapport` int(11) NOT NULL,
  `Numero_D_apogee` int(11) DEFAULT NULL,
  `ID_stage` int(11) DEFAULT NULL,
  `Titre_Rapport` varchar(100) DEFAULT NULL,
  `Nom_Prenom_encadrent` varchar(60) DEFAULT NULL,
  `Note_rapport` float DEFAULT 0,
  `Note_presentation_orale` float DEFAULT 0,
  `Note_encadrant` float DEFAULT 0,
  `Note_finale` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rapport`
--

INSERT INTO `rapport` (`ID_Rapport`, `Numero_D_apogee`, `ID_stage`, `Titre_Rapport`, `Nom_Prenom_encadrent`, `Note_rapport`, `Note_presentation_orale`, `Note_encadrant`, `Note_finale`) VALUES
(77, 14, 1, 'application de gestion des ressources', ' Rachid Mouassim', 18, 16, 18, 17.3);

-- --------------------------------------------------------

--
-- Structure de la table `soutenance`
--

CREATE TABLE `soutenance` (
  `Numero_soutenance` int(11) NOT NULL,
  `Date_soutenance` date DEFAULT NULL,
  `Heure_soutenance` time DEFAULT NULL,
  `Lieu_soutenance` varchar(100) DEFAULT NULL,
  `ID_stage` int(11) DEFAULT NULL,
  `Numero_D_apogee` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `soutenance`
--

INSERT INTO `soutenance` (`Numero_soutenance`, `Date_soutenance`, `Heure_soutenance`, `Lieu_soutenance`, `ID_stage`, `Numero_D_apogee`) VALUES
(77, '2024-08-08', '10:52:00', 'est-fbs-salle-1', 1, 14);

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE `stage` (
  `ID_stage` int(11) NOT NULL,
  `Type_stage` varchar(60) DEFAULT NULL,
  `Abbreviation_stage` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stage`
--

INSERT INTO `stage` (`ID_stage`, `Type_stage`, `Abbreviation_stage`) VALUES
(1, 'Stage d\'Initiation', 'SI'),
(2, 'Stage Technique', 'ST'),
(3, 'Projet de Fin d\'Etude', 'PFE'),
(4, 'Stage Professionnel', 'SP'),
(5, 'Projet de Fin d\'Etude Professionnel', 'PFEP');

-- --------------------------------------------------------

--
-- Structure de la table `stage_par_annee_scolaire`
--

CREATE TABLE `stage_par_annee_scolaire` (
  `ID_annee` int(11) NOT NULL,
  `ID_stage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stage_par_annee_scolaire`
--

INSERT INTO `stage_par_annee_scolaire` (`ID_annee`, `ID_stage`) VALUES
(1, 1),
(2, 2),
(2, 3),
(3, 4),
(3, 5),
(4, 1),
(5, 2),
(5, 3),
(6, 4),
(6, 5),
(7, 1),
(8, 2),
(8, 3),
(9, 4),
(9, 5),
(10, 1),
(11, 2),
(11, 3),
(12, 4),
(12, 5),
(13, 4),
(13, 5);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annee_scolaire`
--
ALTER TABLE `annee_scolaire`
  ADD PRIMARY KEY (`ID_annee`),
  ADD KEY `ID_filiere` (`ID_filiere`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`Numero_D_apogee`),
  ADD KEY `ID_filiere` (`ID_filiere`),
  ADD KEY `ID_annee` (`ID_annee`);

--
-- Index pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`ID_filiere`);

--
-- Index pour la table `jury_soutenance`
--
ALTER TABLE `jury_soutenance`
  ADD PRIMARY KEY (`ID_jury`,`ID_soutenance`),
  ADD KEY `ID_soutenance` (`ID_soutenance`);

--
-- Index pour la table `membre_jury`
--
ALTER TABLE `membre_jury`
  ADD PRIMARY KEY (`ID_prof`),
  ADD KEY `ID_filiere` (`ID_filiere`);

--
-- Index pour la table `pourcentage`
--
ALTER TABLE `pourcentage`
  ADD PRIMARY KEY (`ID_filiere`);

--
-- Index pour la table `rapport`
--
ALTER TABLE `rapport`
  ADD PRIMARY KEY (`ID_Rapport`),
  ADD KEY `Numero_D_apogee` (`Numero_D_apogee`),
  ADD KEY `ID_stage` (`ID_stage`);

--
-- Index pour la table `soutenance`
--
ALTER TABLE `soutenance`
  ADD PRIMARY KEY (`Numero_soutenance`),
  ADD KEY `ID_stage` (`ID_stage`),
  ADD KEY `Numero_D_apogee` (`Numero_D_apogee`);

--
-- Index pour la table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`ID_stage`);

--
-- Index pour la table `stage_par_annee_scolaire`
--
ALTER TABLE `stage_par_annee_scolaire`
  ADD PRIMARY KEY (`ID_annee`,`ID_stage`),
  ADD KEY `ID_stage` (`ID_stage`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `rapport`
--
ALTER TABLE `rapport`
  MODIFY `ID_Rapport` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT pour la table `soutenance`
--
ALTER TABLE `soutenance`
  MODIFY `Numero_soutenance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annee_scolaire`
--
ALTER TABLE `annee_scolaire`
  ADD CONSTRAINT `annee_scolaire_ibfk_1` FOREIGN KEY (`ID_filiere`) REFERENCES `filiere` (`ID_filiere`);

--
-- Contraintes pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD CONSTRAINT `etudiant_ibfk_1` FOREIGN KEY (`ID_filiere`) REFERENCES `filiere` (`ID_filiere`),
  ADD CONSTRAINT `etudiant_ibfk_2` FOREIGN KEY (`ID_annee`) REFERENCES `annee_scolaire` (`ID_annee`);

--
-- Contraintes pour la table `jury_soutenance`
--
ALTER TABLE `jury_soutenance`
  ADD CONSTRAINT `jury_soutenance_ibfk_1` FOREIGN KEY (`ID_jury`) REFERENCES `membre_jury` (`ID_prof`),
  ADD CONSTRAINT `jury_soutenance_ibfk_2` FOREIGN KEY (`ID_soutenance`) REFERENCES `soutenance` (`Numero_soutenance`);

--
-- Contraintes pour la table `membre_jury`
--
ALTER TABLE `membre_jury`
  ADD CONSTRAINT `membre_jury_ibfk_1` FOREIGN KEY (`ID_filiere`) REFERENCES `filiere` (`ID_filiere`);

--
-- Contraintes pour la table `pourcentage`
--
ALTER TABLE `pourcentage`
  ADD CONSTRAINT `pourcentage_ibfk_1` FOREIGN KEY (`ID_filiere`) REFERENCES `filiere` (`ID_filiere`);

--
-- Contraintes pour la table `rapport`
--
ALTER TABLE `rapport`
  ADD CONSTRAINT `rapport_ibfk_1` FOREIGN KEY (`Numero_D_apogee`) REFERENCES `etudiant` (`Numero_D_apogee`),
  ADD CONSTRAINT `rapport_ibfk_2` FOREIGN KEY (`ID_stage`) REFERENCES `stage` (`ID_stage`);

--
-- Contraintes pour la table `soutenance`
--
ALTER TABLE `soutenance`
  ADD CONSTRAINT `soutenance_ibfk_1` FOREIGN KEY (`ID_stage`) REFERENCES `stage` (`ID_stage`),
  ADD CONSTRAINT `soutenance_ibfk_2` FOREIGN KEY (`Numero_D_apogee`) REFERENCES `etudiant` (`Numero_D_apogee`);

--
-- Contraintes pour la table `stage_par_annee_scolaire`
--
ALTER TABLE `stage_par_annee_scolaire`
  ADD CONSTRAINT `stage_par_annee_scolaire_ibfk_1` FOREIGN KEY (`ID_annee`) REFERENCES `annee_scolaire` (`ID_annee`),
  ADD CONSTRAINT `stage_par_annee_scolaire_ibfk_2` FOREIGN KEY (`ID_stage`) REFERENCES `stage` (`ID_stage`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
