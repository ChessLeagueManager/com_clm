<?php
class clm_class_lang {
	private $data;
	public function __construct() {
		$this->data = array();
	}
	public function __get($string) {
		$jlang = JFactory::getLanguage();
		if (!isset($this->data[$string])) {
//			$url = clm_core::$path . DS . "languages" . DS . clm_core::$db->config()->language . DS . $string . ".ini";
			$url = clm_core::$path . DS . "languages" . DS . $jlang->getTag() . DS . $jlang->getTag().".".$string . ".ini";
			if (!is_file($url)) {
				$url = clm_core::$path . DS . "languages" . DS . "de-DE" . DS . "de-DE.".$string . ".ini";
//				$this->data[$string] = new clm_class_lang_object(array());
//			} else {
			}
				$this->data[$string] = self::stringArray(file($url));
//			}
		}
		return $this->data[$string];
	}
	public function stringArray($zeilen) {
		$output = array();
		foreach ($zeilen as $zeile) {
			$line = explode("=", $zeile, 2);
			if (count($line) == 2 && !clm_core::$load->starts_with($line[0],"//")) {
				$output[$line[0]] = preg_replace("/\r\n|\r|\n/","",$line[1]);
			}
		}
	return new clm_class_lang_object($output);
	}
}
?>
