<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$clmAccess = clm_core::$access;
 
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		
		<thead>
			<tr>
				<th width="10">
					#
				</th>
				<th width="10">
					<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
				</th>
				<th width="3%">
					<?php echo JHtml::_('grid.sort',   JText::_('ROUND_DG'), 'dg', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th width="3%">
					<?php echo JHtml::_('grid.sort',   JText::_('ROUND_NR'), 'nr', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ROUND'); ?>
				</th>
				
				<th width="9%">
					<?php echo JText::_('JDATE'); ?>
				</th>
				
				<th width="5%">
					<?php echo JText::_('RUNDE_STARTTIME'); ?>
				</th>

				<th width="10%">
					<?php echo JText::_('MATCH_COUNT'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('MATCHES'); ?>
				</th>
				
				<th width="8%">
					<?php echo JHtml::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'published', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				
				<th width="8%">
					<?php echo JText::_('ENTRY_ENABLED'); ?>
				</th>
				
				<th width="8%">
					<?php echo JHtml::_('grid.sort',   JText::_('TOURNAMENT_DIRECTOR')."<br />".JText::_('APPROVAL'), 'tl_ok', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
		<?php
		$k = 0;
		
		$n=count( $this->turrounds );
		$row =JTable::getInstance( 'turnier_runden', 'TableCLM' );
		foreach ($this->turrounds as $i => $value) {
			//$row = &$value;
			// load the row from the db table 
			$row->load( $value->id );
			$checked 	= JHtml::_('grid.checkedout',   $row, ($i) ); //-1
			$published 	= JHtml::_('grid.published', $row, ($i) );  //-1
			?>
			
			<tr class="<?php echo 'row'. $k; ?>">
				
				<td align="center">
					<?php echo $this->pagination->getRowOffset( $i ); //-1?> 
				</td>
				
				<td>
					<?php echo $checked; ?>
				</td>
				
				<td align="center">
					<?php echo $row->dg;?>
				</td>
				
				<td align="center">
					<?php echo $row->nr;?>
				</td>
				
				<td>
					<?php
					if (($this->turnier->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== true ) OR ($clmAccess->access('BE_tournament_edit_round') === false))
					{
						echo $row->name;
					} else {
						$adminLink = new AdminLink();
						$adminLink->view = "turroundform";
						$adminLink->more = array('task' => 'edit', 'turnierid' => $this->param['id'], 'roundid' => $row->id);
						$adminLink->makeURL();
						echo '<span class="editlinktip hasTip" title="'.JText::_( 'JACTION_EDIT' ).'">';
						echo '<a href="'.$adminLink->url.'">'.$row->name.'</a>';
						echo '</span>';
					}
					?>
				</td>
				
				<td align="center">
					<?php if ($row->datum != 0) echo JHtml::_( 'date', $row->datum, JText::_('DATE_FORMAT_CLM'));?>
				</td>
				<td align="center">
					<?php if ($row->startzeit != 0) echo substr($row->startzeit,0,5);?>
				</td>
				
				<td align="center">
					<?php 
					if (($this->turnier->tl != clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_result') !== true ) OR ($clmAccess->access('BE_tournament_edit_result') === false)) {
						echo CLMText::sgpl($value->countMatches, JText::_('MATCH'), JText::_('MATCHES'));
					} else {	
						$adminLink = new AdminLink();
						$adminLink->view = "turroundmatches";
						$adminLink->more = array('turnierid' =>  $this->param['id'], 'roundid' => $row->id);
						$adminLink->makeURL();
						echo '<a href="'.$adminLink->url.'">'.CLMText::sgpl($value->countMatches, JText::_('MATCH'), JText::_('MATCHES')).'</a>';
					} ?>
				</td>
				
				<td align="center">
					<?php 
						echo $value->countAssigned."&nbsp;".JText::_('MATCHES_ASSIGNED');
						echo '<br />'.$value->countResults."&nbsp;".JText::_('MATCHES_PLAYED');
					?>
				</td>
				
				<td align="center">
					<?php echo $published;?>
				</td>
				
				<td align="center">
					<?php 
						// tl_ok/director approval
						if ($value->abgeschlossen == '1') { 
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i).'\', \'disbale\')" title="'.JText::_('DISABLE_ENTRY').'"><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /></a>';
						} else {
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i).'\', \'enable\')" title="'.JText::_('ENABLE_ENTRY').'"><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /></a>';
						}
					?>
				</td>
				
				<td align="center">
					<?php 
						// tl_ok/director approval
						if ($row->tl_ok == '1') { 
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i).'\', \'unapprove\')" title="'.JText::_('REMOVE_APPROVAL').'"><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /></a>';
						} else {
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i).'\', \'approve\')" title="'.JText::_('SET_APPROVAL').'"><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /></a>';
						}
					?>
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
		
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	
	</table>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turrounds" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="turrounds" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="rlimit" value="1" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
