<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" xml:lang="en" lang="en">
<head>
	
<!--Meta Data -->
<meta property="og:title" content="<?php echo 'Chikoo::'.$page_title; ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo base_url();?>" />
<meta property="og:site_name" content="Chikoo" />

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo "Chikoo::".$page_title;?></title>
	
<meta name="title" content="Procure your social networking updates & broadcast to your WordPress Blog" />

<meta name="Description" content="Procure your social networking updates & broadcast to your WordPress Blog">

<meta name="Keywords" content="facebook, twitter, instagram, wordpress, chikoo">
<!--Meta Data -->

<!-- CSS -->
<link href="<?php echo base_url();?>css/common.css" rel="stylesheet" type="text/css" />
<!-- CSS -->

<!-- SCRIPT-->
<script src="<?php echo base_url();?>javascript/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>javascript/jquery-ui-1.8.16.custom.min.js" type="text/javascript" ></script>
<script type="text/javascript">
$(document).ready(function(){
	
	$(".MessageBox").show();
	$(".MessageBox").delay(3000).hide("slow");

});	
</script>
<!-- SCRIPT-->

</head>
<body>

<?php echo GetFlashMessage(); ?>
