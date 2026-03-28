<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
use Joomla\CMS\Factory;

function clm_escape($in)
{
	$db = Factory::getDbo(); 
	return $db->escape($in);
}
?>
