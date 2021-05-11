<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerTurDecode extends JControllerLegacy 
{
	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		// turnierid
		$this->turnierid = clm_core::$load->request_int('turnierid');
		
		$this->app 	= JFactory::getApplication();
			
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );

		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turplayers";
	}

	
	function save() {
	
		$result = $this->_saveDo();   
		$app =JFactory::getApplication();
		
		if ($result[0]) { // erfolgreich?
		
			$app->enqueueMessage( JText::_('DECODE_TABLE_UPDATE') );
		} else {
			$app->enqueueMessage( $result[2],$result[1] );					
		}
		$this->adminLink->makeURL();
		$app->redirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );
		// Task
		$task = clm_core::$load->request_string('task');
		$tid = clm_core::$load->request_string('tid');
		$sid = clm_core::$load->request_string('sid');
		
		for ($y=1; $y< 500; $y++){
			$oname	= clm_core::$load->request_string( 'oname'.$y,'XYZ');
			if ($oname == 'XYZ') break;
			$nname	= clm_core::$load->request_string( 'nname'.$y);
			$verein	= clm_core::$load->request_string( 'verein'.$y);
			$tname	= clm_core::$load->request_string( 'tname'.$y);
			if ($tname != '-1' AND $tname != '-2' ) {
				$a_tname = explode(' - ', $tname);
				$nname = $a_tname[0];
				$verein = $a_tname[1];
			}
			if ($nname == '') continue;
			$nname = addslashes($nname);
			$verein = addslashes($verein);
			
			if ($tname != '-2' ) {
				// Zusatzdaten holen
				$zps = '';
				$mgl_nr = 0;
				$PKZ = 0;
				$titel = '';
				$birthyear = '0000';
				$geschlecht = '';
				$start_dwz = 0;
				$start_I0 = 0;
				$FIDEelo = 0;
				$FIDEid = 0;
				$FIDEcco = '';
				$query = 'SELECT a.*, Vereinname as verein '
					. ' FROM #__clm_dwz_spieler as a'
					. ' LEFT JOIN #__clm_dwz_vereine as v ON v.sid = a.sid AND v.ZPS = a.ZPS '
					. " WHERE a.sid = ".$sid
					. " AND a.Spielername = '".$nname."' AND v.Vereinname = '".$verein."'"; 
				$dwzDetails = clm_core::$db->loadObjectList($query);	
				if (isset($dwzDetails[0]->Spielername)) {
					$zps = $dwzDetails[0]->ZPS;
					$mgl_nr = (integer) $dwzDetails[0]->Mgl_Nr;
					$PKZ = $dwzDetails[0]->PKZ;
					$titel = $dwzDetails[0]->FIDE_Titel;
					$birthyear = $dwzDetails[0]->Geburtsjahr;
					$geschlecht = $dwzDetails[0]->Geschlecht;
					$start_dwz = (integer) $dwzDetails[0]->DWZ;
					$start_I0 = (integer) $dwzDetails[0]->DWZ_Index;
					$FIDEelo = (integer) $dwzDetails[0]->FIDE_Elo;
					$FIDEid = (integer) $dwzDetails[0]->FIDE_ID;
					$FIDEcco = $dwzDetails[0]->FIDE_Land;
				}	

				// Update der Recode-Tabelle
//				$query	= "REPLACE INTO #__clm_player_decode"
//					. " ( `sid`, `source`, `oname`, `nname`, `verein`) "
//					. " VALUES (".$sid.",'lichess', '".$oname."', '".$nname."', '".$verein."' )";
				$query	= "INSERT INTO #__clm_player_decode"
					. " ( `sid`, `source`, `oname`, `nname`, `verein`) "
					. " VALUES (".$sid.",'lichess', '".$oname."', '".$nname."', '".$verein."' )"
					. " ON DUPLICATE KEY UPDATE nname = '".$nname."', verein = '".$verein."'";
				clm_core::$db->query($query);			
				// Update der Teilnehmertabelle
				$query	= "UPDATE #__clm_turniere_tlnr"
					. " SET name = '".$nname."', verein = '".$verein."', zps = '".$zps."', mgl_nr = ".$mgl_nr.", PKZ = '".$PKZ."'"
					. " , titel = '".$titel."', birthyear = '".$birthyear."', geschlecht = '".$geschlecht."', start_dwz = ".$start_dwz.", start_I0 = ".$start_I0
					. ", FIDEelo = ".$FIDEelo.", FIDEid = ".$FIDEid.", FIDEcco = '".$FIDEcco."'"
					. " WHERE turnier = ".$tid
					. " AND oname = '".$oname."'";
				clm_core::$db->query($query);
			} elseif ($tname == '-2' ) {
				// Delete in Recode-Tabelle
				$query	= "DELETE FROM #__clm_player_decode"
					." WHERE sid = ".$sid." AND source = 'lichess' AND oname = '".$oname."'";
				clm_core::$db->query($query);			
				// Update der Teilnehmertabelle
				$query	= "UPDATE #__clm_turniere_tlnr"
					. " SET name = '".$oname."', verein = NULL "
					. " WHERE turnier = ".$tid
					. " AND oname = '".$oname."'";
				clm_core::$db->query($query);
			}
		}
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('DECODE_TABLE_UPDATED_LOG');
		$clmLog->params = array('sid' => $sid, 'tid' => $tid); 
		$clmLog->write();
		
		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->view = "turdecode"; // WL in Liste
			$this->adminLink->more = array('turnierid' => $tid);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "turplayers"; // WL in Liste
			$this->adminLink->more = array('id' => $tid);
		}
	
		return array(true);
	
	}
	
	
	function cancel() {
		
		$this->adminLink->view = "turplayers";
		$this->adminLink->more = array('id' => $this->turnierid);
		$this->adminLink->makeURL();
		$this->app->redirect( $this->adminLink->url );
		
	}

}
