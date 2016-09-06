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
//JHtml::_('behavior.tooltip', '.CLMTooltip', $params);
JHtml::_('behavior.tooltip', '.CLMTooltip');

$liga		= $this->liga;
//$sub_liga	=$this->sub_liga;
//$sub_msch	=$this->sub_msch;
//$sub_rnd	=$this->sub_rnd;
$itemid		= JRequest::getInt('Itemid','1');
$sid		= JRequest::getInt( 'saison','1');
$lid		= JRequest::getInt('liga','1');

$sql = ' SELECT `sieg`, `remis`, `nieder`, `antritt`, `man_sieg`, `man_remis`, `man_nieder`, `man_antritt`'
		. ' FROM #__clm_liga'
		. ' WHERE `id` = "' . $lid . '"';
$db =JFactory::getDBO ();
$db->setQuery ($sql);
$ligapunkte = $db->loadObject ();

	//Parameter aufbereiten
	$paramsStringArray = explode("\n", $liga[0]->params);
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($params['btiebr1']) OR $params['btiebr1'] == 0) {   //Standardbelegung
		$params['btiebr1'] = 1;
		$params['btiebr2'] = 2;
		$params['btiebr3'] = 3;
		$params['btiebr4'] = 4;
		$params['btiebr5'] = 0;
		$params['btiebr6'] = 0;
	}
	if (!isset($params['bnhtml']) OR $params['bnhtml'] == 0) {   //Standardbelegung
		$params['bnhtml'] = round(($liga[0]->teil)/2);
	}
 
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

?>
<div >
<div id="statistik">
<?php
$config = clm_core::$db->config();
$googlecharts   = $config->googlecharts;

// Browsertitelzeile setzen
$doc =JFactory::getDocument();
$doc->setTitle(JText::_('LEAGUE_STATISTIK').' '.$liga[0]->name);
	
?>
<div class="componentheading">
<?php echo JText::_('LEAGUE_STATISTIK'); echo "&nbsp;".$liga[0]->name; ?>
<div id="pdf">
<?php
echo CLMContent::createPDFLink('statistik', JText::_('LEAGUE_STAT_PDF'), array('layout' => 'brettbeste', 'saison' => $liga[0]->sid, 'liga' => $liga[0]->id));
?>

