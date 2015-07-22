<?php
function clm_view_html($title,$head,$body) { 
$config = clm_core::$db->config();
if($config->favicon!="") {
	$favicon = $config->favicon;
} else {
	$favicon = clm_core::$load->gen_image_url("html/favicon","ico");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="<?php echo $favicon; ?>" type="image/x-icon"/>
<title><?php echo $title; ?></title>
<?php echo $head; ?>
</head>
<body>
<div id="clm">
<?php echo $body; ?>
</div>
</body>
</html>
<?php } ?>
