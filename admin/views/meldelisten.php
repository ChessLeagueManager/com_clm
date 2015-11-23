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

class CLMViewMeldelisten
{
public static function setMeldelistenToolbar()
	{
		JToolBarHelper::title( JText::_( 'TITLE_MELDELISTE' ), 'generic.png' );
		JToolBarHelper::editList();
		JToolBarHelper::help( 'screen.clm.meldeliste' );
	}

public static function meldelisten ( &$rows, &$lists, &$pageNav, $option )
	{
		$mainframe	= JFactory::getApplication();
		CLMViewMeldelisten::setMeldelistenToolbar();
		$user =JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHtml::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=meldelisten" method="post" name="adminForm" id="adminForm">

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
						<?php echo JHtml::_('grid.sort',   'MELDELISTE_MANNSCHAFT', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'MELDELISTE_NR', 'a.mnr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',   'MELDELISTE_LIGA', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="22%">
						<?php echo JHtml::_('grid.sort',   'MELDELISTE_VEREIN', 'b.Vereinname', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort',   'MELDELISTE_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
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
					<td colspan="12">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];

				$link 		= JRoute::_( 'index.php?option=com_clm&section=meldelisten&task=edit&cid[]='. $row->id );

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
	
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'MELDELISTE_EDIT' );?>::<?php echo $row->name.$row->man_nr; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>
	
					</td>


					<td align="center">
						<?php echo $row->man_nr;?>
					</td>

					<td align="center">
						<?php echo $row->liga_name;?>
					</td>
					<td align="center">
						<?php echo $row->verein;?>
					</td>
					<td align="center">
						<?php echo $row->saison;?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>

	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, ($row->liga == @$rows[$i-1]->liga), 'order(1)', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->liga == @$rows[$i+1]->liga), 'order(-1)', 'Move Down', $ordering ); ?></span>
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
		<input type="hidden" name="verein" value="<?php echo $row->verein; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

public static function setMeldelisteToolbar($row)
	{
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		$verein 	= JRequest::getVar( 'verein' );
		JToolBarHelper::title(  JText::_( 'MELDELISTE')." ".$row->name .': [ '. $text.' ]', 'clm_headmenu_mannschaften.png');
		JToolBarHelper::custom( 'save_meldeliste', 'save.png', 'save_f2.png', JText::_('SAVE'), false );
		JToolBarHelper::custom( 'apply_meldeliste', 'apply.png', 'apply_f2.png', JText::_('APPLY'), false );
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function meldeliste( &$row, $row_spl, $row_sel, $max, $liga, $abgabe, $option)
	{
		CLMViewMeldelisten::setMeldelisteToolbar($row);
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		
		// Konfigurationsparameter auslesen
		$config = clm_core::$db->config();
		$countryversion=$config->countryversion;
		?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
<div>
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'MELDELISTE_STAMMSPIELER' ); ?></legend>
		<table class="admintable">

		<tr>
			<th width="10">
				<?php echo JText::_( 'MELDELISTE_BRETT' ); ?>
			</th>
			<th width="10">
				<?php echo JText::_( 'MELDELISTE_SPIELERNAME' ); ?>
			</th>
			<th width="10">
				<?php echo JText::_( 'MELDELISTE_BLOCK' ); ?>
			</th>
		</tr>
<?php if(isset($liga[0])){for ($i=0; $i<$liga[0]->stamm; $i++){ ?>
	<tr>
		<td class="key" nowrap="nowrap">
		  <label for="sid">
			<?php echo JText::_( 'MELDELISTE_BRETT_NR').' '.($i+1).' : '; ?>
		  </label>
		</td>
		<td>
		  <select size="1" name="<?php echo 'spieler'.($i+1); ?>" id="<?php echo $i+1; ?>">
			<option value="0"><?php echo JText::_( 'MELDELISTE_SPIELER_AUSWAEHLEN'); ?></option>
			<?php for ($x=0; $x < $max[0]->max; $x++) { ?>
	<!---		 <option value="<?php //echo $row_spl[$x]->id.'-'.$row_spl[$x]->zps; ?>" <?php //if (((int)$row_spl[$x]->id) == ((int)$row_sel[$i]->mgl_nr) AND ($row_spl[$x]->zps == $row_sel[$i]->zps)) { ?> selected="selected" <?php //} ?>><?php //echo $row_spl[$x]->id.'&nbsp;';if((int)$row_spl[$x]->id < 1000) {echo "&nbsp;&nbsp;";} echo "-&nbsp;&nbsp;".$row_spl[$x]->name; ?></option> ---> 
			 <option value="<?php echo $row_spl[$x]->id.'-'.$row_spl[$x]->zps; ?>" <?php
			  if ($countryversion == "de") {
				if (isset($row_sel[$i]) AND ((int)$row_spl[$x]->id) == ((int)$row_sel[$i]->mgl_nr) AND ($row_spl[$x]->zps == $row_sel[$i]->zps)) { ?> selected="selected" <?php } ?>><?php 
			  } else {
				if (isset($row_sel[$i]) AND ((int)$row_spl[$x]->id) == ($row_sel[$i]->PKZ) AND ($row_spl[$x]->zps == $row_sel[$i]->zps)) { ?> selected="selected" <?php } ?>><?php 
			  }
				echo $row_spl[$x]->name.'&nbsp;-&nbsp;&nbsp;'.$row_spl[$x]->id; ?></option> 
			<?php }	?>
		  </select>
		</td>
		<td align="center">
		  <input type="checkbox" name="check<?php echo $i+1; ?>" value="1" <?php if(isset($row_sel[$i]) AND $row_sel[$i]->gesperrt =="1") { echo 'checked="checked"'; }?>>
		</td>
	</tr>
<?php }} ?> 
		</table>
		</fieldset>
</div>
<div>
 <fieldset class="adminform">
   <legend><?php echo JText::_( 'MELDELISTE_DETAILS' ); ?></legend>
	<table class="admintable">
	<tr>
	<tr>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'MELDELISTE_MELDER' ).' : '; ?></td>
		<td class="key" nowrap="nowrap"><?php if (!isset($abgabe[0]->name)) {echo "---";} 
			else { echo $abgabe[0]->name; } ?>
		</td>
	</tr>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'JDATE' ).' : '; ?></td>
		<td class="key" nowrap="nowrap"><?php if (!isset($abgabe[0]->datum) OR $abgabe[0]->datum=="0000-00-00 00:00:00") {echo  "---";} 
			else { echo JHtml::_('date',  $abgabe[0]->datum, JText::_('DATE_FORMAT_LC2')); } ?>
		</td>
	</tr>
	<tr>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'MELDELISTE_LAST_UPDATE' ).' : '; ?></td>
		<td class="key" nowrap="nowrap"><?php if (!isset($abgabe[0]->editor )) {echo "---";} 
			else { echo $abgabe[0]->name; } ?>
		</td>
	</tr>
	<tr>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'JDATE' ).' : '; ?></td>
		<td class="key" nowrap="nowrap"><?php if (!isset($abgabe[0]->edit_datum) OR $abgabe[0]->edit_datum =="0000-00-00 00:00:00") {echo  "---";} 
			else { echo JHtml::_('date',  $abgabe[0]->edit_datum, JText::_('DATE_FORMAT_LC2')); } ?>
		</td>
	</tr>

	</table>
  </fieldset>
  </div>

		</div>




		<div class="width-50 fltrt">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'MELDELISTE_ERSATZSPIELER' ); ?></legend>

		<table class="admintable">
		<tr>
			<th width="10">
				<?php echo JText::_( 'MELDELISTE_BRETT' ); ?>
			</th>
			<th width="10">
				<?php echo JText::_( 'MELDELISTE_SPIELERNAME' ); ?>
			</th>
			<th width="10">
				<?php echo JText::_( 'MELDELISTE_BLOCK' ); ?>
			</th>
		</tr>
