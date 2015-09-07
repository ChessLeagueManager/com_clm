--
-- Datenbank ohne prefix löschen oder unbennenen
--
DROP TABLE dwz_spieler;
DROP TABLE dwz_vereine;
ALTER TABLE dwz_verbaende RENAME #__clm_dwz_verbaende;
--
-- Zugriffspunkte sind statisch und befinden sich nun in der include/accesspoints.php
--
DROP TABLE #__clm_access_points;
--
-- Die Tabelle wurde seit Joomla 2.5 nicht mehr benutzt, für die neue Konfiguration ist #__clm_config eingerichtet
--
DROP TABLE #__clm_params;
--
-- user_clm entfernen, veraltet durch das Rechtesystem
--
ALTER TABLE #__clm_user DROP COLUMN user_clm; 
ALTER TABLE #__clm_usertype DROP COLUMN user_clm; 
ALTER TABLE #__clm_usertype DROP COLUMN fe_params; 
ALTER TABLE #__clm_usertype CHANGE be_params params text;
--
-- Tabellenoptimierung User- / Saisonverwaltung (nicht wichtig)
--
ALTER TABLE #__clm_user DROP KEY sid_jid;
--
-- Komprimierung bei "kleinen" Tabellen nicht sinnvoll
--
ALTER TABLE #__clm_saison ROW_FORMAT=DEFAULT;
ALTER TABLE #__clm_runden_termine ROW_FORMAT=DEFAULT;
ALTER TABLE #__clm_mannschaften ROW_FORMAT=DEFAULT;
--
-- Alte Tabellen für den direkten Import von Daten über DeWIS entfernen
--
DROP TABLE #__clm_dwz_dewis;
DROP TABLE #__clm_dwz_dewis_merge;
--
-- Tabellen für den direkten Import von Vereinen über DeWIS vorbereiten
--
ALTER TABLE #__clm_dwz_vereine ADD UNIQUE( `sid`, `ZPS`); 
--
-- Tabellenstruktur für Tabelle `#__clm_config`
--
CREATE TABLE IF NOT EXISTS `#__clm_config` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
