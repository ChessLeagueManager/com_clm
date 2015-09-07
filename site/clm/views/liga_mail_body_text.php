<?php
function clm_view_liga_mail_body_text($player, $result, $dateNow, $dateGame, $hname, $gname, $hmf, $gmf, $comment, $ko, $sender, $liga, $gemeldet) {
	$lang = clm_core::$lang->liga_mail_body;
	$header = $lang->raw("header1")."\n".$lang->raw("header11");
	$header.= " " . $hname . " - " . $gname . "\n";
	$header.= $lang->raw("header2");
	if ($gemeldet) {
		$header.= " " . $lang->raw("header31")."\n\n";
	} else {
		$header.= " " . $lang->raw("header32")."\n\n";
	}

	$dateNow = clm_core::$load->date_to_string($dateNow,true);
	if ($dateGame != - 1) {
		$dateGame = clm_core::$load->date_to_string($dateGame,false);
	} else {
		$dateGame = " ";
	}
	echo 	 $header
			.$lang->raw("title")." "
			.$dateNow."\n"
			.$lang->raw("liga") ." "
			.$liga."\n"
			.$lang->raw("day") ." "
			.$dateGame."\n"
			.$lang->raw("home2") ." "
			.$hname."\n"
			.$lang->raw("guest2") ." "
			.$gname."\n"
			.$lang->raw("home3") ." "
			.$hmf."\n"
			.$lang->raw("guest3") ." "
			.$gmf."\n"."\n";

	echo   $lang->raw("board") .", "
		   .$lang->raw("melde_nr") .", "
		   .$lang->raw("mgl_nr") .", "
		   .$lang->raw("player")
		   .$lang->raw("home") .", "
		   .$lang->raw("result") .", "
		   .$lang->raw("melde_nr") .", "
		   .$lang->raw("mgl_nr") .", "
		   .$lang->raw("player") 
		   .$lang->raw("home")."\n";
	   
	for ($i = 0;$i < count($player);$i++) { 
		echo   ($i + 1).": "
				.$player[$i][0]." "
				.$player[$i][1]." "
				.$player[$i][2]." <"
				.$player[$i][3]."> "
				.$player[$i][4]." "
				.$player[$i][5]." "
				.$player[$i][6]."\n"; 
	}

	echo   $lang->raw("man_result")
			." ".$result."\n\n"; ;

	if ($comment != "") { 
		echo $lang->raw("comment"). " " .$comment."\n\n"; ;
	}
	
	if ($ko != - 1) { 
		echo $lang->raw("ko") . " " . $ko."\n\n"; ; 
	}
	
	echo $lang->raw("sender") . " " . $sender;
} 
?>