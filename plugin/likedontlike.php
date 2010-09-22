<?php
/*
Plugin Name: Like Don't Like
Plugin URI: http://www.haykranen.nl/projects/likedontlike
Description: Adds 'Like' and 'Don't Like' buttons underneath your post. Click on a button to see how many other users clicked on it.
Version: 1.0
Author: Hay Kranen
Author URI: http://www.haykranen.nl
License: GPL2 or later
*/
require_once 'class-haybase-plugin.php';
require_once 'class-like-dont-like.php';
new LikeDontLike;
?>