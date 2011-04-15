<?php
/*
Plugin Name: Haybase
Plugin URI: http://www.haykranen.nl/projects/haybase?utm_source=haybaseplugin&utm_medium=plugin&utm_campaign=hknl
Description: Makes developing themes and plugins infinitely more easy
Version: 0.2.4
Author: Hay Kranen
Author URI: http://www.haykranen.nl?utm_source=haybaseplugin&utm_medium=plugin&utm_campaign=hknl
*/

if (version_compare(PHP_VERSION, '5.2.0', '<')) {
    die(
        "Haybase requires PHP 5.2 or higher. You have: " .
        PHP_VERSION . "Please de-activate and upgrade to PHP5 to use this plugin."
    );
}

require_once 'class-haybase-exception.php';
require_once 'class-haybase.php';
require_once 'class-haybase-plugin.php';
require_once 'class-haybase-theme.php';
require_once 'class-haybase-theme-page.php';

// External libs
require_once 'class-mustache.php';
require_once 'class-jsmin.php';
require_once 'class-cssmin.php';
