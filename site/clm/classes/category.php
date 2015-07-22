<?php
class clm_class_category {
	private static $gen = false;
	private static $parentArray;
	private static $parentKeys;
	private static $parentChilds;
	
	private static function gen() {
		$query = "SELECT id, name, parentid FROM #__clm_categories";
		$parentList = clm_core::$db->loadObjectList($query);

		$new = array();
		for($i=0;$i<count($parentList);$i++) {
			$new[$parentList[$i]->id] = $parentList[$i];
		}
		$parentList = $new;

		// Array speichert alle Kategorien in der Tiefe ihrer Verschachtelung
		$parentArray = array();
	
		// Array speichert für alle Kategorien die spezielle einzelne parentID ab
		$parentID = array();
		
		// Array speichert für alle Kategorien die Keys aller vorhandenen Parents ab
		$parentKeys = array();
		
		// Array speichert für alle Kategorien die Childs ab
		$parentChilds = array();
		
		// aufheben für Bearbeitung in parentChilds
		$saved_parentList = $parentList;
		
		// erste Ebene der Parents
		$parentsExisting = array(); // enthält alle IDs von Parents, die bereits ermittelt wurden
		foreach ($parentList as $key => $value) {
			if (!$value->parentid || $value->parentid == 0) {
				$parentArray[$key] = $value->name; // Name an ID binden
				$parentsExisting[] = $value->id; // ID als existierender Parent eintragen
				// Eintrag kann nun aus Liste gelöscht werden!
				unset($parentList[$key]);
				
			}
		}
	
		$continueLoop = 1; // Flag, ob Schleife weiterlaufen soll
	
		// noch Einträge vorhanden?
		WHILE (count($parentList) > 0 AND $continueLoop == 1) { 
			
			$continueLoop = 0; // abschalten - erst wieder anschalten, wenn Eintrag gefunden
			
			
			// weitere Ebenen
			foreach ($parentList as $key => $value) {
				
				// checken, ob ParentID in Array der bereits ermittelten Parents vorhanden
				if (in_array($value->parentid, $parentsExisting)) {
					
					$parentArray[$key] = $parentArray[$value->parentid].' / '.$value->name;
					
					// Parent
					$parentID[$key] = $value->parentid;
					
					// Key
					$parentKeys[$key] = array($value->parentid);
					// hatte Parent schon keys?
					if (isset($parentKeys[$value->parentid])) {
						$parentKeys[$key] = array_merge($parentKeys[$key], $parentKeys[$value->parentid]);
					}
					$parentsExisting[] = $value->id;
					
					// Eintrag kann nun aus Liste gelöscht werden!
					unset($parentList[$key]);
					
					$continueLoop = 1; // Flag, ob Schleife weiterlaufen soll
					
				}
			}
		
		}
	
	
		// alle Childs
		foreach ($saved_parentList as $key => $value) {
			// nur welche, die auch Kind sind, können Kindschaft den Parents anhängen
			if ($value->parentid > 0) {
				// allen Parents dieses Childs diesen Eintrag anhängen
				foreach ($parentKeys[$key] AS $pvalue) {
					$parentChilds[$pvalue][] = $key;
				}
			}
		}
		self::$parentArray=$parentArray;
		self::$parentKeys=$parentKeys;
		self::$parentChilds=$parentChilds;
		self::$gen = true;
	}

	public static function get() {
		if(!self::$gen) {
			self::gen();
		}
		return array(self::$parentArray, self::$parentKeys, self::$parentChilds);
	}

	public static function addCatToName($addCatToName, $name, $catidAlltime, $catidEdition) {
	
		// init
		$catStrings = array();
		// get Tree
		list($parentArray, $parentKeys, $parentChilds) = self::get();

		if ($catidAlltime > 0) {
			$catStrings[] = $parentArray[$catidAlltime];
		}
		if ($catidEdition > 0) {
			$catStrings[] = $parentArray[$catidEdition];
		}
		// set
		$catName = implode(', ', $catStrings);
		// edit name
		if ($addCatToName == 1) {
			$string = $catName." - ".$name;
		} else {
			$string = $name." - ".$catName;
		}
	
		return $string;
	}

	public static function name($id,$group=true) {
		if($group) {
			$table = "liga";		
		} else {
			$table = "turniere";		
		}
		
		$turParams = new clm_class_params(clm_core::$db->$table->get($id)->params);
		$addCatToName = $turParams->get('addCatToName', 0);

		if ($addCatToName != 0 && (clm_core::$db->$table->get($id)->catidAlltime > 0 || clm_core::$db->$table->get($id)->catidEdition > 0)) {
			return self::addCatToName($addCatToName, clm_core::$db->$table->get($id)->name, clm_core::$db->$table->get($id)->catidAlltime, clm_core::$db->$table->get($id)->catidEdition);
		}
		return clm_core::$db->$table->get($id)->name;
	}
}
?>
