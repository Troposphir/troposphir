<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'entry' ); ?>
<?php endwhile; endif; ?>
<?php get_template_part( 'nav', 'below' ); ?>


<?php
function get_template_title() {
	$title  =  '';
	ob_start();
		if ( is_day() ) { printf( __( 'Daily Archives: %s', 'blankslate' ), 	get_the_time( get_option( 'date_format' ) ) ); }
		elseif ( is_month() ) { printf( __( 'Monthly Archives: %s', 'blankslate' ), get_the_time( 'F Y' ) ); }
		elseif ( is_year() ) { printf( __( 'Yearly Archives: %s', 'blankslate' ), get_the_time( 'Y' ) ); }
		else { _e( 'Archives', 'blankslate' ); }
		$title .= ob_get_contents();	
	ob_end_clean();
	return $title;
}
?>