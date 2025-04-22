<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$turParams = new clm_class_params($this->turnier->params);
$param_typeaccount = $turParams->get('typeAccount', 0);
$param_teamranking = $turParams->get('teamranking', 0);
$param_import_source = $turParams->get('import_source', 0);
$param_eloanalysis = $turParams->get('optionEloAnalysis', 0);
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="col width-50">
		<fieldset class="adminform">
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('CLM_NUMBER'); ?>:</td>
				<td><input class="inputbox" type="text" name="snr" id="snr" size="3" maxlength="3" value="<?php echo $this->player->snr; ?>" /></td>
			</tr>
		</table>
		
		</fieldset>
	</div>
	
	<div class="clr"></div>
	
	<div class="col width-50">
		<fieldset class="adminform">
		<br>
		<legend><?php echo JText::_('PLAYER_DATA'); ?></legend>

		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">* <?php echo JText::_('PLAYER_NAME'); ?> (<?php echo JText::_('LASTNAME_FIRSTNAME'); ?>):</td>
				<td><input class="inputbox" type="text" name="name" id="name" size="20" maxlength="60" value="<?php echo $this->player->name; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('CLUB'); ?>:</td>
				<td><input class="inputbox" type="text" name="verein" id="verein" size="20" maxlength="60" value="<?php echo $this->player->verein; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_PKZ'); ?>:</td>
				<td><input class="inputbox" type="text" name="PKZ" id="PKZ" size="9" maxlength="9" value="<?php echo $this->player->PKZ; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_ZPS'); ?>:</td>
				<td><input class="inputbox" type="text" name="zps" id="zps" size="5" maxlength="5" value="<?php if ($this->player->zps != '0') {
				    echo $this->player->zps;
				} else {
				    echo "";
				} ?>" /></td>
		
	   
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_MGLNR'); ?>:</td>
				<td><input class="inputbox" type="text" name="mgl_nr" id="mgl_nr" size="4" maxlength="4" value="<?php if ($this->player->mgl_nr != 0) {
				    echo $this->player->mgl_nr;
				} else {
				    echo "";
				} ?>" /></td>
			</tr>
			<?php if ($param_teamranking > 0) { ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('TEAM'); ?>:</td>
				<td class="paramlist_value">
					<?php
                    $options = array();
			    $options[0] = JText::_('KEIN TEAM');
			    foreach ($this->teams as $team) {
			        $options[$team->tln_nr] = $team->name;
			    }
			    $optionlist = array();
			    foreach ($options as $key => $val) {
			        $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
			    }
			    echo JHtml::_('select.genericlist', $optionlist, 'mtln_nr', 'class="inputbox"', 'id', 'name', $this->player->mtln_nr);
			    ?>
				</td>
			</tr>
			<?php } ?>
			<?php if ($param_import_source != '' and $param_import_source != 0) { ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('DECODE_NICKNAME'); ?> (<?php echo $param_import_source; ?>):</td>
				<td><input class="inputbox" type="text" name="oname" id="oname" size="20" maxlength="60" value="<?php echo $this->player->oname; ?>" /></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('TWZ'); ?>:</td>
				<td><input class="inputbox" type="text" name="twz" id="twz" size="4" maxlength="4" value="<?php echo $this->player->twz; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('RATING'); ?>:</td>
				<td><input class="inputbox" type="text" name="start_dwz" id="start_dwz" size="4" maxlength="4" value="<?php echo $this->player->start_dwz; ?>" /></td>
				<td class="key" nowrap="nowrap"><?php echo JText::_('RATING_INDEX'); ?>:</td>
				<td><input class="inputbox" type="text" name="start_I0" id="start_I0" size="4" maxlength="4" value="<?php echo $this->player->start_I0; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('FIDE_ELO'); ?>:</td>
				<td><input class="inputbox" type="text" name="FIDEelo" id="FIDEelo" size="4" maxlength="4" value="<?php echo $this->player->FIDEelo; ?>" /></td>
			</tr>
			<?php if ($param_eloanalysis == 1) { ?>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('FIDE_ID'); ?>:</td>
					<td><input class="inputbox" type="text" name="FIDEid" id="FIDEid" size="8" maxlength="8" value="<?php echo $this->player->FIDEid; ?>" /></td>
					<td class="key" nowrap="nowrap"><?php echo JText::_('FIDE_CCO'); ?>:</td>
					<td><input class="inputbox" type="text" name="FIDEcco" id="FIDEcco" size="3" maxlength="3" value="<?php echo $this->player->FIDEcco; ?>" /></td>
				</tr>
			<?php } ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_TITLE'); ?>:</td>
				<td><input class="inputbox" type="text" name="titel" id="titel" size="3" maxlength="3" value="<?php echo $this->player->titel; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_SEX'); ?>:</td>
				<td class="paramlist_value">
					<?php
			    $options = array();
$options[''] = '';
$options['M'] = JText::_('OPTION_SEX_M');
$options['W'] = JText::_('OPTION_SEX_W');
$optionlist = array();
foreach ($options as $key => $val) {
    $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
}
echo JHtml::_('select.genericlist', $optionlist, 'geschlecht', 'class="inputbox"', 'id', 'name', $this->player->geschlecht);
?>
				</td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_BIRTH_YEAR'); ?>:</td>
				<td><input class="inputbox" type="text" name="birthYear" id="birthYear" size="4" maxlength="4" value="<?php echo $this->player->birthYear; ?>"/></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('TOURNAMENT_SPECIAL_POINTS'); ?>:</td>
				<td><input class="inputbox" type="text" name="s_punkte" id="s_punkte" size="4" maxlength="4" value="<?php echo $this->player->s_punkte; ?>"/></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_EMAIL'); ?>:</td>
				<td><input class="inputbox" type="text" name="email" id="email" size="50" maxlength="60" value="<?php echo $this->player->email; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_TEL_NO'); ?>:</td>
				<td><input class="inputbox" type="text" name="tel_no" id="tel_no" size="30" maxlength="30" value="<?php echo $this->player->tel_no; ?>" /></td>
			</tr>
			<?php if ($param_typeaccount > '0') { ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_ACCOUNT_'.$param_typeaccount); ?>:</td>
				<td><input class="inputbox" type="text" name="account" id="account" size="50" maxlength="50" value="<?php echo $this->player->account; ?>" /></td>
			</tr>
			<?php } ?>
		</table>
		
		</fieldset>
	</div>


	<div class="col width-50">
		<fieldset class="adminform">
		<br>
		<legend><?php echo JText::_('RESULT'); ?></legend>

		<table class="admintable">
			
			<?php
            if ($this->turnier->typ != 3) { // nicht KO-System
                ?>
			
				</tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('POINTS'); ?>:</td>
					<td><input class="inputbox" type="text" name="sum_punkte" id="sum_punkte" size="4" maxlength="4" value="<?php echo $this->player->sum_punkte; ?>" /></td>
				</tr>
				
				<?php
                    $fwFieldNames = array(1 => 'sum_bhlz', 'sum_busum', 'sum_sobe', 'sum_wins');
                // Feinwertungen durchgehen
                for ($f = 1; $f <= 3; $f++) {
                    $fieldName = 'tiebr'.$f;
                    if ($this->turnier->$fieldName != 0) {
                        $sumFieldname = 'sumTiebr'.$f;
                        ?>
							</tr>
								<td class="key" nowrap="nowrap"><?php echo JText::_('TIEBR_'.$this->turnier->$fieldName); ?>:</td>
								<td><input class="inputbox" type="text" name="<?php echo $sumFieldname ?>" id="<?php echo $sumFieldname ?>" size="4" maxlength="8" value="<?php echo $this->player->$sumFieldname; ?>" /></td>
							</tr>
						<?php
                    }
                }
                ?>
				
			
			
			<?php
            } else { // nur KO-System
                ?>
				</tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('TOURNAMENT_KOSTATUS'); ?>:</td>
					<td>
						<?php
                                $kostatuslist[]	= JHtml::_('select.option', '0', JText::_('TOURNAMENT_KOSTATUS_0'), 'id', 'name');
                $kostatuslist[]	= JHtml::_('select.option', '1', JText::_('TOURNAMENT_KOSTATUS_1'), 'id', 'name');
                echo JHtml::_('select.genericlist', $kostatuslist, 'koStatus', 'class="inputbox" size="1"', 'id', 'name', $this->player->koStatus);
                ?>
					</td>
				</tr>
			<?php
            }
?>
		
		</table>
		
		</fieldset>
	</div>

	<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turplayeredit" />
	<input type="hidden" name="playerid" value="<?php echo $this->param['playerid']; ?>" />
	<input type="hidden" name="turnierid" value="<?php echo $this->player->turnier; ?>" />
	<input type="hidden" name="controller" value="turplayeredit" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
