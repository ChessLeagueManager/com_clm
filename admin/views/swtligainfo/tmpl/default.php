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

$swt_file = clm_core::$load->request_string('swt_file', '');
$update = clm_core::$load->request_int('update', 0);
$mturnier = clm_core::$load->request_int('mturnier', 0);
$noOrgReference = clm_core::$load->request_string('noOrgReference', '0');
$noBoardResults = clm_core::$load->request_string('noBoardResults', '0');
$lid = clm_core::$load->request_int('lid', 0);
$ordering = $this->default['ordering'];
$str_params = clm_core::$load->request_string('str_params');
?>

<script language="javascript" type="text/javascript">

		Joomla.submitbutton = function (pressbutton) { 		
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Joomla.submitform( pressbutton );
            return;
        }
        // do field validation
        if (form.name.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_1', true ); ?>" );
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('sid');			
			var opt;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
		}
		if ( val == 0 ) {
			alert( "<?php echo JText::_( 'LEAGUE_HINT_2', true ); ?>" );
        } else if (form.stamm.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_3', true ); ?>" );
        } else if (form.ersatz.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_4', true ); ?>" );
        } else if (form.teil.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_5', true ); ?>" );
        } else if (form.runden.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_6', true ); ?>" );
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('durchgang');			
			var opt;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
		}
		if ( val == 0 ) {
			alert( "<?php echo JText::_( 'LEAGUE_HINT_7', true ); ?>" );
        } else if ( form.anz_sgp.value < 0 ) {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_8', true ); ?>" );
        } else if ( form.anz_sgp.value > 20 ) {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_8', true ); ?>" );
        } else {
            Joomla.submitform( pressbutton );
        }
    }

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'LEAGUE_DATA' ); ?></legend>
			<table class="paramlist admintable">
			
				<tr>
					<td width="20%" nowrap="nowrap">
						<label for="name"><?php echo JText::_( 'LEAGUE_NAME' ); ?></label>
					</td>
					<td colspan="2">
						<input class="inputbox" type="text" name="name" id="name" size="20" maxlength="30" value="<?php echo $this->swt_data['liga_name']; ?>" />
                    </td>
                    <td nowrap="nowrap">
                        <label for="sl"><?php echo JText::_( 'LEAGUE_CHIEF' ); ?></label>
                        </td><td colspan="2">
                        <?php echo $this->lists['sl']; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap="nowrap">
                        <label for="saison"><?php echo JText::_( 'LEAGUE_SEASON' ); ?></label>
                        </td><td colspan="2">
                        <?php echo $this->lists['saison']; ?>
                    </td>

                    <td nowrap="nowrap">
                        <label for="rang"><?php echo JText::_( 'LEAGUE_LIST_TYPE' ); ?></label>
                    </td>
                    <td colspan="2">
                        <?php
                        	if ($this->rang == 0) {
                        		echo $this->lists['gruppe'];
 	                    	}
 	                    	elseif ($this->rang == 1) {
 	                    		echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_RANK' );
 	                    		// siehe zusaetzlich unten
 	                    	}
 	                    	elseif ($this->rang == 2) {
 	                    		echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_LIST' );
 	                    		// siehe zusaetzlich unten
 	                    	}
 	                    ?> 
                    </td>
                </tr>
                <?php if ($this->rang == 1) { ?>
			        <input type="hidden" name="rang" value="1" />
			    <?php } elseif ($this->rang == 2) { ?>
					<input type="hidden" name="rang" value="0" />
				<?php } ?>
				
				<tr>
					<td nowrap="nowrap">
						<label for="teil"><?php echo JText::_( 'LEAGUE_TEAMS' ); ?></label>
						</td><td colspan="2">
						<input class="inputbox" type="text" name="teil" id="teil" size="4" maxlength="4" value="<?php echo $this->swt_data['anz_mannschaften']; ?>" />
					</td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="stammspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_1' ); ?></label>
						</td><td colspan="2">
						<input class="inputbox" type="text" name="stamm" id="stamm" size="4" maxlength="4" value="<?php echo $this->swt_data['anz_bretter']; ?>" />
					</td>
					<td nowrap="nowrap">
						<label for="erstatzspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_2' ); ?></label>
						</td><td colspan="2">
						<input class="inputbox" type="text" name="ersatz" id="ersatz" size="4" maxlength="4" value="<?php echo $this->swt_data['anz_ersatz']; ?>" />
					</td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="runden"><?php echo JText::_( 'LEAGUE_ROUNDS' ); ?></label>
						</td><td colspan="2">
						<input class="inputbox" type="text" name="runden" id="runden" size="4" maxlength="4" value="<?php echo $this->swt_data['anz_runden']; ?>" />
					</td>
					<td nowrap="nowrap">
						<label for="durchgang"><?php echo JText::_( 'LEAGUE_DG' ); ?></label>
						</td><td colspan="2">
						<select name="durchgang" id="durchgang" value="<?php echo $this->swt_data['anz_durchgaenge']; ?>" size="1">
							<option value="1" <?php if ($this->swt_data['anz_durchgaenge'] < 2) {echo 'selected="selected"';} ?>>1</option>
							<option value="2" <?php if ($this->swt_data['anz_durchgaenge'] == 2) {echo 'selected="selected"';} ?>>2</option>
							<option value="3" <?php if ($this->swt_data['anz_durchgaenge'] == 3) {echo 'selected="selected"';} ?>>3</option>
							<option value="4" <?php if ($this->swt_data['anz_durchgaenge'] == 4) {echo 'selected="selected"';} ?>>4</option>
						</select>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap">
						<label for="ersatz_regel"><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL' ); ?></label>
					</td><td colspan="2">
						<select name="ersatz_regel" id="ersatz_regel" value="<?php echo $this->lists['ersatz_regel']; ?>" size="1">
							<option value="0" <?php if ($this->lists['ersatz_regel'] == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_0' );?></option>
							<option value="1" <?php if ($this->lists['ersatz_regel'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_1' );?></option>
						</select>
					</td>
					<td nowrap="nowrap">
						<label for="anz_sgp"><?php echo JText::_( 'LEAGUE_ANZ_SGP' ); ?></label>
						</td><td colspan="2">
						<input class="inputbox" type="text" name="anz_sgp" id="anz_sgp" size="4" maxlength="4" value="<?php echo $this->default['params']['anz_sgp'] ?>" />
					</td>
				</tr>
				
				<tr>
		<td nowrap="nowrap">
		<label for="color_order"><?php echo JText::_( 'LEAGUE_COLOR_ORDER' ); ?></label>
		</td><td colspan="2">
			<select name="color_order" id="color_order" value="<?php echo $this->default['params']['color_order']; ?>" size="1">
			<!--<option>- wählen -</option>-->
			<option value="1" <?php if ($this->default['params']['color_order'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_1' );?></option>
			<option value="2" <?php if ($this->default['params']['color_order'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_2' );?></option>
			<option value="3" <?php if ($this->default['params']['color_order'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_3' );?></option>
			<option value="4" <?php if ($this->default['params']['color_order'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_4' );?></option>
			<option value="5" <?php if ($this->default['params']['color_order'] == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_5' );?></option>
			<option value="6" <?php if ($this->default['params']['color_order'] == 6) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_6' );?></option>
			</select>
		</td>
		<td nowrap="nowrap"></td><td colspan="2"></td>
		</tr>

<!-- ab hier MTurniere-Eingabemaske -->
<?php if ($mturnier == 1) { ?>
				<tr>
					<td nowrap="nowrap">
						<label for="runden_modus"><?php echo JText::_( 'MTURN_PAIRING_MODE' ); ?></label>
						</td><td colspan="2">
						<select name="runden_modus" id="runden_modus" value="<?php echo $this->swt_data['runden_modus']; ?>" size="1">
							<!-- <option>- wählen -</option> -->
							<option value="1" <?php if ($this->swt_data['runden_modus'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_2' );?></option>
							<option value="2" <?php if ($this->swt_data['runden_modus'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_3' );?></option>
							<option value="3" <?php if ($this->swt_data['runden_modus'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_4' );?></option>
							<option value="4" <?php if ($this->swt_data['runden_modus'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_5' );?></option>
						</select>
					</td>
					<td nowrap="nowrap">
						<label for="heim"><?php echo JText::_( 'LEAGUE_HOME' ); ?></label>
					</td><td colspan="2"><fieldset class="radio">
						<?php echo $this->lists['heim']; ?>
					</fieldset></td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="tiebr1"><?php echo '1.'.JText::_( 'MTURN_TIEBREAKER' ); ?></label>
						</td><td colspan="2">
						<select name="tiebr1" id="tiebr1" value="<?php echo $this->swt_data['tiebr1']; ?>" size="1">
							<!--<option>- wählen -</option>-->
							<option value="0" <?php if ($this->swt_data['tiebr1'] == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_SELECT_TIEBR_0' );?></option>
							<option value="1" <?php if ($this->swt_data['tiebr1'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_1' );?></option>
							<option value="11" <?php if ($this->swt_data['tiebr1'] == 11) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_11' );?></option>
							<option value="2" <?php if ($this->swt_data['tiebr1'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_2' );?></option>
							<option value="12" <?php if ($this->swt_data['tiebr1'] == 12) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_12' );?></option>
							<option value="7" <?php if ($this->swt_data['tiebr1'] == 7) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_7' );?></option>
							<option value="17" <?php if ($this->swt_data['tiebr1'] == 17) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_17' );?></option>
							<option value="8" <?php if ($this->swt_data['tiebr1'] == 8) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_8' );?></option>
							<option value="18" <?php if ($this->swt_data['tiebr1'] == 18) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_18' );?></option>
							<option value="23" <?php if ($this->swt_data['tiebr1'] == 23) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_23' );?></option>
							<option value="4" <?php if ($this->swt_data['tiebr1'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_4' );?></option>
							<option value="5" <?php if ($this->swt_data['tiebr1'] == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_5' );?></option>
							<option value="9" <?php if ($this->swt_data['tiebr1'] == 9) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_9' );?></option>
							<option value="10" <?php if ($this->swt_data['tiebr1'] == 10) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_10' );?></option>
							<option value="3" <?php if ($this->swt_data['tiebr1'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_3' );?></option>
							<option value="25" <?php if ($this->swt_data['tiebr1'] == 25) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_25' );?></option>
						</select>
					</td>
					<td nowrap="nowrap">
						<label for="tiebr2"><?php echo '2.'.JText::_( 'MTURN_TIEBREAKER' ); ?></label>
					</td>
					<td colspan="2">
						<select name="tiebr2" id="tiebr2" value="<?php echo $this->swt_data['tiebr2']; ?>" size="1">
							<!--<option>- wählen -</option>-->
							<option value="0" <?php if ($this->swt_data['tiebr2'] == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_SELECT_TIEBR_0' );?></option>
							<option value="1" <?php if ($this->swt_data['tiebr2'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_1' );?></option>
							<option value="11" <?php if ($this->swt_data['tiebr2'] == 11) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_11' );?></option>
							<option value="2" <?php if ($this->swt_data['tiebr2'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_2' );?></option>
							<option value="12" <?php if ($this->swt_data['tiebr2'] == 12) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_12' );?></option>
							<option value="7" <?php if ($this->swt_data['tiebr2'] == 7) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_7' );?></option>
							<option value="17" <?php if ($this->swt_data['tiebr2'] == 17) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_17' );?></option>
							<option value="8" <?php if ($this->swt_data['tiebr2'] == 8) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_8' );?></option>
							<option value="18" <?php if ($this->swt_data['tiebr2'] == 18) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_18' );?></option>
							<option value="23" <?php if ($this->swt_data['tiebr2'] == 23) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_23' );?></option>
							<option value="4" <?php if ($this->swt_data['tiebr2'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_4' );?></option>
							<option value="5" <?php if ($this->swt_data['tiebr2'] == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_5' );?></option>
							<option value="9" <?php if ($this->swt_data['tiebr2'] == 9) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_9' );?></option>
							<option value="10" <?php if ($this->swt_data['tiebr2'] == 10) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_10' );?></option>
							<option value="3" <?php if ($this->swt_data['tiebr2'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_3' );?></option>
							<option value="25" <?php if ($this->swt_data['tiebr2'] == 25) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_25' );?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap">
						<label for="tiebr3"><?php echo '3.'.JText::_( 'MTURN_TIEBREAKER' ); ?></label>
						</td><td colspan="2">
						<select name="tiebr3" id="tiebr3" value="<?php echo $this->swt_data['tiebr3']; ?>" size="1">
							<!--<option>- wählen -</option>-->
							<option value="0" <?php if ($this->swt_data['tiebr3'] == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_SELECT_TIEBR_0' );?></option>
							<option value="1" <?php if ($this->swt_data['tiebr3'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_1' );?></option>
							<option value="11" <?php if ($this->swt_data['tiebr3'] == 11) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_11' );?></option>
							<option value="2" <?php if ($this->swt_data['tiebr3'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_2' );?></option>
							<option value="12" <?php if ($this->swt_data['tiebr3'] == 12) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_12' );?></option>
							<option value="7" <?php if ($this->swt_data['tiebr3'] == 7) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_7' );?></option>
							<option value="17" <?php if ($this->swt_data['tiebr3'] == 17) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_17' );?></option>
							<option value="8" <?php if ($this->swt_data['tiebr3'] == 8) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_8' );?></option>
							<option value="18" <?php if ($this->swt_data['tiebr3'] == 18) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_18' );?></option>
							<option value="23" <?php if ($this->swt_data['tiebr3'] == 23) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_23' );?></option>
							<option value="4" <?php if ($this->swt_data['tiebr3'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_4' );?></option>
							<option value="5" <?php if ($this->swt_data['tiebr3'] == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_5' );?></option>
							<option value="9" <?php if ($this->swt_data['tiebr3'] == 9) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_9' );?></option>
							<option value="10" <?php if ($this->swt_data['tiebr3'] == 10) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_10' );?></option>
							<option value="3" <?php if ($this->swt_data['tiebr3'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_3' );?></option>
							<option value="25" <?php if ($this->swt_data['tiebr3'] == 25) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_25' );?></option>
						</select>
					</td>
					<td class="paramlist_key">
						<?php echo JText::_('MTURN_TIEBREAKERSFIDECORRECT'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'optionTiebreakersFideCorrect', 'class="inputbox"', $this->swt_data['optionTiebreakersFideCorrect']); ?>
					</fieldset></td>
				</tr>
<?php } ?>
<!-- bis hier MTurniere-Eingabemaske -->

<!-- ab hier Liga-Eingabemaske -->
<?php if ($mturnier != 1) { // $mturnier != 1 ?>
				<tr>
					<td nowrap="nowrap">
						<label for="runden_modus"><?php echo JText::_( 'LEAGUE_PAIRING_MODE' ); ?></label>
						</td><td colspan="2">
						<select name="runden_modus" id="runden_modus" value="<?php echo $this->runden_modus; ?>" size="1">
							<!--<option>- wählen -</option>-->
							<option value="1" <?php if ($this->default['runden_modus'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_PAIRING_MODE_2' );?></option>
							<option value="2" <?php if ($this->default['runden_modus'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_PAIRING_MODE_3' );?></option>
						</select>
					</td>
					<td nowrap="nowrap">
						<label for="heim"><?php echo JText::_( 'LEAGUE_HOME' ); ?></label>
						</td><td colspan="2"><fieldset class="radio">
						<?php echo $this->lists['heim']; ?>
					</fieldset></td>
				</tr>
<?php } ?>
<!-- bis hier Liga-Eingabemaske -->
			<tr>
				<td nowrap="nowrap">
					<label for="pseudo_dwz"><?php echo JText::_( 'LEAGUE_PSEUDO_DWZ' ); ?></label>
				</td><td colspan="2">
					<input class="inputbox" type="text" name="pseudo_dwz" id="pseudo_dwz" size="4" maxlength="4" value="<?php echo $this->swt_data['pseudo_dwz']; ?>" />
				</td>
				<td colspan="2"></td>
			</tr>
				
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_( 'LEAGUE_VALUATION' ); ?></legend>
			<table class="paramlist admintable">
				<tr>
					<td nowrap="nowrap">&nbsp;</td>
					<td><?php echo JText::_( 'LEAGUE_VALUATION_1' );?></td>
					<td><?php echo JText::_( 'LEAGUE_VALUATION_2' );?></td>
					<td><?php echo JText::_( 'LEAGUE_VALUATION_3' );?></td>
					<td><?php echo JText::_( 'LEAGUE_VALUATION_4' );?></td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="punkte_modus"><?php echo JText::_( 'LEAGUE_MATCH_VALUATION' ); ?></label>
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="sieg" id="sieg" size="4" maxlength="4" value="<?php if($this->swt_data['std_wertung'] == false){ echo $this->swt_data['siegpunkte'];} else { echo "1";}; ?>" />
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="remis" id="remis" size="4" maxlength="4" value="<?php if($this->swt_data['std_wertung'] == false){ echo $this->swt_data['remispunkte'];} else { echo "0.5";}; ?>" />
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="nieder" id="nieder" size="4" maxlength="4" value="<?php if($this->swt_data['std_wertung'] == false){ echo $this->swt_data['verlustpunkte'];} else { echo "0";}; ?>" />
					</td>
					<td>&nbsp;&nbsp;&nbsp;
		          		<input class="inputbox" type="text" name="antritt" id="antritt" size="4" maxlength="4" value="<?php if($this->swt_data['std_wertung'] == false){ echo $this->swt_data['antrittspunkte'];} else { echo "0";}; ?>" />
		          	</td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="man_punkte"><?php echo JText::_( 'LEAGUE_TEAM_POINTS' ); ?></label>
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="man_sieg" id="man_sieg" size="4" maxlength="4" value="<?php if ($this->swt_data['man_std_wertung'] == false){ echo $this->swt_data['man_siegpunkte'];} else { echo "2";}; ?>" />
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="man_remis" id="man_remis" size="4" maxlength="4" value="<?php if($this->swt_data['man_std_wertung'] == false){ echo $this->swt_data['man_remispunkte'];} else { echo "1";}; ?>" />
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="man_nieder" id="man_nieder" size="4" maxlength="4" value="<?php if($this->swt_data['man_std_wertung'] == false){ echo $this->swt_data['man_verlustpunkte'];} else { echo "0";}; ?>" />
					</td>
					<td>&nbsp;&nbsp;&nbsp;
						<input class="inputbox" type="text" name="man_antritt" id="man_antritt" size="4" maxlength="4" value="<?php if($this->swt_data['man_std_wertung'] == false){ echo $this->swt_data['man_antrittspunkte'];} else { echo "0";}; ?>" />
					</td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="sieg_bed"><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS' ); ?></label>
					</td>
					<td colspan="2">&nbsp;&nbsp;
						<select name="sieg_bed" id="sieg_bed" value="<?php echo $this->swt_data['sieg_bed']; ?>" size="1">
							<option value="1" <?php if ($this->swt_data['sieg_bed'] == 1) {echo 'selected="selected"';}  ?>>
								<?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_1' );?>
							</option>
							<option value="2" <?php if ($this->swt_data['sieg_bed'] == 2) {echo 'selected="selected"';}  ?>>
								<?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_2' );?>
							</option>
						</select>
					</td>
					<td nowrap="nowrap">
						<label for="b_wertung"><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS' ); //klkl?></label>
					</td>
					<td colspan="2">&nbsp;&nbsp;
						<select name="b_wertung" id="b_wertung" value="<?php echo $this->swt_data['b_wertung']; ?>" size="1">
							<option value="0" <?php if ($this->swt_data['b_wertung'] == 0) {echo 'selected="selected"';}  ?>>
								<?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_0' );?>
							</option>
							<option value="3" <?php if ($this->swt_data['b_wertung'] == 3) {echo 'selected="selected"';}  ?>>
								<?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_3' );?>
							</option>
							<option value="4" <?php if ($this->swt_data['b_wertung'] == 4) {echo 'selected="selected"';}  ?>>
								<?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_4' );?>
							</option>
						</select>
					</td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="auf"><?php echo JText::_( 'LEAGUE_UP' ); ?></label>
					</td>
					<td colspan="2">&nbsp;&nbsp;
						<input class="inputbox" type="text" name="auf" id="auf" size="10" maxlength="10" value="<?php echo $this->default['anz_aufsteiger']; ?>" />
					</td>

					<td nowrap="nowrap">
						<label for="color_auf"><?php echo JText::_( 'LEAGUE_UP_POSSIBLE' ); ?></label>
					</td>
					<td colspan="2">&nbsp;&nbsp;
						<input class="inputbox" type="text" name="auf_evtl" id="auf_evtl" size="10" maxlength="10" value="<?php echo $this->default['anz_moegl_aufsteiger']; ?>" />
					</td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="ab"><?php echo JText::_( 'LEAGUE_DOWN' ); ?></label>
					</td>
					<td colspan="2">&nbsp;&nbsp;
						<input class="inputbox" type="text" name="ab" id="ab" size="10" maxlength="10" value="<?php echo $this->default['anz_absteiger']; ?>" />
					</td>

					<td nowrap="nowrap">
						<label for="color_ab"><?php echo JText::_( 'LEAGUE_DOWN_POSSIBILE' ); ?></label>
					</td>
					<td colspan="2">&nbsp;&nbsp;
						<input class="inputbox" type="text" name="ab_evtl" id="ab_evtl" size="10" maxlength="10" value="<?php echo $this->default['anz_moegl_absteiger']; ?>" />
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_( 'LEAGUE_PREFERENCES' ); ?></legend>
			<table class="paramlist admintable">

				<tr>
					<td nowrap="nowrap" colspan="2">
						<label for="anzeige_ma"><?php echo JText::_( 'LEAGUE_SHOW_PLAYERLIST' ); ?></label>
						</td><td colspan="1"><fieldset class="radio">
						<?php echo $this->lists['anzeige_ma']; ?>
						</fieldset></td>
				</tr>
				<tr>
					<td nowrap="nowrap">
						<label for="mail"><?php echo JText::_( 'LEAGUE_MAIL' ); ?></label>
					</td>
					<td colspan="4"><fieldset class="radio">
						<?php echo $this->lists['mail']; ?>
					</fieldset></td>
				</tr>

				<?php if ($this->sl_mail == "1") { ?>
					<tr>
						<td nowrap="nowrap">
							<label for="sl_mail"><?php echo JText::_( 'LEAGUE_MAIL_CHIEF' ); ?></label>
						</td>
						<td colspan="4"><fieldset class="radio">
							<?php echo $this->lists['sl_mail']; ?>
						</fieldset></td>
					</tr>
				<?php } else { ?>
				<input type="hidden" name="sl_mail" value="0" />
				<?php } ?>
			
				<tr>
					<td nowrap="nowrap">
						<label for="order"><?php echo JText::_( 'LEAGUE_ORDERING' ); ?></label>
						</td>
					<td colspan="4"><fieldset class="radio">
						<?php echo $this->lists['order']; ?>
					</fieldset></td>
				</tr>

				<tr>
					<td nowrap="nowrap">
						<label for="published"><?php echo JText::_( 'LEAGUE_PUBLISHED' ); ?></label>
					</td>
					<td colspan="4"><fieldset class="radio">
						<?php echo $this->lists['published']; ?>
					</fieldset></td>
				</tr>

			</table>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend><?php echo ''; ?></legend>
			<table class="paramlist admintable">
				<legend><?php echo JText::_( 'SWT_DWZ_HANDLING' ); ?></legend>
				<tr>
					<td style="text-align: left;">					
						<label for="dwz_trans_1">
						<input type="radio" id="dwz_trans_1" name="dwz_handling" value="1" >
						<?php echo '&nbsp;'.JText::_( 'SWT_DWZ_HANDLING_1' ); ?></label>
						<label for="dwz_trans_0">
						<input type="radio" id="dwz_trans_0" name="dwz_handling" value="0" checked="checked" >
						<?php echo '&nbsp;'.JText::_( 'SWT_DWZ_HANDLING_0' ); ?></label>
					</td>
				</tr>
			</table>
			<br>
				<legend><?php echo JText::_( 'SWT_SP_ACTIONS' ); ?></legend>
				<tr>
					<td nowrap="nowrap">
						<label for="name_land"><?php echo JText::_( 'SWT_NAME_LAND' ); ?></label>
					</td>
					<td><fieldset class="radio">
						<?php echo $this->lists['name_land']; ?>
					</fieldset></td>
				</tr>
			<br><br>
			<legend><?php echo JText::_( 'REMARKS' ); ?></legend>
			<table class="paramlist admintable">
				<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="4" style="width:90%"><?php echo ''; // str_replace('&','&amp;',$this->bemerkungen);?></textarea>
					</td>
				</tr>
			</table>

			<table class="adminlist">
				<tr>
				<legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',"Import durch SWT-Datei.");?></textarea>
					</td>
				</tr>
			</table>

		</fieldset>

		<fieldset>
			<legend><?php echo JText::_( 'LEAGUE_HINTS' ); ?></legend>
			<b><?php echo JText::_( 'LEAGUE_HINTS_PAIRING_MODE' ); ?></b>

			<?php
				for ($i = 1; $i <= 28; $i++) {
					echo JText::_( 'LEAGUE_HINTS_' . $i );
				}
			?>
			<!--
			<?php echo JText::_( 'LEAGUE_HINTS_1' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_2' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_3' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_4' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_5' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_6' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_7' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_8' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_9' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_10' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_11' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_12' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_20' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_21' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_22' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_23' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_24' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_25' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_26' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_27' ); ?>
			<?php echo JText::_( 'LEAGUE_HINTS_28' ); ?>
			//-->
			
		</fieldset>
	</div>

	<div class="clr"></div>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtligainfo" />
	<input type="hidden" name="controller" value="swtligainfo" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="swt_file" value="<?php echo $swt_file; ?>" />
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
	<input type="hidden" name="mturnier" value="<?php echo $mturnier; ?>" />
	<input type="hidden" name="noOrgReference" value="<?php echo $noOrgReference; ?>" />
	<input type="hidden" name="noBoardResults" value="<?php echo $noBoardResults; ?>" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
	<input type="hidden" name="ordering" value="<?php echo $ordering; ?>" />
	<input type="hidden" name="str_params" value="<?php echo $str_params; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
