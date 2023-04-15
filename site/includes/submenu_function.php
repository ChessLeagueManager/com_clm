<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// array(array("Test",modus,get,array("Test",modus,get)),array(...))
// get ist array(array("option","com_clm"),array(...))
// modus ist 0=normal, 1=angewählt, 2=keinLink, 3=deaktiviert, 4=abstand
function clm_submenu($array) {
	if (count($array) == 0) {
		return "";
	}
	$out = '<div class="clm-navigator"><ul>';
	for ($i = 0;$i < count($array);$i++) {
		$ph = (integer) (count($array[$i][3])/2);

 		$out.= clm_submenu_href($array[$i],(count($array[$i][3]) > 0));
		if (count($array[$i][3]) > 0) {
			$out.= '<ul>';
		  if (!isset($array[2][3][0][2][1][1]) OR $array[2][3][0][2][1][1] != 'mannschaft' OR count($array[$i][3]) < (count($array[2][3]) + 8)) {
			for ($p = 0;$p < count($array[$i][3]);$p++) {
				$out.= clm_submenu_href($array[$i][3][$p],false) . '</li>';
			}
			$out.= '</ul>';
		  } else {	  
			for ($p = 0;$p < count($array[$i][3])/2;$p++) {
				$p1 = $p + $ph;
				$out.= '<li>';
				$out.= str_replace('<li','<span style="display:inline-block; text-align:center; font-weight:500 !important; width:50%;" ',clm_submenu_href($array[$i][3][$p],false)) . '</span>';
				$out.= str_replace('<li','<span style="display:inline-block; text-align:center; font-weight:500; width:50%;" ',clm_submenu_href($array[$i][3][($p1)],false)) . '</span>';
				$out.= '</li>';
			}
			$out.= '</ul>';
		  }
		}
		$out.= '</li>';
	}
	return $out . '</ul></div>';
}
function clm_submenu_get($get) {
	if (count($get) == 0) {
		return "#";
	}
	$out = "index.php?";
	for ($i = 0;$i < count($get);$i++) {
		if ($i != 0) {
			$out.= "&";
		}
		$out.= $get[$i][0] . "=" . $get[$i][1];
	}
	return $out;
}
function clm_submenu_href($href,$sub) {
if($sub)
{
$javascript = ' onmouseout="clm_height_del(this)" onmouseover="clm_height_set(this)" ';	
}else{$javascript='';}
	
	if ($href[1] == 2) {
		return '<li'.$javascript.'><p>' . $href[0] . '</p>';
	}
	if ($href[1] == 3) {
		return '<li'.$javascript.' class="disable" ><p>' . $href[0] . '</p>';
	}
	if ($href[1] == 4) {
		return '<li'.$javascript.' class="distance" >';
	}
	$out = '<li'.$javascript.' ';
	if ($href[1] == 1) {
		$out.= 'class="activ" ';
	}
	$out.= '><a href="' . clm_submenu_get($href[2]) . '">' . $href[0] . '</a>';
	return $out;
}
?>
