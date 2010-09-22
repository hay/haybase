<?php
/*
 * This is the entry point for the theme, so here we load Haybase and the 
 * configuration file, haybase.json. 
 * This file is normally located in the root of your theme. If you want to 
 * change that, use the configuration option below. 
 */
 
// Disable Haybase completely by setting this to false
define ('HAYBASE', true);

// The location of haybase.json, from the theme root. This can be changed to
// something else if you want
define ('HAYBASE_CONFIG_FILE', 'haybase.json');

// Include all necessary classes
require 'haybase/class-haybase.php';

// To add your own theme-specific functions, write a new class that extends 
// Haybase, include it and change the class name for $T

// Initialize the Haybase object
$T = new Haybase();