</div></div>
<div class="clr"></div>
 
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<?php
if ( !$liga OR $liga[0]->published == "0") { echo '<br>'.CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD')); } 
else { ?>
	<div>
<?php
//$weiss		= $this->weiss;
//$schwarz	= $this->schwarz;
$remis		= $this->remis;
$kampflos	= $this->kampflos;
$heim		= $this->heim;
$gast		= $this->gast;
$gesamt		= $this->gesamt;
//$spieler	= $this->spieler;
$mannschaft	= $this->mannschaft;
$brett		= $this->brett;
//$wbrett		= $this->wbrett;
$gbrett		= $this->gbrett;
$rbrett		= $this->rbrett;
$kbrett		= $this->kbrett;
$bestenliste = $this->bestenliste;
$kgmannschaft	= $this->kgmannschaft;
$kvmannschaft	= $this->kvmannschaft;

$sid 		= JRequest::getInt('saison','1');
$lid 		= JRequest::getInt('liga');
// $itemid 	= JRequest::getInt('Itemid');

// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
	
?>
<br>
<h4><?php echo JText::_('LEAGUE_STAT_ALL') ?></h4>

<?php if (!$bestenliste OR !$liga OR ($gesamt[0]->gesamt == 0)) {
echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES')); 
} else { ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('LEAGUE_STAT_BRETT') ?></th>
		<th colspan="1"><?php echo JText::_('LEAGUE_STAT_PLAYERGAMES') ?></th>
		<th colspan="4"><?php echo JText::_('LEAGUE_STAT_POINTS') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_WHITE') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_BLACK') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_REMIS') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?></th>
	</tr>

	<tr class="anfang">
		<td></td>
		<td class="punkte border"><?php echo JText::_('LEAGUE_STAT_SUM') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_HOME') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_GUEST') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="white border"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="white"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="black border"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="black"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="remis border"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="remis"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="kampflos border"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="kampflos"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
	</tr>

<?php 

$w = 0;
$s = 0;
$r = 0;
$k = 0;

$bretter	= CLMModelStatistik::Bretter();
$brett_all	= CLMModelStatistik::CLMBrett_all($bretter);
$sum_weiss	= 0;
$sum_schwarz = 0;

	for ($x=0; $x < $bretter; $x++) {
		if ($x%2 == 0) { $zeilenr = 'zeile1';
				 }
			else { $zeilenr = 'zeile2'; 
			} ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td align="center"><?php echo $x+1; ?></td>
		<td class="punkte border"><?php echo $brett[$x]->count; ?></td>
		<td class="punkte"><?php echo str_replace ('.0', '', $brett[$x]->sum); ?></td>
		<td class="punkte"><?php echo round (100 * ($brett[$x]->sum - $brett[$x]->count * $ligapunkte->antritt) / ($brett[$x]->count * $ligapunkte->sieg), 1); ?></td>

		<td class="punkte"><?php echo str_replace ('.0', '', $gbrett[$x]->sum); ?></td>
		<td class="punkte"><?php echo round (100 * ($gbrett[$x]->sum - $gbrett[$x]->count * $ligapunkte->antritt) / ($brett[$x]->count * $ligapunkte->sieg), 1); ?></td>

		<td class="white border"><?php echo $brett_all[$x]['w']; $sum_weiss += $brett_all[$x]['w']; ?></td>
		<td class="white"><?php echo round((($brett_all[$x]['w']*100)/$brett[$x]->count),1); ?></td>
		<td class="black border"><?php echo $brett_all[$x]['s']; $sum_schwarz += $brett_all[$x]['s']; ?></td>
		<td class="black"><?php echo round((($brett_all[$x]['s']*100)/$brett[$x]->count),1); ?></td>

<?php if (isset($rbrett[$r]->brett) AND $rbrett[$r]->brett == $x+1) {  ?>
		<td class="remis border"><?php echo $rbrett[$r]->sum; ?></td>
		<td class="remis"><?php echo round((($rbrett[$r]->sum*100)/$brett[$x]->count),1); ?></td>
<?php $r++;} else { ?>
		<td class="remis border">0</td>
		<td class="remis">0</td>
<?php } ?>
<?php if (isset($kbrett[$k]->brett) AND $kbrett[$k]->brett == $x+1) {  ?>
		<td class="kampflos border"><?php echo $kbrett[$k]->sum; ?></td>
		<td class="kampflos"><?php echo round((($kbrett[$k]->sum*100)/$brett[$x]->count),1); ?></td>
<?php $k++;} else { ?>
		<td class="kampflos border">0</td>
		<td class="kampflos">0</td>
<?php } ?>

	</tr>
<?php } ?>
	<tr class="ende">
		<td align="center">&sum;</td>
		<td class="punkte border"><?php echo $gesamt[0]->gesamt; ?></td>
		
		<td class="punkte"><?php echo str_replace ('.0', '', $heim[0]->sum); ?></td>
<!--		<td class="punkte"><?php echo round((($heim[0]->sum*100)/$gesamt[0]->gesamt),1); ?></td>-->
		<td class="punkte"><?php echo round (100 * ($heim[0]->sum - $gesamt[0]->gesamt * $ligapunkte->antritt) / ($gesamt[0]->gesamt * $ligapunkte->sieg), 1); ?></td>		
		<td class="punkte"><?php echo str_replace ('.0', '', $gast[0]->sum); ?></td>
		
<!--		<td class="punkte"><?php echo round((($gast[0]->sum*100)/$gesamt[0]->gesamt),1); ?></td>-->
		<td class="punkte"><?php echo round (100 * ($gast[0]->sum - $gesamt[0]->gesamt * $ligapunkte->antritt) / ($gesamt[0]->gesamt * $ligapunkte->sieg), 0); ?></td>
		
		<td class="white border"><?php echo $sum_weiss; ?></td>
		<td class="white"><?php echo round((($sum_weiss * 100)/$gesamt[0]->gesamt), 1); ?></td>
		<td class="black border"><?php echo $sum_schwarz; ?></td>
		<td class="black"><?php echo round((($sum_schwarz * 100)/$gesamt[0]->gesamt), 1); ?></td>
		<td class="remis border"><?php echo $remis[0]->remis; ?></td>
		<td class="remis"><?php echo round((($remis[0]->remis * 100)/$gesamt[0]->gesamt), 1); ?></td>
		<td class="kampflos border"><?php echo $kampflos[0]->kampflos; ?></td>
		<td class="kampflos"><?php echo round((($kampflos[0]->kampflos * 100)/$gesamt[0]->gesamt), 1); ?></td>
	</tr>

<!-- Google Charts-->
<?php if ( $googlecharts == "1" ) { ?>
    <tr>
    	<td colspan="14">
        <br />
<img src="http://chart.apis.google.com/chart
?chxt=y
&chbh=a,9,12
&chs=300x225
&cht=bvs
&chco=BF7300,DF8600,F49300,FF9900,FFA928,FFB444,FFC164,FFD088
&chd=t:<?php 
$w = 0; $s = 0; $r = 0; $k = 0;
$bretter	= CLMModelStatistik::Bretter();
$brett_all	= CLMModelStatistik::CLMBrett_all($bretter);
$sum_weiss	= 0;
$sum_schwarz = 0;

for ($x=0; $x < $bretter; $x++) { 
	echo $brett_all[$x]['w']; $sum_weiss += $brett_all[$x]['w']; echo ","; 
	echo $brett_all[$x]['s']; $sum_schwarz += $brett_all[$x]['s']; echo ",";
	if (isset($rbrett[$r]->brett) AND $rbrett[$r]->brett == $x+1) { 
		echo $rbrett[$r]->sum . ","; $r++;} else { echo "0,"; } 
	if (isset($kbrett[$k]->brett) AND $kbrett[$k]->brett == $x+1) { 
		echo $kbrett[$k]->sum; $k++;} else { echo "0"; }
	if ( $x < $bretter-1) { echo "|"; }
}  ?>&chdl=<?php 
$bretter	= CLMModelStatistik::Bretter();
$brett_all	= CLMModelStatistik::CLMBrett_all($bretter);
for ($x=0; $x < $bretter; $x++) { echo $x+1; if ( $x < $bretter-1) { echo "|"; } }  ?>
&chxt=x,y
&chxl=0:|<?php echo JText::_('LEAGUE_STAT_WHITE') ?>|<?php echo JText::_('LEAGUE_STAT_BLACK') ?>|<?php echo JText::_('LEAGUE_STAT_REMIS') ?>|<?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?>"  alt="Horizontal bar chart" />
        
<img src="http://chart.apis.google.com/chart
?chs=300x225
&cht=p
&chd=t:<?php echo $sum_weiss/2; ?>,<?php echo $sum_schwarz/2; ?>,<?php echo $remis[0]->remis; ?>,<?php echo $kampflos[0]->kampflos; ?>
&chdl=<?php echo JText::_('LEAGUE_STAT_WHITE') ?>|<?php echo JText::_('LEAGUE_STAT_BLACK') ?>|<?php echo JText::_('LEAGUE_STAT_REMIS') ?>|<?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?>
&chdlp=b" width="300" height="225" alt="" />

        </td>
    </tr>
<?php } ?>
<!-- Google Charts ENDE-->
    
</table>
<?php } 
$count = count($bestenliste);
?>
<br>

