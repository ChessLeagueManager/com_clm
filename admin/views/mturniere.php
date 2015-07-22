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

class CLMViewMTurniere
{
function setMTurniereToolbar()
	{
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	// Nur CLM-Admin hat Zugriff auf Toolbar
	//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 	{       
	 
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');

	JToolBarHelper::title( JText::_( 'TITLE_MTURNIERE' ), 'clm_headmenu_mturnier.png' );
	$clmAccess->accesspoint = 'BE_teamtournament_edit_fixture';
	if($clmAccess->access() !== false) {
	JToolBarHelper::custom('paarung','edit.png','edit_f2.png',JText::_( 'LEAGUE_BUTTON_1' ),false);
	}
	$clmAccess->accesspoint = 'BE_teamtournament_edit_detail';
	if($clmAccess->access() !== false) {
		JToolBarHelper::custom('wertpunkte','default.png','apply_f2.png','LEAGUE_BUTTON_W',false);  //klkl
	}	
	$clmAccess->accesspoint = 'BE_teamtournament_edit_round';
	if($clmAccess->access() !== false) {
		JToolBarHelper::custom('runden','back.png','edit_f2.png',JText::_( 'LEAGUE_BUTTON_2' ),false);
		JToolBarHelper::custom('del_runden','cancel.png','unarchive_f2.png',JText::_( 'LEAGUE_BUTTON_3' ),false);
	}
	$clmAccess->accesspoint = 'BE_teamtournament_edit_detail';
	if($clmAccess->access() !== false) {
		//JToolBarHelper::custom( 'daten_dsb_API', 'refresh.png', 'refresh_f2.png', JText::_( 'DB_BUTTON_DWZ_UPDATE_API'),false );
		JToolBarHelper::custom( 'daten_dsb_SOAP', 'refresh.png', 'refresh_f2.png', JText::_( 'DB_BUTTON_DWZ_UPDATE_SOAP'),false );
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'sortByTWZ', 'copy.png', 'copy_f2.png', JText::_('MTURN_BUTTON_S'), false);
	}
	$clmAccess->accesspoint = 'BE_teamtournament_create';
	if($clmAccess->access() !== false) {
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', JText::_( 'LEAGUE_BUTTON_4' ) );
	}
	$clmAccess->accesspoint = 'BE_teamtournament_delete';
	if($clmAccess->access() !== false) {
		JToolBarHelper::custom('remove','delete.png','delete_f2.png',JText::_( 'MTURN_BUTTON_5' ),false);
	}
	// JToolBarHelper::editListX();
	$clmAccess->accesspoint = 'BE_teamtournament_create';
	if($clmAccess->access() !== false) {
	JToolBarHelper::custom('add','new.png','new_f2.png',JText::_( 'MTURN_BUTTON_6' ),false);
		}
	JToolBarHelper::help( 'screen.clm.mturnier' );
	}

