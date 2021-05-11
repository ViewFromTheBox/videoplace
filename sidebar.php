<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_primary_sidebar_active() ) {
	return;
}

?>
<aside id="secondary" class="primary-sidebar widget-area">
	<?php wp_rig()->display_primary_sidebar(); ?>
</aside><!-- #secondary -->
