<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// source noch nicht aktiv!
// Input: Tabellenname, Record-ID
// Output: Freigeben des Datenbanksatzes 

function clm_api_db_checkout($tabname,$id) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	$tabname = clm_core::$load->make_valid($tabname, 8, '');
	$user_jid = clm_core::$access->getJid ();	
echo "<br>user_jid"; var_dump($user_jid);
	$msg = '';
	
	// Test - existiert die Tabelle
	$query = "SHOW TABLES LIKE '%_clm_".$tabname."%'";
echo "<br>query"; var_dump($query);
	$record = clm_core::$db->loadObject($query);	
echo "<br>record"; var_dump($record);
	if (is_null($record)) {
		$msg = 'e_tablenotexist';
		$msg2 = 'table = '.$tabname;
	}
	
	// Test - existiert der Datensatz
	if ($msg == '') {
		$query	= "SELECT * FROM #__clm_".$tabname
		." WHERE id = ".$id
		;
echo "<br>query"; var_dump($query);
		$record = clm_core::$db->loadObject($query);	
echo "<br>record"; var_dump($record);
		if (is_null($record)) {
			$msg = 'e_recordnotexist';
			$msg2 = 'ID '.$id;
		}
	}
		
	// Test - ist Datensatz durch anderen Nutzer blockiert	
	if ($msg == '') {
echo "<br>record"; var_dump($record->checked_out);
//die();
		if ((!is_null($record->checked_out)) AND ($record->checked_out > 0) AND ($record->checked_out != $user_jid)) {
			$msg = 'e_recordblocked';
			$msg2 = 'User = '.$record->checked_out;
		}
	}

	if ($msg == '') {
		$now = clm_core::$cms->getNowDate();
echo "<br>now"; var_dump($now);
		$query	= "UPDATE #__clm_".$tabname
			." SET checked_out = $user_jid"
			." , checked_out_time = '".$now."'"
			." WHERE id = ".$id
			;
		if (!clm_core::$db->query($query)) {
			$msg = 'e_saveerror';
			$msg2 = 'Error = ???';
		}	
	}		
			
	if ($msg != '') {
		clm_core::addWarning("checkout failed",$msg." (".$msg2.")"." Backtrace: ".clm_core::getBacktrace());
		return array(false, 'Im Moment ist diese Bearbeitung nicht möglich, bitte später erneut versuchen!', $msg );	
	}
	
	return array(true,'m_checkout');

}
?>