<a title="<?php echo JText::_('LEAGUE_STAT_PLAYERLIST') ?>" href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?>&amp;layout=bestenliste<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><h4><?php echo JText::_('LEAGUE_RATING_BEST_PLAYER_I') ?> <?php if ($count < 10 AND $count >0) { echo $count; } else { ?> 10<?php } ?> <?php echo JText::_('LEAGUE_RATING_BEST_PLAYER_II') ?> <?php echo $liga[0]->name; ?></h4></a>
<?php if (!$bestenliste) {
echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));
} else { ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_DWZ') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_CLUB') ?></th>
		<?php 
 		$ex = 0; $ey = 0;
		for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			$str_btiebr = 'btiebr'.$xx;
			if (!isset($params[$str_btiebr])) continue;
			if ($params[$str_btiebr] == 1) $hstring = JText::_('LEAGUE_STAT_PLAYERPOINTS');
			elseif ($params[$str_btiebr] == 2) $hstring = JText::_('LEAGUE_STAT_PLAYERGAMES');
			elseif ($params[$str_btiebr] == 3) $hstring = JText::_('LEAGUE_STAT_PLAYERLEVEL');
			elseif ($params[$str_btiebr] == 4) $hstring = JText::_('LEAGUE_STAT_RATING');
			elseif ($params[$str_btiebr] == 5) $hstring = JText::_('LEAGUE_STAT_PERCENT');
			elseif ($params[$str_btiebr] == 6) $hstring = JText::_('LEAGUE_STAT_POINTS_K');
			elseif ($params[$str_btiebr] == 7) $hstring = JText::_('LEAGUE_STAT_GAMES_K');
			elseif ($params[$str_btiebr] == 8) $hstring = JText::_('LEAGUE_STAT_PERCENT_K');
			elseif ($params[$str_btiebr] == 9) $hstring = JText::_('LEAGUE_STAT_BNUMBERS');
			if ($params[$str_btiebr] > 0) { ?>
		<th><?php echo $hstring ?></th>
			<?php } } ?>
	</tr>

