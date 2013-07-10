<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WP_Recent_Views_Widget extends WP_Widget {
	public function WP_Recent_Views_Widget() {
		$widget_ops = array(
			'classname' => 'recent-views-widget',
			'description' => __( 'Display a list of pages you have seen recently.', 'wp-recent-views' )
		);
		$this->defaults = array(
			'title' => __( 'Recent Views', 'wp-recent-views' ),
			'items' => 5,
			'mode'  => 'default'
		);
		$this->WP_Widget( 'recent-views', __( 'Recent Views', 'wp-recent-views' ), $widget_ops );
	}
	
	
	public function update ( $new_instance, $old_instance ){
		$new_instance['items'] = absint( $new_instance['items'] );
		if ( ! $new_instance['items'] ) {
			$new_instance['items'] = $old_instance['items'];
		}
		$new_instance['mode'] = $new_instance['mode'] == 'ajax' ? 'ajax' : 'default';
		return $new_instance;
	}
	
	
	public function form( $instance ) {
		$instance = wp_parse_args( (array)$instance, $this->defaults );
?>
	<p><?php _e( 'Title :', 'wp-recent-views' ); ?><br />
		<input type="text" size="30" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</p>
	<p><?php _e( 'Items :', 'wp-recent-views' ); ?><br />
		<input type="text" size="2" name="<?php echo $this->get_field_name( 'items' ); ?>" value="<?php echo esc_attr( $instance['items'] ); ?>" />
	</p>
	<p><?php _e( 'Mode :', 'wp-recent-views' ); ?><br />
		<select name="<?php echo $this->get_field_name( 'mode' ); ?>">
			<option value="dafault"<?php echo $instance['mode'] != 'ajax' ? ' selected="selected"' : ''; ?>><?php _e( 'Default', 'wp-recent-views' ); ?></option>
			<option value="ajax"<?php echo $instance['mode'] == 'ajax' ? ' selected="selected"' : ''; ?>><?php _e( 'Ajax', 'wp-recent-views' ); ?></option>
		</select>
	</p>

<?php
	}
	
	
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		echo $args['before_widget'] . "\n";
		echo $args['before_title'] . apply_filters( 'the_title', $instance['title'] ) . $args['after_title'] . "\n";
		wp_list_recent_views(
			array(
				'title_li' => '',
				'limit' => absint( $instance['items'] ),
				'mode' => $instance['mode'],
			)
		);
		echo $args['after_widget'] . "\n";
	}
}