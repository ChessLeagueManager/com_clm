<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');
	$lid = clm_core::$load->request_int('lid');
	$tid = clm_core::$load->request_int('tid');
	$returnview = clm_core::$load->request_string('returnview');

	$lang = clm_core::$lang->arbiter;
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->param['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_sid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
					echo "&nbsp;&nbsp;&nbsp;".CLMForm::selectState( $this->param['state'] );
				?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<thead>
		<tr>
		<th width="2%">
			#
		</th>
		<th width="2%">
			<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
		</th>
		<th width="8%" class="title">
			<?php echo JHtml::_('grid.sort',   $lang->title, 'title', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="18%" class="title">
			<?php echo JHtml::_('grid.sort',   $lang->name, 'name', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>

		<th width="18%" class="title">
			<?php echo JHtml::_('grid.sort',   $lang->vorname, 'vorname', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>

		<th width="10%" class="title">
			<?php echo JHtml::_('grid.sort',   $lang->fideid, 'fideid', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="8%" class="title">
			<?php echo JHtml::_('grid.sort',   $lang->fidefed, 'fidefed', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>

		<th width="5%">
		<?php echo JHtml::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'published', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="8%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   JText::_('JGRID_HEADING_ORDERING'), 'ordering', $this->param['order_Dir'], $this->param['order'] ); ?>
			<?php echo JHtml::_('grid.order',  $this->arbiters ); ?>
		</th>
		<th width="1%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'id', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		</tr>
		</thead>

		<tfoot>
		<tr>
		<td colspan="16">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
		</tr>
		</tfoot>

		<tbody>
		<?php
		$k = 0;

		$n=count( $this->arbiters );
		foreach ($this->arbiters as $i => $value) {
			$row = &$value;


			$checked 	= JHtml::_('grid.checkedout',   $row, $i );
			$published 	= JHtml::_('jgrid.published', $row->published, $i );
			?>
			<tr class="<?php echo 'row'. $k; ?>">
			<td align="center">
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>

			<td align="center">
				<?php echo $checked; ?>
			</td>

			<td>
				<?php echo $row->title; ?>
			</td>
			<td>
				<?php
					$adminLink = new AdminLink();
					$adminLink->view = "arbiterform";
					$adminLink->more = array('task' => 'edit', 'id' => $row->id, 'lid' => $lid, 'tid' => $tid, 'returnview' => $returnview);
					$adminLink->makeURL();
				
					?>
					<span class="editlinktip hasTip" title="<?php echo $lang->arbiter_edit; ?>: <?php echo $row->name; ?>">
						<a href="<?php echo $adminLink->url; ?>">
							<?php echo $row->name; ?>
						</a>
					</span>
			</td>

			<td>
				<?php echo $row->vorname; ?>
			</td>

			<td>
				<?php
					$adminLink = new AdminLink();
					$adminLink->view = "arbiterform";
					$adminLink->more = array('task' => 'edit', 'id' => $row->id);
					$adminLink->makeURL();
				
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Link zur FIDE' );?>::<?php echo $row->fideid; ?>">
						<a href="<?php echo 'https://ratings.fide.com/profile/'.$row->fideid; ?>" target="_blank">
							<?php echo $row->fideid; ?>
						</a>
					</span>
			</td>

			<td>
				<?php echo $row->fidefed; ?>
			</td>


			<td align="center">
				<?php echo $published;?>
			</td>
			<td class="order">
			<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $this->param['order'] ); ?></span>
			<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown', 'Move Down', $this->param['order'] ); ?></span>
			<?php $disabled = $this->param['order'] ?  '' : 'disabled="disabled"'; ?>
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


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="arbitermain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="returnview" value="<?php echo $returnview; ?>" />
	<input type="hidden" name="controller" value="arbitermain" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