<?php
	// Ersatzspieler
 if(isset($liga[0])){	for ($i=$liga[0]->stamm; $i< ($liga[0]->stamm + $liga[0]->ersatz); $i++){
?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'MELDELISTE_BRETT_NR' ).' '.($i+1).' : '; ?></label>
			</td>
		<td>
		  <select size="1" name="<?php echo 'spieler'.($i+1); ?>" id="<?php echo $i+1; ?>">
			<option value="0"><?php echo JText::_( 'MELDELISTE_SPIELER_AUSWAEHLEN'); ?></option>
			<?php for ($x=0; $x < $max[0]->max; $x++) { ?>
	<!---	 <option value="<?php //echo $row_spl[$x]->id.'-'.$row_spl[$x]->zps; ?>" <?php //if (((int)$row_spl[$x]->id) == ((int)$row_sel[$i]->mgl_nr) AND ($row_spl[$x]->zps == $row_sel[$i]->zps)) { ?> selected="selected" <?php //} ?>><?php //echo $row_spl[$x]->id.'&nbsp;';if((int)$row_spl[$x]->id < 1000) {echo "&nbsp;&nbsp;";} echo "-&nbsp;&nbsp;".$row_spl[$x]->name; ?></option> ---> 
			 <option value="<?php echo $row_spl[$x]->id.'-'.$row_spl[$x]->zps; ?>" <?php 
			  if ($countryversion == "de") {
				if (isset($row_sel[$i]) AND ((int)$row_spl[$x]->id) == ((int)$row_sel[$i]->mgl_nr) AND ($row_spl[$x]->zps == $row_sel[$i]->zps)) { ?> selected="selected" <?php } ?>><?php 
			  } else {			
				if (isset($row_sel[$i]) AND ((int)$row_spl[$x]->id) == ($row_sel[$i]->PKZ) AND ($row_spl[$x]->zps == $row_sel[$i]->zps)) { ?> selected="selected" <?php } ?>><?php 
			  }
				echo $row_spl[$x]->name.'&nbsp;-&nbsp;&nbsp;'.$row_spl[$x]->id; ?></option> 
			<?php }	?>
		  </select>
		</td>
		<td align="center">
		  <input type="checkbox" name="check<?php echo $i+1; ?>" value="1" <?php if(isset($row_sel[$i]) AND $row_sel[$i]->gesperrt =="1") { echo 'checked="checked"'; }?>>
		</td>

	</tr>
<?php }} ?> 
		</table>
		</fieldset>
		</div>
	<div class="clr"></div>
		<input type="hidden" name="section" value="mannschaften" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="liga" value="<?php echo $row->liga; ?>" />
		<input type="hidden" name="zps" value="<?php echo $row->zps; ?>" />
		<input type="hidden" name="sid" value="<?php echo $row->sid; ?>" />
		<input type="hidden" name="mnr" value="<?php echo $row->man_nr; ?>" />
		<input type="hidden" name="stamm" value="<?php echo $liga[0]->stamm; ?>" />
		<input type="hidden" name="ersatz" value="<?php echo $liga[0]->ersatz; ?>" />
		<input type="hidden" name="max" value="<?php echo $max[0]->max; ?>" />
		<input type="hidden" name="editor" value="<?php echo $abgabe[0]->name; ?>" />

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
