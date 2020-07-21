<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
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
	$mail_bcc = ''; 
	foreach ($this->players as $player) {
		if (clm_core::$load->is_email($player->email)) {
			$mail_bcc .= $player->email.';'; 
		}
	} 
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<div class="clr"></div>
	
	<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'MAIL_DATA' ); ?></legend>

		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('MAIL_TO'); ?>:</td>
				<td><?php echo $this->tl->email; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('MAIL_BCC'); ?>:</td>
				<td><?php echo $mail_bcc; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('MAIL_SUBJECT'); ?>:</td>
				<td><input class="inputbox" type="text" name="mail_subj" id="mail_subj" size="50" maxlength="60" value="<?php echo $this->turnier->name; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('MAIL_BODY'); ?>:</td>
				<td>
				<textarea class="inputbox" name="mail_body" id="mail_body"  rows="8" style="width:350px;" placeholder="<?php echo JText::_('MAIL_PLACEHOLDER'); ?>" ></textarea>
				</td>
			</tr>
		</table>
		
		</fieldset>
	</div>



	<div class="clr"></div>

	<input type="hidden" name="mail_to" value="<?php echo $this->tl->email; ?>" />
	<input type="hidden" name="mail_bcc" value="<?php echo $mail_bcc; ?>" />

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turplayersmail" />
	<input type="hidden" name="turnierid" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="controller" value="turplayersmail" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
