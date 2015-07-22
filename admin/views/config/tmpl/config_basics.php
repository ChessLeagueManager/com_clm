<?php
defined('_JEXEC') or die('Restricted access');

	$config			= JComponentHelper::getParams( 'com_clm' );
	
    ?>
    
    <table class="noshow">
      <tr>
        <td width="50%" valign="top">
	    <fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_GENEREL' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params' ); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>


      <td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_EMAIL' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'email' ); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
        
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_DATABASE' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'database' ); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		
		</td>
      </tr>
    </table>