<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

/**
 * access check on backend within clm component
*/
	
class CLMAccess {

		// Korrektur durch alten PHP 4.x Code
		private $_db;
		private $_season;

	function __construct() {
	  // DB
		$this->_db				= JFactory::getDBO();
	  // season
		$query = ' SELECT id FROM #__clm_saison
					WHERE published = 1 AND archiv = 0
					ORDER BY name DESC LIMIT 1;' ;
		$this->_db->setQuery($query);

		// !NOTE: loadObject gibt null zurück wenn die Abfrage kein Ergebnis ausgibt
		// wenn _season nicht gesetzt wird gibt es ungültigen SQL Code.
		// Das ist mist und führt unter Joomla 3.2 aus Sicherheitsgründen zu Fehlern :(
		// Der gesamte Code muss darauf untersucht werden!!!
		if(!is_null($this->_db->loadObject()))
		{
		$this->_season = $this->_db->loadObject()->id;
		}
		else
		{

		}
		
		$query = ' 	SELECT usertype FROM #__clm_user
					WHERE sid = '.$this->_season.' AND jid = '.CLM_ID.' LIMIT 1;' ;
		$this->_db->setQuery($query);

		$out=$this->_db->loadObject();
		if(isset($out->usertype)){
		$this->_usertype = $out->usertype;
 
		$query = ' 	SELECT be_params FROM #__clm_usertype '
				 .' WHERE usertype = "'.$this->_usertype.'" LIMIT 1;' ;
		$this->_db->setQuery($query);
		$this->_be_params = $this->_db->loadObject()->be_params;
		}else{$this->_be_params="";}

	  // parameters
		$this->accesspoint		= '';  // wird von außen befüllt mit accesspoint like BE_league_create
		$this->accessvalue		= '';  // wird von außen befüllt mit operator und accessvalue (z.B. =1 oder >0) 
		$this->accessuser		= '';  // wird von außen befüllt mit User-Id
		$this->accessusertype	= '';  // wird von außen befüllt mit Benutzergruppe (z.B. admin oder sl)
	}
	
	/**
	 * access check for session user
	 */
	function access() {			
		$ipos = strpos($this->_be_params, $this->accesspoint);
		if ($ipos === false) return false;
		$ipos = $ipos + strlen($this->accesspoint) + 1;
		$result = substr($this->_be_params,$ipos,1);
		if ($result == 0) return false;
		elseif ($result == 1) return true;	
		return $result;
	}

	/**
	 * access check for usertype given
	 */
	function accesstype() {			
		$query = ' 	SELECT be_params FROM #__clm_usertype '
				 .' WHERE usertype = "'.$this->accessusertype.'" LIMIT 1;' ;
		$this->_db->setQuery($query);
		$this->_be_params_gu = $this->_db->loadObject()->be_params;
		$ipos = strpos($this->_be_params_gu, $this->accesspoint);
		if ($ipos === false) return false;
		$ipos = $ipos + strlen($this->accesspoint) + 1;
		$result = substr($this->_be_params_gu,$ipos,1);
		if ($result == 0) return false;
		elseif ($result == 1) return true;	
		return $result;
	}

	/**
	 * user list to a given accesspoint (e.g. BE_league_edit_result)
	 */
	function userlist() {			
	  // access parameter for backend
		$query = ' 	SELECT * FROM #__clm_usertype; ';
		$this->_db->setQuery($query);
		$a_usertypes = $this->_db->loadObjectList();
		$groups = '';
		foreach ($a_usertypes as $atypes) {
			$ipos = strpos($atypes->be_params, $this->accesspoint);
			if ($ipos === false) $result = '0';
			else {
				$ipos = $ipos + strlen($this->accesspoint) + 1;
				$result = substr($atypes->be_params,$ipos,1);
			}
			if (((substr($this->accessvalue,0,1) == '=') AND ($result == substr($this->accessvalue,1,1))) OR
				((substr($this->accessvalue,0,1) == '>') AND ($result > substr($this->accessvalue,1,1))) OR
				((substr($this->accessvalue,0,1) == '<') AND ($result < substr($this->accessvalue,1,1))))
				$groups .= '"'.$atypes->usertype.'",';
		}
		if (strlen($groups) > 0) $groups = substr($groups,0,(strlen($groups)-1));
		$query = ' SELECT a.jid,a.name FROM #__clm_user as a
					WHERE sid = '.$this->_season.' AND usertype IN ('.$groups.') ' ;
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectLIst();
		return $result;
	}

