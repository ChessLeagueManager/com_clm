<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$turParams = new clm_class_params($this->turnier->params);
if (is_null($this->turnier->bemerkungen)) {
    $this->turnier->bemerkungen = '';
}
if (is_null($this->turnier->bem_int)) {
    $this->turnier->bem_int = '';
}

//CLM parameter auslesen
$clm_config = clm_core::$db->config();
if ($clm_config->field_search == 1) {
    $field_search = "js-example-basic-single";
} else {
    $field_search = "inputbox";
}
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

  <div class="width-60 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_('JDETAILS'); ?></legend>
      <table class="paramlist admintable">

	<tr>
		<td width="30%" class="paramlist_key">
			<label for="name"><?php echo JText::_('TOURNAMENT_NAME'); ?>:</label>
		</td>
		<td class="paramlist_value">
			<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" value="<?php echo $this->turnier->name; ?>" />
		</td>
	</tr>
	
	<?php
    if (isset($this->form['catidAlltime'])) {
        ?>
		<tr>
			<td width="30%" class="paramlist_key">
				<label for="category">
					<?php echo JText::_('CATEGORY_ALLTIME'); ?>:
				</label>
			</td>
			<td class="paramlist_value">
				<?php echo $this->form['catidAlltime']; ?>
			</td>
		</tr>
	<?php
    }
?>
	
	<?php
