<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$swm = JRequest::getVar('swm_file', '', 'post', 'string');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<table width="100%" class="admintable"> 
		<tr>
			<td width="50%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_HINTS_TEXT' ); ?>
				</fieldset>
			</td>
			<td width="50%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['saisons'] ?></td>
							<td width="50%"><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_SEASONS_TEXT' ); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->lists['turniere'] ?></td>
							<td width="50%"><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_TOURNAMENT_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>		
	</table>
	
	<input type="hidden" name="swm" value="<?php echo $swm; ?>" />
	
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swmturnier" />
	<input type="hidden" name="controller" value="swmturnier" />
	<input type="hidden" name="swm_file" value="<?php echo $swm; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>