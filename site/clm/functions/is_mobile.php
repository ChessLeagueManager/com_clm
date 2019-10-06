<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
//Über die Abfrage des Betriebssystem wird erkannt, ob das Gerät der Benutzer ein mobiles Teil (Smartphone,Tablet) mit Touchscreen oder ein PC mit Mouse ist
function clm_function_is_mobile()
{
    if (!isset($_SERVER["HTTP_USER_AGENT"])) 
		return false;
	else 
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
?>