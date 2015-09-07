<?php
function clm_view_notification($string, $intern = true) {
	$lang = clm_core::$lang->notification;
	if ($intern) {
		clm_core::$load->load_css("notification");
	}
	if (!$lang->exist($string)) {
		$string = "e_noNotification";
	}
	if ($string{0} == "e") {
		if ($intern) {
			echo '<div class="error"><span>' . $lang->$string . '</span></div>';
		} else {
			return array($lang->$string, 'error');
		}
	} else if ($string{0} == "w") {
		if ($intern) {
			echo '<div class="warning"><span>' . $lang->$string . '</span></div>';
		} else {
			return array($lang->$string, 'warning');
		}
	} else if ($string{0} == "m") {
		if ($intern) {
			echo '<div class="success"><span>' . $lang->$string . '</span></div>';
		} else {
			return array($lang->$string, 'message');
		}
	} else if ($string{0} == "n") {
		if ($intern) {
			echo '<div class="notice"><span>' . $lang->$string . '</span></div>';
		} else {
			return array($lang->$string, 'notice');
		}
	}
} ?>
