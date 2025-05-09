-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 22. Apr 2014 um 01:41
-- Server Version: 5.5.27
-- PHP-Version: 5.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
--
-- Daten für Tabelle `#__clm_usertype`
--

REPLACE INTO `#__clm_usertype` (`id`, `name`, `usertype`, `kind`, `published`, `ordering`, `params`) VALUES
(1, 'Administrator', 'admin', 'CLM', 1, 0, 'BE_general_general=1\nBE_season_general=1\nBE_event_general=1\nBE_event_delete=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=1\nBE_teamtournament_edit_result=1\nBE_teamtournament_edit_fixture=1\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_club_copy=1\nBE_club_edit_ranking=1\nBE_team_general=1\nBE_team_create=1\nBE_team_edit=1\nBE_team_delete=1\nBE_team_registration_list=1\nBE_user_general=1\nBE_user_copy=1\nBE_accessgroup_general=1\nBE_swt_general=1\nBE_pgn_general=1\nBE_dewis_general=1\nBE_database_general=1\nBE_logfile_general=1\nBE_logfile_delete=1\nBE_config_general=1'),
(2, 'DV Referent', 'dv', 'CLM', 1, 1, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_team_general=1\nBE_team_create=1\nBE_team_registration_list=1\nBE_user_general=1\nBE_logfile_general=1'),
(3, 'Spielleiter', 'dv1', 'CLM', 1, 2, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=1\nBE_teamtournament_edit_result=1\nBE_teamtournament_edit_fixture=1\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_team_general=1\nBE_team_create=1\nBE_team_registration_list=1\nBE_logfile_general=1'),
(4, 'DWZ Referent', 'dwz', 'CLM', 1, 4, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_create=1\nBE_tournament_edit_detail=1\nBE_tournament_delete=1\nBE_tournament_edit_round=1\nBE_tournament_edit_result=1\nBE_tournament_edit_fixture=1\nBE_league_general=1\nBE_league_create=1\nBE_league_edit_detail=1\nBE_league_delete=1\nBE_league_edit_round=1\nBE_league_edit_result=1\nBE_league_edit_fixture=1\nBE_teamtournament_general=1\nBE_teamtournament_create=1\nBE_teamtournament_edit_detail=1\nBE_teamtournament_delete=1\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=1\nBE_club_create=1\nBE_club_edit_member=1\nBE_team_general=1\nBE_team_create=1\nBE_team_registration_list=1\nBE_swt_general=1\nBE_pgn_general=1\nBE_dewis_general=1\nBE_database_general=1\nBE_logfile_general=1'),
(5, 'Turnier- und Staffelleiter', 'tsl', 'CLM', 1, 8, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_edit_detail=2\nBE_tournament_edit_round=2\nBE_tournament_edit_result=2\nBE_tournament_edit_fixture=2\nBE_league_general=1\nBE_league_edit_detail=2\nBE_league_edit_round=2\nBE_league_edit_result=2\nBE_league_edit_fixture=2\nBE_teamtournament_general=1\nBE_teamtournament_edit_detail=2\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_club_general=1\nBE_team_general=1\nBE_team_edit=2\nBE_team_registration_list=2\nBE_logfile_general=1'),
(6, 'TL für Mannschaftsturniere', 'mtl', 'CLM', 1, 9, 'BE_general_general=1\nBE_event_general=1\nBE_teamtournament_general=1\nBE_teamtournament_edit_detail=2\nBE_teamtournament_edit_round=2\nBE_teamtournament_edit_result=2\nBE_teamtournament_edit_fixture=2\nBE_team_general=1\nBE_team_edit=2\nBE_team_registration_list=2\nBE_logfile_general=1'),
(7, 'Staffelleiter', 'sl', 'CLM', 1, 10, 'BE_general_general=1\nBE_event_general=1\nBE_league_general=1\nBE_league_edit_detail=2\nBE_league_edit_round=2\nBE_league_edit_result=2\nBE_league_edit_fixture=2\nBE_team_general=1\nBE_team_edit=2\nBE_team_registration_list=2\nBE_logfile_general=1'),
(8, 'Staffelleiter II', 'sl2', 'CLM', 1, 11, 'BE_general_general=1\nBE_event_general=1\nBE_league_general=1\nBE_league_edit_result=2'),
(9, 'Turnierleiter', 'tl', 'CLM', 1, 15, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_edit_detail=2\nBE_tournament_edit_round=2\nBE_tournament_edit_result=2\nBE_tournament_edit_fixture=2\nBE_team_edit=2'),
(10, 'Damenwart', 'dw', 'CLM', 1, 20, ''),
(11, 'Jugendwart', 'jw', 'CLM', 1, 21, ''),
(12, 'Vereinsspielleiter', 'vtl', 'CLM', 1, 31, ''),
(13, 'Vereinsleiter', 'vl', 'CLM', 1, 30, ''),
(14, 'Vereinsjugendwart', 'vjw', 'CLM', 1, 32, ''),
(15, 'Vereinsdamenwart', 'vdw', 'CLM', 1, 33, ''),
(16, 'Mannschaftsführer', 'mf', 'CLM', 1, 40, ''),
(17, 'Spieler', 'spl', 'CLM', 1, 50, ''),
(18, 'Turnierleiter+SWT', 'tlimp', 'CLM', 1, 16, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_edit_detail=2\nBE_tournament_edit_round=2\nBE_tournament_edit_result=2\nBE_tournament_edit_fixture=2\nBE_team_edit=2\nBE_swt_general=1'),
(19, 'CLMreserve02', 'reserve02', 'CLM', 0, 52, ''),
(20, 'CLMreserve03', 'reserve03', 'CLM', 0, 53, '');

--
-- Daten für Tabelle `#__clm_ergebnis`
--
REPLACE INTO `#__clm_ergebnis` (`id`, `eid`, `erg_text`, `dsb_w`, `dsb_s`, `xml_w`, `xml_s`) VALUES
(1, 0, '0-1',      '0','1','0:1','1:0'),
(2, 1, '1-0',      '1','0','1:0','0:1'),
(3, 2, '0,5-0,5',  'R','R','½:½','½:½'),
(4, 3, '0-0',      '0','0','0:0','0:0'),
(5, 4, '-/+',      '-','+','-:+','+:-'),
(6, 5, '+/-',      '+','-','+:-','-:+'),
(7, 6, '-/-',      ':',':','-:-','-:-'),
(8, 7, '---',      ':',':','',''),
(9, 8, 'spielfrei',':',':','+:-','-:+'),
(10, 9, '0-0,5',   '0','R','½:0','0:½'),
(11, 10, '0,5-0',  'R','0','0:½','½:0'),
(12, 11, '1--',    '+','-','+:-','-:+'),
(13, 12, '0,5--',  '=','-','½:0','0:½'),
(14, 13, '0--',    '-','-','-:-','-:-');


-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

REPLACE INTO `#__clm_arbiterlicense` (`id`, `name`) VALUES 
(1, 'verein_turnierleiter'),
(2, 'verein_turnierorganisator'),
(3, 'bezirk_turnierleiter'),
(4, 'bezirk_turnierorganisator'),
(5, 'bezirk_spielleiter'),
(6, 'bezirk_staffelleiter'),
(7, 'verband_turnierleiter'),
(8, 'verband_turnierorganisator'),
(9, 'verband_spielleiter'),
(10, 'verband_staffelleiter'),
(11, 'landesverband_turnierleiter'),
(12, 'landesverband_turnierorganisator'),
(13, 'landesverband_spielleiter'),
(14, 'landesverband_staffelleiter'),
(15, 'dsb_verbandsschiedsrichter'),
(16, 'dsb_turnierorganisator'),
(17, 'dsb_regionalschiedsrichter'),
(18, 'dsb_nationalschiedsrichter'),
(19, 'fide_turnierorganisator'),
(20, 'fide_national_arbiter'),
(21, 'fide_fide_arbiter'),
(22, 'fide_international_arbiter');

