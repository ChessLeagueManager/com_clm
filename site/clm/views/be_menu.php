<?php
function clm_view_be_menu($access, $status)
{
    clm_core::$load->load_css("be_menu");
    clm_core::$load->load_css("icons");
    clm_core::$load->load_js("modal");
    clm_core::$load->load_css("modal");
    clm_core::$load->load_js("be_menu");
    $lang = clm_core::$lang->be_menu;
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
?>

	<div  class="clm-menu">
	<div  class="clm_icons">
	
	<?php //Nur Admin
    if ($access['BE_season_general']) {
?>
	<div class="clm_icon"><a href="index.php?option=com_clm&section=saisons" title=""><img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_saison");
?>" align="middle" border="0" alt="" /><span><?php
        echo $lang->season;
?></span></a> </div>

	<div class="clm_icon"> <a href="index.php?option=com_clm&amp;section=saisons&amp;task=add" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_saison_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->season_new;
?></span></a> </div>
	<?php
    }
    if ($access['BE_event_general']) {
?>	
	<div class="clm_icon">
		<a href="index.php?option=com_clm&view=terminemain" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_termine");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->event;
?></span></a> 
	</div>
	<div class="clm_icon"> 
		<a href="index.php?option=com_clm&view=termineform&task=add" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_termine_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->event_new;
?></span></a> 
	</div>
	<?php
    }
	//if ($countryversion =="de") {
    if ($access['BE_tournament_general']) {
?>	
	<div class="clm_icon"> 
		<a href="index.php?option=com_clm&view=view_tournament" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_turniere");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->tur;
?></span></a> 
	</div>
	<?php
    } 
    //Nur Admin
    if ($access['BE_tournament_create']) {
?>
	<div class="clm_icon"> 
		<a href="index.php?option=com_clm&view=turform&task=add" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_turniere_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->tur_new;
?></span></a> 
	</div>
	<?php
    } //}
    if ($access['BE_league_general']) {
?>
	<div class="clm_icon"> <a href="index.php?option=com_clm&view=view_tournament_group&liga=1" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_liga");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->league;
?></span></a> </div>
	<?php
    }
    if ($access['BE_league_create']) {
?>
	<div class="clm_icon"> <a href="index.php?option=com_clm&amp;section=ligen" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_liga_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->league_new;
?></span></a> </div>
	<?php
    }
    if ($access['BE_teamtournament_general']) {
?>

	<div class="clm_icon"> <a href="index.php?option=com_clm&view=view_tournament_group&liga=0" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_mturnier");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->mantur;
?></span></a> </div>

	<?php
    }
    if ($access['BE_teamtournament_create']) {
?>

	<div class="clm_icon"> <a href="index.php?option=com_clm&amp;section=mturniere" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_mturnier_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->mantur_new;
?></span></a> </div>

	<?php
    }
    if ($access['BE_club_general']) {
?>	

	<div class="clm_icon"> <a href="index.php?option=com_clm&section=vereine" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_verein");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->clubs;
?></span></a> </div>

	<?php
    }
    if ($access['BE_club_create']) {
?>		

	<div class="clm_icon"> <a href="index.php?option=com_clm&amp;section=vereine&amp;task=add" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_verein_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->clubs_new;
?></span></a> </div>

	<?php
    }
    if ($access['BE_team_general']) {
?>		

	<div class="clm_icon"> <a href="index.php?option=com_clm&section=mannschaften" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_mannschaften");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->man;
?></span></a> </div>

	<?php
    }
    if ($access['BE_team_create']) {
?>		

	<div class="clm_icon"> <a href="index.php?option=com_clm&amp;section=mannschaften&amp;task=add" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_mannschaften_neu");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->man_new;
?></span></a> </div>
	<?php
    }
    if ($access['BE_club_edit_member']) {
?>		

	<div class="clm_icon"> <a href="index.php?option=com_clm&section=dwz" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_mitgliederverwaltung");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->player;
?></span></a> </div>
	<?php
    }
    if ($access['BE_user_general']) {
?>		
	<div class="clm_icon"> <a href="index.php?option=com_clm&section=users" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_benutzer");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->user;
?></span></a> </div>
	<?php
    }
    if ($access['BE_dewis_general']) {
?>		
		<div class="clm_icon"> <a href="index.php?option=com_clm&view=auswertung" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_dewis2");
?>" align="middle" border="0" alt="" /> <span><?php
        	if ($countryversion =="de") echo $lang->dewis;
        	if ($countryversion =="en") echo $lang->grading_export;
?></span></a> </div>
	<?php
    }
    if ($access['BE_database_general']) {
?>		
		<div class="clm_icon"> <a href="index.php?option=com_clm&view=db" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_datenbank");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->db;
?></span></a> </div>
	<?php
    }
    if (($access['BE_swt_general'] AND $countryversion =="de") OR $access['BE_pgn_general']) {
?>		
		<div class="clm_icon"> <a href="index.php?option=com_clm&view=swt" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_generic");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->swt;
?></span></a> </div>
	<?php
    } 
    if ($access['BE_config_general']) {
?>
		<div class="clm_icon"> <a href="index.php?option=com_clm&view=view_config" title=""> <img src="<?php
        echo clm_core::$load->gen_image_url("icons/clm_einstellungen");
?>" align="middle" border="0" alt="" /> <span><?php
        echo $lang->config;
?></span></a> </div>
	<?php
    }
?>
	</div>
	</div>
	<?php
    if (!$status["fopen"]) {
        $currentVersion = $lang->fopenProblem;
        $nextVersion    = $lang->fopenProblem;
        $information    = $lang->connection;
        $upgrade = "clm_evil_version";
    } elseif ($status["content"] == "") {
        $currentVersion = $lang->connection;
        $nextVersion    = $lang->connection;
        $information    = $lang->connection;
        $upgrade = "clm_evil_version";
    } else {
        $lang2          = clm_core::$lang->stringArray(explode("\n", $status["content"]));
        $currentVersion = $lang2->currentVersion;
        $nextVersion    = $lang2->nextVersion;      // aktuelle Beta!

        $aktuell        = explode(".", $status["version"]);
        //$install        = explode(".", $lang2->currentVersion);
        $install        = explode(".", $lang2->nextVersion);
        $information    = "information_" . $aktuell[0] . "." . $aktuell[1];
        $information    = $lang2->$information;
        $upgrade        = "clm_good_version";

        // Major Unterschied --> ganz schlecht
        if ($install[0] < $aktuell[0]) {
            $upgrade = "clm_evil_version";
        } else if ($install[0] == $aktuell[0]) {
            // Minor Unterschied --> ganz schlecht
            if ($install[1] > $aktuell[1]) {
                $upgrade = "clm_evil_version";
            } else if ($install[1] == $aktuell[1]) {
                // Patch Unterschied --> schlecht
                if ($install[2] > $aktuell[2]) {
                    $upgrade = "clm_bad_version";
                }
            }
        }
    }
?>
	<div class="clm-status">
	<div class="clm-logo"><img src="<?php
    echo clm_core::$load->gen_image_url("icons/clm_logo");
?>" alt="" /></div>
	<table class="clm-version" border="0">
	<tr>
		<td><?php
    echo $lang->installedVersion;
?></td>
		<td>&nbsp;&nbsp;</td>
		<td <?php
    echo "class=" . $upgrade . " ><b>" . $status["version"];
?></b></td>
	</tr>
	<tr>
		<td><?php
    echo $lang->currentVersion;
?></td>
		<td>&nbsp;&nbsp;</td>
		<td><?php
    echo $currentVersion;
?></td>
	</tr>
	<tr>
		<td><?php
    echo $lang->nextVersion;
?></td>
		<td>&nbsp;&nbsp;</td>
		<td><?php
    echo $nextVersion;
?></td>
	</tr>
	<tr>
	<td colspan="3" ><?php echo $lang->functions;?></td>
	</tr>
	<tr>
		<td>&nbsp;&nbsp;<a href="javascript:void(0);" onclick='clm_modal_display("<?php echo $lang->fopen_info; ?>")' href="javascript:;" ><?php echo $lang->fopen;?></a></td>
		<td>&nbsp;&nbsp;</td>
		<td class="<?php echo ($status["fopen"] ? "clm_good_version" : "clm_evil_version"); ?>" ><b><?php echo ($status["fopen"] ? $lang->enabled : $lang->disabled); ?></b></td>
	</tr>
	<tr>
		<td>&nbsp;&nbsp;<a href="javascript:void(0);" onclick='clm_modal_display("<?php echo $lang->soap_info; ?>")' href="javascript:;" ><?php echo $lang->soap;?></a></td>
		<td>&nbsp;&nbsp;</td>
		<td class="<?php echo ($status["soap"] ? "clm_good_version" : "clm_evil_version"); ?>" ><b><?php echo ($status["soap"] ? $lang->enabled : $lang->disabled); ?></b></td>
	</tr>
	<tr>
	<td colspan="3" ><?php echo $lang->message; ?></td>
	</tr>
	<tr>
	<td colspan="3" class="clm-extern-information" ><?php
    echo $information;
?></td>
	</tr>
	</table>
	<br>
	<a href="http://www.chessleaguemanager.de" target="blank"><?php
    echo $lang->website;
?></a>  |  <a href="http://www.chessleaguemanager.de/index.php?option=com_kunena" target="blank"><?php
    echo $lang->support;
?></a>
	<br>
	<br>
	<u><?php
    echo $lang->team;
?></u>
	<ul type="square">
		<?php
    $i = 0;
    while ($lang->exist("team_" . $i)) {
        $team = "team_" . $i;
        echo '<li>' . $lang->$team . '</li>';
        $i++;
    }
?>
	</ul>
	<br>
	<u><?php
    echo $lang->usedProjects;
?></u>
	<ul type="square">
		<?php
    $i = 0;
    while ($lang->exist("usedProjects_" . $i)) {
        $usedProjects = "usedProjects_" . $i;
        echo '<li>' . $lang->$usedProjects . '</li>';
        $i++;
    }
?>
	</ul>
	</div>
     	<div class="clm_clear" />
<?php
}
?>
