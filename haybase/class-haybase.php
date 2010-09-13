<?php
/*  Haybase - An object-orientated PHP5 theme framework for WordPress
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
class Haybase {
    protected $config;

    function __construct() {
        $this->config = $this->readConfig();
        $this->registerSidebars();

        add_theme_support('post-thumbnails');

    	// This theme uses wp_nav_menu()
    	add_theme_support('nav-menus');
    }

    // Public methods


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

    public function theme($echo = true) {
        if ($echo) {
            bloginfo('template_directory');
        } else {
            return get_bloginfo('template_directory');
        }
    }

    public function style($echo = true) {
        if ($echo) {
            echo bloginfo('stylesheet_directory');
        } else {
            return bloginfo('stylesheet_directory');
        }
    }

    public function home($echo = true) {
        if ($echo) {
            echo bloginfo('url');
        } else {
            return get_bloginfo('url');
        }
    }

    public function getConfig() {
        return $this->config;
    }

    public function getSidebar($id) {
        // Currently this function doesn't do anything fancy, but in the future
        // we might, so this wrapper is already in place
        if (function_exists('dynamic_sidebar')) {
            dynamic_sidebar($id);
        }
    }

    // Private methods

    // Gets the haybase.json file and parses it to an array
    private function readConfig() {
        $file = file_get_contents(TEMPLATEPATH . "/" . HAYBASE_CONFIG_FILE);
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
    
    // Superhandy lightweight template function 
    private function parseTemplate($file, $options) {
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

    private function resize($src, $width, $height, $zc = "1") {
        return sprintf($this->getTheme() . "/img/timthumb.php?src=%s&w=%s&h=%s&zc=%s",
            $src, $width, $height, $zc
        );
    }

    private function getTheme() {
        return get_bloginfo('template_directory');
    }

    private function halt($msg) {
        die('<h1 style="color:red;">' . $msg . '</h1>');
    }
}