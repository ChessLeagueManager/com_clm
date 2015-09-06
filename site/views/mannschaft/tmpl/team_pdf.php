<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

// Variablen ohne foreach setzen
$mannschaft	=$this->mannschaft;
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $mannschaft[0]->params);
	$mannschaft[0]->params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$key = substr($value,0,$ipos);
			if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$mannschaft[0]->params[$key] = substr($value,$ipos+1);
		}
	}
	if (!isset($mannschaft[0]->params['dwz_date'])) $mannschaft[0]->params['dwz_date'] = '0000-00-00';
$count		=$this->count;
$bp			=$this->bp;
$sumbp		=$this->sumbp;
$plan		=$this->plan;
$termin		=$this->termin;
$einzel		=$this->einzel;
$saison		=$this->saison;
// Variblen aus URL holen
$sid 		= JRequest::getInt('saison','1');
$liga 		= JRequest::getInt( 'liga', '1' );
$tln 		= JRequest::getInt('tlnr');
$itemid 	= JRequest::getInt('Itemid');
$option 	= JRequest::getCmd( 'option' );
$o_nr	 	= JRequest::getInt( 'o_nr' );

function vergleich($wert_a,$wert_b) {
	$a = 1000*($wert_a->dg) + 50*($wert_a->runde) + 2*($wert_a->paar) + $wert_a->heim;
	$b = 1000*($wert_b->dg) + 50*($wert_b->runde) + 2*($wert_b->paar) + $wert_b->heim;
	if ($a == $b) { return 0; }
	return ($a < $b) ? -1 : +1; 
}
$bpr = $bp;
usort($bpr, 'vergleich');
 
$sql = ' SELECT `sieg`, `remis`, `nieder`, `antritt` FROM #__clm_liga'
		. ' WHERE `id` = "' . $liga . '"';
$db =JFactory::getDBO ();
$db->setQuery ($sql);
$ligapunkte = $db->loadObject ();
$sieg = $ligapunkte->sieg;
$remis = $ligapunkte->remis;
$nieder = $ligapunkte->nieder;
$antritt = $ligapunkte->antritt;

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
	$telefon= $config->man_tel;
	$mobil	= $config->man_mobil;
	$mail	= $config->man_mail;

	// Userkennung holen
	$user	=JFactory::getUser();
	$jid	= $user->get('id');

require_once(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');

class PDF extends FPDF
{
//Kopfzeile
function Header()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_header.php');
}
//Fusszeile
function Footer()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_footer.php');
}
}

// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 9
$font = 9;
// Fontgröße Datum = 8
$date_font = 8;

// Datum der Erstellung
$date =JFactory::getDate();
$now = $date->toSQL();

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',$date_font);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,4,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
	
