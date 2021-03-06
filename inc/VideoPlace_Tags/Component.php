<?php
/**
 * WP_Rig\WP_Rig\VideoPlace_Tags\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\VideoPlace_Tags;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function add_action;
use function add_filter;
use function get_theme_mod;
use function esc_attr;
use function WP_Rig\WP_Rig\wp_rig;

/**
 * Class for managing sidebars.
 *
 * Exposes template tags:
 * * `wp_rig()->is_primary_sidebar_active()`
 * * `wp_rig()->display_primary_sidebar()`
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'videoplace-tags';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'excerpt_more', [ $this, 'excerpt_more' ] );
		add_filter( 'get_the_archive_title', [ $this, 'archive_title' ] );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags(): array {
		return [
			'social_links'      => [ $this, 'social_links' ],
			'related_posts'     => [ $this, 'related_posts' ],
			'page_navi'         => [ $this, 'page_navi' ],
			'load_light_styles' => [ $this, 'load_light_styles' ],
		];
	}

	/**
	 * Returns the HTML for the social media links section.
	 *
	 * @since 2.0
	 *
	 * @return string      HTML for the social media links section.
	 */
	public function social_links() {
		$html = '<div class="social-links">';
		if ( esc_attr( get_theme_mod( 'videoplace-facebook' ) ) ) { $html .= '<a href="' . esc_url( get_theme_mod( 'videoplace-facebook' ) ) . '" target="_blank"><span class="fab fa-facebook-f"><span class="screen-reader-text">' . esc_html__( 'Facebook Profile', 'wp-rig' ) . '</span></span></a>'; }
		if ( esc_attr( get_theme_mod( 'videoplace-twitter' ) ) ) { $html .= '<a href="' . esc_url( get_theme_mod( 'videoplace-twitter' ) ) . '" target="_blank"><span class="fab fa-twitter"><span class="screen-reader-text">' . esc_html__( 'Twitter Profile', 'wp-rig' ) . '</span></span></a>'; }
		if ( esc_attr( get_theme_mod( 'videoplace-youtube' ) ) ) { $html .= '<a href="' . esc_url( get_theme_mod( 'videoplace-youtube' ) ) . '" target="_blank"><span class="fab fa-youtube"><span class="screen-reader-text">' . esc_html__( 'YouTube Channel', 'wp-rig' ) . '</span></span></a>'; }
		if ( esc_attr( get_theme_mod( 'videoplace-tumblr' ) ) ) { $html .= '<a href="' . esc_url( get_theme_mod( 'videoplace-tumblr' ) ) . '" target="_blank"><span class="fab fa-tumblr"><span class="screen-reader-text">' . esc_html__( 'Tumblr Blog', 'wp-rig' ) . '</span></span></a>'; }
		if ( esc_attr( get_theme_mod( 'videoplace-instagram' ) ) ) { $html .= '<a href="' . esc_url( get_theme_mod( 'videoplace-instagram' ) ) . '" target="_blank"><span class="fab fa-instagram"><span class="screen-reader-text">' . esc_html__( 'Facebook Profile', 'wp-rig' ) . '</span></span></a>'; }
		if ( esc_attr( get_theme_mod( 'videoplace-rss-feed' ) ) ) { $html .= '<a href="' . esc_url( get_theme_mod( 'videoplace-rss-feed' ) ) . '" target="_blank"><span class="fas fa-rss"><span class="screen-reader-text">' . esc_html__( 'RSS Feed', 'wp-rig' ) . '</span></span></a>'; } else { $html .= '<a href="' . get_feed_link( 'rss' ) . '" target="_blank"><span class="fas fa-rss"><span class="screen-reader-text">' . esc_html__( 'RSS Feed', 'wp-rig' ) . '</span></span></a>'; }
		$html .= '</div>';
		return $html;
	}

	/**
	 * Displays the related posts for a post.
	 *
	 * @since 2.0
	 */
	public function related_posts() {
		global $post;
		$tags = wp_get_post_tags( $post->ID );
		if( $tags ) {
			foreach( $tags as $tag ) {
				$tag_arr = $tag->slug . ',';
			}
			$args = array(
				'tag' 			=> $tag_arr,
				'numberposts' 	=> 3, /* you can change this to show more */
				'post__not_in'	 => array( $post->ID )
			);
			$related_posts = get_posts( $args );
			if( $related_posts ) {
				echo'<section class="related-posts">';
				echo '<h4>Related Videos</h4>';
				foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'video-post' ); ?>>
						<h5 class="post-category"><?php $cats = get_the_category(); echo $cats[ 0 ]->name; ?></h5>
						<div class="photo-video">
							<?php if ( has_post_format( 'image' ) ) { ?>
								<?php if ( has_post_thumbnail() ) {
									the_post_thumbnail('videoplace-featured-image');
								} else {
									$media = get_attached_media( 'image' );
									foreach ( $media as $image ) {
										echo '<img width="800" height="440" src="' . esc_url( $image->guid ) . '" />';
										break;
									}
								} ?>
							<?php } elseif ( has_post_format( 'video' ) ) { ?>
								<?php echo wp_rig()->media_grabber( array( 'split_media' => true ) ); ?>
							<?php } else { ?>
								<?php echo wp_rig()->media_grabber( array( 'split_media' => true ) ); ?>
							<?php } ?>
						</div>
						<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="post-details clearfix">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 50 ); ?>
							<h4 class="post-detail"><?php echo __( 'Posted by ', 'wp-rig' ) . get_the_author_link() . __( ' on ', 'wp-rig' ) . get_the_date( get_option( 'date_format' ) ); ?></h4>
						</div>
						<a href="<?php the_permalink(); ?>" class="button white"><?php _e( 'View More Info', 'wp-rig' ); ?></a>
					</article>
				<?php endforeach; }
		}
		wp_reset_postdata();
		echo '</section>';
	}

	/**
	 * Modifies the read more link text to show nothing.
	 *
	 * @since 2.0.
	 *
	 * @param string $more      The default more text.
	 * @return string           The new more text.
	 */
	public function excerpt_more( $more ) {
		return '';
	}

	/**
	 * Returns the title of an archive page.
	 *
	 * @since 2.0
	 *
	 * @param string $title      The current title.
	 * @return string            The new title.
	 */
	public function archive_title( $title ) {
		if ( is_day() ) {
			$title = get_the_time( 'F j, Y' );
		}
		else if (is_month()) {
			$title = get_the_time( 'F Y' );
		}
		else if (is_year()) {
			$title = get_the_time( 'Y' );
		}
		else if ( is_category() ) {
			$title = single_cat_title( '', false );
		}
		else if ( is_search() ) {
			$title = __( 'Search results for ', 'wp-rig' ) . get_search_query();
		}
		else if (is_tag()) {
			$title = single_tag_title( '', false );
		}
		else if (is_author()) {
			$title = __( 'Videos Posted By: ', 'wp-rig' ) . get_the_author();
		}
		else {
			$page = get_query_var( 'paged' );
			$title = __( 'Page ', 'wp-rig' ) . $page;
		}
		return $title;
	}

	/**
	 * Displays the pagination for a page.
	 *
	 * @since 2.0
	 *
	 * @param WP_Query $custom_wp_query      The current WP_Query object.
	 * @param string   $before               HTML to output before the pagination.
	 * @param string   $after                HTML to output after the pagination.
	 */
	public function page_navi( $custom_wp_query, $before = '', $after = '' ) {
		global $wpdb;
		$request        = $custom_wp_query->request;
		$posts_per_page = intval( get_query_var( 'posts_per_page' ) );
		$paged          = intval( get_query_var( 'page' ) );
		$numposts       = $custom_wp_query->found_posts;
		$max_page       = $custom_wp_query->max_num_pages;
		if ( $numposts <= $posts_per_page ) {
			return;
		}
		if ( empty( $paged ) || 0 === $paged ) {
			$paged = 1;
		}
		$pages_to_show         = 7;
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start       = floor( $pages_to_show_minus_1 / 2 );
		$half_page_end         = ceil( $pages_to_show_minus_1 / 2 );
		$start_page            = $paged - $half_page_start;
		if ( $start_page <= 0 ) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if ( ( $end_page - $start_page ) !== $pages_to_show_minus_1 ) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if ( $end_page > $max_page ) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page   = $max_page;
		}
		if ( $start_page <= 0 ) {
			$start_page = 1;
		}
		echo $before . '<nav class="page-navigation"><ul class="pagination">';
		if ( $start_page >= 2 && $pages_to_show < $max_page ) {
			$first_page_text = __( 'First', 'wp-rig' );
			echo '<li><a href="' . get_pagenum_link() . '" title="' . $first_page_text . '">' . $first_page_text . '</a></li>';
		}
		if ( get_previous_posts_link() ) {
			echo '<li>';
			previous_posts_link( __( '&laquo;&laquo; Previous', 'wp-rig' ), $max_page );
			echo '</li>';
		}
		for ( $i = $start_page; $i <= $end_page; $i++ ) {
			if ( $i === $paged ) {
				echo '<li class="current"> ' . $i . ' </li>';
			} else {
				echo '<li><a href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
			}
		}
		if ( get_next_posts_link() ) {
			echo '<li>';
			next_posts_link( __( 'Next &#187;&#187;', 'wp-rig' ), $max_page );
			echo '</li>';
		}
		if ( $end_page < $max_page ) {
			$last_page_text = __( 'Last', 'wp-rig' );
			echo '<li><a href="' . get_pagenum_link( $max_page ) . '" title="' . $last_page_text . '">' . $last_page_text . '</a></li>';
		}
		echo '</ul></nav>' . $after;
	}

	/**
	 * Loads the light theme styles if the option is selected.
	 *
	 * @since 2.0
	 */
	public function load_light_styles() {
		if ( 'light' === get_theme_mod( 'videoplace-color-scheme' ) ) {
			wp_rig()->print_styles( 'wp-rig-light' );
		}
	}
}
