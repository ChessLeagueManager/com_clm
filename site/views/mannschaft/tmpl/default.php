<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

function RGB($Hex)
{
    if (substr($Hex, 0, 1) == "#") {
        $Hex = substr($Hex, 1);
    }
    $R = substr($Hex, 0, 2);
    $G = substr($Hex, 2, 2);
    $B = substr($Hex, 4, 2);
    $R = hexdec($R);
    $G = hexdec($G);
    $B = hexdec($B);
    $R = $R - 32;
    if ($R < 0) {
        $R = 0;
    }
    $G = $G - 32;
    if ($G < 0) {
        $G = 0;
    }
    $B = $B - 32;
    if ($B < 0) {
        $B = 0;
    }
    $R = dechex($R);
    if (strlen($R) < 2) {
        $R = '0'.$R;
    }
    $G = dechex($G);
    if (strlen($G) < 2) {
        $G = '0'.$G;
    }
    $B = dechex($B);
    if (strlen($B) < 2) {
        $B = '0'.$B;
    }
    return '#'.$R.$G.$B;
}

// Variablen ohne foreach setzen
//$aufstellung	=$this->aufstellung;
$mannschaft	= $this->mannschaft;
//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $mannschaft[0]->params);
$lparams = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $key = substr($value, 0, $ipos);
        $lparams[$key] = substr($value, $ipos + 1);
    }
}
if (!isset($lparams['dwz_date'])) {
    $lparams['dwz_date'] = '1970-01-01';
}
if (!isset($lparams['noOrgReference'])) {
    $lparams['noOrgReference'] = '0';
}
if (!isset($lparams['noBoardResults'])) {
    $lparams['noBoardResults'] = '0';
}
$vereine	= $this->vereine;
$count		= $this->count;
$bp			= $this->bp;
$sumbp		= $this->sumbp;
$plan		= $this->plan;
$termin		= $this->termin;
$einzel		= $this->einzel;
$saison 	= $this->saison;
$session_lang = clm_core::$cms->getLanguage();
if ($session_lang == 'en-GB') {
    $google_lang = 'en';
} else {
    $google_lang = 'de';
}

// Variblen aus URL holen
$sid 		= clm_core::$load->request_int('saison', 1);
$lid		= clm_core::$load->request_int('liga', 1);
$liga 		= clm_core::$load->request_int('liga', 1);
$tln 		= clm_core::$load->request_int('tlnr');
$itemid 	= clm_core::$load->request_int('Itemid', 1);
$option 	= clm_core::$load->request_string('option');
$mainframe	= JFactory::getApplication();

function vergleich($wert_a, $wert_b)
{
    $a = 1000 * ($wert_a->dg) + 50 * ($wert_a->runde) + 2 * ($wert_a->paar) + $wert_a->heim;
    $b = 1000 * ($wert_b->dg) + 50 * ($wert_b->runde) + 2 * ($wert_b->paar) + $wert_b->heim;
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : +1;
}
$bpr = $bp;
usort($bpr, 'vergleich');

$sql = ' SELECT `sieg`, `remis`, `nieder`, `antritt` FROM #__clm_liga'
        . ' WHERE `id` = "' . $liga . '"';
$db = JFactory::getDBO();
$db->setQuery($sql);
$ligapunkte = $db->loadObject();

if ($lparams['dwz_date'] == '0000-00-00' or $lparams['dwz_date'] == '1970-01-01') {
    if ($saison[0]->dsb_datum  > '1970-01-01') {
        $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_RUN').' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.JHTML::_('date', $saison[0]->dsb_datum, JText::_('DATE_FORMAT_CLM_F'));
    } else {
        $hint_dwzdsb = '';
    }
} else {
    $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_LEAGUE').' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.JHTML::_('date', $lparams['dwz_date'], JText::_('DATE_FORMAT_CLM_F'));
}
if (!$mannschaft or $mannschaft[0]->lpublished == 0) {
    $msg = JText::_('NOT_PUBLISHED').JText::_('GEDULD');
    $mainframe->enqueueMessage($msg);
    $link = 'index.php?option='.$option.'&view=info&Itemid='.$itemid;
    $mainframe->redirect($link);
}
if ($mannschaft[0]->published == 0) {
    $msg = JText::_('TEAM_NOT_PUBLISHED').JText::_('GEDULD');
    $mainframe->enqueueMessage($msg);
    $link = 'index.php?option='.$option.'&view=info&Itemid='.$itemid;
    $mainframe->redirect($link);
}

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
$doc->setTitle($mannschaft[0]->name.' - '.$mannschaft[0]->liga_name);

