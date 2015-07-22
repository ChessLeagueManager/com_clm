<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// Pfad zu den Menue Bildern

	$document = &JFactory::getDocument();
	$cssDir = JURI::base().'components/com_clm/images';
//	$cssDir = JURI::base().DS. 'components'.DS.'com_clm'.DS.'images';
	$document->addStyleSheet( $cssDir.'/clm_icon.css', 'text/css', null, array() );
//	$document->addStyleSheet( $cssDir.DS.'clm_icon.css', 'text/css', null, array() );
?>