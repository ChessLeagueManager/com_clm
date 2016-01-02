<?php
class clm_class_cms_joomla extends clm_class_cms {
	public function __construct() {
		$this->cms = 1;
		$this->root = JPATH_ROOT;
	}
	public function afterBoot() {
		$config = clm_core::$db->config();
		if ($config->template == 1 && $config->div == 1) {
			// Diese Operation zerstört PDF Dateien, daher nur Aufrufen wenn solche nicht generiert werden.
			if ((!isset($_GET["clm_backend"]) || $_GET["clm_backend"]!=1) && (!isset($_GET["format"]) || $_GET["format"] != "pdf")) {
				clm_core::$cms->addScriptDeclaration("
				function clmCreateMasterDiv (e) {
					var newChildNodes = document.body.childNodes;  
					var newElement = document.createElement('div');
					newElement.id = 'clm';        
					for (var i = 0; i < newChildNodes.length;i++) {
					    newElement.appendChild(newChildNodes.item(i));
					    newChildNodes.item(0).parentNode.insertBefore(newElement, newChildNodes.item(i));
					}
				}
				document.addEventListener('DOMContentLoaded', clmCreateMasterDiv, false);");
			}
		}
	}
	public function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null) {
		jimport( 'joomla.mail.mail' );
		$mail = JFactory::getMailer();
		return $mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}
	public function setTitle($text) {
		$mydoc = JFactory::getDocument();
		$mydoc->setTitle($text);
	}
	public function getTitle() {
		$mydoc = JFactory::getDocument();
		return $mydoc->getTitle();
	}
	public function addStyleSheet($url, $type = "text/css", $media = "all") {
		$document = JFactory::getDocument();
		$document->addStyleSheet($url, $type, $media);
	}
	public function addStyleDeclaration($content, $type = "text/css") {
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($content, $type);
	}
	public function addScript($url, $type = "text/javascript") {
		$document = JFactory::getDocument();
		$document->addScript($url, $type);
	}
	public function addScriptDeclaration($content, $type = "text/javascript") {
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($content, $type);
	}
	// Leer, da Joomla dies generiert.
	public function getStyleScriptHead() { 
	 	return "";
	}
	public function getUserData() {
		$id = JFactory::getUser()->get('id');
		if (isset($id) && !empty($id)) {
			return array($id, JFactory::getUser()->get('username'), JFactory::getUser()->get('name'), JFactory::getUser()->get('email'));
		}
		return array(0);
	}
	public function getLanguage() {
		$jlang = JFactory::getLanguage(); 
		return $jlang->getTag();
	}
	public function getNowDate($format = "Y-m-d H:i:s") {
		$date = JFactory::getDate('now'); 
		$now = date( $format, strtotime( $date->toSQL() ) );
		return $now;
	}
	public function showDate($date_time, $format = "") {
		if ($format == "") $format = JText::_('DATE_FORMAT_LC2');
		$output = JHtml::_('date',  $date_time, $format);
		return $output;
	}
	public function isRoot() {
		return JFactory::getUser()->get('isRoot');
	}
	public function login($name,$password) {
	 	$mainframe = JFactory::getApplication();
	 	$credentials = array( 'username' => $name, 'password' => $password);
		$this->logout();
      $success = $mainframe->login($credentials, array("silent" => true));
      if($success) {
			return parent::login($name,$password);
		} else {
			return $success;
		}
	}
	public function logout() {
		if(clm_core::$access->getJid()!=-1) {
			$mainframe = JFactory::getApplication();
			$mainframe->logout();
			return parent::logout();
		} else {
			return false;
		}
	}
}
?>
