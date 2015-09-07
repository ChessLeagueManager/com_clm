<?php
function clm_view_app_info($config) { 
clm_core::$load->load_js("jquery");
clm_core::$load->load_js("app_info");
clm_core::$load->load_css("app_info");
clm_core::$load->load_css("buttons");
clm_core::$load->load_css("notification");
$lang = clm_core::$lang->app_info;
?>
<div class="clm_title"><?php echo $lang->title; ?></div>
<p><?php echo $lang->text_main;
if($config["https"]==0) {
echo '<div class="clm_view_notification"><div class="warning">'.$lang->warning1.'</div></div>';
}
?>
<div class="clm_title_sub"><?php echo $lang->title_android; ?></div>
<div class="clm_around">
<div class="clm_info"><p>
<?php echo $lang->text_android; ?></p>

<p><?php echo $lang->website; ?><b><?php echo $config["url"];?></b></p>
<p><?php echo $lang->user; ?><b><?php echo $lang->user_example; ?></b></p>
<p><?php echo $lang->password; ?><b><?php echo $lang->password_example; ?></b></p>
<p><?php echo $lang->https; ?><b><?php echo ($config["https"]>0 ? $lang->yes : $lang->no); ?></b></p>
<p><?php echo $lang->certificates; ?><b><?php echo ($config["https"]==1 ? $lang->yes : $lang->no); ?></b></p>
</div>
<div class="clm_qrcode"><div class="clm_qrcode_direct"></div><a href="https://play.google.com/store/apps/details?id=com.ChessLeagueManager.main&hl=de"><?php echo $lang->link_android; ?></a></div>
</div>
<div class="clm_title_sub"><?php echo $lang->title_other; ?></div>
<div class="clm_around">
<div class="clm_info"><p><?php echo $lang->text_other; ?></p>
<?php if($config["https"]==1) {
echo '<div class="clm_view_notification"><div class="warning">'.$lang->warning2.'</div></div>';
} ?>
<p><span><?php echo $lang->user; ?></span><input name="name" type="text" ></p>
<p><span><?php echo $lang->password; ?></span><input name="pas" type="text" ></p>
<div class="clm_button">Link und QR-Code erneuern</div>
<div class="clm_view_notification"></div>
</div>
<div class="clm_qrcode"><div class="clm_qrcode_gen"></div><a href="" class="clm_disabled"><?php echo $lang->link_other; ?></a></div>
</div>

<?php } ?>