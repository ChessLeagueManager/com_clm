<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
 
require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
$clmAccess = new CLMAccess();
 
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
					echo "&nbsp;&nbsp;&nbsp;".CLMForm::selectSeason('filter_sid', $this->param['sid'], TRUE);
					echo "&nbsp;&nbsp;&nbsp;".CLMForm::selectModus('filter_modus', $this->param['modus'], TRUE);
					echo "&nbsp;&nbsp;&nbsp;".JHtml::_('grid.state',  $this->param['state'] );
				?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<thead>
		<tr>
		<th width="2%">
			<?php echo JText::_( 'JGRID_HEADING_ROW_NUMBER' ); ?>
		</th>
		<th width="2%">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->turniere ); ?>);" />
		</th>
		<th width="18%" class="title">
			<?php echo JHtml::_('grid.sort',   JText::_('TOURNAMENT_NAME'), 'a.name', $this->param['order_Dir'], $this->param['order'] ); ?>
			<br />
			<?php echo JText::_('JCATEGORY'); ?>
		</th>
		<th width="5%">
			<?php echo JHtml::_('grid.sort',   JText::_('SEASON'), 'c.name', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="6%" class="title">
			<?php echo JHtml::_('grid.sort',   JText::_('JDATE'), 'a.dateStart', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="6%" class="title">
			<?php echo JText::_('INVITATION'); ?>
		</th>
		<th width="12%">
			<?php echo JText::_('ORGANIZER')."<br />".JText::_('HOSTER'); ?>
		</th>
		<th width="6%">
			<?php echo JHtml::_('grid.sort',   JText::_('MODUS'), 'a.typ', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="3%">
			<?php echo JHtml::_('grid.sort',   JText::_('ROUNDS_COUNT'), 'a.runden', $this->param['order_Dir'], $this->param['order'] ); ?>
			<br />
			(<?php echo JText::_('STAGE_COUNT'); ?>)
		</th>
		<th width="3%">
			<?php echo JHtml::_('grid.sort',   JText::_('PARTICIPANTS'), 'a.teil', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="3%">
			<?php echo JHtml::_('grid.sort',   JText::_('TOURNAMENT_DIRECTOR'), 'TL', 'a.tl', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHtml::_('grid.sort',   JText::_('ROUNDS_CREATED'), 'a.rnd', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>

		<th width="5%">
		<?php echo JHtml::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'a.published', $this->param['order_Dir'], $this->param['order'] ); ?>
		</th>
		<th width="8%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   JText::_('JGRID_HEADING_ORDERING'), 'a.ordering', $this->param['order_Dir'], $this->param['order'] ); ?>
			<?php echo JHtml::_('grid.order',  $this->turniere ); ?>
		</th>
		<th width="1%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
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

		$n=count( $this->turniere );
		foreach ($this->turniere as $i => $value) {
			$row = &$value;
			$tournament = new CLMTournament($row->id, TRUE);
 
			$checked 	= JHtml::_('grid.checkedout',   $row, $i );
			$published 	= JHtml::_('grid.published', $row, $i );
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
				$clmAccess->accesspoint = 'BE_tournament_edit_detail';
				if (($row->tl != CLM_ID AND $clmAccess->access() !== true ) OR ($clmAccess->access() === false)) {
					echo $row->name;
				} elseif (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
					echo $row->name;
				} else {
					
					$adminLink = new AdminLink();
					$adminLink->view = "turform";
					$adminLink->more = array('task' => 'edit', 'id' => $row->id);
					$adminLink->makeURL();
				
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'TOURNAMENT_EDIT' );?>::<?php echo $row->name; ?>">
						<a href="<?php echo $adminLink->url; ?>">
							<?php echo $row->name; ?>
						</a>
					</span>
					<?php 
				} 
				?>
				<?php
				if ($row->catidAlltime > 0) {
					echo '<br />-&nbsp;'.$row->catnameAlltime;
				}
				if ($row->catidEdition > 0) {
					echo '<br />-&nbsp;'.$row->catnameEdition;
				}
				?>
				
			</td>

			<td align="center">
				<?php echo $row->saison;?>
			</td>
			
			<td align="center">
				<?php 
				if ($row->dateStart != '0000-00-00') {
					echo JHtml::_( 'date', $row->dateStart, JText::_('DATE_FORMAT_CLM')) ;
				}
				if ($row->dateEnd != '0000-00-00') {
					echo '<br />'.JText::_('TOURNAMENT_UNTIL').'<br />'.JHtml::_( 'date', $row->dateEnd, JText::_('DATE_FORMAT_CLM'));
				}
				
				?>
			</td>
			
			<td align="center">
			<?php //if (!$tournament->checkAccess(0,0,$row->tl)) {
				$clmAccess->accesspoint = 'BE_tournament_edit_detail';
				if (($row->tl != CLM_ID AND $clmAccess->access() !== true ) OR ($clmAccess->access() === false)) {
					if ($row->inviteLength > 0) {
						echo JText::_('INVITATIONTEXT_EXISTING');
					} else {
						echo JText::_('INVITATIONTEXT_NONE');
					}
				} else { 
				$adminLink = new AdminLink();
				$adminLink->view = "turinvite";
				$adminLink->more = array('task' => 'edit', 'id' => $row->id);
				$adminLink->makeURL();
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'JACTION_EDIT' );?>">
					<a href="<?php echo $adminLink->url; ?>">
						<?php 
						if ($row->inviteLength > 0) {
							echo JText::_('INVITATIONTEXT_EXISTING');
						} else {
							echo JText::_('INVITATIONTEXT_NONE');
						}
						?>
					</a>
				</span>
				<?php 
				} 
				?>
			</td>
			
			<td align="center">
				<?php 
				if ($row->bezirkTur == 1) { 
					echo JText::_('DISTRICT_EVENT')."<br />";
				}
				if (isset($row->hostName)) { 
					echo $row->hostName;
				} 
				?>
			</td>

			<td align="center">
				<?php echo JText::_('MODUS_TYP_'.$row->typ); ?>
			</td>

			<td align="center">
				<?php 
				$clmAccess->accesspoint = 'BE_tournament_edit_result';
				//echo "<br>tl: ".$row->tl."  id: ".CLM_ID." ac: ".$clmAccess->access();
				if (($row->tl == CLM_ID AND $clmAccess->access() !== false) OR ($clmAccess->access() === true)) {
					$adminLink = new AdminLink();
					$adminLink->view = "turrounds";
					$adminLink->more = array('id' => $row->id);
					$adminLink->makeURL();
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'ROUNDS_VIEW' );?>">
						<a href="<?php echo $adminLink->url; ?>">
							<?php 
								// mehrere Durchgänge?
								if ($row->dg > 1) {
									echo $row->dg."&nbsp;x&nbsp;";
								}
								echo $row->runden."&nbsp;".JText::_('ROUNDS');
							?>
						</a>
					</span>
					<?php 
						echo "<br />(".$row->roundsApproved." ".JText::_('CLM_APPROVED').")";
					} else {
						if ($row->dg > 1) {
							echo $row->dg."&nbsp;x&nbsp;";
						}
						echo $row->runden."&nbsp;".JText::_('ROUNDS');
						echo "<br />(".$row->roundsApproved." ".JText::_('CLM_APPROVED').")";
					}
					?>
			</td>
			
			<td align="center">
				<?php //if (!$tournament->checkAccess(0,0,$row->tl)) {
				$clmAccess->accesspoint = 'BE_tournament_edit_detail';
				if (($row->tl != CLM_ID AND $clmAccess->access() !== true ) OR ($clmAccess->access() === false)) {
					echo $row->teil." ".JText::_('PLAYERS'); 
					} else {
					$adminLink = new AdminLink();
					$adminLink->view = "turplayers";
					$adminLink->more = array('id' => $row->id);
					$adminLink->makeURL();
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'PARTICIPANTS_VIEW' );?>">
					<a href="<?php echo $adminLink->url; ?>">
						<?php echo $row->teil." ".JText::_('PLAYERS'); ?>
					</a>
				</span>
				<?php } ?>
				<br />
				(<?php echo $row->registered."&nbsp;".JText::_('REGISTERED'); ?>)
			</td>
			
			<td align="center">
				<?php echo $row->director;?>
			</td>
			
			<td align="center">
				<?php if ($row->rnd == 1) 
				{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
				else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
			</td>

			<td align="center">
				<?php echo $published;?>
			</td>
	
	<td class="order">
	<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup()', 'Move Up', $this->param['order'] ); ?></span>
	<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown()', 'Move Down', $this->param['order'] ); ?></span>
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
	<input type="hidden" name="view" value="turmain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="controller" value="turmain" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
