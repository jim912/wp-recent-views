<?php
function get_recent_views( $limit = 0 ) {
	$recent_view_ids = isset( $_COOKIE['recent-views'] ) && preg_match( '/^[0-9,]+$/', $_COOKIE['recent-views'] ) ? $_COOKIE['recent-views'] : '';
	$recent_view_ids = trim( $recent_view_ids, ',' );
	$recent_view_ids = explode( ',', $recent_view_ids );
	$recent_views = array();
	foreach ( $recent_view_ids as $post_id ) {
		if ( $post = get_post( $post_id ) ) {
			$recent_views[] = $post;
			if ( $limit > 0 && count( $recent_views ) >= $limit ) {
				break;
			}
		}
	}
	return $recent_views;
}



function list_recent_views( $args ) {
	global $WP_Recent_Views;
	$defaults = array(
		'limit' => 5,
		'mode' => 'default',
		'show_option_none' => __( 'No view', 'wp-recent-views' ),
		'ul_id' => '',
		'ul_class' => 'recent-views',
		'style' => 'list',
		'title_li' => __( 'Recent Views' ),
		'ajax' => false,
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );
	
	$recent_views = get_recent_views( $r['limit'] );
	
	$output = '';
	
	if ( 'list' == $r['style'] && ! $r['ajax'] ) {
		if ( $r['title_li'] ) {
			$output = '<li>' . "\n\t" . $title_li . "\n";
		}
		if ( ! $r['ul_id'] ) {
			$r['ul_id'] = 'list-recent-views-' . $WP_Recent_Views->count;
			$WP_Recent_Views->count++;
		}
		$class = $r['ul_class'] ? ' class="' . esc_attr( $r['ul_class'] ) . '"' : '';
		$output .= "\t" . '<ul id="'. esc_attr( $r['ul_id'] ) . '"' . $class . '>' . "\n";
	}

	if ( empty( $recent_views ) ) {
		if ( ! empty( $r['show_option_none'] ) ) {
			if ( 'list' == $r['style'] ) {
				$output .= "\t<li>" . $r['show_option_none'] . "</li>\n";
			} else {
				$output .= "\t" . $r['show_option_none'] . "\n";
			}
		}
	} else {
		if ( $r['mode'] != 'ajax' ) {
			foreach ( $recent_views as $recent_view ) {
				if ( 'list' == $r['style'] ) {
					$output .= "\t" . '<li><a href="' . get_permalink( $recent_view->ID ) . '" title="' . esc_attr( apply_filters( 'the_title', $recent_view->post_title ) ) . '">' . apply_filters( 'the_title', $recent_view->post_title ) . "</a></li>\n";
				} else {
					$output .= "\t" . '<a href="' . get_permalink( $recent_view->ID ) . '" title="' . esc_attr( apply_filters( 'the_title', $recent_view->post_title ) ) . '">' . apply_filters( 'the_title', $recent_view->post_title ) . "</li>\n";
				}
			}
		} else {
			wp_enqueue_script( 'list_recent_views', plugin_dir_url( __FILE__ ) . '/js/recent-views.js', array( 'jquery' ) );
			wp_localize_script( 'list_recent_views', 'RecentViews', array(
				'endpoint' => admin_url( 'admin-ajax.php' ),
				'action' => 'recent_views',
				'id' => $r['ul_id'],
				'limit' => $r['limit']
			));
		}
	}

	if ( 'list' == $r['style'] && ! $r['ajax'] ) {
		$output .= "\t</ul>\n";
		if ( $r['title_li'] ) {
			$output .= "</li>\n";
		}
	}

	$output = apply_filters( 'list_recent_views', $output );
	if ( $r['echo'] ) {
		echo $output;
	} else {
		return $output;
	}
}



function wp_ajax_recent_views() {
	$args = array(
		'echo' => 1,
		'mode' => 'default',
		'style' => 'list',
		'title_li' => 0,
		'ajax' => true
	);
	if ( $limit = absint( $_POST['limit'] ) ) {
		$args['limit'] = $limit;
	}
	list_recent_views( $args );
	exit;
}
