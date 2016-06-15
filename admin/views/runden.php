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

class CLMViewRunden
{
public static function setRundenToolbar($sid, $params_round_date)
  {
	$clmAccess = clm_core::$access;

	// Menubilder laden
	clm_core::$load->load_css("icons_images");
	JToolBarHelper::title( JText::_( 'TITLE_RUNDE' ), 'clm_settings.png' );
	if (clm_core::$db->saison->get($sid)->published == 1 AND clm_core::$db->saison->get($sid)->archiv == 0) {
		if($clmAccess->access('BE_league_edit_fixture') !== false) {
			JToolBarHelper::custom('paarung','edit.png','edit_f2.png',JText::_( 'LEAGUE_BUTTON_1' ),false);
			if  ($params_round_date == '1') {
				JToolBarHelper::custom('pairingdates','edit.png','edit_f2.png',JText::_( 'ROUND_EDIT_PAIRING_DATES' ),false);
			}
		}
		JToolBarHelper::custom('check','preview.png','upload_f2.png','RUNDE_CHECK',false);
		// Nur CLM-Admin hat Zugriff auf Toolbar
	  if($clmAccess->access('BE_league_edit_round') !== false) {
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		JToolBarHelper::deleteList();
		// JToolBarHelper::editList();
		JToolBarHelper::addNew();
	  }
	}
		JToolBarHelper::help( 'screen.clm.runde' );
	}

public static function runden( $rows, $lists, $pageNav, $option )
	{
		$mainframe	= JFactory::getApplication();
		$cliga 		= intval(JRequest::getVar( 'liga' ));

		// Liga-Parameter holen 
		$db 		=JFactory::getDBO();
		$sql = "SELECT params FROM #__clm_liga as l"
			." WHERE l.id = ".$rows[0]->liga;
		$db->setQuery( $sql );
		$tparams = $db->loadObjectList();
		//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $tparams[0]->params);
		$lparams = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$lparams[$key] = substr($value,$ipos+1);
			}
		}	
		if (!isset($lparams['round_date']))  {   //Standardbelegung
			$lparams['round_date'] = '0'; }

		CLMViewRunden::setRundenToolbar($rows[0]->sid, $lparams['round_date']);
		$user =JFactory::getUser();
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$val=$config->menue;
	$dropdown=$config->dropdown;

		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHtml::_('behavior.tooltip');

	if(isset($rows[0]) && $rows[0]->sid_pub =="0" AND $val !=0) {
	JError::raiseNotice( 6000,  JText::_( 'RUNDE_ERROR_SAISON_UNPUBLISHED' ));
	}
		?>
		<form action="index.php?option=com_clm&section=runden" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
		if ($val ==0 OR ( $val ==1 AND $dropdown == 1)) {
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['lid'];
					}
			echo "&nbsp;&nbsp;&nbsp;".$lists['state'];
				?>
			</td>
		</tr>
		</table>

			<table class="adminlist">
			<thead>
				<tr>
					<th width="3%">
						#
					</th>
					<th width="3%">
						<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort',   'RUNDE', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_NR', 'a.nr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="7%">
						<?php echo JHtml::_('grid.sort',   'JDATE', 'a.datum', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				<?php if ($lparams['round_date'] == '0')  { ?>
					<th width="4%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_STARTTIME', 'a.startzeit', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				<?php }
					if ($lparams['round_date'] == '1')  { ?>
					<th width="7%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_ENDDATUM', 'a.enddatum', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				<?php } ?>
					<th width="8%">
						<?php echo JHtml::_('grid.sort',   'ERGEBNISSE', 'e.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_LIGA', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="9%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_MELDUNG', 'a.meldung', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_SL_OK', 'a.sl_ok', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHtml::_('grid.sort',   'RUNDE_INFO', 'a.bemerkungen', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHtml::_('grid.sort',   'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="9%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
						<?php echo JHtml::_('grid.order',  $rows ); ?>
					</th>
					<th width="3%" nowrap="nowrap">
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
				$row 	= JTable::getInstance( 'runden', 'TableCLM' );
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row->load( $rows[$i]->id );
			if ($val == 0) { $menu = 'index.php?option=com_clm&section=runden&task=edit&cid[]='. $row->id; }
			else {
				if ($rows[$i]->durchgang >1 ) {
					if($row->nr > (3 * $rows[$i]->runden)) {$menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr-(3 * $rows[$i]->runden)).'&dg=4&liga='.$row->liga;}
					elseif($row->nr > (2 * $rows[$i]->runden)) {$menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr-(2 * $rows[$i]->runden)).'&dg=3&liga='.$row->liga;}
					elseif($row->nr > $rows[$i]->runden) {$menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr-$rows[$i]->runden).'&dg=2&liga='.$row->liga;}
					else { $menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr).'&dg=1&liga='.$row->liga; }
					}
				else {
					$menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr).'&dg=1'.'&liga='.$row->liga;
				}}

				$link 		= JRoute::_( $menu );
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
	
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Runde' );?>::<?php echo $row->name; ?>">
							<a href="index.php?option=com_clm&section=runden&task=edit&cid[]=<?php echo $row->id; ?>&liga=<?php echo $cliga; ?>">
								<?php echo $row->name; ?></a></span>
	
					</td>

					<td align="center">
						<?php echo $row->nr;?>
					</td>
					<td align="center">
						<?php echo $row->datum;?>
					</td>
				<?php if ($lparams['round_date'] == '0')  { ?>
					<td align="center">
						<?php echo substr($row->startzeit,0,5);?>
					</td>
				<?php } 
					if ($lparams['round_date'] == '1')  { ?>
					<td align="center">
						<?php echo $row->enddatum;?>
					</td>
				<?php } ?>
					<td align="center">
						<a href="<?php echo $link; ?>"><?php echo JText::_( 'ERGEBNISSE' );?></a>
					</td>
					<td align="center">
						<?php echo $rows[$i]->liga_name;?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->saison;?>
					</td>
					<td align="center">
						<?php if ($row->meldung=='1') 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php if ($row->sl_ok=='1') 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php if ($row->bemerkungen<>'') 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php echo $published;?>
					</td>

	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, (isset($rows[$i-1]) AND $rows[$i]->liga == $rows[$i-1]->liga), 'orderup()', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, (isset($rows[$i+1]) AND $rows[$i]->liga == $rows[$i+1]->liga), 'orderdown()', 'Move Down', $ordering ); ?></span>
	<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="4" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
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
		<input type="hidden" name="liga" value="<?php echo JRequest::getVar( 'liga' ); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

