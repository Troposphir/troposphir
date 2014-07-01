<?php
/*
Plugin Name: Advanced Search Widget
Plugin URI: http://wordpress.org/extend/plugins/advanced-search-widget
Description: Allows you to add a widget to search custom post types along with other options
Author: Aaron Axelsen
Version: 0.3
Author URI: http://aaron.axelsen.us
Text Domain: advanced-search-widget
*/

function advancedSearchWidget_getvars($getwidget) {
	if (empty($getwidget)) return false;
	$widget = get_option('widget_advanced-search-widget');
	$instance = esc_attr($_GET['widget']);
	$id = substr($instance,strrpos($instance,'-')+1);
	$options = $widget[$id];

	$opts = array();
	$opts['searchtitle'] = (isset($options['searchtitle']) ? $options['searchtitle'] : '1');
	$opts['searchcontent'] = (isset($options['searchcontent']) ? $options['searchcontent'] : '1');
	$opts['searchtags'] = (isset($options['searchtags']) ? $options['searchtags'] : '0');
	return $opts;
}

function advancedSearchWidget_searchquery($search) {
	if (!isset($_GET['posttype'])) return $search;    

	if (is_search()) {
		if (isset($_GET['widget'])) {
			extract(advancedSearchWidget_getvars($_GET['widget']));
		}
		global $wpdb, $wp_query;

		if ( empty( $search ) )
	        	return $search; // skip processing - no search term in query

		$q = $wp_query->query_vars;    
		$n = ! empty( $q['exact'] ) ? '' : '%';

		$search = "$wpdb->posts.post_type = '".esc_attr($_GET['posttype'])."' AND ";
		$searchand = '';

		foreach( (array) $q['search_terms'] as $term ) {
			$term = esc_sql( like_escape( $term ) );

			//push search "OR's"
			$list = array();
			if (isset($searchtitle) && $searchtitle == 1)
				array_push($list,"($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')");

			if (isset($searchcontent) && $searchcontent == 1)
				array_push($list,"($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')");

			if (isset($searchtags) && $searchtags == 1)
				array_push($list,"(t.name like '{$n}{$term}{$n}' AND post_status = 'publish' and tt.taxonomy in ('post_tag', 'category'))");
	
			$search .= "{$searchand}";
			$search .= "( ";
			$search .= implode(" OR ",$list);
			$search .= ")";
			$searchand = ' AND ';
		}

		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() )
				$search .= " AND ($wpdb->posts.post_password = '') ";
		}
	}
	return $search;
}
add_filter('posts_where','advancedSearchWidget_searchquery');

function advancedSearchWidget_searchjoin($join) {
	if (is_search()) {
                if (isset($_GET['widget'])) {
                        extract(advancedSearchWidget_getvars($_GET['widget']));

			if (isset($searchtags) && $searchtags == 1) {
				global $table_prefix, $wpdb;
				$tabletags = $table_prefix . "terms";
				$tablepost2tag = $table_prefix . "term_relationships";
				$tabletaxonomy = $table_prefix . "term_taxonomy";

				$join .= " LEFT JOIN $tablepost2tag tr ON $wpdb->posts.ID = tr.object_id INNER JOIN $tabletaxonomy tt ON tt.term_taxonomy_id=tr.term_taxonomy_id INNER JOIN $tabletags t ON t.term_id = tt.term_id ";
			}
		}
	}
	return $join;
}
add_filter('posts_join','advancedSearchWidget_searchjoin');

function advancedSearchWidget_searchgroupby($groupby) {
        if (is_search()) {
                if (isset($_GET['widget'])) {
                        extract(advancedSearchWidget_getvars($_GET['widget']));

                        if (isset($searchtags) && $searchtags == 1) {
				global $wpdb;

				// we need to group on post ID
				$mygroupby = "{$wpdb->posts}.ID";
	
				if( preg_match( "/$mygroupby/", $groupby )) {
					// grouping we need is already there
					return $groupby;
	  			}
	
				if( !strlen(trim($groupby))) {
					// groupby was empty, use ours
					return $mygroupby;
				}
	
				// wasn't empty, append ours
				return $groupby . ", " . $mygroupby;
			}
		}
	}	
	return $groupby;	
}
add_filter('posts_groupby', 'advancedSearchWidget_searchgroupby');

