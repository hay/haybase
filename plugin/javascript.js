(function($) {
$(document).ready(function() {
    if (!window.LikeDontLike) {
        return false;
    }
    
    function _(msg) {
        return window.LikeDontLike.messages[msg];
    }
    
    $(".ldl-buttons button").click(function() {
        if ($(this).hasClass('ldl-rated')) {
            return false;
        }

        // Hide the other button temporarily
        if ($(this).hasClass('ldl-button-like')) {
            $(this).parent().find("ldl-button-dont-like").hide();
        } else {
            $(this).parent().find("ldl-button-like").hide();
        }

        $(this).text(_('One moment please'));

        // Do an AJAX call
        $.getJSON(
            window.LikeDontLike.pluginUrl + '/vote.php',
            { "postid" : window.LikeDontLike.postId, "rating" : $(this).attr('data-rating')},

            function(data) {
                // write to the buttons
                if(data.error) {
                    // error!
                    if (data.error == "IP_HAS_VOTED") {
                        alert(_("You can only vote once for a post!"));
                    } else {
                        alert(_("An error occured when trying to vote. Try again later"));
                    }
                    return false;
                }
                
                var $p = $(this).parent().parent()
                $(this).parent().parent().


                var html = ''.conc
                html  = '<p class="col g6">' + data.positive +' mensen vonden dit mooi</p>';
                html += '<p class="col g6">' + data.negative +' mensen vonden dit mooi</p>';
                $("#rating").html(html);
            }
        );
        return false;
    });    
});
})(jQuery);