<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport( 'joomla.html.parameter' );

class CLMModelTurnier_Teilnehmer extends JModelLegacy {
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = clm_core::$load->request_int('turnier');

		$this->_getTurnierData();

		$this->_getTurnierPlayers();
	}
	
	function _getTurnierData() {
	
		$query = "SELECT *"
			." FROM #__clm_turniere"
			." WHERE id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// turniernamen anpassen?
		$turParams = new clm_class_params($this->turnier->params);
		$addCatToName = $turParams->get('addCatToName', 0);
		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}
	}
	
	
	function _getTurnierPlayers() {
	
		$query = "SELECT *"
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			." ORDER BY snr ASC"
			;
		$this->_db->setQuery( $query );
		$this->players = $this->_db->loadObjectList('snr');
	
		$query = "SELECT *"
			." FROM `#__clm_turniere_sonderranglisten`"
			." WHERE turnier = ".$this->turnierid
			." AND shortname > '' "
			." AND published = 1 "
			." ORDER BY id ASC"
			;
		$this->_db->setQuery( $query );
		$this->gruppen = $this->_db->loadObjectList();
		$this->s_gruppen = 0;
		if (count($this->players) > 0) {
			foreach($this->players as $player) {
				if ($player->FIDEcco == NULL OR $player->FIDEid == NULL) {
					$query = "SELECT *"
						." FROM #__clm_dwz_spieler"
						." WHERE sid = ".$player->sid
						." AND ZPS = '".$player->zps."' AND Mgl_nr = ".$player->mgl_nr
						;
					$this->_db->setQuery( $query );
					$this->spieler = $this->_db->loadObject();
					if (isset($this->spieler)) {
						$player->FIDEcco = $this->spieler->FIDE_Land;
						$player->FIDEid = $this->spieler->FIDE_ID;
					}
				}
				$player->gruppen = '';
				foreach($this->gruppen as $gruppe) {
//echo "<br>gruppe"; var_dump($gruppe); //die();
					$count = 0;
					if ($gruppe->use_rating_filter == '1') {
						if($gruppe->rating_type == 0){
							if ($player->start_dwz >= $gruppe->rating_higher_than 
								AND $player->start_dwz <= $gruppe->rating_lower_than
								AND $player->FIDEelo >= $gruppe->rating_higher_than
								AND $player->FIDEelo <= $gruppe->rating_lower_than)
								$count++;
						} elseif($gruppe->rating_type == 1){
							if ($player->start_dwz >= $gruppe->rating_higher_than
								AND $player->start_dwz <= $gruppe->rating_lower_than)
								$count++;
						} elseif($gruppe->rating_type == 2){
							if ($player->FIDEelo >= $gruppe->rating_higher_than
								AND $player->FIDEelo <= $gruppe->rating_lower_than)
								$count++;
						} elseif($gruppe->rating_type == 3){
							if ($player->start_dwz > 0) {
								if ($player->start_dwz >= $gruppe->rating_higher_than
									AND $player->start_dwz <= $gruppe->rating_lower_than)
								$count++; }
							else {
								if ($player->FIDEelo >= $gruppe->rating_higher_than
									AND $player->FIDEelo <= $gruppe->rating_lower_than)
								$count++; }
						} elseif($gruppe->rating_type == 4){
							if ($player->FIDEelo > 0) {
								if ($player->FIDEelo >= $gruppe->rating_higher_than
									AND $player->FIDEelo <= $gruppe->rating_lower_than)
								$count++; }
							else {
								if ($player->start_dwz >= $gruppe->rating_higher_than
									AND $player->start_dwz <= $gruppe->rating_lower_than)
								$count++; }
						} elseif($gruppe->rating_type == 5){
							if ($player->FIDEelo > $player->start_dwz) {
								if ($player->FIDEelo >= $gruppe->rating_higher_than
									AND $player->FIDEelo <= $gruppe->rating_lower_than)
								$count++; }
							else {
								if ($player->start_dwz >= $gruppe->rating_higher_than
									AND $player->start_dwz <= $gruppe->rating_lower_than)
								$count++; }
						}						
					} else {
						$count++;
					}
					if ($gruppe->use_birthYear_filter == '1'){
						if ($player->birthYear >= $gruppe->birthYear_younger_than
							AND $player->birthYear <= $gruppe->birthYear_older_than)			
							$count++; 
					} else {
						$count++;
					}
					if ($gruppe->use_sex_filter == '1') {
						if ($gruppe->sex == $player->geschlecht) 
							$count++; 
					} else {
						$count++;
					}
					if($gruppe->use_sex_year_filter == '1') {
						if ((($player->geschlecht == 'M' OR $player->geschlecht == '') 
							AND $player->birthYear >= $gruppe->maleYear_younger_than			
							AND $player->birthYear <= $gruppe->maleYear_older_than)		
							OR ($player->geschlecht == 'W' 
							AND $player->birthYear >= $gruppe->femaleYear_younger_than			
							AND $player->birthYear <= $gruppe->femaleYear_older_than) ) 			
							$count++; 
					} else {
						$count++;
					}
					if ($gruppe->use_zps_filter == '1') {
						if ($player->zps >= $gruppe->zps_higher_than
							AND $player->zps <= $gruppe->zps_lower_than)			
							$count++; 
					} else {
						$count++;
					}
				
					if ($count == 5) {
						if ($player->gruppen == '') $player->gruppen = $gruppe->shortname;
						else $player->gruppen .= ','.$gruppe->shortname; 
						$this->s_gruppen = 1;
					}
				}
			}
		}
	}

}
?>