if ( $mannschaft[0]->published == 0) {
	$pdf->SetFont('Times','',$head_font+2);
	$pdf->Cell(10,15,utf8_decode(JText::_('TEAM')).' '.utf8_decode($mannschaft[0]->name),0,1);
	$pdf->Cell(10,15,utf8_decode(JText::_('NOT_PUBLISHED')),0,0);
	$pdf->Ln();
	$pdf->Cell(10,15,utf8_decode(JText::_('GEDULD')),0,0);
} elseif (($mannschaft[0]->runden * $mannschaft[0]->dg) > 18) {
	$pdf->SetFont('Times','',$head_font+2);
	$pdf->Cell(10,15,utf8_decode(JText::_('TEAM_PDF_LIMIT')),0,1);
	$pdf->Ln();
	$pdf->Cell(10,15,utf8_decode(JText::_('TEAM_PDF_ADVICE')),0,0);
} else {
$anzspl = $mannschaft[0]->runden * $mannschaft[0]->dg;
// Zellenhöhe -> Standard 6
$zelle = 7;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 7 Runden und 1 Durchgang
if ($anzspl > 11) {
	$breite1 = 1;
	$breite = 6;
} else {
	$breite1 = 10;
	$breite = 8;
}

$pdf->SetFont('Times','B',$head_font);
	$pdf->Cell(10,10,' ',0,0);
	if ($o_nr == 0) 
		$pdf->Cell(100,10,utf8_decode(JText::_('TEAM')).' : '.utf8_decode($mannschaft[0]->name),0,1,'L');
	else {
		$pdf->Cell(100,10,utf8_decode(JText::_('TEAM')).' : '.utf8_decode($mannschaft[0]->name),0,0,'L');
		$pdf->SetFont('Times','',$head_font-2);
		$ztext = "( ".$mannschaft[0]->zps;
		if ($mannschaft[0]->sg_zps > "0") $ztext .= " / ".$mannschaft[0]->sg_zps;
		$ztext .= " )";
		$pdf->Cell(20,10,$ztext,0,1,'L');
	}	
$pdf->SetFont('Times','B',$head_font-2);
	$pdf->Cell(10,10,' ',0,0);
	$pdf->Cell(100,10,utf8_decode(JText::_('LEAGUE')).' : '.utf8_decode($mannschaft[0]->liga_name)." ".$saison[0]->name,0,1,'L');
$pdf->SetFont('Times','B',$font);
	$pdf->Cell(10,6,' ',0,0);
	$pdf->Cell(80,6,utf8_decode(Jtext::_('TEAM_LEADER')),0,0);
	//$pdf->Cell(80,6,utf8_decode(substr(Jtext::_('TEAM_LOCATION'),3,(strlen(Jtext::_('TEAM_LOCATION'))-7))),0,1);
	$pdf->Cell(80,6,utf8_decode(Jtext::_('TEAM_LOCATION')),0,1);
$pdf->SetFont('Times','',$font);	
	$pdf->Cell(10,4,' ',0,0);
	if ($mannschaft[0]->mf_name <> '') $pdf->Cell(80,4,utf8_decode($mannschaft[0]->mf_name),0,0,'L');
	else $pdf->Cell(80,4,utf8_decode(JText::_('NOT_YET')),0,0,'L');
	if ($mannschaft[0]->lokal <> '') $man = explode(",", $mannschaft[0]->lokal);
	else $man = explode(",",utf8_decode(JText::_('NOT_YET')));
	if (isset($man[0])) $pdf->Cell(80,4,utf8_decode($man[0]),0,1);
	else $pdf->Cell(80,4,'',0,1);
$pdf->SetFont('Times','U',$font);	
	$pdf->Cell(10,4,' ',0,0);
	if ($mail=="1" OR ($mail =="0" AND $jid !="0")) $pdf->Cell(80,4,utf8_decode($mannschaft[0]->email),0,0,'L');
	else $pdf->Cell(80,4,utf8_decode(JText::_('TEAM_MAIL')." ".JText::_('TEAM_REGISTERED')),0,0,'L');
$pdf->SetFont('Times','',$font);
	if (isset($man[1])) $pdf->Cell(80,4,utf8_decode($man[1]),0,1);
	else $pdf->Cell(80,4,'',0,1);
	
	$pdf->Cell(10,4,' ',0,0);
	if ($telefon =="1" OR ($telefon =="0" AND $jid !="0")) {
		if (($mannschaft[0]->tel_fest) <> '')  $pdf->Cell(80,4,Jtext::_('TEAM_FON').utf8_decode($mannschaft[0]->tel_fest),0,0,'L');
		elseif ($mannschaft[0]->mf_name <> '') $pdf->Cell(80,4,substr(Jtext::_('TEAM_NO_FONE'),0,(strlen(Jtext::_('TEAM_NO_FONE'))-4)),0,0,'L');
		else $pdf->Cell(80,4,'',0,0,'L'); }
	else $pdf->Cell(80,4,utf8_decode(Jtext::_('TEAM_FON')." ".JText::_('TEAM_REGISTERED')),0,0,'L'); 
	if (isset($man[2])) $pdf->Cell(80,4,utf8_decode($man[2]),0,1);
	else $pdf->Cell(80,4,'',0,1);
	
	$pdf->Cell(10,8,' ',0,0);
	if ($mobil =="1" OR ($mobil =="0" AND $jid !="0")) {
		if (($mannschaft[0]->tel_mobil) <> '') $pdf->Cell(80,4,Jtext::_('TEAM_MOBILE').utf8_decode($mannschaft[0]->tel_mobil),0,0,'L');
		elseif ($mannschaft[0]->mf_name <> '') $pdf->Cell(80,4,substr(Jtext::_('TEAM_NO_MOBILE'),0,(strlen(Jtext::_('TEAM_NO_MOBILE'))-4)),0,0,'L');
		else $pdf->Cell(80,4,'',0,0,'L'); }
	else $pdf->Cell(80,4,utf8_decode(Jtext::_('TEAM_MOBILE')." ".JText::_('TEAM_REGISTERED')),0,0,'L'); 
	if (isset($man[3])) $pdf->Cell(80,4,utf8_decode($man[3]),0,1);
	else $pdf->Cell(80,4,'',0,1);
	
$pdf->SetFont('Times','B',$font);
	$pdf->Ln();
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_FORMATION')),0,1,'L');
	
	if ($mannschaft[0]->anzeige_ma == 1) {
		$pdf->SetFont('Times','',$font);
		$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_FORMATION_BLOCKED')),0,1,'C');
	}
	elseif (!$count) {
		$pdf->SetFont('Times','',$font);
		$pdf->Cell(80,8,utf8_decode(JText::_('NOT_YET')),0,1,'C');
	}
	else {

$pdf->SetFont('Times','',$font);
	$pdf->Cell($breite1,8,' ',0,0);
	$pdf->Cell(12,8,JText::_('DWZ_NR'),0,0,'C');
	if (($o_nr == 0) or ($mannschaft[0]->sg_zps <= "0"))
		$pdf->Cell(40,8,JText::_('DWZ_NAME'),0,0);
	else
		$pdf->Cell(48,8,JText::_('DWZ_NAME'),0,0);
	$pdf->Cell(10,8,JText::_('LEAGUE_STAT_DWZ'),0,0,'R');
	//if (!$count) 
	for ($b=0; $b<$mannschaft[0]->runden; $b++) {
		$pdf->Cell($breite,8,$b+1,0,0,'C');
	}
	if ($mannschaft[0]->dg >1) {
		for ($b=0; $b<$mannschaft[0]->runden; $b++) {
			$pdf->Cell($breite,8,$b+1,0,0,'C');
	} }
	if ($mannschaft[0]->dg >2) {
		for ($b=0; $b<$mannschaft[0]->runden; $b++) {
			$pdf->Cell($breite,8,$b+1,0,0,'C');
	} }
	if ($mannschaft[0]->dg >3) {
		for ($b=0; $b<$mannschaft[0]->runden; $b++) {
			$pdf->Cell($breite,8,$b+1,0,0,'C');
	} }
	$pdf->Cell($breite,8,JText::_('TEAM_POINTS'),0,0,'C');
	$pdf->Cell($breite,8,JText::_('TEAM_GAMES'),0,0,'C');
	$pdf->Cell($breite,8,JText::_('LEAGUE_STAT_PERCENT'),0,1,'C');
	

// Teilnehmerschleife 	
	$ie = 0;
	$y = 0;
	$sumspl = 0;
	$sumgespielt = 0;
for ($x=0; $x< 100; $x++){
	// Überlesen von Null-Sätzen 
	while (isset($count[$x]) and ($count[$x]->mgl_nr == "0"))  {
		$x++; }
	if (!isset($count[$x])) break;
	if (!isset($count[$x]->rrang)) {
		$pdf->Cell($breite1,4,' ',0,0);
		$y++;
		$pdf->Cell(12,4,($y),0,0,'C');
		//$pdf->Cell(12,4,($x+1),0,0,'C');
	} else {
		if ($count[$x]->rmnr > $mannschaft[0]->man_nr) {
		  if (($count[$x]->zps !== $einzel[$ie]->zps)||($count[$x]->mgl_nr !== $einzel[$ie]->spieler)) {
			continue;
		  }	
		}
		$pdf->Cell($breite1,4,' ',0,0);
		$pdf->Cell(12,4,($count[$x]->rmnr.'-'.$count[$x]->rrang),0,0,'C');
	}
	if ($o_nr == 0) 
		$pdf->Cell(40,4,utf8_decode($count[$x]->name),0,0);
	elseif ($mannschaft[0]->sg_zps > "0") {
		$pdf->Cell(33,4,utf8_decode($count[$x]->name),0,0);
		$pdf->SetFont('Times','',7);
		$pdf->Cell(15,4,"(".$count[$x]->zps."-".$count[$x]->mgl_nr.")",0,0);
		$pdf->SetFont('Times','',8);
	} else {
		$pdf->Cell(33,4,utf8_decode($count[$x]->name),0,0);
		$pdf->SetFont('Times','',7);
		$pdf->Cell(7,4,"(".$count[$x]->mgl_nr.")",0,0);
		$pdf->SetFont('Times','',8);
	}
    if ($mannschaft[0]->params['dwz_date'] == '0000-00-00') { $pdf->Cell(10,4,$count[$x]->dwz,0,0,'R'); } 
	else { $pdf->Cell(10,4,$count[$x]->start_dwz,0,0,'R'); } 
	$pkt = 0;
	$spl = 0;
	$gespielt = 0;
  for ($c=0; $c<$mannschaft[0]->dg; $c++) {
	for ($b=0; $b<$mannschaft[0]->runden; $b++) {
	    if (isset($einzel[$ie])&&($einzel[$ie]->dg==$c+1)&&($einzel[$ie]->runde==$b+1)&&($count[$x]->zps==$einzel[$ie]->zps)&&($count[$x]->mgl_nr==$einzel[$ie]->spieler)) {

			$search = array ('.0', '0.5');
			$replace = array ('', chr(189));
			$punkte_text = str_replace ($search, $replace, $einzel[$ie]->punkte);
			
			if ($einzel[$ie]->kampflos == 0) {
				$dr_einzel = $punkte_text;
			} else {
				if ($config->fe_display_lose_by_default == 0) {
					if($einzel[$ie]->punkte == 0) {
						$dr_einzel = "-";
					} else {
						$dr_einzel = "+";
					}
				} elseif ($config->fe_display_lose_by_default == 1) {
					$dr_einzel =  $punkte_text.' (kl)';
				} else {
					$dr_einzel = $punkte_text;
				}
			}
		
		$pdf->Cell($breite,4,$dr_einzel,1,0,'C');
			if ($einzel[$ie]->kampflos == 0) {
				$gespielt++;
				$sumgespielt++;
			}
		$spl++;
		$sumspl++;
			$pkt += $einzel[$ie]->punkte;
		$ie++;
	  }
	  else $pdf->Cell($breite,4,'',1,0,'C');
	}
  }
 
	if ($spl>0) {
		$pdf->Cell($breite,4,$pkt,1,0,'C');
		$pdf->Cell($breite,4,$spl,1,0,'C');
		$prozent = round(100*($pkt - $spl * $ligapunkte->antritt)/($spl * $ligapunkte->sieg), 1);
		$pdf->Cell($breite,4,$prozent,1,0,'C');
	}
	else {
		$pdf->Cell($breite,4,"",1,0,'C');
		$pdf->Cell($breite,4,"",1,0,'C');
		$pdf->Cell($breite,4,"",1,0,'C');
	
	}
	$pdf->Cell($breite,4,'',0,1,'C');
}
	while (isset($einzel[$ie])) {
		$pdf->Cell($breite1,4,' ',0,0);
		$ztext = utf8_decode("Ergebnis übersprungen, da Spieler nicht in Aufstellung ");
		$ztext .= ' Verein:'.$einzel[$ie]->zps.' Mitglied:'.$einzel[$ie]->spieler;
		$ztext .= ' Durchgang:'.$einzel[$ie]->dg.' Runde:'.$einzel[$ie]->runde;
		$ztext .= ' Brett:'.$einzel[$ie]->brett.' Erg:'.$einzel[$ie]->punkte; 	
		$pdf->Cell(50,4,$ztext,0,1,'L');
		$ie++;
	}

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	if (($o_nr == 0) or ($mannschaft[0]->sg_zps <= "0"))
		$pdf->Line($x+$breite1,$y+2,$x+170,$y+2);
	else
		$pdf->Line($x+$breite1,$y+2,$x+178,$y+2);
	$pdf->Cell(8,4,'',0,1,'C');
	
	$pdf->Cell($breite1,4,' ',0,0);
	$pdf->Cell(12,4,JText::_('TEAM_TOTAL'),0,0,'C');
	if (($o_nr == 0) or ($mannschaft[0]->sg_zps <= "0"))
		$pdf->Cell(40,4,'',0,0);
	else
		$pdf->Cell(48,4,'',0,0);
	$pdf->Cell(10,4,'',0,0,'R');
	$pkt = 0;
	$spl = 0;
  for ($c=0; $c<$mannschaft[0]->dg; $c++) {
	for ($b=0; $b<$mannschaft[0]->runden; $b++) {
		while (isset($bp[$spl]) AND $bp[$spl]->tln_nr != $mannschaft[0]->tln_nr) { $spl++; }
		if (isset($bp[$spl]->runde) AND $bp[$spl]->runde == $b+1) { 
			$pdf->Cell($breite,4,str_replace ('.0', '', $bp[$spl]->brettpunkte),1,0,'C');
			$spl++;
		}
		else $pdf->Cell($breite,4,'',1,0,'C');
	}
  }
	if ($sumspl>0) {
		$pdf->Cell($breite,4,str_replace ('.0', '', $sumbp[0]->summe),1,0,'C');
		$pdf->Cell($breite,4,$sumspl,1,0,'C');
		$prozent = round (100 * ($sumbp[0]->summe - $sumgespielt * $antritt) / ($sumgespielt * $sieg), 1);
		$pdf->Cell($breite,4,$prozent,1,0,'C');
	} else {
		$pdf->Cell($breite,4,"",1,0,'C');
		$pdf->Cell($breite,4,"",1,0,'C');
		$pdf->Cell($breite,4,"",1,0,'C');
	}	
	$pdf->Cell($breite,4,'',0,1,'C');
}
	$pdf->Ln();

$pdf->SetFont('Times','B',$font);
	$pdf->Ln();
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(80,8,utf8_decode(JText::_('TEAM_PLAN')),0,1,'L');
	
$pdf->SetFont('Times','',$font);
	$pdf->Cell(10,8,' ',0,0);
	$pdf->Cell(12,8,JText::_('TEAM_ROUNDS'),0,0,'C');
	$pdf->Cell(12,8,JText::_('TEAM_PAIR'),0,0,'C');
	$pdf->Cell(30,8,JText::_('TEAM_DATE'),0,0,'L');
	$pdf->Cell(40,8,JText::_('TEAM_HOME'),0,0,'L');
	$pdf->Cell(40,8,JText::_('TEAM_GUEST'),0,0,'L');
	$pdf->Cell(8,8,'',0,1,'C');
	
	$cnt = 0;
	$ibpr = 0;
	foreach ($bpr as $bpr1) {
//		echo "<br>bpr: "; var_dump($bpr1);
	}
	foreach ($plan as $planl) {
//		echo "<br>pl: "; var_dump($planl);
	}
	foreach ($plan as $planl) { 
		//$datum =JFactory::getDate($planl->datum);
		$hpkt = "";
		$gpkt = "";
		$pdf->Cell(10,4,' ',0,0);
		$pdf->Cell(12,4,$planl->runde,0,0,'C');
		$pdf->Cell(12,4,$planl->paar,0,0,'C');
		while (isset($termin[$cnt]->nr) AND ($planl->runde + $mannschaft[0]->runden*($planl->dg -1)) > $termin[$cnt]->nr) { 
			$cnt++; }
		
		if (isset($termin[$cnt]->nr) AND ($planl->runde + $mannschaft[0]->runden*($planl->dg -1))== $termin[$cnt]->nr) { 
			if ($termin[$cnt]->datum == '0000-00-00') $pdf->Cell(30,4,' ',0,0,'L');
			else $pdf->Cell(30,4,JHTML::_('date',  $termin[$cnt]->datum, JText::_('DATE_FORMAT_CLM')),0,0,'L');
			$cnt++;
			$pdf->Cell(40,4,utf8_decode($planl->hname),0,0,'L');
			$pdf->Cell(40,4,utf8_decode($planl->gname),0,0,'L');
			$pdf->Cell(2,4,'',0,0,'C');
			while (isset($bpr[$ibpr]) AND $bpr[$ibpr]->runde < $planl->runde) { $ibpr++; }
			for ($b=0; $b<2; $b++) {
				if ((!isset($bpr[$ibpr])) OR ($bpr[$ibpr]->runde > $planl->runde)) break;
				if (($bpr[$ibpr]->runde == $planl->runde) AND ($bpr[$ibpr]->tln_nr == $planl->gegner)) $gpkt = $bpr[$ibpr]->brettpunkte;
				if (($bpr[$ibpr]->runde == $planl->runde) AND ($bpr[$ibpr]->tln_nr == $planl->tln_nr)) $hpkt = $bpr[$ibpr]->brettpunkte;
				$ibpr++; 
			}
			$pdf->Cell(8,4,$hpkt,1,0,'C');
			$pdf->Cell(4,4,':',0,0,'C');
			$pdf->Cell(8,4,$gpkt,1,0,'C');
			$pdf->Cell(8,4,'',0,1,'C');
		}
	}
}
 
// Ausgabe
$pdf->Output(JText::_('TEAM').' '.utf8_decode($mannschaft[0]->name).'.pdf','D');

?>
 
