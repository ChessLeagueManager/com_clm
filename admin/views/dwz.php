<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
class CLMViewDWZ
{
    public static function setDWZToolbar($countryversion)
    {
        $clmAccess = clm_core::$access;
        // Menubilder laden
        clm_core::$load->load_css("icons_images");

        JToolBarHelper::title(JText::_('TITLE_MEMBER'), 'clm_headmenu_mitglieder');
        if ($clmAccess->access('BE_club_edit_member') !== false) {
            JToolBarHelper::custom('spieler_delete', 'trash.png', 'trash_f2.png', JText::_('MEMBER_BUTTON_DEL'), false);
        }
        JToolBarHelper::custom('nachmeldung_delete', 'trash.png', 'trash_f2.png', JText::_('MEMBER_BUTTON_DEL_NACH'), false);
        JToolBarHelper::custom('nachmeldung', 'apply.png', 'apply_f2.png', JText::_('MEMBER_BUTTON_NACH'), false);
        JToolBarHelper::custom('daten_edit', 'apply.png', 'apply_f2.png', JText::_('MEMBER_BUTTON_EDIT'), false);
        if ($clmAccess->access('BE_database_general') === true and $countryversion == 'en') {
            JToolBarHelper::custom('player_move_to', 'apply.png', 'apply_f2.png', JText::_('MEMBER_BUTTON_MOVE_TO'), false);
            JToolBarHelper::custom('player_move_from', 'apply.png', 'apply_f2.png', JText::_('MEMBER_BUTTON_MOVE_FROM'), false);
        }
        JToolBarHelper::cancel();
        JToolBarHelper::help('screen.clm.edit');
    }

    public static function DWZ($spieler, $verein, $verein_from, $lists, $pageNav, $option)
    {
        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $countryversion = $config->countryversion;
        CLMViewDWZ::setDWZToolbar($countryversion);
        $_REQUEST['hidemainmenu'] = 1;
        JFilterOutput::objectHTMLSafe($row, ENT_QUOTES, 'extrainfo');

        $clmAccess = clm_core::$access;

        if (count($spieler) == 0) {
            $spieler[0] = new stdClass();
            $spieler[0]->Geschlecht = "";
            $spieler[0]->gesperrt = 0;
        }
        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }
        ?>

<script language="javascript" type="text/javascript">

	function edit()
	{
		var task 	= document.getElementsByName ( "task") [0];
		var pre_task 	= document.getElementsByName ( "pre_task") [0];
		task.value 	= "add";
		pre_task.value 	= "add";
		document.adminForm.submit();
	}

	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		var pre_task = document.getElementsByName ( "pre_task") [0];

		if (pre_task.value == 'add') {
			if (pressbutton == 'cancel') {
				Joomla.submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.filter_vid.value == "0") {
				alert( "<?php echo JText::_('MEMBER_JS_1', true); ?>" );
			} else if (form.filter_sid.value == "0") {
				alert( "<?php echo JText::_('MEMBER_JS_2', true); ?>" );
			} else if (form.filter_gid.value == "0") {
				alert( "<?php echo JText::_('MEMBER_JS_3', true); ?>" );
			} else {
				Joomla.submitform( pressbutton );
			}
		} else {
			Joomla.submitform( pressbutton );
		}
	}
	
	function change_vid()	{
		var form = document.adminForm;

		form.filter_mgl.value = 0;
		document.adminForm.submit();
	}

 
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table class="admintable">
		<tr><td>
		<fieldset class="adminform">
		<legend><?php echo JText::_('MEMBER_TABLE_DATA'); ?></legend>
		<?php echo $lists['vid'];  ?>&nbsp;&nbsp;
		<?php if (isset($lists['mgl'])) {
		    echo $lists['mgl'];
		}  ?>&nbsp;&nbsp;
		<?php $mainframe	= JFactory::getApplication();
        $filter_sort	= $mainframe->getUserStateFromRequest("$option.filter_sort", 'filter_sort', 0, 'string'); ?>
