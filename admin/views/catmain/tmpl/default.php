<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
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
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->param['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_sid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
					echo $this->form['parent'];
//					echo "&nbsp;&nbsp;&nbsp;".JHtml::_('grid.state',  $this->param['state'] );
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
		<th width="18%" class="title">
			<?php echo JHtml::_('grid.sort',   JText::_('CATEGORY_NAME'), 'name', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>

		<th width="6%" class="title">
			<?php echo JText::_('JDATE'); ?>
		</th>

		<th width="10%">
			<?php echo JText::_('TOURNAMENTS'); ?>
		</th>

		<th width="5%">
		<?php echo JHtml::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'published', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="8%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   JText::_('JGRID_HEADING_ORDERING'), 'ordering', $this->param['order_Dir'], $this->param['order'] ); ?>
			<?php echo JHtml::_('grid.order',  $this->categories ); ?>
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

		$n=count( $this->categories );
		foreach ($this->categories as $i => $value) {
			$row = &$value;


			$checked 	= JHtml::_('grid.checkedout',   $row, $i );
//			$published 	= JHtml::_('grid.published', $row, $i );
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
				<?php

					
					$adminLink = new AdminLink();
					$adminLink->view = "catform";
					$adminLink->more = array('task' => 'edit', 'id' => $row->id);
					$adminLink->makeURL();
				
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'CATEGORY_EDIT' );?>::<?php echo $row->name; ?>">
						<a href="<?php echo $adminLink->url; ?>">
							<?php echo $row->name; ?>
						</a>
					</span>
					<?php 
				
				//echo '<br />';
				//echo $row->nameTotal;
				?>
			</td>

			<td align="center">
				<?php 
				if ($row->dateStart != '0000-00-00' AND $row->dateStart != '1970-01-01') {
					echo JHtml::_( 'date', $row->dateStart, JText::_('DATE_FORMAT_CLM')) ;
				}
				if ($row->dateEnd != '0000-00-00' AND $row->dateEnd != '1970-01-01') {
					echo '<br />'.JHtml::_( 'date', $row->dateEnd, JText::_('DATE_FORMAT_CLM'));
				}
				
				?>
			</td>


			<td align="center">
				<?php
				if ($row->tournamentCount > 0) {
					$adminLink = new AdminLink();
					$adminLink->view = "turmain";
					$adminLink->more = array('filter_parentid' => $row->id);
					$adminLink->makeURL();
					?>
					<a href="<?php echo $adminLink->url; ?>">
						<?php echo $row->tournamentCount; ?>
					</a>
				<?php
				} else {
				?>
					-
				<?php
				}
				?>
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
	<input type="hidden" name="view" value="catmain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="controller" value="catmain" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
