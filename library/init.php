<?php

// Content Width
if ( ! isset( $content_width ) && get_theme_mod( 'setting_site_contents_width', 1200 ) ) {
	$content_width = 1200;
}

/*****************
 * Add Theme Support
 *****************/

function nishiki_custom_theme_support() {
	// Html5
	add_theme_support('html5', array(
			'comment-form',
			'comment-list',
		)
	);

	// Title Tag
	add_theme_support('title-tag');

	// Thumbnails
	add_theme_support('post-thumbnails');
	set_post_thumbnail_size( 640, 360, true );
	add_image_size( 'nishiki-thumbnail', 64, 36, true );

	// Custom Header
	add_theme_support( 'custom-header', array(
			'width'             => 1280,
			'height'            => 720,
			'flex-height'       => true,
			'video'             => true,
		)
	);

	// Custom Logo
	add_theme_support( 'custom-logo', array(
			'height'        => 80,
			'width'         => 240,
			'flex-height'   => true,
			'flex-width'    => true,
			'header-text'   => array( 'site-title', 'site-description' ),
		)
	);

	// Feed Links
	add_theme_support('automatic-feed-links');

	/*****************
	 * Gutenberg
	 *****************/

	// Align Wide
	add_theme_support( 'align-wide' );

}
add_action( 'after_setup_theme', 'nishiki_custom_theme_support' );

/*****************
 * Add Editor Style
 *****************/

function nishiki_editor_style() {
	add_editor_style();
}
add_action( 'admin_init', 'nishiki_editor_style' );

/*****************
 * Enqueue block editor style
 *****************/
function nishiki_block_editor_styles() {
	wp_enqueue_style( 'nishiki-block-editor-styles', get_theme_file_uri( '/editor-gutenberg-style.css' ), false, '1.0', 'all' );
}
add_action( 'enqueue_block_editor_assets', 'nishiki_block_editor_styles' );

/*****************
 * Add Pingback
 *****************/

function nishiki_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'nishiki_pingback_header' );

/*****************
 * Check Front Page
 *****************/

// Setting Front Page Check
add_filter('frontpage_template', 'nishiki_front_page_template');
function nishiki_front_page_template( $template ) {
    return is_home() ? '' : $template;
}

// Check Front Page
function nishiki_is_static_front_page() {
    return ( is_front_page() && ! is_home() );
}

/*****************
 * Check function exists
 *****************/

// Check has_header_video()
function nishiki_has_header_video() {
	if ( function_exists('has_header_video') ) {
		return has_header_video();
	}

	return false;
}

/*****************
 * Custom header
 *****************/

add_filter( 'header_video_settings', 'nishiki_header_video_settings');
function nishiki_header_video_settings( $settings ) {
	$settings['width'] = 1280;
	$settings['height'] = 720;
	$settings['minWidth'] = 640;
	$settings['minHeight'] = 360;
	$settings['l10n']['pause'] = '<i class="icomoon icon-pause2"></i>';
	$settings['l10n']['play'] = '<i class="icomoon icon-play3"></i>';
	return $settings;
}

add_filter( 'get_header_image_tag', 'nishiki_header_image_tag', 10, 3 );
function nishiki_header_image_tag( $html, $header, $attr ){
	return '<img class="header-image" data-src="' . $attr['src'] . '" width="' . $attr['width'] . '" height="' . $attr['height'] . '" alt="' . $attr['alt'] . '" data-srcset="' . $attr['srcset'] . '" sizes="' . $attr['sizes'] . '">';
}

/*****************
 * Translate
 *****************/

load_theme_textdomain( 'nishiki', get_template_directory() . '/languages' );

/*****************
 * Register Nav
 *****************/

// Add Nav
add_action( 'after_setup_theme', 'nishiki_register_nav' );
function nishiki_register_nav() {
	// Global Nav
	register_nav_menu( 'global', __( 'Global Menu', 'nishiki' ) );
}

/*****************
 * Disable & Delete
 *****************/

// Disable Default Gallery Style
add_filter( 'use_default_gallery_style', '__return_false' );

/*****************
 * Excerpt
 *****************/

// Excerpt
add_filter('excerpt_length', 'nishiki_home_excerpt_length', 999);
function nishiki_home_excerpt_length( $excerpt ) {
	if ( is_admin() ) {
		return $excerpt;
	}

	if( get_theme_mod( 'setting_archive_excerpt_text_num' ) ){
		$excerpt = absint( get_theme_mod( 'setting_archive_excerpt_text_num' ) );
	} else {
		$excerpt = 50;
	}

	return $excerpt;
}

// Excerpt More
add_filter('excerpt_more', 'nishiki_new_excerpt_more');
function nishiki_new_excerpt_more( $more ) {
	if ( is_admin() ) {
		return $more;
	}

	if( get_theme_mod( 'setting_archive_excerpt_text' ) ){
		$more = esc_html( get_theme_mod( 'setting_archive_excerpt_text' ) );
	} else {
		$more = '...';
	}

	return $more;
}

/*****************
 * Title
 *****************/

// Title Separator
add_filter( 'document_title_separator', 'nishiki_header_title_sep' );
function nishiki_header_title_sep( $sep ) {
	$sep = '|';
	return $sep;
}

// Archive Title
add_filter( 'get_the_archive_title', 'nishiki_change_archive_title' );
function nishiki_change_archive_title($title){
	if( is_category() or is_tag() ) {
		$title = single_cat_title( '', false );
	} elseif( is_author() ) {
		$title = get_the_author_meta('display_name');
	} elseif( is_tax() ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$title = $term->name;
	} elseif( is_day() ){
		$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'nishiki' ) );
	} elseif( is_month() ){
//		$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'nishiki' ) );
		/* translators: %s: get_the_date */
		$title = sprintf( __( '%1$s %2$d', 'nishiki' ), get_the_date('M'), get_the_date('Y') );
	} elseif( is_year() ){
		/* translators: %s: get_the_date */
		$title = sprintf( esc_html__( '%s year', 'nishiki' ), get_the_date('Y') );
	} elseif( is_search() ){
		$title = __( 'Search Result', 'nishiki' );
	} elseif( is_archive() ){
		$title = single_cat_title( '', false );
	}

	return $title;
}

/*****************
 * Register Widget Area
 *****************/

// Register Widget Area
add_action( 'widgets_init', 'nishiki_custom_widgets_init' );
function nishiki_custom_widgets_init() {

	// Front
	register_sidebar( array(
		'name'          => __( 'Front Page(Beta)', 'nishiki' ),
		'id'            => 'front_widget',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	) );

	// Sidebar
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'nishiki' ),
		'id'            => 'sidebar_main',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	) );

	// Footer
	register_sidebar( array(
		'name'          => __( 'Footer', 'nishiki' ),
		'id'            => 'footer_widget',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	) );

}

/*****************
 * Tag Cloud
 *****************/

// Custom Tag Cloud
add_filter('widget_tag_cloud_args','nishiki_custom_tag_cloud');
function nishiki_custom_tag_cloud( $args ) {
	$args['smallest'] = 11;
	$args['largest'] = 11;
	return $args;
}

/*****************
 * Embed
 *****************/

// Embed Youtube & Vimeo Responsive
add_filter( 'embed_oembed_html', 'nishiki_add_oembed_class' );
function nishiki_add_oembed_class( $code ){
	if( (stripos( $code, 'youtube' ) !== FALSE || strpos( $code, 'vimeo' ) ) && stripos( $code, 'iframe' ) !== FALSE )
		$code = '<div class="embed-video">' . $code . '</div>';

	return $code;
}
