<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper;

	// Joomla-Version ermitteln
	$version = new Version();
	$joomlaVersion = $version->getShortVersion();

	// tooltip für 3.x aktivieren
	if (substr($joomlaVersion,0,1) < 4) {  
		HTMLHelper::_('behavior.tooltip', '.CLMTooltip');
	}

// eine ähnliche tooltip-Lösung ist für Joomla 4 noch nicht bekannt
