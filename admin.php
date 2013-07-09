<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$post_types = get_post_types( array( 'public' => true ), false );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'WP Recent Views', 'wp-recent-views' ) ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'recent-views' ); ?>
		<table class="form-table">
			<tr>
				<th><?php _e( 'Generations', 'wp-recent-views' ); ?></th>
				<td>
					<input type="text" name="recent-views[generations]" size="2" value="<?php echo esc_attr( $this->settings['generations'] ); ?>" />
					<p class="description"><?php _e( 'History will be saved in <a href="http://en.wikipedia.org/wiki/HTTP_cookie">COOKIE</a>. For this reason, the data is not saved The more number of generations too, there is a risk of or damage. Please make up to about 100 at most.', 'wp-recent-views' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Post Types', 'wp-recent-views' ); ?></th>
				<td>
					<input type="hidden" name="recent-views[post_types]" value="0" />
					<ul>
<?php foreach ( $post_types as $post_type ) : ?>
						<li>
							<label for="recent-views-post_type-<?php echo esc_attr( $post_type->name ); ?>">
								<input type="checkbox" name="recent-views[post_types][]" id="recent-views-post_type-<?php echo esc_attr( $post_type->name ); ?>" value="<?php echo esc_attr( $post_type->name ); ?>"<?php echo in_array( $post_type->name, $this->settings['post_types'] ) ? ' checked="checkes"' : ''; ?> />
								<?php echo esc_html( $post_type->label ); ?>
							</label>
						</li>
<?php endforeach; ?>
					</ul>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Expire', 'wp-recent-views' ); ?></th>
				<td>
					<?php
						printf(
							__( '%1s days', 'wp-recent-views' ),
							'<input type="text" name="recent-views[expire]" size="3" value="' . esc_attr( $this->settings['expire'] ) . '" />'
						);
					?>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>