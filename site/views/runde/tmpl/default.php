<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip', '.CLMTooltip');
 
function RGB($Hex){ 
	if (substr($Hex,0,1) == "#") $Hex = substr($Hex,1);
	$R = substr($Hex,0,2);
	$G = substr($Hex,2,2);
	$B = substr($Hex,4,2);
	$R = hexdec($R);
	$G = hexdec($G);
	$B = hexdec($B);	
	$R = $R - 32; if ($R < 0) $R =0;
	$G = $G - 32; if ($G < 0) $G =0;
	$B = $B - 32; if ($B < 0) $B =0;
	$R=dechex($R);
	If (strlen($R)<2) $R='0'.$R;
	$G=dechex($G);
	If (strlen($G)<2) $G='0'.$G;
	$B=dechex($B);
	If (strlen($B)<2) $B='0'.$B;
return '#'.$R.$G.$B;
}
// ist die Aktuelle Runde abgeschlossen //
$NO_RESULT_YET=0;
$RESULT_YET=0;

$lid		= JRequest::getInt('liga','1'); 
$sid		= JRequest::getInt('saison','1');
$runde		= JRequest::getInt( 'runde', '1' );
$dg			= JRequest::getInt('dg','1');
$item		= JRequest::getInt('Itemid','1');
$liga		= $this->liga;
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $liga[0]->params);
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($params['pgntype'])) $params['pgntype']= 0;
	if (!isset($params['dwz_date'])) $params['dwz_date'] = '0000-00-00';
	if (!isset($params['round_date'])) $params['round_date'] = '0';
	if (!isset($params['noBoardResults'])) $params['noBoardResults'] = '0';
	if (!isset($params['ReportForm'])) $params['ReportForm'] = '0';
