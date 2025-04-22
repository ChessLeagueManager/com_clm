<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Joomla-Version ermitteln
$version = new JVersion();
$joomlaVersion = $version->getShortVersion();

// tooltip für 3.x aktivieren
if (substr($joomlaVersion, 0, 1) < 4) {
    JHtml::_('behavior.tooltip', '.CLMTooltip');
}

// eine ähnliche tooltip-Lösung ist für Joomla 4 noch nicht bekannt
