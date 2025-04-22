<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

$liga		= $this->liga;
//Liga-Parameter aufbereiten
if (isset($liga[0])) {
    $paramsStringArray = explode("\n", $liga[0]->params);
} else {
    $paramsStringArray = array();
}
$params = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
    }
}
if (!isset($params['dwz_date']) or $params['dwz_date'] == '0000-00-00') {
    $params['dwz_date'] = '1970-01-01';
}
if ($params['dwz_date'] == '1970-01-01') {
    $old = true;
} // dwz aus dwz_spieler
else {
    $old = false;
} // dwz aus Meldeliste
$dwz		= $this->dwz;
$spieler	= $this->spieler;
$sid		= clm_core::$load->request_int('saison', 0);
$lid		= clm_core::$load->request_int('liga', 0);
$item		= clm_core::$load->request_int('Itemid', 0);

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
if (isset($dwz[0])) {
    $doc->setTitle(JText::_('DWZ_LIGA').' '.(isset($dwz[0]) ? $dwz[0]->name : ""));
} else {
    $doc->setTitle(JText::_('DWZ_LIGA'));
}

//CLM parameter auslesen
$config = clm_core::$db->config();
$countryversion = $config->countryversion;
?>

<div id="clm">
<div id="dwz_liga">
<?php echo CLMContent::componentheading(JText::_('DWZ_LIGA').'&nbsp;'.(isset($dwz[0]) ? $dwz[0]->name : "")); ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<?php
if (!isset($params['dwz_date']) or $params['dwz_date'] == '0000-00-00' or $params['dwz_date'] == '1970-01-01') {
    if (isset($dwz[0]) && $dwz[0]->dsb_datum  > '1970-01-01') {
        $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_RUN').' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.JHTML::_('date', $dwz[0]->dsb_datum, JText::_('DATE_FORMAT_CLM_F'));
    } else {
        $hint_dwzdsb = '';
    }
} else {
    $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_LEAGUE').' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.JHTML::_('date', $params['dwz_date'], JText::_('DATE_FORMAT_CLM_F'));
}

