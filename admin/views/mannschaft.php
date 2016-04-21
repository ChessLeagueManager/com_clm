<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewMannschaften
{
public static function setMannschaftenToolbar()
	{
	$clmAccess = clm_core::$access;
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

		JToolBarHelper::title( JText::_( 'TITLE_MANNSCHAFT' ), 'clm_headmenu_mannschaften.png' );
	if($clmAccess->access('BE_team_registration_list') !== false) {
		JToolBarHelper::custom('delete_meldeliste','send.png','send_f2.png', JText::_( 'MANNSCHAFT_BUTTON_ML_DEL'), false);
		JToolBarHelper::custom('meldeliste','send.png','send_f2.png', JText::_( 'MANNSCHAFT_BUTTON_ML_UPD'), false);
		JToolBarHelper::custom('spielfrei','cancel.png','cancel_f2.png', JText::_( 'MANNSCHAFT_BUTTON_SPIELFREI'), false);
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', JText::_( 'MANNSCHAFT_BUTTON_COPY' )); 
		JToolBarHelper::deleteList();
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
		}
		JToolBarHelper::help( 'screen.clm.mannschaft' );
	}

public static function mannschaften( $rows, $lists, $pageNav, $option )
	{
		$mainframe	= JFactory::getApplication();
		CLMViewMannschaften::setMannschaftenToolbar();
		$user =JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHtml::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=mannschaften" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['lid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['vid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['state'];
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
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_NR', 'a.man_nr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_LIGA', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_T_NR', 'a.tln_nr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_MF', 'a.mf', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_MELDELISTE', 'a.liste', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="22%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_VEREIN', 'b.Vereinname', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort',   'MANNSCHAFT_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="6%">
						<?php echo JHtml::_('grid.sort',   'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
						<?php echo JHtml::_('grid.order',  $rows ); ?>
					</th>

					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>
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
			$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				//$row = &$rows[$i];
				// load the row from the db table 
				$row->load( $rows[$i]->id );
				$link 		= JRoute::_( 'index.php?option=com_clm&section=mannschaften&task=edit&cid[]='. $row->id );
				$checked 	= JHtml::_('grid.checkedout',   $row, $i );
				$published 	= JHtml::_('grid.published', $row, $i );

				?>
				<tr class="<?php echo 'row'. $k; ?>">

					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>

					<td>
						<?php echo $checked; ?>
					</td>

					<td>

								<span class="editlinktip hasTip" title="<?php echo JText::_( 'MANNSCHAFT_EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>

					</td>

					<td align="center">
						<?php echo $row->man_nr;?>
					</td>

					<td align="center">
						<?php echo $rows[$i]->liga_name;?>
					</td>
					<td align="center">
						<?php echo $row->tln_nr;?>
					</td>
					<td align="center">
						<?php if ($row->mf > 0) 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php if ($row->liste > 0) 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->verein;?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->saison;?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, ($row->liga == @$rows[$i-1]->liga), 'orderup()', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->liga == @$rows[$i+1]->liga), 'orderdown()', 'Move Down', $ordering ); ?></span>
	<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
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
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

public static function setMannschaftToolbar()
	{
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'MANNSCHAFT' ).': [ '. $text.' ]' , 'clm_headmenu_mannschaften.png'  );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function mannschaft( &$row,$lists, $option )
	{
		CLMViewMannschaften::setMannschaftToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>
	<script language="javascript" type="text/javascript">

		 Joomla.submitbutton = function (pressbutton) { 		
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo JText::_( 'MANNSCHAFT_NAMEN_ANGEBEN', true ); ?>" );
			} else if (form.man_nr.value == "") {
				alert( "<?php echo JText::_( 'MANNSCHAFT_NUMMER_ANGEBEN', true ); ?>" );
			} else if (form.tln_nr.value == "") {
				alert( "<?php echo JText::_( 'MANNSCHAFT_TEILNEHMER_NR_ANGEBEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'MANNSCHAFT_SAISON_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','liga') == 0 ) {
				alert( "<?php echo JText::_( 'MANNSCHAFT_LIGA_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','zps') == 0 ) {
				alert( "<?php echo JText::_( 'MANNSCHAFT_VEREIN_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
				<label for="name">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'MANNSCHAFT_HINT' );?>">
				<?php echo JText::_( 'MANNSCHAFT' )." : "; ?></span></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $row->name; ?>" />
			</td>
		</tr>
	<?php if ($lists['pgntype'] > 3) { ?>	
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="sname"><?php echo JText::_( 'MANNSCHAFT_SHORT' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sname" id="sname" size="50" maxlength="60" value="<?php echo $row->sname; ?>" />
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="contact"><?php echo JText::_( 'MANNSCHAFT_NUMMER' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="man_nr" id="man_nr" size="50" maxlength="60" value="<?php echo $row->man_nr; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="tln_nr"><?php echo JText::_( 'MANNSCHAFT_TEILNEHMER_NR' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tln_nr" id="tln_nr" size="50" maxlength="60" value="<?php echo $row->tln_nr; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'MANNSCHAFT_SAISON' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" ><label for="liga"><?php echo JText::_( 'MANNSCHAFT_LIGA' )." :"; ?></label>
			</td>
			<td>
			<?php echo $lists['liga']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
				<label for="verein">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'MANNSCHAFT_HINT' );?>">
				<?php echo JText::_( 'MANNSCHAFT_VEREIN' )." : "; ?></span></label>
			</td>
			<td>
			<?php echo $lists['verein']; ?>
			</td>
		</tr>
		<?php for ($i = 0; $i < $lists['anz_sgp']; $i++) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="<?php echo 'sg_zps'.$i; ?>"><?php echo JText::_( 'MANNSCHAFT_PARTNERVEREIN' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['sg'.$i]; ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="mf"><?php echo JText::_( 'MANNSCHAFT_FUEHRER' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['mf']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' )." : "; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="abzug"><?php echo JText::_( 'MANNSCHAFT_MPABZUG' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="abzug" id="abzug" size="10" maxlength="10" value="<?php echo $row->abzug; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="bpabzug"><?php echo JText::_( 'MANNSCHAFT_BPABZUG' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="bpabzug" id="bpabzug" size="10" maxlength="10" value="<?php echo $row->bpabzug; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>

		<tr>
			<td class="key" nowrap="nowrap">
				<label for="lokal">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'MANNSCHAFT_HINT' );?>">
				<?php echo JText::_( 'MANNSCHAFT_SPIELLOKAL' )." : "; ?></span></label>
			</td>
			<td>
			<?php  echo JText::_( 'CLM_KOMMA' ) . "<br><br>"; ?>
			<textarea class="inputbox" name="lokal" id="lokal" cols="40" rows="2" style="width:100%"><?php echo $row->lokal; ?></textarea>
			<br><?php  echo JText::_( 'CLM_ADDRESS' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'MANNSCHAFT_ADRESSE' )." : "; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="adresse" id="adresse" cols="40" rows="2" style="width:100%"><?php echo $row->adresse; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'MANNSCHAFT_TERMINE' )." : "; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="termine" id="termine" cols="40" rows="2" style="width:100%"><?php echo $row->termine; ?></textarea>
			<br><?php  echo JText::_( 'CLM_TERMINE' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'MANNSCHAFT_HOMEPAGE' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="homepage" id="homepage"  size="50" value="<?php echo $row->homepage; ?>" />
			</td>
		</tr>



		</table>
		</fieldset>
		</div>

 <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'REMARKS' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>
		<div class="clr"></div>

		<input type="hidden" name="section" value="mannschaften" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="pre_man" value="<?php echo $row->man_nr; ?>" />
<!---		<input type="hidden" name="cid" value="<?php //echo $row->cid; ?>" />
		<input type="hidden" name="client_id" value="<?php //echo $row->cid; ?>" />
--->		<input type="hidden" name="liste" value="<?php echo $row->liste; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
