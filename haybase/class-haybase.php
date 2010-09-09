<?php
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

    // Reads the config.json in the inc/ directory, and returns it as an
    // array
    private function readConfig() {
        $file = file_get_contents(TEMPLATEPATH . "/inc/config.json");
        if (!$file) {
            $this->halt("Could not read the config.json file. Is it in inc?");
        }

        $conf = json_decode($file);
        if (!$conf) {
            $this->halt("Could not decode JSON. Is it valid? Try jsonlint.com!");
        }

        $conf = $this->processJavascript($conf);

        return $conf;
    }

    private function processJavascript($conf) {
        // Javascript files not prefixed with "http" get the style path
        // prepended
        foreach ($conf->javascript->custom as &$js) {
            if (substr($js, 0, 4) != "http") {
                $js = $this->getTheme() . "/" . $js;
            }
        }
        return $conf;
    }

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