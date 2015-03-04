<!DOCTYPE html>
<html lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>qBit v3.0</title>

    <link rel="stylesheet" type="text/css" charset="utf-8" href="<?php echo Backstage::gi()->EXTERNAL_URL; ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" charset="utf-8" href="<?php echo Backstage::gi()->EXTERNAL_URL; ?>bootstrap/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?php echo Backstage::gi()->EXTERNAL_URL; ?>datetimepicker/bootstrap-datetimepicker.css" />
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?php echo Backstage::gi()->EXTERNAL_URL; ?>selectize/css/selectize.bootstrap3.css" />
		
    <link rel="stylesheet" href="<?php echo Backstage::gi()->TEMPLATE_URL; ?>../admin/default/css/admin.css">

    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>jquery/jquery.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>jquery/jquery.serializeJSON.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>jquery/jquery.cookie.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>jquery/jquery.print.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>datetimepicker/moment.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>datetimepicker/bootstrap-datetimepicker.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>datetimepicker/locales/bootstrap-datetimepicker.en-gb.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>datetimepicker/locales/bootstrap-datetimepicker.ru.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>selectize/js/standalone/selectize.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>typeahead/typeahead.bundle.min.js"></script>    
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->EXTERNAL_URL; ?>typeahead/bloodhound.min.js"></script>

    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->TEMPLATE_URL; ?>../admin/default/js/easyLQ.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo Backstage::gi()->TEMPLATE_URL; ?>../admin/default/js/admin.js"></script>

    <script>
           var portal_url =  '<?php echo Backstage::gi()->portal_url; ?>';
    </script>
</head>
<body>

<div id="header">
<div id="logo" style="padding:10px">
    <a href="<?php echo Backstage::gi()->portal_url; ?>admin/"><img width="80px" src="<?php echo Backstage::gi()->TEMPLATE_URL; ?>../admin/default/images/qbit.png"></a>
</div>
</div>
<div id="admin_content">
<?php require_once 'navigation.php'; ?>