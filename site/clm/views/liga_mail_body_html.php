<?php
function clm_view_liga_mail_body_html($player, $result, $dateNow, $dateGame, $hname, $gname, $hmf, $gmf, $comment, $ko, $sender, $liga, $gemeldet) {
	$lang = clm_core::$lang->liga_mail_body;
	
	// Mailbody TXT
	$body_msg = JText::_('RESULT_DATA_BODY1')." ".$begegnung[0]->name." - ".$begegnung[1]->name
	.JText::_('RESULT_DATA_BODY2_1').$fromname.JText::_('RESULT_DATA_BODY2_2')
	."\r\n\r\n http://$pfad/index.php?option=com_clm&view=runde&saison=$sid&liga=$lid&runde=$rnd&dg=$dg"
	.JText::_('RESULT_DATA_BODY3')
	.JText::_('RESULT_DATA_BODY4')
	."\r\n\r\n http://$pfad/index.php?option=com_clm&view=rangliste&saison=$sid&liga=$lid"
	.JText::_('RESULT_DATA_BODY5')
	;
	// Mailbody - TXT Ergänzung
	$body_msg .= "\r\n\r\n "; 
	$zeile .= $mail[0]->name.', '.$rundeterm[0]->name;  
	if(isset($rundeterm[0]->datum)) { 
	  $zeile .= ' '.JText::_('ON_DAY').' '.JHTML::_('date',  $rundeterm[0]->datum, JText::_('%d. %B %Y')); }
    $body_msg .= $zeile;
	$zeile  = "\r\n "; 
	$zeile = mb_str_pad($zeile, 7, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->htln, 10, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->hname, 34, " "); 
	$zeile = mb_str_pad($zeile.'('.round($dwzgespielt[0]->dwz).')', 41, " "); 
	$zeile = mb_str_pad($zeile.$summe[0]->sum.' : '.$summe[1]->sum, 53, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->gtln, 56, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->gname, 80, " "); 
	$zeile = mb_str_pad($zeile.'('.round($dwzgespielt[0]->gdwz).')', 87, " "); 
	$body_msg .= $zeile;
	$body_msg .= "\r\n "; 
	for ($x=0; $x<$stamm; $x++) {
		$zeile = "\r\n ";
		$zeile = mb_str_pad($zeile.($x+1), 5, " "); 
		if (mb_strlen($einzel[$x]->hsnr) < 3) $zeile .= '  ';
		$zeile = mb_str_pad($zeile.$einzel[$x]->hsnr, 10, " "); 
		$zeile = mb_str_pad($zeile.$einzel[$x]->hname, 34, " "); 
		$zeile = mb_str_pad($zeile.'('.$einzel[$x]->hdwz.')', 41, " "); 
		if (mb_strlen($erg_text[$einzel[$x]->ergebnis]->erg_text) == 3) $zeile .= '   ';
		if (mb_strlen($erg_text[$einzel[$x]->ergebnis]->erg_text) == 5) $zeile .= '  ';
		$zeile = mb_str_pad($zeile.$erg_text[$einzel[$x]->ergebnis]->erg_text, 51, " "); 
		if (mb_strlen($einzel[$x]->gsnr) < 3) $zeile .= '  ';
		$zeile = mb_str_pad($zeile.$einzel[$x]->gsnr, 56, " "); 
		$zeile = mb_str_pad($zeile.$einzel[$x]->gname, 80, " "); 
		$zeile = mb_str_pad($zeile.'('.$einzel[$x]->gdwz.')', 87, " "); 
		$body_msg .= $zeile;
	}
	if ($paar[0]->comment != "") { 
		$body_msg .= "\r\n ";
		$zeile = "\r\n ".JText::_('PAAR_COMMENT_L');
		$body_msg .= $zeile;
		$zeile = $paar[0]->comment;
		$body_msg .= $zeile;
	}
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
			<td bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;" colspan="6"><div align="center" style="font-size: 12px;"><strong>Online Spielbericht vom ' .JHTML::_('date', date('Y-m-d H:i:s'), JText::_('DATE_FORMAT_CLM_PDF')). '</strong></div></td>
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
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>Liga:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$mail[0]->name. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>Spieltag:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .JHTML::_('date',  $rundeterm[0]->datum, JText::_('DATE_FORMAT_CLM_F')). '&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>Heim:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->hname. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>Gast:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->gname. '&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>MF-Heim:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->hmf. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>MF-Gast:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->gmf. '&nbsp;</td>
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
			<td width="50" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Brett</strong></div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Melde Nr. </strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Mgl. Nr. </strong></div></td>
			<td width="210" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Spieler</strong> (Heim)</div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Ergebnis</strong></div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Melde Nr. </strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Mgl. Nr. </strong></div></td>
			<td width="215" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Spieler</strong> (Gast)</div></td>
		</tr>
	';
	for ($x=0; $x<$stamm; $x++) {
  	  $body_html .=   '
		<tr>
			<td width="50" style="border-bottom: solid 1px #999999;"><div align="center"><strong>'.($x+1).'</strong></div></td>
			<td width="75" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->hsnr. '&nbsp;</div></td>
			<td width="60" style="border-bottom: solid 1px #999999;"><div align="center">' .str_pad($einzel[$x]->hmglnr,3,"0",STR_PAD_LEFT). '&nbsp;</div></td>
			<td width="210" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->hname. '&nbsp;</div></td>
			<td width="75" style="border-bottom: solid 1px #999999; border-left: solid 1px #999999; border-right: solid 1px #999999;"><div align="center">' .$erg_text[$einzel[$x]->ergebnis]->erg_text. '&nbsp;</div></td>
			<td width="75" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->gsnr. '&nbsp;</div></td>
			<td width="60" style="border-bottom: solid 1px #999999;"><div align="center">' .str_pad($einzel[$x]->gmglnr,3,"0",STR_PAD_LEFT). '&nbsp;</div></td>
			<td width="215" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->gname. '&nbsp;</div></td>
		</tr>
	  ';
	}
	$body_html .= 	  '
		<tr>
			<td width="50"><div align="center"></div></td>
			<td width="75"><div align="center"></div></td>
			<td width="60"><div align="center"></div></td>
			<td width="210"><div align="right"><strong>Gesamtergebnis: </strong></div></td>
			<td style="border-bottom: solid 1px #999999; border-left: solid 1px #999999; border-right: solid 1px #999999;" width="75"><div align="center" style="color:#FF0000"><strong>' .$summe[0]->sum.' : '.$summe[1]->sum. '&nbsp;</strong></div></td>
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
	if ($paar[0]->comment != "") { 
		$paar[0]->comment = ereg_replace('
','<br>',$paar[0]->comment);

      $body_html .= 	'
		<tr>
			<td width="80" valign="top"><strong>'.JText::_('PAAR_COMMENT_L').'</strong></td>
			<td  width="420" nowrap="nowrap" valign="top" size="1">
				<textarea cols="30" rows="2" style="width:90%">'.str_replace('&','&amp;',$paar[0]->comment).'</textarea>
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
			<td width="80" valign="top"><strong>Ergebnismelder:</strong></td>
			<td>' .$paar[0]->melder. '&nbsp;</td>
		</tr>
	
		</table>
	';
	
	// Mailbody HTML ML
	$body_html_mf = '
	  <table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
		  <td>'.JText::_('RESULT_DATA_BODY1')." ".$begegnung[0]->name." - ".$begegnung[1]->name
			.JText::_('RESULT_DATA_BODY2_1').$fromname;
	if (isset($id[0]->gemeldet)) $body_html_mf .= JText::_('RESULT_DATA_BODY2_2A'); else $body_html_mf .= JText::_('RESULT_DATA_BODY2_2');
	$body_html_mf .= ' siehe unten oder <a href="http://'.$pfad.'/index.php?option=com_clm&view=runde&saison='.$sid.'&liga='.$lid.'&runde='.$rnd.'&dg='.$dg.'">hier</a>
		  </td>
		</tr>
		<tr>
		  <td><a href="mailto:'.$sl_bcc.'">'.JText::_('RESULT_DATA_BODY3').'</a></td>
		</tr>
		<tr>
 		  <td>'.JText::_('RESULT_DATA_BODY4').'<a href="http://'.$pfad.'/index.php?option=com_clm&view=rangliste&saison='.$sid.'&liga='.$lid.'"> Liga</a></td>
  		</tr>
		<tr>
		  <td>'.JText::_('RESULT_DATA_BODY5').'</td>
		</tr>
		</table>
	';
	$body_name1 = JText::_('RESULT_NAME').$empfang[0]->empfang.",";
	$body = $body_html_header.$body_name1.$body_html_mf.$body_html.$body_html_footer;
	
}
?>