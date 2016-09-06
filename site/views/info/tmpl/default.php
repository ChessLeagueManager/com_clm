<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$saison		= $this->saison;
$sid 		= $saison[0]->id;
$itemid 	= JRequest::getInt('Itemid');

$sql = ' SELECT `sieg`, `remis`, `nieder`, `antritt`, `man_sieg`, `man_remis`, `man_nieder`, `man_antritt`'
		. ' FROM #__clm_liga'
		. ' WHERE `sid` = "' . $sid . '"';
$db =JFactory::getDBO ();
$db->setQuery ($sql);
$saisonpunkte = $db->loadObjectList ();
$ligapunkte = $saisonpunkte[0];

if ($saison[0]->dsb_datum  > 0) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_RUN').' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $saison[0]->dsb_datum, JText::_('DATE_FORMAT_CLM_F')));  
if (($saison[0]->dsb_datum == 0) || (!isset($saison))) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_UNCLEAR');  

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

	// Browsertitelzeile setzen
	$doc =JFactory::getDocument();
	$doc->setTitle(JText::_('SEASON_STATISTIK').' '.$saison[0]->name);

?>

<div >
<div id="info">
<div class="componentheading"><?php echo JText::_('SEASON_STATISTIK') ?> <?php echo $saison[0]->name; ?></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<?php 
//$weiss		= $this->weiss;
//$schwarz	= $this->schwarz;
$remis		= $this->remis;
$kampflos	= $this->kampflos;
$heim		= $this->heim;
$gast		= $this->gast;
$gesamt		= $this->gesamt;
$spieler	= $this->spieler;
$mannschaft	= $this->mannschaft;
$brett		= $this->brett;
$wbrett		= $this->wbrett;
$sbrett		= $this->sbrett;
$rbrett		= $this->rbrett;
$kbrett		= $this->kbrett;
?>
<div id="desc">
<?php if ($saison[0]->bemerkungen <> "") { ?>

<b><?php echo JText::_('SEASON_STAT_ADMIN') ?></b><br>
<?php echo nl2br($saison[0]->bemerkungen); ?>
<?php } ?>
</div>