$einzel		= $this->einzel;
$pgn		= JRequest::getInt('pgn','0'); 
$detail		= JRequest::getInt('detail','0');
if ($detail == 0) $detailp = '1'; else $detailp = '0';

	// Userkennung holen
	$user	=JFactory::getUser();
	$jid	= $user->get('id');
    // Check ob User Mitglied eines Vereins dieser Liga ist
	if ($jid != 0) {
		$clmuser = $this->clmuser;
		$club_jid = false;
		foreach ($einzel as $einz) {
			if ($einz->zps == $clmuser[0]->zps OR $einz->gzps == $clmuser[0]->zps) {
				$club_jid = true; }
		}
	}
  if (($pgn == 1) OR ($pgn == 2)) { 
	$config		= clm_core::$db->config();
	$name_subuml	= $config->fe_runde_subuml;
	$clmuser = $this->clmuser;
	$nl = "\n";
	$file_name = utf8_decode($liga[0]->name).'_'.utf8_decode($liga[$runde-1]->rname);
	if ($pgn == 1) $file_name .= '_'.utf8_decode($clmuser[0]->zps);
	$file_name .= '.pgn'; 
	$file_name = strtr($file_name,' ','_');
	if (!file_exists('components'.DS.'com_clm'.DS.'pgn'.DS)) mkdir('components'.DS.'com_clm'.DS.'pgn'.DS);
	$pdatei = fopen('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name,"wt");
	foreach ($einzel as $einz) {
	  if (($einz->zps == $clmuser[0]->zps) OR ($einz->gzps == $clmuser[0]->zps) OR ($pgn == 2)) {
		  $gtmarker = "*";
		  $resulthint = "";
		switch ($params['pgntype']) {
		  case 1:
			fputs($pdatei, '[Event "'.utf8_decode($liga[0]->name).'"]'.$nl);
			break;
		  case 2:
			fputs($pdatei, '[Event "'.utf8_decode($params['pgnlname']).'"]'.$nl);
			break;
		  case 3:
			fputs($pdatei, '[Event "'.utf8_decode($einz->name).' - '.utf8_decode($einz->mgname).'"]'.$nl);
			break;
		  case 4:
			fputs($pdatei, '[Event "'.utf8_decode($einz->sname).' - '.utf8_decode($einz->smgname).'"]'.$nl);
			break;
		  case 5:
			fputs($pdatei, '[Event "'.utf8_decode($params['pgnlname']).': '.utf8_decode($einz->sname).' - '.utf8_decode($einz->smgname).'"]'.$nl);
			break;
		  default:
		 	fputs($pdatei, '[Event "'.'"]'.$nl);
		}
		fputs($pdatei, '[Site "?"]'.$nl);
		fputs($pdatei, '[Date "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('Y.m.d')).'"]'.$nl);
		fputs($pdatei, '[Round "'.$runde.'.'.$einz->paar.'"]'.$nl);
		fputs($pdatei, '[Board "'.$einz->brett.'"]'.$nl);
		if ($name_subuml == 1) {
			$einz->hname = clm_core::$load->sub_umlaute($einz->hname);
			$einz->gname = clm_core::$load->sub_umlaute($einz->gname);
			$einz->name = clm_core::$load->sub_umlaute($einz->name);
			$einz->mgname = clm_core::$load->sub_umlaute($einz->mgname);
		}
		if ($einz->weiss == "0") {
			fputs($pdatei, '[White "'.utf8_decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->gdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->hdwz.'"]'.$nl);
			if ($einz->erg_text == "0,5-0,5") { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($einz->erg_text == "1-0") { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($einz->erg_text == "0-1") { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($einz->erg_text == "-/+") { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".utf8_decode('Weiß gewinnt kampflos')."}"; $gtmarker = "1-0"; }
			elseif ($einz->erg_text == "+/-") { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{Schwarz gewinnt kampflos}"; $gtmarker = "0-1"; }
			elseif ($einz->erg_text == "-/-") { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{beide verlieren kampflos}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		} else {
			fputs($pdatei, '[White "'.utf8_decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->hdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->gdwz.'"]'.$nl);
			if ($einz->erg_text == "0,5-0,5") { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($einz->erg_text == "1-0") { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($einz->erg_text == "0-1") { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($einz->erg_text == "-/+") { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{Schwarz gewinnt kampflos}"; $gtmarker = "0-1"; }
			elseif ($einz->erg_text == "+/-") { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".utf8_decode('Weiß gewinnt kampflos')."}"; $gtmarker = "1-0"; }
			elseif ($einz->erg_text == "-/-") { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{beide verlieren kampflos}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		}
		fputs($pdatei, '[PlyCount "0"]'.$nl);
		fputs($pdatei, '[EventDate "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('Y.m.d')).'"]'.$nl);
		fputs($pdatei, '[SourceDate "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('Y.m.d')).'"]'.$nl);
		fputs($pdatei, ' '.$nl);
		fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
		fputs($pdatei, ' '.$nl);
	  }
	}
	fclose($pdatei);
    header('Content-Disposition: attachment; filename='.$file_name);
		header('Content-type: text/html');
		header('Cache-Control:');
		header('Pragma:');
		readfile('components'.DS.'com_clm'.DS.'pgn'.DS.$file_name);
		flush();
		JFactory::getApplication()->close();
  }	

$runde_t = $runde + (($dg - 1) * $liga[0]->runden);  
// Test alte/neue Standardrundenname bei 2 Durchgängen, nur bei Ligen/Turniere vor 2013 (Archiv!)
if ($liga[$runde_t-1]->datum < '2013-01-01') {
if ($liga[0]->durchgang > 1) {
	if ($liga[$runde_t-1]->rname == JText::_('ROUND').' '.$runde_t) {  //alt
		if ($dg == 1) { $liga[$runde_t-1]->rname = JText::_('ROUND').' '.$runde." (".JText::_('PAAR_HIN').")";}
		if ($dg == 2) { $liga[$runde_t-1]->rname = JText::_('ROUND').' '.$runde." (".JText::_('PAAR_RUECK').")";}
    }
} }

$runden_modus = $liga[0]->runden_modus;
$runde_orig = $runde;
if ($dg == 2) { $runde = $runde + $liga[0]->runden; }
if ($dg == 3) { $runde = $runde + (2 * $liga[0]->runden); }
if ($dg == 4) { $runde = $runde + (3 * $liga[0]->runden); }
 
// Browsertitelzeile setzen
$doc =JFactory::getDocument();
$daten['title'] = $liga[0]->name.', '.$liga[$runde-1]->rname;      // JText::_('ROUND').' '.$runde; 
if(isset($liga[$runde-1]->datum)) { $daten['title'] .= ' '.JText::_('ON_DAY').' '.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('DATE_FORMAT_CLM_F'));
	if(isset($liga[$runde-1]->startzeit)) { $daten['title'] .= '  '.substr($liga[$runde-1]->startzeit,0,5).' Uhr'; } }
$doc->setTitle($daten['title']);

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$rang_runde	= $config->fe_runde_rang;
$clm_zeile1			= $config->zeile1;
$clm_zeile2			= $config->zeile2;
$clm_zeile1D			= RGB($clm_zeile1);
$clm_zeile2D			= RGB($clm_zeile2);

?>

<div >
<div id="runde">

<?php
$ok=$this->ok;

if ((isset($ok[0]->sl_ok)) AND ($ok[0]->sl_ok > 0)) $hint_freenew = JText::_('CHIEF_OK');  
if ((isset($ok[0]->sl_ok)) AND ($ok[0]->sl_ok == 0)) $hint_freenew = JText::_('CHIEF_NOK');  
if ((!isset($ok[0]->sl_ok))) $hint_freenew = JText::_('CHIEF_NOK');  

if (isset($liga[$runde-1]->datum) AND $liga[$runde-1]->datum =='0000-00-00') {
?>

<div class="componentheading"><?php echo $liga[0]->name.', '.$liga[$runde-1]->rname;      // JText::_('ROUND').' '.$runde; 
?>
<?php } else { ?>
<div class="componentheading">
	<?php echo $liga[0]->name.', '.$liga[$runde-1]->rname;      // JText::_('ROUND').' '.$runde; 
	if(isset($liga[$runde-1]->datum)) { echo ' '.JText::_('ON_DAY').' '.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('DATE_FORMAT_CLM_F')); 
		if($params['round_date'] == '0' and isset($liga[$runde-1]->startzeit) and $liga[$runde-1]->startzeit != '00:00:00') { echo '  '.substr($liga[$runde-1]->startzeit,0,5); } 
		if($params['round_date'] == '1' and isset($liga[$runde-1]->enddatum) and $liga[$runde-1]->enddatum > '1970-01-01' and $liga[$runde-1]->enddatum != $liga[$runde-1]->datum) { 
			echo ' - '.JHTML::_('date',  $liga[$runde-1]->enddatum, JText::_('DATE_FORMAT_CLM_F'));} }
    ?>
    
    <?php } ?>
    
    <div id="pdf">
	
	<?php 
	
	// PGN eigene Paarung
	if (($params['pgntype'] > 0) AND ($jid != 0) AND ($club_jid == true)) { 
		echo CLMContent::createPGNLink('runde', JText::_('ROUND_PGN_CLUB'), array('saison' => $liga[0]->sid, 'liga' => $liga[0]->id, 'runde' => $runde, 'dg' => $dg) );
	} 
	
	// PGN gesamte Runde
	if (($params['pgntype'] > 0) AND ($jid != 0)) {
		echo CLMContent::createPGNLink('runde', JText::_('ROUND_PGN_ALL'), array('saison' => $liga[0]->sid, 'liga' => $liga[0]->id, 'runde' => $runde, 'dg' => $dg), 2 );
   } 
    
	// PDF
	echo CLMContent::createPDFLink('runde', JText::_('PDF_ROUND'), array('saison' => $liga[0]->sid, 'layout' => 'runde', 'liga' => $liga[0]->id, 'runde' => $runde_orig, 'dg' => $dg));
	?>
	<?php if ($liga[0]->runden_modus != 4 OR (isset($liga[$runde-1]->datum) AND ($liga[$runde-1]->datum > '2014-05-31'))) { ?>
		<div class="pdf"><a href="index.php?option=com_clm&view=runde&Itemid=<?php echo $item ?>&saison=<?php echo $liga[0]->sid ?>&liga=<?php echo $liga[0]->id ?>&runde=<?php echo $runde_orig ?>&dg=<?php echo $dg ?>&detail=<?php echo $detailp ?>"><img src="<?php echo CLMImage::imageURL('lupe.png') ?>" width="16" height="19" alt="PDF" class="CLMTooltip" title="<?php echo JText::_('Details ein/aus') ?>"  /></a>
		</div>
    <?php } ?>
	</div>
</div>
<div class="clr"></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php');

//if (
if ( !$liga OR $liga[0]->published == "0") { 
echo "<br>". CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD'))."<br>"; }
else if ($liga[0]->rnd == 0){ 
echo "<br>". CLMContent::clmWarning(JText::_('NO_ROUND_CREATED').'<br>'.JText::_('NO_ROUND_CREATED_HINT'))."<br>"; }
else if ($liga[$runde - 1]->pub == 0){ 
echo "<br>". CLMContent::clmWarning(JText::_('ROUND_UNPUBLISHED').'<br>'.JText::_('ROUND_UNPUBLISHED_HINT'))."<br>"; } 
else {   ?>

<?php // Kommentare zur Liga
if (isset($liga[$runde-1]->comment) AND $liga[$runde-1]->comment <> "") { ?>
<div id="desc">
    <p class="run_note_title"><?php echo JText::_('NOTICE') ?></p>
    <p><?php echo nl2br($liga[$runde-1]->comment); ?></p>
</div>
<?php } 

// Variablen ohne foreach setzen
$dwzschnitt	=$this->dwzschnitt;
$dwzgespielt=$this->dwzgespielt;
$paar		=$this->paar;
 
$summe		=$this->summe;
//$ok=$this->ok;

// Ergebnistext f�r flexibele Punktevergabe holen
$erg_text = CLMModelRunde::punkte_text($liga[0]->id);

// Array für DWZ Schnitt setzen
$dwz = array();
for ($y=1; $y< ($liga[0]->teil)+1; $y++){
	if ($params['dwz_date'] == '0000-00-00') {
		if(isset($dwzschnitt[($y-1)]->dwz)) {
		$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
	} else {
		if(isset($dwzschnitt[($y-1)]->start_dwz)) {
		$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; }
	}
}
// Rundenschleife
?>
<br>

<table cellpadding="0" cellspacing="0" class="runde">
    <tr><td colspan="9">
    <div>
        <?php // Wenn SL_OK dann Haken anzeigen (nur wenn Staffelleiter eingegeben ist)
         if (isset($liga[0]->mf_name)) {
         if (isset($ok[0]->sl_ok) AND ($ok[0]->sl_ok > 0)) { ?>
            <div class="run_admit"><img  src="<?php echo CLMImage::imageURL('accept.png'); ?>" class="CLMTooltip" title="<?php echo $hint_freenew; 	//echo JText::_('CHIEF_OK'); ?>" /></div>
            <?php } 
         else { ?>
            <div class="run_admit"><img  src="<?php echo CLMImage::imageURL('con_info.png'); ?>" class="CLMTooltip" title="<?php echo $hint_freenew; 	//echo JText::_('CHIEF_OK'); ?>" /></div>
        <?php } } ?>
        <div class="run_titel">
            <a href="index.php?option=com_clm&amp;view=paarungsliste&amp;liga=<?php echo $liga[0]->id ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga[$runde-1]->rname; ?><img src="<?php echo CLMImage::imageURL('cancel_f2.png'); ?>" title="<?php echo JText::_('ROUND_BACK') ?>"/></a>
        </div>
    </div>
    </td></tr>
<?php
//$z=(($liga[0]->teil)/2)*($runde-1);
// Teilnehmerschleife
$w=0;
$z2=0;
$zz=0;
for ($y=0; $y< ($liga[0]->teil)/2; $y++){

if (isset($paar[$y]->htln)) {  // Leere Begegnungen ausblenden 
	if ($params['round_date'] == '1' AND $paar[$y]->pdate > '1970-01-01') { ?>
		<tr><th colspan="8" class="paarung2" style="text-align: right;">
			<?php 
			echo JHTML::_('date',  $paar[$y]->pdate, JText::_('DATE_FORMAT_CLM_F')); 
			if ($paar[$y]->ptime > '00:00:00') echo '  '.substr($paar[$y]->ptime,0,5); 
			?>
		</th></tr>
	<?php } ?>
    <tr>
        <th colspan="3" class="paarung2">
        <?php
        $edit=0;
        $medit=0;
		?> <div class=paarung> <?php	if ($liga[0]->rang == 0 AND $paar[$y]->hname != 'spielfrei' AND $paar[$y]->gname != 'spielfrei' AND $params['ReportForm'] != '0') {   // $jid != 0 AND 
		?>
            <div class="run_admit"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&amp;layout=paarung&amp;runde=<?php echo $runde_orig; ?>&amp;dg=<?php echo $dg; ?>&amp;paarung=<?php echo ($y + 1); ?>&amp;format=pdf"><label for="name" class="hasTip"><img  src="<?php echo CLMImage::imageURL('pdf_button.png'); ?>"  class="CLMTooltip" title="<?php echo JText::_('PAIRING_PDF'); ?>" /></label></a>
			<?php } ?>
		</div> <?php
        // Meldenden einfügen wenn Runde eingegeben wurde
        if (isset($einzel[$w]->paar) AND $einzel[$w]->paar == ($y+1)) { ?>
            <div class="run_admit"><label for="name" class="hasTip"><img  src="<?php echo CLMImage::imageURL('edit_f2.png'); ?>"  class="CLMTooltip" title="<?php echo JText::_('REPORTED_BY').' '.$summe[$z2]->name; ?>" /></label>
            </div>
        <?php }
        if (isset($paar[$y]->hpublished) AND $paar[$y]->hpublished == 1 AND $params['noBoardResults'] == '0') { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$y]->htln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$y]->hname; ?></a>
        <?php } else {
        if (isset($paar[$y]->hname)){
        echo $paar[$y]->hname;
        }} ?>
        </th>
        <th class="paarung">
        <?php if (isset($dwzgespielt[$zz]->dwz) AND $dwzgespielt[$zz]->paar == ($y+1) AND $paar[$y]->htln !=0 AND $paar[$y]->gtln != 0)
                { echo round($dwzgespielt[$zz]->dwz); }
                else { if(isset($paar[$y]->htln) AND isset($dwz[($paar[$y]->htln)])) { echo round($dwz[($paar[$y]->htln)]); }} ?>
        </th>
        <th class="paarung">
        <?php
        // Ergebnis Mannschaft
        $paar_exist = 0;
        if ($summe[$z2]->sum !="" AND $summe[$z2]->paarung == ($y+1)) {
            $paar_exist = 1;
            echo $summe[$z2]->sum.' : '.$summe[$z2+1]->sum;
			if (($runden_modus == 4 OR $runden_modus == 5) AND ($summe[$z2]->sum == $summe[$z2+1]->sum)) $remis_com = 1; else $remis_com = 0;
            if ($summe[$z2]->dwz_editor !="") { $medit++; }
             }
        else { ?> : <?php }
        $z2=$z2+2; ?>
        </th>
        <th class="paarung2" colspan ="2">
        <?php // Name Gastmannschaft
        if (isset($paar[$y]->gpublished) AND $paar[$y]->gpublished == 1 AND $params['noBoardResults'] == '0') { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$y]->gtln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$y]->gname; ?></a>
        <?php } else {
        if (isset($paar[$y]->gname)) {
        echo $paar[$y]->gname;
        }} ?>
        </th>
        <th class="paarung">
        <?php if (isset($dwzgespielt[$zz]->dwz) AND $dwzgespielt[$zz]->paar == ($y+1) AND $paar[$y]->htln !=0 AND $paar[$y]->gtln != 0)
                { echo round($dwzgespielt[$zz]->gdwz);
                    $zz++;
                }
                else { if(isset($paar[$y]->gtln) AND isset($dwz[($paar[$y]->gtln)])) { echo round($dwz[($paar[$y]->gtln)]); }} ?>
        </th>
    </tr>
<?php
}
if (isset($einzel[$w]->paar) AND $einzel[$w]->paar == ($y+1)) {
// Bretter
for ($x=0; $x<$liga[0]->stamm; $x++) {

	if ($x%2 != 0) { $zeilenr = 'zeile1'; 
		$zeiled = $clm_zeile1D; }
	else { $zeilenr = 'zeile2';
		$zeiled = $clm_zeile2D; }
	if ($einzel[$w]->ergebnis != 8) { $RESULT_YET++;    
?>
    <tr class="<?php echo $zeilenr; ?>">
    <td class="paarung"><div><?php echo $einzel[$w]->brett; ?></div></td>
	<?php if ($detail == 1) { 
		if ($liga[0]->rang > 0) $einzel[$w]->hsnr = $einzel[$w]->tmnr.'-'.$einzel[$w]->trang; ?>
    <td class="paarung" style="border-right: none;<?php if ($einzel[$w]->weiss == 0) echo 'background-color:'.$zeiled.';';?>"><div><font size=-2><?php echo $einzel[$w]->hsnr; ?></font></div></td>
    <td class="paarung2" style="border-left: none" colspan ="1">
	  <div><?php if ($einzel[$w]->zps =="ZZZZZ") {echo "N.N.";} else { 
		if ($einzel[$w]->zps != "-1") { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->zps; ?>&mglnr=<?php echo $einzel[$w]->spieler; ?>&PKZ=<?php echo $einzel[$w]->PKZ; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $einzel[$w]->hname; } 
		else echo $einzel[$w]->hname; } ?></div></td>
	<?php } else { ?>
    <td class="paarung2" colspan ="2"><div><?php if ($einzel[$w]->zps =="ZZZZZ") {echo "N.N.";} else { 
		if ($einzel[$w]->zps != "-1") { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->zps; ?>&mglnr=<?php echo $einzel[$w]->spieler; ?>&PKZ=<?php echo $einzel[$w]->PKZ; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $einzel[$w]->hname; } 
		else echo $einzel[$w]->hname; } ?></div></td>
	<?php } ?>
    <td class="paarung"><div><?php if ($params['dwz_date'] == '0000-00-00') echo $einzel[$w]->hdwz; else echo $einzel[$w]->hstart_dwz;?></a></div></td>
        <?php if ($einzel[$w]->dwz_edit !="") { $edit++; ?>
    <td class="paarung"><div><b><?php echo $einzel[$w]->dwz_text; ?><font size="1"><br>( <?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?> )</font></b></div></td>
        <?php } else { ?>
    <td class="paarung"><div><b><?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?></b></div></td>
        <?php } ?>
	<?php if ($detail == 1) { 
		if ($liga[0]->rang > 0) $einzel[$w]->gsnr = $einzel[$w]->smnr.'-'.$einzel[$w]->srang; ?>
    <td class="paarung" style="border-right: none;<?php if ($einzel[$w]->weiss != 0) echo 'background-color:'.$zeiled.';';?>"><div><font size=-2><?php echo $einzel[$w]->gsnr; ?></font></div></td>
    <td class="paarung2" style="border-left: none" colspan ="1"><div><?php if ($einzel[$w]->gzps =="ZZZZZ") {echo "N.N.";} else { 
		if ($einzel[$w]->zps != "-1") { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->gzps; ?>&mglnr=<?php echo $einzel[$w]->gegner; ?>&PKZ=<?php echo $einzel[$w]->gPKZ; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $einzel[$w]->gname; } 
		else echo $einzel[$w]->gname; } ?></div></td>
	<?php } else { ?>
    <td class="paarung2" colspan ="2"><div><?php if ($einzel[$w]->gzps =="ZZZZZ") {echo "N.N.";} else { 
		if ($einzel[$w]->gzps != "-1") { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->gzps; ?>&mglnr=<?php echo $einzel[$w]->gegner; ?>&PKZ=<?php echo $einzel[$w]->gPKZ; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $einzel[$w]->gname; } 
		else echo $einzel[$w]->gname; } ?></div></td>
	<?php } ?>
    <td class="paarung"><div><?php if ($params['dwz_date'] == '0000-00-00') echo $einzel[$w]->gdwz; else echo $einzel[$w]->gstart_dwz; ?></a></div></td>
    </tr>
<?php }
$w++; }
 
if ($edit > 0 OR $medit >0) { ?>
	<tr><td colspan ="8"><?php if ($medit >0 AND $edit =="0"){ echo JText::_('CHIEF_EDIT_TEAM'); }
	else { echo JText::_('CHIEF_EDIT_SINGLE'); }
		echo JText::_('BREACH_TO') ?>
	<?php if($edit >0) { ?><br><?php echo JText::_('CHIEF_EDIT_DWZ') ?></b><?php } ?>
	</td></tr>
<?php } elseif ($remis_com == 1) { ?>
	<tr><td colspan ="8"><?php  if ($paar[$y]->ko_decision == 1) { //1
									if ($paar[$y]->wertpunkte > $paar[$y]->gwertpunkte) echo JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$y]->wertpunkte." : ".$paar[$y]->gwertpunkte." für ".$paar[$y]->hname; 
									else echo JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$y]->gwertpunkte." : ".$paar[$y]->wertpunkte." für ".$paar[$y]->gname; } 
								if ($paar[$y]->ko_decision == 2) echo JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$y]->hname;
								if ($paar[$y]->ko_decision == 3) echo JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$y]->gname; 
								if ($paar[$y]->ko_decision == 4) echo JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$y]->hname;
								if ($paar[$y]->ko_decision == 5) echo JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$y]->gname; ?>		
	</td></tr>
<?php } ?>

<?php if ($paar[$y]->comment != "") { ?>
<tr><td colspan ="8"><?php  echo JText::_('PAAR_COMMENT').$paar[$y]->comment; ?>		
	</td></tr>
<?php } ?>

<tr><td colspan ="8" class="noborder">&nbsp;</td></tr>
<?php } elseif ((isset($paar[$y]->gpublished) AND $paar[$y]->gpublished == 1 AND $paar[$y]->hpublished == 1) AND ($paar_exist== 0)) { ?>
    <tr><td colspan ="8" align="left"><?php echo JText::_('NO_RESULT_YET'); $NO_RESULT_YET++; ?></td></tr>
    <?php } elseif (isset($paar[$y]) AND $paar[$y]->comment != "") { ?>
	<tr><td colspan ="8"><?php  echo JText::_('PAAR_COMMENT').$paar[$y]->comment; ?></td></tr>
	<?php } else { ?><tr><td colspan ="8" class="noborder">&nbsp;</td></tr><?php }
}
?>
</table>

<div class="legend">
    <p><img src="<?php echo CLMImage::imageURL('cancel_f2.png'); ?>" /> = <?php echo JText::_('HIDE_DETAILS') ?></p>
    <p><img src="<?php echo CLMImage::imageURL('edit_f2.png'); ?>" /> = <?php echo JText::_('REPORTED_BY') ?></p>
</div>

<?php
// Rangliste
if (($rang_runde =="1") AND ($liga[0]->runden_modus != 4 AND $liga[0]->runden_modus != 5)) { 

$lid		= $liga[0]->id; 
$sid		= JRequest::getInt('saison','1');
$punkte		= $this->punkte;
$spielfrei	= $this->spielfrei;

// Spielfreie Teilnehmer finden //
$diff = $spielfrei[0]->count; ?>

<br>
<div id="rangliste">
<table cellpadding="0" cellspacing="0" class="rangliste">
	<?php 
	if ($liga[0]->liga_mt == 0) { $columns = 4;     //liga
		if ( $liga[0]->b_wertung > 0) $columns++; }
	else { $columns = 3;
		if ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50)  $columns++; 
		if ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50)  $columns++; 
		if ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50)  $columns++;  } ?>
	<tr><th colspan="<?php echo $columns+(($liga[0]->teil-$diff) * $liga[0]->durchgang); ?>"><?php 

if($RESULT_YET>0 && $NO_RESULT_YET>0){
echo JText::_('RANGLISTE').' '.$liga[$runde-1]->rname.' '.JText::_('NOT_FINISH'); 
}else if($RESULT_YET>0 && $NO_RESULT_YET==0){
echo JText::_('RANGLISTE').' '.JText::_('AFTER').' '.$liga[$runde-1]->rname;  
}else {
echo JText::_('RANGLISTE').' '.JText::_('BEFORE').' '.$liga[$runde-1]->rname;
}
?></th></tr><?php //} ?>
	<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
	<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
	<?php if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) { // vollrundig
	// erster Durchgang
		for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
			<th class="rnd"><div><?php echo $rnd+1;?></div></th>
		<?php }
//  zweiter Durchgang 
	if ($liga[0]->durchgang > 1) { for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
		<th class="rnd"><div><?php echo $rnd+1; ?></div></th>
			<?php }
		//  dritter Durchgang 
			if ($liga[0]->durchgang > 2) { for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
				<th class="rnd"><div><?php echo $rnd+1; ?></div></th>
				<?php } 
			//  vierter Durchgang 
				if ($liga[0]->durchgang > 3) { for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
					<th class="rnd"><div><?php echo $rnd+1; ?></div></th>
				<?php }}}}} ?>
	<?php if ($liga[0]->runden_modus == 3) {    // Schweizer System
		for ($rnd=0; $rnd < $liga[0]->runden ; $rnd++) { ?>
			<th class="rndch"><div><?php echo $rnd+1;?></div></th>
		<?php }} ?>
	<th class="mp"><div><?php echo JText::_('MP') ?></div></th>
	<?php if ( $liga[0]->liga_mt == 0) { 		// Liga ?>
		<th class="bp"><div><?php echo JText::_('BP') ?></div></th>
			<?php if ( $liga[0]->b_wertung > 0) { ?><th class="bp"><div><?php echo JText::_('BW') ?></div></th><?php } ?>
	<?php } else {										// CH-Turniere ?>
		<?php if ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1) ?></div></th><?php } ?>
		<?php if ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2) ?></div></th><?php } ?>
		<?php if ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3) ?></div></th><?php } ?>
	<?php } ?>
