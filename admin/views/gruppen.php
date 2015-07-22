<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewGruppen
{
public static function setGruppenToolbar()
	{
	// Make sure the user is authorized to view this page
	$user = JFactory::getUser();
	$jid = $user->get('id');
	// CLM Userstatus auslesen 
		JToolBarHelper::title( JText::_( 'TITLE_GROUPS' ), 'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
		JToolBarHelper::help( 'screen.clm.mannschaft' );
	}

public static function gruppen( &$rows, &$lists, &$pageNav, $option )
	{
		$mainframe	= JFactory::getApplication();
		CLMViewGruppen::setGruppenToolbar();
		$user =JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHtml::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=gruppen" method="post" name="adminForm" id="adminForm">

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
						<?php echo JHtml::_('grid.sort',  JText::_( 'GROUPS_OVERVIEW_GROUPS'), 'a.Gruppe', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',   JText::_( 'GROUPS_OVERVIEW_END'), 'a.Meldeschluss', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',   JText::_( 'GROUPS_OVERVIEW_BY'), 'a.user', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort',   JText::_( 'GROUPS_OVERVIEW_SEASON'), 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="6%">
						<?php echo JHtml::_('grid.sort',   'Published', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'Order', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
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
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];

				$link 		= JRoute::_( 'index.php?option=com_clm&section=gruppen&task=edit&cid[]='. $row->id );

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

								<span class="editlinktip hasTip" title="<?php echo JText::_( 'GROUPS_OVERVIEW_TIP' );?>::<?php echo $row->Gruppe; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->Gruppe; ?></a></span>

					</td>

					<td align="center">
						<?php echo $row->Meldeschluss;?>
					</td>
					<td align="center">
						<?php echo $row->user;?>
					</td>
					<td align="center">
						<?php echo $row->saison;?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
	<td class="order">
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

public static function setGruppeToolbar()
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'TITLE_GROUPS_2' ).': [ '. $text.' ]' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function gruppe( &$row,$lists, $option, $jid)
	{
		CLMViewGruppen::setGruppeToolbar();
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
			if (form.Gruppe.value == "") {
				alert( "<?php echo JText::_( 'GROUPS_OVERVIEW_SCRIPT_GROUP', true ); ?>" );
			} else if (form.Meldeschluss.value == "") {
				alert( "<?php echo JText::_( 'GROUPS_OVERVIEW_SCRIPT_END', true ); ?>" );
			}
			 else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'GROUPS_OVERVIEW_SCRIPT_SEASON', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'GROUPS_OVERVIEW_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="Gruppe"><?php echo JText::_( 'GROUPS_OVERVIEW_GROUP_NAME' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="Gruppe" id="Gruppe" size="50" maxlength="60" value="<?php echo $row->Gruppe; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="Meldeschluss"><?php echo JText::_( 'GROUPS_OVERVIEW_END' ); ?></label>
			</td>
            		<td>
                	<?php echo JHtml::_('calendar', $row->Meldeschluss, JText::_( 'GROUPS_OVERVIEW_END_CALENDAR' ), JText::_( 'GROUPS_OVERVIEW_END_CALENDAR' ), '%Y-%m-%d', array('class'=>'text_area', 'size'=>'10',  'maxlength'=>'10')); ?>
            		</td>

		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="geschlecht">
			<?php echo JText::_( 'GROUPS_OVERVIEW_SEX_DD' ); ?>
			</label>
			</td>
			<td>
			<select name="geschlecht" id="geschlecht">" size="1">
			<option>- wählen -</option>
			<option <?php if ($row->geschlecht == "1") {echo 'selected="selected"';} ?> value="1"><?php echo JText::_( 'GROUPS_OVERVIEW_SEX_DD1' );?></option>
			<option <?php if ($row->geschlecht == "2") {echo 'selected="selected"';} ?> value="2"><?php echo JText::_( 'GROUPS_OVERVIEW_SEX_DD2' );?></option>
			<option <?php if ($row->geschlecht == 0) {echo 'selected="selected"';} ?> value="0"><?php echo JText::_( 'GROUPS_OVERVIEW_SEX_DD3' );?></option>
			</select>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="durchgang">
			<?php echo JText::_( 'GROUPS_OVERVIEW_AGE_DD1' ); ?>
			</label>
			</td>
			<td>
			<select name="alter_grenze" id="alter_grenze" >" size="1">
			<option>- wählen -</option>
			<option <?php if ($row->alter_grenze == "1") {echo 'selected="selected"';} ?> value="1"><?php echo JText::_( 'GROUPS_OVERVIEW_AGE_DD2' );?></option>
			<option <?php if ($row->alter_grenze == "2") {echo 'selected="selected"';} ?> value="2"><?php echo JText::_( 'GROUPS_OVERVIEW_AGE_DD3' );?></option>
			<option <?php if ($row->alter_grenze == 0) {echo 'selected="selected"';} ?> value="0"><?php echo JText::_( 'GROUPS_OVERVIEW_AGE_DD4' );?></option>
			</select>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="alter">
			<?php echo JText::_( 'GROUPS_OVERVIEW_AGE_DD5' ); ?>
			</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="alter" id="alter" size="2" maxlength="3" value="<?php echo $row->alter; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'GROUPS_OVERVIEW_TEXT_SEASON' ); ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>
<!---
		<tr>
			<td class="key" nowrap="nowrap"><label for="delete"><?php echo JText::_( 'GROUPS_OVERVIEW_TEXT_DELETE' ); ?></label>
			</td>
			<td>
				<input type="checkbox" id="delete" name="delete" value="1" /><?php echo JText::_( 'GROUPS_OVERVIEW_TEXT_DELETE_HINT' );?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="create"><?php echo JText::_( 'GROUPS_OVERVIEW_TEXT_LEVEL' ); ?></label>
			</td>
			<td>
				<input type="checkbox" id="create" name="create" value="1" /><?php echo JText::_( 'GROUPS_OVERVIEW_TEXT_LEVEL_HINT' );?>
			</td>
		</tr>
--->		
		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' ); ?></label>
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

		<input type="hidden" name="section" value="gruppen" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="user" value="<?php echo $jid; ?>" />
		<input type="hidden" name="liste" value="<?php echo $row->liste; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
