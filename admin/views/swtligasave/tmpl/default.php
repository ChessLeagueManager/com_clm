<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$swt_file    = clm_core::$load->request_string('swt_file', '');
$update = clm_core::$load->request_int('update', 0);
$lid 	= clm_core::$load->request_int('lid', 0);
$swt_id = clm_core::$load->request_int('swt_id', 0);
$sid 	= clm_core::$load->request_int('sid', 0);
$par	= clm_core::$load->request_int('par', 0);
$runde	= clm_core::$load->request_int('runde', 0);
$mturnier = clm_core::$load->request_int('mturnier', 0);
$ungerade = clm_core::$load->request_int('ungerade', 0);

?>
<script language="javascript" type="text/javascript">
    
	Joomla.submitbutton = function (pressbutton) { 		
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Joomla.submitform( pressbutton );
            return;
        }
        // do field validation ( z.Z. nichts)
		
        Joomla.submitform( pressbutton );
        
    }
	
</script>

Der Import kann nun abgeschlossen werden.
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtligasave" />
	<input type="hidden" name="controller" value="swtligasave" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="swt_file" value="<?php echo $swt_file; ?>" />
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
    <input type="hidden" name="swt_id" value="<?php echo $swt_id; ?>" />
    <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
    <input type="hidden" name="runde" value="<?php echo $runde; ?>" />
	<input type="hidden" name="mturnier" value="<?php echo $mturnier; ?>" />
	<input type="hidden" name="ungerade" value="<?php echo $ungerade; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
	
</form>
