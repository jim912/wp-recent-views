<?php
/*
Plugin Name: WP Recent Views
Plugin URI: 
Description: 最近見たページ
Author: jim912
Version: 1.0
Author URI: 
*/

class WP_Recent_Views {
	
	private $generations = 20;
	private $post_types = array( 'post', 'corporation', 'case', 'event' );

	public function __construct() {
		add_action( 'template_redirect', array( &$this, 'register_post' ), 9999 );
		add_action( 'init'             , array( &$this, 'register_shortcode' ) );
		add_action( 'admin_menu'       , array( &$this, 'add_setting_page' ) );
	}
	
	
	public function register_post() {
		global $post;
		if ( is_singular() && in_array( $post->post_type, $this->post_types ) ) {
			$recent_view_ids = isset( $_COOKIE['recent-views'] ) && preg_match( '/^[0-9,]+$/', $_COOKIE['recent-views'] ) ? $_COOKIE['recent-views'] : '';
			$recent_view_ids = trim( $recent_view_ids, ',' );
			$recent_view_ids = explode( ',', $recent_view_ids );
			if ( $indexes = array_keys( $recent_view_ids, $post->ID ) ) {
				foreach ( $indexes as $index ) {
					unset( $recent_view_ids[$index] );
				}
			}
			array_unshift( $recent_view_ids, $post->ID );
			$recent_view_ids = array_slice( $recent_view_ids, 0, $this->generations );
			$recent_view_ids = implode( ',', $recent_view_ids );
			$expire = time() + 30 * 24 * 3600;
			setcookie( 'recent-views', $recent_view_ids, $expire, '/' );
		}
	}
	
	
	public function register_shortcode() {
		add_shortcode( 'recent-views', array( &$this, 'recent_views_shortcode' ) );
	}
	
	
	public function recent_views_shortcode() {
		global $post;
		$return = '';
		$recent_views = get_recent_views();
		
		$template = apply_filters( 'recent_views_template', dirname( __FILE__ ) . '/tpl/shortcode.php' );
		ob_start();
		foreach ( $recent_views as $post ) {
			setup_postdata( $post );
			if ( file_exists( $template ) ) {
				include( $template );
			}
		}
		$return = ob_get_clean();
		wp_reset_postdata();
		return $return;
	}
	
	
	public function add_setting_page() {
		add_options_page( '最近見たページ', '最近見たページ', 'manage_options', basename( __FILE__ ), array( &$this, 'setting_page' ) );
	}
	
	
	public function setting_page() {
		include dirname( __FILE__ ) . '/admin.php';
	}
}
new WP_Recent_Views;


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


function wp_ajax_recent_views() {
	
}
