-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3301
-- Généré le : dim. 02 juin 2024 à 20:36
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sportify`
--

-- --------------------------------------------------------

--
-- Structure de la table `activites`
--

DROP TABLE IF EXISTS `activites`;
CREATE TABLE IF NOT EXISTS `activites` (
  `id_activites` int NOT NULL,
  `nom_activites` varchar(50) NOT NULL,
  `type_activites` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_activites`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `activites`
--

INSERT INTO `activites` (`id_activites`, `nom_activites`, `type_activites`) VALUES
(1, 'musculation', 'activite_sportive'),
(2, 'fitness', 'activite_sportive'),
(3, 'biking', 'activite_sportive'),
(4, 'cardio-training', 'activite_sportive'),
(5, 'cours_collectifs', 'activite_sportive'),
(6, 'basketball', 'sport_de_competition'),
(7, 'football', 'sport_de_competition'),
(8, 'rugby', 'sport_de_competition'),
(9, 'tennis', 'sport_de_competition'),
(10, 'natation', 'sport_de_competition'),
(11, 'plongeon', 'sport_de_competition'),
(12, 'salle_de_sport_omnes', 'salle_de_sport_omnes');

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nom_admin` varchar(50) NOT NULL,
  `prenom_admin` varchar(50) NOT NULL,
  `sexe_admin` varchar(50) NOT NULL,
  `mdp_admin` varchar(50) NOT NULL,
  `email_admin` varchar(50) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_admin`, `nom_admin`, `prenom_admin`, `sexe_admin`, `mdp_admin`, `email_admin`) VALUES
(5, 'Nicolas', 'test', 'Homme', '123', 'test@test.fr'),
(4, 'Alfred', 'test', 'Homme', '123', 'test@test.fr');

-- --------------------------------------------------------

--
-- Structure de la table `ajouter_supprimer`
--