</tr>

<?php
// Anzahl der Teilnehmer durchlaufen
for ($x=0; $x< ($liga[0]->teil)-$diff; $x++){
	if (!isset($punkte[$x])) break;
// Farbgebung der Zeilen //
if ($x%2 != 0) { $zeilenr	= "zeile2";
		$zeilenr_dg2	= "zeile2_dg2";}
	else { $zeilenr		= "zeile1";
		$zeilenr_dg2	= "zeile1_dg2";}
?>
<tr class="<?php echo $zeilenr; ?>">
<td class="rang<?php 
	if($x < $liga[0]->auf) { echo "_auf"; }
	if($x >= $liga[0]->auf AND $x < ($liga[0]->auf + $liga[0]->auf_evtl)) { echo "_auf_evtl"; }
	if($x >= ($liga[0]->teil-$liga[0]->ab)) { echo "_ab"; }
	if($x >= ($liga[0]->teil-($liga[0]->ab_evtl + $liga[0]->ab)) AND $x < ($liga[0]->teil-$liga[0]->ab) ) { echo "_ab_evtl"; }
	?>"><?php echo $x+1; ?></td>
	<td class="team">
	<?php if ($punkte[$x]->published ==1 AND $params['noBoardResults'] == '0') { ?>
	<div><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $punkte[$x]->tln_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $punkte[$x]->name; ?></a></div>
	<div class="dwz"><?php if (isset($dwz[($punkte[$x]->tln_nr)])) echo "( ".round($dwz[($punkte[$x]->tln_nr)])." )"; else echo "( 0 )"; ?></div>
	<?php } else { ?>
	<div><?php	echo $punkte[$x]->name; ?></div>
	<div class="dwz"><?php if (isset($dwz[($punkte[$x]->tln_nr)])) echo "( ".round($dwz[($punkte[$x]->tln_nr)])." )"; else echo "( 0 )"; } ?></div>
	</td>
<?php
// Anzahl der Runden durchlaufen 1.Durchgang
$runden = CLMModelRunde::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,1,$liga[0]->runden_modus);
$count = 0;
if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) { 
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr; ?>"><?php 
	if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
		if ($runde != "" AND $runden[($punkte[$y]->tln_nr)-2]->runde <= $runde) {
		echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; }
		if ($runde == "") { echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; }
		}
	if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
		if ($runde != "" AND $runden[($punkte[$y]->tln_nr)-1]->runde <= $runde) {
		echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; }
		if ($runde == "") { echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; }
		} ?>
	</td>
	<?php }}}
