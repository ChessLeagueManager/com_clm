<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


/**
* CLMImage
* Klassenbibliothek für bild-bezogene Funktionalitäten
*/
class CLMImage {

	
	/**
	* imageURL()
	* Stellt die URL eines Frontend-Images zusammen
	*/
	public static function imageURL($image) {
	
		$string = JUri::root().'components/com_clm/images/'.$image;
		
		return $string;
	
	}
	
}
?>
