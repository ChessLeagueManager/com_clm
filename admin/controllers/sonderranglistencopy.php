<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSonderranglistenCopy extends JControllerLegacy {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply`', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "sonderranglistenmain";
	
	}


	function edit() {
			// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "sonderranglistenmain"; // WL in Liste
		}
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			$app =JFactory::getApplication();			
			$app->enqueueMessage( JText::_('SP_RANKING_COPIED') );
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		defined('_JEXEC') or die('Restricted access');
	
		$clmAccess = clm_core::$access;      
		if ($clmAccess->access('BE_tournament_edit_detail') === false) {
			$this->setMessage( JText::_( 'TOURNAMENT_NO_ACCESS' ), 'warning' );
			return false;
		}
		$turnier_source = clm_core::$load->request_string('turnier_source');
		$turnier_target = clm_core::$load->request_string('turnier_target');
		$sql = ' SELECT * FROM #__clm_turniere_sonderranglisten '
				.' WHERE turnier = '.$turnier_source; 
		$source = clm_core::$db->loadObjectList($sql); 
		if (count($source) > 0) {
			$counter = 0;
			for ($i = 0;$i < count($source);$i++) {
				if ($source[$i]->checked_out_time == '0000-00-00 00:00:00') $source[$i]->checked_out_time = '1970-01-01 00:00:00';
				$sql = "INSERT INTO #__clm_turniere_sonderranglisten ( `turnier`, `name`, `use_rating_filter`, `rating_type`, `rating_higher_than`, `rating_lower_than`, "
					." `use_birthYear_filter`, `birthYear_younger_than`, `birthYear_older_than`, "
					." `use_sex_filter`, `sex`, `published`, `checked_out`, `checked_out_time`, `ordering`, "
					." `use_zps_filter`, `zps_higher_than`, `zps_lower_than`, "
					." `use_sex_year_filter`, `maleYear_younger_than`, `maleYear_older_than`, `femaleYear_younger_than`, `femaleYear_older_than` )"
					." VALUES ('".$turnier_target."','".$source[$i]->name."','".$source[$i]->use_rating_filter."','".$source[$i]->rating_type."','".$source[$i]->rating_higher_than."','".$source[$i]->rating_lower_than."',"
					." '".$source[$i]->use_birthYear_filter."','".$source[$i]->birthYear_younger_than."','".$source[$i]->birthYear_older_than."',"
					." '".$source[$i]->use_sex_filter."','".$source[$i]->sex."','".$source[$i]->published."','".$source[$i]->checked_out."','".$source[$i]->checked_out_time."','".$source[$i]->ordering."',"
					." '".$source[$i]->use_zps_filter."','".$source[$i]->zps_higher_than."','".$source[$i]->zps_lower_than."',"
					." '".$source[$i]->use_sex_year_filter."','".$source[$i]->maleYear_younger_than."','".$source[$i]->maleYear_older_than."','".$source[$i]->femaleYear_younger_than."','".$source[$i]->femaleYear_older_than."' )";
				clm_core::$db->query($sql);
				$counter++;
			}
		}
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_( 'SPECIALRANKING_COPY' );
		$clmLog->params = array('tid_from' => $turnier_source, 'tid_to' => $turnier_target); // TurnierID wird als LigaID gespeichert
		$clmLog->write();

		// Weiterleitung in Liste
		$this->adminLink->view = "sonderranglistenmain"; // WL in Liste
			
		return true;
	
	}


	function cancel() {
		
		$this->adminLink->view = "sonderranglistenmain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}