if (isset($this->form['catidEdition'])) {
    ?>
		<tr>
			<td width="30%" class="paramlist_key">
				<label for="category">
					<?php echo JText::_('CATEGORY_EDITION'); ?>:
				</label>
			</td>
			<td class="paramlist_value">
				<?php echo $this->form['catidEdition']; ?>
			</td>
		</tr>
	<?php
}
?>
	
	<tr>
		<td width="30%" class="paramlist_key">
			<label for="saison">
				<?php echo JText::_('SEASON'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['sid']; ?>
		</td>
	</tr>
	
	
	<tr>
		<td width="30%" class="paramlist_key">
			<label for="dateStart">
				<?php echo JText::_('TOURNAMENT_DAYSTART'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
        echo CLMForm::calendar($this->turnier->dateStart, 'dateStart', 'dateStart', '%Y-%m-%d', array('class' => 'text_area', 'size' => '32',  'maxlength' => '19')); ?>
		</td>
	</tr>
	
	<tr>
		<td width="30%" class="paramlist_key">
			<label for="dateEnd">
				<?php echo JText::_('TOURNAMENT_DAYEND'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
        echo CLMForm::calendar($this->turnier->dateEnd, 'dateEnd', 'dateEnd', '%Y-%m-%d', array('class' => 'text_area', 'size' => '32',  'maxlength' => '19')); ?>
		</td>
	</tr>
	
	<tr>
		<td width="30%" class="paramlist_key">
			<label for="modus">
				<?php echo JText::_('MODUS'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
        // nur, wenn Runden noch nicht erstellt
        if (IS_NULL($this->turnier->rnd) or $this->turnier->rnd == 0) {
            echo $this->form['modus'];
        } else {
            echo JText::_('MODUS_TYP_'.$this->turnier->typ)." (".JText::_('ROUNDS_CREATED')."!)";
            echo CLMForm::hidden('typ', $this->turnier->typ); // damit JavaScript funktioniert
        }
?>
		</td>
	</tr>
	
	<tr>
		<td width="30%" class="paramlist_key">
			<label for="runden">
				<?php echo JText::_('ROUNDS_COUNT'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<span id="formRoundscountValue">
			<?php
// nur, wenn Runden noch nicht erstellt
if (IS_NULL($this->turnier->rnd) or $this->turnier->rnd == '0') {
    if ($this->turnier->typ != 2 and $this->turnier->typ != 3 and $this->turnier->typ != 5) {
        echo '<input class="inputbox" type="text" name="runden" id="runden" size="10" maxlength="5" value="'.$this->turnier->runden.'" />';
    } else {
        echo CLMForm::hidden('runden', $this->turnier->runden); // damit JavaScript funktioniert
    }
} else {
    echo $this->turnier->runden;
    echo CLMForm::hidden('runden', $this->turnier->runden); // damit JavaScript funktioniert
}
?>
			</span>
			<span id="formRoundscountText">
			<?php
// nur, wenn Runden noch nicht erstellt
if (IS_NULL($this->turnier->rnd) or $this->turnier->rnd == '0') {
    if ($this->turnier->typ == 2 or $this->turnier->typ == 3 or $this->turnier->typ == 5) {
        echo $this->turnier->runden." (".JText::_('ROUNDS_COUNT_GENERATED').")";
    }
}
?>
			</span>
		</td>
	</tr>


	<tr>
		<td width="30%" class="paramlist_key">
			<label for="dg">
				<?php echo JText::_('STAGE_COUNT'); ?>:
			</label>
		</td>
		<td class="paramlist_value" id="formStagecount">
			<?php
// nur, wenn Runden noch nicht erstellt
if (IS_NULL($this->turnier->rnd) or $this->turnier->rnd == 0) {
    if ($this->turnier->typ != 1 and $this->turnier->typ != 3 and $this->turnier->typ != 5) {
        echo $this->form['dg'];
    } else {
        echo '-';
        echo CLMForm::hidden('dg', $this->turnier->dg); // damit JavaScript funktioniert
    }
} else {
    echo $this->turnier->dg;
    echo CLMForm::hidden('dg', $this->turnier->dg); // damit JavaScript funktioniert
}
?>
		</td>
	</tr>

	<tr>
		<td width="30%" class="paramlist_key">
			<label for="teil">
				<?php echo JText::_('PARTICIPANT_COUNT'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
if (IS_NULL($this->turnier->rnd) or $this->turnier->rnd == 0) {
    ?>
				<input class="inputbox" onchange="showFormRoundscount();" type="text" name="teil" id="teil" size="10" maxlength="5" value="<?php echo $this->turnier->teil; ?>" />
			<?php
} else {
    echo $this->turnier->teil;
    echo CLMForm::hidden('teil', $this->turnier->teil); // damit JavaScript funktioniert
}
?>
		</td>
	</tr>

	<tr>
		<td width="30%" class="paramlist_key">
			<label for="tiebreakers">
				<?php echo JText::_('TIEBREAKERS'); ?>:
			</label>
		</td>
		<td class="paramlist_value" id="formTiebreakers">
			<?php
if ($this->turnier->typ != 3 and $this->turnier->typ != 5) {
    echo $this->form['tiebr1'].'<br>';
    echo $this->form['tiebr2'].'<br>';
    echo $this->form['tiebr3'];
} else {
    echo '-';
}
?>
		</td>
		<td class="paramlist_value">
			<label for="teamranking">
				<?php echo JText::_('TEAMRANKING'); ?>:
			</label>
			<br>
			<?php
$options = array();
$options[0] = JText::_('TEAMRKG_0');
$options[2] = JText::_('TEAMRKG_2');
$options[3] = JText::_('TEAMRKG_3');
$options[4] = JText::_('TEAMRKG_4');
$options[99] = JText::_('TEAMRKG_99');
$optionlist = array();
foreach ($options as $key => $val) {
    $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
}
//			echo JHtml::_('select.genericlist', $optionlist, 'params[teamranking]', 'class="js-example-basic-single" size="1" style="width:250px"', 'id', 'name', $turParams->get('teamranking', 0));
echo JHtml::_('select.genericlist', $optionlist, 'params[teamranking]', 'class="'.$field_search.'" size="1" style="width:250px"', 'id', 'name', $turParams->get('teamranking', 0));
?>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key" title="<?php echo JText::_('OPTION_TIEBREAKERSFIDECORRECT_HINT');?>" >
			<?php echo JText::_('OPTION_TIEBREAKERSFIDECORRECT'); ?>:
		</td>
		<td class="paramlist_value"><fieldset class="radio">
			<?php echo JHtml::_('select.booleanlist', 'params[optionTiebreakersFideCorrect]', 'class="inputbox"', $turParams->get('optionTiebreakersFideCorrect', 0)); ?>
		</fieldset></td>
		<td class="paramlist_key" title="<?php echo JText::_('OPTION_ELO_ANALYSIS_HINT');?>" >
			<?php echo JText::_('OPTION_ELO_ANALYSIS'); ?>:
		</td>
		<td class="paramlist_value"><fieldset class="radio">
			<?php echo JHtml::_('select.booleanlist', 'params[optionEloAnalysis]', 'class="inputbox"', $turParams->get('optionEloAnalysis', 0)); ?>
		</fieldset></td>
	</tr>
	<tr>
		<td class="paramlist_key" title="<?php echo JText::_('OPTION_50PERCENTRULE_HINT');?>" >
			<?php echo JText::_('OPTION_50PERCENTRULE'); ?>:
		</td>
		<td class="paramlist_value"><fieldset class="radio">
			<?php echo JHtml::_('select.booleanlist', 'params[option50PercentRule]', 'class="inputbox"', $turParams->get('option50PercentRule', 0)); ?>
		</fieldset></td>
	</tr>

	<tr>
		<td nowrap="nowrap">
			<label for="punkte_modus"><?php echo JText::_('LEAGUE_MATCH_VALUATION'); ?></label>
		</td>
		<td><?php echo JText::_('LEAGUE_VALUATION_1');?>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="sieg" id="sieg" size="4" maxlength="4" value="<?php echo $this->turnier->sieg ?>" /><br>
			<?php echo JText::_('LEAGUE_VALUATION_2');?>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="remis" id="remis" size="4" maxlength="4" value="<?php echo $this->turnier->remis ?>" /><br>
			<?php echo JText::_('LEAGUE_VALUATION_3');?>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="nieder" id="nieder" size="4" maxlength="4" value="<?php echo $this->turnier->nieder ?>" /></td>
		<td><?php echo JText::_('LEAGUE_VALUATION_1S');?>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="siegs" id="siegs" size="4" maxlength="4" value="<?php echo $this->turnier->siegs ?>" /><br>
			<?php echo JText::_('LEAGUE_VALUATION_2S');?>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="remiss" id="remiss" size="4" maxlength="4" value="<?php echo $this->turnier->remiss ?>" /><br>
			<?php echo JText::_('LEAGUE_VALUATION_3K');?>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="niederk" id="niederk" size="4" maxlength="4" value="<?php echo $this->turnier->niederk ?>" /></td>
	</tr>

	<tr>
		<td width="30%" class="paramlist_key">
			<?php echo JText::_('OPTION_USEASTWZ'); ?>:
		</td>
		<td class="paramlist_value">
			<?php
$options = array();
if ($turParams->get('useAsTWZ', 0) == 8) {
    $options[8] = JText::_('OPTION_USEASTWZ_8');
} else {
    $options[0] = JText::_('OPTION_USEASTWZ_0');
    $options[1] = JText::_('OPTION_USEASTWZ_1');
    $options[2] = JText::_('OPTION_USEASTWZ_2');
    $options[3] = JText::_('OPTION_USEASTWZ_3');
    $options[4] = JText::_('OPTION_USEASTWZ_4');
}
$optionlist = array();
foreach ($options as $key => $val) {
    $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
}
//			echo JHtml::_('select.genericlist', $optionlist, 'params[useAsTWZ]', 'class="js-example-basic-single" size="1" style="width:350px"', 'id', 'name', $turParams->get('useAsTWZ', 0));
echo JHtml::_('select.genericlist', $optionlist, 'params[useAsTWZ]', 'class="'.$field_search.'" size="1" style="width:350px"', 'id', 'name', $turParams->get('useAsTWZ', 0));
?>
		</td>
	</tr>
	<tr>
		<td width="30%" class="paramlist_key">
			<?php echo JText::_('OPTION_AUTODWZ'); ?>:
		</td>
		<td class="paramlist_value">
			<?php
$options = array();
$options[0] = JText::_('OPTION_AUTODWZ_0');
$options[1] = JText::_('OPTION_AUTODWZ_1');
$options[2] = JText::_('OPTION_AUTODWZ_2');
$optionlist = array();
foreach ($options as $key => $val) {
    $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
}
//			echo JHtml::_('select.genericlist', $optionlist, 'params[autoDWZ]', 'class="js-example-basic-single" size="1" style="width:350px"', 'id', 'name', $turParams->get('autoDWZ', 0));
echo JHtml::_('select.genericlist', $optionlist, 'params[autoDWZ]', 'class="'.$field_search.'" size="1" style="width:350px"', 'id', 'name', $turParams->get('autoDWZ', 0));
?>
		</td>
	</tr>

	</table>
  </fieldset>
  
  <fieldset class="adminform">
  <legend><?php echo JText::_('QUALIFICATION'); ?></legend>
      <table class="paramlist admintable">
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_UP'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiUp]" id="params[qualiUp]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiUp', 0); ?>" />
				</td>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_UP_POSSIBLE'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiUpPoss]" id="params[qualiUpPoss]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiUpPoss', 0); ?>" />
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_DOWN'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiDown]" id="params[qualiDown]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiDown', 0); ?>" />
				</td>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_DOWN_POSSIBILE'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiDownPoss]" id="params[qualiDownPoss]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiDownPoss', 0); ?>" />
				</td>
			</tr>
  
  
		</table>
	</fieldset>
  
  
  <fieldset class="adminform">
   <legend><?php echo "<br>".JText::_('PERSONALIA'); ?></legend>
      <table class="paramlist admintable">
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="tl">
				<?php echo JText::_('TOURNAMENT_DIRECTOR'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['tl']; ?>
		</td>
	</tr>
	<tr>
	<td width="40%" class="paramlist_key">
			<label for="torg">
				<?php echo JText::_('TOURNAMENT_ORGANIZER'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['torg']; ?>
		</td>
	</tr>

	<tr>
		<td width="40%" class="paramlist_key">
			<label for="bezirkVer">
				<?php echo JText::_('DISTRICT_EVENT'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<fieldset class="radio">
				<?php echo $this->form['bezirkTur']; ?>
			</fieldset></td>
		</tr>

	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="vereinZPS">
				<?php echo JText::_('ORGANIZER'); ?>/<?php echo JText::_('HOSTER'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['vereinZPS']; ?>
		</td>
	</tr>
	
	<tr>
		<td width="30%" class="paramlist_key">
			<label for="dateRegistration">
				<?php echo JText::_('TOURNAMENT_DATE_REGISTRATION'); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
echo CLMForm::calendar($this->turnier->dateRegistration, 'dateRegistration', 'dateRegistration', '%Y-%m-%d', array('class' => 'text_area', 'size' => '32',  'maxlength' => '19')); ?>
		</td>
	</tr>
	
	<tr>
		<td width="30%" class="paramlist_key">
			<?php echo JText::_('OPTION_REGISTRATION'); ?>:
		</td>
		<td class="paramlist_value">
			<?php
$options = array();
$options[0] = JText::_('OPTION_REGISTRATION_0');
$options[5] = JText::_('OPTION_REGISTRATION_5');
//$options[2] = JText::_('OPTION_AUTODWZ_2');
$optionlist = array();
foreach ($options as $key => $val) {
    $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
}
//			echo JHtml::_('select.genericlist', $optionlist, 'params[typeRegistration]', 'class="js-example-basic-single" size="1" style="width:350px"', 'id', 'name', $turParams->get('typeRegistration', 0));
echo JHtml::_('select.genericlist', $optionlist, 'params[typeRegistration]', 'class="'.$field_search.'" size="1" style="width:350px"', 'id', 'name', $turParams->get('typeRegistration', 0));
?>
		</td>
	</tr>

	<tr>
		<td width="30%" class="paramlist_key">
			<?php echo JText::_('OPTION_ACCOUNT'); ?>:
		</td>
		<td class="paramlist_value">
			<?php
$options = array();
$options[0] = JText::_('OPTION_ACCOUNT_0');
$options[1] = JText::_('OPTION_ACCOUNT_1');
//$options[2] = JText::_('OPTION_ACCOUNT_2');
$optionlist = array();
foreach ($options as $key => $val) {
    $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
}
//			echo JHtml::_('select.genericlist', $optionlist, 'params[typeAccount]', 'class="js-example-basic-single" size="1" style="width:350px"', 'id', 'name', $turParams->get('typeAccount', 0));
echo JHtml::_('select.genericlist', $optionlist, 'params[typeAccount]', 'class="'.$field_search.'" size="1" style="width:350px"', 'id', 'name', $turParams->get('typeAccount', 0));
?>
		</td>
	</tr>

			</table>
		</fieldset>
	<?php // if (JComponentHelper::isInstalled ( 'com_clm_pairing' ) AND $this->turnier->typ == 1) {?>
	<?php if (JPluginHelper::isEnabled('xxx', 'clm_pairing_files') and $this->turnier->typ == 1) { ?>  
	<fieldset class="adminform">
	<legend><?php echo "<br>".JText::_('DRAWING'); ?></legend>
      <table class="paramlist admintable">
	
		<tr>
			<td width="30%" class="paramlist_key">
				<?php echo JText::_('DRAWING_MODE'); ?>:
			</td>
			<td class="paramlist_value">
				<?php
    $options = array();
	    $options[0] = JText::_('DRAWING_MODE_0');
	    $options[1] = JText::_('DRAWING_MODE_1');
	    //				$options[2] = JText::_('DRAWING_MODE_2');
	    $optionlist = array();
	    foreach ($options as $key => $val) {
	        $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
	    }
	    echo JHtml::_('select.genericlist', $optionlist, 'params[drawing_mode]', 'class="inputbox"', 'id', 'name', $turParams->get('drawing_mode', 0)); ?>
			</td>
		</tr>
	
	  </table>
	</fieldset>
	<?php } ?>
	
	</div>

	<div class="width-40 fltrt">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('STATUS'); ?></legend>
		
			<table class="paramlist admintable">
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="published">
							<?php echo JText::_('JPUBLISHED'); ?>:
						</label>
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo $this->form['published']; ?>
					</fieldset></td>
				</tr>
			</table>
	
		</fieldset>
	</div>
	
	
	<div class="width-40 fltrt">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('DISPLAY_OPTIONS'); ?></legend>
		
			<table class="paramlist admintable">
				
				<?php
	    if (isset($this->form['catidAlltime']) or isset($this->form['catidEdition'])) {
	        ?>
					<tr>
						<td width="40%" class="paramlist_key">
							<?php echo JText::_('OPTION_ADDCATTONAME'); ?>:
						</td>
						<td class="paramlist_value">
							<?php
	                    $options = array();
	        $options[0] = JText::_('OPTION_ADDCATTONAME_0');
	        $options[1] = JText::_('OPTION_ADDCATTONAME_1');
	        $options[2] = JText::_('OPTION_ADDCATTONAME_2');
	        $optionlist = array();
	        foreach ($options as $key => $val) {
	            $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
	        }
	        echo JHtml::_('select.genericlist', $optionlist, 'params[addCatToName]', 'class="inputbox"', 'id', 'name', $turParams->get('addCatToName', 0));
	        ?>
						</td>
					</tr>
				<?php
	    }
?>
				
				<tr>
					<td width="40%" class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYROUNDDATE'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayRoundDate]', 'class="inputbox"', $turParams->get('displayRoundDate', 1)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERSNR'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerSnr]', 'class="inputbox"', $turParams->get('displayPlayerSnr', 1)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERTITLE'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerTitle]', 'class="inputbox"', $turParams->get('displayPlayerTitle', 1)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERCLUB'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerClub]', 'class="inputbox"', $turParams->get('displayPlayerClub', 1)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERRATING'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerRating]', 'class="inputbox"', $turParams->get('displayPlayerRating', 0)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERELO'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerElo]', 'class="inputbox"', $turParams->get('displayPlayerElo', 0)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERFIDELINK'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerFideLink]', 'class="inputbox"', $turParams->get('displayPlayerFideLink', 0)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERFEDERATION'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayPlayerFederation]', 'class="inputbox"', $turParams->get('displayPlayerFederation', 0)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYTLOK'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[displayTlOK]', 'class="inputbox"', $turParams->get('displayTlOK', $this->params['tourn_showtlok'])); ?>
					</fieldset></td>
				</tr>
			</table>
	
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('ADDITIONAL_OPTIONS'); ?></legend>
		
			<table class="paramlist admintable">
	
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PGNINPUT'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[pgnInput]', 'class="inputbox"', $turParams->get('pgnInput', 1)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PGNPUBLIC'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[pgnPublic]', 'class="inputbox"', $turParams->get('pgnPublic', 1)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PGNDOWNLOAD'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[pgnDownload]', 'class="inputbox"', $turParams->get('pgnDownload', 1)); ?>
					</fieldset></td>
				</tr>
			
			</table>
	
		</fieldset>
	</div>
	
	<div class="width-40 fltrt">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('PLAYER_VIEW_DISPLAY_OPTIONS'); ?></legend>
		
			<table class="paramlist admintable">
	
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PLAYER_VIEW_DISPLAY_SEX'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[playerViewDisplaySex]', 'class="inputbox"', $turParams->get('playerViewDisplaySex', 0)); ?>
					</fieldset></td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PLAYER_VIEW_DISPLAY_BIRTH_YEAR'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[playerViewDisplayBirthYear]', 'class="inputbox"', $turParams->get('playerViewDisplayBirthYear', 0)); ?>
					</fieldset></td>
				</tr>
				
			</table>
	
		</fieldset>
	</div>
	
	<div class="width-40 fltrt">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_('JOOMGALLERY_OPTIONS'); ?></legend>
		
			<table class="paramlist admintable">
	
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_JOOMGALLERY_DISPLAY_PLAYER_PHOTOS'); ?>:
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo JHtml::_('select.booleanlist', 'params[joomGalleryDisplayPlayerPhotos]', 'class="inputbox"', $turParams->get('joomGalleryDisplayPlayerPhotos', 0)); ?>
					</fieldset></td>
				</tr>
			
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_JOOMGALLERY_CATEGORY_ID'); ?>:
					</td>
					<td class="paramlist_value">
						<input class="inputbox" type="text" name="params[joomGalleryCatId]" id="params[joomGalleryCatId]" value="<?php echo $turParams->get('joomGalleryCatId', ''); ?>" />
					</td>
				</tr>
                
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_JOOMGALLERY_PHOTOS_WIDTH'); ?>:
					</td>
					<td class="paramlist_value">
						<input class="inputbox" type="text" name="params[joomGalleryPhotosWidth]" id="params[joomGalleryPhotosWidth]" value="<?php echo $turParams->get('joomGalleryPhotosWidth', ''); ?>" />
					</td>
				</tr>
			
			
			</table>
	
		</fieldset>
	</div>

	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<br>
			<legend><?php echo JText::_('REMARKS'); ?></legend>
		
			<table class="paramlist admintable">
				<legend><?php echo JText::_('REMARKS_PUBLIC'); ?></legend>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&', '&amp;', $this->turnier->bemerkungen);?></textarea>
					</td>
				</tr>
			</table>
		
			<table class="adminlist">
				<legend><?php echo JText::_('REMARKS_INTERNAL'); ?></legend>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&', '&amp;', $this->turnier->bem_int);?></textarea>
					</td>
				</tr>
			</table>
		
		</fieldset>
	</div>


<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turform" />
	<input type="hidden" name="id" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="controller" value="turform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="rnd" value="<?php echo $this->turnier->rnd; ?>" />
	<input type="hidden" name="ordering" value="<?php echo $this->turnier->ordering; ?>" />
	<input type="hidden" name="params[inofDWZ]" id="params[inofDWZ]" value="<?php echo $turParams->get('inofDWZ', '0'); ?>" />
	<input type="hidden" name="params[import_source]" id="params[import_source]" value="<?php echo $turParams->get('import_source', '0'); ?>" />
	<?php echo JHtml::_('form.token'); ?>

</form>
