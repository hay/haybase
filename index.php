<?php global $T; ?>
<?php get_header(); ?>

<?php /* The loop */ ?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : ?>
        <?php the_post(); ?>
        <?php require 'article.php'; ?>
    <?php endwhile; ?>
<?php else: ?>
    <?php require '404_msg.php'; ?>
<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>