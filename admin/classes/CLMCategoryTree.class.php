<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


/**
 * CategoryTree
*/
	
class CLMCategoryTree {


	function getTree() {
	
		// DB
		$_db				= & JFactory::getDBO();
	
		// alle Cats holen
		$query = "SELECT id, name, parentid FROM #__clm_categories";
		$_db->setQuery($query);
		$parentList = $_db->loadObjectList('id');
	
		// Array speichert alle Kategorien in der Tiefe ihrer Verschachtelung
		$this->parentArray = array();
	
		// Array speichert für alle Kategorien die spezielle einzelne parentID ab
		$this->parentID = array();
		
		// Array speichert für alle Kategorien die Keys aller vorhandenen Parents ab
		$this->parentKeys = array();
		
		// Array speichert für alle Kategorien die Childs ab
		$this->parentChilds = array();
		
		// aufheben für Bearbeitung in parentChilds
		$saved_parentList = $parentList;
		
		// erste Ebene der Parents
		$parentsExisting = array(); // enthält alle IDs von Parents, die bereits ermittelt wurden
		foreach ($parentList as $key => $value) {
			if (!$value->parentid OR $value->parentid == 0) {
				$this->parentArray[$key] = $value->name; // Name an ID binden
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
					
					$this->parentArray[$key] = $this->parentArray[$value->parentid].' > '.$value->name;
					
					// Parent
					$this->parentID[$key] = $value->parentid;
					
					// Key
					$this->parentKeys[$key] = array($value->parentid);
					// hatte Parent schon keys?
					if (isset($this->parentKeys[$value->parentid])) {
						$this->parentKeys[$key] = array_merge($this->parentKeys[$key], $this->parentKeys[$value->parentid]);
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
				foreach ($this->parentKeys[$key] AS $pvalue) {
					$this->parentChilds[$pvalue][] = $key;
				}
			}
		}
	
		return array($this->parentArray, $this->parentKeys, $this->parentChilds);
	
	}

}
?>