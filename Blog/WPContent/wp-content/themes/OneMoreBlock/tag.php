<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'entry' ); ?>
<?php endwhile; endif; ?>
<?php get_template_part( 'nav', 'below' ); ?>

<?php
function get_template_title() {
	$title =  '';
	ob_start();
		_e( 'Tag Archives: ', 'blankslate' ); 
		single_tag_title();
		$title = ob_get_contents();	
	ob_end_clean();
	return $title;
}
?>