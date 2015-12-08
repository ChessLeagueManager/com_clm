<?php
abstract class clm_class_cms {
	protected $cms; // 0 = noCMS, 1 = joomla
	protected $root; // JPATH_ROOT
	
   public static function init() {
		if (!defined('_JEXEC')) {
			require_once(clm_core::$path . DS . "classes" . DS . "cms" . DS . "standalone.php");
			return new clm_class_cms_standalone();
		} else {
			require_once(clm_core::$path . DS . "classes" . DS . "cms" . DS . "joomla.php");
			return new clm_class_cms_joomla();
		}
   }
	abstract public function afterBoot();
		
	// Abstraktionsebene zwischen CLM und CMS
	abstract public function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null );
	abstract public function setTitle($text);
	abstract public function getTitle();
	abstract public function addStyleSheet($url, $type = "text/css", $media = "all");
	abstract public function addStyleDeclaration($content, $type = "text/css");
	abstract public function addScript($url, $type = "text/javascript");
	abstract public function addScriptDeclaration($content, $type = "text/javascript");
	abstract public function getStyleScriptHead();
	abstract public function getUserData();
	abstract public function getLanguage();
	abstract public function getNowDate($format = "Y-m-d H:i:s");
	abstract public function showDate($date_time, $format = "");
	abstract public function isRoot();
	
	// Die Rechteverwaltung muss nach einem login/logout stets neu gestartet werden.
	public function login($name,$password) {
			clm_core::$access = new clm_class_access();
			return true;
	}
	public function logout() {
			clm_core::$access = new clm_class_access();
			return true;
	}
		
	public function getStatus() {
		return $this->cms;
	}
	public function getRoot() {
		return $this->root;
	}
   public static function joomla_config() {
		require_once (clm_core::$cms->getRoot() . DS . "configuration.php");
		$joomla_config = new JConfig();
		$db_data = array($joomla_config->host, $joomla_config->db, $joomla_config->user, $joomla_config->password, $joomla_config->dbprefix);
		// host table user password prefix
		return $db_data;
	}
}
?>
