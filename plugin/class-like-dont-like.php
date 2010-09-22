<?php
class LikeDontLike extends HaybasePlugin {
    const NAME = "likedontlike";
    
    function __construct() {
        parent::__construct(self::NAME);
        $this->init();
    }
    
    public function init() {
        add_filter('the_content', array($this, "addButtons"));
    }
    
    public function addButtons() {
    ?>
        <div class="likedontlike">
            <div class="ldl-results">
                <p class="ldl-result-like">12 People liked this</p>
                <p class="ldl-result-dont-like">5 People didn't like this</p>
            </div>
            
            <div class="ldl-buttons">
                <button class="ldl-button-like">LIKE</button>
                <button class="ldl-button-dont-like">DON'T LIKE</button>
            </div>
        </div>
    <?php
    }
}