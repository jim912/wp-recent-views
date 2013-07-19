<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function get_recent_views( $args ) {
	global $WP_Recent_Views;

	$recent_view_ids = isset( $_COOKIE['recent-views'] ) && preg_match( '/^[0-9,]+$/', $_COOKIE['recent-views'] ) ? $_COOKIE['recent-views'] : '';
	$recent_view_ids = trim( $recent_view_ids, ',' );
	$recent_view_ids = explode( ',', $recent_view_ids );
	
	if ( ! isset( $args['posts_per_page'] ) || ! $args['posts_per_page'] ) {
		$length = get_option('posts_per_page');
	} elseif ( $args['posts_per_page'] == -1 ) {
		$length = NULL;
	} else {
		$length = absint( $args['posts_per_page'] );
	}

	if ( ! isset( $args['paged'] ) || ! $args['paged'] ) {
		$args['paged'] = get_query_var( 'page' );
	}

	if ( isset( $args['offset'] ) && absint( $args['offset'] ) ) {
		$offset = absint( $args['offset'] );
			$WP_Recent_Views->numpages = 1;
	} else {
		if ( isset( $args['paged'] ) && absint( $args['paged'] ) && ! is_null( $length ) ) {
			$offset = $length * ( absint( $args['paged'] ) - 1 );
		} else {
			$offset = 0;
		}
		$found_posts = count( $recent_view_ids );
		if ( $length ) {
			$WP_Recent_Views->numpages = ceil( $found_posts / $length );
			$WP_Recent_Views->multipage = 1;
		} else {
			$WP_Recent_Views->numpages = 1;
		}
	}
	
	$recent_view_ids = array_slice( $recent_view_ids, $offset, $length );
	
	$recent_views = array();
	foreach ( $recent_view_ids as $post_id ) {
		if ( $post = get_post( $post_id ) ) {
			$recent_views[] = $post;
		}
	}
	return $recent_views;
}



function wp_list_recent_views( $args ) {
	global $WP_Recent_Views;
	$defaults = array(
		'limit' => 5,
		'mode' => 'default',
		'show_option_none' => __( 'No view', 'wp-recent-views' ),
		'ul_id' => '',
		'ul_class' => 'recent-views',
		'style' => 'list',
		'title_li' => __( 'Recent Views', 'wp-recent-views' ),
		'ajax' => false,
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );
	
	$recent_views = get_recent_views(
		array(
			'offset' => 0,
			'posts_per_page' => $args['limit'],
			'paged' => 1,
		)
	);
	
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
			wp_enqueue_script( 'wp_list_recent_views', plugin_dir_url( __FILE__ ) . 'js/recent-views.js', array( 'jquery' ), $WP_Recent_Views->version );
			wp_localize_script( 'wp_list_recent_views', 'RecentViews', array(
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

	$output = apply_filters( 'wp_list_recent_views', $output );
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
	wp_list_recent_views( $args );
	exit;
}
