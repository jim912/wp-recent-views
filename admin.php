<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$post_types = get_post_types( array( 'public' => true ), false );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>最近見たページ</h2>
	<form method="post" action="options.php">
		<?php wp_nonce_field(); ?>
		<table class="form-table">
			<tr>
				<th>保持世代数</th>
				<td>
					<input type="text" name="recent-views[generations]" size="2" value="<?php echo esc_attr( $this->generations ); ?>" />
					<p class="description">履歴は<a href="http://ja.wikipedia.org/wiki/HTTP_cookie">COOKIE</a>に保存されます。このため、あまり世代数を多くするとデータが保存されない、または破損する恐れがあります。多くとも100程度までにしてください。</p>
				</td>
			</tr>
			<tr>
				<th>履歴に含める投稿タイプ</th>
				<td>
					<input type="hidden" name="recent-views[post_types]" value="0" />
					<ul>
<?php foreach ( $post_types as $post_type ) : ?>
						<li>
							<label for="recent-views-post_type-<?php echo esc_attr( $post_type->name ); ?>">
								<input type="checkbox" name="recent-views[post_types][]" id="recent-views-post_type-<?php echo esc_attr( $post_type->name ); ?>" value="<?php echo esc_attr( $post_type->name ); ?>" />
								<?php echo esc_html( $post_type->label ); ?>
							</label>
						</li>
<?php endforeach; ?>
					</ul>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>