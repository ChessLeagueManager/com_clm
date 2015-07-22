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

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="col width-50">
		<fieldset class="adminform">
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('CLM_NUMBER'); ?>:</td>
				<td><input class="inputbox" type="text" name="snr" id="snr" size="3" maxlength="3" value="<?php echo $this->player->snr; ?>" /></td>
			</tr>
		</table>
		
		</fieldset>
	</div>
	
	<div class="clr"></div>
	
	<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'PLAYER_DATA' ); ?></legend>

		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">* <?php echo JText::_('PLAYER_NAME'); ?> (<?php echo JText::_('LASTNAME_FIRSTNAME'); ?>):</td>
				<td><input class="inputbox" type="text" name="name" id="name" size="20" maxlength="60" value="<?php echo $this->player->name; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('CLUB'); ?>:</td>
				<td><input class="inputbox" type="text" name="verein" id="verein" size="20" maxlength="60" value="<?php echo $this->player->verein; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('TWZ'); ?>:</td>
				<td><input class="inputbox" type="text" name="twz" id="twz" size="4" maxlength="4" value="<?php echo $this->player->twz; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('RATING'); ?>:</td>
				<td><input class="inputbox" type="text" name="start_dwz" id="start_dwz" size="4" maxlength="4" value="<?php echo $this->player->start_dwz; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('RATING_INDEX'); ?>:</td>
				<td><input class="inputbox" type="text" name="start_I0" id="start_I0" size="4" maxlength="4" value="<?php echo $this->player->start_I0; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('FIDE_ELO'); ?>:</td>
				<td><input class="inputbox" type="text" name="FIDEelo" id="FIDEelo" size="4" maxlength="4" value="<?php echo $this->player->FIDEelo; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_TITLE'); ?>:</td>
				<td><input class="inputbox" type="text" name="titel" id="titel" size="3" maxlength="3" value="<?php echo $this->player->titel; ?>" /></td>
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
					echo JHtml::_('select.genericlist', $optionlist, 'geschlecht', 'class="inputbox"', 'id', 'name', $this->player->geschlecht);
					?>
				</td>
			</tr>
			<tr>
				<td class="key" nowrap="nowrap"><?php echo JText::_('PLAYER_BIRTH_YEAR'); ?>:</td>
				<td><input class="inputbox" type="text" name="birthYear" id="birthYear" size="4" maxlength="4" value="<?php echo $this->player->birthYear; ?>"/></td>
			</tr>
		</table>
		
		</fieldset>
	</div>


	<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RESULT' ); ?></legend>

		<table class="admintable">
			
			<?php
			if ($this->turnier->typ != 3) { // nicht KO-System
			?>
			
				</tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('POINTS'); ?>:</td>
					<td><input class="inputbox" type="text" name="sum_punkte" id="sum_punkte" size="4" maxlength="4" value="<?php echo $this->player->sum_punkte; ?>" /></td>
				</tr>
				
				<?php
				$fwFieldNames = array(1 => 'sum_bhlz', 'sum_busum', 'sum_sobe', 'sum_wins');
				// Feinwertungen durchgehen
				for ($f=1; $f<=3; $f++) {
					$fieldName = 'tiebr'.$f;
					if ($this->turnier->$fieldName != 0) {
						$sumFieldname = 'sumTiebr'.$f;
						?>
							</tr>
								<td class="key" nowrap="nowrap"><?php echo JText::_('TIEBR_'.$this->turnier->$fieldName); ?>:</td>
								<td><input class="inputbox" type="text" name="<?php echo $sumFieldname ?>" id="<?php echo $sumFieldname ?>" size="4" maxlength="8" value="<?php echo $this->player->$sumFieldname; ?>" /></td>
							</tr>
						<?php
					}
				}
				?>
				
			
			
			<?php
			} else { // nur KO-System
			?>
				</tr>
					<td class="key" nowrap="nowrap"><?php echo JText::_('TOURNAMENT_KOSTATUS'); ?>:</td>
					<td>
						<?php 
							$kostatuslist[]	= JHtml::_('select.option',  '0', JText::_('TOURNAMENT_KOSTATUS_0'), 'id', 'name' );
							$kostatuslist[]	= JHtml::_('select.option',  '1', JText::_('TOURNAMENT_KOSTATUS_1'), 'id', 'name' );
							echo JHtml::_('select.genericlist', $kostatuslist, 'koStatus', 'class="inputbox" size="1"', 'id', 'name', $this->player->koStatus );
						?>
					</td>
				</tr>
			<?php
			}
			?>
		
		</table>
		
		</fieldset>
	</div>

	<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turplayeredit" />
	<input type="hidden" name="playerid" value="<?php echo $this->param['playerid']; ?>" />
	<input type="hidden" name="turnierid" value="<?php echo $this->player->turnier; ?>" />
	<input type="hidden" name="controller" value="turplayeredit" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
