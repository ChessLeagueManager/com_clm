<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

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
$punkte		= $this->punkte;
$spielfrei	= $this->spielfrei;

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

echo '<div id="clm"><div id="rangliste">';

// schon veröffentlicht
if (!$liga or $liga[0]->published == 0) {

    echo CLMContent::clmWarning(JText::_('NOT_PUBLISHED')."<br/>".JText::_('GEDULD'));

} else {

    // Browsertitelzeile setzen
    $doc = JFactory::getDocument();
    $doc->setTitle(JText::_('TEILNEHMER').' '.$liga[0]->name);


    // Konfigurationsparameter auslesen
    $config = clm_core::$db->config();
    $pdf_melde = $config->pdf_meldelisten;
    $man_showdwz = $config->man_showdwz;
    $show_sl_mail = $config->show_sl_mail;

    // Userkennung holen
    $user	= JFactory::getUser();
    $jid	= $user->get('id');

    // DWZ Durchschnitte - Aufstellung
    $result = clm_core::$api->db_nwz_average($lid);
    $a_average_dwz_lineup = $result[2];

    // Spielfreie Teilnehmer finden //
    $diff = $spielfrei[0]->count;
    ?>

	<div class="componentheading">

	<?php echo JText::_('TEILNEHMER');
    echo "&nbsp;".$liga[0]->name; ?>

	<div id="pdf">
	<!--<img src="printButton.png" alt="drucken"  /></a>-->

	<?php
    echo CLMContent::createPDFLink('teilnehmer', JText::_('TABELLE_PDF'), array('saison' => $sid, 'layout' => 'teilnehmer', 'liga' => $lid));
    //echo CLMContent::createViewLink('rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('saison' => $sid, 'liga' => $lid) );
    if ($pdf_melde == 1) {
        // Neue Ausgabe: Saisonstart
        echo CLMContent::createPDFLink('rangliste', JText::_('PDF_RANGLISTE_TEAM_LISTING'), array('saison' => $sid, 'layout' => 'start', 'liga' => $lid));
    }
    ?>

	</div></div>
	<div class="clr"></div>

	<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

	<br>

	<table cellpadding="0" cellspacing="0" class="rangliste">
		<tr>
			<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
			<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
		</tr>		
		<?php
        // Anzahl der Teilnehmer durchlaufen
        $xx = 0;
    for ($x = 0; $x < ($liga[0]->teil); $x++) {
        if (!isset($punkte[$x])) {
            continue;
        }
        if ($punkte[$x]->published == 0) {
            continue;
        }
        $xx++;
        // Farbgebung der Zeilen //
        if ($xx % 2 == 0) {
            $zeilenr	= "zeile2";
            $zeilenr_dg2	= "zeile2_dg2";
        } else {
            $zeilenr		= "zeile1";
            $zeilenr_dg2	= "zeile1_dg2";
        }

        // Zeile Start
        echo '<tr class="'.$zeilenr.'">';

        // CSS-class des Rang-Eintrags
        $class = "rang";
        echo '<td class="'.$class.'">'.($xx).'</td>';

        echo '<td class="team">';
        if (substr($punkte[$x]->name, 0, 10) == 'Mannschaft') {
            $punkte[$x]->name = '';
        }
        if ($punkte[$x]->published == 1 and $punkte[$x]->name != '') {
            $link = new CLMcLink();
            $link->view = 'mannschaft';
            $link->more = array('saison' => $sid, 'liga' => $lid, 'tlnr' => $punkte[$x]->tln_nr, 'Itemid' => $item);
            $link->makeURL();
            $strName = $link->makeLink($punkte[$x]->name);
        } else {
            $strName = $punkte[$x]->name;
        }
        echo '<div>'.$strName.'</div>';
        if ($man_showdwz == 1) {
            echo '<div class="dwz">';
            echo "(".$a_average_dwz_lineup[$punkte[$x]->tln_nr].")";
            echo '</div>';
        }
        echo '</td>';

        echo '</tr>';
    }
    // Ende Teilnehmer

    ?>
	</table>


	<?php
    if (($liga[0]->sl <> "") or ($liga[0]->bemerkungen <> "")) {
        ?>
		<div id="desc">
			
			<?php
            if ($liga[0]->sl <> "") {
                ?>
				<div class="ran_chief">
					<div class="ran_chief_left"><?php echo JText::_('CHIEF') ?></div>
					<?php if ($jid > 0 or $show_sl_mail > 0) { ?>
						<div class="ran_chief_right"><?php echo $liga[0]->sl; ?> | <?php echo JHTML::_('email.cloak', $liga[0]->email); ?></div>	
					<?php } else { ?>
						<div class="ran_chief_right"><?php echo $liga[0]->sl; ?></div>	
					<?php } ?>
				</div>
				<div class="clr"></div>
				<?php
            }

        // Kommentare zur Liga
        if ($liga[0]->bemerkungen <> "") {
            ?>
				<div class="ran_note">
					<div class="ran_note_left"><?php echo JText::_('NOTICE_SL') ?></div>
					<div class="ran_note_right"><?php echo nl2br($liga[0]->bemerkungen); ?></div>
				</div>
				<div class="clr"></div>
			
				<?php
            if ($diff == 1 and $liga[0]->ab == 1) {
                echo JText::_('ROUND_NO_RELEGATED_TEAM');
            }
            //if ($diff == 1 AND $liga[0]->ab >1 ) { echo JText::_('ROUND_LESS_RELEGATED_TEAM'); }
            ?>
			<?php
        }
        echo '</div>';
    }
}
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
?>


<div class="clr"></div>

</div>
</div>
