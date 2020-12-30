<?php

/**
 * Plugin Name: WP ImgIX
 * Version: 0.12.5
 * Description: Simple Wordpress ImgIX integration
 * Author: Ryan Soury | Web Doodle
 * Author URI: https://www.webdoodle.com.au/
 */

if (!defined('WP_IMGIX_URL') || !WP_IMGIX_URL) {
	return;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/includes/class-wp-imgix.php';
require_once dirname(__FILE__) . '/includes/classes/class-compat.php';

WPImgIX::instance();

/**
 * Generates an ImgIX URL.
 *
 * @param string $image_url URL to the publicly accessible image you want to manipulate
 * @param array|string $args An array of arguments, i.e. array( 'w' => '300', 'resize' => array( 123, 456 ) ), or in string form (w=123&h=456)
 * @return string The raw final URL. You should run this through esc_url() before displaying it.
 */
function imgix_url($image_url, $args = array(), $scheme = null)
{

	$upload_dir = wp_upload_dir();
	$upload_baseurl = $upload_dir['baseurl'];

	if (is_multisite()) {
		$upload_baseurl = preg_replace('#/sites/[\d]+#', '', $upload_baseurl);
	}

	$image_url = trim($image_url);

	// Check of image_url to source from CDN is from uploads
	if (strpos($image_url, $upload_baseurl) !== 0) {
		return $image_url;
	}

	if (false !== apply_filters('imgix_skip_for_url', false, $image_url, $args, $scheme)) {
		return $image_url;
	}

	if (empty($args)) {
		$args = [];
	}

	if (!is_array($args)) {
		$args = explode('&', $args);
	}

	$args = array_merge($args, [
		'auto' => 'format,compress'
	]);

	$image_url = apply_filters('imgix_pre_image_url', $image_url, $args, $scheme);
	$args      = apply_filters('imgix_pre_args', $args, $image_url, $scheme);

	// $imgix_url = str_replace($upload_baseurl, WP_IMGIX_URL, $image_url);
	$image_pathname = parse_url($image_url, PHP_URL_PATH);

	$imgix_url = WPImgIX::instance()->build_url($image_pathname, $args); // add_query_arg($args, $imgix_url);

	/**
	 * Allows a final modification of the generated imgix URL.
	 *
	 * @param string $imgix_url The final imgix image URL including query args.
	 * @param string $image_url   The image URL without query args.
	 * @param array  $args        A key value array of the query args appended to $image_url.
	 */
	return apply_filters('imgix_url', $imgix_url, $image_url, $args);
}
