<?php
class clm_class_cms_standalone extends clm_class_cms {
	
	private $title;
	private $style;
	private $script;
	private $initJoomla;
	
	public function __construct() {
		$this->title = "CLM";
		$this->style = array();
		$this->script = array();
		$this->cms = 0;
		$this->root =  $this->normalizePath(clm_core::$path . DS . ".." . DS . ".." . DS . "..");
		$this->initJoomla = false;
		define('_JEXEC', 1);
		define('JPATH_BASE', $this->root);
	}
	public function afterBoot() {
	}
	public function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null) {
		$this->initJoomla();
		jimport( 'joomla.mail.mail' );
		$mail = JFactory::getMailer();
		return $mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	public function addStyleSheet($url, $type = "text/css", $media = "all") {
		$this->style[] = array($url,$type,$media);
	}
	public function addStyleDeclaration($content, $type = "text/css") {
		$this->style[] = array($content,$type);
	}
	public function addScript($url, $type = "text/javascript") {
		$this->script[] = array(true,$url,$type);
	}
	public function addScriptDeclaration($content, $type = "text/javascript") {
		$this->script[] = array(false,$content,$type);
	}
	public function getStyleScriptHead() { 
		$out = "";
		// Style
		foreach($this->style as $key) {
			if(count($key)==3) {
				$out .= '<link rel="stylesheet" href="' . $key[0] . '" type="' . $key[1] . '" media="' . $key[2] . '" />';
			} else {
				$out .= '<style type="' . $key[1] . '">' . $key[0] . '</style>';;
			}
		}
		// Script
		foreach($this->script as $key) {
			if($key[0]) {
				$out .= '<script src="' . $key[1] . '" type="' . $key[2] . '"></script>';
			} else {
				$out .= '<script type="' . $key[2] . '">' . $key[1] . '</script>';
			}
		}
	 	return $out;
	}
	public function getUserData() {
		$this->initJoomla();
		$id = JFactory::getUser()->get('id');
		if (isset($id) && $id > 0) {
			return array($id, JFactory::getUser()->get('username'), JFactory::getUser()->get('name'), JFactory::getUser()->get('email'));
		}
		return array(0);
	}
	public function getLanguage() {
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		if ($countryversion =="de") {
			return 'de-DE'; }
		if ($countryversion =="en") {
			return 'en-GB'; }
		return 'xx-XX'; 
	}
	public function getNowDate($format = "Y-m-d H:i:s") {
		$this->initJoomla();
		$date = JFactory::getDate(); 
		$now = date( $format, strtotime( $date->toSQL() ) );
		return $now;
	}
	public function showDate($date_time, $format = "") {
		$this->initJoomla();
		if ($format == "") $format = JText::_('DATE_FORMAT_LC2');
		$output = JHtml::_('date',  $date_time, $format);
		return $output;
	}
	public function isRoot() {
		$this->initJoomla();
		if (JFactory::getUser()->get('isRoot')) {
			return true;
		}
		return false;
	}
	public function login($name,$password) {
		$this->initJoomla();
		ob_start();
		$mainframe = JFactory::getApplication();
		$credentials = array( 'username' => $name, 'password' => $password);
		$this->logout();
 	   	$success = $mainframe->login($credentials, array("silent" => true));
 	   	ob_end_clean();
      		if($success) {
			return parent::login($name,$password);
		} else {
			return $success;
		}
	}
	public function logout() {
		if(clm_core::$access->getJid()!=-1) {
			ob_start();
			$this->initJoomla();
			$mainframe = JFactory::getApplication();
			$mainframe->logout();
			ob_end_clean();
			return parent::logout();
		} else {
			return false;
		}
	}
	private function initJoomla() {
		if(!$this->initJoomla) {
			ob_start();
			require_once ($this->root . '/includes/defines.php');
			require_once ($this->root . '/includes/framework.php');
			if (isset($_GET["clm_backend"]) && $_GET["clm_backend"] == "1") {
				$mainframe = JFactory::getApplication('administrator');
			} else {
				$mainframe = JFactory::getApplication('site');
			}
			$mainframe->initialise();
			$this->initJoomla=false;
			ob_end_clean();
		}
	}
	// http://php.net/manual/de/function.realpath.php, author: runeimp at gmail dot com
	private function normalizePath($path)
	{
		$parts = array();// Array to build a new path from the good parts
		$path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
		$path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
		$segments = explode('/', $path);// Collect path segments
		$test = '';// Initialize testing variable
		foreach($segments as $segment)
		{
			if($segment != '.')
			{
				$test = array_pop($parts);
				if(is_null($test))
					$parts[] = $segment;
				else if($segment == '..')
				{
					if($test == '..')
						$parts[] = $test;
	
					if($test == '..' || $test == '')
						$parts[] = $segment;
				}
				else
				{
					$parts[] = $test;
					$parts[] = $segment;
				}
			}
		}
		return implode('/', $parts);
	}
}
?>
