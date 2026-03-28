<?php 
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/

defined('_JEXEC') or die; 

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$mainframe	= Factory::getApplication();

	//$token = JRequest::getVar('token');
	$token 	= clm_core::$load->request_string('token', '');
	$db	= Factory::getDBO();
	$db->setQuery('SELECT id FROM #__users WHERE block = 0 AND activation = '.$db->Quote($token));

	// Verify the token
	if (!($id = $db->loadResult()))
	{
		$this->setError(Text::_('INVALID_TOKEN'));
		echo Text::_('RESET_PASSWORD_LINK');
	}
else {
	// Push the token and user id into the session
	$mainframe->setUserState($this->_namespace.'token',	$token);
	$mainframe->setUserState($this->_namespace.'id',	$id);
?>



<div class="componentheading">
	<?php echo Text::_('RESET_PASSWORD_CHOOSE'); ?>
</div>

<form action="index.php?option=com_clm&amp;task=completereset" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo Text::_('RESET_PASSWORD_INPUT'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="password1" class="hasTip" title="<?php echo Text::_('RESET_PASSWORD_CHOOSE'); ?>::<?php echo Text::_('RESET_PASSWORD_INPUT2'); ?>"><?php echo Text::_('RESET_PASSWORD_INPUT3'); ?>:</label>
			</td>
			<td>
				<input id="password1" name="password1" type="password" class="required validate-password" />
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="password2" class="hasTip" title="<?php echo Text::_('RESET_PASSWORD_AGAIN'); ?>::<?php echo Text::_('RESET_PASSWORD_INPUT_2ND'); ?>"><?php echo Text::_('RESET_PASSWORD_RETYPE'); ?>:</label>
			</td>
			<td>
				<input id="password2" name="password2" type="password" class="required validate-password" />
			</td>
		</tr>

	</table>
<p><?php echo Text::_('RESET_PASSWORD_ADVICE1').Text::_('RESET_PASSWORD_ADVICE2'); ?></p>

	<button type="submit" class="validate"><?php echo Text::_('RESET_PASSWORD_SUBMIT'); ?></button>
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
<?php } ?>