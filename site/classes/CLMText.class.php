<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


/**
* CLMText
* Klassenbibliothek für text-bezogene Funktionalitäten
*/
class CLMText {

	
	
	/**
	* public static function composeHeadTitle
	* erstellt den Titel der Seite -> Browserzeile
	*
	*/
	public static function composeHeadTitle($text, $showSitename = true, $showCLM = true ) {
	
		// INIT
		$string = '';
		
		// Seitennamen anzeigen
		if ($showSitename) {
			$mf = JFactory::getApplication();
			$string .= $mf->getCfg('sitename').' - ';
		}
		
		// 'CLM' anzeigen
		if ($showCLM) {
			$string .= 'CLM - ';
		}
	
		// Text als Array geliefert
		if (is_array($text)) {
			$string .= implode(' / ', $text);
		} elseif ($text != '') { // oder einzelner String
			$string .= $text;
		} else {
			$string .= '...';
		}
	
		return $string;
	
	}
	
	
	
	/**
	* public static function sgpl
	* weist einer vorgegebenen Zahl einen Singular- oder Pluraltext zu
	*
	*/
	public static function sgpl ($count, $text_sg, $text_pl, $complete_string = true) {
	
		if ($count == 1) {
			$text_return = $text_sg;
		} else {
			$text_return = $text_pl;
		}
	
		if ($complete_string == true) { // kompletter String!
			return $count."&nbsp;".$text_return;
		} else {
			return $text_return;
		}
			
	}
	
	/**
	* getResultString()
	* erzeugt Ergebnis-String
	*/
	public static function getResultString($erg, $length = 1) {
	
		$strShort = array("0", "1", "&frac12;", "0", "-", "+", "-", "---", "*", "0", "&frac12;");
		$strLong = array("0:1", "1:0", "&frac12;:&frac12;", "0:0", "-/+", "+/-", "-/-", "---", "*", "0:&frac12;", "&frac12;:0");
		
		switch ($length) {
			case 0:
				$string = $strShort[$erg];
				break;
			case 1:
			default:
				$string = $strLong[$erg];
		}
		
		return $string;
	
	}
	
	
	/**
	* getPosString()
	* erzeugt Platzierungs-String
	* $pos - übergibt Platz
	* $afterPoint - ob Platzzahl mit Ordnungspunkt ergänzt werden soll
	* $stringNoPos - überigibt String, falls keine Position vorliegt
	*/
	public static function getPosString($pos, $afterPoint = 1, $stringNoPos = "") {
	
		if ($pos > 0) {
			$string = $pos;
			switch ($afterPoint) {
				case 1:
					$string .= '.';
					break;
				case 2:
					$string .= '.&nbsp;'.JText::_('TOURNAMENT_POSITION');
					break;
				
			}
		} else {
			$string = $stringNoPos;
		}
		return $string;
	
	}
	
	
	/**
	* public static function tiebrFormat
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
			case 5: // Brettpunkte
				$format = "%01.1f";
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
	* formatRating()
	* formatiert Wertungszahl
	*/
	public static function formatRating($rating) {
	
		if ($rating > 0) {
			return $rating;
		} else {
			return '-';
		}
	
	}
	
	
	
	/**
	* formatNote()
	* formatiert öffentliche Notiz
	*/
	public static function formatNote($text) {
	
		$string = nl2br(JFilterOutput::cleantext($text));
		
		return $string;
	
	}
	
	
	
	public static function addCatToName($addCatToName, $name, $catidAlltime, $catidEdition) {
	
		// init
		$catStrings = array();
		// get Tree
		list($parentArray, $parentKeys, $parentChilds) = CLMCategoryTree::getTree();
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
	
	
	
	
	public static function createCLMLink($string, $view, $params = array()) {
	
		$html = '<a href="index.php?option=com_clm&amp;view='.$view;
		
		// Params?
		if (count($params) > 0) {
			foreach ($params as $key => $value) {
				$html .= '&amp;'.$key.'='.$value;
			}
		}
		$html .= '">';
		$html .= $string;
		$html .= '</a>';
	
		return $html;
	
	}
		
	

}
?>