<?php 
if ($count < 10) { $a = $count; }
	else { $a=10; }
	for ($x=0; $x < $a; $x++) {
		if ($x%2 == 0) { $zeilenr = 'zeile1'; }
			else { $zeilenr = 'zeile2'; } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $x+1; ?></td>
		<td><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $bestenliste[$x]->zps; ?>&mglnr=<?php echo $bestenliste[$x]->mgl_nr; ?>&PKZ=<?php echo $bestenliste[$x]->PKZ; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $bestenliste[$x]->Spielername; ?></a></td>
		<td><?php echo $bestenliste[$x]->DWZ; ?></td>
		<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $bestenliste[$x]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $bestenliste[$x]->Vereinname; ?></a></td>
		<?php for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			$str_btiebr = 'btiebr'.$xx;
			if (!isset($params[$str_btiebr])) continue;
			//if ($params[$str_btiebr] == 1) $hstring = $bestenliste[$x]->gpunkte;
			if ($params[$str_btiebr] == 1) $hstring = $bestenliste[$x]->Punkte;
			//elseif ($params[$str_btiebr] == 2) $hstring = $bestenliste[$x]->gpartien;
			elseif ($params[$str_btiebr] == 2) $hstring = $bestenliste[$x]->Partien;
			elseif ($params[$str_btiebr] == 3) $hstring = $bestenliste[$x]->Niveau;
			elseif ($params[$str_btiebr] == 4) if (($bestenliste[$x]->Punkte == $bestenliste[$x]->Partien) AND ($bestenliste[$x]->Leistung > 0)) { $hstring = (($bestenliste[$x]->Niveau)+667).' &sup2;'; $ex = 1; } else { $hstring = $bestenliste[$x]->Leistung;}
			//elseif ($params[$str_btiebr] == 5) $hstring = round($bestenliste[$x]->gprozent,1);
			elseif ($params[$str_btiebr] == 5) $hstring = round($bestenliste[$x]->Prozent,1);
			elseif ($params[$str_btiebr] == 6) { $hstring = $bestenliste[$x]->epunkte; $ey = 1; }
			elseif ($params[$str_btiebr] == 7) { $hstring = $bestenliste[$x]->epartien; $ey = 1; }
			elseif ($params[$str_btiebr] == 8) { $hstring = round($bestenliste[$x]->eprozent,1); $ey = 1; }
			elseif ($params[$str_btiebr] == 9) $hstring = $bestenliste[$x]->ebrett;
			if ($params[$str_btiebr] > 0) { ?>
		<td align="center"><?php echo $hstring ?></td>
			<?php } } ?>
	</tr>
