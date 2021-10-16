<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
class clm_class_log {
	private $callid;
	public function __construct() {
		$this->callid = uniqid ( "", false );
	}
	private function add($type, $name, $content) {
//	public function add($type, $name, $content) {
		$userid = clm_core::$access->getId ();
		$sql = 'INSERT INTO #__clm_logging (`callid`, `userid`,`timestamp`,`type`,`name`,`content`)'
			.' VALUES ("' . $this->callid . '", ' . $userid . ', ' . time () . ', ' . $type . ',  "' . clm_core::$db->escape ( htmlspecialchars ( trim(preg_replace('/\s+/', ' ', $name)), ENT_QUOTES, "UTF-8" ) ) . '", "' . clm_core::$db->escape ( htmlspecialchars ( trim(preg_replace('/\s+/', ' ', $content)), ENT_QUOTES, "UTF-8" ) ) . '")';
		clm_core::$db->query ( $sql );
	}
	
	// Kritischer Fehler (type 0)
	public function addError($name, $content) {
		if (clm_core::$db->config ()->log_error) {
			$this->add ( 0, $name, $content );
		}
		$this->error ();
	}
	
	// Warnung (type 1)
	public function addWarning($name, $content) {
		if (clm_core::$db->config ()->log_warning) {
			$this->add ( 1, $name, $content );
		}
	}
	
	// Notiz (type 2)
	public function addNotice($name, $content) {
		if (clm_core::$db->config ()->log_notice) {
			$this->add ( 2, $name, $content );
		}
	}
	
	// Unbekannt (type 3)
	public function addUnknown($name, $content) {
		if (clm_core::$db->config ()->log_unknown) {
			$this->add ( 3, $name, $content );
		}
	}
	
	// festgelegte Informationen (Saisonwechsel, Konfigurationsänderungen) (type 4)
	public function addInfo($name, $content) {
		if (clm_core::$db->config ()->log_info) {
			$this->add ( 4, $name, $content );
		}
	}
	
	// Einträge aus dem alten Log 
	public function addDeprecated($name,$content) {
		if (clm_core::$db->config ()->log_info) { // zählt als normale Information
			$this->add ( 5, $name, $content );
		}
	}
	
	public function error() {
		$lang = clm_core::$lang->logging_error;
		if (clm_core::$db->config ()->log_error) {
			echo $lang->error1 . "<br/>" . $lang->error2 . "<br/>" . $lang->error3 . "<br/>" . $lang->error4 . " " . $this->callid;
		} else {
			echo $lang->error1 . "<br/>" . $lang->error2 . "<br/>" . $lang->error_problem;
		}
		die ();
	}

	public function errorHandler($errno, $errstr, $errfile = "", $errline = "", $errcontext = "") {
		if (isset($_SERVER["HTTP_USER_AGENT"])) $user = $_SERVER["HTTP_USER_AGENT"]; 
		else $user = 'UNDEFINED';
		$parameter = $_SERVER["REQUEST_URI"];
		$parameter2 = $_SERVER["QUERY_STRING"];
		$domain = $_SERVER['HTTP_HOST'];
		$message = "errno:" . $errno . " errstr:" . $errstr . " errfile:" . $errfile . " errline:" . $errline . " errcontext:" . json_encode ( $errcontext ); 
		$message .= " Backtrace: " . clm_core::getBacktrace () . " User: " . $user . " Domain: " . $domain . " Parameter: " . $parameter. " Parameter2: " . $parameter2;


		$bots = array('crawl', 'metaweb', 'msn.com', 'google', 'archiver', 'firefly', 'msnbo', 'slurp', 'inktomisearch', 'bot', 'AhrefsBot', 'qwant', 'AppleWebKit', 'Trident'); // bot erkennung
		$ist_bot = 0;
		foreach($bots as $element) {
			if (stristr(getEnv("HTTP_USER_AGENT"),$element) == TRUE) {
				$ist_bot = 1; // zur weiteren Verarbeitung 
			}
		}
		if ($ist_bot == 1) $message .= " = Bot";
		switch ($errno) {
			case E_USER_ERROR :
				$this->addError ( "E_USER_ERROR", $message );
				break;
			case E_ERROR :
				$this->addError ( "E_ERROR", $message );
				break;
			case E_USER_WARNING :
				$this->addWarning ( "E_USER_WARNING", $message );
				break;
			case E_WARNING :
				$this->addWarning ( "E_WARNING", $message );
				break;
			case E_USER_NOTICE :
				$this->addNotice ( "E_USER_NOTICE", $message );
				break;
			case E_NOTICE :
				$this->addNotice ( "E_NOTICE", $message );
				break;
			default :
				$this->addUnknown ( "Unknown", $message );
				break;
		}
		// Normales Handling bleibt aktiv
		return false;
	}
	public static function refactor($type,$name,$content) {
		$lang = clm_core::$lang->logging;
		switch ($type) {
			case 0 :
				$content = self::hideError($content,$lang->errorlog);
			break;
			case 1 :
				$content = self::hideError($content,$lang->errorlog);	
			break;
			case 2 :
				$content = self::hideError($content,$lang->errorlog);
			break;
			case 3 :
				$content = self::hideError($content,$lang->errorlog);
			break;
			case 4 :
				if($lang->exist($name)) {
					$name = '<p title="'.$name.'" >'.$lang->$name . "</p>";
				}
			break;
			case 5 :

			break;
		}
		$string="type".$type;
		$type = $lang->$string;
		return array($type,$name,$content);
	}
	private static function hideError($content,$errorlog) {
		return '<a href="javascript:void(0);" onclick=\'clm_modal_display("'.htmlspecialchars(trim(preg_replace('/\s+/', ' ', $content)), ENT_QUOTES, "UTF-8").'")\' href="javascript:;" >'.$errorlog.'</a>';
		
	} 
}
