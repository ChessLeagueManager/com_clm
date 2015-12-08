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
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table>
	<tbody>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td>
			</td>
			<td><?php //echo ($this->lists['filter_accessgroup']); ?>
			</td>
		</tr>
	</tbody>
</table>
<?php if( !$this->accessgroupExists ) {
	JError::raiseWarning( 500, $row->name.": ".JText::_( 'ACCESSGROUP_WARNING_NO_TOURNEMENT' ) );
} ?>
<div id="editcell"> 
	<table class="adminlist">
		<thead>
			<tr>
				<th width="10">#</th>
				<th width="20">
					<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
				</th>
				<th width="" class="title"><?php echo JHtml::_('grid.sort',   JText::_('ACCESSGROUP_NAME'), 'name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="" class="title"><?php echo JHtml::_('grid.sort',   JText::_('ACCESSGROUP_KIND'), 'kind', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="" class="title"><?php echo JHtml::_('grid.sort',   JText::_('ACCESSGROUP_USERTYPE'), 'usertype', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="20"><?php echo JHtml::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="100" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',   JText::_('JGRID_HEADING_ORDERING'), 'ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					<?php echo JHtml::_('grid.order',  $this->accessgroups ); ?>
				</th>
				<th width="10" nowrap="nowrap"><?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$n=count( $this->accessgroups );
		$disabled = $this->ordering ?  '' : 'disabled="disabled"';
		$row	= JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		foreach ($this->accessgroups as $i => $value) {
			//$row = $value;
			$row->load( $value->id );
			$checked 	= JHtml::_('grid.checkedout',   $row, $i );
			$published 	= JHtml::_('grid.published', $row, $i );
			?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td align="center"><?php echo $checked; ?></td>
				<td>
					<?php

						
						$adminLink = new AdminLink();
						$adminLink->view = "accessgroupsform";
						//$adminLink->more = array('task' => 'edit', 'layout' => 'form', 'hidemainmenu' => 1, 'cid' => $row->id);
						$adminLink->more = array('task' => 'edit', 'hidemainmenu' => 1, 'cid' => $row->id);
						$adminLink->makeURL();
					
						if ($row->kind == "CLM") $row->name = JText::_( 'ACCESSGROUP_NAME_'.$row->usertype );
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ACCESSGROUP_EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $adminLink->url; ?>">
								<?php echo $row->name; ?>
							</a>
						</span>
						<?php 
					
					?>				
				</td>
				<td align="center"><?php echo $row->kind;?></td>
				<td align="center"><?php echo $row->usertype;?></td>
				<td align="center"><?php echo $published;?></td>
				<td class="order" align="center">
					<div style="float:left; width:20px;"><?php echo $this->pagination->orderUpIcon($i, (isset($this->accessgroups[$i-1]->name) AND $row->kind == $this->accessgroups[$i-1]->name), 'orderup', 'Move Up', $this->ordering); ?></div>
					<div style="float:left; width:20px;"><?php echo $this->pagination->orderDownIcon($i, $n, (isset($this->accessgroups[$i+1]->name) AND $row->kind == $this->accessgroups[$i+1]->name), 'orderdown', 'Move Down', $this->ordering); ?></div>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align:center;" />
				</td>
				<td align="center"><?php echo $row->id; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>
</div> 

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="accessgroupsmain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order'] ; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir'] ; ?>" />
	<input type="hidden" name="controller" value="accessgroupsmain" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