<?php } ?>
</table>
<div class="hint"><?php echo JText::_('LEAGUE_RATING_COMMENT') ?></div>
<?php if($ex >0) { ?><div class="hint"><?php echo JText::_('LEAGUE_RATING_IMPOSSIBLE'); ?></div><?php } ?>
<?php if($ey >0) { ?><div class="hint"><?php echo JText::_('LEAGUE_WITH_UNCONTESTED'); ?></div><?php } ?>
<?php
if ($count >9 ) {
$punkte = CLMModelStatistik::checkSpieler($bestenliste[9]->Punkte);
if ($punkte == 11) {
?>
<br>
<div class="hint">** <?php echo JText::_('LEAGUE_RATING_ONE_MORE') ?> <?php echo $bestenliste[9]->Punkte; ?></div>
<?php } if ($punkte > 11) { ?>
<div class="hint">** <?php echo JText::_('LEAGUE_RATING_MORE_I') ?> <?php echo $punkte-10; ?> <?php echo JText::_('LEAGUE_RATING_MORE_II') ?> <?php echo $bestenliste[9]->Punkte; ?></div><?php }}} ?>
<br>

<?php $ex = 0; $ey = 0;
if (!$bestenliste OR !$liga OR ($gesamt[0]->gesamt == 0)) {
echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES')); 
} else { ?>
		<h4><?php echo JText::_('LEAGUE_STAT_BEST'); ?></h4>
<?php for ($x=0; $x < $liga[0]->stamm+1; $x++) { 
		if ($x < $liga[0]->stamm) $xtext = $x+1; else $xtext = JText::_('LEAGUE_STAT_ERSATZ'); ?>
		<h4><?php echo JText::_('LEAGUE_STAT_BRETT')." ".$xtext ?></h4>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('LEAGUE_STAT_BRETT') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_DWZ') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_CLUB') ?></th>
		<?php for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			$str_btiebr = 'btiebr'.$xx;
			if (!isset($params[$str_btiebr])) continue;
			if ($params[$str_btiebr] == 1) $hstring = JText::_('DWZ_POINTS');
			elseif ($params[$str_btiebr] == 2) $hstring = JText::_('DWZ_GAMES');
			elseif ($params[$str_btiebr] == 3) $hstring = JText::_('DWZ_LEVEL');
			elseif ($params[$str_btiebr] == 4) $hstring = JText::_('LEAGUE_STAT_RATING');
			elseif ($params[$str_btiebr] == 5) $hstring = JText::_('LEAGUE_STAT_PERCENT');
			elseif ($params[$str_btiebr] == 6) $hstring = JText::_('LEAGUE_STAT_POINTS_K');
			elseif ($params[$str_btiebr] == 7) $hstring = JText::_('LEAGUE_STAT_GAMES_K');
			elseif ($params[$str_btiebr] == 8) $hstring = JText::_('LEAGUE_STAT_PERCENT_K');
			elseif ($params[$str_btiebr] == 9) $hstring = JText::_('LEAGUE_STAT_BNUMBERS');
			if ($params[$str_btiebr] > 0) { ?>
		<th><?php echo $hstring ?></th>
			<?php } } ?>
	</tr>