// Konfigurationsparameter auslesen
$config 			= clm_core::$db->config();
$email_from 		= $config->email_from;
$countryversion   	= $config->countryversion;
$telefon			= $config->man_tel;
$mobil				= $config->man_mobil;
$mail				= $config->man_mail;
$man_manleader		= $config->man_manleader;
$man_spiellokal		= $config->man_spiellokal;
$man_spielplan		= $config->man_spielplan;
$fixth_msch 		= $config->fixth_msch;
$googlemaps_msch   	= $config->googlemaps_msch;
$googlemaps   		= $config->googlemaps;
$googlemaps_rtype   = $config->googlemaps_rtype;
$googlemaps_mrout   = $config->googlemaps_mrout;
$maps_zoom			= $config->maps_zoom;

// Aufbereitung Googledaten 1. Spiellokal
if (is_null($mannschaft[0]->lokal)) {
    $mannschaft[0]->lokal = '';
}
$mannschaft[0]->lokal = str_replace(chr(10), "", $mannschaft[0]->lokal);
$mannschaft[0]->lokal = str_replace(chr(13), "", $mannschaft[0]->lokal);
$spiellokal1G = explode(",", $mannschaft[0]->lokal);
if (isset($spiellokal1G[2]) and $googlemaps_rtype == 1) {
    $google_address = $spiellokal1G[0].','.$spiellokal1G[1].','.$spiellokal1G[2];
} elseif (isset($spiellokal1G[2]) and $googlemaps_rtype == 2) {
    $google_address = $spiellokal1G[1].','.$spiellokal1G[2];
} elseif (isset($spiellokal1G[1]) and $googlemaps_rtype == 3) {
    $google_address = $spiellokal1G[0].','.$spiellokal1G[1];
} else {
    $google_address = $mannschaft[0]->lokal;
}

// Load functions for map display
require_once(JPATH_COMPONENT.DS.'includes'.DS.'geo_functions.php');

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

// Konfigurationsparameter auslesen Teil2
$clm_zeile1			= $config->zeile1;
$clm_zeile2			= $config->zeile2;
$clm_zeile1D			= RGB($clm_zeile1);
$clm_zeile2D			= RGB($clm_zeile2);
$attr = clm_core::$api->db_lineup_attr($lid);
?>

<div id="clm">
<div id="mannschaft">
<?php
require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php');

