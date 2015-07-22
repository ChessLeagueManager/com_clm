<?php
defined('_JEXEC') or die('Restricted access');
?>
<div id="clm_content" >
<div id="iframedoc"></div>
<iframe frameborder="0" width="100%" height="600px" src="index.php?option=com_config&amp;view=component&amp;component=com_clm&amp;path=&amp;tmpl=component"></iframe>

<!--
<form action="index.php?option=com_clm&amp;controller=config" method="post" name="adminForm" autocomplete="off">
	
	<?php
/*	$pane =& JPane::getInstance('tabs'); 
	echo $pane->startPane( 'pane' );
		
		echo $pane->startPanel( JText::_('CONFIG_BASICS'), 'panel_basics' );
			require_once('config_basics.php'); 
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('CONFIG_FRONTEND'), 'panel_frontend' );
			require_once('config_frontend.php'); 
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('CONFIG_TOURN'), 'panel_turnier' );
			require_once('config_turnier.php'); 
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('CONFIG_TEMPLATE'), 'panel_template' );
			require_once('config_template.php'); 
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('CONFIG_UPGRADE'), 'panel_upgrade' );
			require_once('config_upgrade.php'); 
		echo $pane->endPanel();
	
	echo $pane->endPane();
*/	?>

	<div class="clr"></div>
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="config" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="config" />
	<?php //echo JHtml::_( 'form.token' ); ?>
</form>
-->
</div>
