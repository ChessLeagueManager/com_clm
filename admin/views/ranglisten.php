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
class CLMViewRanglisten
{
    public static function setRanglistenToolbar($check)
    {
        JToolBarHelper::title(JText::_('TITLE_RANGLISTE'), 'generic.png');
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        $clmAccess = clm_core::$access;
        if ($clmAccess->access('BE_club_edit_ranking') === true) {
            JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', JText::_('COPY'));
        }
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        JToolBarHelper::addNew();
        JToolBarHelper::help('screen.clm.info');
    }

    public static function Ranglisten(&$rows, &$lists, &$pageNav, $option)
    {
        $db		= JFactory::getDBO();
        $user 		= JFactory::getUser();
        $jid 		= $user->get('id');
        $sql = " SELECT usertype FROM #__clm_user "
            ." WHERE jid =".$jid
            ." AND published = 1 "
        ;
        $db->setQuery($sql);
        $check = $db->loadObjectList();
        CLMViewRanglisten::setRanglistenToolbar($check);

        //Ordering allowed ?
        $ordering = ($lists['order'] == 'a.ordering');

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");

        ?>
		<form action="index.php?option=com_clm&section=ranglisten" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_('Filter'); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
                // eigenes Dropdown Menue
                    echo $lists['tgid'];
        echo "&nbsp;&nbsp;&nbsp;"."&nbsp;&nbsp;&nbsp;"."&nbsp;&nbsp;&nbsp;"."&nbsp;&nbsp;&nbsp;"."&nbsp;&nbsp;&nbsp;"."&nbsp;&nbsp;&nbsp;".$lists['sid'];
        echo "&nbsp;&nbsp;&nbsp;".$lists['gid'];
        echo "&nbsp;&nbsp;&nbsp;".$lists['vid'];
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
						<?php echo JHtml::_('grid.sort', 'RANGLISTE_VEREIN', 'c.vname', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort', 'RANGLISTE_GRUPPE', 'a.Meldelschluss', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'RANGLISTE_AUTOR', 'a.rang', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort', 'RANGLISTE_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="6%">
						<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order']); ?>
						<?php echo JHtml::_('grid.order', $rows); ?>
					</th>

					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="13">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
        $k = 0;
        for ($i = 0, $n = count($rows); $i < $n; $i++) {
            $row = &$rows[$i];

            $link 		= JRoute::_('index.php?option=com_clm&section=ranglisten&task=edit&id='. $row->id);

            $checked 	= JHtml::_('grid.checkedout', $row, $i);
            //				$published 	= JHtml::_('grid.published', $row, $i );
            $published 	= JHtml::_('jgrid.published', $row->published, $i);

            ?>
				<tr class="<?php echo 'row'. $k; ?>">

					<td align="center">
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>

					<td>
						<?php echo $checked; ?>
					</td>

					<td>

								<span class="editlinktip hasTip" title="<?php echo JText::_('RANGLISTE_EDIT').' ';?>: <?php echo $row->vname; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->vname; ?></a></span>

					</td>

					<td align="center">
						<?php echo $row->gname;?>
					</td>
					<td align="center">
						<?php if ($row->rang == 0) { ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php } else { ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php echo $row->saison;?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
	<td class="order">
	<?php $disabled = $ordering ? '' : 'disabled="disabled"'; ?>
	<input type="text" name="order" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
					</td>

					<td align="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
            $k = 1 - $k;
        }
        ?>
			</tbody>
			</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</form>
		<?php
    }

    public static function setRanglisteToolbar($vname)
    {

        $cid = clm_core::$load->request_array_int('cid');
        if (clm_core::$load->request_string('task') == 'edit') {
            $text = JText::_('Edit');
        } else {
            $text = JText::_('New');
        }
        JToolBarHelper::title(JText::_('RANGLISTE')." $vname : [ ". $text.' ]');
        JToolBarHelper::custom('sortieren', 'back.png', 'edit_f2.png', 'REORDER', false);
        JToolBarHelper::custom('pruefen', 'back.png', 'edit_f2.png', 'RANGLISTE_CHECK', false);
        JToolBarHelper::custom('neu_laden', 'back.png', 'edit_f2.png', 'RANGLISTE_LOAD', false);
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();
        JToolBarHelper::help('screen.clm.edit');
    }

    public static function Rangliste($spieler, &$row, &$lists, $option, $jid, $vname, $sg_vname, $gname, $sname, $cid, $exist, $sg_exist, $pa_exist, $count, $gid_exist)
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        CLMViewRanglisten::setRanglisteToolbar($vname);

        JFilterOutput::objectHTMLSafe($row, ENT_QUOTES, 'extrainfo');
        $anz_sgp = $lists['anz_sgp'];
        clm_core::$load->load_js("ranglisten");
        $s_error = 0;
        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");
        ?>

<?php if ($exist and clm_core::$load->request_string('task') == "add") { ?>

	<div class="col width-100">
	<fieldset class="adminform">
	<legend><?php echo JText::_('RANGLISTE_TIP'); ?></legend>

	<h1><?php echo JText::_('RANGLISTE_TIP_LINE1'); ?></h1>
	<h2><?php echo JText::_('RANGLISTE_TIP_LINE2'); ?></h2>
	<br>

	</fieldset>
	</div>

<?php $s_error = 1;
} ?>
<?php if ($pa_exist and clm_core::$load->request_string('task') == "add") { ?>

	<div class="col width-100">
	<fieldset class="adminform">
	<legend><?php echo JText::_('RANGLISTE_TIP'); ?></legend>

	<h1><?php echo JText::_('RANGLISTE_TIP_PA_LINE1'); ?></h1>
	<h2><?php echo JText::_('RANGLISTE_TIP_PA_LINE2'); ?></h2>
	<br>

	</fieldset>
	</div>

<?php $s_error = 1;
} ?>
<?php if ($sg_exist and clm_core::$load->request_string('task') == "add") { ?>

	<div class="col width-100">
	<fieldset class="adminform">
	<legend><?php echo JText::_('RANGLISTE_TIP'); ?></legend>

	<h1><?php echo JText::_('RANGLISTE_TIP_SG_LINE1'); ?></h1>
	<h2><?php echo JText::_('RANGLISTE_TIP_SG_LINE2'); ?></h2>
	<br>

	</fieldset>
	</div>

<?php $s_error = 1;
} ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div>
		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_('RANGLISTE_DETAILS'); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" nowrap="nowrap"><label for="filter_vid"><?php echo JText::_('RANGLISTE_VEREIN').' : '; ?></label>
			</td>
			<td>
			<?php if (clm_core::$load->request_string('task') == 'edit') {
			    echo $vname;
			} else {
			    echo $lists['vid'];
			} ?>
			</td>
		</tr>

		<?php if ($anz_sgp > 0) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sg_vid"><?php echo JText::_('RANGLISTE_VEREIN2').' : '; ?></label>
			</td>
			<td>
			<?php if (clm_core::$load->request_string('task') == 'edit') {
			    if (isset($sg_vname[1])) {
			        echo $sg_vname[1];
			    } else {
			        echo '';
			    }
			} else {
			    echo $lists['sg_vid1'];
			} ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($anz_sgp > 1) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sg_vid"><?php echo JText::_('RANGLISTE_VEREIN2').' : '; ?></label>
			</td>
			<td>
			<?php if (clm_core::$load->request_string('task') == 'edit') {
			    if (isset($sg_vname[2])) {
			        echo $sg_vname[2];
			    } else {
			        echo '';
			    }
			} else {
			    echo $lists['sg_vid2'];
			} ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($anz_sgp > 2) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sg_vid"><?php echo JText::_('RANGLISTE_VEREIN2').' : '; ?></label>
			</td>
			<td>
			<?php if (clm_core::$load->request_string('task') == 'edit') {
			    if (isset($sg_vname[3])) {
			        echo $sg_vname[3];
			    } else {
			        echo '';
			    }
			} else {
			    echo $lists['sg_vid3'];
			} ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($anz_sgp > 3) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sg_vid"><?php echo JText::_('RANGLISTE_VEREIN2').' : '; ?></label>
			</td>
			<td>
			<?php if (clm_core::$load->request_string('task') == 'edit') {
			    if (isset($sg_vname[4])) {
			        echo $sg_vname[4];
			    } else {
			        echo '';
			    }
			} else {
			    echo $lists['sg_vid4'];
			} ?>
			</td>
		</tr>
		<?php } ?>

		<tr>
			<td class="key" nowrap="nowrap"><label for="filter_sid"><?php echo JText::_('RANGLISTE_SAISON').' : '; ?></label>
			</td>
			<td>
			<?php if (clm_core::$load->request_string('task') == 'edit') {
			    echo $sname;
			} else {
			    echo $lists['sid'];
			} ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="filter_gid"><?php echo JText::_('RANGLISTE_GRUPPE').' : '; ?></label>
			</td>
			<td>
			<?php	if (clm_core::$load->request_string('task') == 'edit') {
			    echo $gname;
			} else {
			    echo $lists['gruppe'];
			} ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_('JPUBLISHED').' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>

		</table>
		</fieldset>
		</div>

 <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_('REMARKS'); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_('REMARKS_PUBLIC'); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="1" style="width:90%"><?php echo str_replace('&', '&amp;', $row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>
	<table class="adminlist">
	<legend><?php echo JText::_('REMARKS_INTERNAL'); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="1" style="width:90%"><?php echo str_replace('&', '&amp;', $row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>
</div>

<?php if (($s_error == 0) and (!$exist and clm_core::$load->request_string('task') == "add") or (clm_core::$load->request_string('task') == "edit")) {
    $mainframe	= JFactory::getApplication();

    $filter_vid	= $mainframe->getUserStateFromRequest("$option.filter_vid", 'filter_vid', 0, 'int');
    $filter_sg_vid	= $mainframe->getUserStateFromRequest("$option.filter_sg_vid", 'filter_sg_vid', 0, 'int');
    $filter_sid	= $mainframe->getUserStateFromRequest("$option.filter_sid", 'filter_sid', 0, 'int');
    $filter_gid	= $mainframe->getUserStateFromRequest("$option.filter_gid", 'filter_gid', 0, 'int');

    if (clm_core::$load->request_string('task') == "add" and (!$spieler or !$gid_exist or !$filter_gid)) { ?>
	<br><br><br><br><br><br><br><br><br><br>
	<fieldset class="adminform">
	<legend><?php echo JText::_('RANGLISTE_TIP'); ?></legend>
	<?php if (!$gid_exist and $filter_sid and $filter_vid) { ?>
	<h2><?php echo JText::_('RANGLISTE_TIP_LINE11'); ?></h2>
	<br>
	<?php } else { ?>
	<?php if (!$spieler and $filter_vid and $filter_sid and $filter_gid) { ?>
	<h2><?php echo JText::_('RANGLISTE_TIP_LINE21'); ?></h2>
	<?php } else { ?>
	<h2><?php echo JText::_('RANGLISTE_TIP_LINE31'); ?></h2>
	<br>
	<?php } ?>
	</fieldset>
	
 <?php }
	} else { ?>
<br><br><br><br><br><br><br><br><br><br>
<div>
<div class="width-50 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_('RANGLISTE_PLATZ').' 1 - '.(1 + intval(((count($spieler)) / 2))); ?></legend>
	
	<table class="admintable">

	<tr>
		<td width="5%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_M_NR'); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_RANG'); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_NAME'); ?></td>
		<td width="9%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_ZPSM'); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_MGL_NR'); ?></td>
		<td width="11%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_PKZ'); ?></td>
		<td width="3%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_STATUS'); ?></td>
		<td colspan="2" width="10%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_DWZ'); ?></td>
		<td width="8" class="key" nowrap="nowrap"><?php echo JText::_('MELDELISTE_BLOCK'); ?></td>
	</tr>

<?php

if (clm_core::$load->request_string('task') == 'edit') {

    $rang	= array();
    $cnt	= 0;
    for ($x = (count($spieler) - $count); $x < count($spieler); $x++) {
        $rang_x	= array($cnt => $x);
        $rang	= $rang + $rang_x;
        $cnt++;
    }
    for ($x = 0; $x < (count($spieler) - $count); $x++) {
        $rang_x	= array($cnt => $x);
        $rang	= $rang + $rang_x;
        $cnt++;
    }

    for ($x = 0; $x < (1 + intval((count($spieler)) / 2)); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->PKZ; ?>" />
	<input type="hidden" name="ZPSM<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->ZPS; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?>" />
	<input type="hidden" name="BLOCK_A<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->gesperrt; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="2" maxLength="2" value="<?php echo $spieler[$rang[$x]]->man_nr; ?>" onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="4" maxLength="4" value="<?php echo $spieler[$rang[$x]]->Rang; ?>" onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" name="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php if ($spieler[$rang[$x]]->gesperrt != 1) {
		    echo $spieler[$rang[$x]]->Spielername;
		} else {
		    echo '<del>'.$spieler[$rang[$x]]->Spielername.'</del>';
		}?></td>
	<td id="ZPSM<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->ZPS; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->PKZ; ?></td>
	<td id="Status<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Status; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ_Index; ?></td>
	<td align="center">
		<input type="checkbox" name="check<?php echo $x; ?>" id="check<?php echo $x; ?>" value="1" <?php if ($spieler[$rang[$x]]->gesperrt == "1") {
		    echo 'checked="checked"';
		}?>>
	</td>
	</tr>

<?php }
    } else {

        for ($x = 0; $x < (1 + intval((count($spieler)) / 2)); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$x]->PKZ; ?>" />
	<input type="hidden" name="ZPSM<?php echo $x; ?>" value="<?php echo $spieler[$x]->ZPS; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$x]->Mgl_Nr; ?>" />
	<input type="hidden" name="BLOCK_A<?php echo $x; ?>" value="<?php echo $spieler[$x]->gesperrt; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="2" maxLength="2" <?php if (isset($spieler[$x]->man_nr)) { ?> value="<?php echo $spieler[$x]->man_nr; ?>"<?php } ?> onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
		<input type="text" name="RA<?php echo $x ?>" size="4" maxLength="4" <?php if (isset($spieler[$x]->Rang)) { ?> value="<?php echo $spieler[$x]->Rang; ?>" <?php } ?> onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" name="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php if ($spieler[$x]->gesperrt != 1) {
		    echo $spieler[$x]->Spielername;
		} else {
		    echo '<del>'.$spieler[$x]->Spielername.'</del>';
		}?></td>
	<td id="ZPSM<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->ZPS; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->PKZ; ?></td>
	<td id="Status<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Status; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ_Index; ?></td>
	<td align="center">
		<input type="checkbox" name="check<?php echo $x; ?>" id="check<?php echo $x; ?>" value="1" <?php if ($spieler[$x]->gesperrt == "1") {
		    echo 'checked="checked"';
		}?>>
	</td>
	</tr>

<?php }
        } ?>
	</table>
  </fieldset>
  </div>


<div class="width-50 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_('RANGLISTE_PLATZ').' '.intval(((count($spieler)) / 2) + 2)." - ".count($spieler); ?></legend>

	<table class="admintable">

	<tr>
		<td width="5%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_M_NR'); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_RANG'); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_NAME'); ?></td>
		<td width="9%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_ZPSM'); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_MGL_NR'); ?></td>
		<td width="11%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_PKZ'); ?></td>
		<td width="3%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_STATUS'); ?></td>
		<td colspan="2" width="10%" class="key" nowrap="nowrap"><?php echo JText::_('RANGLISTE_DWZ'); ?></td>
		<td width="8" class="key" nowrap="nowrap"><?php echo JText::_('MELDELISTE_BLOCK'); ?></td>
	</tr>
	
<?php
if (clm_core::$load->request_string('task') == 'edit') {

    for ($x = (1 + intval((count($spieler)) / 2)); $x < count($spieler); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->PKZ; ?>" />
	<input type="hidden" name="ZPSM<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->ZPS; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?>" />
	<input type="hidden" name="BLOCK_A<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->gesperrt; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="2" maxLength="2" value="<?php echo $spieler[$rang[$x]]->man_nr; ?>" onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="4" maxLength="4" value="<?php echo $spieler[$rang[$x]]->Rang; ?>" onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php if ($spieler[$rang[$x]]->gesperrt != 1) {
		    echo $spieler[$rang[$x]]->Spielername;
		} else {
		    echo '<del>'.$spieler[$rang[$x]]->Spielername.'</del>';
		}?></td>
	<td id="ZPSM<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->ZPS; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->PKZ; ?></td>
	<td id="Status<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Status; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ_Index; ?></td>
	<td align="center">
		<input type="checkbox" name="check<?php echo $x; ?>" id="check<?php echo $x; ?>" value="1" <?php if ($spieler[$rang[$x]]->gesperrt == "1") {
		    echo 'checked="checked"';
		}?>>
	</td>
	</tr>

<?php }
    } else {

        for ($x = (1 + intval((count($spieler)) / 2)); $x < count($spieler); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$x]->PKZ; ?>" />
	<input type="hidden" name="ZPSM<?php echo $x; ?>" value="<?php echo $spieler[$x]->ZPS; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$x]->Mgl_Nr; ?>" />
	<input type="hidden" name="BLOCK_A<?php echo $x; ?>" value="<?php echo $spieler[$x]->gesperrt; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="2" maxLength="2" <?php if (isset($spieler[$x]->man_nr)) { ?> value="<?php echo $spieler[$x]->man_nr; ?>"<?php } ?> onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
		<input type="text" name="RA<?php echo $x ?>" size="4" maxLength="4" <?php if (isset($spieler[$x]->Rang)) { ?> value="<?php echo $spieler[$x]->Rang; ?>" <?php } ?> onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php if ($spieler[$x]->gesperrt != 1) {
		    echo $spieler[$x]->Spielername;
		} else {
		    echo '<del>'.$spieler[$x]->Spielername.'</del>';
		}?></td>
	<td id="ZPSM<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->ZPS; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->PKZ; ?></td>
	<td id="Status<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Status; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ_Index; ?></td>
	<td align="center">
		<input type="checkbox" name="check<?php echo $x; ?>" id="check<?php echo $x; ?>" value="1" <?php if ($spieler[$x]->gesperrt == "1") {
		    echo 'checked="checked"';
		}?>>
	</td>
	</tr>

<?php }
        } ?>
	</table>

  </fieldset>
  </div>
  </div>

<?php }
	} ?>
		<div class="clr"></div>
		<input type="hidden" name="section" value="ranglisten" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />

		<input type="hidden" name="count" value="<?php echo count($spieler); ?>" />
<!--		<input type="hidden" name="zps" value="<?php if (isset($spieler[0])) {
    echo $spieler[0]->ZPS;
} ?>" /> -->
		<input type="hidden" name="zps" value="<?php echo $row->zps; ?>" />
		<input type="hidden" name="sid" value="<?php if (isset($spieler[0])) {
		    echo $spieler[0]->sid;
		} ?>" />
		<input type="hidden" name="gid" value="<?php echo $row->gid; ?>" />
		<input type="hidden" name="exist" value="<?php echo $exist; ?>" />

		<input type="hidden" name="pre_task" value="<?php echo clm_core::$load->request_string('task'); ?>" />
		<input type="hidden" name="task" value="edit" />

		<?php echo JHtml::_('form.token'); ?>
		</form>
		<?php
    }
}
?>
