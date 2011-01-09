<?php
/**
 * Haybase - makes developing WordPress themes and plugins infinitely easier
 * By Hay Kranen < http://www.haykranen.nl/projects/haybase
 *   Released under the GPL. See LICENSE for information
 */
class HaybaseThemePage {
    private $conf, $options = array(), $haybase, $statusMessage = false, $pageId;
    private $statusMessages = array(
        "saved" => "Your settings have been saved!",
        "reset" => "This theme has been reset"
    );

    public function __construct($conf, $haybase) {
        $this->conf = $conf;
        $this->haybase = $haybase;

        // Generate a id from the title, so we can save all
        // options under that name in the options table
        $this->pageId = sanitize_title($this->conf['title']);

        add_action('admin_menu', array($this, "addThemePage"));
        add_action('admin_menu', array($this, "handleActions"));

        if (is_admin()) {
            add_action('admin_menu', array($this, "loadOptions"));
        } else {
            add_action('init', array($this, "loadOptions"));
        }
    }

    // Add instead of set, so we can dynamically add options on the fly
    public function addOptions($opts) {
        $this->options = array_merge($this->options, $opts);
    }

    public function addThemePage() {
        add_theme_page(
            $this->conf['title'],
            $this->conf['title'],
            'edit_themes',
            $this->pageId,
            array($this, 'showAdminPage')
        );
    }

    public function handleActions() {
        switch($_POST['action']) {
            case "save":
                $this->saveOptions();
                $this->statusMessage = "saved";
                break;
            case "reset":
                $this->resetOptions();
                $this->statusMessage = "reset";
                break;
            default:
                $this->statusMessage = false;
                break;
        }
    }

    public function getOptions() {
        return $this->options;
    }

    public function getOption($id) {
        $opt = $this->options[$id];
        if ($opt['value']) {
            return $opt['value'];
        } else if ($opt['default']) {
            return $opt['default'];
        } else {
            return false;
        }
    }

    public function showAdminPage() {
        // Show the statusmessage if needed
        $this->showStatusMessage();

        // First build the option table
        $table = "";

        foreach($this->options as $id => $opt) {
            $table .= $this->template("row", array(
                "title" => $opt['title'],
                "value" => $this->getValueHtml($id, $opt),
                "description" => ($opt['description']) ? $opt['description'] : ""
            ));
        }

        echo $this->template("page", array(
            "title" => $this->conf['title'],
            "table" => $table
        ));
    }
    
    public function loadOptions() {
        $dbOpts = get_option($this->pageId);

        foreach ($this->options as $id => &$opt) {
            if ($dbOpts[$id]) {
                $opt['value'] = $dbOpts[$id];
            }
        }
    }    

    private function getValueHtml($id, $opt) {
        // If no type is available, default to 'text'
        $type = (isset($opt['type'])) ? $opt['type'] : "text";
        $setting = $this->getOption($id);
        $value = false;
        $defaultTemplateValues = array(
            "id" => $id,
            "setting" => $setting
        );

        switch($type) {
            case "textarea":
                $value = $this->template("textarea", $defaultTemplateValues);
                break;
            case "text":
                $value = $this->template("text", $defaultTemplateValues);
                break;
            case "select":
                $options = "";
                foreach ($opt['data'] as $key => $val) {
                    // Handle selected state: when either the value is set, or
                    // no value is set but there is a default value
                    $selected = ( ($key == $opt['value']) || ($key == $opt['default']) && empty($opt['value']))  ? "selected" : "";
                    $options .= sprintf(
                        '<option value="%s" %s>%s</option>',
                        $key, $selected, $val
                    );
                }
                $value = sprintf(
                    '<select name="%s">%s</select>',
                    $id, $options
                );
        }

        return $value;
    }

    // Nothing more than a simple wrapper to save typing
    private function template($name, $args) {
        return $this->haybase->parseTemplate(
            $this->haybase->pluginPath . "/templates/themepage/$name.html",
            $args
        );
    }

    private function saveOptions() {
        // First convert to simple key->value array
        $toSave = array();

        foreach ($this->options as $id => $opt) {
            if (isset($_POST[$id])) {
                $toSave[$id] = $_POST[$id];
            }
        }

        update_option($this->pageId, $toSave);
    }

    private function resetOptions() {
        delete_option($this->pageId);
    }

    private function showStatusMessage() {
        if (!$this->statusMessage) return;
        $message = $this->statusMessages[$this->statusMessage];
        echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
    }
}