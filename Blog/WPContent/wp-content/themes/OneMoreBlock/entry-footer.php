<footer class="entry-footer">
<span class="cat-links"><?php _e( 'Tagged as: ', 'blankslate' ); ?><?php the_category( ', ' ); ?></span>
<span class="tag-links"><?php the_tags(); ?></span>
<!--<?php if ( comments_open() ) { 
echo '<Br /><span class="comments-link"><a href="' . get_comments_link() . '">' . sprintf( __( 'Comments', 'blankslate' ) ) . '</a></span>';
} ?>-->
</footer> 