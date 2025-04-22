<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip');
require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

$lid		= clm_core::$load->request_int('liga', 1);
$sid		= clm_core::$load->request_int('saison', 0);
$runde		= clm_core::$load->request_int('runde');
$item		= clm_core::$load->request_int('Itemid', 1);
$liga		= $this->liga;
//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $liga[0]->params);
$params = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
    }
}
if (!isset($params['dwz_date'])) {
    $params['dwz_date'] = '1970-01-01';
}
if (!isset($params['color_order'])) {
    $params['coloer_order'] = '';
}
if (!isset($params['time_control'])) {
    $params['time_control'] = '';
}
if (!isset($params['waiting_period'])) {
    $params['waiting_period'] = '';
}
$paramsc = new clm_class_params($liga[0]->params);
$params['pseudo_dwz'] = $paramsc->get('pseudo_dwz', 0);
if ($params['pseudo_dwz'] < 1) {
    $params['pseudo_dwz'] = '';
}

$punkte		= $this->punkte;
$spielfrei	= $this->spielfrei;
//$dwzschnitt	= $this->dwzschnitt;

$lang = clm_core::$lang->liga_info;

if ($sid == 0) {
    $db	= JFactory::getDBO();
    $query = " SELECT a.* FROM #__clm_liga as a"
            ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
            ." WHERE a.id = ".$lid
            ." AND s.published = 1"
    ;
    $db->setQuery($query);
    $zz	= $db->loadObjectList();
    if (isset($zz)) {
        $_GET['saison'] = $zz[0]->sid;
        $sid = $zz[0]->sid;
    }
}

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
$doc->setTitle($lang->title.' '.$liga[0]->name);

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$pdf_melde = $config->pdf_meldelisten;
$man_showdwz = $config->man_showdwz;

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

echo '<div id="clm"><div id="rangliste">';

require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php');

$archive_check = clm_core::$api->db_check_season_user($sid);
if (!$archive_check) {
    echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
}
// schon veröffentlicht
elseif (!$liga or $liga[0]->published == 0) {

    echo CLMContent::clmWarning(JText::_('NOT_PUBLISHED')."<br/>".JText::_('GEDULD'));

} else {

    // Array für DWZ Schnitt setzen
    /*	$dwz = array();
        for ($y=1; $y< ($liga[0]->teil)+1; $y++) {
            if ($params['dwz_date'] == '0000-00-00' OR $params['dwz_date'] == '1970-01-01') {
                if(isset($dwzschnitt[($y-1)]->dwz)) {
                $dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
            } else {
                if(isset($dwzschnitt[($y-1)]->start_dwz)) {
                $dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; }
            }
        }
    */
    // Spielfreie Teilnehmer finden //
    $diff = $spielfrei[0]->count;
    ?>

	<div class="componentheading">

	<?php echo $lang->title;
    echo "&nbsp;&nbsp;".$liga[0]->name; ?>

	</div>
	<div class="clr"></div>

	<br>
	<table>
	<tr>
		<th align="left" colspan="2" class="anfang"><?php echo JText::_('TOURNAMENT_DATA'); ?></th>
	</tr>
	
	<tr>
		<td align="left" width="25%"><?php echo $lang->season ?>:</td>
		<td><?php echo $liga[0]->sname ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->modus ?>:</td>
		<td><?php echo clm_core::$load->mode_to_name(intval($liga[0]->runden_modus), true); ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->teams ?>:</td>
		<td><?php echo($liga[0]->teil - $diff); ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->stamm ?>:</td>
		<td><?php echo $liga[0]->stamm; ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->ersatz ?>:</td>
		<td><?php echo $liga[0]->ersatz; ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->color_order ?>:</td>
		<td><?php echo clm_core::$load->key_to_name('color_order', intval($params['color_order']), true); ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->rounds ?>:</td>
		<td><?php if ($liga[0]->durchgang == 1) {
		    echo $liga[0]->runden;
		} else {
		    echo $liga[0]->durchgang.' x '.$liga[0]->runden;
		} ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->tiebreaks ?>:</td>
		<td><?php if ($liga[0]->liga_mt == 0) { 	// Liga
		    echo clm_core::$load->key_to_name('tiebreak', 5, true);
		    if ($liga[0]->b_wertung == 0 and $liga[0]->order == 1) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', 51, true);
		    }
		    if ($liga[0]->b_wertung == 3 and $liga[0]->order == 1) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', 10, true).';'.clm_core::$load->key_to_name('tiebreak', 51, true);
		    }
		    if ($liga[0]->b_wertung == 3 and $liga[0]->order == 0) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', 10, true);
		    }
		    if ($liga[0]->b_wertung == 4 and $liga[0]->order == 1) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', 51, true).';'.clm_core::$load->key_to_name('tiebreak', 10, true);
		    }
		    if ($liga[0]->b_wertung == 4 and $liga[0]->order == 0) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', 10, true);
		    }
		} else {  						// Mannschaftsturnier
		    if ($liga[0]->tiebr1 > 0) {
		        echo clm_core::$load->key_to_name('tiebreak', intval($liga[0]->tiebr1), true);
		    }
		    if ($liga[0]->tiebr2 > 0) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', intval($liga[0]->tiebr2), true);
		    }
		    if ($liga[0]->tiebr3 > 0) {
		        echo ';'.clm_core::$load->key_to_name('tiebreak', intval($liga[0]->tiebr3), true);
		    }
		}
    ?>
		</td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->pseudo_dwz ?>:</td>
		<td><?php echo $params['pseudo_dwz']; ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->time_control ?>:</td>
		<td><?php echo $params['time_control']; ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php echo $lang->waiting_period ?>:</td>
		<td><?php echo $params['waiting_period']; ?></td>
	</tr>
	<tr>
		<td align="left" width="100"><?php if ($liga[0]->liga_mt == 0) {
		    echo $lang->league_manager;
		} 	// Liga
                                   else {
                                       echo $lang->tournament_manager;
                                   } ?>:</td>
		<td><?php echo $liga[0]->sl; ?></td>
	</tr>
	</table>

	<br>

	

	<?php
}
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
?>


<div class="clr"></div>

</div>
</div>
