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

defined('_JEXEC') or die();

	if (!isset($lid)) $lid = 0;
	// Ligen
    $db	= JFactory::getDBO();
    $id	= @$options['id'];
        
    $query = "SELECT a.*, b.datum as dsb_datum FROM #__clm_liga as a"
			." LEFT JOIN #__clm_saison as b ON b.id = a.sid"
			." WHERE a.sid = ". $sid
			." AND a.published = 1"
			." ORDER BY a.ordering";	
    
    $db->setQuery($query);
    $sub_liga = $db->loadObjectList();
		
	// Mannschaften
    $db		= JFactory::getDBO();
    $id		= @$options['id'];
        
    $query = " SELECT a.name, a.liga, a.sid, a.tln_nr FROM #__clm_mannschaften as a"
			." WHERE a.liga = ".$lid 
			." AND a.published = 1"
			." ORDER BY a.name ASC"
			;
    
    $db->setQuery($query);
    $sub_msch = $db->loadObjectList();
		
	// Rundenliste
    $db		= JFactory::getDBO();
    $id		= @$options['id'];
        
    $query = " SELECT a.name, a.liga, a.sid, a.nr, b.runden as runden, b.durchgang as durchgang, b.runden_modus" //klkl
			." FROM #__clm_runden_termine as a"                                                  //klkl
			." LEFT JOIN #__clm_liga as b ON b.id = a.liga"                                      //klkl
			." WHERE a.liga = ".$lid
			." AND b.published = 1"
			." ORDER BY nr ASC"
			;
    
    $db->setQuery($query);
    $sub_runden = $db->loadObjectList();
		
	// Saisons
	$db	= JFactory::getDBO();
	$id	= @$options['id'];
		
	$query = " SELECT a.name, a.id, a.archiv FROM #__clm_saison AS a"
			." WHERE a.published = 1"
			." ORDER BY a.name DESC ";	
	
	$db->setQuery($query);
	$saisonlist = $db->loadObjectList();
?>