DROP TABLE IF EXISTS `ajouter_supprimer`;
CREATE TABLE IF NOT EXISTS `ajouter_supprimer` (
  `id_admin` int DEFAULT NULL,
  `id_coach` int DEFAULT NULL,
  KEY `id_admin` (`id_admin`),
  KEY `id_coach` (`id_coach`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bulletin`
--

DROP TABLE IF EXISTS `bulletin`;
CREATE TABLE IF NOT EXISTS `bulletin` (
  `id_bulletin` int NOT NULL AUTO_INCREMENT,
  `titre_bulletin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contenu_bulletin` text NOT NULL,
  PRIMARY KEY (`id_bulletin`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bulletin`
--

INSERT INTO `bulletin` (`id_bulletin`, `titre_bulletin`, `contenu_bulletin`) VALUES
(1, 'JO : La France sera t\'elle prête à temps', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(2, 'JO : La France sera t\'elle prête à temps', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(3, 'JO : La France sera t\'elle prête à temps', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(4, 'JO : La France sera t\'elle prête à temps', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `nom_client` varchar(50) NOT NULL,
  `prenom_client` varchar(50) NOT NULL,
  `sexe_client` varchar(50) NOT NULL,
  `date_de_naissance` date NOT NULL,
  `mdp_client` varchar(50) NOT NULL,
  `email_client` varchar(50) NOT NULL,
  `num_telephone` varchar(50) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `nom_carte` varchar(50) NOT NULL,
  `prenom_carte` varchar(50) NOT NULL,
  `adresse_ligne_1_carte` varchar(50) NOT NULL,
  `adresse_ligne_2_carte` varchar(50) NOT NULL,
  `ville_carte` varchar(50) NOT NULL,
  `code_postal_carte` varchar(50) NOT NULL,
  `pays_carte` varchar(50) NOT NULL,
  `carte_etudiant_client` varchar(50) NOT NULL,
  PRIMARY KEY (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `nom_client`, `prenom_client`, `sexe_client`, `date_de_naissance`, `mdp_client`, `email_client`, `num_telephone`, `profession`, `nom_carte`, `prenom_carte`, `adresse_ligne_1_carte`, `adresse_ligne_2_carte`, `ville_carte`, `code_postal_carte`, `pays_carte`, `carte_etudiant_client`) VALUES
(8, 'Client_2', 'test', 'homme', '0000-00-00', '123', 'test@test.fr', '', '', '', '', '', '', '', '', '', ''),
(7, 'Client_1', 'test', 'Homme', '2019-12-27', '123', 'test@test.test', '01 01 01 01 01', 'Etudiant', 'Client_1', 'test', 'test', 'test', 'test', 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Structure de la table `coach`
--

DROP TABLE IF EXISTS `coach`;
CREATE TABLE IF NOT EXISTS `coach` (
  `id_coach` int NOT NULL AUTO_INCREMENT,
  `nom_coach` varchar(50) NOT NULL,
  `prenom_coach` varchar(50) NOT NULL,
  `sexe_coach` varchar(50) NOT NULL,
  `mdp_coach` varchar(50) NOT NULL,
  `email_coach` varchar(50) NOT NULL,
  `cv_coach` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bureau_coach` varchar(50) NOT NULL,
  `photo_coach` varchar(50) NOT NULL,
  `specialite_coach` varchar(50) NOT NULL,
  `telephone_coach` varchar(50) NOT NULL,
  PRIMARY KEY (`id_coach`),
  KEY `fk_specialite_coach` (`specialite_coach`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `communiquer`
--

DROP TABLE IF EXISTS `communiquer`;
CREATE TABLE IF NOT EXISTS `communiquer` (
  `id_client` int DEFAULT NULL,
  `id_coach` int DEFAULT NULL,
  `type_communication` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `destinateur` varchar(50) DEFAULT NULL,
  `destinataire` varchar(50) DEFAULT NULL,
  `contenue` varchar(50) DEFAULT NULL,
  KEY `id_client` (`id_client`),
  KEY `id_coach` (`id_coach`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `disponibilite`
--

DROP TABLE IF EXISTS `disponibilite`;
CREATE TABLE IF NOT EXISTS `disponibilite` (
  `id_disponibilite` int NOT NULL AUTO_INCREMENT,
  `id_coach` int NOT NULL,
  `id_jour` int NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `type_activite` varchar(255) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id_disponibilite`),
  KEY `id_coach` (`id_coach`),
  KEY `id_jour` (`id_jour`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `effectuer`
--

DROP TABLE IF EXISTS `effectuer`;
CREATE TABLE IF NOT EXISTS `effectuer` (
  `id_paiement` int DEFAULT NULL,
  `id_client` int DEFAULT NULL,
  KEY `id_paiement` (`id_paiement`),
  KEY `id_client` (`id_client`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `horaire`
--

DROP TABLE IF EXISTS `horaire`;
CREATE TABLE IF NOT EXISTS `horaire` (
  `id_horaire` int NOT NULL AUTO_INCREMENT,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  PRIMARY KEY (`id_horaire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jour`
--

DROP TABLE IF EXISTS `jour`;
CREATE TABLE IF NOT EXISTS `jour` (
  `id_jour` int NOT NULL AUTO_INCREMENT,
  `nom_jour` varchar(50) NOT NULL,
  PRIMARY KEY (`id_jour`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `jour`
--

INSERT INTO `jour` (`id_jour`, `nom_jour`) VALUES
(1, 'Lundi'),
(2, 'Mardi'),
(3, 'Mercredi'),
(4, 'Jeudi'),
(5, 'Vendredi'),
(6, 'Samedi'),
(7, 'Dimanche');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `msg_id` int NOT NULL AUTO_INCREMENT,
  `incoming_msg_id` int NOT NULL,
  `outgoing_msg_id` int NOT NULL,
  `msg` varchar(1000) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`) VALUES
(1, 1529656807, 359701098, 'Bonjour Pierre Louis'),
(2, 359701098, 1529656807, 'Bonjour Jerry !'),
(3, 359701098, 1529656807, 'Comment ca va ?'),
(4, 1529656807, 359701098, 'ca va super et toi ?'),
(5, 359701098, 1529656807, 'oui ca va'),
(6, 359701098, 1529656807, 'b'),
(7, 359701098, 1529656807, 'b'),
(8, 359701098, 1529656807, 'b'),
(9, 359701098, 1529656807, 'b'),
(10, 359701098, 1529656807, 'b'),
(11, 359701098, 1529656807, 'b'),
(12, 1529656807, 104212775, 'hello pierre louis !'),
(13, 104212775, 1529656807, 'bonjour monsieur :)'),
(14, 359701098, 104212775, 'Bonjour jerry'),
(15, 104212775, 359701098, 'Bonjour monsieur l\'administrateur !'),
(17, 530454711, 359701098, 'bonjour monsieur le coach'),
(18, 359701098, 530454711, 'bonjour jerry, comment vas tu ?'),
(19, 530454711, 359701098, 'ca va super et vous ?'),
(20, 359701098, 530454711, 'très bien merci'),
(21, 359701098, 530454711, 'on va commencer par une séance pecs aujourd\'hui'),
(22, 104212775, 530454711, 'Bonjour monsieur l\'admin, j\'ai un problème avec un client qui ne veut pas payer'),
(23, 359701098, 530454711, 'après on va faire les triceps'),
(24, 530454711, 359701098, 'ok trop cool'),
(25, 1381345566, 1628309577, 'blablabla'),
(26, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Lundi à 08:00.'),
(27, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Lundi à 08:00.'),
(28, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Mercredi à 14:00.'),
(29, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Mercredi à 14:00.'),
(30, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Jeudi à 15:00.'),
(31, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Jeudi à 15:00.'),
(32, 359701098, 530454711, 'Votre rendez-vous avec moi prévu le Mercredi à 14:00 a été annulé.'),
(33, 359701098, 530454711, 'Votre rendez-vous avec moi prévu le Jeudi à 15:00 a été annulé.'),
(34, 359701098, 530454711, 'Votre rendez-vous avec moi prévu le Lundi à 11:00 a été annulé.'),
(35, 359701098, 530454711, 'Votre rendez-vous avec moi a bien été validé pour le Mardi à 09:00.'),
(36, 698810676, 926730153, 'Votre rendez-vous avec moi a bien été validé pour le Lundi à 08:00.'),
(37, 698810676, 926730153, 'Votre rendez-vous avec moi a bien été validé pour le Lundi à 09:00.'),
(38, 698810676, 926730153, 'Votre rendez-vous avec moi prévu le Lundi à 08:00 a été annulé.'),
(39, 926730153, 698810676, 'Bonjour, on se retrouve pour le rendez vous de lundi matin a 9h pour faire une séance pecs en balle'),
(40, 1292337670, 926730153, 'Votre rendez-vous avec moi a bien été validé pour le Lundi à 14:00.'),
(41, 926730153, 758944502, 'Bonjour, j\'ai annulé tout vos rendez, vous etes viré'),
(42, 758944502, 926730153, 'oh non ! pourquoi ?');

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `date_paiement` date NOT NULL,
  `facture` int NOT NULL,
  `type_carte` varchar(50) NOT NULL,
  `numero_carte` varchar(50) NOT NULL,
  `nom_carte` varchar(50) NOT NULL,
  `id_client` int DEFAULT NULL,
  `date_expiration` date NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id_paiement`),
  KEY `fk_id_client` (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prise_de_rendez_vous`
--

DROP TABLE IF EXISTS `prise_de_rendez_vous`;
CREATE TABLE IF NOT EXISTS `prise_de_rendez_vous` (
  `id_rdv` int NOT NULL AUTO_INCREMENT,
  `id_salle` int DEFAULT NULL,
  `id_coach` int DEFAULT NULL,
  `id_client` int DEFAULT NULL,
  `id_paiement` int DEFAULT NULL,
  `type_communication` varchar(50) DEFAULT NULL,
  `statut_rdv` tinyint(1) DEFAULT NULL,
  `jour_rdv` varchar(50) NOT NULL,
  `heure_rdv` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rdv`),
  KEY `id_salle` (`id_salle`),
  KEY `id_coach` (`id_coach`),
  KEY `id_client` (`id_client`),
  KEY `id_paiement` (`id_paiement`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `prise_de_rendez_vous`
--

INSERT INTO `prise_de_rendez_vous` (`id_rdv`, `id_salle`, `id_coach`, `id_client`, `id_paiement`, `type_communication`, `statut_rdv`, `jour_rdv`, `heure_rdv`) VALUES
(1, 3, 5, 1, NULL, NULL, 1, 'Mercredi', '11:00'),
(2, 1, 5, 2, NULL, NULL, 1, 'Vendredi', '08:00'),
(3, 4, 5, 2, NULL, NULL, 1, 'Mardi', '14:00'),
(4, 5, 4, 2, NULL, NULL, 1, 'Jeudi', '10:00'),
(5, 1, 5, 2, NULL, NULL, 1, 'Lundi', '10:00'),
(6, 4, 5, 7, NULL, NULL, 1, 'Mardi', '09:00'),
(7, 3, 9, 8, NULL, NULL, 1, 'Lundi', '08:00'),
(15, 2, 7, 6, NULL, NULL, 1, 'Mardi', '09:00');

-- --------------------------------------------------------

--
-- Structure de la table `salle`
--

DROP TABLE IF EXISTS `salle`;
CREATE TABLE IF NOT EXISTS `salle` (
  `id_salle` int NOT NULL AUTO_INCREMENT,
  `nom_salle` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id_salle`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `salle`
--

INSERT INTO `salle` (`id_salle`, `nom_salle`) VALUES
(1, 'Salle 1'),
(2, 'Salle 2'),
(3, 'Salle 3'),
(4, 'Salle 4'),
(5, 'Salle 5'),
(6, 'Salle 6'),
(7, 'Salle 7'),
(8, 'Salle 8'),
(9, 'Salle 9');

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `id_service` int NOT NULL AUTO_INCREMENT,
  `id_salle` int DEFAULT NULL,
  `informations_service` varchar(50) DEFAULT NULL,
  `regles_service` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_service`),
  KEY `id_salle` (`id_salle`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `unique_id` int NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`user_id`, `unique_id`, `fname`, `lname`, `email`, `password`, `img`, `status`) VALUES
(10, 698810676, 'client', 'Client_1', 'test@test.test', '123', 'image_coach/defaut.jpg', 'En ligne'),
(11, 758944502, 'admin', 'Alfred', 'test@test.fr', '123', 'image_coach/defaut.jpg', 'En ligne'),
(12, 149333304, 'admin', 'Nicolas', 'test@test.fr', '123', 'image_admin/defaut.jpg', 'En ligne'),
(18, 1292337670, 'client', 'Client_2', 'test@test.fr', '123', 'image_coach/defaut.jpg', 'En ligne');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
