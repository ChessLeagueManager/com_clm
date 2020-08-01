<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleague.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

		$turParams = new clm_class_params($this->turnier->params);
		$param_typeaccount = $turParams->get('typeAccount', 0);
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="clr"></div>
	
	<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'PLAYER_DATA' ); ?></legend>
<?php //echo "<br>registrationid:".$this->param['registrationid']; ?>
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">* <?php echo JText::_('PLAYER_NAME'); ?> (<?php echo JText::_('LASTNAME_FIRSTNAME'); ?>):</td>
				<td><input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $this->registration->name.','.$this->registration->vorname; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_BIRTH_YEAR'); ?>:</td>
				<td><input class="inputbox" type="text" name="birthYear" id="birthYear" size="4" maxlength="4" value="<?php echo $this->registration->birthYear; ?>"/></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('CLUB'); ?>:</td>
				<td><input class="inputbox" type="text" name="club" id="club" size="50" maxlength="60" value="<?php echo $this->registration->club; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_EMAIL'); ?>:</td>
				<td><input class="inputbox" type="text" name="email" id="email" size="50" maxlength="60" value="<?php echo $this->registration->email; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_TEL_NO'); ?>:</td>
				<td><input class="inputbox" type="text" name="tel_no" id="tel_no" size="30" maxlength="30" value="<?php echo $this->registration->tel_no; ?>" /></td>
			</tr>
			<?php if ($param_typeaccount > '0') { ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_ACCOUNT_'.$param_typeaccount); ?>:</td>
				<td><input class="inputbox" type="text" name="account" id="account" size="50" maxlength="50" value="<?php echo $this->registration->account; ?>" /></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('RATING'); ?>:</td>
				<td><input class="inputbox" type="text" name="dwz" id="dwz" size="4" maxlength="4" value="<?php if ($this->registration->dwz != 0) echo $this->registration->dwz; else echo ""; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('RATING_INDEX'); ?>:</td>
				<td><input class="inputbox" type="text" name="dwz_I0" id="dwz_I0" size="4" maxlength="4" value="<?php if ($this->registration->dwz_I0 != 0) echo $this->registration->dwz_I0; else echo ""; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('FIDE_ELO'); ?>:</td>
				<td><input class="inputbox" type="text" name="elo" id="elo" size="4" maxlength="4" value="<?php if ($this->registration->elo != 0) echo $this->registration->elo; else echo ""; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_TITLE'); ?>:</td>
				<td><input class="inputbox" type="text" name="titel" id="titel" size="3" maxlength="3" value="<?php echo $this->registration->titel; ?>" /></td>
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
						$optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name' );
					}
					echo JHtml::_('select.genericlist', $optionlist, 'geschlecht', 'class="inputbox"', 'id', 'name', $this->registration->geschlecht);
					?>
				</td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_FIDE_ID'); ?>:</td>
				<td><input class="inputbox" type="text" name="FIDEid" id="FIDEid" size="8" maxlength="8" value="<?php if ($this->registration->FIDEid != 0) echo $this->registration->FIDEid; else echo ""; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_FIDE_CCO'); ?>:</td>
				<td><input class="inputbox" type="text" name="FIDEcco" id="FIDEcco" size="3" maxlength="3" value="<?php echo $this->registration->FIDEcco; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_MGLNR'); ?>:</td>
				<td><input class="inputbox" type="text" name="mgl_nr" id="mgl_nr" size="5" maxlength="5" value="<?php if ($this->registration->mgl_nr != 0) echo $this->registration->mgl_nr; else echo ""; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_PKZ'); ?>:</td>
				<td><input class="inputbox" type="text" name="PKZ" id="PKZ" size="9" maxlength="9" value="<?php echo $this->registration->PKZ; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_ZPS'); ?>:</td>
				<td><input class="inputbox" type="text" name="zps" id="zps" size="5" maxlength="5" value="<?php if ($this->registration->zps != '0') echo $this->registration->zps; else echo ""; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_COMMENT'); ?>:</td>
				<td width="100%" valign="top">
					<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$this->registration->comment);?></textarea>
				</td>
			</tr>
		</table>
		
		</fieldset>
	</div>

	<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turregistrationedit" />
	<input type="hidden" name="registrationid" value="<?php echo $this->param['registrationid']; ?>" />
	<input type="hidden" name="turnierid" value="<?php echo $this->registration->tid; ?>" />
	<input type="hidden" name="snrmax" value="<?php echo $this->snrmax; ?>" />
	<input type="hidden" name="controller" value="turregistrationedit" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
