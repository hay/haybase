<?php
/*  Haybase - makes developing WordPress themes and plugins infinitely easier
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
abstract class Haybase {
    private $configFile;
    protected $config;
    
    function __construct($configFile = false) {
        if ($configFile) {
            $this->configFile = $configFile;
            $this->initWithConfig();
        }
    }

    public function initWithConfig() {
        $this->config = $this->readConfig($this->configFile);
        $this->registerSidebars();

        add_theme_support('post-thumbnails');

    	// This theme uses wp_nav_menu()
    	add_theme_support('nav-menus');
    }

    public function postthumb() {
        $thumbid = get_post_thumbnail_id($post->ID);
        $img = wp_get_attachment_image_src($thumbid, $size);
        if (!$img) return;
        echo $this->parseTemplate(
            $this->theme(false) . "/templates/postthumb.html", array(
                "img" => $this->resize($img[0], $this->config->postthumb->width, $this->config->postthumb->height)
            )
        );
    }

    // Easy shortcuts to commonly used variables
    // Use the get* variants for return, and the 'keyword' ones for 
    // echoing
    public function theme() {
        echo $this->getTheme();
    }
    
    public function getTheme() {
        return get_bloginfo('template_directory');
    }

    public function style() {
        echo $this->getStyle();
    }
    
    public function getStyle() {
        return bloginfo('stylesheet_directory');
    }

    public function home() {
        echo $this->getHome();
    }
    
    public function getHome() {
        return get_bloginfo('url');
    }
    
    // Other stuff
    public function getConfig() {
        return $this->config;
    }
    
    // Superhandy lightweight template function 
    protected function parseTemplate($file, $options) {
        $template = @file_get_contents($file);
        if (!$template) {
            return false;
        }

        preg_match_all("!\{([^{]*)\}!", $template, $matches);

        $replacements = array();
        for ($i = 0; $i < count($matches[1]); $i++) {
            $key = $matches[1][$i];
            if (isset($options[$key])) {
                $val = $matches[0][$i];
                $template = str_replace($val, $options[$key], $template);
            }
        }

        return $template;
    }    
    
    protected function halt($msg) {
        die('<h1 style="color:red;">' . $msg . '</h1>');
    }

    // Private methods

    // Gets the haybase.json file and parses it to an array
    private function readConfig($configFile) {
        $file = file_get_contents($configFile);
        
        if (!$file) {
            $this->halt("Could not read configuration file. Is HAYBASE_CONFIG_FILE set correctly?");
        }

        $conf = json_decode($file);
        if (!$conf) {
            $this->halt("Could not decode JSON. Is it valid? Try jsonlint.com!");
        }
        
        // Preprocess some of the configuration options so we don't need to 
        // call extra methods in the implementation files
        $conf = $this->processJavascript($conf);
        $conf = $this->processCss($conf);

        return $conf;
    }
    
    private function rewriteExternalFiles($files) {
        foreach ($files as &$file) {
            if (substr($file, 0, 4) != "http") {
                $file = $this->getTheme() . "/" . $file;
            }
        }
        return $files;
    }

    private function processJavascript($conf) {
        $conf->javascript->files = $this->rewriteExternalFiles($conf->javascript->files);
        return $conf;
    }
    
    private function processCss($conf) {
        $conf->css->files = $this->rewriteExternalFiles($conf->css->files);
        return $conf;
    }
    
    private function resize($src, $width, $height, $zc = "1") {
        return sprintf($this->getTheme() . "/img/timthumb.php?src=%s&w=%s&h=%s&zc=%s",
            $src, $width, $height, $zc
        );
    }
}