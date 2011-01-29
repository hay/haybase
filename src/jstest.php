<?php
    require 'class-jsminplus.php';
    $js = file_get_contents("http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js");
    echo JSMinPlus::minify($js);