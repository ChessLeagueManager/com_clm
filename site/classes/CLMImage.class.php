<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


/**
* CLMImage
* Klassenbibliothek für bild-bezogene Funktionalitäten
*/
use Joomla\CMS\Uri\Uri;

class CLMImage {
	
	/**
	* imageURL()
	* Stellt die URL eines Frontend-Images zusammen
	*/
	public static function imageURL($image) {
	
		$string = Uri::root(true).'components/com_clm/images/'.$image;
		
		return $string;
	
	}
	
}
?>