<?php $xb = 1;
	foreach ( $bestenliste as $spielerbrett ) {
		if ($xb > $params['bnhtml']) break;
		if ($xb%2 == 0) { $zeilenr = 'zeile1'; } else { $zeilenr = 'zeile2'; } 
		if (($spielerbrett->snr == ($x+1) AND $x < $liga[0]->stamm) OR
			($spielerbrett->snr > $liga[0]->stamm AND $x >= $liga[0]->stamm)) { $xb++; ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $spielerbrett->snr; ?></td>
		<td><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $spielerbrett->zps; ?>&mglnr=<?php echo $spielerbrett->mgl_nr; ?>&PKZ=<?php echo $spielerbrett->PKZ; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $spielerbrett->Spielername; ?></a></td>
		<td><?php echo $spielerbrett->DWZ; ?></td>
		<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $spielerbrett->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $spielerbrett->Vereinname; ?></a></td>
		<?php for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			$str_btiebr = 'btiebr'.$xx;
			if (!isset($params[$str_btiebr])) continue;
			//if ($params[$str_btiebr] == 1) $hstring = $spielerbrett->gpunkte;
			if ($params[$str_btiebr] == 1) $hstring = $spielerbrett->Punkte;
			//elseif ($params[$str_btiebr] == 2) $hstring = $spielerbrett->gpartien;
			elseif ($params[$str_btiebr] == 2) $hstring = $spielerbrett->Partien;
			elseif ($params[$str_btiebr] == 3) $hstring = $spielerbrett->Niveau;
			elseif ($params[$str_btiebr] == 4) if (($spielerbrett->Punkte == $spielerbrett->Partien) AND ($spielerbrett->Leistung > 0)) { $hstring = (($spielerbrett->Niveau)+667).' &sup2;'; $ex = 1; } else { $hstring = $spielerbrett->Leistung;}
			//elseif ($params[$str_btiebr] == 5) $hstring = round($spielerbrett->gprozent,1);
			elseif ($params[$str_btiebr] == 5) $hstring = round($spielerbrett->Prozent,1);
			elseif ($params[$str_btiebr] == 6) $hstring = $spielerbrett->epunkte;
			elseif ($params[$str_btiebr] == 7) $hstring = $spielerbrett->epartien;
			elseif ($params[$str_btiebr] == 8) $hstring = round($spielerbrett->eprozent,1);
			elseif ($params[$str_btiebr] == 9) $hstring = $spielerbrett->ebrett;
			if ($params[$str_btiebr] > 0) { ?>
		<td align="center"><?php echo $hstring ?></td>
			<?php } } ?>
	</tr>
<?php        }		
	} ?>
</table>
<?php } } ?>
<div class="hint"><?php echo JText::_('LEAGUE_RATING_COMMENT') ?></div>
<?php if($ex >0) { ?><div class="hint"><?php echo JText::_('LEAGUE_RATING_IMPOSSIBLE'); ?></div><?php } ?>
<?php if($ey >0) { ?><div class="hint"><?php echo JText::_('LEAGUE_WITH_UNCONTESTED'); ?></div><?php } ?>
<br>
<h4><?php echo JText::_('LEAGUE_STAT_UNCONTESTED_LIST') ?></h4>
<?php if (!$mannschaft) { 
echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES')); 
} else { 
	$stat_kampflos = array();
	foreach($mannschaft as $mannschaft1) {
		$stat_kampflos[$mannschaft1->tln_nr] = new stdClass();
		$stat_kampflos[$mannschaft1->tln_nr]->name = $mannschaft1->name;
		$stat_kampflos[$mannschaft1->tln_nr]->tln_nr = $mannschaft1->tln_nr;
		$stat_kampflos[$mannschaft1->tln_nr]->kg_sum = 0;
		$stat_kampflos[$mannschaft1->tln_nr]->kv_sum = 0;
	}
	foreach($kgmannschaft as $kgmannschaft1) {
		$stat_kampflos[$kgmannschaft1->tln_nr]->kg_sum = $kgmannschaft1->kg_sum;
	}
	foreach($kvmannschaft as $kvmannschaft1) {
		if (isset($stat_kampflos[$kvmannschaft1->tln_nr]))
			$stat_kampflos[$kvmannschaft1->tln_nr]->kv_sum = $kvmannschaft1->kv_sum;
	}
?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_UNCONTESTED_GEWO') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_UNCONTESTED_VERL') ?></th>
	</tr>
