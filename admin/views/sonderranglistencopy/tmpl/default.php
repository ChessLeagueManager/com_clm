<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

?>
<script language="javascript" type="text/javascript">
	
	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		Joomla.submitform( pressbutton );
	}  
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm"> 
	<table class="admintable"> 
		<tr>
			<td width="50%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_('SPECIALRANKING_COPY'); ?></legend> 
					<table class="admintable">
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="turnier"><?php echo JText::_('SPECIALRANKING_TOURNAMENT_SOURCE').'&nbsp'; ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['turnier_source'];?> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="turnier"><?php echo JText::_('SPECIALRANKING_TOURNAMENT_TARGET').'&nbsp&nbsp'; ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['turnier_target'];?> 
							</td> 
						</tr>
					</table> 
				</fieldset>

			</td>
			<td width="50%" style="vertical-align: top;">
			</td>
		</tr>
    </table> 
	<br><?php echo JText::_('SPECIALRANKING_COPY_COMMENT1'); ?>
	<br><?php echo JText::_('SPECIALRANKING_COPY_COMMENT2'); ?>
	<div class="clr"></div> 
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="sonderranglistencopy" />
<!--	<input type="hidden" name="id" value="<?php echo $this->sonderrangliste->id; ?>" />  -->
	<input type="hidden" name="controller" value="sonderranglistencopy" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
