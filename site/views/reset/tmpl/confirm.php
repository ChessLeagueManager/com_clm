<?php defined('_JEXEC') or die; ?>

<div class="componentheading">
	<?php echo JText::_('Account aktivieren'); ?>
</div>

<form action="index.php?option=com_clm&amp;task=confirmreset" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo JText::_('Geben Sie in dem Eingabefeld ihren Aktivierungscode ein, den Sie per Mail erhalten haben.'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="token" class="hasTip" title="<?php echo JText::_('Aktivierungscode'); ?>::<?php echo JText::_('Geben Sie hier den Code ein, den Sie per E-mail vom Administrator / Staffelleiter bekommen haben'); ?>"><?php echo JText::_('Aktivierungscode '); ?>:</label>
			</td>
			<td>
				<input id="token" name="token" type="text" class="required" size="36" />
			</td>
		</tr>
	</table>

	<button type="submit" class="validate"><?php echo JText::_('Absenden'); ?></button>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>