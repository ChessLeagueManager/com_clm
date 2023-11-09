<?php
/**
 * @ CLM Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_mail($out) {
	clm_core::$load->load_css("mail");
	clm_core::$load->load_css("buttons");
	clm_core::$load->load_css("notification");
	$lang = clm_core::$lang->mail;
	clm_core::$cms->setTitle($lang->title);

	clm_core::$load->load_js("mail");
	$jid = clm_core::$access->getJid();

	$users = $out["users"];
	$auser = $out["auser"];

	$str_mail_cc = $auser[0]->name." <".$auser[0]->email.">"; 

	$str_mail_to = '';
	for ($x=0; $x < (count($users)); $x++) {
		if ($x > 0) $str_mail_to .= ', ';
		$str_mail_to .= $users[$x]->name." <".$users[$x]->email.">"."\r\n"; 
	}

	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	$from = $config->email_from;
	$fromname = $config->email_fromname;

	$str_mail_from = $fromname." <".$from.">"; 

	echo "<h4>".$lang->title;
	echo '  '.$lang->date_on.' '.clm_core::$cms->showDate(time(), $lang->date_format_clm_f);
	echo "</h4>";
?>

	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_from; ?>
			</div>
			<div class="info_text">
				<?php echo $str_mail_from; ?>
			</div>
	</div>
	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_to; ?>
			</div>
			<div class="info_text">
				<?php echo $str_mail_to; ?>
			</div>
	</div>
	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_cc; ?>
			</div>
			<div class="info_text">
				<?php echo $str_mail_cc; ?>
			</div>
	</div>
	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_subject; ?>
			</div>
			<div class="text">
				<textarea class="mail_subj" oninput="clm_mail_fill_fields(this,false);"><?php echo '';?></textarea>
			</div>
	</div>
	<div class="outer_mail_body">
			<div class="info">
				<?php echo $lang->mail_body; ?>
			</div>
			<div class="text">
				<textarea class="mail_body" oninput="clm_mail_fill_fields(this,false);"><?php echo '';?></textarea>
			</div>
	</div>

<?php
echo '<input type="hidden" class="return_section" value="'.$out["input"]["return_section"].'">';
echo '<input type="hidden" class="return_view" value="'.$out["input"]["return_view"].'">';
echo '<input type="hidden" class="cids" value="'.$out["input"]["cids"].'">';

echo '<div class="clm_view_notification"><div class="notice"><span>' . $lang->data_needed . '</div></div>';
echo '<div class="button_container">';
echo '<button type="button" onclick="javascript:history.back(1);" class="clm_button button_back">'.$lang->button_back.'</button>';
echo '<button type="button" onclick="clm_mail_save(this)" class="clm_button button_save" >'.$lang->button_save.'</button>';
echo '</div><div class="space"></div>';
 } ?>