<h4><?php echo JText::_('SEASON_STAT_ALL') ?></h4>
<?php if (!$spieler) {
echo "<div id='wrong'>" . JText::_('SEASON_NO_GAMES') ."</div>";  }
else { ?>
<?php $bretter = CLMModelInfo::Bretter(); ?>


<table cellpadding="0" cellspacing="0" class="info">
	<tr>
		<th><?php echo JText::_('SEASON_STAT_BRETT') ?></th>
		<th colspan="5"><?php echo JText::_('SEASON_STAT_POINTS') ?></th>
		<th colspan="2"><?php echo JText::_('SEASON_STAT_WHITE') ?></th>
		<th colspan="2"><?php echo JText::_('SEASON_STAT_BLACK') ?></th>
		<th colspan="2"><?php echo JText::_('SEASON_STAT_REMIS') ?></th>
		<th colspan="2"><?php echo JText::_('SEASON_STAT_UNCONTESTED') ?></th>
	</tr>
	
	<tr class="anfang">
		<td align="center"></td>
		<td class="punkte border"><?php echo JText::_('SEASON_STAT_SUM') ?></td>
		<td class="punkte"><?php echo JText::_('SEASON_STAT_HOME') ?></td>
		<td class="punkte"><?php echo JText::_('SEASON_STAT_PERCENT') ?></td>
		<td class="punkte"><?php echo JText::_('SEASON_STAT_GUEST') ?></td>
		<td class="punkte"><?php echo JText::_('SEASON_STAT_PERCENT') ?></td>
		<td class="white border"><?php echo JText::_('SEASON_STAT_QUANTITY') ?></td>
		<td class="white"><?php echo JText::_('SEASON_STAT_PERCENT') ?></td>
		<td class="black border"><?php echo JText::_('SEASON_STAT_QUANTITY') ?></td>
		<td class="black"><?php echo JText::_('SEASON_STAT_PERCENT') ?></td>
		<td class="remis border"><?php echo JText::_('SEASON_STAT_QUANTITY') ?></td>
		<td class="remis"><?php echo JText::_('SEASON_STAT_PERCENT') ?></td>
		<td class="kampflos border"><?php echo JText::_('SEASON_STAT_QUANTITY') ?></td>
		<td class="kampflos"><?php echo JText::_('SEASON_STAT_PERCENT') ?></td>
	</tr>

<?php 

$w = 0;
$s = 0;
$r = 0;
$k = 0;
$sum_weiss = 0;
$sum_schwarz = 0;
	for ($x=0; $x < $bretter; $x++) { 
		if ($x%2 == 0) {
				$zeilenr = 'zeile1'; }
			else {
				$zeilenr = 'zeile2';
				} ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td class="brett" align="center"><?php echo $x+1; ?></td>
		<td class="punkte border"><?php if(isset($brett[$x]->count)){ echo $brett[$x]->count; } ?></td>
		<td class="punkte"><?php if(isset($brett[$x]->sum)){ echo str_replace ('.0', '', $brett[$x]->sum); } ?></td>
<!--		<td class="punkte"><?php if(isset($brett[$x]->sum)){ echo round((($brett[$x]->sum *100)/($brett[$x]->count)),1); ?></td>-->
		<td class="punkte"><?php if (isset($brett[$x]->sum)) { echo round (100 * ($brett[$x]->sum - $brett[$x]->count * $ligapunkte->antritt) / ($brett[$x]->count * $ligapunkte->sieg), 1); } ?></td>
		
<!--		<td class="punkte"><?php echo $brett[$x]->count-$brett[$x]->sum; } ?></td>-->
		<td class="punkte"><?php if (isset ($brett[$x]->sum)) { echo str_replace ('.0', '', $brett[$x]->count * ($ligapunkte->sieg + $ligapunkte->antritt) - $brett[$x]->sum); } ?></td>
		
<!--		<td class="punkte"><?php if(isset($brett[$x]->sum)){ echo 100 -round((($brett[$x]->sum *100)/($brett[$x]->count)),1); } ?></td>-->
		<td class="punkte"><?php if (isset ($brett[$x]->sum)) { echo 100 - round (100 * ($brett[$x]->sum - $brett[$x]->count * $ligapunkte->antritt) / ($brett[$x]->count * $ligapunkte->sieg), 1); } ?></td>
		
		
<?php if (isset($wbrett[$w]->brett) AND $wbrett[$w]->brett == $x+1) {
		if (!isset($sbrett[$w]))  { $sbrett[$w] = new StdClass;
									$sbrett[$w]->sum = 0; }
		if  ($x%2 !=0) { ?>
		<td class="white border"><?php echo $sbrett[$w]->sum; ?></td>
		<td class="white"><?php echo round((($sbrett[$w]->sum*100)/$brett[$x]->count),1); ?></td>
		<?php $sum_weiss = $sum_weiss + $sbrett[$w]->sum; } else { ?>
		<td class="white border"><?php echo $wbrett[$w]->sum; ?></td>
		<td class="white"><?php echo round((($wbrett[$w]->sum*100)/$brett[$x]->count),1); ?></td>
		<?php $sum_weiss = $sum_weiss + $wbrett[$w]->sum; } ?>
<?php $w++;} else { ?>
		<td class="white border">0</td>
		<td class="white">0</td>
<?php } ?>
<?php if (isset($sbrett[$s]->brett) AND $sbrett[$s]->brett == $x+1) {
		if (!isset($wbrett[$s]))  { $wbrett[$s] = new StdClass;
									$wbrett[$s]->sum = 0; }
		if  ($x%2 !=0) { ?>
		<td class="black border"><?php echo $wbrett[$s]->sum; ?></td>
		<td class="black"><?php echo round((($wbrett[$s]->sum*100)/$brett[$x]->count),1); ?></td>
		<?php $sum_schwarz = $sum_schwarz + $wbrett[$s]->sum; } else { ?>
		<td class="black border"><?php echo $sbrett[$s]->sum; ?></td>
		<td class="black"><?php echo round((($sbrett[$s]->sum*100)/$brett[$x]->count),1); ?></td>
		<?php $sum_schwarz = $sum_schwarz + $sbrett[$s]->sum; } ?>
<?php $s++;} else { ?>
		<td class="black border">0</td>
		<td class="black">0</td>
<?php } ?>
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
		<td class="brett"align="center">&sum;</th>
		<td class="punkte border"><?php echo $gesamt[0]->gesamt; ?></td>
		<td class="punkte"><?php echo str_replace ('.0', '', $heim[0]->sum); ?></td>
<?php if ($gesamt[0]->gesamt >0 ) {  ?>
<!--		<td class="punkte"><?php echo round((($heim[0]->sum*100)/$gesamt[0]->gesamt),1); ?></td>-->
		<td class="punkte"><?php echo round (100 * ($heim[0]->sum - $gesamt[0]->gesamt * $ligapunkte->antritt) / ($gesamt[0]->gesamt * $ligapunkte->sieg), 1); ?></td>		
<?php } else { ?><td class="punkte">0</td><?php } ?>
		<td class="punkte"><?php echo str_replace ('.0', '', $gast[0]->sum); ?></td>
<?php if ($gesamt[0]->gesamt >0 ) {  ?>
<!--		<td class="punkte"><?php echo round((($gast[0]->sum*100)/$gesamt[0]->gesamt),1); ?></td>-->
		<td class="punkte"><?php echo round (100 * ($gast[0]->sum - $gesamt[0]->gesamt * $ligapunkte->antritt) / ($gesamt[0]->gesamt * $ligapunkte->sieg), 0); ?></td>
<?php } else { ?><td class="punkte">0</td><?php } ?>
		<td class="white border"><?php echo $sum_weiss; ?></td>
<?php if ($gesamt[0]->gesamt >0 ) {  ?>
		<td class="white"><?php echo round((($sum_weiss * 100)/$gesamt[0]->gesamt), 1); ?></td>
<?php } else { ?><td class="white">0</td><?php } ?>
		<td class="black border"><?php echo $sum_schwarz; ?></td>
<?php if ($gesamt[0]->gesamt >0 ) {  ?>
		<td class="black"><?php echo round((($sum_schwarz * 100)/$gesamt[0]->gesamt), 1); ?></td>
<?php } else { ?><td class="black">0</td><?php } ?>
		<td class="remis border"><?php echo $remis[0]->remis; ?></td>
<?php if ($gesamt[0]->gesamt >0 ) {  ?>
		<td class="remis"><?php echo round((($remis[0]->remis * 100)/$gesamt[0]->gesamt), 1); ?></td>
<?php } else { ?><td class="remis">0</td><?php } ?>
		<td class="kampflos border"><?php echo $kampflos[0]->kampflos; ?></td>
<?php if ($gesamt[0]->gesamt >0 ) {  ?>
		<td class="kampflos"><?php echo round((($kampflos[0]->kampflos * 100)/$gesamt[0]->gesamt), 1); ?></td>
<?php } else { ?><td class="kampflos">0</td><?php } ?>
	</tr>
</table>
<?php } ?>

<br>
<?php $count = count($spieler); $ex = 0; ?>
<h4><?php echo JText::_('SEASON_RATING_BEST_PLAYER_I') ?> <?php if ($count < 10 AND $count >0) { echo $count; } else { ?> 10<?php } ?> <?php echo JText::_('SEASON_RATING_BEST_PLAYER_II') ?></h4>
<?php if (!$spieler) { ?>
<?php echo "<div id='wrong'>" .JText::_('SEASON_NO_GAMES') ."</div>" ?>

<?php } else { ?>
<table cellpadding="0" cellspacing="0" class="info">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><a title="<?php echo $hint_dwzdsb; ?>"><?php echo JText::_('SEASON_STAT_DWZ') //klkl ?></a></th>
		<th><?php echo JText::_('SEASON_STAT_CLUB') ?></th>
		<th><?php echo JText::_('DWZ_POINTS') ?></th>
		<th><?php echo JText::_('DWZ_LEVEL') ?></th>
		<th><?php echo JText::_('SEASON_STAT_RATING') ?></th>
	</tr>

<?php
if ($count < 10) { $a = $count; }
	else { $a=10; }
 for ($x=0; $x < $a; $x++) {
		if ($x%2 == 0) { $zeilenr = "zeile1"; }
			else { $zeilenr = "zeile2"; } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td class="nr" align="center"><?php echo $x+1; ?></td>
		<td class="name" align="left"><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $spieler[$x]->zps; ?>&mglnr=<?php echo $spieler[$x]->mgl_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $spieler[$x]->Spielername; ?></a></td>
		<td class="dwz" align="center"><?php echo $spieler[$x]->DWZ; ?></td>
		<td class="verein" align="left"><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $spieler[$x]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $spieler[$x]->Vereinname; ?></a></td>
		<td class="punkte" align="center"><?php echo $spieler[$x]->Punkte; ?></td>
		<td class="niveau"><?php echo $spieler[$x]->Niveau; ?></td>
		<td class="leistung"><?php if ($spieler[$x]->Punkte == $spieler[$x]->Partien AND $spieler[$x]->Leistung > 0) { echo (($spieler[$x]->Leistung)+667).' &sup2'; $ex = 1; } else { echo $spieler[$x]->Leistung;} ?></td>

	</tr>
<?php } ?>
</table>

<div class="hint"><?php echo JText::_('LEAGUE_RATING_COMMENT') ?></div>
<?php if($ex >0) { ?><div class="hint"><?php echo JText::_('SEASON_RATING_IMPOSSIBLE') ?></div><?php }}
if(isset($spieler[9]->Punkte)){ $punkte = CLMModelInfo::checkSpieler($spieler[9]->Punkte); }
else { $punkte = 0; }
if ($punkte == 11) {
?>
<div class="hint">** <?php echo JText::_('SEASON_RATING_ONE_MORE') ?> <?php echo $spieler[9]->Punkte; ?></div>
<?php } if ($punkte > 11) { ?>
<div class="hint">** <?php echo JText::_('SEASON_RATING_MORE_I') ?> <?php echo $punkte-10; ?> <?php echo JText::_('SEASON_RATING_MORE_II') ?> <?php echo $spieler[9]->Punkte; ?></div><?php } ?>

<br>
<h4><?php echo JText::_('SEASON_RATING_BEST_TEAM_I') ?> <?php if (count($mannschaft) < 5 AND count($mannschaft) > 0) { echo count($mannschaft); $counter = count($mannschaft);} else { ?>5<?php $counter =5 ;} ?> <?php echo JText::_('SEASON_RATING_BEST_TEAM_II') ?></h4>

<?php if (!$mannschaft OR !$spieler) { ?>
<div id="wrong"><?php echo JText::_('SEASON_NO_GAMES') ?></div>

<?php } else { ?>
<table cellpadding="0" cellspacing="0" class="info">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('SEASON_STAT_LEAGUE') ?></th>
		<th><?php echo JText::_('SEASON_STAT_TEAM_POINTS') ?></th>
		<th><?php echo JText::_('SEASON_STAT_TEAM_POINTS_PERCENT') ?></th>
		<th><?php echo JText::_('SEASON_STAT_BOARD_POINTS') ?></th>
		<th><?php echo JText::_('SEASON_STAT_BOARD_POINTS_PERCENT') ?></th>
	</tr>

<?php for ($x=0; $x < $counter; $x++) {
		if ($x%2 == 0) { $zeilenr = "zeile1"; }
			else { $zeilenr = "zeile2"; } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td class="nr" align="center"><?php echo $x+1; ?></td>
		<td class="mannschaft" align="left"><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $mannschaft[$x]->lid; ?>&tlnr=<?php echo $mannschaft[$x]->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $mannschaft[$x]->name; ?></a></td>
		<td class="liga" align="left"><a href="index.php?option=com_clm&view=rangliste&saison=<?php echo $sid; ?>&liga=<?php echo $mannschaft[$x]->lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $mannschaft[$x]->liga; ?></a></td>
		<td class="mp"><?php echo $mannschaft[$x]->mp; ?></td>
<!--		<td class="mp_prozent"><?php echo round((($mannschaft[$x]->mp *100)/(2*$mannschaft[$x]->count)),1); ?></td>-->
		<td class="mp_prozent"><?php echo round (100 * ($mannschaft[$x]->mp - $mannschaft[$x]->count * $mannschaft[$x]->man_antritt) / ($mannschaft[$x]->count * $mannschaft[$x]->man_sieg), 1); ?></td>
		<td class="bp"><?php echo $mannschaft[$x]->bp; ?></td>
<!--		<td class="bp_prozent"><?php echo round((($mannschaft[$x]->bp *100)/($mannschaft[$x]->stamm * $mannschaft[$x]->count)),1); ?></td>-->
		<td class="bp_prozent"><?php echo round (100 * ($mannschaft[$x]->bp - $mannschaft[$x]->stamm * $mannschaft[$x]->count * $ligapunkte->antritt) / ($mannschaft[$x]->stamm * $mannschaft[$x]->count * $ligapunkte->sieg), 1); ?></td>
	</tr>
<?php } ?>
</table>

<?php $count = 0;
	for ($x=5; $x < 20; $x++) {
		if (isset($mannschaft[$x]->mp) AND $mannschaft[$x]->mp == $mannschaft[4]->mp) { $count++; }
		else { break; }
		}
if ($count == 1) { ?>
<div class="hint">* <?php echo JText::_('SEASON_RATING_MORE_TEAM_I') ?> <?php echo $mannschaft[4]->mp; ?>  <?php echo JText::_('SEASON_RATING_MORE_TEAM_II') ?></div>
<?php } if ($count > 1) { ?>
<div class="hint">* <?php echo JText::_('SEASON_RATING_MORE_I') ?> <?php echo $count; ?> <?php echo JText::_('SEASON_RATING_MORE_TEAMS') ?> <?php echo $mannschaft[4]->mp; ?>  <?php echo JText::_('SEASON_RATING_MORE_TEAM_II') ?></div>
<?php } } ?> 

<br>
<?php echo '<div class="hint">'.$hint_dwzdsb.'</div>'; ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>
 