<?php
/*
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
clm_core::$cms->addStyleSheet(clm_core::$url."css/select2.min.css");

/*
 * enthalten in
 *
 * Vereinefilter (Einzel Controller)
 *      Vereine         -> ZPS
 *      Veranstaltungen -> Veranstalter
 *      Turniere        -> Veranstalter
 *      Mannschaften    -> Verein
 *                      -> Spielgemeinschaft
 *      Benutzer
 *      Mitgliederverwaltung
 *      Ranglisten
 *
 * Vereinefilter als Hauptmenuepunkt
 *      Benutzer
 *      Mannschaften
 *      Ranglisten
 *
 * Spieler
 *      Spieler wählen
 * Liga
 * User
 *      Verein -> Vereinsleiter
 * Mitgliederverwaltung -> Spieler löschen
 * ?? Saison
 *
*/
?>