<?php for ($x=0; $x < count($stat_kampflos); $x++) {
		if ($x == 0) $xx = 0; 
		if (!isset($stat_kampflos[$x+1])) continue;
		if ($stat_kampflos[$x+1]->name == 'spielfrei') continue;
		$xx++;
		if ($xx%2 == 0) { $zeilenr = 'zeile1'; }
			else { $zeilenr = 'zeile2'; } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $xx; ?></td>
		<td><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $stat_kampflos[$x+1]->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $stat_kampflos[$x+1]->name; ?></a></td>
		<td><?php echo $stat_kampflos[$x+1]->kg_sum; ?></td>
		<td><?php echo $stat_kampflos[$x+1]->kv_sum; ?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>
<br>
<h4><?php echo JText::_('LEAGUE_RATING_BEST_TEAM_I') ?> <?php $counter = ceil((count($mannschaft))/2); if($counter < 2 AND count($mannschaft) >1){$counter++;}; echo $counter; ?> <?php echo JText::_('LEAGUE_RATING_BEST_TEAM_II') ?> <?php echo $liga[0]->name; ?></h4>
<?php if (!$mannschaft) { 
echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES')); 
} else { ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_LEAGUE') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_TEAM_POINTS') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_TEAM_POINTS_PERCENT') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_BOARD_POINTS') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_BOARD_POINTS_PERCENT') ?></th>
	</tr>

<?php for ($x=0; $x < $counter; $x++) {
		if ($x%2 == 0) { $zeilenr = 'zeile1'; }
			else { $zeilenr = 'zeile2'; } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $x+1; ?></td>
		<td><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $mannschaft[$x]->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $mannschaft[$x]->name; ?></a></td>
		<td><a href="index.php?option=com_clm&view=rangliste&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $mannschaft[$x]->liga; ?></a></td>
		<td><?php echo $mannschaft[$x]->mp; ?></td>
		
<!--		<td><?php echo round(($mannschaft[$x]->mp *100)/(2*$mannschaft[$x]->count),1); ?></td>-->
		<td><?php echo round (100 * ($mannschaft[$x]->mp - $mannschaft[$x]->count * $ligapunkte->man_antritt) / ($mannschaft[$x]->count * $ligapunkte->man_sieg), 1); ?></td>
		
		<td><?php echo $mannschaft[$x]->bp; ?></td>
		
<!--		<td><?php echo round(($mannschaft[$x]->bp *100)/($mannschaft[$x]->stamm * $mannschaft[$x]->count),1); ?></td>-->
		<td><?php echo round (100 * ($mannschaft[$x]->bp - $mannschaft[$x]->stamm * $mannschaft[$x]->count * $ligapunkte->antritt) / ($mannschaft[$x]->stamm * $mannschaft[$x]->count * $ligapunkte->sieg), 1); ?></td>
	</tr>
<?php } ?>
</table>
<?php }
$count = 0;
	for ($x=5; $x < (2*count($mannschaft)); $x++) {
		if (isset($mannschaft[$x]->mp) AND $mannschaft[$x]->mp == $mannschaft[4]->mp) { $count++; }
		else { break; }
		}
if ($count == 1 AND $mannschaft) { ?>
<div class="hint">* <?php echo JText::_('LEAGUE_RATING_MORE_TEAM_I') ?> <?php echo $mannschaft[4]->mp; ?> <?php echo JText::_('LEAGUE_RATING_MORE_TEAM_II') ?></div><?php }
if ($count > 1 AND $mannschaft) { ?>
<div class="hint">* <?php echo JText::_('LEAGUE_RATING_MORE_I') ?> <?php echo $count; ?> <?php echo JText::_('LEAGUE_RATING_MORE_TEAMS') ?> <?php echo $mannschaft[4]->mp; ?> <?php echo JText::_('LEAGUE_RATING_MORE_TEAM_II') ?></div>
<?php } ?>

</div>
<?php } ?>

<br>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>


<div class="clr"></div>
</div>
</div>
 
