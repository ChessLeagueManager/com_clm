<?php
	function clm_function_date_to_string($date,$long=true,$short=false) {
		$lang = clm_core::$lang->date_to_string;
		$trans = array('Monday' => $lang->Monday, 'Tuesday' => $lang->Tuesday, 'Wednesday' => $lang->Wednesday, 'Thursday' => $lang->Thursday, 'Friday' => $lang->Friday, 'Saturday' => $lang->Saturday, 'Sunday' => $lang->Sunday, 'Mon' => $lang->Mon, 'Tue' => $lang->Tue, 'Wed' => $lang->Wed, 'Thu' => $lang->Thu, 'Fri' => $lang->Fri, 'Sat' => $lang->Sat, 'Sun' => $lang->Sun, 'January' => $lang->January, 'February' => $lang->February, 'March' => $lang->March, 'May' => $lang->May, 'June' => $lang->June, 'July' => $lang->July, 'October' => $lang->October, 'December' => $lang->December);

		$date = strtotime($date);
		if($short) {
			if($long) {
				$date = date($lang->format_month_num.", H:i", $date);
			} else {
				$date = date($lang->format_month_num, $date);
			}
			return $date;
		} else {
			if($long) {
				$date = date($lang->format_month_txt.", H:i", $date);
			} else {
				$date = date($lang->format_month_txt, $date);
			}
			return strtr($date, $trans);
		}
	}
?>
