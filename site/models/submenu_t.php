<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();

	// Sonderranglisten
	$turnierid = JRequest::getInt('turnier', 0);
	$spRang = JRequest::getInt('spRang', 0); 	
	
    $db	= JFactory::getDBO();
        
    $query = "	SELECT 
					`id`, `name`
				FROM 
					`#__clm_turniere_sonderranglisten`"
			." 	WHERE `turnier` = ". $turnierid
			."    AND published = 1"
			." 	ORDER BY ordering"
			;	
    
    $db->setQuery($query);
    $sub_spRang = $db->loadObjectList();
		
	// Runden
	$turnierid = JRequest::getInt('turnier', 0);
	
    $db	= JFactory::getDBO();
        
    $query = "	SELECT 
					`dg`, `nr`, `name`
				FROM 
					`#__clm_turniere_rnd_termine`"
			." 	WHERE `turnier` = ". $turnierid
			."    AND published = 1"
			."  ORDER BY dg, nr"
			;	
    
    $db->setQuery($query);
    $sub_rounds = $db->loadObjectList();
	
?>