<!--		<select name="filter_sort" id="filter_sort" class="js-example-basic-single" size="1" onchange="document.adminForm.submit();"> -->
		<select name="filter_sort" id="filter_sort" class="<?php echo $field_search;?>" size="1" onchange="document.adminForm.submit();">
		<option value="0"  <?php if ($filter_sort == "0") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_1');?></option>
		<option value="(0+Mgl_Nr) DESC" <?php if ($filter_sort == "(0+Mgl_Nr) DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_2');?></option>
		<option value="(0+Mgl_Nr) ASC" <?php if ($filter_sort == "(0+Mgl_Nr) ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_3');?></option>
		<option value="Spielername DESC" <?php if ($filter_sort == "Spielername DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_4');?></option>
		<option value="Spielername ASC" <?php if ($filter_sort == "Spielername ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_5');?></option>
		<option value="DWZ DESC" <?php if ($filter_sort == "DWZ DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_6');?></option>
		<option value="DWZ ASC" <?php if ($filter_sort == "DWZ ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_7');?></option>
		<option value="Geburtsjahr DESC" <?php if ($filter_sort == "Geburtsjahr DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_8');?></option>
		<option value="Geburtsjahr ASC" <?php if ($filter_sort == "Geburtsjahr ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_9');?></option>
		<option value="Geschlecht DESC, Spielername ASC" <?php if ($filter_sort == "Geschlecht DESC, Spielername ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_10');?></option>
		<option value="Geschlecht ASC, Spielername ASC" <?php if ($filter_sort == "Geschlecht ASC, Spielername ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_11');?></option>
		<option value="Status DESC, Spielername ASC" <?php if ($filter_sort == "Status DESC, Spielername ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_12');?></option>
		<option value="Status ASC, Spielername ASC" <?php if ($filter_sort == "Status ASC, Spielername ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_('MEMBER_DD_13');?></option>
		</select>
		</fieldset>
		</td>
		</tr>
	</table>

<?php	 $filter_vid	= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var'); ?>
<?php	 $filter_mgl	= $mainframe->getUserStateFromRequest("$option.filter_mgl", 'filter_mgl', 0, 'int'); ?>

	<div class="width-40 fltlft">
	<fieldset class="adminform">
	<legend><?php echo JText::_('MEMBER_TABLE_1'); ?></legend>
	<?php if ($filter_vid != "0") { ?>
	<table class="admintable">
		<tr>
			<th width="4%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_2'); ?></th>
			<th width="20%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_3'); ?></th>
			<?php if ($countryversion == "de") { ?>
				<th width="4%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_4'); ?></th>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_5'); ?></th>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_51'); ?></th>
			<?php } else { ?>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_5E'); ?></th>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_51E'); ?></th>
			<?php } ?>
			<th width="3%" class="key" nowrap="nowrap"><?php echo JText::_('St'); ?></th>
			<th width="4%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_53'); ?></th>
			<th width="3%" class="key" nowrap="nowrap"><?php echo JText::_('MEMBER_TABLE_54'); ?></th>
		</tr>
	<?php	for ($x = 0; $x < count($verein);$x++) { ?>
		<tr>
			<td class="key" nowrap="nowrap">
				<?php echo $x + 1; ?>
			</td>
			<td class="key" width="25%" nowrap="nowrap">
				<?php if ($verein[$x]->gesperrt == 1) {
				    echo '<del>'.$verein[$x]->Spielername.'</del>';
				} else {
				    echo $verein[$x]->Spielername;
				} ?>
			</td>
			<?php if ($countryversion == "de") { ?>
				<td class="key" nowrap="nowrap">
					<?php echo $verein[$x]->Mgl_Nr; ?>
				</td>
				<td class="key" nowrap="nowrap">
					<?php echo $verein[$x]->DWZ." - ".$verein[$x]->DWZ_Index; ?>
				</td>
			<?php } else { ?>
				<td class="key" nowrap="nowrap">
					<?php echo $verein[$x]->DWZ; ?>
				</td>
			<?php } ?>
			<td class="key" nowrap="nowrap">
				<?php echo $verein[$x]->PKZ; ?>
			</td>
			<td class="key" nowrap="nowrap">
				<?php echo $verein[$x]->Status; ?>
			</td>
			<td class="key" nowrap="nowrap">
				<?php if ($verein[$x]->Geburtsjahr != '0000') {
				    echo $verein[$x]->Geburtsjahr;
				} ?>
			</td>
			<td class="key" nowrap="nowrap">
				<?php if ($verein[$x]->Geschlecht != 'M') {
				    echo $verein[$x]->Geschlecht;
				} ?>
			</td>
		</tr>
	<?php  } ?>
	</table>
	<?php } else {
	    echo JText::_('MEMBER_TABLE_6');
	} ?>
	</fieldset>
	</div>

	<div class="width-60 fltrt">
	<div>
	<fieldset class="adminform">
	<legend><?php echo JText::_('MEMBER_TABLE_7'); ?></legend>
	<?php echo JText::_('MEMBER_TABLE_8'); ?>

	</fieldset>
	</div>

	<div>
	<fieldset class="adminform">
	<legend><?php echo JText::_('MEMBER_TABLE_9'); ?></legend>
	<?php if ($filter_vid != "0") { ?>
	<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_('MEMBER_TABLE_10'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->Spielername ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_11'); ?></td>
		</tr>
	  <?php if ($countryversion == "de") { ?>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="mglnr"><?php echo JText::_('MEMBER_TABLE_12'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="mglnr" id="mglnr" size="7" maxlength="7" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->Mgl_Nr ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_13'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="gesperrt"><?php echo JText::_('MELDELISTE_BLOCK'); ?></label>
			</td>
			<td align="left">
			<input type="checkbox" name="gesperrt" id="gesperrt" value="1" <?php if ($spieler[0]->gesperrt == "1") {
			    echo 'checked="checked"';
			}?>>
			</td>
			<td><?php echo JText::_(''); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="PKZ"><?php echo JText::_('MEMBER_TABLE_121'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="PKZ" id="PKZ" size="10" maxlength="10" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->PKZ ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_131'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="dwz"><?php echo JText::_('MEMBER_TABLE_14'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="dwz" id="dwz" size="7" maxlength="4" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->DWZ ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_15'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="dwz_index"><?php echo JText::_('MEMBER_TABLE_16'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="dwz_index" id="dwz_index" size="7" maxlength="4" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->DWZ_Index ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_17'); ?></td>
		</tr>
	  <?php } else { ?>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="mglnr"><?php echo JText::_('MEMBER_TABLE_12E'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="mglnr" id="mglnr" size="7" maxlength="7" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->Mgl_Nr ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_13E'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="PKZ"><?php echo JText::_('MEMBER_TABLE_121E'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="PKZ" id="PKZ" size="10" maxlength="10" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->PKZ ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_131E'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="dwz"><?php echo JText::_('MEMBER_TABLE_14E'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="dwz" id="dwz" size="7" maxlength="4" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->DWZ ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_15E'); ?></td>
		</tr>
	  <?php } ?>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="FIDE_Elo"><?php echo JText::_('Elo'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="FIDE_Elo" id="FIDE_Elo" size="7" maxlength="4" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->FIDE_Elo ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('Bspl. 1355'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="FIDE_ID"><?php echo JText::_('Fide-ID'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="FIDE_ID" id="FIDE_ID" size="9" maxlength="8" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->FIDE_ID ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('Bspl. 1503015'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="FIDE_Titel"><?php echo JText::_('Fide-Titel'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="FIDE_Titel" id="FIDE_Titel" size="7" maxlength="3" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->FIDE_Titel ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('Bspl. IM'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="FIDE_Land"><?php echo JText::_('Fide-Föderation'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="FIDE_Land" id="FIDE_Land" size="7" maxlength="3" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->FIDE_Land ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('Bspl. GER'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="geschlecht"><?php echo JText::_('MEMBER_TABLE_18'); ?></label>
			</td>
			<td>
<!--				<select class="js-example-basic-single" size="1" name="geschlecht" id="geschlecht"> -->
				<select class="<?php echo $field_search;?>" size="1" name="geschlecht" id="geschlecht">
				<option value="0" <?php if (isset($filter_mgl) and $spieler[0]->Geschlecht != "M" and $spieler[0]->Geschlecht != "W") { ?> selected="selected"<?php } ?>><?php echo JText::_('MEMBER_TABLE_19'); ?></option>
				<option value="M" <?php if (isset($filter_mgl) and $spieler[0]->Geschlecht == "M") { ?> selected="selected"<?php } ?>><?php echo JText::_('MEMBER_TABLE_20'); ?></option> 
				<option value="W" <?php if (isset($filter_mgl) and $spieler[0]->Geschlecht == "W") { ?> selected="selected"<?php } ?>><?php echo JText::_('MEMBER_TABLE_21'); ?></option> 
			</td>
			<td>Bspl. W</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="geburtsjahr"><?php echo JText::_('MEMBER_TABLE_22'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="geburtsjahr" id="geburtsjahr" size="7" maxlength="4" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->Geburtsjahr ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_23'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="status"><?php echo JText::_('MEMBER_TABLE_32'); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="status" id="status" size="1" maxlength="1" <?php if (isset($filter_mgl) and $filter_mgl != "0") {?> value="<?php echo $spieler[0]->Status ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_33'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap" title="<?php echo JText::_('MEMBER_TABLE_JOIN_HINT'); ?>">
			<label for="joiningdate"><?php echo JText::_('MEMBER_TABLE_JOIN'); ?></label>
			</td>
			<td>
				<?php if (isset($spieler[0]->joiningdate) and $spieler[0]->joiningdate > '1970-01-01') {
				    $zjoiningdate = $spieler[0]->joiningdate;
				} else {
				    $zjoiningdate = '';
				} ?>
				<?php echo CLMForm::calendar($zjoiningdate, "joiningdate", "joiningdate", '%Y-%m-%d', array('class' => 'text_area', 'size' => '12',  'maxlength' => '19')); ?>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_JOIN_BEM'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap" title="<?php echo JText::_('MEMBER_TABLE_LEAVE_HINT'); ?>">
			<label for="leavingdate"><?php echo JText::_('MEMBER_TABLE_LEAVE'); ?></label>
			</td>
			<td>
				<?php if (isset($spieler[0]->leavingdate) and $spieler[0]->leavingdate > '1970-01-01') {
				    $zleavingdate = $spieler[0]->leavingdate;
				} else {
				    $zleavingdate = '';
				} ?>
				<?php echo CLMForm::calendar($zleavingdate, "leavingdate", "leavingdate", '%Y-%m-%d', array('class' => 'text_area', 'size' => '12',  'maxlength' => '19')); ?>
			</td>
			<td><?php echo JText::_('MEMBER_TABLE_LEAVE_BEM'); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo JText::_('MEMBER_TABLE_24'); ?></td>
			<td><?php echo JText::_('MEMBER_TABLE_25'); ?></td>
		</tr>

	</table>
	<?php } else {
	    echo JText::_('MEMBER_TABLE_26');
	} ?>
	</fieldset>
	</div>

	<div>
	<?php 	$zps = $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
        $spl = CLMControllerDWZ::spieler($zps); ?>
	<br>
	<fieldset class="adminform">
	<legend style="margin-bottom:0"><?php echo JText::_('MEMBER_TABLE_27'); ?></legend>
	<?php if ($filter_vid != "0") { ?>
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">
<!--	  			<select class="js-example-basic-single" style="width:300px" size="1" name="spieler" id="spieler"> -->
	  			<select class="<?php echo $field_search;?>" style="width:300px" size="1" name="spieler" id="spieler">
					<option value="0"><?php echo JText::_('MEMBER_TABLE_28'); ?></option>
				<?php for ($x = 0; $x < count($spl); $x++) { ?>
		 		<?php if ($countryversion == "de") { ?>
				  <option value="<?php echo $spl[$x]->Mgl_Nr; ?>"><?php echo $spl[$x]->Spielername; ?></option> 
		 		<?php } else { ?>
				  <option value="<?php echo $spl[$x]->PKZ; ?>"><?php echo $spl[$x]->Spielername; ?></option> 
				<?php }
		 		}	?>
	  			</select>
			</tr>
		</table>
	<?php } else {
	    echo JText::_('MEMBER_TABLE_29');
	} ?>
	</fieldset>
	</div>

<?php if ($clmAccess->access('BE_club_edit_member') === true) { ?>
	<div>
	<?php 	$zps = $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
    $spl = CLMControllerDWZ::spieler($zps); ?>
	<br>
	<fieldset class="adminform">
	<legend style="margin-bottom:0"><?php echo JText::_('MEMBER_TABLE_30'); ?></legend>
	<?php if ($filter_vid != "0") { ?>
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">
<!--	  			<select class="js-example-basic-single" style="width:300px" size="1" name="del_spieler" id="del_spieler"> -->
	  			<select class="<?php echo $field_search;?>" style="width:300px" size="1" name="del_spieler" id="del_spieler">
					<option value="0"><?php echo JText::_('MEMBER_TABLE_28'); ?></option>
				<?php for ($x = 0; $x < count($verein); $x++) { ?>
		 		<?php if ($countryversion == "de") { ?>
		 		  <option value="<?php echo $verein[$x]->Mgl_Nr; ?>"><?php echo $verein[$x]->Mgl_Nr.' - '.$verein[$x]->Spielername; ?></option> 
		 		<?php } else { ?>
		 		  <option value="<?php echo $verein[$x]->PKZ; ?>"><?php echo $verein[$x]->PKZ.' - '.$verein[$x]->Spielername; ?></option> 
				<?php }
		 		}	?>
	  			</select>
			</tr>
		</table>
	<?php } else {
	    echo JText::_('MEMBER_TABLE_29');
	} ?>
	</fieldset>
	</div>
<?php } ?>

<?php if ($clmAccess->access('BE_database_general') === true and $countryversion == "en") { ?>
	<div>
	<?php 	$zps = $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'var');
    $spl = CLMControllerDWZ::spieler($zps); ?>
	<br>
	<fieldset class="adminform">
	<legend style="margin-bottom:0"><?php echo JText::_('MEMBER_TABLE_MOVE_0'); ?></legend>
	<?php if ($filter_vid != "0") { ?>
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">
<!--	  			<select class="js-example-basic-single" size="1" name="spieler_to" id="spieler_to"> -->
	  			<select class="<?php echo $field_search;?>" size="1" name="spieler_to" id="spieler_to">
					<option value="0"><?php echo JText::_('MEMBER_TABLE_28'); ?></option>
				<?php for ($x = 0; $x < count($verein); $x++) { ?>
		 		<?php if ($countryversion == "de") { ?>
		 		  <option value="<?php echo $verein[$x]->Mgl_Nr; ?>"><?php echo $verein[$x]->Mgl_Nr.' - '.$verein[$x]->Spielername; ?></option> 
		 		<?php } else { ?>
		 		  <option value="<?php echo $verein[$x]->PKZ; ?>"><?php echo $verein[$x]->PKZ.' - '.$verein[$x]->Spielername; ?></option> 
				<?php }
		 		}	?>
	  			</select>
				</td>
				<td><?php echo JText::_('MEMBER_TABLE_MOVE_TO'); ?></td>
				<td><?php echo $lists['vid_to'];  ?></td>
			</tr>
		</table>
	<?php } else {
	    echo JText::_('MEMBER_TABLE_29');
	} ?>
	</fieldset>
	</div>
<?php } ?>
<?php if ($clmAccess->access('BE_database_general') === true and $countryversion == "en") { ?>
	<div>
	<?php 	//$zps = $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
        //$spl = CLMControllerDWZ::spieler($filter_vid_from);?>
	<br>
	<fieldset class="adminform">
	<legend style="margin-bottom:0"><?php echo JText::_('MEMBER_TABLE_MOVE_1'); ?></legend>
<!---	<?php if ($filter_vid_from != "0") { ?>  -->
		<table class="admintable">
			<tr>
				<td><?php echo JText::_('MEMBER_TABLE_MOVE_FROM'); ?></td>
				<td><?php echo $lists['vid_from'];  ?></td>
				<td><?php echo '&nbsp;&nbsp;'; ?></td>
				<td class="key" nowrap="nowrap">
<!--	  			<select class="js-example-basic-single" size="1" name="spieler_from" id="spieler_from"> -->
	  			<select class="<?php echo $field_search;?>" size="1" name="spieler_from" id="spieler_from">
					<option value="0"><?php echo JText::_('MEMBER_TABLE_28'); ?></option>
				<?php for ($x = 0; $x < count($verein_from); $x++) { ?>
		 		<?php if ($countryversion == "de") { ?>
		 		  <option value="<?php echo $verein_from[$x]->Mgl_Nr; ?>"><?php echo $verein_from[$x]->Mgl_Nr.' - '.$verein_from[$x]->Spielername; ?></option> 
		 		<?php } else { ?>
		 		  <option value="<?php echo $verein_from[$x]->PKZ; ?>"><?php echo $verein_from[$x]->PKZ.' - '.$verein_from[$x]->Spielername; ?></option> 
				<?php }
		 		}	?>
	  			</select>
				</td>
			</tr>
		</table>
<!---	<?php } else {
    echo JText::_('MEMBER_TABLE_29');
} ?> -->
	</fieldset>
	</div>
<?php } ?>

	</div>

		<div class="clr"></div>
		<?php if (isset($verein[0]->sid)) {
		    $sid = $verein[0]->sid;
		} elseif (isset($lists['saison'][0]->id)) {
		    $sid = $lists['saison'][0]->id;
		} else {
		    $sid = false;
		} ?>
		<input type="hidden" name="section" value="dwz" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="zps" value="<?php echo $filter_vid; ?>" />
<?php if (isset($filter_mgl)) { ?>
		<input type="hidden" name="mgl" value="<?php echo $filter_mgl; ?>" />
<?php } ?>
		<?php if (!($sid === false)) {
		    echo '<input type="hidden" name="sid" value="'.$sid.'" />';
		} ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="pre_task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		</form>
		<?php
    }
}
?>
