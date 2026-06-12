-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 01 juin 2026 à 13:57
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
-- Base de données : `cuf_dataforest`
--

-- --------------------------------------------------------

--
-- Structure de la table `fiche_abattage`
--

CREATE TABLE `fiche_abattage` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `nom_abatteur` varchar(100) DEFAULT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `nom_aide_abatteur` varchar(100) DEFAULT NULL,
  `uc` varchar(50) DEFAULT NULL,
  `p1_num_code_barre` varchar(50) DEFAULT NULL,
  `p1_num_df10` varchar(50) DEFAULT NULL,
  `p1_num_ligne` varchar(50) DEFAULT NULL,
  `p1_essence` varchar(50) DEFAULT NULL,
  `p1_c1_piste_fuite_direction` varchar(5) DEFAULT NULL,
  `p1_c1_nettoyage` varchar(5) DEFAULT NULL,
  `p1_c1_longueur_piste` varchar(5) DEFAULT NULL,
  `p1_c1_largeur_piste` varchar(5) DEFAULT NULL,
  `p1_c2_egobelage` varchar(5) DEFAULT NULL,
  `p1_c3_hauteur_souche` varchar(5) DEFAULT NULL,
  `p1_c4_entaille_1er_trait` varchar(5) DEFAULT NULL,
  `p1_c4_entaille_2eme_trait` varchar(5) DEFAULT NULL,
  `p1_c4_02_traits` varchar(5) DEFAULT NULL,
  `p1_c4_semelle` varchar(5) DEFAULT NULL,
  `p1_c5_charniere_longue` varchar(5) DEFAULT NULL,
  `p1_c5_largeur_charniere` varchar(5) DEFAULT NULL,
  `p1_c5_epaulement` varchar(5) DEFAULT NULL,
  `p1_c6_coupe_abattage` varchar(5) DEFAULT NULL,
  `p1_c7_patte_retenue` varchar(5) DEFAULT NULL,
  `p1_c7_taille_patte` varchar(5) DEFAULT NULL,
  `p1_c8_aubiers` varchar(5) DEFAULT NULL,
  `p1_c9_direction_chute` varchar(5) DEFAULT NULL,
  `p1_c10_tronconnage` varchar(5) DEFAULT NULL,
  `p1_c10_etelage` varchar(5) DEFAULT NULL,
  `p1_c10_ecuage` varchar(5) DEFAULT NULL,
  `p1_c11_marquage_souche` varchar(5) DEFAULT NULL,
  `p1_c11_defaut_apparent` varchar(5) DEFAULT NULL,
  `p1_total` decimal(5,2) DEFAULT NULL,
  `p2_num_code_barre` varchar(50) DEFAULT NULL,
  `p2_num_df10` varchar(50) DEFAULT NULL,
  `p2_essence` varchar(50) DEFAULT NULL,
  `p2_total` decimal(5,2) DEFAULT NULL,
  `p3_num_code_barre` varchar(50) DEFAULT NULL,
  `p3_num_df10` varchar(50) DEFAULT NULL,
  `p3_essence` varchar(50) DEFAULT NULL,
  `p3_total` decimal(5,2) DEFAULT NULL,
  `p4_num_code_barre` varchar(50) DEFAULT NULL,
  `p4_num_df10` varchar(50) DEFAULT NULL,
  `p4_essence` varchar(50) DEFAULT NULL,
  `p4_total` decimal(5,2) DEFAULT NULL,
  `p5_num_code_barre` varchar(50) DEFAULT NULL,
  `p5_num_df10` varchar(50) DEFAULT NULL,
  `p5_essence` varchar(50) DEFAULT NULL,
  `p5_total` decimal(5,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `appreciation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_abattage`
--

INSERT INTO `fiche_abattage` (`id`, `rapport_id`, `nom_controleur`, `nom_abatteur`, `titre_forestier`, `aac`, `date_controle`, `nom_aide_abatteur`, `uc`, `p1_num_code_barre`, `p1_num_df10`, `p1_num_ligne`, `p1_essence`, `p1_c1_piste_fuite_direction`, `p1_c1_nettoyage`, `p1_c1_longueur_piste`, `p1_c1_largeur_piste`, `p1_c2_egobelage`, `p1_c3_hauteur_souche`, `p1_c4_entaille_1er_trait`, `p1_c4_entaille_2eme_trait`, `p1_c4_02_traits`, `p1_c4_semelle`, `p1_c5_charniere_longue`, `p1_c5_largeur_charniere`, `p1_c5_epaulement`, `p1_c6_coupe_abattage`, `p1_c7_patte_retenue`, `p1_c7_taille_patte`, `p1_c8_aubiers`, `p1_c9_direction_chute`, `p1_c10_tronconnage`, `p1_c10_etelage`, `p1_c10_ecuage`, `p1_c11_marquage_souche`, `p1_c11_defaut_apparent`, `p1_total`, `p2_num_code_barre`, `p2_num_df10`, `p2_essence`, `p2_total`, `p3_num_code_barre`, `p3_num_df10`, `p3_essence`, `p3_total`, `p4_num_code_barre`, `p4_num_df10`, `p4_essence`, `p4_total`, `p5_num_code_barre`, `p5_num_df10`, `p5_essence`, `p5_total`, `observations`, `appreciation`) VALUES
(1, 1, 'reine FOKO', '', '09027', '', '2026-05-29', '', '', '45', '657', '456', '657', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '1', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', ''),
(2, 2, 'reine FOKO', '', '09027', '', '2026-05-31', '', '', '23', '23', '45', '34', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', 11.00, '', '', '', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', ''),
(3, 3, 'reine FOKO', '', '09027', '', '2026-05-31', '', '', '456', '56', '45', '56', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', 14.00, '', '', '', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', '', '', 0.00, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_base_mecanique`
--

CREATE TABLE `fiche_base_mecanique` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `point_retention_fonctionnel` varchar(5) DEFAULT NULL,
  `diversements_accidentels` varchar(5) DEFAULT NULL,
  `equip_securite_disponible` varchar(5) DEFAULT NULL,
  `equip_conforme` varchar(5) DEFAULT NULL,
  `equip_signale` varchar(5) DEFAULT NULL,
  `equip_accessible` varchar(5) DEFAULT NULL,
  `equip_visite` varchar(5) DEFAULT NULL,
  `consignes_securite` varchar(5) DEFAULT NULL,
  `cuve_volume` varchar(20) DEFAULT NULL,
  `cuve_contenu` varchar(50) DEFAULT NULL,
  `cuve_nom_fabricant` varchar(100) DEFAULT NULL,
  `cuve_homologuee` varchar(5) DEFAULT NULL,
  `cuve_toiture` varchar(5) DEFAULT NULL,
  `aire_depotage_operationnelle` varchar(5) DEFAULT NULL,
  `local_regles_conformes` varchar(5) DEFAULT NULL,
  `local_autres` varchar(5) DEFAULT NULL,
  `vigiles_json` text DEFAULT NULL,
  `details_json` text DEFAULT NULL,
  `observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_base_mecanique`
--

INSERT INTO `fiche_base_mecanique` (`id`, `rapport_id`, `titre_forestier`, `aac`, `longitude`, `latitude`, `nom_controleur`, `date_controle`, `point_retention_fonctionnel`, `diversements_accidentels`, `equip_securite_disponible`, `equip_conforme`, `equip_signale`, `equip_accessible`, `equip_visite`, `consignes_securite`, `cuve_volume`, `cuve_contenu`, `cuve_nom_fabricant`, `cuve_homologuee`, `cuve_toiture`, `aire_depotage_operationnelle`, `local_regles_conformes`, `local_autres`, `vigiles_json`, `details_json`, `observations`) VALUES
(1, 1, '09027', '', '', '', 'reine FOKO', '2026-05-29', 'Oui', '', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', '45', 'gas', 'DONGMO', 'Oui', 'Oui', 'Oui', '', '', 'DONGMO\r\nFOKO', '{\"s3_disponible\":\"Oui\",\"s3_propre\":\"Oui\",\"s3_credit_communication\":\"Oui\",\"s3_produits_hygiene\":\"Oui\",\"s3_agregats_construction\":\"Oui\",\"s3_presence_vigile\":\"Oui\",\"s3_nb_vigiles\":\"5\",\"s4_eau_potable\":\"Oui\",\"s4_cuisine\":\"Oui\",\"s4_absence_gibier\":\"Oui\",\"s4_barbecue\":\"Oui\",\"s4_langue_disponible\":\"Oui\",\"s5_propre\":\"Oui\",\"s5_fermeture\":\"Oui\",\"s5_piez_detachees\":\"Oui\",\"s5_tronconneuses\":\"Oui\",\"s5_guides_chaines\":\"Oui\",\"s5_jerricans\":\"Oui\",\"s5_autres_materiel\":\"Oui\",\"s6_fiche_epi\":\"Oui\",\"s6_stock_epi\":\"Oui\",\"s6_consommations\":\"Oui\",\"s6_approvisionnement\":\"Oui\",\"s6_bien_entretenu\":\"Oui\",\"s6_cables_proteges\":\"Oui\",\"s6_bac_filtres\":\"Oui\",\"s6_recuperation\":\"Oui\"}', ''),
(2, 2, '09027', '', '', '', 'reine FOKO', '2026-05-31', 'Oui', '', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', '45', 'GAS', 'DONGMO', 'Oui', 'Oui', 'Oui', '', '', 'FOKO\r\nNGEUPI\r\nDONFACK', '{\"s3_disponible\":\"Oui\",\"s3_propre\":\"Oui\",\"s3_credit_communication\":\"Oui\",\"s3_produits_hygiene\":\"Oui\",\"s3_agregats_construction\":\"Oui\",\"s3_presence_vigile\":\"Oui\",\"s3_nb_vigiles\":\"5\",\"s4_eau_potable\":\"Oui\",\"s4_cuisine\":\"Oui\",\"s4_absence_gibier\":\"Non\",\"s4_barbecue\":\"Non\",\"s4_langue_disponible\":\"Non\",\"s5_propre\":\"Non\",\"s5_fermeture\":\"Oui\",\"s5_piez_detachees\":\"Oui\",\"s5_tronconneuses\":\"Oui\",\"s5_guides_chaines\":\"Oui\",\"s5_jerricans\":\"Oui\",\"s5_autres_materiel\":\"Oui\",\"s6_fiche_epi\":\"Oui\",\"s6_stock_epi\":\"Oui\",\"s6_consommations\":\"Oui\",\"s6_approvisionnement\":\"Oui\",\"s6_bien_entretenu\":\"Oui\",\"s6_cables_proteges\":\"Oui\",\"s6_bac_filtres\":\"Oui\",\"s6_recuperation\":\"Oui\"}', ''),
(3, 3, '09027', '', '', '', 'reine FOKO', '2026-05-31', 'Oui', '', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', '54', 'gas', 'DONGMO', 'Oui', 'Oui', 'Oui', '', '', 'FOKO\r\nNGUEPI\r\nDONFACK', '{\"s3_disponible\":\"Oui\",\"s3_propre\":\"Non\",\"s3_credit_communication\":\"Non\",\"s3_produits_hygiene\":\"Non\",\"s3_agregats_construction\":\"Non\",\"s3_presence_vigile\":\"Oui\",\"s3_nb_vigiles\":\"5\",\"s4_eau_potable\":\"Oui\",\"s4_cuisine\":\"Non\",\"s4_absence_gibier\":\"Non\",\"s4_barbecue\":\"Oui\",\"s4_langue_disponible\":\"Non\",\"s5_propre\":\"Oui\",\"s5_fermeture\":\"Oui\",\"s5_piez_detachees\":\"Oui\",\"s5_tronconneuses\":\"Non\",\"s5_guides_chaines\":\"Non\",\"s5_jerricans\":\"Oui\",\"s5_autres_materiel\":\"Oui\",\"s6_fiche_epi\":\"Oui\",\"s6_stock_epi\":\"Oui\",\"s6_consommations\":\"Oui\",\"s6_approvisionnement\":\"Oui\",\"s6_bien_entretenu\":\"Non\",\"s6_cables_proteges\":\"Non\",\"s6_bac_filtres\":\"Oui\",\"s6_recuperation\":\"Oui\"}', '');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_debardage`
--

CREATE TABLE `fiche_debardage` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `uc` varchar(50) DEFAULT NULL,
  `pistes_json` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `appreciation` text DEFAULT NULL,
  `c1` varchar(5) DEFAULT NULL,
  `c2` varchar(5) DEFAULT NULL,
  `c3` varchar(5) DEFAULT NULL,
  `c4` varchar(5) DEFAULT NULL,
  `c5` varchar(5) DEFAULT NULL,
  `c6` varchar(5) DEFAULT NULL,
  `c7` varchar(5) DEFAULT NULL,
  `c8` varchar(5) DEFAULT NULL,
  `c9` varchar(5) DEFAULT NULL,
  `c10` varchar(5) DEFAULT NULL,
  `total_points` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_debardage`
--

INSERT INTO `fiche_debardage` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `uc`, `pistes_json`, `observations`, `appreciation`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, `total_points`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', '', NULL, '', '', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 0.00),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', '', NULL, '', '', '1', '1', '1', '1', '1', '0', '0', '0', '1', '1', 7.00),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', '', NULL, '', '', '1', '1', '1', '0', '0', '1', '1', '1', '1', '1', 8.00);

-- --------------------------------------------------------

--
-- Structure de la table `fiche_dechets_foret`
--

CREATE TABLE `fiche_dechets_foret` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `bac_nettoyage` varchar(5) DEFAULT NULL,
  `decanteur` varchar(5) DEFAULT NULL,
  `poubelle_non_biodeg` varchar(5) DEFAULT NULL,
  `huiles_usees` varchar(5) DEFAULT NULL,
  `filtres` varchar(5) DEFAULT NULL,
  `batteries` varchar(5) DEFAULT NULL,
  `cables_debardage` varchar(5) DEFAULT NULL,
  `absence_huiles` varchar(5) DEFAULT NULL,
  `absence_plastiques` varchar(5) DEFAULT NULL,
  `transfert_dechets` varchar(5) DEFAULT NULL,
  `transfert_contenants` varchar(5) DEFAULT NULL,
  `sensibilisation` varchar(5) DEFAULT NULL,
  `consignes_respectees` varchar(5) DEFAULT NULL,
  `total_points` decimal(5,2) DEFAULT NULL,
  `observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_dechets_foret`
--

INSERT INTO `fiche_dechets_foret` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `bac_nettoyage`, `decanteur`, `poubelle_non_biodeg`, `huiles_usees`, `filtres`, `batteries`, `cables_debardage`, `absence_huiles`, `absence_plastiques`, `transfert_dechets`, `transfert_contenants`, `sensibilisation`, `consignes_respectees`, `total_points`, `observations`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 0.00, ''),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 20.00, ''),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '0', 13.00, '');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_parc_foret`
--

CREATE TABLE `fiche_parc_foret` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `reference_parc_foret` varchar(100) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `superficie_parc` decimal(10,2) DEFAULT NULL,
  `nombre_pieds_debardés` int(11) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `c1_installation` varchar(5) DEFAULT NULL,
  `c2_panneau_matricule` varchar(5) DEFAULT NULL,
  `c3_pente_douce` varchar(5) DEFAULT NULL,
  `c4_distance_nappe` varchar(5) DEFAULT NULL,
  `c5_tiges_avenir` varchar(5) DEFAULT NULL,
  `c6_marquage_grumes` varchar(5) DEFAULT NULL,
  `c7_couche_debris` varchar(5) DEFAULT NULL,
  `c8_culées_coin` varchar(5) DEFAULT NULL,
  `c9_coursons` varchar(5) DEFAULT NULL,
  `c10_marques_ab` varchar(5) DEFAULT NULL,
  `total_points` decimal(5,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `appreciation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_parc_foret`
--

INSERT INTO `fiche_parc_foret` (`id`, `rapport_id`, `reference_parc_foret`, `longitude`, `latitude`, `superficie_parc`, `nombre_pieds_debardés`, `nom_controleur`, `date_controle`, `c1_installation`, `c2_panneau_matricule`, `c3_pente_douce`, `c4_distance_nappe`, `c5_tiges_avenir`, `c6_marquage_grumes`, `c7_couche_debris`, `c8_culées_coin`, `c9_coursons`, `c10_marques_ab`, `total_points`, `observations`, `appreciation`) VALUES
(1, 1, '09027', '', '', 0.00, 0, 'reine FOKO', '2026-05-29', '1', '1', '1', '1', '1', '0', '0', '0', '1', '1', 0.00, '', ''),
(2, 2, '09027', '', '', 0.00, 0, 'reine FOKO', '2026-05-31', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 10.00, '', ''),
(3, 3, '09027', '', '', 0.00, 0, 'reine FOKO', '2026-05-31', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', 8.00, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_pont_forestier`
--

CREATE TABLE `fiche_pont_forestier` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `uc` varchar(50) DEFAULT NULL,
  `ponts_json` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `appreciation` text DEFAULT NULL,
  `c1` varchar(5) DEFAULT NULL,
  `c2` varchar(5) DEFAULT NULL,
  `c3` varchar(5) DEFAULT NULL,
  `c4` varchar(5) DEFAULT NULL,
  `c5` varchar(5) DEFAULT NULL,
  `c6` varchar(5) DEFAULT NULL,
  `c7` varchar(5) DEFAULT NULL,
  `c8` varchar(5) DEFAULT NULL,
  `c9` varchar(5) DEFAULT NULL,
  `c10` varchar(5) DEFAULT NULL,
  `total_points` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_pont_forestier`
--

INSERT INTO `fiche_pont_forestier` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `uc`, `ponts_json`, `observations`, `appreciation`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, `total_points`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', '', '{\"reference_ouvrage\":\"345\",\"pont_forestier\":\"45\",\"longitude\":\"4545\",\"latitude\":\"45\",\"largeur_pont\":\"45\",\"longueur_pont\":\"45\"}', '', '', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 0.00),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', '', '{\"reference_ouvrage\":\"454\",\"pont_forestier\":\"32\",\"longitude\":\"213\",\"latitude\":\"546\",\"largeur_pont\":\"12\",\"longueur_pont\":\"33.96\"}', '', '', '1', '1', '1', '1', '0', '0', '1', '1', '0', '0', 6.00),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', '', '{\"reference_ouvrage\":\"56\",\"pont_forestier\":\"76\",\"longitude\":\"34\",\"latitude\":\"34\",\"largeur_pont\":\"23\",\"longueur_pont\":\"32\"}', '', '', '1', '1', '1', '1', '0', '0', '1', '1', '1', '1', 8.00);

-- --------------------------------------------------------

--
-- Structure de la table `fiche_post_exploitation`
--

CREATE TABLE `fiche_post_exploitation` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `base_mec_demontee` varchar(5) DEFAULT NULL,
  `base_mec_entretenue` varchar(5) DEFAULT NULL,
  `mart_souches_100pct` varchar(5) DEFAULT NULL,
  `mart_souches_carte` varchar(5) DEFAULT NULL,
  `mart_houppiers` varchar(5) DEFAULT NULL,
  `empiete_ufa` varchar(5) DEFAULT NULL,
  `empiete_aac` varchar(5) DEFAULT NULL,
  `empiete_zones_prot` varchar(5) DEFAULT NULL,
  `empiete_zones_interet` varchar(5) DEFAULT NULL,
  `parcs_superficie` varchar(5) DEFAULT NULL,
  `dechets_geres` varchar(5) DEFAULT NULL,
  `culees_non_marteles` varchar(5) DEFAULT NULL,
  `grumes_abandonnees` varchar(5) DEFAULT NULL,
  `restauration_parcs` varchar(5) DEFAULT NULL,
  `parcs_cartographies` varchar(5) DEFAULT NULL,
  `remise_etat_lit` varchar(5) DEFAULT NULL,
  `ouvrages_demantelés` varchar(5) DEFAULT NULL,
  `ouvrages_cartographies` varchar(5) DEFAULT NULL,
  `fermeture_secondaire` varchar(5) DEFAULT NULL,
  `fermeture_principale` varchar(5) DEFAULT NULL,
  `barriere` varchar(5) DEFAULT NULL,
  `pistes_conformes` varchar(5) DEFAULT NULL,
  `routes_cartographiees` varchar(5) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `cotation` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_post_exploitation`
--

INSERT INTO `fiche_post_exploitation` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `base_mec_demontee`, `base_mec_entretenue`, `mart_souches_100pct`, `mart_souches_carte`, `mart_houppiers`, `empiete_ufa`, `empiete_aac`, `empiete_zones_prot`, `empiete_zones_interet`, `parcs_superficie`, `dechets_geres`, `culees_non_marteles`, `grumes_abandonnees`, `restauration_parcs`, `parcs_cartographies`, `remise_etat_lit`, `ouvrages_demantelés`, `ouvrages_cartographies`, `fermeture_secondaire`, `fermeture_principale`, `barriere`, `pistes_conformes`, `routes_cartographiees`, `observations`, `cotation`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', '', ''),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', '', '80-100'),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', 'Oui', 'Non', 'Oui', 'Non', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Non', 'Non', 'Oui', 'Oui', 'Non', 'Oui', 'Oui', 'Oui', 'Non', 'Non', '', '80-100');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_routes_forestieres`
--

CREATE TABLE `fiche_routes_forestieres` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `caracteristiques_troncon` text DEFAULT NULL,
  `c1` varchar(5) DEFAULT NULL,
  `c2` varchar(5) DEFAULT NULL,
  `c3` varchar(5) DEFAULT NULL,
  `c4` varchar(5) DEFAULT NULL,
  `c5` varchar(5) DEFAULT NULL,
  `c6` varchar(5) DEFAULT NULL,
  `c7` varchar(5) DEFAULT NULL,
  `c8` varchar(5) DEFAULT NULL,
  `c9` varchar(5) DEFAULT NULL,
  `c10` varchar(5) DEFAULT NULL,
  `total_points` decimal(5,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `appreciation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_routes_forestieres`
--

INSERT INTO `fiche_routes_forestieres` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `caracteristiques_troncon`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, `total_points`, `observations`, `appreciation`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', '', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', 0.00, '', ''),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', '', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', 6.00, '', ''),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', '', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', 7.00, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_securite_tronconneuses`
--

CREATE TABLE `fiche_securite_tronconneuses` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `tc_json` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_securite_tronconneuses`
--

INSERT INTO `fiche_securite_tronconneuses` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `tc_json`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', '[{\"num_serie\":\"09\",\"e1\":\"1\",\"e2\":\"1\",\"e3\":\"1\",\"e4\":\"1\",\"e5\":\"1\",\"e6\":\"1\",\"e7\":\"1\",\"e8\":\"1\",\"presence_carburant\":\"Oui\"}]'),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', '[{\"num_serie\":\"345\",\"e1\":\"1\",\"e2\":\"1\",\"e3\":\"1\",\"e4\":\"1\",\"e5\":\"1\",\"e6\":\"1\",\"e7\":\"1\",\"e8\":\"1\",\"presence_carburant\":\"Oui\"}]'),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', '[{\"num_serie\":\"23\",\"e1\":\"1\",\"e2\":\"0\",\"e3\":\"0\",\"e4\":\"1\",\"e5\":\"1\",\"e6\":\"1\",\"e7\":\"1\",\"e8\":\"1\",\"presence_carburant\":\"Oui\"}]');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_sortie_pieds`
--

CREATE TABLE `fiche_sortie_pieds` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `uc` varchar(50) DEFAULT NULL,
  `parc_foret_planifie` varchar(50) DEFAULT NULL,
  `nb_tiges_avenir_materialisees` int(11) DEFAULT NULL,
  `nb_tiges_avenir_non_materialisees` int(11) DEFAULT NULL,
  `trace_principal_nb_pistes` int(11) DEFAULT NULL,
  `trace_principal_nb_prise_mesure` int(11) DEFAULT NULL,
  `trace_principal_largeur_moyenne` decimal(5,2) DEFAULT NULL,
  `trace_secondaire_nb_pistes` int(11) DEFAULT NULL,
  `trace_secondaire_nb_prise_mesure` int(11) DEFAULT NULL,
  `trace_secondaire_largeur_moyenne` decimal(5,2) DEFAULT NULL,
  `c1` varchar(5) DEFAULT NULL,
  `c2` varchar(5) DEFAULT NULL,
  `c3` varchar(5) DEFAULT NULL,
  `c4` varchar(5) DEFAULT NULL,
  `c5` varchar(5) DEFAULT NULL,
  `c6` varchar(5) DEFAULT NULL,
  `c7` varchar(5) DEFAULT NULL,
  `c8` varchar(5) DEFAULT NULL,
  `c9` varchar(5) DEFAULT NULL,
  `c10` varchar(5) DEFAULT NULL,
  `total_points` decimal(5,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `appreciation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_sortie_pieds`
--

INSERT INTO `fiche_sortie_pieds` (`id`, `rapport_id`, `titre_forestier`, `aac`, `nom_controleur`, `date_controle`, `uc`, `parc_foret_planifie`, `nb_tiges_avenir_materialisees`, `nb_tiges_avenir_non_materialisees`, `trace_principal_nb_pistes`, `trace_principal_nb_prise_mesure`, `trace_principal_largeur_moyenne`, `trace_secondaire_nb_pistes`, `trace_secondaire_nb_prise_mesure`, `trace_secondaire_largeur_moyenne`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, `total_points`, `observations`, `appreciation`) VALUES
(1, 1, '09027', '', 'reine FOKO', '2026-05-29', '', '', 0, 0, 87, 23, 32.00, 23, 45, 77.93, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 0.00, '', ''),
(2, 2, '09027', '', 'reine FOKO', '2026-05-31', '', '', 0, 0, 65, 86, 67.00, 32, 43, 11.97, '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', 6.00, '', ''),
(3, 3, '09027', '', 'reine FOKO', '2026-05-31', '', '', 0, 0, 43, 34, 34.00, 23, 19, 34.00, '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', 7.00, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `fiche_tracabilite_grumes`
--

CREATE TABLE `fiche_tracabilite_grumes` (
  `id` int(11) NOT NULL,
  `rapport_id` int(11) NOT NULL,
  `nom_controleur` varchar(100) DEFAULT NULL,
  `date_controle` date DEFAULT NULL,
  `grume_json` text DEFAULT NULL,
  `observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fiche_tracabilite_grumes`
--

INSERT INTO `fiche_tracabilite_grumes` (`id`, `rapport_id`, `nom_controleur`, `date_controle`, `grume_json`, `observations`) VALUES
(1, 1, 'reine FOKO', '2026-05-29', '[{\"essence\":\"23\",\"num_df10\":\"32\",\"code_barre\":\"56\",\"date_abattage\":\"2026-05-29\",\"num_seq\":\"56\",\"n_ligne\":\"76\",\"n_ordre\":\"223\",\"n_fiche\":\"23\",\"volume\":\"23\",\"diam_pb\":\"21\",\"diam_gb\":\"23\",\"long\":\"32\",\"n_lv\":\"78\",\"affectation\":\"90\"}]', ''),
(2, 2, 'reine FOKO', '2026-05-31', '[{\"essence\":\"45\",\"num_df10\":\"56\",\"code_barre\":\"76\",\"date_abattage\":\"2026-05-31\",\"num_seq\":\"87\",\"n_ligne\":\"89\",\"n_ordre\":\"12\",\"n_fiche\":\"32\",\"volume\":\"12\",\"diam_pb\":\"54\",\"diam_gb\":\"56\",\"long\":\"768\",\"n_lv\":\"67\",\"affectation\":\"89\"}]', ''),
(3, 3, 'reine FOKO', '2026-05-31', '[{\"essence\":\"34\",\"num_df10\":\"65\",\"code_barre\":\"76\",\"date_abattage\":\"2026-05-31\",\"num_seq\":\"778\",\"n_ligne\":\"54\",\"n_ordre\":\"34\",\"n_fiche\":\"43\",\"volume\":\"43\",\"diam_pb\":\"34\",\"diam_gb\":\"23\",\"long\":\"233\",\"n_lv\":\"3\",\"affectation\":\"23\"}]', '');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `lu` tinyint(4) DEFAULT 0,
  `rapport_id` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `lu`, `rapport_id`, `date_creation`) VALUES
(1, 1, 'rapport_soumis', 'Le contrôleur reine FOKO a soumis le rapport #1.', 1, 1, '2026-05-29 21:25:44'),
(2, 1, 'rapport_soumis', 'Le contrôleur reine FOKO a soumis le rapport #2.', 1, 2, '2026-05-31 20:05:04'),
(3, 1, 'rapport_soumis', 'Le controleur reine FOKO a soumis le rapport #3.', 1, 3, '2026-05-31 20:45:48'),
(4, 2, 'rapport_validé', 'Votre rapport #3 a ete validé par l administrateur.', 1, 3, '2026-05-31 20:47:01');

-- --------------------------------------------------------

--
-- Structure de la table `rapports`
--

CREATE TABLE `rapports` (
  `id` int(11) NOT NULL,
  `controleur_id` int(11) NOT NULL,
  `titre` varchar(200) DEFAULT NULL,
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `date_rapport` date NOT NULL,
  `statut` enum('brouillon','soumis','validé','rejeté') DEFAULT 'brouillon',
  `avis_global` text DEFAULT NULL,
  `note_globale` decimal(5,2) DEFAULT NULL,
  `date_soumission` datetime DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `commentaire_admin` text DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rapports`
--

INSERT INTO `rapports` (`id`, `controleur_id`, `titre`, `titre_forestier`, `aac`, `date_rapport`, `statut`, `avis_global`, `note_globale`, `date_soumission`, `date_validation`, `admin_id`, `commentaire_admin`, `pdf_path`, `date_creation`) VALUES
(1, 2, 'CONTROLE TEST', '09027', '', '2026-05-29', 'validé', 'tres bon dans l&#039;ensemble', NULL, '2026-05-29 21:25:44', '2026-05-29 21:27:04', 1, 'bon observation', NULL, '2026-05-29 08:30:43'),
(2, 2, 'CONTROLE TEST2', '09027', '', '2026-05-31', 'validé', 'bonne observation', NULL, '2026-05-31 20:05:04', '2026-05-31 20:06:34', 1, 'bon controle', NULL, '2026-05-31 19:45:29'),
(3, 2, 'CONTROLE TEST3', '09027', '', '2026-05-31', 'validé', 'site conforme', NULL, '2026-05-31 20:45:48', '2026-05-31 20:47:01', 1, 'bon controle', NULL, '2026-05-31 20:33:49');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('administrateur','controleur') NOT NULL DEFAULT 'controleur',
  `titre_forestier` varchar(100) DEFAULT NULL,
  `aac` varchar(50) DEFAULT NULL,
  `statut` enum('actif','inactif','en_attente') DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT current_timestamp(),
  `derniere_connexion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `role`, `titre_forestier`, `aac`, `statut`, `date_creation`, `derniere_connexion`) VALUES
(1, 'Nguepi', 'Darryl', 'nguepidarryl@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur', NULL, NULL, 'actif', '2026-05-29 08:25:51', '2026-05-31 20:49:53'),
(2, 'FOKO', 'reine', 'reinefoko8@yahoo.com', '$2y$10$dQHRwKh3LHGXKxCKkI.Dje7N9meGzBRxduiTLp9d8lRyVovHISYSi', 'controleur', '09027', '', 'actif', '2026-05-29 08:29:58', '2026-05-31 20:48:42');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `fiche_abattage`
--
ALTER TABLE `fiche_abattage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_base_mecanique`
--
ALTER TABLE `fiche_base_mecanique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_debardage`
--
ALTER TABLE `fiche_debardage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_dechets_foret`
--
ALTER TABLE `fiche_dechets_foret`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_parc_foret`
--
ALTER TABLE `fiche_parc_foret`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_pont_forestier`
--
ALTER TABLE `fiche_pont_forestier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_post_exploitation`
--
ALTER TABLE `fiche_post_exploitation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_routes_forestieres`
--
ALTER TABLE `fiche_routes_forestieres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_securite_tronconneuses`
--
ALTER TABLE `fiche_securite_tronconneuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_sortie_pieds`
--
ALTER TABLE `fiche_sortie_pieds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `fiche_tracabilite_grumes`
--
ALTER TABLE `fiche_tracabilite_grumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapport_id` (`rapport_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `controleur_id` (`controleur_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `fiche_abattage`
--
ALTER TABLE `fiche_abattage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_base_mecanique`
--
ALTER TABLE `fiche_base_mecanique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_debardage`
--
ALTER TABLE `fiche_debardage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_dechets_foret`
--
ALTER TABLE `fiche_dechets_foret`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_parc_foret`
--
ALTER TABLE `fiche_parc_foret`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_pont_forestier`
--
ALTER TABLE `fiche_pont_forestier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_post_exploitation`
--
ALTER TABLE `fiche_post_exploitation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_routes_forestieres`
--
ALTER TABLE `fiche_routes_forestieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_securite_tronconneuses`
--
ALTER TABLE `fiche_securite_tronconneuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_sortie_pieds`
--
ALTER TABLE `fiche_sortie_pieds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fiche_tracabilite_grumes`
--
ALTER TABLE `fiche_tracabilite_grumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `rapports`
--
ALTER TABLE `rapports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `fiche_abattage`
--
ALTER TABLE `fiche_abattage`
  ADD CONSTRAINT `fiche_abattage_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_base_mecanique`
--
ALTER TABLE `fiche_base_mecanique`
  ADD CONSTRAINT `fiche_base_mecanique_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_debardage`
--
ALTER TABLE `fiche_debardage`
  ADD CONSTRAINT `fiche_debardage_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_dechets_foret`
--
ALTER TABLE `fiche_dechets_foret`
  ADD CONSTRAINT `fiche_dechets_foret_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_parc_foret`
--
ALTER TABLE `fiche_parc_foret`
  ADD CONSTRAINT `fiche_parc_foret_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_pont_forestier`
--
ALTER TABLE `fiche_pont_forestier`
  ADD CONSTRAINT `fiche_pont_forestier_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_post_exploitation`
--
ALTER TABLE `fiche_post_exploitation`
  ADD CONSTRAINT `fiche_post_exploitation_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_routes_forestieres`
--
ALTER TABLE `fiche_routes_forestieres`
  ADD CONSTRAINT `fiche_routes_forestieres_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_securite_tronconneuses`
--
ALTER TABLE `fiche_securite_tronconneuses`
  ADD CONSTRAINT `fiche_securite_tronconneuses_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_sortie_pieds`
--
ALTER TABLE `fiche_sortie_pieds`
  ADD CONSTRAINT `fiche_sortie_pieds_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiche_tracabilite_grumes`
--
ALTER TABLE `fiche_tracabilite_grumes`
  ADD CONSTRAINT `fiche_tracabilite_grumes_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD CONSTRAINT `rapports_ibfk_1` FOREIGN KEY (`controleur_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rapports_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