if ($liga[0]->runden_modus == 3) { 
	for ($y=0; $y< $liga[0]->runden; $y++) { ?>
			<td class="<?php echo $zeilenr; ?>"><?php 
			if (!isset($runden[$y])) echo " ";
			elseif ($runden[$y]->name == "spielfrei") echo "  +";
			//else echo $runden[$y]->rankingpos."/".$runden[$y]->brettpunkte;  
			else echo $runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")";  ?>
			</td>
			<?php }
	}
// Anzahl der Runden durchlaufen 2.Durchgang
	if ($liga[0]->durchgang > 1) {
		$runden_dg2 = CLMModelRunde::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,2,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php 
			if (isset($runden_dg2[($punkte[$y]->tln_nr)-2]) AND isset($runden_dg2[0]) AND $punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
				if ($runde != "" AND ($runden_dg2[($punkte[$y]->tln_nr)-2]->dg < $dg OR 
					($runden_dg2[($punkte[$y]->tln_nr)-2]->dg == $dg AND $runden_dg2[($punkte[$y]->tln_nr)-2]->runde <= $runde_orig))) {
					echo $runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte; }
				if ($runde == "") { echo $runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte; }
				//echo $runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte;
		}
			if (isset($runden_dg2[($punkte[$y]->tln_nr)-1]) AND isset($runden_dg2[0]) AND $punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
				if ($runde != "" AND ($runden_dg2[($punkte[$y]->tln_nr)-1]->dg < $dg OR 
					($runden_dg2[($punkte[$y]->tln_nr)-1]->dg == $dg AND $runden_dg2[($punkte[$y]->tln_nr)-1]->runde <= $runde_orig))) {
					echo $runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte; }
				if ($runde == "") { echo $runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte; }
				//echo $runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte;
		} ?>
	</td>
	<?php }}}
