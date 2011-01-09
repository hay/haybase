<?php
/**
 * Haybase - makes developing WordPress themes and plugins infinitely easier
 * By Hay Kranen < http://www.haykranen.nl/projects/haybase
 *   Released under the GPL. See LICENSE for information
 */
class HaybaseThemePage {
    private $conf, $options = array(), $haybase, $statusMessage = false;
    private $statusMessages = array(
        "saved" => "Your settings have been saved!",
        "reset" => "This theme has been reset"
    );

    public function __construct($conf, $haybase) {
        $this->conf = $conf;
        $this->haybase = $haybase;

        add_action('admin_menu', array($this, "handleAdminPage"));
    }

    // Add instead of set, so we can dynamically add options on the fly
    public function addOptions($opts) {
        $this->options = array_merge($this->options, $opts);
    }

    public function handleAdminPage() {
        add_theme_page(
            $this->conf['title'],
            $this->conf['title'],
            'edit_themes',
            "edit-basic-simplicity",
            array($this, 'showAdminPage')
        );


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

    public function showAdminPage() {
        // Show the statusmessage if needed
        $this->showStatusMessage();

        // First build the option table
        $table = array();
        foreach($this->options as $opt) {
            // The option should have a name, else don't show it
            if (!isset($opt['title']) || !isset($opt['id'])) {
                continue;
            } else {
                $title = $opt['title'];
                $id = $opt['id'];
            }

            // Get some standard values
            $default = (isset($opt['default'])) ? $opt['default'] : "";
            // If no type is available, default to 'text'
            $type = (isset($opt['type'])) ? $opt['type'] : "text";


        	$table[] = "<tr>";
        	$table[] = "<td nowrap>$title</td>";

        	// Get the setting from the db
        	$table[] = '<td>';
        	$setting = stripslashes(get_option($id, $default));

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

    private function saveOptions() {
        foreach ($this->options as $id => $value) {
            if(isset($_POST[$id])) {
                update_option($id, $_POST[$id]);
            } else {
                delete_option($id);
            }
        }
    }

    private function resetOptions() {
        foreach ($this->options as $id => $value) {
            delete_option($id);
        }
    }

    private function showStatusMessage() {
        if (!$this->statusMessage) return;
        $message = $this->statusMessages[$this->statusMessage];
        echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
    }

}