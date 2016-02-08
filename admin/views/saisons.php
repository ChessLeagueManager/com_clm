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

class CLMViewSaisons
{
public static function setSaisonsToolbar($countryversion)
	{
	
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

		JToolBarHelper::title( JText::_( 'Saison Manager' ), 'clm_headmenu_saison.png' );
	  if ($countryversion =="de") {
		JToolBarHelper::custom('dwz_del','cancel.png','unarchive_f2.png','RUNDE_DWZ_DELETE',false);	
		JToolBarHelper::custom('dwz_start','default.png','apply_f2.png','RUNDE_DWZ_APPLY',false);			
	  }
	/* Debugging / Testing
		JToolBarHelper::custom( 'change', 'upload.png', 'upload_f2.png', 'Status ändern' , false);
	*/
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
		JToolBarHelper::help( 'screen.clm.saison' );
	}

public static function saisons ( &$rows, &$lists, &$pageNav, $option )
	{
	// Nur CLM-Amin darf hier zugreifen
	if (!JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 
	{       
	 return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	 }
	 
		$mainframe	= JFactory::getApplication();
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		CLMViewSaisons::setSaisonsToolbar($countryversion);
		$user =JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHtml::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=saisons" method="post" name="adminForm" id="adminForm">

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
						<?php echo JHtml::_('grid.sort',   'SAISON', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',   'JDATE', 'a.datum', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="6%">
						<?php echo JHtml::_('grid.sort',   'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="6%">
						<?php echo JHtml::_('grid.sort',   'SAISON_ARCHIVE', 'a.archiv', @$lists['order_Dir'], @$lists['order'] ); ?>
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
			$row = JTable::getInstance('saisons', 'TableCLM');
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				//$row = &$rows[$i];
				// load the row from the db table
				$row->load( $rows[$i]->id );
				$link 		= JRoute::_( 'index.php?option=com_clm&section=saisons&task=edit&cid[]='. $row->id );
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
			 <span title="<?php echo JText::_( 'SAISON_EDIT' );?>"><a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>
					</td>
					
					<td align="center">
						<?php echo $row->datum; ?>
					</td>
					
					<td align="center">
						<?php echo $published;?>
					</td>
					<td align="center">
						<?php if ($row->archiv > 0) 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
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

public static function setSaisonToolbar()
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
	
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title(  JText::_( 'SAISON' ).': [ '. $text.' ]', 'clm_headmenu_saison.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function saison( &$row,$lists, $option)
	{
	// Nur CLM-Admin darf hier zugreifen (neue Saison)
	if (!JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 
	{       
	 return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	 }
		CLMViewSaisons::setSaisonToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>
	
		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'SAISON_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'SAISON' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $row->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' ).' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="archiv"><?php echo JText::_( 'SAISON_ARCHIVED' ).' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['archiv']; ?>
			</fieldset></td>
		</tr>
		<tr>
			<td class="key" width="20%" >
			<label for="datum"><?php echo JText::_( 'SAISON_DSB_DATE' ).' : '; ?></label>
			</td>
			<td>
			<?php echo JHtml::_('calendar', $row->datum, 'datum', 'datum', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'32', 'maxlength'=>'19')); ?>
            </td>
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

		<input type="hidden" name="section" value="saisons" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
} ?>
