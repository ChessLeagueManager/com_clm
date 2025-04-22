<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/
defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip');
require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');
$lang = clm_core::$lang->schedule;

$sid	= clm_core::$load->request_int('saison', 1);
$liga	= clm_core::$load->request_int('liga', 1);
$runde	= clm_core::$load->request_int('runde', 1);
$dg	= clm_core::$load->request_int('dg', 1);
$categoryid	= clm_core::$load->request_int('categoryid', 0);
$start	= clm_core::$load->request_string('start', '1');
$itemid	= clm_core::$load->request_int('Itemid', 1);

$termine		= $this->termine;
$schnellmenu	= $this->schnellmenu;

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
$doc->setTitle(JText::_('TERMINE_HEAD'));

?>
<div id="clm">
<div id="termine">
	<div class="componentheading"><?php echo JText::_('TERMINE_HEAD') ?>

		<?php
        // PDF-Links
        echo CLMContent::createPDFLink('termine', JText::_('TERMINE_SHORT_PRINT'), array('layout' => 'termine_short', 'saison' => $sid));
echo CLMContent::createPDFLink('termine', JText::_('TERMINE_LONG_PRINT'), array('layout' => 'termine_long', 'saison' => $sid));
if (clm_core::$access->getJid() > 0) {
    echo clm_core::$load->create_link_xls('terminliste', JText::_('CSV_TERM'), array('layout' => 'terminliste', 'saison' => $sid));
}
?>	
	</div>

	<!-- Navigationsmenu -->
    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>
    <br />
    
    <?php if (count($termine) < 1) {	?>
    <div class="wrong"><?php echo JText::_('NO_TERMINE') ?></div>
    <?php  } elseif (isset($termine[0]) and $termine[0]->nr == 0) {	?>
    <div class="wrong"><?php echo JText::_('NO_ROUNDS') ?></div>
    <?php  } else {

        $arrMonth = array(
            "January" => JText::_('MOD_CLM_TERMINE_M01'),
            "February" => JText::_('MOD_CLM_TERMINE_M02'),
            "March" => JText::_('MOD_CLM_TERMINE_M03'),
            "April" => JText::_('MOD_CLM_TERMINE_M04'),
            "May" => JText::_('MOD_CLM_TERMINE_M05'),
            "June" => JText::_('MOD_CLM_TERMINE_M06'),
            "July" => JText::_('MOD_CLM_TERMINE_M07'),
            "August" => JText::_('MOD_CLM_TERMINE_M08'),
            "September" => JText::_('MOD_CLM_TERMINE_M09'),
            "October" => JText::_('MOD_CLM_TERMINE_M10'),
            "November" => JText::_('MOD_CLM_TERMINE_M11'),
            "December" => JText::_('MOD_CLM_TERMINE_M12'),
            "01" => JText::_('MOD_CLM_TERMINE_M01'),
            "02" => JText::_('MOD_CLM_TERMINE_M02'),
            "03" => JText::_('MOD_CLM_TERMINE_M03'),
            "04" => JText::_('MOD_CLM_TERMINE_M04'),
            "05" => JText::_('MOD_CLM_TERMINE_M05'),
            "06" => JText::_('MOD_CLM_TERMINE_M06'),
            "07" => JText::_('MOD_CLM_TERMINE_M07'),
            "08" => JText::_('MOD_CLM_TERMINE_M08'),
            "09" => JText::_('MOD_CLM_TERMINE_M09'),
            "10" => JText::_('MOD_CLM_TERMINE_M10'),
            "11" => JText::_('MOD_CLM_TERMINE_M11'),
            "12" => JText::_('MOD_CLM_TERMINE_M12')
        );

        $arrWochentag = array(
            "Monday" => JText::_('MOD_CLM_TERMINE_T01'),
            "Tuesday" => JText::_('MOD_CLM_TERMINE_T02'),
            "Wednesday" => JText::_('MOD_CLM_TERMINE_T03'),
            "Thursday" => JText::_('MOD_CLM_TERMINE_T04'),
            "Friday" => JText::_('MOD_CLM_TERMINE_T05'),
            "Saturday" => JText::_('MOD_CLM_TERMINE_T06'),
            "Sunday" => JText::_('MOD_CLM_TERMINE_T07') );

        ?>
    
    <table>
        <tr>
            <td align="left" colspan="3" class="clmbox"><ul>
            <?php // Schnellauswahlmenu
                for ($x = 0 ; $x < count($schnellmenu); $x++) {
                    $schnellmenu_arr[$x] = explode("-", $schnellmenu[$x]->datum);
                    $schnellmenu_monat = mktime(0, 0, 0, $schnellmenu_arr[$x][1] + 1, 0, 0);
                    if (!isset($schnellmenu_arr[$x - 1]) or $schnellmenu_arr[$x][0] > $schnellmenu_arr[$x - 1][0]) {
                        $new_year = true;
                        echo "</ul></li>\n<li><ul><li>\n".'<a href="index.php?option=com_clm&amp;view=termine&amp;categoryid='.$categoryid.'&amp;saison='. $sid .'&amp;Itemid='. $itemid .'&amp;start='.$schnellmenu_arr[$x][0].'-01-01">'. $schnellmenu_arr[$x][0] ."</a>&nbsp;:&nbsp;</li>\n";
                    } else {
                        $new_year = false;
                    }
                    if (!isset($schnellmenu_arr[$x - 1]) or  $schnellmenu_arr[$x][1] > $schnellmenu_arr[$x - 1][1]  or ($new_year)) {
                        echo '<li><a href="index.php?option=com_clm&amp;view=termine&amp;categoryid='.$categoryid.'&amp;saison='. $sid .'&amp;Itemid='. $itemid .'&amp;start='.$schnellmenu_arr[$x][0].'-'.$schnellmenu_arr[$x][1].'-01">'. $arrMonth[date('F', $schnellmenu_monat)] ."</a></li>\n";
                    }

                } ?>
            </ul></td>
        </tr>
        <?php // START : Terminschleife
        if ($start == '1') {
            $date = date("Y-m-d");
        } else {
            $date = $start;
        }

        for ($t = 0 ; $t < count($termine); $t++) {

            if ($date <= $termine[$t]->datum) {

                // Veranstaltung verlinken
                $linkname0 = '';
                if ($termine[$t]->source == 'termin') {
                    $linkname = "index.php?option=com_clm&amp;view=termine&amp;nr=". $termine[$t]->id ."&amp;layout=termine_detail&amp;categoryid=".$categoryid;
                } elseif ($termine[$t]->source == 'liga') {
                    if ($termine[$t]->nr <= $termine[$t]->ligarunde) {
                        $runde = $termine[$t]->nr;
                        $dg = 1;
                    } elseif ($termine[$t]->nr <= (2 * $termine[$t]->ligarunde)) {
                        $runde = ($termine[$t]->nr - $termine[$t]->ligarunde);
                        $dg = 2;
                    } elseif ($termine[$t]->nr <= (3 * $termine[$t]->ligarunde)) {
                        $runde = ($termine[$t]->nr - (2 * $termine[$t]->ligarunde));
                        $dg = 3;
                    } else {
                        $runde = ($termine[$t]->nr - (3 * $termine[$t]->ligarunde));
                        $dg = 4;
                    }
                    $linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=". $termine[$t]->sid ."&amp;liga=".  $termine[$t]->typ_id ."&amp;runde=". $runde ."&amp;dg=". $dg;
                } elseif ($termine[$t]->source == 'lpaar') {
                    $linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=". $termine[$t]->sid ."&amp;liga=".  $termine[$t]->typ_id ."&amp;runde=". $termine[$t]->nr ."&amp;dg=". $termine[$t]->dg;
                } else { // source == turnier
                    if (isset($termine[$t]->znr) and isset($termine[$t]->zname)) {
                        $linkname0 = "index.php?option=com_clm&amp;view=turnier_runde&amp;runde=". $termine[$t]->znr ."&amp;turnier=". $termine[$t]->typ_id;
                    }
                    $linkname = "index.php?option=com_clm&amp;view=turnier_runde&amp;runde=". $termine[$t]->nr ."&amp;turnier=". $termine[$t]->typ_id;
                }

                // Veranstaltungsbereich / Ort verlinken
                $ics_link = '';
                if ($termine[$t]->source == 'termin') {
                    $linktyp = $termine[$t]->typ;
                    $ics_link = clm_core::$load->create_link_ics('termine', $lang->ics, array('layout' => 'termine', 'id' => $termine[$t]->id));
                } elseif ($termine[$t]->source == 'liga') {
                    $linktyp = '<a href="index.php?option=com_clm&amp;view=rangliste&amp;saison='. $termine[$t]->sid .'&amp;liga='. $termine[$t]->typ_id;
                    if ($itemid <> '') {
                        $linktyp .= "&Itemid=". $itemid;
                    }
                    $linktyp .= '">'. $termine[$t]->typ .'</a>';
                } else {
                    $linktyp = '<a href="index.php?option=com_clm&amp;view=turnier_rangliste&amp;turnier='. $termine[$t]->typ_id;
                    if ($itemid <> '') {
                        $linktyp .= "&Itemid=". $itemid;
                    }
                    $linktyp .= '">'. $termine[$t]->typ .'</a>';
                }


                // Datumsberechnungen
                $datum[$t] = strtotime($termine[$t]->datum);
                $datum_arr[$t] = explode("-", $termine[$t]->datum);
                $monatsausgabe = mktime(0, 0, 0, $datum_arr[$t][1] + 1, 0, 0);

                // Monatsberechnungen
                if (!isset($datum_arr[$t - 1]) or ($datum_arr[$t][1] > $datum_arr[$t - 1][1]) or ($datum_arr[$t][0] > $datum_arr[$t - 1][0])) {
                    echo '<tr><td colspan="3" class="noborder">&nbsp;</td></tr>';

                    // Jahresberechnungen
                    if (!isset($datum_arr[$t - 1]) or $datum_arr[$t][0] > $datum_arr[$t - 1][0]) {
                        echo '<tr><th colspan="3"><a name="'. $datum_arr[$t][0] .'">'. $datum_arr[$t][0] .'</a></th></tr>';
                    }

                    echo '<tr class="anfang"><td colspan="3"><a name="'. $datum_arr[$t][0] .'-'. $datum_arr[$t][1] .'">'. $arrMonth[date('F', $monatsausgabe)] . '</a></td></tr>';
                } ?>
            <tr>
                <td width="110" align="right" class="date">
                <?php if (isset($datum[$t - 1]) and $datum[$t] == $datum[$t - 1]) {
                    echo '';
                } else {
                    echo JHTML::_('date', $termine[$t]->datum, JText::_('DATE_FORMAT_CLM_F'));
                }
                if (isset($datum[$t]) and $datum[$t] != '0000-00-00' and $datum[$t] != '1970-01-01') {
                    if ($termine[$t]->starttime != '00:00:00') {
                        echo '&nbsp;&nbsp;&nbsp;'.substr($termine[$t]->starttime, 0, 5).'&nbsp;';
                    }
                } ?>
                </td>
                <td width="120" class="title">
					<?php if ($linkname0 > '') { ?>
						<a href="<?php echo $linkname0;
					    if ($itemid <> '') {
					        echo "&Itemid=".$itemid;
					    } ?>"><?php  echo $termine[$t]->zname; ?></a>
					<?php echo ' - ';
					} ?>
				<a href="<?php echo $linkname;
                if ($itemid <> '') {
                    echo "&Itemid=".$itemid;
                } ?>"><?php  echo $termine[$t]->name; ?></a></td>
                <td width="120" class="typ"><?php echo $ics_link;
                echo $linktyp; ?></td>
            </tr>
            
        <?php  }
        }  // ENDE : Terminschleife?>
        
    </table>
    <?php  } ?>
    
    <br />
    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
    <div class="clr"></div>
</div>
</div>