// Anzahl der Runden durchlaufen 3.Durchgang
	if ($liga[0]->durchgang > 2) {
		$runden_dg3 = CLMModelRunde::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,3,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php 
	if (isset($runden_dg3[($punkte[$y]->tln_nr)-2]) AND isset($runden_dg3[0]) AND $punkte[$y]->tln_nr > $runden_dg3[0]->tln_nr) {
				if ($runde != "" AND ($runden_dg3[($punkte[$y]->tln_nr)-2]->dg < $dg OR 
					($runden_dg3[($punkte[$y]->tln_nr)-2]->dg == $dg AND $runden_dg3[($punkte[$y]->tln_nr)-2]->runde <= $runde_orig))) {
					echo $runden_dg3[($punkte[$y]->tln_nr)-2]->brettpunkte; }
				if ($runde == "") { echo $runden_dg3[($punkte[$y]->tln_nr)-2]->brettpunkte; }
				//echo $runden_dg3[($punkte[$y]->tln_nr)-2]->brettpunkte;
			}
			if (isset($runden_dg3[($punkte[$y]->tln_nr)-1]) AND isset($runden_dg3[0]) AND $punkte[$y]->tln_nr < $runden_dg3[0]->tln_nr) {
				if ($runde != "" AND ($runden_dg3[($punkte[$y]->tln_nr)-1]->dg < $dg OR 
					($runden_dg3[($punkte[$y]->tln_nr)-1]->dg == $dg AND $runden_dg3[($punkte[$y]->tln_nr)-1]->runde <= $runde_orig))) {
					echo $runden_dg3[($punkte[$y]->tln_nr)-1]->brettpunkte; }
				if ($runde == "") { echo $runden_dg3[($punkte[$y]->tln_nr)-1]->brettpunkte; }
				//echo $runden_dg3[($punkte[$y]->tln_nr)-1]->brettpunkte;
		} ?>
	</td>
	<?php }}}