	/**
	 * list of usertypes in string format (e.g. "sl","tl","mtl" )
	 * including all usertypes with the same or less authorisations as a given one
	 */
	function usertypelist() {			
		// list of all accesspoints published
		$query = ' 	SELECT * FROM #__clm_access_points WHERE published = 1 ';
		$this->_db->setQuery($query);
		$accesspoints = $this->_db->loadObjectList();
		// list of all usertypes published 
		$query = ' 	SELECT * FROM #__clm_usertype WHERE published = 1 ';
		$this->_db->setQuery($query);
		$a_usertypes = $this->_db->loadObjectList();
		$typelist = '';
		
		foreach ($a_usertypes as $atypes) {
			$swresult = true;
			foreach ( $accesspoints as $ap) {
				$accesspoint = $ap->area.'_'.$ap->accesstopic.'_'.$ap->accesspoint;
				$ipos = strpos($atypes->be_params, $accesspoint);
				if ($ipos === false) $cresult = '0';
				else { 
					$ipos = $ipos + strlen($accesspoint) + 1;
					$cresult = substr($atypes->be_params,$ipos,1);
				}
			
				$ipos = strpos($this->_be_params, $accesspoint);
				if ($ipos === false) $oresult = '0';
				else { 
					$ipos = $ipos + strlen($accesspoint) + 1;
					$oresult = substr($this->_be_params,$ipos,1);
				}
				if ($oresult == '1') continue;
				if ($cresult == '0') continue;
				if ($oresult == $cresult) continue;
				$swresult = false;
				break;
			}
			if ($swresult) {
				$typelist .= '"'.$atypes->usertype.'",';
			}
		}
		if (strlen($typelist) > 0) $typelist = substr($typelist,0,(strlen($typelist)-1));
		return $typelist;
	}

	/**
	 * user comparison
	 */
	function comparison() {			
	  // access parameter for backend
		$query = ' 	SELECT be_params FROM #__clm_usertype '
				 .' WHERE usertype = "'.$this->accessusertype.'" LIMIT 1;' ;
		$this->_db->setQuery($query);
		$c_usertype = $this->_db->loadObject();
		if (isset($c_usertype)) $c_be_params = $c_usertype->be_params; else $c_be_params = '';
		$query = ' 	SELECT * FROM #__clm_access_points; ';
		$this->_db->setQuery($query);
		$accesspoints = $this->_db->loadObjectList();
	
		foreach ( $accesspoints as $ap) {
			$accesspoint = $ap->area.'_'.$ap->accesstopic.'_'.$ap->accesspoint;
			$ipos = strpos($c_be_params, $accesspoint);
			if ($ipos === false) $cresult = '0';
			else { 
				$ipos = $ipos + strlen($accesspoint) + 1;
				$cresult = substr($c_be_params,$ipos,1);
			}	
			$ipos = strpos($this->_be_params, $accesspoint);
			if ($ipos === false) $oresult = '0';
			else { 
				$ipos = $ipos + strlen($accesspoint) + 1;
				$oresult = substr($this->_be_params,$ipos,1);
			}
			if ($oresult == '1') continue;
			if ($cresult == '0') continue;
			if ($oresult == $cresult) continue;
			return false;
		}
		return true;
	}
 }
?>
