<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMTurniere extends JTable
{
	var $id			= null;
	var $name		= '';
	var $sid		= '';
	var $dateStart 	= '';
	var $dateEnd 	= '';
	var $catidAlltime = 0;
	var $catidEdition = 0;
	var $typ		= '';
	var $tiebr1		= '';
	var $tiebr2		= '';
	var $tiebr3		= '';
	var $rnd		= null; // Bugfix HF
	var $teil		= '';
	var $runden		= '';
	var $dg			= 1;
	var $tl			= '';
	var $bezirk		= '';
	var $bezirkTur = '1';
	var $vereinZPS = null;
	var $published		= '';
	// var $invitationText = ''; // soll nicht aus turform heraus gelöscht werden...
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= 0;
	var $checked_out_time	= 0;
	var $ordering		= null;
	var $params 		= null;
	var $sieg			= 1.0;
	var $siegs			= 1.0;
	var $remis 			= 0.5;
	var $remiss			= 0.5;
	var $nieder			= 0.0;
	var $niederk 		= 0.0;

	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 */

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	 // wegen Abwärtskompatibilität kein Überschreiben und Verwenden von check()
	 // kann bei Bedarf geändert werden, wenn alte Turnier-Implementierung gekappt wird.
	function checkData() {

		// aktuelle Turnierdaten laden
		$tournament = new CLMTournament($this->id, true);

		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		
		} elseif ($this->sid <= 0) { // SaisonID > 0
			$this->setError( CLMText::errorText('SEASON', 'IMPOSSIBLE') );
			return false;
		
		} elseif (!is_numeric($this->teil)) { // Teilnehmerzahl = Zahl
			$this->setError( CLMText::errorText('PARTICIPANT_COUNT', 'NOTANUMBER') );
			return false;
		} elseif ($this->teil <= 1) { // Teilnehmerzahl >= 2
			$this->setError( CLMText::errorText('PARTICIPANT_COUNT', 'TOOLOW') );
			return false;
		} elseif ($this->teil > 500) { // Teilnehmerzahl 
			$this->setError( CLMText::errorText('PARTICIPANT_COUNT', 'TOOBIG') );
			return false;
		} elseif ($this->teil < $tournament->getPlayersIn()) {
			$this->setError( CLMText::errorText('PARTICIPANT_COUNT', 'MOREPLAYERSIN') );
			return false;
		
		} elseif (!is_numeric($this->runden)) { // Runden = Zahl
			$this->setError( CLMText::errorText('ROUNDS_COUNT', 'NOTANUMBER') );
			return false;
		} elseif ($this->runden < 1) { // Runden >= 2
			$this->setError( CLMText::errorText('ROUNDS_COUNT', 'TOOLOW') );
			return false;
		} elseif ($this->runden > 50) { // Runden 
			$this->setError( CLMText::errorText('ROUNDS_COUNT', 'TOOBIG') );
			return false;
		
		} elseif ($this->typ != 3 AND ($this->dg < 1 OR $this->dg > 4)) { // DG möglich
			$this->setError( CLMText::errorText('STAGE_COUNT', 'IMPOSSIBLE') );
			return false;
		
		
		// Runden schon erstellt? dann keine Änderung Typ, Runden, dg möglich!
		} elseif ($this->typ != $tournament->data->typ AND $tournament->data->rnd == 1) {
			$this->setError( CLMText::errorText('MODUS', 'ROUNDSCREATED') );
			return false;
		} elseif ($this->runden != $tournament->data->runden AND $tournament->data->rnd == 1) {
			$this->setError( CLMText::errorText('ROUNDS_COUNT', 'ROUNDSCREATED') );
			return false;
		} elseif ($this->dg != $tournament->data->dg AND $tournament->data->rnd == 1) {
			$this->setError( CLMText::errorText('STAGE_COUNT', 'ROUNDSCREATED') );
			return false;
		} elseif ($this->teil != $tournament->data->teil AND $tournament->data->rnd == 1) {
			$this->setError( CLMText::errorText('PARTICIPANT_COUNT', 'ROUNDSCREATED') );
			return false;
		
/*		} elseif ($this->tl <= 0) {
			$this->setError( CLMText::errorText('TOURNAMENT_DIRECTOR', 'MISSING') );
			return false;
*/		
		} elseif (
					($this->tiebr2 != 0 AND $this->tiebr2 == $this->tiebr1) OR
					($this->tiebr3 != 0 AND ($this->tiebr3 == $this->tiebr1 OR $this->tiebr3 == $this->tiebr2))
					) {
			$this->setError( CLMText::errorText('TIEBREAKERS', 'NOTDISTINCT') );
			return false;
		}

		// Endtag gegegeben und Datum geändert?
		if ($this->dateEnd != '0000-00-00' AND $this->dateEnd != '' AND ($this->dateStart != $tournament->data->dateStart OR $this->dateEnd != $tournament->data->dateEnd) ) {
			// zerlegen
			list($startY, $startM, $startD) = explode("-", $this->dateStart);
			list($endY, $endM, $endD) = explode("-", $this->dateEnd);
			// Endtag kleiner Starttag?
			if ( mktime(0, 0, 0, $startM, $startD, $startY) > mktime(0, 0, 0, $endM, $endD, $endY)) {
				$this->setError( CLMText::errorText('TOURNAMENT_DAYEND', 'TOOLOW') );
				return false;
			}
		}

		// wurde eine Feinwertung verändert?
		if ($this->tiebr1 != $tournament->data->tiebr1 OR $this->tiebr2 != $tournament->data->tiebr2 OR $this->tiebr3 != $tournament->data->tiebr3) {
			// Rangliste neu berechnen
			$tournament->setRankingPositions();
		}

		return true;
	
	}


}
