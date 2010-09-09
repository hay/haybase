<?php global $T; ?>
<?php
    // Currently we have two sidebars defined: home-sidebar and sidebar, 
    // but you can define as many sidebars as you wish in inc/config.json
    if (is_front_page()) {
        $T->getSidebar('home-sidebar');
    } else {
        $T->getSidebar('sidebar');
    }
?>