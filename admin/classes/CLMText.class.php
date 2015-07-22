<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


// Klassenbibliothek CLMText für verschiedene textbezogene Funktionalitäten
class CLMText {

	
	
	/**
	* function sgpl
	* weist einer vorgegebenen Zahl einen Singular- oder Pluraltext zu
	*
	*/
	public static function sgpl ($count, $text_sg, $text_pl, $complete_string = TRUE) {
	
		if ($count == 1) {
			$text_return = $text_sg;
		} else {
			$text_return = $text_pl;
		}
	
		if ($complete_string == TRUE) { // kompletter String!
			return $count."&nbsp;".$text_return;
		} else {
			return $text_return;
		}
			
	}
	
	/**
	* function tiebrFormat
	* erstellt formatierten String einer Feinwertung
	*
	*/
	public static function tiebrFormat ($tiebrID, $value) {
	
		switch ($tiebrID) {
			case 1: // buchholz
				$format = "%01.1f";
				break;
			case 2: // buchholz-Summe
				$format = "%01.1f";
				break;
			case 3: // sonneborn-berger alt
				$format = "%01.2f";
				break;
			case 4: // Anzahl Siege
				$format = "%01.0f";
				break;
			case 6: // Elo Schnitt
				$format = "%01.0f";
				break;
			case 7: // Summenwertung
				$format = "%01.1f";
				break;
			case 8: // DWZ Schnitt
				$format = "%01.0f";
				break;
			case 9: // TWZ Schnitt
				$format = "%01.0f";
				break;
			case 11: // Buchholz 1 Streichresultat
				$format = "%01.1f";
				break;
			case 12: // Buchholz-Summe 1 Streichresultat
				$format = "%01.1f";
				break;
			case 13: // sonneborn-berger 1 Streichresultat
				$format = "%01.2f";
				break;
			case 16: // Elo Schnitt 1 Streichresultat
				$format = "%01.0f";
				break;
			case 18: // DWZ Schnitt 1 Streichresultat
				$format = "%01.0f";
				break;
			case 19: // TWZ Schnitt 1 Streichresultat
				$format = "%01.0f";
				break;
			case 23: // sonneborn-berger
				$format = "%01.2f";
				break;
			case 25: // Direkter Vergleich
				if ($value == NULL) $format = '';
				else $format = "%01.0f";
				break;
			case 29: // Prozentpunkte
				$format = "%01.2f";
				break;	
		}
		
		return sprintf($format, $value);
	}	


	/**
	* function selectOpener
	* formatiert einen standartisierten String für Verwendung als Erklärung in einem Dropdown
	*
	*/
	public static function selectOpener($text) {
	
		return " - ".$text." - ";
	
	}

	
	public static function selectEntity($entity) {
	
		return JText::_('SELECT').' '.$entity;
	
	}
	
	
	/**
	* function stringOnchange
	* gibt für ein formularfeld eine JS-onchnage-Anweisung aus, wenn $filter == TRUE
	*
	*/
	public static function stringOnchange ($filter) {
	
		if ($filter) {
			return 'onchange="document.adminForm.submit();"';
		} else {
			return '';
		}
	
	}
	
	
	/**
	* function errorText
	* setzt einen Fehlertext zusammen
	*
	*/
	public static function errorText ($subject, $error) {
	
		return JText::_($subject).": ".JText::_('ERROR_'.$error);
	
	}
	
	
	/**
	* function isTime
	* überprüft, ob Zeitstring korrekt ist
	*
	*/
	public static function isTime ($time, $is24Hours=true, $seconds=false) {
		$pattern = "/^".($is24Hours ? "([0-2][0-3]|[01]?[1-9])" : "(1[0-2]|0?[1-9])").":([0-5]?[0-9])".($seconds ? ":([0-5]?[0-9])" : "")."$/";
		if (preg_match($pattern, $time)) {
        return true;
		}
		return false;
	}
	
	
	

}
?>
