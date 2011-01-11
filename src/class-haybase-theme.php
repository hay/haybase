<?php
/*  Haybase - makes developing WordPress themes and plugins infinitely easier
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
class HaybaseTheme extends Haybase {
    private $themepage;

    public function newThemePage($opts) {
        $this->themepage = new HaybaseThemePage($opts, $this);
        return $this->themepage;
    }

    public function themeOption($id) {
        echo $this->themepage->getOption($id);
    }
    
    public function getThemeOption($id) {
        return $this->themepage->getOption($id);
    }
}