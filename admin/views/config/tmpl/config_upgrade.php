<?php
defined('_JEXEC') or die('Restricted access');

	$config			= JComponentHelper::getParams( 'com_clm' );
	
    ?>
    
    <table class="noshow">
      <tr>
        <td width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_UPGRADE_GLOBALHINT' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
						<td valign="top">
							<? echo nl2br(JText::_('CONFIG_UPGRADE_GLOBALHINTTEXT')); ?>
						</td>
					</tr>
				</tbody>
				</table>
			</fieldset>
			</td>
      </tr>
      <tr>
        <td width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_UPGRADE' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
						<td valign="top">
							<?php echo $this->params->render( 'params', 'update' ); ?>
						</td>
					</tr>
				</tbody>
				</table>
			</fieldset>
			</td>
      </tr>
    </table>