-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 18. Apr 2014 um 16:22
-- Server Version: 5.5.27
-- PHP-Version: 5.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `clm`
--

-- --------------------------------------------------------

DROP TABLE IF EXISTS #__clm_categories;
DROP TABLE IF EXISTS #__clm_config;
DROP TABLE IF EXISTS #__clm_dwz_spieler;
DROP TABLE IF EXISTS #__clm_dwz_vereine;
DROP TABLE IF EXISTS #__clm_dwz_verbaende;
DROP TABLE IF EXISTS #__clm_ergebnis;
DROP TABLE IF EXISTS #__clm_liga;
DROP TABLE IF EXISTS #__clm_logging;
DROP TABLE IF EXISTS #__clm_mannschaften;
DROP TABLE IF EXISTS #__clm_meldeliste_spieler;
DROP TABLE IF EXISTS #__clm_pgn;
DROP TABLE IF EXISTS #__clm_rangliste_id;
DROP TABLE IF EXISTS #__clm_rangliste_name;
DROP TABLE IF EXISTS #__clm_rangliste_spieler;
DROP TABLE IF EXISTS #__clm_rnd_man;
DROP TABLE IF EXISTS #__clm_rnd_spl;
DROP TABLE IF EXISTS #__clm_runden_termine;
DROP TABLE IF EXISTS #__clm_saison;
DROP TABLE IF EXISTS #__clm_swt_liga;
DROP TABLE IF EXISTS #__clm_swt_mannschaften;
DROP TABLE IF EXISTS #__clm_swt_meldeliste_spieler;
DROP TABLE IF EXISTS #__clm_swt_rnd_man;
DROP TABLE IF EXISTS #__clm_swt_rnd_spl;
DROP TABLE IF EXISTS #__clm_swt_turniere;
DROP TABLE IF EXISTS #__clm_swt_turniere_rnd_spl;
DROP TABLE IF EXISTS #__clm_swt_turniere_rnd_termine;
DROP TABLE IF EXISTS #__clm_swt_turniere_tlnr;
DROP TABLE IF EXISTS #__clm_swt_spl;
DROP TABLE IF EXISTS #__clm_swt_man;
DROP TABLE IF EXISTS #__clm_swt_spl_nach;
DROP TABLE IF EXISTS #__clm_swt_spl_tmp;
DROP TABLE IF EXISTS #__clm_termine;
DROP TABLE IF EXISTS #__clm_turniere;
DROP TABLE IF EXISTS #__clm_turniere_rnd_spl;
DROP TABLE IF EXISTS #__clm_turniere_rnd_termine;
DROP TABLE IF EXISTS #__clm_turniere_sonderranglisten;
DROP TABLE IF EXISTS #__clm_turniere_tlnr;
DROP TABLE IF EXISTS #__clm_user;
DROP TABLE IF EXISTS #__clm_usertype;
DROP TABLE IF EXISTS #__clm_vereine;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
