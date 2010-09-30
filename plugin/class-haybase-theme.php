<?php
/*  Haybase - makes developing WordPress themes and plugins infinitely easier
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
class HaybaseTheme extends Haybase {
    function __construct($configFile = false) {
        if ($configFile) {
            parent::__construct($configFile);
        }
    }
    
    public function getSidebar($id) {
        // Currently this function doesn't do anything fancy, but in the future
        // we might, so this wrapper is already in place
        if (function_exists('dynamic_sidebar')) {
            dynamic_sidebar($id);
        }
    }
    
    public function registerSidebars() {
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
}