$archive_check = clm_core::$api->db_check_season_user($sid);
if (!$archive_check) {
    echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
}
// Liga schon veröffentlicht
elseif ($mannschaft[0]->lpublished != 0 and $mannschaft[0]->published != 0) { ?>

    <div class="componentheading"><?php echo $mannschaft[0]->name; ?> - <?php echo $mannschaft[0]->liga_name; ?></div>

    <?php if ($mannschaft[0]->zps != "0") { ?>
		<div class="clmbox">
		<a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $mannschaft[0]->zps; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo JText::_('TEAM_DETAILS') ?></a>
		| <a href="index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $mannschaft[0]->zps; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo JText::_('TEAM_OVERVIEW') ?></a>
		<?php $isg = 0;
        $isgn = 0;
        while (isset($vereine[$isg]) and $isg <= 10) :
            if ($vereine[$isg]->zps != "0" and  $vereine[$isg]->zps != "" and $vereine[$isg]->vzps != "0" and  $vereine[$isg]->vzps != "" and $vereine[$isg]->zps != $vereine[$isg]->vzps) {
                while (strlen($vereine[$isg]->name) < 40) :
                    $vereine[$isg]->name .= '&nbsp';
                endwhile;  ?>
				<br><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $vereine[$isg]->vzps; ?><?php if ($itemid <> '') {
				    echo "&Itemid=".$itemid;
				} ?>"><?php echo $vereine[$isg]->name.' &nbsp '.JText::_('TEAM_DETAILS'); ?></a>
				<?php echo($isgn + 1); ?>
				<a href="index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $vereine[$isg]->vzps; ?><?php if ($itemid <> '') {
				    echo "&Itemid=".$itemid;
				} ?>"><?php echo JText::_('TEAM_OVERVIEW') ?></a>
		<?php $isgn++;
            } $isg++;
        endwhile; ?>
    <?php } else { ?>
		<div class="clmbox"><?php echo "|  " ?>
    <?php }  ?>
	<div id="pdf">
	
	<?php
    if ($jid != "0") {
        echo CLMContent::createPDFLink('mannschaft', JText::_('PDF_TEAM1'), array('layout' => 'team', 'o_nr' => 1, 'saison' => $mannschaft[0]->sid, 'liga' => $mannschaft[0]->liga, 'tlnr' => $mannschaft[0]->tln_nr));
    }
    echo CLMContent::createPDFLink('mannschaft', JText::_('PDF_TEAM'), array('layout' => 'team', 'o_nr' => 0, 'saison' => $mannschaft[0]->sid, 'liga' => $mannschaft[0]->liga, 'tlnr' => $mannschaft[0]->tln_nr));
    ?>
	
	</div></div>
    <div class="clr"></div>
    
    <?php if ($lparams['noOrgReference'] == '0') { ?>
    <div class="teamdetails">
        <div id="leftalign">
    <?php if ($man_manleader == "1") { ?>
        <b><?php echo JText::_('TEAM_LEADER') ?></b><br>
        <?php if ($mannschaft[0]->mf_name <> '') {
            echo $mannschaft[0]->mf_name; ?><br>
        <?php if ($mail == "1" or ($mail == "0" and $jid != "0")) {
            echo JHTML::_('email.cloak', $mannschaft[0]->email);
        } else {
            echo JText::_('TEAM_MAIL');
            echo JText::_('TEAM_REGISTERED');
        } ?><br> 
        
        <?php if ($mannschaft[0]->tel_fest != '') {
            if ($telefon == "1" or ($telefon == "0" and $jid != "0")) {
                echo JText::_('TEAM_FON');
                echo " ".$mannschaft[0]->tel_fest;
            }
            if ($telefon == "0" and $jid == "0") {
                echo JText::_('TEAM_FON');
                echo JText::_('TEAM_REGISTERED');
            }
            ?><br>
        <?php } else {
            echo JText::_('TEAM_NO_FONE');
        } ?>
        
        <?php if ($mannschaft[0]->tel_mobil <> '') {
            if ($mobil == "1" or ($mobil == "0" and $jid != "0")) {
                echo JText::_('TEAM_MOBILE');
                echo " ".$mannschaft[0]->tel_mobil;
            }
            if ($mobil == "0" and $jid == "0") {
                echo JText::_('TEAM_MOBILE');
                echo JText::_('TEAM_REGISTERED');
            }
        } else {
            echo JText::_('TEAM_NO_MOBILE') ;
        }
        } else { ?><?php echo JText::_('TEAM_NOT_SET') ?><?php }
        } ?>
        </div>
        <div id="rightalign">
    <?php if ($man_spiellokal == "1") { ?>
        <b><?php echo JText::_('TEAM_LOCATION'); ?></b>
        <?php if (($mannschaft[0]->lokal == ! false) and ($googlemaps_msch > "0") and ($googlemaps == "1")) { ?>&nbsp;(&nbsp;
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $mannschaft[0]->sid ?>&liga=<?php echo $mannschaft[0]->liga ?>&tlnr=<?php echo $mannschaft[0]->tln_nr ?>#google"><?php echo JText::_('TEAM_KARTE') ?></a>&nbsp;)<?php } ?>
        <br />
        <div style="float:left; width: 50%;">
            <?php $spiellokal1 = explode(",", $mannschaft[0]->lokal);
        if (is_null($mannschaft[0]->adresse)) {
            $mannschaft[0]->adresse = '';
        }
        $spiellokal2 = explode(",", $mannschaft[0]->adresse); ?>
            <?php
            // 1. Spiellokal
            if ($spiellokal1[0] == ! false) {
                echo $spiellokal1[0];
            }
        if (isset($spiellokal1[1])) {
            echo "<br>".$spiellokal1[1];
        }
        if (isset($spiellokal1[2])) {
            echo "<br>".$spiellokal1[2];
        }
        if (isset($spiellokal1[3])) {
            echo "<br>".$spiellokal1[3];
        }

        // Routenplaner
        if ($spiellokal1[0] == ! false and $googlemaps_mrout == 1) {
            if ($googlemaps_rtype == 1 and isset($spiellokal1[2])) {
                echo '<br><a href="http://maps.google.com/maps?hl='.$google_lang.'&saddr=&daddr=' . $spiellokal1[0].','. $spiellokal1[1].','.$spiellokal1[2].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>';
            } elseif ($googlemaps_rtype == 2 and isset($spiellokal1[2])) {
                echo '<br><a href="http://maps.google.com/maps?hl='.$google_lang.'&saddr=&daddr=' . $spiellokal1[1].','.$spiellokal1[2].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>';
            } elseif ($googlemaps_rtype == 3 and isset($spiellokal1[1])) {
                echo '<br><a href="http://maps.google.com/maps?hl='.$google_lang.'&saddr=&daddr=' . $spiellokal1[0].','.$spiellokal1[1].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>';
            } elseif (isset($spiellokal1[2])) {
                echo '<br><a href="http://maps.google.com/maps?hl='.$google_lang.'&saddr=&daddr=' . $spiellokal1[0].','.$spiellokal1[1].','.$spiellokal1[2].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>';
            } elseif (isset($spiellokal1[1])) {
                echo '<br><a href="http://maps.google.com/maps?hl='.$google_lang.'&saddr=&daddr=' . $spiellokal1[0].','.$spiellokal1[1].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>';
            } else {
                echo '<br><a href="http://maps.google.com/maps?hl='.$google_lang.'&saddr=&daddr=' . $spiellokal1[0].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>';
            }
        } ?>
            </div>
            <div style="float:right; width: 50%;">
            <?php
        // 2. Spiellokal
        if ($spiellokal2[0] == ! false) {
            echo $spiellokal2[0];
        }
        if (isset($spiellokal2[1])) {
            echo "<br>".$spiellokal2[1];
        }
        if (isset($spiellokal2[2])) {
            echo "<br>".$spiellokal2[2];
        }
        if (isset($spiellokal2[3])) {
            echo "<br>".$spiellokal2[3];
        }
        ?>
        </div>
    <?php } ?>
    <div class="clr"></div>
        
        <?php
        // Termine
        if ($mannschaft[0]->termine == ! false) {
            echo "<br />". str_replace(",", "<br />", $mannschaft[0]->termine);
        }

        // Homepage
        if ($mannschaft[0]->homepage == ! false) { ?><br><a href="<?php echo $mannschaft[0]->homepage; ?>" target="blank"><?php echo $mannschaft[0]->homepage; ?></a><?php }
        ?>
        </div>
    <div class="clr"></div>
    
    <?php if ($mannschaft[0]->bemerkungen <> '') { ?>
    <br /><b><?php echo JText::_('TEAM_NOTICE') ?></b><br />
    <?php echo $mannschaft[0]->bemerkungen; ?>
    <?php	} ?>
    </div>
    <?php } ?>
    
  <?php if ($lparams['noBoardResults'] == '0') { ?>    
    <?php
    if ($mannschaft[0]->anzeige_ma == 1) { ?>
    <div id="wrong"><?php echo JText::_('TEAM_FORMATION_BLOCKED') ?></div><br>
    <?php } elseif (!$count) { ?>
    <div id="wrong"><?php echo JText::_('TEAM_NO_FORMATION') ?></div><br>
    <?php } else {

        ?>
    <h4><?php echo JText::_('TEAM_FORMATION') ?></h4>
    <table cellpadding="0" cellspacing="0" id="mannschaft" <?php if ($fixth_msch == "1") { ?>class="tableWithFloatingHeader"<?php } ?>>
    
    <tr>
    <?php if ($mannschaft[0]->lrang > 0) { ?><th class="nr"><?php echo JText::_('TEAM_RANK') ?></th><?php } else { ?><th class="nr"><?php echo JText::_('DWZ_NR') ?></th><?php } ?>
    <?php if ($attr) { ?>
		<th class="nr"><?php echo JText::_('CLUB_LIST_ATTR') ?></th>
    <?php } ?>
		<th class="nr"><?php echo JText::_('CLUB_LIST_TITEL') ?></th>
        <th class="name"><?php echo JText::_('DWZ_NAME') ?></th>
        <th class="dwz">
		<?php if ($hint_dwzdsb != '') { ?>
			<a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php if ($countryversion == "de") {
			    echo JText::_('LEAGUE_STAT_DWZ');
			} else {
			    echo JText::_('LEAGUE_STAT_DWZ_EN');
			}?></a>
		<?php } else { ?>
			<?php if ($countryversion == "de") {
			    echo JText::_('LEAGUE_STAT_DWZ');
			} else {
			    echo JText::_('LEAGUE_STAT_DWZ_EN');
			}?>
		<?php } ?>
		</th>
	 <?php
    // erster Durchgang
    for ($b = 0; $b < $mannschaft[0]->runden; $b++) { ?>
        <th class="rnd"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $b + 1; ?>&dg=1<?php if ($itemid <> '') {
            echo "&Itemid=".$itemid;
        } ?>"><?php echo $b + 1; ?></th></a><?php }

    // zweiter Durchgang
        if ($mannschaft[0]->dg > 1) {
            for ($b = 0; $b < $mannschaft[0]->runden; $b++) { ?>
        <th class="rnd"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $b + 1; ?>&dg=2<?php if ($itemid <> '') {
            echo "&Itemid=".$itemid;
        } ?>"><?php echo $b + 1; ?></th></a><?php
            }
        }
        // dritter Durchgang
        if ($mannschaft[0]->dg > 2) {
            for ($b = 0; $b < $mannschaft[0]->runden; $b++) { ?>
        <th class="rnd"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $b + 1; ?>&dg=3<?php if ($itemid <> '') {
            echo "&Itemid=".$itemid;
        } ?>"><?php echo $b + 1; ?></th></a><?php
            }
        }
        // vierter Durchgang
        if ($mannschaft[0]->dg > 3) {
            for ($b = 0; $b < $mannschaft[0]->runden; $b++) { ?>
        <th class="rnd"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $b + 1; ?>&dg=3<?php if ($itemid <> '') {
            echo "&Itemid=".$itemid;
        } ?>"><?php echo $b + 1; ?></th></a><?php
            }
        }
        ?>
        <th class="punkte"><?php echo JText::_('TEAM_POINTS') ?></th>
        <th class="spiele"><?php echo JText::_('TEAM_GAMES') ?></th>
        <th class="prozent"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></th>
    </tr>
    <?php
        $y = 1;
        // Teilnehmerschleife
        $ie = 0;
        $sumspl = 0;
        $sumgespielt = 0;
        for ($x = 0; $x < 400; $x++) {
            // Überlesen von Null-Sätzen
            while (isset($count[$x]) and $count[$x]->mgl_nr == "0" and $countryversion == "de") {
                $x++;
            }
            if (!isset($count[$x])) {
                break;
            }
            if ($count[$x]->PKZ === null) {
                $count[$x]->PKZ = "";
            }
            if ($x % 2 != 0) {
                $zeilenr = 'zeile1';
                $zeiled = $clm_zeile1D;
            } else {
                $zeilenr = 'zeile2';
                $zeiled = $clm_zeile2D;
            }
            ?>
        
    <tr class="<?php echo $zeilenr; ?>">
    <?php if ($mannschaft[0]->lrang > 0) { ?><td class="nr" ><?php echo $count[$x]->rmnr.' - '.$count[$x]->rrang; ?></td><?php } else { ?><td class="nr" ><?php echo $y; ?></td><?php } ?>
    <?php if ($attr) { ?>
		<td class="dwz"><?php echo $count[$x]->attr; ?></td>
    <?php } ?>
		<td class="dwz"><?php echo $count[$x]->FIDE_Titel; ?></td>
	<?php if ($count[$x]->zps != "-2") { ?>
		<td class="name"><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $count[$x]->zps; ?>&mglnr=<?php echo $count[$x]->mgl_nr; ?>&PKZ=<?php echo $count[$x]->PKZ; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>">
		<?php if ($count[$x]->gesperrt == 1) {
		    echo "<del>";
		}
	    echo $count[$x]->name;
	    if ($count[$x]->Status != 'A') {
	        echo " (".$count[$x]->Status.")";
	    }
	    if ($count[$x]->gesperrt == 1) {
	        echo "</del>";
	    } ?></a></td>
	<?php } else { ?>
		<td class="name"><?php echo $count[$x]->name; ?></td>
	<?php } ?>
    <td class="dwz"><?php if ($lparams['dwz_date'] == '0000-00-00' or $lparams['dwz_date'] == '1970-01-01') {
        if ($count[$x]->dwz > 0) {
            echo $count[$x]->dwz;
        }
    } else {
        if ($count[$x]->start_dwz > 0) {
            echo $count[$x]->start_dwz;
        }
    } ?></td>
    <?php
    //keine Ergebnisse zum Spieler
    if (isset($einzel[$ie]) and ($einzel[$ie]->PKZ === null)) {
        $einzel[$ie]->PKZ = "";
    }
            if (!isset($einzel[$ie]) or ($count[$x]->zps !== $einzel[$ie]->zps)
                or (($countryversion == "de" and $count[$x]->mgl_nr !== $einzel[$ie]->spieler)
                or ($countryversion == "en" and $count[$x]->PKZ !== $einzel[$ie]->PKZ))) {
                for ($z = 0; $z < $mannschaft[0]->dg * $mannschaft[0]->runden; $z++) { ?>
        <td class="rnd">&nbsp;</td>
    <?php	} ?>
    <td class="punkte">&nbsp;</td>
    <td class="spiele">&nbsp;</td>
    <td class="prozent">&nbsp;</td>
    </tr>
    <?php
    $y++;
                continue;
            }
            //Spieler mit Ergebnissen
            $pkt = 0;
            $spl = 0;
            $gespielt = 0;
            for ($c = 0; $c < $mannschaft[0]->dg; $c++) {
                for ($b = 0; $b < $mannschaft[0]->runden; $b++) {
                    //if (isset($einzel[$ie])&&($einzel[$ie]->dg==$c+1)&&($einzel[$ie]->runde==$b+1)&&($count[$x]->zps==$einzel[$ie]->zps)&&($count[$x]->mgl_nr==$einzel[$ie]->spieler)) {
                    if (isset($einzel[$ie]) && ($einzel[$ie]->dg == $c + 1) && ($einzel[$ie]->runde == $b + 1) && ($count[$x]->zps == $einzel[$ie]->zps)
                        && (($countryversion == "de" and $count[$x]->mgl_nr == $einzel[$ie]->spieler) or ($countryversion == "en" and $count[$x]->PKZ == $einzel[$ie]->PKZ))) {

                        $search = array('.0', '0.5');
                        $replace = array('', '&frac12;');
                        $punkte_text = str_replace($search, $replace, $einzel[$ie]->punkte);

                        if ($einzel[$ie]->kampflos == 0) {
                            $dr_einzel = $punkte_text;
                        } else {
                            if ($config->fe_display_lose_by_default == 0) {
                                if ($einzel[$ie]->punkte == 0) {
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

                        ?>
				<td class="rnd" style="white-space: nowrap;<?php if ($einzel[$ie]->weiss == 0) {
				    echo ' background-color:'.$zeiled.';';
				}?>"><?php echo $dr_einzel; ?></td>
			<?php
            if ($einzel[$ie]->kampflos == 0) {
                $gespielt++;
                $sumgespielt++;
            }
                        $spl++;
                        $sumspl++;
                        $pkt += $einzel[$ie]->punkte;
                        $ie++;
                    } else { ?>
        <td class="rnd">&nbsp;</td>
    <?php	     }
                }
            }

            //    $prozent = round(100*($punkte/$spl));
            if (($gespielt * $ligapunkte->sieg) != 0) {
                //		$prozent = round (100 * ($pkt - $gespielt * $ligapunkte->antritt) / ($gespielt * $ligapunkte->sieg), 1);
                $prozent = round(100 * ($pkt - $spl * $ligapunkte->antritt) / ($spl * $ligapunkte->sieg), 1);
            } else {
                $prozent = '';
                $gespielt = '';
                $pkt = '';
            }
            ?>
    <td class="punkte"><?php echo $pkt; ?></td>
    <td class="spiele"><?php echo $spl; //echo $gespielt;?></td>
    <td class="prozent"><?php echo $prozent ?></td>
    
    </tr>
    <?php
            $y++;
        }
        while (isset($einzel[$ie])) {
            $ztext = "    ".JText::_('TEAM_WARNING');
            $ztext .= JText::_('TEAM_CLUB').$einzel[$ie]->zps.JText::_('TEAM_MEMBER').$einzel[$ie]->spieler.JText::_('TEAM_PKZ').$einzel[$ie]->PKZ;
            $ztext .= JText::_('TEAM_DG').$einzel[$ie]->dg.JText::_('TEAM_ROUND').$einzel[$ie]->runde;
            $ztext .= JText::_('TEAM_BOARD').$einzel[$ie]->brett.JText::_('TEAM_RESULT2').$einzel[$ie]->punkte;
            $zcolspan = 6 + ($mannschaft[0]->dg * $mannschaft[0]->runden);
            ?>
		<tr class="<?php echo $zeilenr; ?>">
			<td class="name" colspan ="<?php echo $zcolspan; ?>"><?php echo $ztext; ?></td>
		</tr> 
		<?php $ie++;
        }

        ?>
    <tr class="ende">
    <?php if ($attr) { ?>
		<td colspan="5"><?php echo JText::_('TEAM_TOTAL'); ?></td>
    <?php } else { ?>
		<td colspan="4"><?php echo JText::_('TEAM_TOTAL'); ?></td>
    <?php } ?>
    <?php	$spl = 0;
        $gespielt = 0;
        $ibe = 0;
        // erster Durchgang
        for ($z = 0; $z < $mannschaft[0]->runden; $z++) {
            while ((isset($bp[$spl]->tln_nr)) and ($bp[$spl]->tln_nr != $mannschaft[0]->tln_nr)) {
                $spl++;
            }
            if (isset($bp[$spl]->runde) and $bp[$spl]->runde == $z + 1) { ?>
    <td class="rnd"><?php if (!is_null($bp[$spl]->brettpunkte)) {
        echo str_replace('.0', '', $bp[$spl]->brettpunkte);
    } ?></td>
    <?php if (!is_null($bp[$spl]->brettpunkte)) {
        $ibe++;
    }
                $spl++;
            } else { ?>
    <td class="rnd">&nbsp;</td>
    <?php 		}
        }
        // zweiter Durchgang
        if ($mannschaft[0]->dg > 1) {
            for ($z = 0; $z < $mannschaft[0]->runden; $z++) {
                while ((isset($bp[$spl]->tln_nr)) and ($bp[$spl]->tln_nr != $mannschaft[0]->tln_nr)) {
                    $spl++;
                }
                if (isset($bp[$spl]->runde) and $bp[$spl]->runde == $z + 1) { ?>
    <td class="rnd"><?php echo str_replace('.0', '', $bp[$spl]->brettpunkte); ?></td>
    <?php if (!is_null($bp[$spl]->brettpunkte)) {
        $ibe++;
    }
                    $spl++;
                } else { ?>
    <td class="rnd">&nbsp;</td>
    <?php 		}
            }
        }
        // dritter Durchgang
        if ($mannschaft[0]->dg > 2) {
            for ($z = 0; $z < $mannschaft[0]->runden; $z++) {
                while ((isset($bp[$spl]->tln_nr)) and ($bp[$spl]->tln_nr != $mannschaft[0]->tln_nr)) {
                    $spl++;
                }
                if (isset($bp[$spl]->runde) and $bp[$spl]->runde == $z + 1) { ?>
    <td class="rnd"><?php echo str_replace('.0', '', $bp[$spl]->brettpunkte); ?></td>
    <?php if (!is_null($bp[$spl]->brettpunkte)) {
        $ibe++;
    }
                    $spl++;
                } else { ?>
    <td class="rnd">&nbsp;</td>
    <?php 		}
            }
        }
        // vierter Durchgang
        if ($mannschaft[0]->dg > 3) {
            for ($z = 0; $z < $mannschaft[0]->runden; $z++) {
                while ((isset($bp[$spl]->tln_nr)) and ($bp[$spl]->tln_nr != $mannschaft[0]->tln_nr)) {
                    $spl++;
                }
                if (isset($bp[$spl]->runde) and $bp[$spl]->runde == $z + 1) { ?>
    <td class="rnd"><?php echo str_replace('.0', '', $bp[$spl]->brettpunkte); ?></td>
    <?php if (!is_null($bp[$spl]->brettpunkte)) {
        $ibe++;
    }
                    $spl++;
                } else { ?>
    <td class="rnd">&nbsp;</td>
    <?php 		}
            }
        }
        if (is_null($sumbp[0]->summe)) {
            $sumbp[0]->summe = 0;
        }
        ?>
    <td class="punkte"><?php echo str_replace('.0', '', $sumbp[0]->summe); ?></td>
    <td class="spiele"><?php $sumspl = $mannschaft[0]->stamm * $ibe;
        echo $sumspl; //echo $sumgespielt;?></td>
    <?php if ($sumgespielt < 1) { ?>
    <td class="spiele">&nbsp;</td>
    <?php } else { ?>
    <td class="prozent"><?php if (($sumspl * $ligapunkte->sieg) != 0) {
        echo round(100 * ($sumbp[0]->summe - $sumspl * $ligapunkte->antritt) / ($sumspl * $ligapunkte->sieg), 1);
    } //echo round(100*($sumbp[0]->summe - $sumgespielt * $ligapunkte->antritt) / ($sumgespielt * $ligapunkte->sieg), 1);?></td>
    <?php } ?>
    </tr>
    
    </table><br>
    <?php } ?>
  <?php } ?>
    
    <?php if ($man_spielplan == "1") { ?>
    <h4><?php echo JText::_('TEAM_PLAN') ?></h4>
    
    <table cellpadding="0" cellspacing="0" class="spielplan">
    <tr>
        <th><?php echo JText::_('TEAM_ROUNDS') ?></th>
        <th><?php echo JText::_('TEAM_PAIR') ?></th>
        <th><?php echo JText::_('TEAM_DATE') ?></th>
        <th><?php echo JText::_('TEAM_HOME') ?></th>
        <th><?php echo JText::_('TEAM_GUEST') ?></th>
        <th><?php echo JText::_('TEAM_RESULT') ?></th>
   </tr>
    <?php
    $cnt = 0;
        $ibpr = 0;
        foreach ($plan as $plan) {
            //$datum =JFactory::getDate($plan->datum);?>
    <tr>
    <td><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $plan->runde; ?>&dg=<?php echo $plan->dg; ?><?php if ($itemid <> '') {
        echo "&Itemid=".$itemid;
    } ?>"><?php if ($mannschaft[0]->dg > 1) {
        echo $plan->dg." / ";
    } echo $plan->runde; ?></a></td>
    <td><?php echo $plan->paar; ?></td>
    <td><?php while (isset($termin[$cnt]->nr) and ($plan->runde + $mannschaft[0]->runden * ($plan->dg - 1)) > $termin[$cnt]->nr) {
        $cnt++;
    }
            if (isset($termin[$cnt]->nr) and ($plan->runde + $mannschaft[0]->runden * ($plan->dg - 1)) == $termin[$cnt]->nr) {
                if ($termin[$cnt]->pdate > '1970-01-01') {
                    echo JHTML::_('date', $termin[$cnt]->pdate, JText::_('DATE_FORMAT_CLM'));
                    if ($termin[$cnt]->ptime != '00:00:00') {
                        echo '  '.substr($termin[$cnt]->ptime, 0, 5);
                    }
                } elseif ($termin[$cnt]->datum > '1970-01-01') {
                    echo JHTML::_('date', $termin[$cnt]->datum, JText::_('DATE_FORMAT_CLM'));
                    if ($termin[$cnt]->startzeit != '00:00:00') {
                        echo '  '.substr($termin[$cnt]->startzeit, 0, 5);
                    }
                }
                $cnt++;
            } ?></td>
    <?php if ($plan->tln_nr == $tln) { ?>
        <td><?php echo $plan->hname; ?></td>
        <td>
        <?php if ($plan->gpublished == 1) { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $liga ?>&tlnr=<?php echo $plan->gegner; ?><?php if ($itemid <> '') {
            echo "&Itemid=".$itemid;
        } ?>"><?php echo $plan->gname; ?></a>
        <?php } else {
            echo $plan->gname;
        } ?>
        </td>
    <?php 	} else { ?>
        <td>
        <?php if ($plan->hpublished == 1) { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $liga ?>&tlnr=<?php echo $plan->tln_nr; ?><?php if ($itemid <> '') {
            echo "&Itemid=".$itemid;
        } ?>"><?php echo $plan->hname; ?></a>
        <?php } else {
            echo $plan->hname;
        } ?>
        </td>
        <td><?php echo $plan->gname; ?></td>
    <?php } ?>
		<?php	while (isset($bpr[$ibpr]) and $bpr[$ibpr]->runde < $plan->runde) {
		    $ibpr++;
		}
            $hpkt = '';
            $gpkt = '';
            for ($b = 0; $b < 2; $b++) {
                if ((!isset($bpr[$ibpr])) or ($bpr[$ibpr]->runde > $plan->runde)) {
                    break;
                }
                if (($bpr[$ibpr]->runde == $plan->runde) and ($bpr[$ibpr]->tln_nr == $plan->gegner)) {
                    $gpkt = $bpr[$ibpr]->brettpunkte;
                }
                if (($bpr[$ibpr]->runde == $plan->runde) and ($bpr[$ibpr]->tln_nr == $plan->tln_nr)) {
                    $hpkt = $bpr[$ibpr]->brettpunkte;
                }
                $ibpr++;
            } ?>
		<td><?php if ($hpkt != '' and $gpkt != '') {
		    echo $hpkt.' : '.$gpkt;
		} ?></td>	
    </tr>
    <?php } ?>
    </table>
    <?php } ?>
    <br>
	
    <a name="google"></a>    
	<?php //Kartenanzeige
    if (($mannschaft[0]->lokal == ! false) and (($googlemaps_msch == "3") || ($googlemaps_msch == "1"))  and ($googlemaps == "1")) { ?>
	<h4><?php echo JText::_('OSM_MAPS') ?></h4>
    <?php
        $lat = $mannschaft[0]->lokal_coord_lat;
        $lon = $mannschaft[0]->lokal_coord_long;
        if ($spiellokal1G[0] == ! false) {
            $loc_text = $spiellokal1G[0];
        } else {
            $loc_text = '';
        }
        if (isset($spiellokal1G[1])) {
            $loc_text .= '<br>'.$spiellokal1G[1];
        }
        if (isset($spiellokal1G[2])) {
            $loc_text .= '<br>'.$spiellokal1G[2];
        }
        if (isset($spiellokal1G[3])) {
            $loc_text .= '<br>'.$spiellokal1G[3];
        }
        //Error text if coordinates are zero
        if (isset($spiellokal1G[2]) and $googlemaps_rtype == 2) {
            $error_text = "name,straße,ort,...";
        } else {  // $googlemaps_rtype == 3
            $error_text = "straße,ort,...";
        }
        $img_marker = clm_core::$load->gen_image_url("table/marker-icon");

        ?>
	<br><br>

	<div style="position:relative;">
		<div id="mapdiv1" class="map" style="position:absolute;top:0;right:0;float:right;width:100%;height:300px;text-align:center;"></div>
		<?php if (($lat != 0 || $lon != 0) && $googlemaps_msch == 3) {?>
			<div id="madinfo" style="position:absolute;top:0;right:0;float:right;z-index:1000;width:80%;height:200px;text-align:center;"><span style="font-weight: bold; background-color: #FFF"><?php echo $loc_text; ?></span></div>
		<?php } ?>
	</div>
	<br><br>
,

	<script>
		var Lat=<?php printf('%0.7f', $lat); ?>;
		var Lon=<?php printf('%0.7f', $lon); ?>;
		if (Lat == 0 && Lon == 0) {
			console.log("Zu dieser Adresse sind keine Koordinaten hinterlegt.");
			document.getElementById('mapdiv1').innerHTML = "Die Adresse des Spiellokals wird nicht gefunden.<br>Vielleicht entspricht die Angabe nicht der Vorgabe " + "<?php echo $error_text; ?>" + "<br><br>" + '<?php echo $loc_text; ?>';
		} else {
			<?php if ($googlemaps_msch == 1) {?>
				var popupText = `<?php printf($loc_text); ?>`;
				createLeafletMap(Lat, Lon, popupText, <?php echo $maps_zoom; ?>);
			<?php } ?>
			<?php if ($googlemaps_msch == 3) {?>
				createOSMap(Lat, Lon, `<?php echo $img_marker; ?>`, <?php echo $maps_zoom; ?>);
			<?php } ?>
		}
	</script>
	<div id="mapdiv0" class="map" style="width:100%;height:300px;"></div>																		   
    <br><br>
    <?php } ?>

    <?php echo '<div class="hint">'.$hint_dwzdsb.'</div><br>'; ?>

    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
	<?php } ?>

<div class="clr"></div>
</div>
</div>