$archive_check = clm_core::$api->db_check_season_user($sid);
if (!$archive_check) {
    echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
}
// existiert die Liga
elseif (!isset($dwz[0])) {
    echo "<div id='wrong'>".JText::_('NOT_EXIST')." (".$lid.")<br>".JText::_('GEDULDA')."</div>";
}
// schon veröffentlicht
elseif (!$dwz or $dwz[0]->published == "0") {
    echo '<br><div class="wrong">'. JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD') .'</div><br>';
} else {

    ?>

<?php if (!$liga) {
    echo "<br>".CLMContent::clmWarning(JText::_('DWZ_NO_RESULTS'))."<br>";
} elseif ($liga[0]->anzeige_ma == 1) {
    echo "<br>".CLMContent::clmWarning(JText::_('TEAM_FORMATION_BLOCKED'))."<br>";
} else {

    $count = 0;
    $x = 0; ?>
<!-- ///////////////////// DWZ Auswertung ///////////////// -->

<table cellpadding="0" cellspacing="0" class="dwz_liga">
<?php foreach ($liga as $liga) {
    $x++;
    if ($x % 2 == 0) {
        $zeilenr = 'zeile1';
    } else {
        $zeilenr = 'zeile2';
    }

    if ($liga->tln_nr > $count) {
        if ($x != 1) {
            if (isset($spieler[$count - 1]) and $spieler[$count - 1]->count > 0) {
                ?>
<tr class="ende">
	<td colspan="2" align="left"><?php echo JText::_('TEAM_TOTAL') ?></td>
	<td><?php if ($team_dwz_partien > 0) {
	    echo round($team_dwz_galt / $team_dwz_partien);
	} ?></td>
	<td><?php echo round($team_punkte, 1); ?></td>
	<td><?php echo round($team_we, 2); ?></td>
	<td><?php echo "-"; ?></td>
	<td><?php echo "-"; ?></td>
	<td><?php if ($team_partien > 0) {
	    echo round($team_niveau / $team_partien);
	} ?></td>
	<td><?php echo $team_punkte." / ".$team_partien; ?></td>
	<td><?php if ($team_dwz_partien > 0) {
	    echo round($team_dwz_gneu / $team_dwz_partien);
	} ?></td>
	<td><?php if ($team_diff > 0) {
	    echo "+";
	} echo $team_diff; ?></td>
</tr>
<?php }
            } ?>
</table>
<br>

<table cellpadding="0" cellspacing="0" class="dwz_liga">
<tr>
<th><?php echo $liga->tln_nr;?></th>
<th colspan="11"><a href="index.php?option=com_clm&amp;view=mannschaft&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?>&amp;tlnr=<?php echo $liga->tln_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga->name;?></a></th>
</tr>
<tr>
	<td><?php echo JText::_('DWZ_NR') ?></td>
	<td><?php echo JText::_('DWZ_NAME') ?></td>
	<?php if ($old) { ?>
		<td><a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php echo JText::_('DWZ_OLD') ?></a></td>
	<?php } else { ?>
		<td><a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php echo JText::_('DWZ_START') ?></a></td>
	<?php } ?>
	<td><?php echo JText::_('DWZ_W') ?></td>
	<td><?php echo JText::_('DWZ_WE') ?></td>
	<td><?php echo JText::_('DWZ_EF') ?></td>
	<td><?php echo JText::_('DWZ_RATING') ?></td>
	<td><?php echo JText::_('DWZ_LEVEL') ?></td>
	<td><?php echo JText::_('DWZ_POINTS') ?></td>
	<td colspan="2"><?php echo JText::_('DWZ_NEW'); //klkl?></td>
</tr>

<?php
                $team_dwz_galt = 0;
        $team_we = 0;
        $team_punkte = 0;
        $team_partien = 0;
        $team_dwz_partien = 0;
        $team_niveau = 0;
        $team_dwz_gneu = 0;
        $team_diff = 0;
    } ?>
<tr class="<?php echo $zeilenr; ?>">
    <td><?php if ($liga->rang == "1") {
        echo $liga->mnr.'-';
    } echo $liga->snr;?></td>
	<td><a href="index.php?option=com_clm&amp;view=spieler&amp;saison=<?php echo $sid; ?>&amp;zps=<?php echo $liga->zps; ?>&amp;mglnr=<?php echo $liga->mgl_nr; ?>&amp;PKZ=<?php echo $liga->PKZ; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga->Spielername;?></a></td>

    <?php if ($old) {
        $dwz_alt = $liga->dsbDWZ;
        $dwz_I0_alt = $liga->DWZ_Index;
        if ($countryversion == "de"  and $liga->DWZ_Index > 0) { ?>
			<td><?php echo $liga->dsbDWZ.'-'.$liga->DWZ_Index;?></td>
		<?php } else { ?>
			<td><?php echo $liga->dsbDWZ; ?></td>
		<?php } ?>
	<?php } else {
	    $dwz_alt = $liga->start_dwz;
	    $dwz_I0_alt = $liga->start_I0;
	    if ($countryversion == "de" and $liga->start_I0 > 0) { ?>
			<td><?php echo $liga->start_dwz.'-'.$liga->start_I0;?></td>	
		<?php } else { ?>
			<td><?php echo $liga->start_dwz; ?></td>
		<?php } ?>
	<?php } ?>
    <td><?php echo $liga->Punkte;?></td>
    <td><?php echo number_format($liga->We, 2);?></td>
    <td><?php echo $liga->EFaktor;?></td>
    <td><?php if ($liga->Leistung == 0) {
        echo "-";
    } else {
        echo $liga->Leistung;
    }?></td>
    <td><?php echo $liga->Niveau;?></td>
    
    <?php  $Pkt = explode(".", $liga->Punkte);
    if ($Pkt[1] != "0") {
        if ($Pkt[0] != "0") { ?>
            <td><?php echo $Pkt[0].'&frac12;  /  '.$liga->Partien;?></td>
            <?php } else { ?>
            <td><?php echo '&frac12;  /  '.$liga->Partien;?></td>
            <?php }
            } else { ?>
    <td><?php echo $Pkt[0].'  /  '.$liga->Partien;?></td>
     <?php } ?>
    <?php if ($old) {
        if ($liga->DWZ > 0) {
            if ($countryversion == "de" and $dwz_I0_alt > 0 and $liga->I0 > 0) { ?>
				<td><?php echo $liga->DWZ.'-'.$liga->I0;?></td>
			<?php } else { ?>
				<td><?php echo $liga->DWZ; ?></td>
			<?php } ?>
		<?php }
        if ($liga->dsbDWZ > 0 and $liga->DWZ == 0) { ?>
			<td><?php echo $liga->dsbDWZ.'-'.$liga->DWZ_Index;?></td>
			<?php }
        if ($liga->dsbDWZ  == 0 and $liga->DWZ == 0) { ?>
			<td><?php echo JText::_('DWZ_REST') ?></td>
		<?php } ?>
	<?php } else {
	    if ($liga->DWZ > 0) {
	        if ($countryversion == "de" and $dwz_I0_alt > 0 and $liga->I0 > 0) { ?>
				<td><?php echo $liga->DWZ.'-'.$liga->I0;?></td>
			<?php } else { ?>
				<td><?php echo $liga->DWZ; ?></td>
			<?php } ?>
		<?php } else { ?>
			<td><?php echo JText::_('DWZ_REST') ?></td>
		<?php } ?>
	<?php } ?>
    <td>
    <?php
    $dwzdifferenz = $liga->DWZ - $dwz_alt;
    if ($dwzdifferenz > 0) {
        echo "+" . $dwzdifferenz;
    } elseif ($dwzdifferenz < 0) {
        echo $dwzdifferenz;
    } else {
        echo '';
    }?></td>
</tr>

<?php
$count = $liga->tln_nr;
    if (!is_numeric($liga->Partien) or  $liga->Partien < 0) {
        $liga->Partien = 0;
    }
    if ($dwz_alt > 0) {
        $team_dwz_galt += ($dwz_alt * $liga->Partien);
    }
    $team_we += $liga->We;
    $team_punkte += $liga->Punkte;
    $team_partien += $liga->Partien;
    if ($dwz_alt > 0) {
        $team_dwz_partien += $liga->Partien;
    }
    $team_niveau += ($liga->Niveau * $liga->Partien);
    if ($dwz_alt > 0) {
        $team_dwz_gneu += ($liga->DWZ * $liga->Partien);
    }
    if ($dwz_alt > 0) {
        $team_diff += $dwzdifferenz;
    }
}
    if (isset($spieler[$count - 1]) and $spieler[$count - 1]->count > 0) {
        ?>
<tr class="ende">
	<td colspan="2" align="left"><?php echo JText::_('TEAM_TOTAL') ?></td>
	<td><?php if ($team_dwz_partien > 0) {
	    echo round($team_dwz_galt / $team_dwz_partien);
	} ?></td>
	<td><?php echo round($team_punkte, 1); ?></td>
	<td><?php echo round($team_we, 2); ?></td>
	<td><?php echo "-"; ?></td>
	<td><?php echo "-"; ?></td>
	<td><?php if ($team_partien > 0) {
	    echo round($team_niveau / $team_partien);
	} ?></td>
	<td><?php echo $team_punkte." / ".$team_partien; ?></td>
	<td><?php if ($team_dwz_partien > 0) {
	    echo round($team_dwz_gneu / $team_dwz_partien);
	} ?></td>
	<td><?php if ($team_diff > 0) {
	    echo "+";
	} echo $team_diff; ?></td>
</tr>
<?php } ?>
</table>
<?php } ?>

<?php if ($old) {
    echo '<div class="hint">'.$hint_dwzdsb.'</div>';
} ?>
<?php } ?>
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
<div class="clr"></div>
</div>
</div>
