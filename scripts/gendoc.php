<?php
    $file = file_get_contents("../src/class-haybase.php");
    preg_match_all("/public function (.*) {/", $file, $matches);
    asort($matches[1]);
    print_r($matches[1]);