function mturniere(&$rows, &$lists, &$pageNav, &$option)
	{
	$mainframe	= JFactory::getApplication();
	CLMViewMTurniere::setMTurniereToolbar();
	$user =& JFactory::getUser();
	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$val	= $config->get('menue',1);

	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();

	//Ordering allowed ?
	$ordering = ($lists['order'] == 'a.ordering');

	JHtml::_('behavior.tooltip');
	?>
	<form action="index.php?option=com_clm&section=mturniere" method="post" name="adminForm" id="adminForm">
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
			<?php echo JText::_( 'JGRID_HEADING_ROW_NUMBER' ); ?>		</th>
		<th width="10">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />		</th>
		<th class="title">
			<?php echo JHtml::_('grid.sort', JText::_( 'MTURN_OVERVIEW_LEAGUE' ), 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="9%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_SEASON' ), 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="9%">
			<?php echo JHtml::_('grid.sort', JText::_( 'MTURN_OVERVIEW_MODE' ), 'a.runden_modus', @$lists['order_Dir'], @$lists['order'] ); ?> </th>
		<th width="9%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_ROUNDS' ), 'a.runden', @$lists['order_Dir'], @$lists['order'] ); ?><br />(<?php echo JText::_( 'LEAGUE_OVERVIEW_DG' ); ?>)</th>
		<th width="9%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_TEAMS' ), 'a.teil', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="5%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_STAMM' ), 'a.stamm', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="5%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_ERSATZ' ), 'a.ersatz', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="3%">
			<?php echo JHtml::_('grid.sort', JText::_( 'MTURN_OVERVIEW_TL' ), 'a.sl', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_MAIL' ), 'a.mail', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="4%">
			<?php echo JHtml::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_HINT' ), 'a.bemerkungen', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>

		<th width="6%">
		<?php echo JHtml::_('grid.sort',   'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
<?php 	$clmAccess->accesspoint = 'BE_teamtournament_edit_round';
		if($clmAccess->access() === true) {
		//if (CLM_usertype === 'admin') { ?>
		<th width="8%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
			<?php echo JHtml::_('grid.order',  $rows ); ?>		</th>
<?php	} ?>
		<th width="1%" nowrap="nowrap">
			<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		</tr>
		</thead>

		<tfoot>
		<tr>
		<td colspan="16">
			<?php echo $pageNav->getListFooter(); ?>		</td>
		</tr>
		</tfoot>

		<tbody>
		<?php
		$k = 0;
	if ($val == 1) { $menu ='index.php?option=com_clm&section=runden&liga=';
				}
		else { $menu ='index.php?option=com_clm&section=mturniere&task=edit&cid[]=';
			}

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link = JRoute::_( $menu . $row->id );
			$checked 	= JHtml::_('grid.checkedout',   $row, $i );
			$published 	= JHtml::_('grid.published', $row, $i );
			?>
			<tr class="<?php echo 'row'. $k; ?>">
			<td align="center">
				<?php echo $pageNav->getRowOffset( $i ); ?>			</td>
			<td>
				<?php echo $checked; ?>
            </td>
			<td>
				<?php
// 				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
				// Nur CLM-Admin darf hier zugreifen 
				//if (!JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 	{       
				$clmAccess->accesspoint = 'BE_teamtournament_edit_detail';
				if (($row->sl != CLM_ID AND $clmAccess->access() !== true ) OR ($clmAccess->access() === false)) {
					echo $row->name;} 
				else {	
				
					$ligenedit ='index.php?option=com_clm&section=mturniere&task=edit&cid[]=' . $row->id;
					
					?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'MTURN_OVERVIEW_TIP' );?>::<?php echo $row->name; ?>">
					<a href="<?php echo $ligenedit; ?>">
						<?php echo $row->name; ?>
					</a>
				<?php } ?>
				</span>
				<?php //} ?>			
            </td>
			<td align="center"><?php echo $row->saison;?></td>
			<td align="center"><?php echo JText::_( 'MTURN_PAIRING_MODE_'.($row->runden_modus + 1) );?></td>
			<td align="center">
			<?php
				$clmAccess->accesspoint = 'BE_teamtournament_edit_result';
				if (($row->sl != CLM_ID AND $clmAccess->access() !== true ) OR ($clmAccess->access() === false)) {
			if ( $row->durchgang > 1 ) { echo $row->durchgang."&nbsp;x&nbsp;"; } ?><?php echo $row->runden."&nbsp;".JText::_( 'SWT_RUNDEN' );
			} else { ?>
            <a href="<?php echo $link; ?>">
				<?php if ( $row->durchgang > 1 ) { echo $row->durchgang."&nbsp;x&nbsp;"; } ?><?php echo $row->runden."&nbsp;".JText::_( 'SWT_RUNDEN' );?>
            </a><?php } if ($row->rnd == '0') { ?><br /><?php echo '('.JText::_( 'LEAGUE_OVERVIEW_NOTCREATED' ).')';?><?php }?>
            </td>
			<td align="center"><?php echo $row->teil;?></td>
	 	 	<td align="center"><?php echo $row->stamm;?></td>
			<td align="center"><?php echo $row->ersatz;?></td>
			<td align="center"><?php echo $row->sl;?></td>
			<td align="center">
				<?php if ($row->mail == '1') 
				{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
				else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>			</td>
			<td align="center">
				<?php if ($row->bemerkungen <> '') 
				{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
				else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>			</td>

			<td align="center">
				<?php echo $published;?>			</td>
<?php
// Nur CLM-Admin darf hier zugreifen 
	if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 	{   ?>
	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, ($row->sid == @$rows[$i-1]->sid), 'orderup()', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->sid == @$rows[$i+1]->sid), 'orderdown()', 'Move Down', $ordering ); ?></span>
	<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />					</td>
<?php		} ?>
					<td align="center">
						<?php echo $row->id; ?>					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>



    <input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />

		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}


function setMTurnierToolbar()
	{
	if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
	else { $text = JText::_( 'New' );}
	
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
	JToolBarHelper::title( JText::_( 'MTURN_BUTTON_7' ).': <small><small>[ '. $text.' ]</small></small>', 'clm_headmenu_mturnier.png' );
	JToolBarHelper::save( 'save' );
	if (JRequest::getVar( 'task') == 'new') {
	JToolBarHelper::apply( 'apply' );
	}
	JToolBarHelper::cancel();
	}

function mturnier(&$row, $lists, $option )
	{
	CLMViewMTurniere::setMTurnierToolbar();
	JRequest::setVar( 'hidemainmenu', 1 );

	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$rang	= $config->get('rangliste',0);
	$sl_mail= $config->get('sl_mail',0);
	?>
	<?php 
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $row->params);
	$row->params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$row->params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($row->params['btiebr1']) OR $row->params['btiebr1'] == 0) {   //Standardbelegung
		$row->params['btiebr1'] = 1;
		$row->params['btiebr2'] = 2;
		$row->params['btiebr3'] = 3;
		$row->params['btiebr4'] = 4;
		$row->params['btiebr5'] = 0;
		$row->params['btiebr6'] = 0; }
	if (!isset($row->params['bnhtml']) OR $row->params['bnhtml'] == 0) {   //Standardbelegung
		$row->params['bnhtml'] = 5; }
	if (!isset($row->params['bnpdf']) OR $row->params['bnpdf'] == 0) {   //Standardbelegung
		$row->params['bnpdf'] = 4; }
	if (!isset($row->params['anz_sgp']))  {   //Standardbelegung
		$row->params['anz_sgp'] = 1; }
	if (!isset($row->params['color_order']))  {   //Standardbelegung
		$row->params['color_order'] = '1'; }
	?>
	
	<script language="javascript" type="text/javascript">

	<?php if (JVersion::isCompatible("1.6.0")) { ?>
		 Joomla.submitbutton = function (pressbutton) { 
	<?php } else { ?>
		 function submitbutton(pressbutton) {
	<?php } ?>		
			var form = document.adminForm;
			var i;
			var potenzg = 1;
			var potenzk = 1;
			for (i = 1; i <= form.runden.value; i++) {
					potenzk = potenzg +1;
					potenzg = potenzg * 2; }
			var potenzg5 = 1;
			var potenzk5 = 1;
			for (i = 1; i <= (form.runden.value -1); i++) {
					potenzk5 = potenzg5 +1;
					potenzg5 = potenzg5 * 2; }
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo JText::_( 'MTURN_HINT_1', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_2', true ); ?>" );
			} else if (form.stamm.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_3', true ); ?>" );
			} else if (form.ersatz.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_4', true ); ?>" );
			} else if (form.teil.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_5', true ); ?>" );
			} else if (form.runden.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_6', true ); ?>" );
			} else if ( getSelectedValue('adminForm','durchgang') == "" ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_7', true ); ?>" );
			} else if ( form.runden_modus.value == 4 && form.teil.value > potenzg ) {
				alert( "<?php echo JText::_( 'MTURN_HINT_8', true ); ?>" ); 
			} else if ( form.runden_modus.value == 4 && form.teil.value < potenzk ) {
				alert( "<?php echo JText::_( 'MTURN_HINT_9', true ); ?>" ); 
			} else if ( form.runden_modus.value == 5 && form.teil.value > potenzg5 ) {
				alert( "<?php echo JText::_( 'MTURN_HINT_8', true ).'\n'.JText::_( 'MTURN_HINT_15', true ); ?>" ); 
			} else if ( form.runden_modus.value == 5 && form.teil.value < potenzk5 ) {
				alert( "<?php echo JText::_( 'MTURN_HINT_9', true ).'\n'.JText::_( 'MTURN_HINT_15', true ); ?>" ); 
			} else if (form.anz_sgp.value < 0 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_8', true ); ?>" );
			} else if (form.anz_sgp.value > 20 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_8', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		 
		</script>

 <form action="index.php" method="post" name="adminForm" id="adminForm">
  <div class="width-60 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'MTURN_DATA' ); ?></legend>
      <table class="adminlist">

	<tr>
	<td width="20%" nowrap="nowrap">
	<label for="name"><?php echo JText::_( 'MTURN_NAME' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="name" id="name" size="30" maxlength="30" value="<?php echo $row->name; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="sl"><?php echo JText::_( 'MTURN_CHIEF' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['sl']; ?>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="saison"><?php echo JText::_( 'LEAGUE_SEASON' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['saison']; ?>
	</td>

	<td nowrap="nowrap">
	<label for="rang"><?php echo JText::_( 'LEAGUE_LIST_TYPE' ); ?></label>
	</td><td colspan="2">
	<?php if ($rang == 0) { ?>
	<?php echo $lists['gruppe']; ?>
	</td>
	</tr>
	<?php } if ($rang == 1) { echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_RANK' ); ?>
	</td>
	</tr>
	<input type="hidden" name="rang" value="1" />
	<?php }
	if ($rang == 2) { echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_LIST' ); ?>
	</td>
	</tr>
	<input type="hidden" name="rang" value="0" />
	<?php } ?>

	<tr>
	<td nowrap="nowrap">
	<label for="teil"><?php echo JText::_( 'LEAGUE_TEAMS' ); ?></label>
	</td><td colspan="5">
	<input class="inputbox" type="text" name="teil" id="teil" size="4" maxlength="4" value="<?php echo $row->teil; ?>" />
	</td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="stammspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_1' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="stamm" id="stamm" size="4" maxlength="4" value="<?php echo $row->stamm; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="erstatzspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_2' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="ersatz" id="ersatz" size="4" maxlength="4" value="<?php echo $row->ersatz; ?>" />
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="runden"><?php echo JText::_( 'LEAGUE_ROUNDS' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="runden" id="runden" size="4" maxlength="4" value="<?php echo $row->runden; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="durchgang"><?php echo JText::_( 'LEAGUE_DG' ); ?></label>
	</td><td colspan="2">
		<select name="durchgang" id="durchgang" value="<?php echo $row->durchgang; ?>" size="1">
		<option <?php if ($row->durchgang < 2) {echo 'selected="selected"';} ?>>1</option>
		<option <?php if ($row->durchgang == 2) {echo 'selected="selected"';} ?>>2</option>
		</select>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="params['color_order']"><?php echo JText::_( 'LEAGUE_COLOR_ORDER' ); ?></label>
	</td><td colspan="2">
		<select name="params['color_order']" id="params['color_order']" value="<?php echo $row->params['color_order']; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="1" <?php if ($row->params['color_order'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_1' );?></option>
		<option value="2" <?php if ($row->params['color_order'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_2' );?></option>
		<option value="3" <?php if ($row->params['color_order'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_3' );?></option>
		<option value="4" <?php if ($row->params['color_order'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_4' );?></option>
		<option value="5" <?php if ($row->params['color_order'] == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_5' );?></option>
		<option value="6" <?php if ($row->params['color_order'] == 6) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_6' );?></option>
		</select>
	</td>
	<td nowrap="nowrap"></td><td colspan="2"></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="runden_modus"><?php echo JText::_( 'MTURN_PAIRING_MODE' ); ?></label>
	</td><td colspan="2">
		<select name="runden_modus" id="runden_modus" value="<?php echo $row->runden_modus; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="1" <?php if ($row->runden_modus == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_2' );?></option>
		<option value="2" <?php if ($row->runden_modus == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_3' );?></option>
		<option value="3" <?php if ($row->runden_modus == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_4' );?></option>
		<option value="4" <?php if ($row->runden_modus == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_5' );?></option>
		<option value="5" <?php if ($row->runden_modus == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_PAIRING_MODE_6' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="heim"><?php echo JText::_( 'LEAGUE_HOME' ); ?></label>
	</td><td colspan="2"><fieldset class="radio">
		<?php echo $lists['heim']; ?>
	</fieldset></td>
	</tr>
	
    <tr>
	<td nowrap="nowrap">
	<label for="tiebr1"><?php echo '1.'.JText::_( 'MTURN_TIEBREAKER' ); ?></label>
	</td><td colspan="2">
		<select name="tiebr1" id="tiebr1" value="<?php echo $row->tiebr1; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="0" <?php if ($row->tiebr1 == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_SELECT_TIEBR_0' );?></option>
		<option value="1" <?php if ($row->tiebr1 == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_1' );?></option>
		<option value="11" <?php if ($row->tiebr1 == 11) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_11' );?></option>
		<option value="2" <?php if ($row->tiebr1 == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_2' );?></option>
		<option value="23" <?php if ($row->tiebr1 == 23) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_23' );?></option>
		<option value="4" <?php if ($row->tiebr1 == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_4' );?></option>
		<option value="5" <?php if ($row->tiebr1 == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_5' );?></option>
		<option value="6" <?php if ($row->tiebr1 == 6) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_6' );?></option>
		<option value="3" <?php if ($row->tiebr1 == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_3' );?></option>
		<option value="25" <?php if ($row->tiebr1 == 25) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_25' );?></option>
		<option value="51" <?php if ($row->tiebr1 == 51) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_51' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="tiebr2"><?php echo '2.'.JText::_( 'MTURN_TIEBREAKER' ); ?></label>
	</td>
	<td colspan="2">
		<select name="tiebr2" id="tiebr1" value="<?php echo $row->tiebr2; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="0" <?php if ($row->tiebr2 == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_SELECT_TIEBR_0' );?></option>
		<option value="1" <?php if ($row->tiebr2 == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_1' );?></option>
		<option value="11" <?php if ($row->tiebr2 == 11) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_11' );?></option>
		<option value="2" <?php if ($row->tiebr2 == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_2' );?></option>
		<option value="23" <?php if ($row->tiebr2 == 23) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_23' );?></option>
		<option value="4" <?php if ($row->tiebr2 == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_4' );?></option>
		<option value="5" <?php if ($row->tiebr2 == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_5' );?></option>
		<option value="6" <?php if ($row->tiebr2 == 6) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_6' );?></option>
		<option value="3" <?php if ($row->tiebr2 == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_3' );?></option>
		<option value="25" <?php if ($row->tiebr2 == 25) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_25' );?></option>
		<option value="51" <?php if ($row->tiebr2 == 51) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_51' );?></option>
		</select>
	</td>
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="tiebr3"><?php echo '3.'.JText::_( 'MTURN_TIEBREAKER' ); ?></label>
	</td><td colspan="2">
		<select name="tiebr3" id="tiebr1" value="<?php echo $row->tiebr3; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="0" <?php if ($row->tiebr3 == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_SELECT_TIEBR_0' );?></option>
		<option value="1" <?php if ($row->tiebr3 == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_1' );?></option>
		<option value="11" <?php if ($row->tiebr3 == 11) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_11' );?></option>
		<option value="2" <?php if ($row->tiebr3 == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_2' );?></option>
		<option value="23" <?php if ($row->tiebr3 == 23) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_23' );?></option>
		<option value="4" <?php if ($row->tiebr3 == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_4' );?></option>
		<option value="5" <?php if ($row->tiebr3 == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_5' );?></option>
		<option value="6" <?php if ($row->tiebr3 == 6) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_6' );?></option>
		<option value="3" <?php if ($row->tiebr3 == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_3' );?></option>
		<option value="25" <?php if ($row->tiebr3 == 25) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_25' );?></option>
		<option value="51" <?php if ($row->tiebr3 == 51) {echo 'selected="selected"';} ?>><?php echo JText::_( 'MTURN_TIEBR_51' );?></option>
		</select>
	</td>
	<td colspan="2">
	</td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="ersatz_regel"><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL' ); ?></label>
	</td>
	<td colspan="2">
		<select name="ersatz_regel" id="ersatz_regel" value="<?php echo $row->ersatz_regel; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="0" <?php if ($row->ersatz_regel == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_0' );?></option>
		<option value="1" <?php if ($row->ersatz_regel == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_1' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="anz_sgp"><?php echo JText::_( 'LEAGUE_ANZ_SGP' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="anz_sgp" id="anz_sgp" size="4" maxlength="4" value="<?php echo $row->params['anz_sgp'] ?>" />
	</td>
	</tr>
	
		</table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_VALUATION' ); ?></legend>
      <table class="adminlist">

	<tr>
	<td nowrap="nowrap">&nbsp;</td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_1' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_2' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_3' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_4' );?></td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="punkte_modus"><?php echo JText::_( 'LEAGUE_MATCH_VALUATION' ); ?></label>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="sieg" id="sieg" size="4" maxlength="4" value="<?php if($row->sieg !=""){ echo $row->sieg;} else { echo "1";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="remis" id="remis" size="4" maxlength="4" value="<?php if($row->remis !=""){ echo $row->remis;} else { echo "0.5";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="nieder" id="nieder" size="4" maxlength="4" value="<?php if($row->nieder !=""){ echo $row->nieder;} else { echo "0";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="antritt" id="antritt" size="4" maxlength="4" value="<?php if($row->antritt !=""){ echo $row->antritt;} else { echo "0";}; ?>" /></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="man_punkte"><?php echo JText::_( 'LEAGUE_TEAM_POINTS' ); ?></label>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_sieg" id="man_sieg" size="4" maxlength="4" value="<?php if($row->man_sieg !=""){ echo $row->man_sieg;} else { echo "2";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_remis" id="man_remis" size="4" maxlength="4" value="<?php if($row->man_remis !=""){ echo $row->man_remis;} else { echo "1";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_nieder" id="man_nieder" size="4" maxlength="4" value="<?php if($row->man_nieder !=""){ echo $row->man_nieder;} else { echo "0";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_antritt" id="man_antritt" size="4" maxlength="4" value="<?php if($row->man_antritt !=""){ echo $row->man_antritt;} else { echo "0";}; ?>" /></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="sieg_bed"><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="sieg_bed" id="sieg_bed" value="<?php echo $row->sieg_bed;  ?>" size="1">
		<option value="1" <?php if ($row->sieg_bed == 1) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_1' );?></option>
		<option value="2" <?php if ($row->sieg_bed == 2) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_2' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="b_wertung"><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS' ); //klkl?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="b_wertung" id="b_wertung" value="<?php echo $row->b_wertung; ?>" size="1">
		<option value="0" <?php if ($row->b_wertung == 0) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_0' );?></option>
		<option value="3" <?php if ($row->b_wertung == 3) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_3' );?></option>
		<option value="4" <?php if ($row->b_wertung == 4) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_4' );?></option>
		</select>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="auf"><?php echo JText::_( 'LEAGUE_UP' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="auf" id="auf" size="10" maxlength="10" value="<?php echo $row->auf; ?>" />
	</td>

	<td nowrap="nowrap">
	<label for="color_auf"><?php echo JText::_( 'LEAGUE_UP_POSSIBLE' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="auf_evtl" id="auf_evtl" size="10" maxlength="10" value="<?php echo $row->auf_evtl; ?>" />
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="ab"><?php echo JText::_( 'LEAGUE_DOWN' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="ab" id="ab" size="10" maxlength="10" value="<?php echo $row->ab; ?>" />
	</td>

	<td nowrap="nowrap">
	<label for="color_ab"><?php echo JText::_( 'LEAGUE_DOWN_POSSIBILE' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="ab_evtl" id="ab_evtl" size="10" maxlength="10" value="<?php echo $row->ab_evtl; ?>" />
	</td>
	</tr>
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_BOARD_VALUATION' ); ?></legend>
      <table class="adminlist">
	<tr>
	<td nowrap="nowrap">
	<label for="params['btiebr1']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION1' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr1']" id="params['btiebr1']" value="<?php echo $row->params['btiebr1']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr1'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params['btiebr2']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION2' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr2']" id="params['btiebr2']" value="<?php echo $row->params['btiebr2']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr2'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params['btiebr3']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION3' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr3']" id="params['btiebr3']" value="<?php echo $row->params['btiebr3']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr3'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params['btiebr4']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION4' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr4']" id="params['btiebr4']" value="<?php echo $row->params['btiebr4']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr4'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params[btiebr5]"><?php echo JText::_( 'LEAGUE_BOARD_COLUMN5' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr5']" id="params['btiebr5']" value="<?php echo $row->params['btiebr5']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr5'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params['btiebr6']"><?php echo JText::_( 'LEAGUE_BOARD_COLUMN6' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr6']" id="params['btiebr6']" value="<?php echo $row->params['btiebr6']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr6'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="params['bnhtml']"><?php echo JText::_( 'LEAGUE_BOARD_POSITIONS_LIST' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="params['bnhtml']" id="params['bnhtml']" size="2" maxlength="2" value="<?php echo $row->params['bnhtml']; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="params['bnpdf']"><?php echo JText::_( 'LEAGUE_BOARD_POSITIONS_PDF' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="params['bnpdf']" id="params['bnpdf']" size="2" maxlength="2" value="<?php echo $row->params['bnpdf']; ?>" />
	</td>
	</tr>
	
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_PREFERENCES' ); ?></legend>
      <table class="adminlist">

      <tr>
	<td nowrap="nowrap">
	<label for="anzeige_ma"><?php echo JText::_( 'LEAGUE_SHOW_PLAYERLIST' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['anzeige_ma']; ?>
	</fieldset></td>
	</tr>

    <tr>
	<td nowrap="nowrap">
	<label for="mail"><?php echo JText::_( 'LEAGUE_MAIL' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['mail']; ?>
	</fieldset></td>
	</tr>

<?php if ($sl_mail == "1") { ?>
	<tr>
	<td nowrap="nowrap">
	<label for="sl_mail"><?php echo JText::_( 'LEAGUE_MAIL_CHIEF' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['sl_mail']; ?>
	</fieldset></td>
	</tr>
<?php } else { ?>
	<input type="hidden" name="sl_mail" value="0" />
<?php } ?>
	<tr>
	<td nowrap="nowrap">
	<label for="order"><?php echo JText::_( 'LEAGUE_ORDERING' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['order']; ?>
	</fieldset></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="published"><?php echo JText::_( 'LEAGUE_PUBLISHED' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['published']; ?>
	</fieldset></td>
	</tr>


	</table>
  </fieldset>
  </div>

  <div class="width-40 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'REMARKS' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
	
  </fieldset>
  
    <fieldset>
  	<legend><?php echo JText::_( 'LEAGUE_HINTS' ); ?></legend>
  	<b><?php echo JText::_( 'MTURN_HINTS_FINE_RANKINGS' ); ?></b>
  	<?php echo JText::_( 'MTURN_HINTS_01' ); ?>
  	<?php echo JText::_( 'MTURN_HINTS_02' ); ?>
  	<?php echo JText::_( 'MTURN_HINTS_03' ); ?>
  	<?php echo JText::_( 'MTURN_HINTS_04' ); ?>
  	<?php echo JText::_( 'MTURN_HINTS_05' ); ?>
  	<?php echo JText::_( 'MTURN_HINTS_06' ); ?>
  	<?php echo JText::_( 'MTURN_HINTS_07' ); ?>
	<br><br><br>
  	<b><?php echo JText::_( 'MTURN_HINTS_PAIRING_MODE' ); ?></b>
  	<?php echo JText::_( 'LEAGUE_HINTS_1' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_2' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_3' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_4' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_5' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_6' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_7' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_8' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_9' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_10' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_11' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_12' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_20' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_21' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_22' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_23' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_24' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_25' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_26' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_27' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_28' ); ?>

  	</legend>

  </div>

<div class="clr"></div>

	<input type="hidden" name="section" value="mturniere" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="sid_alt" value="<?php echo $row->sid; ?>" />
	<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
	<input type="hidden" name="client_id" value="<?php echo $row->cid; ?>" />
	<input type="hidden" name="rnd" value="<?php echo $row->rnd; ?>" />
	<input type="hidden" name="task" value="" />
	<?php $row->liga_mt = 1; //mtmt ?>
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php }}
?>