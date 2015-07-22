<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
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

class CLMModelTurnier_Tabelle extends JModelLegacy {
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = JRequest::getInt('turnier', 0);
		$this->spRang = JRequest::getInt('spRang', 0); 		//Sonderranglisten

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
	
		$query = "SELECT rankingPos, snr, name, birthYear, geschlecht, sid, zps, verein, twz, titel, anz_spiele, sum_punkte, sumTiebr1, sumTiebr2, sumTiebr3"
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			.$this->_getSpecialRankingWhere()		//Sonderranglisten
			." ORDER BY rankingPos ASC, sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC";
			;
		
		$this->_db->setQuery($query);
		$this->players = $this->_db->loadObjectList();
		
		$this->turnier->playersCount = count($this->players);
		
		
		//RankingPos neu berechnen für Sonderranglisten
		if($this->turnier->playersCount != 0){
			if($this->spRang != 0){
				$spRankingPos = 0;		
				$rankingPosBefor = 0;	
				foreach($this->players as $key => $player) {
					if($rankingPosBefor != $player->rankingPos){
						$spRankingPos++;
					}
					$rankingPosBefor = $player->rankingPos;
					$this->players[$key]->rankingPos = $spRankingPos;
				}
			}
		}
	}
	
	
	//Sonderranglisten
	function _getSpecialRankingWhere()	{
		$where = "";
		if($this->spRang != 0){
		
			$query = "	SELECT *
						FROM
							`#__clm_turniere_sonderranglisten`
						WHERE
							`turnier` = ".$this->turnierid." AND 
							`id` = ".$this->spRang;
						
			$this->_db->setQuery($query);
			$this->spRank = $this->_db->loadObject();
			$this->turnier->spRangName = $this->spRank->name;
		
			if($this->spRank->use_rating_filter == 1){
				if($this->spRank->rating_type == 0){
					$where = $where ." AND start_dwz >= '".$this->spRank->rating_higher_than."'"
									." AND start_dwz <= '".$this->spRank->rating_lower_than."'"
									." AND FIDEelo >= '".$this->spRank->rating_higher_than."'"
									." AND FIDEelo <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 1){
					$where = $where ." AND start_dwz >= '".$this->spRank->rating_higher_than."'"
									." AND start_dwz <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 2){
					$where = $where ." AND FIDEelo >= '".$this->spRank->rating_higher_than."'"
									." AND FIDEelo <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 3){
					$where = $where ." AND IF(start_dwz > 0, start_dwz, FIDEelo) >= '".$this->spRank->rating_higher_than."'"
									." AND IF(start_dwz > 0, start_dwz, FIDEelo) <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 4){
					$where = $where ." AND IF(FIDEelo > 0, FIDEelo, start_dwz) >= '".$this->spRank->rating_higher_than."'"
									." AND IF(FIDEelo > 0, FIDEelo, start_dwz) <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 5){
					$where = $where ." AND IF(FIDEelo > start_dwz, FIDEelo, start_dwz) >= '".$this->spRank->rating_higher_than."'"
									." AND IF(FIDEelo > start_dwz, FIDEelo, start_dwz) <= '".$this->spRank->rating_lower_than."'";
				}
			}
			if($this->spRank->use_birthYear_filter == 1){
				$where = $where ." AND birthYear >= '".$this->spRank->birthYear_younger_than."'"
								." AND birthYear <= '".$this->spRank->birthYear_older_than."'";			
			}
			if($this->spRank->use_sex_filter == 1){
				if($this->spRank->sex == 'M'){
					$where = $where ." AND geschlecht = 'M'";
				} elseif($this->spRank->sex == 'W'){
					$where = $where ." AND geschlecht = 'W'";	
				}
			}
			if($this->spRank->use_sex_year_filter == 1){
				$where = $where ." AND ( ( ( geschlecht = 'M' OR geschlecht = '' )"
								." AND birthYear >= '".$this->spRank->maleYear_younger_than."'"			
								." AND birthYear <= '".$this->spRank->maleYear_older_than."' )"			
								." OR ( geschlecht = 'W' "
								." AND birthYear >= '".$this->spRank->femaleYear_younger_than."'"			
								." AND birthYear <= '".$this->spRank->femaleYear_older_than."' ) )";			
			}
			if($this->spRank->use_zps_filter == 1){
				$where = $where ." AND zps >= '".$this->spRank->zps_higher_than."'"
								." AND zps <= '".$this->spRank->zps_lower_than."'";			
			}
			
		}
		return $where;
	}
	
}
?>
