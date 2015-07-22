<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
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
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'apply') {
				//task.value 	= "add";
				submitform( pressbutton );
				//alert( " ADD !!");
				return;
			}
 			else {	submitform( pressbutton ); }
		}

		</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="width-70 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'FROM_RATING_DATABASE' ); ?></legend>
		
				<table>
				<tr>
					<td align="left" width="40%">
						<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->param['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
					</td>
					
					<td align="center" width="20%">
						<?php echo CLMForm::selectDWZRanges('filter_dwz', $this->param['dwz'], TRUE); ?>
					</td>
					
					<td align="right" width="40%">
		
					<?php	// eigenes Dropdown Menue
						echo CLMForm::selectAssociation('filter_verband', $this->param['verband'], TRUE);
						echo "<br /><br />".CLMForm::selectVereinZPSinAssoc('filter_vid', $this->param['vid'], $this->param['verband'], TRUE);
					?>
					</td>
				</tr>
				</table>
		
					<table class="adminlist">
					<thead>
						<tr>
							<th width="10">
								#
							</th>
							<th width="10">
								<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
							</th>
							<th class="title">
								<?php echo JHtml::_('grid.sort',   JText::_('PLAYER_NAME'), 'Spielername', $this->param['order_Dir'], $this->param['order'] ); ?>
							</th>
							<th width="5%">
								<?php echo JHtml::_('grid.sort',   JText::_('PLAYER_TITLE'), 'FIDE_Titel', $this->param['order_Dir'], $this->param['order'] ); ?>
							</th>
							<th width="5%">
								<?php echo JHtml::_('grid.sort',   JText::_('CLM_NUMBER_ABB'), 'snr', $this->param['order_Dir'], $this->param['order'] ); ?>
							</th>
							<th width="7%">
								<?php echo JHtml::_('grid.sort',   JText::_('RATING'), 'DWZ', $this->param['order_Dir'], $this->param['order'] ); ?>
							</th>
							<th width="7%">
								<?php echo JHtml::_('grid.sort',   JText::_('FIDE_ELO'), 'FIDE_Elo', $this->param['order_Dir'], $this->param['order'] ); ?>
							</th>
							<th width="28%">
								<?php echo JHtml::_('grid.sort',   JText::_('CLUB'), 'Vereinname', $this->param['order_Dir'], $this->param['order'] ); ?>
							</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="13">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					$count=0;
					
					foreach ($this->playerlist as $i => $value) {
						$row = &$value;
		
						?>
						<tr class="<?php echo 'row'. $k; ?>">
							<td align="center">
								<?php echo $this->pagination->getRowOffset( $i ); ?>
							</td>
							<td align="center">
								<?php 
									if ($row->snr == "" ) { 
								?>
									<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $row->ZPS.$row->Mgl_Nr; ?>" onclick="isChecked(this.checked);" />
								<?php 
									} else {
										echo '-';
									}
								?>
							</td>
							<td align="left">
								<?php echo $row->Spielername;?>
							</td>
							<td align="center">
								<?php echo $row->FIDE_Titel;?>
							</td>
							<td align="center">
								<?php echo $row->snr;?>
							</td>
							<td align="center">
								<?php 
								if ($row->DWZ > 0) {
									echo $row->DWZ;
								} else {
									echo "-";
								}
								?>
							</td>
							<td align="center">
								<?php 
								if ($row->FIDE_Elo > 0) {
									echo $row->FIDE_Elo;
								} else {
									echo "-";
								}
								?>
							</td>
							<td align="left">
								<?php echo $row->Vereinname; ?>
							</td>
		
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
					</tbody>
					</table>
		</fieldset>
	</div>
	
	<div class="width-30 fltrt">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'ENTER_PLAYERS_BY_NAME' ); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key" nowrap="nowrap">* <?php echo JText::_('PLAYER_NAME'); ?> (<?php echo JText::_('LASTNAME_FIRSTNAME'); ?>):</td>
					<td><input class="inputbox" type="text" name="name" id="name" size="20" maxlength="60" /></td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('CLUB'); ?>:</td>
					<td><input class="inputbox" type="text" name="verein" id="verein" size="20" maxlength="60"  /></td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('RATING'); ?>:</td>
					<td><input class="inputbox" type="text" name="natrating" id="natrating" size="4" maxlength="4" /></td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('FIDE_ELO'); ?>:</td>
					<td><input class="inputbox" type="text" name="fideelo" id="fideelo" size="4" maxlength="4" /></td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_TITLE'); ?>:</td>
					<td><input class="inputbox" type="text" name="titel" id="titel" size="3" maxlength="3" /></td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_SEX'); ?>:</td>
					<td class="paramlist_value">
						<?php
						$options = array();
						$options[''] = '';
						$options['M'] = JText::_('OPTION_SEX_M');
						$options['W'] = JText::_('OPTION_SEX_W');
						$optionlist = array();
						foreach ($options as $key => $val) {
							$optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name' );
						}
						echo JHtml::_('select.genericlist', $optionlist, 'geschlecht', 'class="inputbox"', 'id', 'name', '');
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_BIRTH_YEAR'); ?>:</td>
					<td><input class="inputbox" type="text" name="birthYear" id="birthYear" size="4" maxlength="4" /></td>
				</tr>
			</table>
		* <? echo JText::_('MANDATORY_ITEM'); ?>
		</fieldset>
	</div>



	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turplayerform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="turplayerform" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
	<input type="hidden" name="add_nz" value="<?php echo $this->param['add_nz']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
