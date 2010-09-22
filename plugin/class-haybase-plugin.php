<?php
/*  Haybase - An object-orientated PHP5 theme framework for WordPress
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
class HaybasePlugin {
    protected $config;
    private $pluginname, $path, $url;

    function __construct($pluginname = "haybase") {
        $this->pluginname = $pluginname;
        $this->path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->pluginname;
        $this->url = WP_PLUGIN_URL . "/" . $this->pluginname;
        $this->config = $this->readConfig();
    }

    public function getConfig() {
        return $this->config;
    }

    public function addCssFiles() {
        foreach ($this->config->css->files as $file) {
            printf(
                '<link rel="stylesheet" type="text/css" href="%s" />' . "\n",
                $file
            );
        }
    }

    public function addJsFiles() {
        // Load libraries first, these are all handles
        foreach ($this->config->js->libraries as $file) {
            wp_enqueue_script($file, null, null, null, $this->config->js->load_at_bottom);
        }

        foreach ($this->config->js->files as $file) {
            wp_enqueue_script("", $file, null, null, $this->config->js->load_at_bottom);
        }
    }

    // Private methods

    // Gets the haybase.json file and parses it to an array
    private function readConfig() {
        $file = file_get_contents($this->path . DIRECTORY_SEPARATOR . "haybase.json");
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
        add_action("wp_head", array($this, "addCssFiles"));
        add_action("init", array($this, "addJsFiles"));

        return $conf;
    }

    private function rewriteExternalFiles($files) {
        foreach ($files as &$file) {
            if (substr($file, 0, 4) != "http") {
                $file = $this->url . "/" . $file;
            }
        }
        return $files;
    }

    private function processJavascript($conf) {
        $conf->js->files = $this->rewriteExternalFiles($conf->js->files);

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

    private function halt($msg) {
        die('<h1 style="color:red;">' . $msg . '</h1>');
    }
}