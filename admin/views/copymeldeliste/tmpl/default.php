<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

	if (count($this->mannschaften) == 0) {
		$app	= JFactory::getApplication();
		$msg = 'Leider stehen keine Meldelisten des Vereins zum Kopieren zur Verfügung';
		$app->enqueueMessage( $msg, 'warning' );
		$app->redirect( 'index.php?option=com_clm&section=mannschaften' );
	  }

	echo '<br><h3>Kopie-Auswahl &nbsp; &nbsp; &nbsp; <span style="font-weight:normal;"></span> &nbsp; &nbsp; &nbsp; <span style="font-weight:normal;">Mannschaft:</span> '.$this->mannschaft->name.'</h3><br>';

?>

	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table>
			<tr><th class="anfang">
				<td class="anfang"><?php echo JText::_('Liga'); ?></td>
				<td class="anfang"><?php echo '&nbsp&nbsp'; ?></td>
				<td class="anfang"><?php echo JText::_('Mannschaft'); ?></td>
			</th></tr>
			<?php $i = 0;
				foreach ($this->mannschaften as $m01) { 
					$i++; ?>
				<tr><td style="text-align: center;"><input type="radio" id="<?php echo 'team'.($i); ?>" name="<?php echo 'teamid'; ?>" value="<?php echo $m01->id; ?>"></td>
				<td><?php echo $m01->lname; ?></td>
				<td><?php echo '&nbsp&nbsp'; ?></td>
				<td><?php echo $m01->name; ?></td>
				</tr>
			<?php } ?>
		</table>

		<div class="clr"></div>

		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="view" value="copymeldeliste" />
		<input type="hidden" name="id" value="<?php echo $this->mannschaft->id; ?>" />
		<input type="hidden" name="controller" value="copymeldeliste" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>

	</form>
