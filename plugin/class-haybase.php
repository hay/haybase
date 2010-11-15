<?php
/*  Haybase - makes developing WordPress themes and plugins infinitely easier
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
abstract class Haybase {
    private $configFile;
    protected $config;
    private $pluginPath, $pluginUrl;
    private $defaults = array(
        "defaultcharset" => "utf-8"
    );

    function __construct($configFile = false) {
        if ($configFile) {
            $this->configFile = $configFile;
            $this->initWithConfig();
        }

        // This is a *little* dirty, but because the plugin's directory
        // could be a symlink, we're doing it this way.
		$this->pluginPath = WP_CONTENT_DIR . "/plugins/haybase";
		$this->pluginUrl = WP_CONTENT_URL . "/plugins/haybase";
    }

    public function initWithConfig() {
        $this->config = $this->readConfig($this->configFile);

        add_theme_support('post-thumbnails');
    }

    public function getPostThumbResized($id, $width = false, $height = false) {
        $imgUrl = $this->getPostThumbUrl($id);
        if (!$imgUrl) return false;

        $width = ($width) ? $width : $this->config->postthumb->width;
        $height = ($height) ? $height : $this->config->postthumb->height;

        return $this->getResizeUrl($imgUrl, $width, $height);
    }

    public function getResizeUrl($src, $width, $height, $zc = "1") {
        return sprintf($this->pluginUrl . "/timthumb.php?src=%s&amp;w=%s&amp;h=%s&amp;zc=%s",
            $src, $width, $height, $zc
        );
    }

    public function getPostThumbUrl($id) {
        $thumbid = get_post_thumbnail_id($post->ID);
        $img = wp_get_attachment_image_src($thumbid, $size);
        if ($img) {
            return $img[0];
        } else if (!$img && $this->config->postthumb->custom_key) {
            // Might have a custom key
            $key = get_post_custom($id);
            if ($key[$this->config->postthumb->custom_key]) {
                return $key[$this->config->postthumb->custom_key][0];
            }
        } else {
            return false;
        }
    }

    public function getRecentPosts($limit = 10) {
		$q = new WP_Query(array(
		  'showposts' => $limit,
		  'nopaging' => 0,
		  'post_status' => 'publish'
        ));

        if (!$q->have_posts()) {
            return false;
        }

        $posts = array();
        while ($q->have_posts()) {
            $q->the_post();

            $p = array(
                "link" => get_permalink(),
                "title" => get_the_title(),
                "ID" => get_the_ID(),
                "excerpt" => get_the_excerpt(),
                "author" => get_the_author()
            );

            $posts[] = (object) $p;
        }

		wp_reset_postdata();

		return $posts;
    }

    public function getRecentComments($limit = 10) {
        $r = get_comments(array(
            "number" => $limit,
            "status" => "approve"
        ));

        $comments = array();
        foreach ($r as $c) {
            $a = array(
                "author" => $c->comment_author,
                "author_url" => $c->comment_author_url,
                "link" => get_comment_link($c->comment_ID),
                "ID" => $c->comment_ID,
                "text" => $this->escape($c->comment_content),
                "title" => get_the_title($c->comment_post_ID)
            );

            $comments[] = (object) $a;
        }

        return $comments;
    }

    public function hasJsLib($lib) {
        return in_array($lib, $this->config->javascript->libs);
    }

    public function jsFilesAsScriptTags() {
        if (!$this->config->javascript->files) return false;
        foreach ($this->config->javascript->files as $js) {
            printf('<script src="%s"></script>' . "\n", $js);
        }
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

    public function rss() {
        echo $this->getRss();
    }

    public function getRss() {
        return get_bloginfo('rss2_url');
    }

    public function searchQuery() {
        echo $this->getSearchQuery();
    }

    public function getSearchQuery() {
        return (empty($_GET['s'])) ? "" : $this->escape($_GET['s']);
    }

    public function archiveTitle() {
        echo $this->getArchiveTitle();
    }

    public function getArchiveTitle() {
        switch ($this->getArchiveType()) {
            case "category":
                return single_cat_title('', false);
                break;
            case "tag":
                return single_tag_title('', false);
                break;
            case "day":
                return get_the_time('F jS, Y');
                break;
            case "month":
                return get_the_time('F, Y');
                break;
            case "year":
                return get_the_time('Y');
                break;
            case "author":
                return '';
                break;
        }
    }

    public function bodyClass() {
        echo $this->getBodyClass();
    }

    // This function is virtually identical to pageType, but prefixes the
    // '404' as 'p404' because CSS classes that start with a number are invalid
    public function getBodyClass() {
        $c = $this->getPageType();
        return ($c == "404") ? "p404" : $c;
    }

    public function pageType() {
        echo $this->getPageType();
    }

    public function getPageType() {
        if (is_home()) return 'home';
        if (is_404()) return '404';
        if (is_archive()) return $this->getArchiveType();
        if (is_page()) return 'page';
        if (is_single()) return 'singlepost';
        return 'unknown';
    }

    public function archiveType() {
        echo $this->getArchiveType();
    }

    public function getArchiveType() {
        if (is_category()) return 'category';
        if (is_tag()) return 'tag';
        if (is_day()) return 'day';
        if (is_month()) return 'month';
        if (is_year()) return 'year';
        if (is_author()) return 'author';

        // Probably a paged archive...
        return 'archive';
    }

    public function authorUrl() {
        echo $this->getAuthorUrl();
    }

    public function getAuthorUrl() {
	   return get_author_posts_url(get_the_author_meta('ID'));
    }

    public function postCategories() {
        echo $this->getPostCategories();
    }

    public function getPostCategories() {
        $count = count(get_the_category());
        if ($count < 1) return false;
        return get_the_category_list(', ');
    }

    public function postTags() {
        echo $this->getPostTags();
    }

    public function getPostTags() {
        return get_the_tag_list('', ', ');
    }

    // Other stuff
    public function getConfig() {
        return $this->config;
    }

    public function escape($str) {
        return htmlentities($str, ENT_QUOTES, $this->config->defaultcharset);
    }

    // Superhandy lightweight template function
    public function parseTemplate($file, $options) {
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

    // Returns <meta> tags with basic Open Graph information
    // See http://opengraphprotocol.org/
    // Note that you still need to add the xmlns to your html tag
    public function openGraphMetaTags() {
        echo $this->getOpenGraphMetaTags();
    }

    protected function halt($msg) {
        die('<h1 style="color:red;">' . $msg . '</h1>');
    }

    // Private methods
    private function getOpenGraphMetaTags() {
        global $post;
        $o  = $this->openGraphMetaTag("title", get_the_title());
        $o .= $this->openGraphMetaTag("type", "blog");
        $o .= $this->openGraphMetaTag("url", get_permalink());
        $o .= $this->openGraphMetaTag("image", $this->getPostThumbResized($post->ID));
        $o .= $this->openGraphMetaTag("description", get_the_excerpt());
        return $o;
    }

    private function openGraphMetaTag($prop, $content) {
        return sprintf(
            '<meta property="og:%s" content="%s" />' . "\n",
            $prop, $content
        );
    }

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

        if (!$conf->defaultcharset) {
            $conf->defaultcharset = $this->defaults['defaultcharset'];
        }

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

    private function mb_str_replace($search, $replace, $subject) {
        if(function_exists('mb_str_replace')) {
            return mb_str_replace($search, $replace, $subject);
        } else {
            if(is_array($subject)) {
                $ret = array();
                foreach($subject as $key => $val) {
                    $ret[$key] = mb_str_replace($search, $replace, $val);
                }
                return $ret;
            }

            foreach((array) $search as $key => $s) {
                if($s == '') {
                    continue;
                }
                $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
                $pos = mb_strpos($subject, $s);
                while($pos !== false) {
                    $subject = mb_substr($subject, 0, $pos) . $r . mb_substr($subject, $pos + mb_strlen($s));
                    $pos = mb_strpos($subject, $s, $pos + mb_strlen($r));
                }
            }

            return $subject;
        }
    }
}