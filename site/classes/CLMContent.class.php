<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


/**
* CLMContent 
* Klassenbibliothek für content-bezogene, modulare, Funktionalitäten
* keine Printausgabe, immer nur String-Rückgabe
*/
class CLMContent {

	
	/**
	* componentheading()
	* erstellt cpmponentheading
	*/
	public static function componentheading($text) {
	
		$string = '<div class="componentheading">';
		$string .= $text;
		$string .= '</div>';
	
		return $string;
	
	}
	
	
	/**
	* clmWarning()
	* erstellt Hinweistext
	*/
	public static function clmWarning($text) {
		
		$string = '<div id="wrong">';
		$string .= $text;
		$string .= '</div>';
	
		return $string;
	
	}
	
	
	/**
	* createPDFLink()
	* erstellt Link auf PDF
	*/
	public static function createPDFLink($view, $title, $params = array()) {
	
		// open div
		$string = '<div class="pdf">';
		
		// imageTag zusammensetzen
		$imageTag = '<img src="'.CLMImage::imageURL('pdf_button.png').'" width="16" height="19" title="'.JText::_('PDF_PRINT').$title.'" alt="PDF" class="CLMTooltip" />';
		
		// Format ergänzen
		$params['format'] = 'pdf';
		
		$string .= CLMText::createCLMLink($imageTag, $view, $params);
		
		// close div
		$string .= '</div>';
	
		return $string;
	
	}
	
	/**
	* createViewLink()
	* erstellt Link anderen View
	*/
	public static function createViewLink($view, $title, $params = array()) {
	
		// open div
		$string = '<div class="pdf">';
		
		// imageTag zusammensetzen
		$imageTag = '<img src="'.CLMImage::imageURL('goto_view.jpg').'" width="16" height="19" title="'.$title.'" alt="Goto" class="CLMTooltip" />';
			
		$string .= CLMText::createCLMLink($imageTag, $view, $params);
		
		// close div
		$string .= '</div>';
	
		return $string;
	
	}
	
	
	/**
	* createPGNLink()
	* erstellt Link auf PGN-Vorlage
	* Parameter pgn optional
	*/
	public static function createPGNLink($view, $title, $params = array(), $pgn = 1) {
	
		// open div
		$string = '<div class="pdf">';
		
		// imageTag zusammensetzen
		$imageTag = '<img src="'.CLMImage::imageURL('pgn.gif').'" width="16" height="19" title="'.$title.'" alt="PGN" class="CLMTooltip" />';
		
		// Paramter pgn ergänzen
		$params['pgn'] = $pgn;
		
		$string .= CLMText::createCLMLink($imageTag, $view, $params);
		
		// close div
		$string .= '</div>';
	
		return $string;
	
	}
	
	
	/**
	* clmFooter()
	* erstellt clmFooter mit Versionsnummer und Link
	*/
	/*
	public static function clmFooter() {
	
		$Dir = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_clm';
		$data = JApplicationHelper::parseXMLInstallFile($Dir.DS.'clm.xml');
		
		$string = '<br /><br /><br /><hr>';
		$string .= '<div style="float:left; text-align:left; padding-left:1%">CLM '.$data['version'].'</div>';
		$string .= '<div style=" text-align:right; padding-right:1%">';
		$string .= '<label for="name" class="hasTip" title="'.JText::_('Das Chess League Manager (CLM) Projekt ist freie, kostenlose Software unter der GNU / GPL. Besuchen Sie unsere Projektseite www.fishpoke.de für die neueste Version, Dokumentationen und Fragen. Wenn Sie an der Entwicklung des CLM teilnehmen wollen melden Sie sich bei uns per E-mail. Wir sind für jede Hilfe dankbar !').'">Sie wollen am Projekt teilnehmen oder haben Verbesserungsvorschläge? - <a href="http://www.fishpoke.de">CLM Projektseite</a></label></div>';
	
		return $string;
	
	}
	*/
	
	

}
?>
