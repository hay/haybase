<div class="article">
    <h2 class="title">
        <a name="post-<?php the_ID(); ?>" href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h2>

    <h3 class="meta">Posted on <?php the_time('d-m-Y'); ?> by <?php the_author(); ?></h3>

    <div class="text">
        <?php
            $T->postthumb();
            if ($excerpt) {
                the_excerpt();
                echo '<p><a href="' . get_permalink() . '">Read on &raquo;</a></p>';
            } else {
                the_content("Read on &raquo;");
            }
        ?>
        <?php edit_post_link("Edit"); ?>
	</div>
</div>