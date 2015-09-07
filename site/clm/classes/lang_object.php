<?php
class clm_class_lang_object {
	private $data;
	public function __construct($data) {
		$this->data = $data;
	}
	public function __get($string) {
		if (isset($this->data[$string])) {
			return str_replace(array("&lt;br/&gt;","&lt;br&gt;","&#039;"), array("<br/>","<br/>",'&#34;'), (htmlentities($this->data[$string], ENT_QUOTES, 'UTF-8')));
		} else {
			return htmlentities("<".$string.">", ENT_QUOTES, 'UTF-8');
		}
	}
	public function raw($string) {
		if (isset($this->data[$string])) {
			return $this->data[$string];
		} else {
			return "<".$string.">";
		}
	}
	public function onlySpecial($string) {
		if (isset($this->data[$string])) {
			return nl2br(htmlspecialchars($this->data[$string], ENT_QUOTES, 'UTF-8'));
		} else {
			return htmlspecialchars("<".$string.">", ENT_QUOTES, 'UTF-8');
		}
	}
	public function exist($string) {
		if (isset($this->data[$string])) {
			return true;
		} else {
			return false;
		}
	}
}
?>
