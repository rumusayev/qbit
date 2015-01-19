<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $data['item']->page_title; ?></title>
    <meta name="description" content="<?php echo $data['item']->page_meta_description; ?>">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="<?php echo Backstage::gi()->TEMPLATE_URL; ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo Backstage::gi()->TEMPLATE_URL; ?>css/icomoon-social.css">
    <link href='<?php echo Backstage::gi()->TEMPLATE_URL; ?>css/googleOpenSans.css' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="<?php echo Backstage::gi()->TEMPLATE_URL; ?>css/leaflet.css" />
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="<?php echo Backstage::gi()->TEMPLATE_URL; ?>css/leaflet.ie.css" />
    <![endif]-->
    <link rel="stylesheet" href="<?php echo Backstage::gi()->TEMPLATE_URL; ?>css/main.css">

    <script src="<?php echo Backstage::gi()->TEMPLATE_URL; ?>js/modernizr-2.6.2-respond-1.1.0.min.js"></script>

    <script>
        var portal_url =  '<?php echo Backstage::gi()->portal_url; ?>';
    </script>
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->