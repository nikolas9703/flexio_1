<!DOCTYPE html>
<html lang="en" ng-app="bluapp">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">
<link rel="shortcut icon" href="<?php echo base_url('public/assets/images/favicon.ico') ?>" type="image/x-icon">
<meta name="description" content="BluLeaf v1.0">
<meta name="keywords" content="erp">
<meta name="author" content="Pensanomica">
<title><?php echo $this->title; ?> |  Flexio</title>
<link href="<?php echo base_url('public/assets/css/default/bootstrap.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/assets/css/default/custom.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/assets/css/default/font-awesome.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/assets/css/default/animate.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/themes/'. Template::$theme_default .'/css/style.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/assets/css/plugins/jquery/toastr.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/assets/css/plugins/bootstrap/bootstrap-tabdrop.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/assets/css/plugins/bootstrap/jasny-bootstrap.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('public/resources/compile/css/flexio.css'); ?>" rel="stylesheet">
<?php Assets::css(); ?>
</head>
<body class="fixed-sidebar pace-done<?php echo session_isset() != "" ? "skin-4" : "gray-bg" ?>">
