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

$swt    = JRequest::getVar('swt', '', 'default', 'string');
$update = JRequest::getVar('update', 0);
$lid = JRequest::getVar('lid', 0);
$swt_id = JRequest::getVar('swt_id', 0, 'default', 'int');
$sid = JRequest::getVar('sid', 0, 'default', 'int');
$par	= JRequest::getVar('par', 0, 'default', 'int');
$runde	= JRequest::getVar('runde', 0, 'default', 'int');
$mturnier = JRequest::getVar('mturnier', 0, 'default', 'int');
$ungerade = JRequest::getVar('ungerade', false, 'default', 'bool');

?>
<script language="javascript" type="text/javascript">
    <!--
    function submitbutton(pressbutton) {
        var form = document.adminForm;
        submitform( pressbutton );
        return;
    }
	//-->
</script>

Der Import kann nun abgeschlossen werden.
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtligasave" />
	<input type="hidden" name="controller" value="swtligasave" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="swt" value="<?php echo $swt; ?>" />
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
    <input type="hidden" name="swt_id" value="<?php echo $swt_id; ?>" />
    <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
    <input type="hidden" name="runde" value="<?php echo $runde; ?>" />
	<input type="hidden" name="mturnier" value="<?php echo $mturnier; ?>" />
	<input type="hidden" name="ungerade" value="<?php echo $ungerade; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
	
</form>
