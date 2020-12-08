<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
// DB-Problem: bei Update einer Tabelle werden auch nicht beteiligte DB-Felder auf korrekten Inhalt geprüft.
// Es kann:   #1292 - Falscher datetime-Wert: '0000-00-00 00:00:00' für Feld 'xyz'  
// auftreten und die geplante DB-Änderung wird nicht durchgeführt.
//  
// Start dieser Routine mit z.B. http://localhost/hallep/administrator/index.php?option=com_clm&view=forceDBCorrection
// 
// Diese Routine behandelt DB-Felder der Typen datetime und date und ist nötig z.B. bei MySQL 5.7.26
// 
// Außerkraftsetzen der Feldprüfung 
$query = "SET SESSION SQL_MODE='ALLOW_INVALID_DATES'";
clm_core::$db->query($query);

$total = -1;
$query = "UPDATE #__clm_liga"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_liga</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_mannschaften"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_mannschaften</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_mannschaften"
		." SET datum = '1970-01-01 00:00:00' "
		." WHERE datum = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_mannschaften</b>  Field  <b>datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_mannschaften"
		." SET edit_datum = '1970-01-01 00:00:00' "
		." WHERE edit_datum = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_mannschaften</b>  Field  <b>edit_datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rangliste_id"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rangliste_id</b>  Field  <b>checked_out_time</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rangliste_name"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rangliste_name</b>  Field  <b>checked_out_time</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rnd_man"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rnd_man</b>  Field  <b>checked_out_time</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rnd_man"
		." SET zeit = '1970-01-01 00:00:00' "
		." WHERE zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rnd_man</b>  Field  <b>zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rnd_man"
		." SET edit_zeit = '1970-01-01 00:00:00' "
		." WHERE edit_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rnd_man</b>  Field  <b>edit_zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rnd_man"
		." SET dwz_zeit = '1970-01-01 00:00:00' "
		." WHERE dwz_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rnd_man</b>  Field  <b>dwz_zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_runden_termine"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_runden_termine</b>  Field  <b>checked_out_time</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_runden_termine"
		." SET zeit = '1970-01-01 00:00:00' "
		." WHERE zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_runden_termine</b>  Field  <b>zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_runden_termine"
		." SET edit_zeit = '1970-01-01 00:00:00' "
		." WHERE edit_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_runden_termine</b>  Field  <b>edit_zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_saison"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_saison</b>  Field  <b>checked_out_time</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_liga"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_liga</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_mannschaften"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_mannschaften</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_mannschaften"
		." SET datum = '1970-01-01 00:00:00' "
		." WHERE datum = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_mannschaften</b>  Field  <b>datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_mannschaften"
		." SET edit_datum = '1970-01-01 00:00:00' "
		." WHERE edit_datum = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_mannschaften</b>  Field  <b>edit_datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_rnd_man"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_rnd_man</b>  Field  <b>checked_out_time</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_rnd_man"
		." SET zeit = '1970-01-01 00:00:00' "
		." WHERE zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_rnd_man</b>  Field  <b>zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_rnd_man"
		." SET edit_zeit = '1970-01-01 00:00:00' "
		." WHERE edit_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_rnd_man</b>  Field  <b>edit_zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_rnd_man"
		." SET dwz_zeit = '1970-01-01 00:00:00' "
		." WHERE dwz_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_rnd_man</b>  Field  <b>dwz_zeit</b>  has been corrected,  $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_turniere</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere_rnd_termine"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)		
	echo "<br>Table  <b>clm_swt_turniere_rnd_termine</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere_rnd_termine"
		." SET zeit = '1970-01-01 00:00:00' "
		." WHERE zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();
if ($total != 0)		
	echo "<br>Table  <b>clm_swt_turniere_rnd_termine</b>  Field  <b>zeit</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere_rnd_termine"
		." SET edit_zeit = '1970-01-01 00:00:00' "
		." WHERE edit_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_turniere_rnd_termine</b>  Field  <b>edit_zeit</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere_tlnr"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_turniere_tlnr</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_termine"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_termine</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere_rnd_termine"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere_rnd_termine</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere_rnd_termine"
		." SET zeit = '1970-01-01 00:00:00' "
		." WHERE zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere_rnd_termine</b>  Field  <b>zeit</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere_rnd_termine"
		." SET edit_zeit = '1970-01-01 00:00:00' "
		." WHERE edit_zeit = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere_rnd_termine</b>  Field  <b>edit_zeit</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere_sonderranglisten"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere_sonderranglisten</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere_tlnr"
		." SET checked_out_time = '1970-01-01 00:00:00' "
		." WHERE checked_out_time = '0000-00-00 00:00:00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere_tlnr</b>  Field  <b>checked_out_time</b>  has been corrected , $total rows affected.<br>";

//-------------------------------------------------------------------------------------------------------------------------------------------
// Felder vom Typ date
$total = -1;
$query = "UPDATE #__clm_categories"
		." SET dateStart = '1970-01-01' "
		." WHERE dateStart = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_categories</b>  Field  <b>dateStart</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_categories"
		." SET dateEnd = '1970-01-01' "
		." WHERE dateEnd = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_categories</b>  Field  <b>dateEnd</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rangliste_name"
		." SET Meldeschluss = '1970-01-01' "
		." WHERE Meldeschluss = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rangliste_name</b>  Field  <b>Meldeschluss</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_rnd_man"
		." SET pdate = '1970-01-01' "
		." WHERE pdate = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_rnd_man</b>  Field  <b>pdate</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_runden_termine"
		." SET datum = '1970-01-01' "
		." WHERE datum = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_runden_termine</b>  Field  <b>datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_runden_termine"
		." SET deadlineday = '1970-01-01' "
		." WHERE deadlineday = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_runden_termine</b>  Field  <b>deadlineday</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_runden_termine"
		." SET enddatum = '1970-01-01' "
		." WHERE enddatum = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_runden_termine</b>  Field  <b>enddatum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_saison"
		." SET datum = '1970-01-01' "
		." WHERE datum = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_saison</b>  Field  <b>datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere"
		." SET dateStart = '1970-01-01' "
		." WHERE dateStart = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_turniere</b>  Field  <b>dateStart</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere"
		." SET dateEnd = '1970-01-01' "
		." WHERE dateEnd = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_turniere</b>  Field  <b>dateEnd</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_swt_turniere_rnd_termine"
		." SET datum = '1970-01-01' "
		." WHERE datum = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_swt_turniere_rnd_termine</b>  Field  <b>datum</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_termine"
		." SET startdate = '1970-01-01' "
		." WHERE startdate = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_termine</b>  Field  <b>startdate</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_termine"
		." SET enddate = '1970-01-01' "
		." WHERE enddate = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_termine</b>  Field  <b>enddate</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere"
		." SET dateStart = '1970-01-01' "
		." WHERE dateStart = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere</b>  Field  <b>dateStart</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere"
		." SET dateEnd = '1970-01-01' "
		." WHERE dateEnd = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere</b>  Field  <b>dateEnd</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere"
		." SET dateRegistration = '1970-01-01' "
		." WHERE dateRegistration = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere</b>  Field  <b>dateRegistration</b>  has been corrected , $total rows affected.<br>";

$total = -1;
$query = "UPDATE #__clm_turniere_rnd_termine"
		." SET datum = '1970-01-01' "
		." WHERE datum = '0000-00-00' "
		;
clm_core::$db->query($query);
$total = clm_core::$db->affected_rows();		
if ($total != 0)
	echo "<br>Table  <b>clm_turniere_rnd_termine</b>  Field  <b>datum</b>  has been corrected , $total rows affected.<br>";

	
		
	
?>