// Anzahl der Runden durchlaufen 4.Durchgang
	if ($liga[0]->durchgang > 3) {
		$runden_dg4 = CLMModelRunde::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,4,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php 
	if (isset($runden_dg4[($punkte[$y]->tln_nr)-2]) AND isset($runden_dg4[0]) AND $punkte[$y]->tln_nr > $runden_dg4[0]->tln_nr) {
				if ($runde != "" AND ($runden_dg4[($punkte[$y]->tln_nr)-2]->dg < $dg OR 
					($runden_dg4[($punkte[$y]->tln_nr)-2]->dg == $dg AND $runden_dg4[($punkte[$y]->tln_nr)-2]->runde <= $runde_orig))) {
					echo $runden_dg4[($punkte[$y]->tln_nr)-2]->brettpunkte; }
				if ($runde == "") { echo $runden_dg4[($punkte[$y]->tln_nr)-2]->brettpunkte; }
				//echo $runden_dg4[($punkte[$y]->tln_nr)-2]->brettpunkte;
			}
			if (isset($runden_dg4[($punkte[$y]->tln_nr)-1]) AND isset($runden_dg4[0]) AND $punkte[$y]->tln_nr < $runden_dg4[0]->tln_nr) {
				if ($runde != "" AND ($runden_dg4[($punkte[$y]->tln_nr)-1]->dg < $dg OR 
					($runden_dg4[($punkte[$y]->tln_nr)-1]->dg == $dg AND $runden_dg4[($punkte[$y]->tln_nr)-1]->runde <= $runde_orig))) {
					echo $runden_dg4[($punkte[$y]->tln_nr)-1]->brettpunkte; }
				if ($runde == "") { echo $runden_dg4[($punkte[$y]->tln_nr)-1]->brettpunkte; }
				//echo $runden_dg4[($punkte[$y]->tln_nr)-1]->brettpunkte;
		} ?>
	</td>
	<?php }}}
