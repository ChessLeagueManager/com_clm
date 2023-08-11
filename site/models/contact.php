<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fred Baumgarten
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelContact extends JModelLegacy
{
	function _getCLMClmuser ( &$options ) {
		$user	= JFactory::getUser();
		$jid	= $user->get('id');
		$query	= "SELECT * FROM #__clm_user WHERE jid = $jid AND sid in (select id from #__clm_saison where published = 1)";
		return $query;
	}

	function getCLMClmuser ( $options=array() ) {
		$query	= $this->_getCLMClmuser( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function updateUser ( $fest, $mobil, $email ) {
		$user	= JFactory::getUser();
		$jid	= $user->get('id');
		$parray = array();
		$query	= "UPDATE #__clm_user SET ";
		$nc = 0;
		if ($fest !== "_ZERO_") {
			$query = $query . "tel_fest='" . $fest . "'";
			$nc++;
			$parray['tel_fest'] = $fest;
		}
		if ($mobil !== "_ZERO_") {
			if ($nc != 0) {
				$query .= ", ";
			}
			$query = $query . "tel_mobil='" . $mobil . "'";
			$nc++;
			$parray['tel_mobil'] = $mobil;
		}
		if ($email !== "_ZERO_") {
			if ($nc != 0) {
				$query .= ", ";
			}
			$query = $query . "email='" . $email . "'";
			$nc++;
			$parray['email'] = $email;
		}
		$query .= " WHERE jid = $jid AND sid in (select id from #__clm_saison where published = 1)";
		if ($nc != 0) {
			clm_core::$db->query($query);
			// Log - log
			$aktion = "Kontaktdatenpflege FE";
			clm_core::addDeprecated($aktion, json_encode($parray));
		}
	}
}
?>
