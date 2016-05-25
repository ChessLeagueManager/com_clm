<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelTermine extends JModelLegacy
{
	
	function _getTermine( &$options )
	{
		$sid	= JRequest::getInt('saison','1');
		$liga	= JRequest::getInt('liga','1');
		$categoryid	= JRequest::getInt('categoryid',0);
		// CategoryID vorgegeben?
		$addWhere_t = '';
		$addWhere_e = '';
		$addWhere_b = '';
		if ($categoryid > 0) {
			list($parentArray, $parentKeys, $parentChilds) = CLMModelTermine::getTree();
			// für jede Kategorie Unterkategorien ermitteln
			$arrayAllCatid = array();
			if (isset($parentChilds[$categoryid])) {
				$arrayAllCatid = $parentChilds[$categoryid];
				$arrayAllCatid[] = $categoryid;
			} else {
				$arrayAllCatid[] = $categoryid;
			}
			$addWhere_t = ' AND ( ( t.catidAlltime = '.implode( ' OR t.catidAlltime = ', $arrayAllCatid ).' )
					OR 
					( t.catidEdition = '.implode( ' OR t.catidEdition = ', $arrayAllCatid ).' ) )'; 
			$addWhere_e = ' AND ( ( e.catidAlltime = '.implode( ' OR e.catidAlltime = ', $arrayAllCatid ).' )
					OR 
					( e.catidEdition = '.implode( ' OR e.catidEdition = ', $arrayAllCatid ).' ) )'; 
			$addWhere_b = ' AND ( ( b.catidAlltime = '.implode( ' OR b.catidAlltime = ', $arrayAllCatid ).' )
					OR 
					( b.catidEdition = '.implode( ' OR b.catidEdition = ', $arrayAllCatid ).' ) )'; 
		}

		$start	= clm_escape(JRequest::getVar('start','1'));
		$db	= JFactory::getDBO();
		if ($start == '1') $date = date("Y-m-d");
		else $date = $start;
	
		$query = " (SELECT 'liga' AS source, li.datum AS datum, li.sid, li.name, li.nr, '1' as dg, li.liga AS typ_id, t.id, t.name AS typ, t.durchgang AS durchgang, t.published, t.runden AS ligarunde"
				." , t.ordering, li.startzeit AS starttime "
				." FROM #__clm_runden_termine AS li "
				." LEFT JOIN #__clm_liga AS t ON t.id = li.liga "
				." WHERE t.published != '0' AND TO_DAYS(datum)+183 >= TO_DAYS('".$date."')".$addWhere_t." )"
				
				." UNION ALL"
				
				." (SELECT 'lpaar' AS source, rm.pdate AS datum, rm.sid, CONCAT(heim.name,' - ',gast.name) as name, rm.runde, rm.dg, rm.lid AS typ_id, t.id, t.name AS typ, t.durchgang AS durchgang, t.published, t.runden AS ligarunde"
				." , t.ordering, rm.ptime AS starttime "
				." FROM #__clm_rnd_man AS rm LEFT JOIN #__clm_liga AS t ON t.id = rm.lid "
				." LEFT JOIN #__clm_mannschaften AS heim ON heim.liga = rm.lid AND heim.tln_nr = rm.tln_nr "
				." LEFT JOIN #__clm_mannschaften AS gast ON gast.liga = rm.lid AND gast.tln_nr = rm.gegner "
				." WHERE t.published != '0' AND TO_DAYS(pdate)+183 >= TO_DAYS('".$date."')".$addWhere_t." "
				." AND rm.pdate > '1970-01-01' AND rm.heim = 1 )"
				
				." UNION ALL"
				
				." (SELECT 'termin', e.startdate AS datum, '1', e.name, '1', '1', '', e.id, e.address AS typ, '1', e.published, 'event' AS ligarunde "
				." , e.ordering, starttime "
				." FROM #__clm_termine AS e "
				." WHERE e.published != '0' AND TO_DAYS(e.startdate)+183 >= TO_DAYS('".$date."')".$addWhere_e." )"
				
				." UNION ALL"
				
				." (SELECT 'turnier', tu.datum AS datum, tu.sid, tu.name, tu.nr, '1', tu.turnier AS typ_id, b.id, b.name AS typ, tu.dg AS durchgang, b.published, '' "
				." , b.ordering, tu.startzeit AS starttime "
				." FROM #__clm_turniere_rnd_termine AS tu "
				." LEFT JOIN #__clm_turniere AS b ON b.id = tu.turnier "
				." WHERE b.published != '0' AND TO_DAYS(datum)+183 >= TO_DAYS('".$date."')".$addWhere_b." )"
				
				." ORDER BY datum ASC, starttime ASC, ABS(ordering) ASC, ABS(typ_id) ASC, ABS(nr) ASC "
				;

		return $query;
	}
	function getTermine( $options=array() )
	{
		$query	= $this->_getTermine( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	
	function _getTermine_Detail( &$options )
	{
	
		$nr		= JRequest::getInt('nr',-1);
		$db		= JFactory::getDBO();
		$date 	= date("Y-m-d");
		if($nr!=-1){
		$query = " SELECT host FROM #__clm_termine AS a "
				." WHERE a.id =". $nr
				." AND a.published != 0"
				;
		$db->setQuery($query);
		$termin=$db->loadObjectList();
		}

		if (isset($termin[0]) AND strlen($termin[0]->host) == 5)
		$query = " SELECT a.*, b.Vereinname AS hostname "
				." FROM #__clm_termine AS a "
				." LEFT JOIN #__clm_dwz_vereine AS b ON b.ZPS = a.host "
				." WHERE a.id =". $nr
				." AND a.published != 0"
				;
		elseif (isset($termin[0]) AND strlen($termin[0]->host) == 3)
			$query = " SELECT a.*, b.Verbandname AS hostname "
				." FROM #__clm_termine AS a "
				." LEFT JOIN #__clm_dwz_verbaende AS b ON b.Verband = a.host "
				." WHERE a.id =". $nr
				." AND a.published != 0"
				;
		else $query = " SELECT a.*, '-' AS hostname " 
                ." FROM #__clm_termine AS a " 
                ." WHERE a.id =". $nr 
                ." AND a.published != 0" 		
				;
		return $query;
	}
	function getTermine_Detail( $options=array() )
	{
		$query	= $this->_getTermine_Detail( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	
	function _getSchnellmenu( &$options )
	{
		$sid	= JRequest::getInt('saison','1');
		$liga	= JRequest::getInt('liga','1');
		$start	= clm_escape(JRequest::getVar('start','1'));
		$db	= JFactory::getDBO();
		if ($start == '1') $date = date("Y-m-d");
		else $date = $start;
	 
		$query = " (SELECT a.datum AS datum FROM #__clm_runden_termine AS a "
				." LEFT JOIN #__clm_liga AS l ON l.id = a.liga "
				." WHERE l.published != '0' AND  TO_DAYS(datum)+183 >= TO_DAYS('".$date."') )"
				." UNION ALL"
				." (SELECT b.startdate AS datum FROM #__clm_termine AS b "
				." WHERE b.published != '0' AND  TO_DAYS(b.startdate)+183 >= TO_DAYS('".$date."') )"
				." UNION ALL"
				." (SELECT c.datum AS datum FROM #__clm_turniere_rnd_termine AS c "
				." WHERE TO_DAYS(datum)+183 >= TO_DAYS('".$date."') )"
				." ORDER BY datum ASC"
				;
		
		return $query;
	}
	function getSchnellmenu( $options=array() )
	{
		$query	= $this->_getSchnellmenu( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function _getCLMSumPlan ( &$options )
	{
	$sid = JRequest::getInt('saison','1');
	$db		= JFactory::getDBO();
	$date 	= date("Y-m-d");
		$query = " SELECT a.dg,a.lid,a.sid,a.runde,a.paar,a.tln_nr,a.gegner "
			." ,t.name as dat_name, t.datum as datum "
			." ,m.name as hname, n.name as gname, m.published as hpublished, "
			." n.published as gpublished "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_liga as l ON l.sid = a.sid AND l.id = a.lid " //klkl
			." LEFT JOIN #__clm_mannschaften as m ON m.tln_nr = a.tln_nr AND m.sid = a.sid AND m.liga = a.lid "
			." LEFT JOIN #__clm_mannschaften as n ON n.tln_nr = a.gegner AND n.sid = a.sid AND n.liga = a.lid"
			." LEFT JOIN #__clm_runden_termine as t ON t.nr = (a.runde + ((a.dg - 1)*l.runden)) AND t.liga = a.lid AND t.sid = a.sid " //klkl
			." WHERE TO_DAYS(t.datum)+2 >= TO_DAYS(NOW())"
			." AND a.heim = 1"
			." ORDER BY datum ASC, a.lid ASC, a.paar ASC "
			;
		return $query;
	}

	function getCLMSumPlan ( $options=array() )
	{
		$query	= $this->_getCLMSumPlan( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	public static function getTree() {  //das ist eine Kopie von modCLM_TurnierHelper::getTree()
	
		// DB
		$_db				=  JFactory::getDBO();
	
		// alle Cats holen
		$query = "SELECT id, name, parentid FROM #__clm_categories";
		$_db->setQuery($query);
		$parentList = $_db->loadObjectList('id');
	
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
			if (!$value->parentid OR $value->parentid == 0) {
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
					
					$parentArray[$key] = $parentArray[$value->parentid].' > '.$value->name;
					
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
	
		return array($parentArray, $parentKeys, $parentChilds);
	
	}
	
}
