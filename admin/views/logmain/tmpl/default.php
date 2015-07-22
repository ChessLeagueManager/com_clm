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
					echo "&nbsp;&nbsp;&nbsp;".$this->forms['lid'];
					echo "&nbsp;&nbsp;&nbsp;".$this->forms['tid'];
				?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<thead>
		<tr>
		<th width="15">
			<?php echo JText::_( 'JGRID_HEADING_ROW_NUMBER' ); ?>
		</th>
		<th width="2%">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->log ); ?>);" />
		</th>
		<th class="title">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_ACTION' ), 'a.aktion', @$lists['order_Dir'], @$lists['order'] ); ?>
		</th>
		<th width="15%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_USER' ), 'a.username', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_SEASON' ), 'a.sid', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_LEAGUE' ), 'a.lid', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_TOURNAMENT' ), 'a.tid', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_ROUND' ), 'a.rnd', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_PAIRING' ), 'a.paar', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_DG' ), 'a.dg', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_ZPS' ), 'a.zps', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_TEAM' ), 'a.man', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_MEMBER_NO' ), 'a.mgl_nr', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_AFFECTED' ), 'a.jid', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_CIDS' ), 'a.cids', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_DATE' ), 'a.datum', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>

		<th width="1%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   JText::_( 'LOGFILE_ID' ), 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		</tr>
		</thead>


		<tfoot>
		<tr>
		<td colspan="17">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
		</tr>
		</tfoot>

		<tbody>
		<?php
		$k = 0;

		$n=count( $this->log );
		foreach ($this->log as $i => $value) {
			$row = &$value;

			$checked 	= JHtml::_('grid.checkedout',   $row, $i );
			?>
			<tr class="<?php echo 'row'. $k; ?>">
			<td align="center">
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>

			<td align="center">
				<?php echo $checked; ?>
			</td>
			
			<td align="center">
				<?php echo $row->aktion;?>
			</td>
			<td align="center">
				<?php echo $row->username;?>
			</td>
			<td align="center">
				<?php 
				if ($row->sid > 0) {
					echo $row->saisonname.'<br />ID: '.$row->sid;
				}
				?>
			</td>
			<td align="center">
				<?php 
				if ($row->lid > 0) {
					echo $row->liganame.'<br />ID: '.$row->lid;
				}
				?>
			</td>
			<td align="center">
				<?php 
				if ($row->tid > 0) {
					echo $row->turniername.'<br />ID: '.$row->tid;
				}
				?>
			</td>
			<td align="center">
				<?php echo $row->rnd;?>
			</td>
			<td align="center">
				<?php echo $row->paar;?>
			</td>
			<td align="center">
				<?php echo $row->dg;?>
			</td>
			<td align="center">
				<?php echo $row->zps;?>
			</td>
			<td align="center">
				<?php echo $row->man;?>
			</td>
			<td align="center">
				<?php echo $row->mgl_nr;?>
			</td>
			<td align="center">
				<?php echo $row->jid;?>
			</td>
			<td align="center">
				<?php echo $row->cids;?>
			</td>
			<td align="center">
				<?php echo $row->datum;?>
			</td>
			<td align="center">
				<?php echo $row->id;?>
			</td>
				
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="logmain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="controller" value="logmain" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