function customsearchwidget_getquery($id) {
	$widget = esc_attr($_GET['widget']);
	$posttype = esc_attr($_GET['posttype']);
	if (!empty($widget) && !empty($posttype)) {
		if ($widget == $id) return $_GET['s'];
	} 
}

#function postsrequeststmp($sql) {
#	if (is_search()) {
#	print_r($sql);
#	}
#	return $sql;
#}
#add_filter('posts_request','postsrequeststmp');

/**
 * Adds Advanced_Search widget.
 */
class Advanced_Search_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'advanced-search-widget', // Base ID
			'Advanced Search Widget', // Name
			array( 'description' => __( 'Advanced search widget', 'advanced-search-widget' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
	        $form = '<form role="search" method="get" id="'.$widget_id.'-searchform" action="' . esc_url( home_url( '/' ) ) . '" >
        	<div class="widget_search"><label class="screen-reader-text" for="'.$widget_id.'-s"></label>
	        <input type="text" value="" name="s" id="'.$widget_id.'-s" />
		<input type="hidden" name="posttype" value="' .$instance['posttype']. '" />
		<input type="hidden" name="widget" value="' .$widget_id. '" />
        	<input type="submit" id="'.$widget_id.'-searchsubmit" value="'. esc_attr__('Search') .'" />
	        </div>
        	</form>';
		echo $form;
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		$instance['searchtitle'] = (empty($new_instance['searchtitle']) ? '0' : strip_tags( $new_instance['searchtitle'] ));
		$instance['searchcontent'] = (empty($new_instance['searchcontent']) ? '0' : strip_tags( $new_instance['searchcontent'] ));
		$instance['searchtags'] = (empty($new_instance['searchtags']) ? '0' : strip_tags( $new_instance['searchtags'] ));
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'advanced-search-widget' );
		}
		if (isset($instance['searchtitle'])) {
			$searchtitle = ($instance['searchtitle'] == 1 ? ' checked="checked"' : '');
		} else {
			$searchtitle = ' checked="checked"';
		}
		if (isset($instance['searchcontent'])) {
			$searchcontent = ($instance['searchcontent'] == 1 ? ' checked="checked"' : '');
		} else {
			$searchcontent = ' checked="checked"';
		}
		if (isset($instance['searchtags'])) {
			$searchtags = ($instance['searchtags'] == 1 ? ' checked="checked"' : '');
		} else {
			$searchtags = '';
		}

		$custom_post_types = get_post_types( array('exclude_from_search' => false) );	
		#array_unshift($custom_post_types,'any');
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( 'Post Type:' ); ?></label>
		<select class='widefat' id="<?php echo $this->get_field_id( 'posttype' ); ?>" name="<?php echo $this->get_field_name( 'posttype' ); ?>">";
        		<?php foreach ($custom_post_types as $t) {
				$selected = '';
				if (isset($instance['posttype']) && $instance['posttype'] == $t) $selected = ' selected="selected"';
				echo "<option{$selected}>$t</option>";
			}?>
		</select>
		</p>
		<p><?php _e('Search using the following fields:','advanced-search-widget'); ?></p>
		<p>
		<input id="<?php echo $this->get_field_id( 'searchtitle' ); ?>" name="<?php echo $this->get_field_name( 'searchtitle' ); ?>"<?php echo $searchtitle; ?> type="checkbox" value="1" />
		<label for="<?php echo $this->get_field_id( 'searchtitle' ); ?>"><?php _e( 'Title' ); ?></label> 
		</p>
		<p>
		<input id="<?php echo $this->get_field_id( 'searchcontent' ); ?>" name="<?php echo $this->get_field_name( 'searchcontent' ); ?>"<?php echo $searchcontent; ?> type="checkbox" value="1" />
		<label for="<?php echo $this->get_field_id( 'searchcontent' ); ?>"><?php _e( 'Content' ); ?></label> 
		</p>
		<p>
		<input id="<?php echo $this->get_field_id( 'searchtags' ); ?>" name="<?php echo $this->get_field_name( 'searchtags' ); ?>"<?php echo $searchtags; ?> type="checkbox" value="1" />
		<label for="<?php echo $this->get_field_id( 'searchtags' ); ?>"><?php _e( 'Tags' ); ?></label> 
		</p>
		<?php 
	}

} // class Foo_Widget

// register advanced search widget
add_action( 'widgets_init', create_function( '', 'register_widget( "advanced_search_widget" );' ) );
