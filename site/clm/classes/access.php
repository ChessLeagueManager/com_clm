<?php
/**
 * Benutzerrechteverwaltung
 */
class clm_class_access {
	private $id;
	private $jid;
	private $name;
	private $username;
	private $zps;
	private $type;
	private $typeId;
	private $season;
	private static $accesspoints = null;
	private static $rights = null;
	private static $usertypeId = array();
	function __construct() {
		// Es muss immer eine aktive Saison vorhanden sein
		$query = 'SELECT id FROM #__clm_saison WHERE published = 1 AND archiv = 0';
		$saison = clm_core::$db->loadObjectList($query);
		if (count($saison) != 1) {
			$this->season = - 1;
			$this->makeEmergencyAdmin();
			return;
		}
		$this->season = $saison[0]->id;
		// Ist der Benutzer in Joomla eingeloggt?
		$jid = clm_core::$cms->getUserData();
		$jid = $jid[0]; // array dereferencing fix php 5.3
		if ($jid == 0) {
			$this->makeGuest();
			return;
		}
		// Falls der Benutzer eingeloggt ist aber keinen CLM Benutzer besitzt, so...
		$query = 'SELECT id FROM #__clm_user WHERE sid = ' . $this->season . ' AND jid = ' . $jid . ' AND  published = 1';
		$user = clm_core::$db->loadObjectList($query);
		if (count($user) < 1) {
			// Mache aus ihm einen Admin falls er Root ist, sonst entsteht ein Gast
			$this->makeEmergencyAdmin();
			return;
		}
		$this->id = $user[0]->id;
		$this->jid = $jid;
		$this->username = clm_core::$db->user->get($this->id)->username;
		$this->name = clm_core::$db->user->get($this->id)->name;
		$this->zps = clm_core::$db->user->get($this->id)->zps;
		$this->type = clm_core::$db->user->get($this->id)->usertype;
		$this->typeId = clm_class_access::getUsertypeId($this->type);
	}
	// nicht eingeloggt -> Gast
	private function makeGuest() {
		$lang = clm_core::$lang->designation;
		$this->id = -1;
		$this->jid = -1;
		$this->username = $lang->Guest;
		$this->name = $lang->Guest;
		$this->type = "";
		$this->typeId = - 1;
	}
	// Wenn keine Saison aktiv ist oder es keinen Administrator in der aktiven Saison gibt
	// wird der zugreifende Benutzer zum Admin, falls er in Joomla ein Super Admin ist.
	private function makeEmergencyAdmin() {
		if (clm_core::$cms->isRoot()) {
			$user = clm_core::$cms->getUserData();
			$this->id = - 1;
			$this->jid = $user[0];
			$this->username = $user[1];
			$this->name = $user[2];
			$this->type = "admin";
			$this->typeId = 1;
		} else {
			$this->makeGuest();
		}
	}
	/**
	 * Rechtekontrolle für den aktiven Benutzer
	 */
	function api($api, $input = array(), $onlyPublished = true) {
		return clm_class_access::apiWithId($this->typeId, $api, $input, $onlyPublished);
	}
	function access($accesspoint, $onlyPublished = true) {
		return clm_class_access::accessWithId($this->typeId, $accesspoint, $onlyPublished);
	}
	public function compare($type, $onlyPublished = true) {
		return clm_class_access::compareWithId($this->typeId, $type, $onlyPublished);
	}
	public function usertypelist($onlyPublished = true) {
		return clm_class_access::usertypelistWithId($this->typeId, $onlyPublished = true);
	}
	public function getParams($onlyPublished = true) {
		return clm_class_access::getParamsWithId($this->type, $onlyPublished = true);
	}
	/**
	 * Rechtekontrolle für beliebigen Benutzer
	 */
	// Besitzt die angegebene Benutzergruppe Zugriff auf die jeweilige API
	public static function apiWithType($type, $api, $input = array(), $onlyPublished = true) {
		return clm_class_access::apiWithId(clm_class_access::getUsertypeId($type), $api, $input, $onlyPublished);
	}
	public static function apiWithId($id, $api, $input = array(), $onlyPublished = true) {		
		$rights = clm_class_access::getRights();
		if(!isset($rights[$api])){
			return false;
		} else if(count($rights[$api])==0){
			return true;
		}
		switch ($rights[$api][0]) {
			case 0:
				if((!$rights[$api][2] && clm_class_access::accessWithId($id, $rights[$api][1], $onlyPublished)) || (($rights[$api][2] && clm_class_access::accessWithId($id, $rights[$api][1], $onlyPublished) === true))) {
					return true;
				}
				break;
			case 1:
				if(!isset($input[$rights[$api][4]]) || !isset($input[$rights[$api][5]])) {
					return false;
				}
				return self::tournamentCheck($rights[$api],$input[$rights[$api][4]],$input[$rights[$api][5]]);
			case 2:
				if(!isset($input[$rights[$api][2]])) {
					return false;
				}
				return self::tournamentCheck($rights[$api],false,$input[$rights[$api][2]]);
			case 3:
				foreach($rights[$api][1] as $key => $value) {
					if(!(!$value && clm_class_access::accessWithId($id, $key, $onlyPublished)) && (!($value && clm_class_access::accessWithId($id, $key, $onlyPublished) === true))) {
						return false;
					}
				}	
				return true;
			case 4:
				foreach($rights[$api][1] as $key => $value) {
					if((!$value && clm_class_access::accessWithId($id, $key, $onlyPublished)) || (($value && clm_class_access::accessWithId($id, $key, $onlyPublished) === true))) {
						return true;
					}
				}
				return false;
			case 5:
				if(!isset($input[$rights[$api][4]]) || !isset($input[$rights[$api][5]])) {
					return false;
				}
				return self::tournamentCheckOnlyOne($rights[$api],$input[$rights[$api][4]],$input[$rights[$api][5]]);
			default:
				return false; // Kontrolle unbekannt
		}
		return false;
	}
	// Besitzt die angegebene Benutzergruppe Zugriff auf den angegebenen Zugriffspunkt und wenn wie viel (false,true,2)
	public static function accessWithType($type, $accesspoint, $onlyPublished = true) {
		return clm_class_access::accessWithId(clm_class_access::getUsertypeId($type), $accesspoint, $onlyPublished);
	}
	public static function accessWithId($id, $accesspoint, $onlyPublished = true) {
		$params = clm_class_access::getParamsWithId($id, $onlyPublished);
		$ipos = strpos($params, $accesspoint);
		if ($ipos === false) return false;
		$ipos = $ipos + strlen($accesspoint) + 1;
		$result = substr($params, $ipos, 1);
		if ($result == 0) return false;
		elseif ($result == 1) return true;
		return $result;
	}
	// Vergleiche zwei Benutzergruppen mithilfe deren Parameter auf die ihnen gegebenen Rechte.
	// true = type1 hat die selben oder mehr Rechte als type2
	// false = type1 hat manche Rechte die type2 hat nicht
	public static function compareWithType($type1, $type2, $onlyPublished = true) {
		return clm_class_access::compareWithId(clm_class_access::getUsertypeId($type1), clm_class_access::getUsertypeId($type2), $onlyPublished);
	}
	public static function compareWithId($id1, $id2, $onlyPublished = true) {
		$accesspoints = clm_class_access::getAccesspoints();
		foreach ($accesspoints as $accesspoint) {
			$result1 = clm_class_access::accessWithId($id1, $accesspoint, $onlyPublished);
			if ($result1 === true) {
				continue;
			} // Recht vorhanden -> ob der andere es auch hat ist egal
			$result2 = clm_class_access::accessWithId($id2, $accesspoint, $onlyPublished);
			if ($result2 === false) {
				continue;
			} // Er hat das Recht nicht -> was ich habe ist egal
			if ($result2 === true || $result1 === false) {
				return false;
			} // Der andere hat das Recht oder zumindest mehr davon (Turnier/Liga Verwalter)
			
		}
		return true;
	}
	// Lese die Parameter einer Benutzergruppe aus
	public static function getParamsWithType($type, $onlyPublished = true) {
		return clm_class_access::getParamsWithId(clm_class_access::getUsertypeId($type1), clm_class_access::getUsertypeId($type2));
	}
	public static function getParamsWithId($id, $onlyPublished = true) {
		if ($id > - 1 && (!$onlyPublished || clm_core::$db->usertype->get($id)->published)) {
			return clm_core::$db->usertype->get($id)->params;
		}
		return "";
	}
	// Liste aller Benutzergruppen im String Format (bsp. "sl,tl,mtl")
	// die dem gebebenen NICHT usertype gleich oder unterlegen sind (siehe compareWithId)
	public static function usertypelistWithType($type) {
		return clm_class_access::usertypelistWithId(clm_class_access::getUsertypeId($type));
	}
	public static function usertypelistWithId($id1) {
		$typelist = '';
		foreach (clm_core::$db->usertype->content() as $id2 => $value) {
			if (!clm_class_access::compareWithId($id1, $id2)) {
				$typelist.= '"' . clm_core::$db->usertype->get($id2)->usertype . '",';
			}
		}
		// Schneide das unnötige Komma am Ende ab
		if (strlen($typelist) > 0) {
			$typelist = substr($typelist, 0, (strlen($typelist) - 1));
		}
		return $typelist;
	}
	// Liste aller Benutzer die über einen bestimmen Zugriffspunkt mit bestimmten Werten verfügen und in der aktuellen Saison vorliegen sowie freigegeben sind
	// mit accessvalue werden die gültigen Werte bestimmt (>1,=1,<1 mit 0,1,2)
	public static function userlist($accesspoint, $accessvalue) {
		// Sollen User mit ungültiger Gruppe eingeschlossen werden?
		if ($accessvalue == "=0" || substr($accessvalue, 0, 1) == "<") {
			$out = true;
		} else {
			$out = false;
		}
		$groups = '';
		foreach (clm_core::$db->usertype->content() as $id => $value) {
			$result = clm_class_access::accessWithId($id, $accesspoint, false);
			if (((substr($accessvalue, 0, 1) == '=') && ($result == substr($accessvalue, 1, 1))) || ((substr($accessvalue, 0, 1) == '>') && ($result > substr($accessvalue, 1, 1))) || ((substr($accessvalue, 0, 1) == '<') && ($result < substr($accessvalue, 1, 1)))) {
				// Nur die Gruppen merken in der die Bedingung zutrifft wenn Benutzer ohne gültige Gruppe ausgeschlossen sind			
				if (!$out) {
					$groups.= '"' . clm_core::$db->escape(clm_core::$db->usertype->get($id)->usertype) . '",';
				}
			} else {
				// Nur die Gruppen merken in der die Bedingung NICHT zutrifft wenn Benutzer ohne gültige Gruppe eingeschlossen sind
				if ($out) {
					$groups.= '"' . clm_core::$db->escape(clm_core::$db->usertype->get($id)->usertype) . '",';
				}
			}
		}
		if (strlen($groups) > 0 || $out) {
			if (strlen($groups) == 0) {
				$where = ''; // Wenn Benutzer mit ungültiger Gruppe eingeschlossen sind bedeutet keine Gruppe im Filter das auf alle zugegriffen werden darf
			} else {
				$groups = substr($groups, 0, (strlen($groups) - 1)); // letztes Komma entfernen
				$where = ' AND usertype ' . ($out ? 'OUT' : 'IN') . ' (' . $groups . ')';
			}
			$query = 'SELECT a.id,a.jid,a.name,a.usertype FROM #__clm_user as a
					WHERE sid = ' . clm_core::$access->getSeason() . $where;
			$result = clm_core::$db->loadObjectList($query);
			return $result;
		} else {
			return "";
		}
	}
	// Zu einem Usertype die id finden
	public static function getUsertypeId($type) {
		if (!isset(clm_class_access::$usertypeId[$type])) {
			$query = 'SELECT id FROM #__clm_usertype ' . ' WHERE usertype = "' . clm_core::$db->escape($type) . '"';
			$typeResult = clm_core::$db->loadObjectList($query);
			if (count($typeResult) != 1) {
				clm_class_access::$usertypeId[$type] = - 1;
			} else {
				clm_class_access::$usertypeId[$type] = $typeResult[0]->id;
			}
		}
		return clm_class_access::$usertypeId[$type];
	}
	private static function loadAccesspoints() {
		if (is_null(clm_class_access::$accesspoints)) {
			$path = clm_core::$path . DS . "includes" . DS . 'accesspoints.php';
			require($path);
			clm_class_access::$accesspoints = $accesspoints;
		}
	}
	public static function getAccesspoints() {
		clm_class_access::loadAccesspoints();
		return clm_class_access::$accesspoints;
	}
	private static function loadRights() {
		if (is_null(clm_class_access::$rights)) {
			$path = clm_core::$path . DS . "includes" . DS . 'rights.php';
			require($path);
			clm_class_access::$rights = $rights;
		}
	}
	private static function getRights() {
		clm_class_access::loadRights();
		return clm_class_access::$rights;
	}
	// Standardabfragen
	public function getId() {
		return $this->id;
	}
	public function getJid() {
		return $this->jid;
	}
	public function getUsername() {
		return $this->username;
	}
	public function getName() {
		return $this->name;
	}
	public function getUserZPS() {
		return $this->zps;
	}
	public function getSeason() {
		return $this->season;
	}
	public function getType() {
		return $this->type;
	}
	public function getTypeId() {
		return $this->typeId;
	}
	// Rechtekontrolle API
	private static function tournamentCheck($right, $group, $id) {
		if($group) {
			if(clm_core::$db->liga->get($id)->isNew()) {
				//return array(false,"e_teamtournamentNotExisting");
				return false;
			}
			if (clm_core::$db->liga->get($id)->liga_mt == 0) {
				$right = $right[2];
			} else {
				$right = $right[3];
			}
			if ((clm_core::$db->liga->get($id)->tl != clm_core::$access->getJid() && clm_core::$access->access($right) !== true) || (clm_core::$access->access($right) === false)) {
				return false;
			}
		} else {
			if(clm_core::$db->turniere->get($id)->isNew()) {
				//return array(false,"e_tournamentNotExisting");
				return false;
			}
			if ((clm_core::$db->turniere->get($id)->tl != clm_core::$access->getJid() && clm_core::$access->access($right[1]) !== true ) || (clm_core::$access->access($right[1]) === false)) {
				//return array(false,"e_noRights");
				return false;
			}
		}
		return true;
	}
	private static function tournamentCheckOnlyOne($right, $group, $id) {
		if($group) {
			if(clm_core::$db->liga->get($id)->isNew()) {
				//return array(false,"e_teamtournamentNotExisting");
				return false;
			}
			if (clm_core::$db->liga->get($id)->liga_mt == 0) {
				$right = $right[2];
			} else {
				$right = $right[3];
			}
			if (clm_core::$access->access($right)===false) {
				return false;
			}
		} else {
			if(clm_core::$db->turniere->get($id)->isNew()) {
				//return array(false,"e_tournamentNotExisting");
				return false;
			}
			if (clm_core::$access->access($right[1])===false) {
				return false;
			}
		}
		return true;
	}
}
