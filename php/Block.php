<?php
/**
 * Block class.
 *
 * @package SiteCounts
 */

namespace XWP\SiteCounts;

use WP_Block;

/**
 * The Site Counts dynamic block.
 *
 * Registers and renders the dynamic block.
 */
class Block {

	/**
	 * The Plugin instance.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Instantiates the class.
	 *
	 * @param Plugin $plugin The plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Adds the action to register the block.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_block' ] );
	}

	/**
	 * Registers the block.
	 */
	public function register_block() {
		register_block_type_from_metadata(
			$this->plugin->dir(),
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	/**
	 * Renders the block.
	 *
	 * @param array    $attributes The attributes for the block.
	 * @param string   $content    The block content, if any.
	 * @param WP_Block $block      The instance of this block.
	 * @return string The markup of the block.
	 */
	public function render_callback( $attributes, $content, $block ) {
		$post_types = get_post_types( [ 'public' => true ] );
		$post_id    = get_the_ID();
		$attributes = wp_parse_args( $attributes, [ 'className' => '' ] );
		$class_name = $attributes['className'];
		ob_start();

		?>
		<div class="<?php echo esc_attr( $class_name ); ?>">
			<h2><?php _e( 'Post Counts', 'site-counts' ); ?></h2>
			<?php
			foreach ( $post_types as $post_type_slug ) :
				$post_type_object = get_post_type_object( $post_type_slug );
				$post_count       = wp_count_posts( $post_type_slug );
				$post_label       = $post_count->publish > 1 ? $post_type_object->labels->name : $post_type_object->labels->singular_name;
				?>
				<?php /* translators: %1d: post count, %2s: post type name  */ ?>
				<p><?php printf( _n( 'There is %1$d %2$s.', 'There are %1$d %2$s.', $post_count->publish, 'site-counts' ), $post_count->publish, $post_label ); ?>
			<?php endforeach; ?>
			<?php /* translators: %d: curent post id */ ?>
			<p><?php printf( __( 'The current post ID is %d.', 'site-counts' ), $post_id ); ?></p>
		</div>
		<?php

		return ob_get_clean();
	}
}
