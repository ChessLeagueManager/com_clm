<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
class clm_core {
	// Pfad zum CLM Ordner
	public static $path;
	// URL zum CLM Ordner
	public static $url;
	// CLM Funktionen laden
	public static $load;
	// ID Generator (CSS IDs)
	public static $id;
	// Datenbankverbindung mit eigener Verwaltung
	public static $db;
	// Sprachdateien verwenden
	public static $lang;
	// Berechtigungs- und Benutzerdaten
	public static $access;
	// API Zugriffe (gekoppelt an Berechtigungsdaten)
	public static $api;
	// Auflösung der Abhängigkeiten nach Joomla
	public static $cms;
	// Logging / gebrückt durch diese Klasse, daher private
	private static $log;
	function __construct($url, $path) {
		self::$url = $url;
		self::$path = $path;
		self::$load = $this;
		// CLM Klassen automatisch einbinden
		spl_autoload_register("clm_core::autoload");
		self::$cms = clm_class_cms::init();
		self::$id = new clm_class_id();
		// Grundlegende Konfiguration
		require (self::$path . "/includes/database.php");
		require (self::$path . "/includes/table.php");
		require (self::$path . "/includes/config.php");
		self::$db = new clm_class_db($database, $table, $config);
		self::$lang = new clm_class_lang();
		// Weitere Initialisierung nur bei abgeschlossener Installation
		if (!defined("clm_install")) {
			self::$access = new clm_class_access();
			self::$api = new clm_class_api();
			// Erzeugung von Beispieldaten nach Installation / Korrektur bei inkonsistenter Datenbank
			if (self::$access->getId() == - 1 && self::$access->getType() == "admin") {
				if (self::$access->getSeason() == - 1) {
					$lang = self::$lang->designation;
					self::$api->direct("db_season_save", array(-1, 1, 0, $lang->ExampleSeason, "", "", "1970-01-01 00:00:00"));
				} else {
					self::$api->direct("db_season_enable", array(self::$access->getSeason()));
				}
			}
			self::$cms->afterBoot();
			// Log prinzipiell aktiv?
			if(clm_core::$db->config()->log) {
				self::$log = new clm_class_log();
				set_error_handler('clm_core::errorHandler');
				set_exception_handler('clm_core::exceptionHandler');
			register_shutdown_function('clm_core::shutdown');
			}
		} else {
			self::$api = new clm_class_api();
			self::$api->check(false); // Rechtekontrolle deaktivieren
		}
	}
	function __destruct() {
		// Änderungen an der DB Schreiben
		self::$db->write();
		// CLM Klassen brauchen nicht mehr geladen zu werden.
		spl_autoload_unregister ("clm_core::autoload");
	}
	// benötigte Funktionen automatisch Nachladen
	function __call($function, $args) {
		if (!function_exists("clm_function_" . $function)) {
			$path = self::$path . DS . "functions" . DS . $function . '.php';
			if(file_exists ($path)) {
				require_once (self::$path . DS . "functions" . DS . $function . '.php');
			} else {
				clm_core::addError("function missing",$path." (".$function.")"." Backtrace: ".self::getBacktrace());
			}
		}
		return call_user_func_array("clm_function_" . $function, $args);
	}
	public static function autoload($class) {
  		if(!class_exists($class) && substr($class , 0, 10)=="clm_class_") {
			$path = clm_core::$path . DS . "classes" . DS . substr ($class , 10) . '.php';			
			if(file_exists ($path)) {
				require_once ($path);
			} else {
				clm_core::addError("class missing",$path." (".$class.")"." Backtrace: ".self::getBacktrace());
			}
		}
	}
	
	// Interface des Error Handlers, Weiterleitung zum Beispiel bei der Installation inaktiv
	public static function errorHandler($errno , $errstr, $errfile="", $errline="", $errcontext="") {
		if(clm_core::$log==null) {
			return false;
		}
		return clm_core::$log->errorHandler($errno , $errstr, $errfile, $errline, $errcontext);
	}
	public static function exceptionHandler($e) {
		if(clm_core::$log==null) {
			return false;
		}
		return clm_core::$log->errorHandler(E_ERROR , "(".get_class($e).")".$e->getMessage(), $e->getFile(), $e->getLine());
	}
	public static function addError($name,$content) {
		if(clm_core::$log==null) {
			return false;
		}
		clm_core::$log->addError($name,$content);
	}
	public static function addWarning($name,$content) {
		if(clm_core::$log==null) {
			return false;
		}
		clm_core::$log->addWarning($name,$content);
	}
	public static function addNotice($name,$content) {
		if(clm_core::$log==null) {
			return false;
		}
		clm_core::$log->addNotice($name,$content);
	}
	public static function addUnknown($name,$content) {
		if(clm_core::$log==null) {
			return false;
		}
		clm_core::$log->addUnknown($name,$content);
	}
	public static function addInfo($name,$content) {
		if(clm_core::$log==null) {
			return false;
		}
		clm_core::$log->addInfo($name,$content);
	}
	public static function addDeprecated($name,$content) {
		if(clm_core::$log==null) {
			return false;
		}
		clm_core::$log->addDeprecated($name,$content);
	}
	// Fängt fatale Fehler für den Error Handler ab
	public static function shutdown() {
		$error = error_get_last();
    	//if ($error["type"] == E_ERROR ) { 
    	if ((isset($error["type"])) AND ($error["type"] == E_ERROR )) { 
			clm_core::errorHandler( $error["type"], $error["message"], $error["file"], $error["line"]);
      }
      restore_error_handler();
		restore_exception_handler();
	}
	// http://php.net/manual/de/function.debug-backtrace.php (jurchiks101 at gmail dot com)
	public static function getBacktrace($ignore = 0)
	{
   	$e = new Exception();
   	$trace = explode("\n", $e->getTraceAsString());
   	// reverse array to make steps line up chronologically
   	$trace = array_reverse($trace);
   	array_shift($trace); // remove {main}
   	array_pop($trace); // remove call to this method
   	$length = count($trace);
   	$result = array();
   	for ($i = 0; $i < $length; $i++)
   	{
      	$result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' '), strpos($trace[$i], ': ')- strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
   	}
   	return implode(" | ", $result);
	}
}
