<?php
/*  Haybase - makes developing WordPress themes and plugins infinitely easier
    By Hay Kranen < http://www.haykranen.nl/projects/haybase
    Released under the GPL. See LICENSE for information
*/
abstract class Haybase {
    public $pluginPath, $pluginUrl;
    protected $config;

    function __construct($args = false) {
        // This is a *little* dirty, but because the plugin's directory
        // could be a symlink, we're doing it this way.
		$this->pluginPath = WP_CONTENT_DIR . "/plugins/haybase";
		$this->pluginUrl = WP_CONTENT_URL . "/plugins/haybase";
        add_theme_support('post-thumbnails');

        $this->config = $this->readConfig();

        if ($args) {
            $this->setConfig($args);
        }

        $this->config = (object) $this->config;
    }

    public function getConfig() {
        return $this->config;
    }

    public function setConfig($a1, $a2 = false) {
        // If this is an array extend over the current config, else just set
        // one single key
        if (is_array($a1)) {
            $this->config = array_merge($this->config, $a1);
        } else if (is_string($a1) && is_string($a2)) {
            $this->config->$a1 = $a2;
        }
    }

    public function getPostThumbResized($id, $width = false, $height = false) {
        $imgUrl = $this->getPostThumbUrl($id);
        if (!$imgUrl) return false;

        $width = (isset($width)) ? $width : $this->config->postthumb_width;
        $height = (isset($height)) ? $height : $this->config->postthumb_height;

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
        } else if (!$img && $this->config->postthumb_customkey) {
            // Might have a custom key
            $key = get_post_custom($id);
            if ($key[$this->config->postthumb_customkey]) {
                return $key[$this->config->postthumb_customkey][0];
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

    public function loadStylesheets() {
        $files = func_get_args();
        if (empty($files)) return false;

        $files = $this->rewriteExternalFiles($files);

        foreach ($files as $file) {
            echo '<link rel="stylesheet" href="' . $file . '" />' . "\n";
        }
    }

    public function loadJavascripts() {
        $files = func_get_args();
        if (empty($files)) return false;

        $files = $this->rewriteExternalFiles($files);

        foreach ($files as $file) {
            echo '<script src="' . $file . '"></script>' . "\n";
        }
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

    public function escape($str) {
        return htmlentities($str, ENT_QUOTES, $this->config->defaultcharset);
    }

    // Superhandy lightweight template function
    public function parseTemplate($file, $options) {
        $template = file_get_contents($file);
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

    private function readConfig() {
        $file = file_get_contents($this->pluginPath . "/defaults.json");
        return json_decode($file, true);
    }

    private function rewriteExternalFiles($files) {
        foreach ($files as &$file) {
            if (substr($file, 0, 4) != "http") {
                $file = $this->getTheme() . "/" . $file;
            }
        }
        return $files;
    }

    // Private methods
    private function getOpenGraphMetaTags() {
        global $post;

        $opts = array(
            "title" => get_the_title(),
            "type" => "blog",
            "url" => get_permalink(),
            "image" => $this->getPostThumbResized($post->ID),
            "description" => get_the_excerpt()
        );

        $o = "";
        foreach ($opts as $key => $value) {
            $o .= sprintf(
                '<meta property="og:%s" content="%s" />' . "\n",
                $key, $value
            );
        }

        return $o;
    }
}