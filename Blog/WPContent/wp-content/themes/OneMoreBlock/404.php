<article id="post-0" class="post not-found">
<section class="entry-content">
<p><?php _e( 'Nothing found for the requested page. Try a search instead?', 'blankslate' ); ?></p>
<?php get_search_form(); ?>
</section>
</article>


<?php
function get_template_title() {
	$title  =  '';
	ob_start();
		_e( 'Nothing found for the requested page.', 'blankslate' );
		$title .= ob_get_contents();
	ob_end_clean();
	return $title;
}
?>