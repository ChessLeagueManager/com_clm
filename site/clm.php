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

// kein direkter Zugriff
defined('_JEXEC') or die('Restricted access');

// lädt Funktion zum sichern vor SQL-Injektion
require_once (JPATH_COMPONENT.DS.'includes'.DS.'escape.php');
 
// lädt alle CLM-Klassen - quasi autoload
$classpath = dirname(__FILE__).DIRECTORY_SEPARATOR.'classes';
foreach( JFolder::files($classpath) as $file ) {
	JLoader::register(str_replace('.class.php', '', $file), $classpath.DIRECTORY_SEPARATOR.$file);
}


// laden des Joomla! Basis Controllers
require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php');

$controller 		= JRequest::getVar( 'controller');

// laden von weiteren Controllern
if($controller = JRequest::getVar('controller')) {
	$path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
// Erzeugen eines Objekts der Klasse controller
$classname	= 'CLMController'.ucfirst($controller);
$controller = new $classname( );

// den request task ausleben
$controller->execute(JRequest::getCmd('task'));

// Redirect aus dem controller
$controller->redirect();

?>