<header class="header">
<?php the_post(); ?>
<?php if ( '' != get_the_author_meta( 'user_description' ) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . get_the_author_meta( 'user_description' ) . '</div>' ); ?>
<?php rewind_posts(); ?>
</header>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'entry' ); ?>
<?php endwhile; ?>
<?php get_template_part( 'nav', 'below' ); ?>


<?php
function get_template_title() {
	$title  =  '';
	ob_start();
		_e( 'Author Archives', 'blankslate' ); 
		$title .= ob_get_contents();
		ob_clean();
		$title .= ': ';
		the_author_link();
		$title .= ob_get_contents();
	ob_end_clean();
	return $title;
}
?>