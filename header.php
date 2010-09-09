<?php global $T; ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

    <?php /* Use these if you don't use a favicon.ico or iOS icon in your root */ ?>
	<link rel="shortcut icon" href="<?php $T->theme(); ?>/img/favicon.png"/>
	<link rel="apple-touch-icon" href="<?php $T->theme(); ?>/img/touch_icon.png"/>

	<?php /* Always force latest IE rendering engine, or Chrome Frame */ ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <?php /* Fix for viewport scaling on mobile devices */ ?>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">

    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?>" href="/feed" />
    <link rel="stylesheet" type="text/css" href="<?php $T->style(); ?>/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="<?php $T->style(); ?>/css/style.css" />

    <?php wp_head(); ?>
</head>
<body>
<div id="wrapper">
    <?php /* W00t! Put your site here ;) */ ?>