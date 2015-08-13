<?php
function clm_view_liga_mail_body_html($player, $result, $dateNow, $dateGame, $hname, $gname, $hmf, $gmf, $comment, $ko, $sender, $liga, $gemeldet, $out, $recipient) {
	$lang = clm_core::$lang->liga_mail_body;
	$dateNow = clm_core::$load->date_to_string($dateNow,true);
	if ($dateGame != - 1) {
		$dateGame = clm_core::$load->date_to_string($dateGame,false);
	} else {
		$dateGame = " ";
	}
	// Details aus $out
	$sid 	= $out["paar"][0]->sid;
	$lid	= $out["paar"][0]->lid;
	$rnd	= $out["paar"][0]->runde;
	$dg		= $out["paar"][0]->dg; 
	if (isset($out["hmf"][0])) {
		$hmf_name	= $out["hmf"][0]->name;
		$hmf_email	= $out["hmf"][0]->email;
	} else {
		$hmf_name	= "";
		$hmf_email	= "";
	}
	if (isset($out["gmf"][0])) {
		$gmf_name	= $out["gmf"][0]->name;
		$gmf_email	= $out["gmf"][0]->email;
	} else {
		$gmf_name	= "";
		$gmf_email	= "";
	}
	if (isset($out["sl"][0])) {
		$sl_name	= $out["sl"][0]->name;
		$sl_email	= $out["sl"][0]->email;
	} else {
		$sl_name	= "";
		$sl_email	= "";
	}

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$fromname = $config->email_fromname;
	
	// Pfad der Application bestimmen für Links
	$pfad = (empty($_SERVER['HTTPS'])) ? 'http' : 'https';
    $pfad .= '://'.$_SERVER['HTTP_HOST'];
	
// Mailbody HTML Header
	$body_html_header = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>Online Spielbericht</title>
			</head>
			<body>';
	$body_html_footer = '
			</body>
			</html>';	
// Mailbody HTML Spielbericht
	$body_html =	'
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;" colspan="6"><div align="center" style="font-size: 12px;"><strong>Online Spielbericht vom ' .$dateNow. '</strong></div></td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.$lang->raw("liga").'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$liga. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.$lang->raw("day").'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$dateGame. '&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.$lang->raw("home2").'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$hname. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.$lang->raw("guest2").'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$gname. '&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.$lang->raw("home3").'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$hmf_name. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.$lang->raw("guest3").'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$gmf_name. '&nbsp;</td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</table>
		
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td width="50" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("board").'</strong></div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("melde_nr").'</strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("mgl_nr").'</strong></div></td>
			<td width="210" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("player").'</strong>'.$lang->raw("home").'</div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("result").'</strong></div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("melde_nr").'</strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("mgl_nr").'</strong></div></td>
			<td width="215" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$lang->raw("player").'</strong> '.$lang->raw("guest").'</div></td>
		</tr>
	';
	for ($i=0; $i<count($player); $i++) {
  	  $body_html .=   '
		<tr>
			<td width="50" style="border-bottom: solid 1px #999999;"><div align="center"><strong>'.($i+1).'</strong></div></td>
			<td width="75" style="border-bottom: solid 1px #999999;"><div align="center">' .$player[$i][0]. '&nbsp;</div></td>
			<td width="60" style="border-bottom: solid 1px #999999;"><div align="center">' .$player[$i][1]. '&nbsp;</div></td>
			<td width="210" style="border-bottom: solid 1px #999999;"><div align="center">' .$player[$i][2]. '&nbsp;</div></td>
			<td width="75" style="border-bottom: solid 1px #999999; border-left: solid 1px #999999; border-right: solid 1px #999999;"><div align="center">' .$player[$i][3]. '&nbsp;</div></td>
			<td width="75" style="border-bottom: solid 1px #999999;"><div align="center">' .$player[$i][4]. '&nbsp;</div></td>
			<td width="60" style="border-bottom: solid 1px #999999;"><div align="center">' .$player[$i][5]. '&nbsp;</div></td>
			<td width="215" style="border-bottom: solid 1px #999999;"><div align="center">' .$player[$i][6]. '&nbsp;</div></td>
		</tr>
	  ';
	}
	$body_html .= 	  '
		<tr>
			<td width="50"><div align="center"></div></td>
			<td width="75"><div align="center"></div></td>
			<td width="60"><div align="center"></div></td>
			<td width="210"><div align="right"><strong>'.$lang->raw("man_result").' </strong></div></td>
			<td style="border-bottom: solid 1px #999999; border-left: solid 1px #999999; border-right: solid 1px #999999;" width="75"><div align="center" style="color:#FF0000"><strong>' .$result. '&nbsp;</strong></div></td>
			<td width="75"><div align="center"></div></td>
			<td width="60"><div align="center"></div></td>
			<td width="215">&nbsp;</td>
		</tr>
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	';
	if ($comment != "") { 
		$comment = ereg_replace('
','<br>',$comment);

      $body_html .= 	'
		<tr>
			<td width="80" valign="top"><strong>'.$lang->raw("comment").'</strong></td>
			<td  width="420" nowrap="nowrap" valign="top" size="1">
				<textarea cols="30" rows="2" style="width:90%">'.str_replace('&','&amp;',$comment).'</textarea>
			</td>
  		</tr>
	  ';
	}
	$body_html .= 	  '
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="80" valign="top"><strong>'.$lang->raw("sender").'</strong></td>
			<td>' .$sender. '&nbsp;</td>
		</tr>
	
		</table>
	';
	
	$body_html_sp = "XXX";
	// Mail Admin
	if ($recipient == 'Admin') {
		// Text Admin
		$body_html_sp= '<br>'
			.$lang->raw('RESULT_ADMIN_COPY1')
			.'<br>'.$lang->raw('RESULT_ADMIN_COPY2')." ".$hname." - ".$gname
			.'<br>'.$lang->raw('RESULT_ADMIN_COPY3_1').$fromname;
		if ($gemeldet) $body_html_sp .= $lang->raw('RESULT_ADMIN_COPY3_2A'); 
		else $body_html_sp .= $lang->raw('RESULT_ADMIN_COPY3_2');
		$body_html_sp .= '<br>'.'<br>';
		$body_name1 = "";
	}
	// Text Staffelleiter
	if ($recipient == 'SL') {
		$body_html_sp = '<br>'
			.$lang->raw('RESULT_SL_COPY1')
			.'<br>'.$lang->raw('RESULT_ADMIN_COPY2')." ".$hname." - ".$gname
			.'<br>'.$lang->raw('RESULT_ADMIN_COPY3_1').$fromname;
		if ($gemeldet) $body_html_sp .= $lang->raw('RESULT_ADMIN_COPY3_2A'); 
		else $body_html_sp .= $lang->raw('RESULT_ADMIN_COPY3_2');
		$body_html_sp .= '<br>'.'<br>';
		$body_name1 = "";
	}
	// Mailbody HTML ML
	if ($recipient == 'Home' OR $recipient == 'Guest') {
		$body_html_sp = '
			<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
			  <tr>
				<td>'.$lang->raw('RESULT_DATA_BODY1')." ".$hname." - ".$gname
					.$lang->raw('RESULT_DATA_BODY2_1').$fromname;
		if ($gemeldet) $body_html_sp .= $lang->raw('RESULT_DATA_BODY2_2A'); 
		else $body_html_sp .= $lang->raw('RESULT_DATA_BODY2_2');
		$body_html_sp .= ' siehe unten oder <a href="'.$pfad.'/index.php?option=com_clm&view=runde&saison='.$sid.'&liga='.$lid.'&runde='.$rnd.'&dg='.$dg.'">hier</a>
				</td>
			  </tr><tr>
				<td><a href="mailto:'.$sl_email.'">'.$lang->raw('RESULT_DATA_BODY3').'</a></td>
			  </tr><tr>
				<td>'.$lang->raw('RESULT_DATA_BODY4').'<a href="'.$pfad.'/index.php?option=com_clm&view=rangliste&saison='.$sid.'&liga='.$lid.'"> '.$lang->raw("liga").'</a></td>
			  </tr><tr>
				<td>'.$lang->raw('RESULT_DATA_BODY5').'</td>
			  </tr>
			</table>
			';
		if ($recipient == 'Home')
			$body_name1 = $lang->raw('RESULT_NAME').$hmf_name.",";
		else
			$body_name1 = $lang->raw('RESULT_NAME').$gmf_name.",";
	}
	$body = $body_html_header.$body_name1.$body_html_sp.$body_html.$body_html_footer;
	echo $body;
}
?>