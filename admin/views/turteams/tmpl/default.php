<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

	$turParams = new clm_class_params($this->turnier->params);
	$param_typeaccount = $turParams->get('typeAccount', 0);

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<div class="clr"></div>
	
	<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'EDIT_TEAMS' ); ?></legend>

		<table class="admintable">
		<tr>
			<th width="10">
				<?php echo JText::_( 'TEAM_NR' ); ?>
			</th>
			<th width="250">
				<?php echo JText::_( 'TEAM' ); ?>
			</th>
		</tr>
			<?php 
		    $i = 0;
			foreach ($this->teams as $team) { 
			  $i++; ?>
			  <tr>
			    <td>
				  <input type="text" name="tln_nr<?php echo $i; ?>" id="tln_nr<?php echo $i; ?>" value="<?php echo $team->tln_nr;  ?>" size="1" maxlength="4" style="width:100%;">
				</td><td>
				  <input type="text" name="name<?php echo $i; ?>" id="name<?php echo $i; ?>" value="<?php echo $team->name;  ?>" size="50" maxlength="100" style="width:100%;">
				</td>
			  </tr>
			<?php } 
			for ($ii=($i+1); $ii<($i+6); $ii++ ) { ?>
			  <tr>
			    <td>
				  <input type="text" name="tln_nr<?php echo $ii; ?>" id="tln_nr<?php echo $ii; ?>" value="<?php echo '';  ?>" size="1" maxlength="4" style="width:100%;">
				</td><td>
				  <input type="text" name="name<?php echo $ii; ?>" id="name<?php echo $ii; ?>" value="<?php echo '';  ?>" size="50" maxlength="100" style="width:100%;">
				</td>
			  </tr>
			<?php } ?>
		</table>
		
		</fieldset>
	</div>



	<div class="clr"></div>

	<input type="hidden" name="tid" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="sid" value="<?php echo $this->turnier->sid; ?>" />

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turteams" />
	<input type="hidden" name="turnierid" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="controller" value="turteams" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
