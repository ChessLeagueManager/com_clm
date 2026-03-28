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
	<?php echo Text::_('FORGOT_YOUR_PASSWORD'); ?>
</div>

<form action="index.php?option=com_user&amp;task=requestreset" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo Text::_('RESET_PASSWORD_REQUEST_DESCRIPTION'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="email" class="hasTip" title="<?php echo Text::_('RESET_PASSWORD_EMAIL_TIP_TITLE'); ?>::<?php echo Text::_('RESET_PASSWORD_EMAIL_TIP_TEXT'); ?>"><?php echo Text::_('Email Address'); ?>:</label>
			</td>
			<td>
				<input id="email" name="email" type="text" class="required validate-email" />
			</td>
		</tr>
	</table>

	<button type="submit" class="validate"><?php echo Text::_('Submit'); ?></button>
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>

<br><br><br>
<hr>
<?php
$Dir = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_clm';
$data = JApplicationHelper::parseXMLInstallFile($Dir.DS.'clm.xml');
?>
<div style="float:left; text-align:left; padding-left:1%">CLM <?php echo $data[version];?></div>
<div style=" text-align:right; padding-right:1%"><label for="name" class="hasTip" title="<?php echo Text::_('CLM_OS'); ?>"><?php echo Text::_('CLM_HELP')?> - <a href="http://www.fishpoke.de"><?php echo Text::_('CLM_PROJEKT')?></a></label></div>