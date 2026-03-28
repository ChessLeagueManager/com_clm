<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

	$config			= JComponentHelper::getParams( 'com_clm' );
	
    ?>
    
    <table class="noshow">
      <tr>
        <td width="50%" valign="top">
	    <fieldset class="adminform">
			<legend><?php echo Text::_( 'CONFIG_GENEREL' ); ?></legend>
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
			<legend><?php echo Text::_( 'CONFIG_EMAIL' ); ?></legend>
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
			<legend><?php echo Text::_( 'CONFIG_DATABASE' ); ?></legend>
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