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

$liga		= $this->liga;
//$sub_liga	= $this->sub_liga;
//$sub_msch	= $this->sub_msch;
//$sub_rnd	= $this->sub_rnd;
$bestenliste= $this->bestenliste;
$item		= JRequest::getInt('Itemid','1');
$itemid 	= JRequest::getInt('Itemid');
$sid		= JRequest::getInt( 'saison','1');
$lid		= JRequest::getInt('liga','1');

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

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


// Konfigurationsparameter auslesen
$config = clm_core::$db->config();

// Browsertitelzeile setzen
$doc =JFactory::getDocument();
$doc->setTitle(JText::_('LEAGUE_STATISTIK').' '.$liga[0]->name);
	
?>

<Script language="JavaScript">

function tableOrdering( order, dir, task )
{
	var form = document.adminForm;
 
	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	document.adminForm.submit( task );
}
</SCRIPT>

<div >
<div id="statistik">
<?php echo CLMContent::componentheading(JText::_('LEAGUE_STATISTIK').'&nbsp;'.$liga[0]->name); ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<?php
if ( !$liga OR $liga[0]->published == "0") { echo '<br>'.CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD')); } 
else {

if (!$bestenliste OR !$liga) { echo '<br>'.CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));  } 

else { ?>

<div>
<br>

<h4><?php echo JText::_('LEAGUE_RATING_ALL_PLAYERS').' '.$liga[0]->name; ?></h4>
<?php if (!$bestenliste) { ?>
<div id="wrong"><?php echo JText::_('LEAGUE_NO_GAMES') ?></div>
<?php } else { 
// Sortierung
if ($itemid <>'') { $plink = '&saison=' . $sid . '&liga='. $lid .'&layout=bestenliste&Itemid='.$itemid; }
else { $plink = '&saison=' . $sid . '&liga='. $lid .'&layout=bestenliste' ;  } ?>

<form id="adminForm" action="<?php echo JRoute::_( 'index.php?option=com_clm&view=statistik' . $plink ) ;?>" method="post" name="adminForm">
<table cellpadding="0" cellspacing="0" class="details">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><a href="javascript:tableOrdering('Spielername','asc','');"><?php echo JText::_('LEAGUE_STAT_PLAYER') ?></a></th>
		<th><?php echo JHTML::_( 'grid.sort', 'LEAGUE_STAT_DWZ', 'DWZ', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<th><a href="javascript:tableOrdering('Vereinname','asc','');"><?php echo JText::_('LEAGUE_STAT_CLUB') ?></a></th>
		<th><a href="javascript:tableOrdering('snr','asc','');"><?php echo JText::_('LEAGUE_STAT_TEAMRANKING') ?></a></th>
<!---		<th><?php echo JHTML::_( 'grid.sort', 'LEAGUE_STAT_TEAMRANKING', 'snr', $this->lists['order_Dir'], $this->lists['order']); ?></th> -->
		<?php for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			//if ($params['btiebr'.$xx] == 1) { $hstring = 'LEAGUE_STAT_PLAYERPOINTS'; $vstring = 'gpunkte'; }
			if ($params['btiebr'.$xx] == 1) { $hstring = 'LEAGUE_STAT_PLAYERPOINTS'; $vstring = 'Punkte'; }
			//elseif ($params['btiebr'.$xx] == 2) { $hstring = 'LEAGUE_STAT_PLAYERGAMES'; $vstring = 'gpartien'; }
			elseif ($params['btiebr'.$xx] == 2) { $hstring = 'LEAGUE_STAT_PLAYERGAMES'; $vstring = 'Partien'; }
			elseif ($params['btiebr'.$xx] == 3) { $hstring = 'LEAGUE_STAT_PLAYERLEVEL'; $vstring = 'Niveau'; }
			elseif ($params['btiebr'.$xx] == 4) { $hstring = 'LEAGUE_STAT_RATING'; $vstring = 'Leistung'; }
			//elseif ($params['btiebr'.$xx] == 5) { $hstring = 'LEAGUE_STAT_PERCENT'; $vstring = 'gprozent'; }
			elseif ($params['btiebr'.$xx] == 5) { $hstring = 'LEAGUE_STAT_PERCENT'; $vstring = 'Prozent'; }
			elseif ($params['btiebr'.$xx] == 6) { $hstring = 'LEAGUE_STAT_POINTS_K'; $vstring = 'epunkte'; $ey = 1; }
			elseif ($params['btiebr'.$xx] == 7) { $hstring = 'LEAGUE_STAT_GAMES_K'; $vstring = 'epartien'; $ey = 1; }
			elseif ($params['btiebr'.$xx] == 8) { $hstring = 'LEAGUE_STAT_PERCENT_K'; $vstring = 'eprozent'; $ey = 1; }
			elseif ($params['btiebr'.$xx] == 9) { $hstring = 'LEAGUE_STAT_BNUMBERS'; $vstring = 'ebrett'; }
			if ($params['btiebr'.$xx] > 0) { ?>
		<th align="center"><?php echo JHTML::_( 'grid.sort', $hstring, $vstring, $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } } ?>
	</tr>
<?php 
$count = count($bestenliste);
$ex = 0; $ey = 0;
	for ($x=0; $x < $count; $x++) {
		if ($x%2 == 0) { $zeilenr = 'zeile1'; }
			else { $zeilenr = 'zeile2'; } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $x+1; ?></td>
		<td><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $bestenliste[$x]->zps; ?>&mglnr=<?php echo $bestenliste[$x]->mgl_nr; ?>&PKZ=<?php echo $bestenliste[$x]->PKZ; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $bestenliste[$x]->Spielername; ?></a></td>
		<td><?php echo $bestenliste[$x]->DWZ; ?></td>
		<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $bestenliste[$x]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $bestenliste[$x]->Vereinname; ?></a></td>
		<td><?php echo $bestenliste[$x]->snr; ?></td>
		<?php for ($xx=1; $xx < 7; $xx++) {   //max. 6 Spalten
			//if ($params['btiebr'.$xx] == 1) $hstring = $bestenliste[$x]->gpunkte;
			if ($params['btiebr'.$xx] == 1) $hstring = $bestenliste[$x]->Punkte;
			//elseif ($params['btiebr'.$xx] == 2) $hstring = $bestenliste[$x]->gpartien;
			elseif ($params['btiebr'.$xx] == 2) $hstring = $bestenliste[$x]->Partien;
			elseif ($params['btiebr'.$xx] == 3) $hstring = $bestenliste[$x]->Niveau;
			elseif ($params['btiebr'.$xx] == 4) { 
				if (($bestenliste[$x]->Punkte == $bestenliste[$x]->Partien) AND ($bestenliste[$x]->Leistung > 0)) { 
					$hstring = $bestenliste[$x]->Leistung.' &sup2;'; $ex = 1; 
				} else { 
					$hstring = $bestenliste[$x]->Leistung; 
				}
			}
//			elseif ($params['btiebr'.$xx] == 5) $hstring = round($bestenliste[$x]->gprozent,1);
			elseif ($params['btiebr'.$xx] == 5) $hstring = round($bestenliste[$x]->Prozent,1);
			elseif ($params['btiebr'.$xx] == 6) { $hstring = $bestenliste[$x]->epunkte; }
			elseif ($params['btiebr'.$xx] == 7) { $hstring = $bestenliste[$x]->epartien; }
			elseif ($params['btiebr'.$xx] == 8) { $hstring = round($bestenliste[$x]->eprozent,1); }
			elseif ($params['btiebr'.$xx] == 9) $hstring = $bestenliste[$x]->ebrett;
			if ($params['btiebr'.$xx] > 0) { ?>
		<td align="center"><?php echo $hstring ?></td>
			<?php } } ?>
	</tr>
	<?php } ?>
</table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div class="hint"><?php echo JText::_('LEAGUE_RATING_COMMENT') ?></div>
<?php if($ex >0) { ?><div class="hint"><?php echo JText::_('LEAGUE_RATING_IMPOSSIBLE'); ?></div><?php } ?>
<?php if($ey >0) { ?><div class="hint"><?php echo JText::_('LEAGUE_WITH_UNCONTESTED'); ?></div><?php } ?>
<?php } ?>

</div>
<?php } } ?>

<br>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>


<div class="clr"></div>
</div>
</div>
 