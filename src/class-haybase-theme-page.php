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
        
        // Load all options from the database
        $this->loadOptions();        
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
        $table = array();
        foreach($this->options as $id => $opt) {
            // The option should have a title, else don't show it
            if (!isset($opt['title'])) {
                continue;
            } else {
                $title = $opt['title'];
            }

            // Get some standard values
            $default = (isset($opt['default'])) ? $opt['default'] : "";
            // If no type is available, default to 'text'
            $type = (isset($opt['type'])) ? $opt['type'] : "text";


        	$table[] = "<tr>";
        	$table[] = "<td nowrap>$title</td>";

        	// Get the setting from the db
        	$table[] = '<td>';
        	$setting = $this->getOption($id);

        	switch($value['type']) {
        	   case "textarea":
                	$line  = '<textarea name="' . $id . '" id="' . $id . '" ';
                	$line .= 'rows="10" cols="50">' . $setting . '</textarea>';
                	$table[] = $line;
                	break;
            	case "radioshowhide":
                    foreach(array("hide", "show") as $type) {
                    	$line  = '<input name="'  . $id . '" id="' . $id . '" ';
                    	$line .= 'type="radio" value="' . $type . '"';
                    	$line .= ($setting == $type) ? 'checked="checked"' : '';
                    	$line .= " /> $type ";
                        $table[] = $line;
                    }
                	break;
            	default:
                   	$line  = '<input name="'  . $id . '" id="' . $id . '" ';
                   	$line .= 'type="text" value="' . $setting . '" size="50" />';
                   	$table[] = $line;
                	break;
        	}
            $table[] = "</td>";
            $table[] = "</tr>";
    	}
    	$table = implode("\n", $table);

        echo $this->haybase->parseTemplate($this->haybase->pluginPath . "/templates/adminpage.html", array(
            "title" => $this->conf['title'],
            "table" => $table
        ));
    }

    private function loadOptions() {
        $dbOpts = get_option($this->pageId);

        foreach ($this->options as $id => &$opt) {
            if ($dbOpts[$id]) {
                $opt['value'] = $dbOpts[$id];
            }
        }
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