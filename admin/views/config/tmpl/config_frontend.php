<?php
defined('_JEXEC') or die('Restricted access');

	$config			= JComponentHelper::getParams( 'com_clm' );
	
    ?>
    
    <table class="noshow">
      <tr>
        <td width="50%" valign="top">
	    <fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_CLUB' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_vereine'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_TEAMS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_msch'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_CLUBS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_vereinsliste'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_ROUNDS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_runde'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_MELDUNG' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_meldelisten'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>


      <td width="50%" valign="top">
	<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_SUBMENU' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_submenu'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_FIXHEADER' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_fixheader'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_DISPLAY' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_display'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_MAPS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                       	<?php echo $this->params->render( 'params', 'googlemaps'); ?> <a href="http://code.google.com/intl/de-DE/apis/maps/signup.html" target="_blank"><?php echo JText::_( 'CONFIG_FRONTEND_MAPS_GETKEY' ); ?></a>
                        </td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_CHARTS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                       	<?php echo $this->params->render( 'params', 'charts'); ?>
                        </td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>
      </tr>
    </table>