// Ende Runden
?>
	<td class="mp"><div><?php echo $punkte[$x]->mp; if ($punkte[$x]->abzug > 0) echo '*'; ?></div></td>
	<?php if ( $liga[0]->liga_mt == 0) { // Liga ?>				
		<td class="bp"><div><?php echo $punkte[$x]->bp; if ($punkte[$x]->bpabzug > 0) echo '*'; ?></div></td>
		<?php if ( $liga[0]->b_wertung > 0) { ?><td class="bp"><div><?php echo $punkte[$x]->wp; ?></div></td><?php } ?>
	<?php } else { 								// Turniere ?>
		<?php if ( $liga[0]->tiebr1 == 5 ) { // Brettpunkte
				echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1); if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
			  } elseif ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50) { 
				echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'</div></td>';
			  } ?>
		<?php if ( $liga[0]->tiebr2 == 5 ) { // Brettpunkte
				echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2); if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
			  } elseif ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50) { 
				echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'</div></td>';
			  } ?>
		<?php if ( $liga[0]->tiebr3 == 5 ) { // Brettpunkte
				echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3); if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
			  } elseif ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50) { 
				echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'</div></td>';
			  } ?>
	<?php } ?>
</tr>
<?php }
// Ende Teilnehmer
?>
</table>
<?php if ($diff == 1 AND $liga[0]->ab ==1 ) 
	{echo JText::_(ROUND_NO_RELEGATED_TEAM); }
	if ($diff == 1 AND $liga[0]->ab >1 ) 
	{echo JText::_(ROUND_LESS_RELEGATED_TEAM); }
	?>
</div>
<?php }} // Ende Rangliste
?>
<?php // Wenn SL_OK dann Erklärung für Haken anzeigen (nur wenn Staffelleiter eingegeben ist)
 if (isset($liga[0]->mf_name)) {
 if (isset($ok[0]->sl_ok) AND $ok[0]->sl_ok > 0) { ?>

<div class="legend"><p><img src="<?php echo CLMImage::imageURL('accept.png'); ?>" width="16" height="16"/> = <?php echo JText::_('CHIEF_OK') ?></p></div>
<?php }  ?>

<br>
<?php } ?>


<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>
