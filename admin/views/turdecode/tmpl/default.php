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
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	$trial_and_error = $config->trial_and_error;

?>
<div id="clm"><div class="clm">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<table>
		<tr>
			<td width="75%">
				<?php if ($trial_and_error == 1) {
					echo JText::_( 'DECODE_ANZEIGEZEILEN' );				
				?>
				  <input type="text" name="filter_numberlines" id="filter_numberlines" value="<?php echo $this->param['numberlines']; ?>" size="4" maxlength="5" title="<?php echo JText::_( 'DECODE_HINT' ); ?>" onchange="document.adminForm.submit();">
				<?php } ?>
			</td>
			<td nowrap="nowrap" width="25%">
				<?php
					echo JText::_( 'DECODE_SELECTION' );
					if ($countryversion =="de") {
						echo "&nbsp;&nbsp;&nbsp;".$this->lists['verband'];
					}
					echo "&nbsp;&nbsp;&nbsp;".$this->lists['verein'];
				?>
			</td>
		</tr>
	</table>

	

	<table class="adminlist">
	<thead>
		<tr>
			<th width="2">
				<?php echo JText::_( '' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'DECODE_NICKNAME' ); ?>
			</th>
			<th width="200">
				<?php echo JText::_( 'DECODE_NAME' ); ?>
			</th>
			<th width="200">
				<?php echo JText::_( 'DECODE_CLUB' ); ?>
			</th>
			<th width="350">
				<?php echo JText::_( 'DECODE_SELECT' ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
			<?php 
		    $i = 0;
			foreach ($this->turplayers as $player) { 
			  $i++; 
			  if (isset($this->a_names[$player->oname])) {
				  $nname = $this->a_names[$player->oname]->nname;
			  } else { 
				  $nname = '';
			  } 
			  if (isset($this->a_names[$player->oname])) {
				  $verein = $this->a_names[$player->oname]->verein;
			  } else { 
				  $verein = '';
			  } 
			  if ($nname == '' AND $player->name != $player->oname) {
				  $nname = $player->name;
				  $verein = $player->verein;
			  } 
			  
				  ?>
			  <tr>
			    <td>
				  <?php echo $i; ?>
				</td>
			    <td>
				  <input type="text" name="oname<?php echo $i; ?>" id="oname<?php echo $i; ?>" value="<?php echo $player->oname;  ?>" size="30" maxlength="50" style="width:100%;">
				<td>
				  <input type="text" name="nname<?php echo $i; ?>" id="nname<?php echo $i; ?>" value="<?php echo $nname;  ?>" size="50" maxlength="150" style="width:100%;" title="<?php echo JText::_( 'DECODE_HINT' ); ?>">
				</td><td>
				  <input type="text" name="verein<?php echo $i; ?>" id="verein<?php echo $i; ?>" value="<?php echo $verein;  ?>" size="50" maxlength="150" style="width:100%;" title="<?php echo JText::_( 'DECODE_HINT' ); ?>">
				</td>
				</td><td class="paramlist_value">
					<select id="tname<?php echo $i; ?>" name="tname<?php echo $i; ?>" class="inputbox" size="1" title="<?php echo JText::_( 'DECODE_HINT' ); ?>" style="width: 350px !important; min-width: 350px; max-width: 350px;">
						<option value="<?php echo '-1'; ?>"><?php echo '-- Spieler auswählen --' ?></option> 
						<option value="<?php echo '-2'; ?>"><?php echo '-- Zuordnung löschen --' ?></option> 
						<?php for ($x=0; $x < count($this->spielernamen); $x++) { ?>
						<option value="<?php echo $this->spielernamen[$x]->Spielername.' - '.$this->spielernamen[$x]->verein; ?>"><?php echo $this->spielernamen[$x]->Spielername.' - '.$this->spielernamen[$x]->verein; ?></option> 
						<?php } ?>
					</select>
				</td>
			  </tr>
			<?php } ?>

			</tbody>
			</table>

	<input type="hidden" name="tid" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="sid" value="<?php echo $this->turnier->sid; ?>" />

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turdecode" />
	<input type="hidden" name="turnierid" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="controller" value="turdecode" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
</div></div>
