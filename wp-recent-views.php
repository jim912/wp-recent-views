<?php
/*
Plugin Name: WP Recent Views
Plugin URI: https://github.com/jim912/wp-recent-views
Description: Display a list of pages you have seen recently.
Author: jim912
Version: 1.0
Author URI: http://www.warna.info/
Text Domain: wp-recent-views
Domain Path: /languages/
*/


class WP_Recent_Views {
	
	private $default = array(
		'generations' => 10,
		'post_types'  => array( 'post' ),
		'expire'      => 15
	);
	public $settings;
	public $count = 1;
	public $numpages;
	public $multipage;
	public $version;

	public function __construct() {
		require_once( dirname( __FILE__ ) . '/functions.php' );
		require_once( dirname( __FILE__ ) . '/widget.php' );

		$data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
		$this->version = $data['version'];

		add_action( 'template_redirect'          , array( &$this, 'enqueue_script' ) );
		add_action( 'wp_footer'                  , array( &$this, 'register_post_id' ), 9 );
		add_action( 'init'                       , array( &$this, 'register_shortcode' ) );
		add_action( 'admin_menu'                 , array( &$this, 'add_setting_page' ) );
		add_action( 'widgets_init'               , array( &$this, 'register_widget' ) );
		add_action( 'wp_ajax_recent_views'       , 'wp_ajax_recent_views' );
		add_action( 'wp_ajax_nopriv_recent_views', 'wp_ajax_recent_views' );
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

		$this->settings = array_merge( $this->default, get_option( 'recent-views', array() ) );
		load_plugin_textdomain( 'wp-recent-views', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	}
	
	
	public function register_widget() {
		register_widget( 'WP_Recent_Views_Widget' );
	}
	
	
	public function enqueue_script() {
		global $post;
		if ( is_singular() && in_array( $post->post_type, $this->settings['post_types'] ) ) {
			wp_enqueue_script( 'wp-recent-views', plugin_dir_url( __FILE__ ) . 'js/wp-recent-views.js', array(), $this->version, true );
		}
	}
	
	
	public function register_post_id() {
		global $post;
		$path = preg_replace( '#^' . $_SERVER['DOCUMENT_ROOT'] . '#', '', str_replace( '\\', '/', ABSPATH ) );
?>
<script type='text/javascript'>
/* <![CDATA[ */
var viewPost = { id: "<?php echo esc_js( $post->ID ); ?>", generations: "<?php echo esc_js( $this->settings['generations'] ); ?>", path: "<?php echo esc_js( $path ); ?>", maxAge: "<?php echo esc_js( $this->settings['expire'] * 24 * 3600 ); ?>" };
/* ]]> */
</script>
<?php
	}
	
	
	public function register_shortcode() {
		add_shortcode( 'recent-views', array( &$this, 'recent_views_shortcode' ) );
	}
	
	
	public function recent_views_shortcode( $args ) {
		global $post, $numpages, $multipage;
		
		$atts = shortcode_atts( array( 'posts_per_page' => 0, 'offset' => 0, 'paged' => false ), $args );
		$return = '';
		$recent_views = get_recent_views( $atts );
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
		$numpages = $this->numpages;
		$multipage = $this->multipage;
		return $return;
	}
	
	
	public function add_setting_page() {
		add_options_page( __( 'Recent Views', 'wp-recent-views' ), __( 'Recent Views', 'wp-recent-views' ), 'manage_options', basename( __FILE__ ), array( &$this, 'setting_page' ) );
		register_setting( 'recent-views', 'recent-views', array( &$this, 'sanitize_setting' ) );
	}
	
	
	public function sanitize_setting( $post_data ) {
		foreach ( $post_data as $key => $value ) {
			switch ( $key ) {
				case 'generations' :
				case 'expire' :
					if ( function_exists( 'mb_convert_kana' ) ) {
						$value = mb_convert_kana( $value, 'n', 'UTF-8' );
					}
					$value = preg_replace( '/[^0-9]/', '', $value );
					$post_data[$key] = absint( $value );
					if ( ! $post_data[$key] ) {
						$post_data[$key] = $this->settings[$key];
					}
					break;
				case 'post_types' :
					if ( is_array( $value ) ) {
						$post_types = array_keys( get_post_types( array( 'public' => true ) ) );
						$tmp = array();
						foreach ( $value as $post_type ) {
							if ( in_array( $post_type, $post_types ) ) {
								$tmp[] = $post_type;
							}
						}
						$post_data[$key] = $tmp;
					} else {
						$post_data[$key] = array();
					}
					break;
				default :
					unset( $post_data[$key] );
			}
		}
		return $post_data;
	}
	
	
	public function setting_page() {
		include dirname( __FILE__ ) . '/admin.php';
	}

}
$WP_Recent_Views = new WP_Recent_Views;