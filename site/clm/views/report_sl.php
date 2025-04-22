<?php
/**
 * @ CLM Extern Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_report_sl($out)
{
    $msg = clm_core::$load->request_int('msg');
    clm_core::$load->load_css("report_sl");
    clm_core::$load->load_css("buttons");
    clm_core::$load->load_css("notification");
    $lang = clm_core::$lang->report;
    clm_core::$cms->setTitle($lang->title.' '.$out["liga"][0]->name);

    // Variablen initialisieren
    $dg 		= $out["input"]["dg"];
    $runde 		= $out["input"]["runde"];
    $iapaar 	= $out["input"]["apaar"];
    $liga 		= $out["liga"];
    $lid 		= $liga[0]->id;
    $_POST['clm_p_sieg'] = $liga[0]->sieg;
    $_POST['clm_p_remis'] = $liga[0]->remis;
    $_POST['clm_p_nieder'] = $liga[0]->nieder;
    $_POST['clm_p_antritt'] = $liga[0]->antritt; //die();
    clm_core::$load->load_js("report_sl");
    $paar 		= $out["paar"];
    $apaar 		= $out["apaar"];
    $oldresult 	= $out["oldresult"];
    $heim 		= $out["heim"];
    $gast 		= $out["gast"];
    $ergebnis 	= $out["ergebnis"];
    $access		= $out["access"];
    $erg_text	= $out["punkteText"];
    $allresults = array();

    if (isset($out["allresult"]) and !is_null($out["allresult"])) {
        foreach ($out["allresult"] as $aresult) {
            $allresult[$aresult->paar] = $aresult->serg;
        }
    }
    $jid = clm_core::$access->getJid();
    //CLM parameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;
    //echo "<br>apaar<br>"; var_dump($apaar);
    //echo "<br>iapaar<br>"; var_dump($iapaar);
    //die();

    echo "<h4>".$liga[0]->name.', '.$lang->round.' '.$runde;
    if ($liga[0]->durchgang > 1) {
        echo " (".$dg.". ".$lang->dg.")";
    }
    if ($liga[0]->datum != '0000-00-00' && $liga[0]->datum != '1970-01-01' && $liga[0]->datum) {
        //echo ', am '.	clm_core::$load->date_to_string($liga[0]->datum,false);
        echo '  '.$lang->date_on.' '.clm_core::$cms->showDate($liga[0]->datum, $lang->date_format_clm_f);
    }
    echo "</h4>";

    ?>
<div class="flex title1">
<div class="element board"><?php echo $lang->board; ?></div>
<div class="element home"><?php echo $lang->home; ?></div>
<div class="element result"><?php echo $lang->result; ?></div>
<div class="element guest"><?php echo $lang->guest; ?></div>
</div>

<div class="flex title2">
<div class="element board">&nbsp;</div>
<div class="element home"><?php echo $paar[0]->hname; ?></div>
<div class="element result result_final"></div>
<div class="element guest"><?php echo $paar[0]->gname; ?></div>
</div>

<?php	for ($i = 0; $i < $liga[0]->stamm; $i++) { ?>

<div class="flex">
<div class="element board"><?php echo($i + 1); ?></div>
<div class="element home">
		  <select name="0" onchange="clm_report_change_player(this,false)" size="1" class="home_select">
			<option value="-2"><?php echo $lang->choose_player; ?></option>
			<?php for ($x = 0; $x < (count($heim)); $x++) {
			    if ($liga[0]->rang != "0") {?>
			  <option value="<?php echo $heim[$x]->mgl_nr.':'.$heim[$x]->zps; ?>"<?php if (isset($oldresult[$i]) and $heim[$x]->mgl_nr == $oldresult[$i]->spieler and $heim[$x]->zps == $oldresult[$i]->zps) {
			      echo ' selected="selected" ';
			  } ?>><?php echo $heim[$x]->rmnr.' - '.$heim[$x]->rang.' &nbsp;&nbsp;';
			        if ($heim[$x]->rang < 1000) {
			            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			        };
			        if ($heim[$x]->rang < 10) {
			            echo "&nbsp;&nbsp;";
			        };
			        echo $heim[$x]->name; ?></option> 
			<?php } else { ?>
			<?php if ($countryversion == "de") { ?>
			  <option value="<?php echo $heim[$x]->mgl_nr.':'.$heim[$x]->zps; ?>"<?php if (isset($oldresult[$i]) and $heim[$x]->mgl_nr == $oldresult[$i]->spieler and $heim[$x]->zps == $oldresult[$i]->zps) {
			      echo ' selected="selected" ';
			  } ?>><?php echo $heim[$x]->snr;
			    if ($heim[$x]->snr < 10) {
			        echo "&nbsp;&nbsp;";
			    } echo ' - '.$heim[$x]->name; ?></option> 
			<?php } else { ?>
			  <option value="<?php echo $heim[$x]->PKZ.':'.$heim[$x]->zps; ?>"<?php if (isset($oldresult[$i]) and $heim[$x]->PKZ == $oldresult[$i]->PKZ and $heim[$x]->zps == $oldresult[$i]->zps) {
			      echo ' selected="selected" ';
			  } ?>><?php echo $heim[$x]->snr;
			    if ($heim[$x]->snr < 10) {
			        echo "&nbsp;&nbsp;";
			    } echo ' - '.$heim[$x]->name; ?></option> 
			<?php }
			}
			} ?>
			<option value="-1"<?php if (isset($oldresult[$i]) and $oldresult[$i]->zps == null) { ?> selected="selected"<?php } ?>>--&nbsp;<?php echo $lang->no_player; ?>&nbsp;--</option>
		  </select>
</div>
<div class="element result">
		  <select class="result_select" onchange="clm_report_change_result(this,false);" size="1" name="<?php echo "ergebnis".($i + 1); ?>" id="<?php echo "ergebnis".($i + 1); ?>">
			<option value="-2"><?php echo $lang->choose_result; ?></option>
			<?php for ($x = 0; $x < 11; $x++) { ?>
			 <option value="<?php echo $ergebnis[$x]->eid; ?>"
				<?php if (isset($oldresult[$i]) and $ergebnis[$x]->eid == $oldresult[$i]->ergebnis) {
				    echo ' selected="selected" ';
				} ?>
				<?php if (!isset($oldresult[$i]) and $ergebnis[$x]->eid == 7) {
				    echo ' selected="selected" ';
				} ?>
				><?php echo $erg_text[$x]->erg_text ; ?></option> 
			<?php } ?>
		  </select>
</div>
<div class="element guest">
		  <select name="0" onchange="clm_report_change_player(this,false)" size="1" class="guest_select">
			<option value="-2"><?php echo $lang->choose_player; ?></option>
			<?php for ($x = 0; $x < (count($gast)); $x++) {
			    if ($liga[0]->rang != "0") {?>
			 <option value="<?php echo $gast[$x]->mgl_nr.':'.$gast[$x]->zps; ?>"<?php if (isset($oldresult[$i]) and $gast[$x]->mgl_nr == $oldresult[$i]->gegner and $gast[$x]->zps == $oldresult[$i]->gzps) {
			     echo ' selected="selected" ';
			 } ?>><?php echo $gast[$x]->rmnr.' - '.$gast[$x]->rang.' &nbsp;&nbsp;';
			        if ($gast[$x]->rang < 1000) {
			            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			        };
			        if ($gast[$x]->rang < 10) {
			            echo "&nbsp;&nbsp;";
			        };
			        echo $gast[$x]->name; ?></option> 
			<?php } else { ?>
			<?php if ($countryversion == "de") { ?>
			 <option value="<?php echo $gast[$x]->mgl_nr.':'.$gast[$x]->zps; ?>"<?php if (isset($oldresult[$i]) and $gast[$x]->mgl_nr == $oldresult[$i]->gegner and $gast[$x]->zps == $oldresult[$i]->gzps) {
			     echo ' selected="selected" ';
			 } ?>><?php echo $gast[$x]->snr;
			    if ($gast[$x]->snr < 10) {
			        echo "&nbsp;&nbsp;";
			    } echo ' - '.$gast[$x]->name; ?></option> 
			<?php } else { ?>
			 <option value="<?php echo $gast[$x]->PKZ.':'.$gast[$x]->zps; ?>"<?php if (isset($oldresult[$i]) and $gast[$x]->PKZ == $oldresult[$i]->gPKZ and $gast[$x]->zps == $oldresult[$i]->gzps) {
			     echo ' selected="selected" ';
			 } ?>><?php echo $gast[$x]->snr;
			    if ($gast[$x]->snr < 10) {
			        echo "&nbsp;&nbsp;";
			    } echo ' - '.$gast[$x]->name; ?></option> 
			<?php }
			}
			} ?>
			<option value="-1"<?php if (isset($oldresult[$i]) and $oldresult[$i]->gzps == null) { ?> selected="selected"<?php } ?>>--&nbsp;<?php echo $lang->no_player; ?>&nbsp;--</option>
		  </select>
</div>
</div>

<?php } ?>

<?php
    if (($config->kommentarfeld == 1) or ($config->kommentarfeld == 2 and ($liga[0]->runden_modus == 4 or $liga[0]->runden_modus == 5))) {    // Kommentarfeld?>			
	<div class="outer_comment">
			<div class="info">
				<?php echo $lang->notice; ?>
			</div>
			<div class="text">
<!--				<textarea class="comment" ><?php echo str_replace('&', '&amp;', $paar[0]->comment);?></textarea> -->
				<textarea class="comment" ><?php echo $paar[0]->comment;?></textarea>
			</div>
	</div>
<?php }
    if (($config->ikommentarfeld == 1) or ($config->ikommentarfeld == 2 and ($liga[0]->runden_modus == 4 or $liga[0]->runden_modus == 5))) {    // internes Kommentarfeld?>			
	<div class="outer_icomment">
			<div class="info">
				<?php echo $lang->inotice; ?>
			</div>
			<div class="text">
<!--				<textarea class="icomment" ><?php echo str_replace('&', '&amp;', $paar[0]->icomment);?></textarea> -->
				<textarea class="icomment" ><?php echo $paar[0]->icomment;?></textarea>
			</div>
	</div>
<?php }

    if ($liga[0]->runden_modus == 4 or $liga[0]->runden_modus == 5) {    // KO System?>	
	<div class="ko">
		<div class="info">
			<?php echo $lang->ko; ?>
		</div>
		<div class="choice">
			<select class="ko_decision" value="<?php echo $paar[0]->ko_decision; ?>" size="1">
			<option value="1" <?php if ($paar[0]->ko_decision == 1) {
			    echo 'selected="selected"';
			} ?>><?php echo $lang->bw;?></option>
			<option value="2" <?php if ($paar[0]->ko_decision == 2) {
			    echo 'selected="selected"';
			} ?>><?php echo $lang->blitz." ".$paar[0]->hname;?></option>
			<option value="3" <?php if ($paar[0]->ko_decision == 3) {
			    echo 'selected="selected"';
			} ?>><?php echo $lang->blitz." ".$paar[0]->gname;?></option>
			<option value="4" <?php if ($paar[0]->ko_decision == 4) {
			    echo 'selected="selected"';
			} ?>><?php echo $lang->luck." ".$paar[0]->hname;?></option>
			<option value="5" <?php if ($paar[0]->ko_decision == 5) {
			    echo 'selected="selected"';
			} ?>><?php echo $lang->luck." ".$paar[0]->gname;?></option>
			</select>
		</div>
	</div>
<?php }
    echo '<input type="hidden" class="liga" value="'.$out["input"]["liga"].'">';
    echo '<input type="hidden" class="runde" value="'.$out["input"]["runde"].'">';
    echo '<input type="hidden" class="dg" value="'.$out["input"]["dg"].'">';
    echo '<input type="hidden" class="paar" value="'.$out["input"]["paar"].'">';
    echo '<input type="hidden" class="apaar" value="'.$out["input"]["apaar"].'">';
    if ($msg == 1) {
        echo '<div class="clm_view_notification"><div class="notice"><span>' . $lang->result_success_needed . '</div></div>';
        $_GET["msg"] = 0;
        $msg = 0;
    } else {
        echo '<div class="clm_view_notification"><div class="notice"><span>' . $lang->data_needed . '</div></div>';
    }
    echo '<div class="button_container">';
    echo '<button type="button" onclick="javascript:history.back(1);" class="clm_button button_back">'.$lang->button_back.'</button>';
    echo '<button type="button" onclick="clm_report_block(this)" class="clm_button button_block" disabled>'.$lang->button_block.'</button>';
    echo '<button type="button" onclick="clm_report_save(this)" class="clm_button button_save" disabled>'.$lang->button_save.'</button>';
    echo '</div><div class="space"></div>';
    echo '<br>';

    foreach ($apaar as $apaar1) {
        if (strpos($iapaar, 'p'.(string) $apaar1->paar) === false) {
            continue;
        }
        //	$href = 'http://localhost/sbb11/index.php/component/clm/?view=meldung_sl&saison='.$apaar1->sid;
        $href = 'index.php?option=com_clm&view=meldung_sl&saison='.$apaar1->sid;
        $href .= '&liga='.$lid.'&dg='.$dg.'&runde='.$runde;
        $href .= '&paar='.$apaar1->paar.'&apaar='.$iapaar.'&Itemid=1';
        //echo "<br>href ".$href;
        if (is_null($apaar1->brettpunkte)) {
            $apaar1->brettpunkte = 0;
        }
        if (is_null($apaar1->gbrettpunkte)) {
            $apaar1->gbrettpunkte = 0;
        }
        $htext = str_pad($apaar1->hname, 30, ".", STR_PAD_LEFT).'&nbsp;&nbsp;&nbsp;'.$apaar1->brettpunkte.' : '.$apaar1->gbrettpunkte.'&nbsp;&nbsp;&nbsp;'.str_pad($apaar1->gname, 30, ".");
        //echo "<br>htext ".$htext;
        //die();
        ?>
<br>
<button type="button" onclick="location.href='<?php echo $href; ?>'" class="clm_button button_save">
	<span style="display: inline-block;width: 160px;text-align: right"><?php echo $apaar1->hname; ?></span>
	<span style="display: inline-block;width: 80px;text-align: center"><?php echo $apaar1->brettpunkte.' : '.$apaar1->gbrettpunkte; ?></span>
	<span style="display: inline-block;width: 160px;text-align: left"><?php echo $apaar1->gname; ?></span></button>
<?php
            if (!isset($allresult[$apaar1->paar])) {
                $sttext = '<span style="color: red;">Aufstellungen fehlen noch</span>';
            } elseif ($allresult[$apaar1->paar] == $liga[0]->stamm) {
                $sttext = '<span style="color: green;">Vergleich beendet</span>';
            } else {
                $sttext = '<span style="color: blue;">Vergleich läuft</span>';
            }
        echo '&nbsp;&nbsp;'.$sttext;
    }
} ?>
