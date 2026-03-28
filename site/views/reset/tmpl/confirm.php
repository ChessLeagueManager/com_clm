<?php 
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die; 

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>

<div class="componentheading">
	<?php echo Text::_('Account aktivieren'); ?>
</div>

<form action="index.php?option=com_clm&amp;task=confirmreset" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo Text::_('Geben Sie in dem Eingabefeld ihren Aktivierungscode ein, den Sie per Mail erhalten haben.'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="token" class="hasTip" title="<?php echo Text::_('Aktivierungscode'); ?>::<?php echo Text::_('Geben Sie hier den Code ein, den Sie per E-mail vom Administrator / Staffelleiter bekommen haben'); ?>"><?php echo Text::_('Aktivierungscode '); ?>:</label>
			</td>
			<td>
				<input id="token" name="token" type="text" class="required" size="36" />
			</td>
		</tr>
	</table>

	<button type="submit" class="validate"><?php echo Text::_('Absenden'); ?></button>
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>