public static function setRundeToolbar($sid)
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'RUNDE' ).': [ '. $text.' ]' );
		if (clm_core::$db->saison->get($sid)->published == 1 AND clm_core::$db->saison->get($sid)->archiv == 0) {
			JToolBarHelper::save();
			JToolBarHelper::apply();
		}
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function runde( &$row,$lists, $option )
	{
		CLMViewRunden::setRundeToolbar($row->sid);
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		$cliga 		= intval(JRequest::getVar( 'liga' ));
		// Liga-Parameter holen 
		$db 	=JFactory::getDBO();
		$sql = "SELECT params FROM #__clm_liga as l"
			." WHERE l.id = ".$row->liga;
		$db->setQuery( $sql );
		$tparams = $db->loadObjectList();
		//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $tparams[0]->params);
		$lparams = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
				if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
				$lparams[$key] = substr($value,$ipos+1);
			}
		}	
		if (!isset($lparams['round_date']))  {   //Standardbelegung
			$lparams['round_date'] = '0'; }

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
				alert( "<?php echo JText::_( 'RUNDE_NAME_ANGEBEN', true ); ?>" );
			} else if (form.nr.value == "") {
				alert( "<?php echo JText::_( 'RUNDE_NUMMER_ANGEBEN', true ); ?>" );
			} else if (form.datum.value == "") {
				alert( "<?php echo JText::_( 'RUNDE_DATE_ANGEBEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'RUNDE_SAISON_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','liga') == 0 ) {
				alert( "<?php echo JText::_( 'RUNDE_LIGA_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RUNDE_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="30" maxlength="60" value="<?php echo $row->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="nr"><?php echo JText::_( 'RUNDE_NR' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="nr" id="nr" size="5" maxlength="5" value="<?php echo $row->nr; ?>" />
			</td>
		</tr>

		   <tr>
            		<td width="100" class="key">
                	<label for="datum">
                    	<?php echo JText::_( 'RUNDE_SPIELTAG' ).' : '; ?>
                	</label>
            		</td>
            		<td>
                	<?php echo JHtml::_('calendar', $row->datum, 'datum', 'datum', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
            		</td>
        	</tr>
	<?php if ($lparams['round_date'] == '0')  { ?>
		<tr>
			<td class="key" nowrap="nowrap">
				<label for="startzeit" >
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'RUNDE_STARTTIME_HINT' );?>">
					<?php echo JText::_( 'RUNDE_STARTTIME' ).' : '; ?></span>
				</label>
			</td>
			<td>
			<input class="inputbox" type="time" name="startzeit" id="startzeit" size="8" maxlength="10" value="<?php echo substr($row->startzeit,0,5); ?>"  />
			</td>
        </tr>
	<?php }
		if ($lparams['round_date'] == '1')  { ?>
	   <tr>
           	<td width="100" class="key">
               	<label for="enddatum">
                  	<?php echo JText::_( 'RUNDE_ENDDATUM' ).' : '; ?>
               	</label>
           	</td>
           	<td>
				<?php if ($row->enddatum < '1970-01-02' and $row->datum > '1970-01-01') $row->enddatum = $row->datum; ?>
               	<?php echo JHtml::_('calendar', $row->enddatum, 'enddatum', 'enddatum', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
			</td>
   		</tr>
	<?php } ?>
		<tr>
       		<td width="100" class="key">
               	<label for="deadlineday">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'RUNDE_DEADLINEDAY_HINT' );?>">
                   	<?php echo JText::_( 'RUNDE_DEADLINEDAY' ).' : '; ?>
               	</label>
       		</td>
       		<td>
               	<?php echo JHtml::_('calendar', $row->deadlineday, 'deadlineday', 'deadlineday', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
				<input class="inputbox" type="time" name="deadlinetime" id="deadlinetime" size="8" maxlength="10" value="<?php echo substr($row->deadlinetime,0,5); ?>"  />
       		</td>
       	</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'RUNDE_SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" ><label for="liga"><?php echo JText::_( 'RUNDE_LIGA' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['liga']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="verein"><?php echo JText::_( 'RUNDE_MELDUNG_MOEGLICH' ).' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['complete']; ?>
			</fieldset></td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="mf"><?php echo JText::_( 'RUNDE_SL_FREIGABE' ).' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['slok']; ?>
			</fieldset></td>
		</tr>
		
		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' ).' : '; ?></label>
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

		<input type="hidden" name="section" value="runden" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<!---		<input type="hidden" name="cid" value="<?php //echo $row->cid; ?>" />
		<input type="hidden" name="client_id" value="<?php //echo $row->cid; ?>" />
--->		<input type="hidden" name="task" value="" />
		<input type="hidden" name="slok_old" value="<?php echo $row->sl_ok; //klkl ?>" /> 
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

}
?>
