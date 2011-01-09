<?php
/*  Haybase - makes developing WordPress themes and plugins infinitely easier
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
class HaybaseTheme extends Haybase {
    public function newThemePage($opts) {
        return new HaybaseThemePage($opts, $this);
    }
    /*
    function __construct($args = false) {
        if ($args) {
            parent::__construct($args);
            $this->registerSidebars();
            $this->registerNavMenus();
        }
    }

    public function sidebar($id) {
        // Currently this function doesn't do anything fancy, but in the future
        // we might, so this wrapper is already in place
        if (function_exists('dynamic_sidebar')) {
            dynamic_sidebar($id);
        }
    }

    public function navMenu($id) {
        if (function_exists('wp_nav_menu')) {
            wp_nav_menu($id);
        }
    }

    private function registerSidebars() {
        if (empty($this->config->sidebars->sidebars)) return false;

        foreach ($this->config->sidebars->sidebars as $sidebar) {
            register_sidebar(array(
                "id" => $sidebar->id,
                "name" => $sidebar->name,
                "before_widget" => $this->sidebars->before_widget,
                "after_widget" => $this->sidebars->after_widget,
                "before_title" => $this->sidebars->before_title,
                "after_title" => $this->sidebars->after_title
            ));
        }
    }

    private function registerNavMenus() {
        if (empty($this->config->menus->menus)) return false;

        foreach ($this->config->menus->menus as $menu) {
            register_nav_menu($menu->id, $menu->name);
        }
    }
    */
}