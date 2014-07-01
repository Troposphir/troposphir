<header class="header">
<?php if ( '' != category_description() ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . category_description() . '</div>' ); ?>
</header>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'entry' ); ?>
<?php endwhile; endif; ?>
<?php get_template_part( 'nav', 'below' ); ?>

<?php
function get_template_title() {
	$title  =  '';
	ob_start();
		_e( 'Category Archives: ', 'blankslate' );
		$title .= ob_get_contents();
		ob_clean();
		$title .= ': ';
		single_cat_title();
		$title .= ob_get_contents();
	ob_end_clean();
	return $title;
}
?>